<template>
    <form method="post" ref="form" :data-url="submit_url" @submit.prevent="save" v-if="!is_loading">
        <template v-if="!is_using_default_settings">
            <template v-if="mailSettings.provider">
                <note
                    :description="notes[mailSettings.provider]"
                    :title="$fieldTitle('delivery', 'settings')"
                    v-if="notes[mailSettings.provider]"
                />
                <br>
            </template>

            <app-form-group
                page="page"
                type="select"
                :label="$fieldTitle('select', 'provider')"
                v-model="mailSettings.provider"
                :list="providers"
                :error-message="$errorMessage(errors, 'provider')"
            />

            <component
                :alias="false"
                :errors="errors"
                :is="componentNames[mailSettings.provider]"
                v-model="mailSettings"
            />

            <app-form-group
                page="page"
                type="text"
                v-model="mailSettings.from_email"
                :label="$t('from_email')"
                :placeholder="$placeholder('from', 'email')"
                :error-message="$errorMessage(errors, 'from_email')"
            />

            <app-form-group
                page="page"
                type="text"
                v-model="mailSettings.from_name"
                :label="$t('from_name')"
                :placeholder="$placeholder('from', 'name')"
                :error-message="$errorMessage(errors, 'from_name')"
            />

            <div class="mt-primary">
                <submit-button
                    v-if="$can('update_specific_brand_delivery_settings')"
                    @click="submitData"
                    :loading="loading"
                />
                <app-button
                    v-if="testMailButton"
                    :isDisabled="false"
                    :label="$t('test_email')"
                    class="btn-secondary ml-2"
                    @submit="testMailModal = true"
                />
                <app-button
                    v-if="testMailButton"
                    :isDisabled="false"
                    :label="$t('restore_default')"
                    class="btn-secondary ml-2"
                    data-toggle="tooltip"
                    :title="$t('use_default_brand_delivery_setting')"
                    @submit="confirmationModalActive = true"
                />
                <app-cancel-button
                    @click="is_using_default_settings = true"
                    btnClass="ml-2"
                    v-if="Object.keys(mailSettings).length < 2 || create_state"
                />
                <a href="https://mailer.gainhq.com/documentation/#delivery-settings" class="float-right" target="_blank">
                    {{ $t('need_help') }}
                </a>
            </div>
        </template>
        <template v-else>
            <note :title="$fieldLabel('delivery', 'settings')"
                  :description="$t('default_brand_delivery_setting_message')"/>
            <div class="mt-primary">
                <app-default-button
                    :title="$t('new_setup')"
                    @click="is_using_default_settings = false"
                />
            </div>
        </template>
        <app-test-mail-modal
            v-if="testMailModal"
            v-model="testMailModal"
            alias="brand"
        />
        <app-confirmation-modal
            v-if="confirmationModalActive"
            @cancelled="confirmationModalActive = false"
            @confirmed="deleteDeliverySetting()"
            modal-id="app-confirmation-modal"
            modal-class="warning"
            :message="$t('brand_delivery_settings_will_removed_permanently')"
            icon="alert-triangle"
        />
    </form>
    <app-pre-loader v-else/>
</template>

