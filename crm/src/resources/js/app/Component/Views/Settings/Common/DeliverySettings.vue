<template>
    <div class="container-fluid p-0">
        <template v-if="mailSettings.provider && props.alias !== 'brand'">
            <note
                    :description="notes[mailSettings.provider]"
                    :title="$fieldTitle('delivery', 'settings')"
                    v-if="notes[mailSettings.provider]"
            />
            <br>
        </template>
        <form :data-url="submit_url" @submit.prevent="save" method="post" ref="form">
            <note :description="`<li>
                                    ${this.$t('brand_global_delivery_settings_note')}
                                </li>
                                <li> ${this.$t('cron_job_settings_warning',{
                                documentation: `<a href='https://mailer.gainhq.com/documentation/important-settings.html#schedulerAndQueue' target='_blank'>${this.$t('documentation')}</a>`
                                })}</li>`"
                  :title="$fieldTitle('delivery', 'settings')"
                  v-if="props.alias === 'brand'"/>
            <br v-if="props.alias === 'brand'"/>
            <app-form-group
                    :error-message="$errorMessage(errors, 'provider')"
                    :label="$fieldTitle('select', 'provider')"
                    :list="providers"
                    page="page"
                    type="select"
                    v-model="mailSettings.provider"
            />

            <component :alias="props.alias" :errors="errors" :is="componentNames[mailSettings.provider]"
                       v-model="mailSettings"/>

            <app-form-group
                    :error-message="$errorMessage(errors, 'from_email')"
                    :label="$fieldLabel('from', 'email')"
                    :placeholder="$placeholder('from', 'email')"
                    page="page"
                    :disabled="props.alias === 'brand'"
                    v-model="mailSettings.from_email"
            />

            <app-form-group
                    :error-message="$errorMessage(errors, 'from_name')"
                    :label="$fieldLabel('from', 'name')"
                    :placeholder="$placeholder('from', 'name')"
                    page="page"
                    :disabled="props.alias === 'brand'"
                    v-model="mailSettings.from_name"
            />


            <div class="form-group mt-5 mb-0">
                <submit-button :loading="loading" @click="submitData"/>
                <app-button
                    v-if="testMailButton"
                    :isDisabled="false"
                    :label="$t('test_email')"
                    class="btn-secondary ml-2"
                    @submit="testMailModal = true"
                />
                <a href="https://mailer.gainhq.com/documentation/#delivery-settings" class="float-right" target="_blank">
                    {{ $t('need_help') }}
                </a>
            </div>
        </form>
        <app-test-mail-modal
            v-if="testMailModal"
            v-model="testMailModal"
        />
    </div>
</template>

<script>
    import FormHelperMixins from "../../../../Mixins/Global/FormHelperMixins";
    import SubmitButton from "../../../Helper/Button/SubmitButton";
    import Message from "../../../Helper/Message/Message";
    import {axiosGet, urlGenerator} from '../../../../Helpers/AxiosHelper'
    import {
        app_delivery_quotas,
        brand_default_mail_settings,
        mail_settings_list
    } from "../../../../config/apiUrl";
    import Note from "../../../Helper/Note/Note";

    export default {
        name: "DeliverySettings",
        components: {Note, Message, SubmitButton},
        mixins: [FormHelperMixins],
        data() {
            return {
                alias: false,
                mailSettings: {
                    provider: '',
                },
                submit_url: 'admin/app/settings/delivery-settings',
                componentNames: {
                    amazon_ses: 'app-ses',
                    mailgun: 'app-mailgun',
                    smtp: 'app-smtp',
                },

                notes: {
                    amazon_ses: `<li> ${this.$t('delivery_settings_message_amazon_ses', {
                        endpoint: urlGenerator(`webhook/ses`),
                        doc: ` <a href='https://mailer.gainhq.com/documentation/important-settings.html#deliverySettings' target ='_blank'>doc</a>`
                    })}</li>
                    <li> ${this.$t('cron_job_settings_warning',{
                        documentation: `<a href='https://mailer.gainhq.com/documentation/important-settings.html#schedulerAndQueue' target='_blank'>${this.$t('documentation')}</a>`
                    })}</li>`,
                    mailgun: `<li> ${this.$t('delivery_settings_message_mailgun', {
                        endpoint: urlGenerator(`webhook/mailgun`),
                        doc: ` <a href='https://mailer.gainhq.com/documentation/important-settings.html#deliverySettings' target ='_blank'>doc</a>`
                    })}</li>
                    <li> ${this.$t('cron_job_settings_warning',{
                        documentation: `<a href='https://mailer.gainhq.com/documentation/important-settings.html#schedulerAndQueue' target='_blank'>${this.$t('documentation')}</a>`
                    })}</li>`,
                    smtp: `<li> ${this.$t('cron_job_settings_warning',{
                        documentation: `<a href='https://mailer.gainhq.com/documentation/important-settings.html#schedulerAndQueue' target='_blank'>${this.$t('documentation')}</a>`
                    })}</li>`,
                },
                testMailButton: false,
                testMailModal: false,
            }
        },
        props: {
            mailContext: {
                default: function () {
                    return null;
                }
            },
            props: {
                default: function () {
                    return {
                        alias: 'app'
                    }
                }
            }
        },
        computed: {
            providers() {
                return [{
                    id: '',
                    disabled: true,
                    value: this.$t('provider')
                }, ...this.$store.getters.getFormattedConfig('mail_context')]
            },
        },
        methods: {
            submitData() {
                this.loading = true;
                this.message = '';
                this.errors = {};
                this.save(this.mailSettings);
            },


            afterSuccess({data}) {
                this.scrollToTop();
                this.setSMTPQuotaNote()
                this.toastAndReload(data.message);
                this.testMailButton = true;
            },

            getMailSettings(provider = '') {
                const p = provider ? `/${provider}` : '';

                const url = this.props.alias === 'brand'
                    ? `${brand_default_mail_settings}${p}`
                    : `${mail_settings_list}${p}`;

                axiosGet(url).then(({data}) => {
                    if (Object.keys(data).length && this.props.alias === 'app') {
                        this.testMailButton = true
                    }
                    if (data.provider) {
                        this.mailSettings = Object.keys(data).length ? data : {provider: 'mailgun'};

                        if (data.provider === 'smtp')  {
                            this.setInitialSMTPConfigs(data)
                        }
                    }
                    else {
                        this.mailSettings = {provider: this.mailSettings.provider}
                    }

                    if (provider === 'smtp') {
                        this.setInitialSMTPConfigs(data)
                    }

                });
            },

            setSMTPQuotaNote() {
                axiosGet(app_delivery_quotas).then(({data}) => {
                    if (Object.keys(data).length) {
                        let note = this.$t('app_delivery_quota_message_1', {
                            type: data.type,
                            count: data.count,
                        });
                        this.notes.smtp = note + `</br>` + this.$t('brand_delivery_quota_message_2') + `</br>` + `- ${this.$t('cron_job_settings_warning',{
                            documentation: `<a href='https://mailer.gainhq.com/documentation/important-settings.html#schedulerAndQueue' target='_blank'>${this.$t('documentation')}</a>`
                        })}`
                    }
                });
            },
            setInitialSMTPConfigs(data) {
                this.mailSettings.smtp_daily_quota = data?.smtp_daily_quota || 0;
                this.mailSettings.smtp_hourly_quota = data?.smtp_hourly_quota || 0;
                this.mailSettings.smtp_monthly_quota = data?.smtp_monthly_quota || 0;
                this.mailSettings.smtp_encryption = data?.smtp_encryption || 'tls';
            }
        },
        created() {
            this.$store.dispatch('getConfig');
        },

        watch: {
            'mailSettings.provider': {
                handler: function (provider) {
                    this.getMailSettings(provider);
                    if(provider === 'smtp') {
                        this.setSMTPQuotaNote()
                    }
                },
                immediate: true
            },

            'props': {
                handler: function (props) {
                    if (props.alias === 'brand') {
                        this.submit_url = 'admin/app/settings/brand-delivery';
                    }
                },
                immediate: true
            }
        }

    }
</script>

<style scoped>

</style>
