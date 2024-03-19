<template>
    <div :class="wrapper ? 'content-wrapper' : ''">
        <app-page-top-section
            :title="$t('emails')"
            icon="mail"
            v-if="topBar">
        </app-page-top-section>

        <app-table
            :options="options"
            @action="triggerActions"
            id="email-table"
        />

        <app-template-preview-modal
            modalId="template-preview"
            :title="$t('email')"
            :sourceURL="email_template_url"
            :isActionsActive="false"
            v-if="isEmailPreviewModalActive"
            @close="isEmailPreviewModalActive = false"
        />

        <app-confirmation-modal
            @cancelled="cancelled"
            @confirmed="confirmed('email-table')"
            modal-id="app-confirmation-modal"
            v-if="confirmationModalActive"
        />
    </div>
</template>

<script>
    import {email_logs} from '../../../config/apiUrl';
    import EmailLogMixin from "../../DataTable/DataTableMixins/EmailLogMixin";
    import {mapState} from "vuex";
    import {axiosGet} from "../../../Helpers/AxiosHelper";

    export default {
        mixins: [EmailLogMixin],
        props: {
            url: {
                type: String,
                default: email_logs
            },
            wrapper: {
                type: Boolean,
                default: true
            },
            topBar: {
                type: Boolean,
                default: true
            },
            hideCampaignName: {
                type: Boolean,
                default: false
            },
            hideCampaignFilter: {
                type: Boolean,
                default: false
            }

        },

        data() {
            return {
                email_template: '',
                email_template_url: '',
                isEmailPreviewModalActive: false,
                loading: false
            }
        },

        methods: {
            triggerActions(row, action, active) {
                if (action.name === 'email-preview') {
                    this.email_template_url = `${email_logs}/${row.id}`
                    this.isEmailPreviewModalActive = true;
                } else {
                    this.getAction(row, action, active)
                }
            },
        },
        created() {
            this.$store.dispatch('getCampaigns')
        },
        computed: {
            ...mapState({
                selectableCampaigns: ({campaign}) => campaign.campaigns
            }),
            campaignsObjectWatcher() {
                return this.selectableCampaigns.length
            }
        },
        watch: {
            campaignsObjectWatcher: function (length) {
                if (length) {
                    this.options.filters.find(({key, option}) => {
                        if (key === 'campaign') option.push(...this.selectableCampaigns)
                    })
                }
            }
        }


    }
</script>