<script>
    import FormHelperMixins from "../../../../../Mixins/Global/FormHelperMixins";
    import SubmitButton from "../../../../Helper/Button/SubmitButton";
    import Message from "../../../../Helper/Message/Message";
    import {
        app_delivery_quotas,
        brand_delivery_quotas,
        brand_delivery_settings,
        brand_delivery_settings_delete
    } from '../../../../../config/apiUrl'
    import {axiosDelete, axiosGet, urlGenerator} from "../../../../../Helpers/AxiosHelper";
    import Note from "../../../../Helper/Note/Note";

    export default {
        name: "BrandDeliverySettings",
        components: {Note, Message, SubmitButton},
        mixins: [FormHelperMixins],
        data() {
            return {
                mailSettings: {
                    provider: '',
                },
                submit_url: brand_delivery_settings,
                is_loading: true,
                is_using_default_settings: false,
                brand_short_name: window.brand.short_name,
                create_state: true,
                componentNames: {
                    amazon_ses: 'app-ses',
                    mailgun: 'app-mailgun',
                    smtp: 'app-smtp',
                },

                notes: {
                    amazon_ses: `<li> ${this.$t('delivery_settings_message_amazon_ses', {
                                    endpoint: urlGenerator(`webhook/${this.brand_short_name}/ses`),
                                    doc: ` <a href='https://mailer.gainhq.com/documentation/important-settings.html#deliverySettings' target ='_blank'>doc</a>`
                                })}</li>
                                <li> ${this.$t('cron_job_settings_warning',{
                                    documentation: `<a href='https://mailer.gainhq.com/documentation/important-settings.html#schedulerAndQueue' target='_blank'>${this.$t('documentation')}</a>`
                                })}</li>`,
                    mailgun: `<li> ${this.$t('delivery_settings_message_mailgun', {
                                endpoint: urlGenerator(`webhook/mailgun`),
                                doc: `<a href='${urlGenerator('/documentation/#delivery-settings')}' target='_blank'>doc</a>`
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
                confirmationModalActive: false,
            }
        },
        methods: {
            submitData() {
                this.loading = true;
                this.message = '';
                this.errors = [];
                this.save(this.mailSettings);
            },

            afterSuccess({data}) {
                this.scrollToTop(false);
                this.setSMTPQuotaNote();
                this.toastAndReload(data.message);
                this.create_state = false;
                this.testMailButton = true;
            },
            getMailSettings(provider = '') {
                const p = provider ? `/${provider}` : '';
                const url = `${brand_delivery_settings}${p}`;
                axiosGet(url).then(({data}) => {
                    this.is_using_default_settings = !Object.keys(data).length && !provider;
                    if (Object.keys(data).length && !this.is_using_default_settings) {
                        this.testMailButton = true
                    }
                    if (data.provider) {
                        this.mailSettings = Object.keys(data).length ? data : {provider: 'mailgun'};
                        if (data.provider === 'smtp')  {
                            this.setInitialSMTPConfigs(data)
                        }
                        this.create_state = false;
                    }
                    else {
                        this.mailSettings = {provider: this.mailSettings.provider}
                    }

                    if (provider === 'smtp') {
                        this.setInitialSMTPConfigs(data)
                    }
                }).finally(() => this.is_loading = false);
            },

            setInitialSMTPConfigs(data) {
                this.mailSettings.smtp_daily_quota = data?.smtp_daily_quota || 0;
                this.mailSettings.smtp_hourly_quota = data?.smtp_hourly_quota || 0;
                this.mailSettings.smtp_monthly_quota = data?.smtp_monthly_quota || 0;
                this.mailSettings.smtp_encryption = data?.smtp_encryption || 'tls';
            },
            setSMTPQuotaNote() {
                axiosGet(brand_delivery_quotas).then(({data}) => {
                    if (Object.keys(data).length) {
                        let note = this.$t('brand_delivery_quota_message_1', {
                            type: data.type,
                            count: data.count,
                        });
                        this.notes.smtp = note + `</br>` + this.$t('brand_delivery_quota_message_2') + `</br>` + `- ${this.$t('cron_job_settings_warning',{
                            documentation: `<a href='https://mailer.gainhq.com/documentation/important-settings.html#schedulerAndQueue' target='_blank'>${this.$t('documentation')}</a>`
                        })}`
                    }
                });
            },
            deleteDeliverySetting(){
                this.is_loading = true;
                axiosDelete(brand_delivery_settings_delete).then(({data})=>{
                    this.confirmationModalActive = false;
                    this.$toastr.s(data.message);
                    setTimeout(()=>{
                        location.reload();
                    },2000)
                }).catch(({response}) => {
                    this.$toastr.e(response.data.message);
                }).finally(()=>{
                    this.is_loading = false;
                })
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
        created() {
            this.$store.dispatch('getConfig');
        },
        watch: {
            'mailSettings.provider': {
                handler: function (provider) {
                    this.getMailSettings(provider);
                    if (provider == 'smtp') {
                        this.setSMTPQuotaNote()
                    }
                },
                immediate: true
            }
        }
    }
</script>
