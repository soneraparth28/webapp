<template>
    <div class="content-wrapper">
        <app-page-top-section
            :directory="`${$t('campaigns')} | <a href='${urlGenerator(url.campaigns_front_end)}'>${$t('back_to',
            {destination: $allLabel('campaigns')} )}</a>`"
            :hide-button="true"
            :title="$fieldTitle(selectedUrl ? 'edit' : 'add', 'campaign', true)"
            icon="sun"
        />

        <app-overlay-loader v-if="preloader"/>

        <div v-else class="card border-0 card-with-shadow">
            <div class="card-body">
                <app-note
                    v-if="!hasDeliverySetting"
                    :title="$t('no_delivery_settings_found')"
                    :notes="`<li>${$t('no_brand_delivery_settings_warning', {
                                    location: `<a href='${urlGenerator(brand_mail_settings_view)}'>${$t('here')}</a>`
                                 })}</li>
                                 <li>${this.$t('cron_job_settings_warning',{
                                    documentation: `<a href='https://mailer.gainhq.com/documentation/important-settings.html#schedulerAndQueue' target='_blank'>${this.$t('documentation')}</a>`
                                 })}</li>`"
                    content-type="html"
                />
                <app-form-wizard
                    v-else
                    :enable-all="selectedUrl != null"
                    :tab-init="init"
                    :tabs="tabs"
                    @disabledTab="showErrorToast"
                    @selectedComponentName="getSelectedCard"
                />
            </div>
        </div>

    </div>
</template>

<script>
import {FormMixin} from "../../../../core/mixins/form/FormMixin.js";
import FormHelperMixins from "../../../Mixins/Global/FormHelperMixins";
import {
    brand_mail_settings_view,
    campaigns,
    campaigns_front_end,
    check_brand_mail_settings
} from "../../../config/apiUrl";
import {collection} from "../../../Helpers/helpers";
import {axiosGet} from "../../../Helpers/AxiosHelper";

export default {
    name: "CampaignsCreateEdit",

    mixins: [FormMixin, FormHelperMixins],
    props: ['tabInit', 'id', 'dMessage', 'tags'],

    data() {
        return {
            preloader: false,
            url: {
                campaigns,
                campaigns_front_end
            },
            preventRedirect: null,
            tabs: [
                {
                    name: this.$t("details"),
                    component: "app-campaign-details",
                    props: {}
                },
                {
                    name: this.$t("delivery"),
                    component: "app-campaign-delivery",
                    props: {}
                },
                {
                    name: this.$t("audience"),
                    component: "app-campaign-audience",
                    props: {}
                },
                {
                    name: this.$t("content"),
                    component: "app-campaign-content",
                    props: {}
                },
                {
                    name: this.$t("confirmation"),
                    component: "app-campaign-confirmation",
                    props: {}
                },
            ],
            init: 0,
            hasDeliverySetting: false,
            brand_mail_settings_view,
        }
    },
    methods: {
        afterSuccessFromGetEditData({data}) {
            this.preloader = false;
            this.tabs.forEach(tab => {
                if (tab.component === 'app-campaign-content') {
                    tab.props = {...data, campaign_id: this.id, tags: this.tags};
                } else {
                    tab.props = {...data, campaign_id: this.id};
                }
            });
            this.preventRedirect = true
        },

        getSelectedCard(card) {
            const tab = collection(this.tabs).find('app-campaign-confirmation', 'component')
            const campaign = tab.props;
            this.$hub.$emit('selectedCard', {
                card,
                campaign: {
                    ...campaign, campaign_id: this.id
                },
                message: this.dMessage
            })
            if (!this.isCreateState) {
                this.preventRedirect = true
            }
        },

        showErrorToast() {
            this.$toastr.e(this.$t('please_complete_the_first_step'))
        },
        checkDeliverySetting() {
            this.preloader = true;
            axiosGet(check_brand_mail_settings)
                .then(({data}) => {
                    if (Number(data.brand)) {
                        this.hasDeliverySetting = true;
                    }
                }).finally(() => {
                this.preloader = false;
            })
        }
    },
    computed: {
        isCreateState() {
            return !this.selectedUrl;
        }
    },
    mounted() {
        this.$hub.$on('selectedCard', ({card, campaign, message}) => {
            this.scrollToTop()
        });
    },
    created() {
        this.checkDeliverySetting();
        this.init = this.tabInit;
    },
    watch: {
        preventRedirect: function (flag) {
            if (flag) {
                window.onbeforeunload = function () {
                    return flag;
                }
            } else {
                window.onbeforeunload = flag
            }
        }
    }
}
</script>