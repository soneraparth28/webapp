import {formatDateToLocal, onlyTime} from "../../../Helpers/helpers";
import {campaigns, email_logs} from "../../../config/apiUrl";
import DatatableHelperMixin from "../../../Mixins/Global/DatatableHelperMixin";
import {urlGenerator} from "../../../Helpers/AxiosHelper";

export default {
    mixins: [DatatableHelperMixin],
    data() {
        return {
            options: {
                name: this.$t('email'),
                url: this.url,
                showHeader: true,
                columns: [
                    {
                        title: this.$t('date'),
                        type: 'custom-html',
                        key: 'email_date',
                        modifier: date => date ? formatDateToLocal(date) : ''
                    },
                    {
                        title: this.$t('time'),
                        type: 'custom-html',
                        key: 'email_date',
                        modifier: date => date ? onlyTime(date) : ''
                    },
                    {
                        title: this.$t('emails'),
                        type: 'object',
                        key: 'subscriber',
                        modifier: subscriber => subscriber.email

                    },
                    this.hideCampaignName ? {} :
                    {
                        title: this.$fieldTitle('campaign', 'name'),
                        type: 'custom-html',
                        key: 'campaign',
                        modifier: (campaign) => {
                            return `<a href="${urlGenerator(`/${campaigns}/${campaign.id}/view`)}">
                                   ${campaign.name}
                                </a>`
                        },
                        isVisible: true
                    },
                    {
                        title: this.$t('status'),
                        type: 'custom-html',
                        key: 'status',
                        isVisible: true,
                        modifier: status => {
                            return `<span class="badge badge-pill badge-${status.class}">
                                    ${status.translated_name}
                                </span>`
                        }
                    },
                    {
                        title: this.$t('marked_as_spam'),
                        type: 'custom-html',
                        key: 'is_marked_as_spam',
                        isVisible: true,
                        modifier: flag => {
                            return `<span class="badge badge-pill badge-${flag ? 'primary' : 'secondary'}">
                                    ${this.$t(flag ? 'yes' : 'no')}
                                </span>`
                        }
                    },
                    {
                        title: this.$t('actions'),
                        type: 'action',
                        key: 'invoice',
                        isVisible: true
                    },

                ],
                filters: [
                    {
                        title: this.$t('created'),
                        type: "range-picker",
                        key: "date",
                        option: ["today", "thisMonth", "last7Days", "thisYear"]
                    },
                    {
                        title: this.$t('status'),
                        type: "checkbox",
                        key: "status",
                        option: [],
                        listValueField: 'value'
                    },
                    this.hideCampaignFilter ? {} :
                    {
                        title: this.$t('campaigns'),
                        type: "drop-down-filter",
                        key: "campaign",
                        option: [],
                        listValueField: 'name'
                    },
                ],
                paginationType: "pagination",
                responsive: true,
                rowLimit: 10,
                showAction: true,
                actionType: 'default',
                orderBy: 'desc',
                showCount: true,
                showClearFilter: true,
                actions: [
                    {
                        title: this.$t('eye'),
                        icon: 'eye',
                        type: 'modal',
                        component: 'app-template-preview-modal',
                        modalId: 'template-preview',
                        name: 'email-preview'
                    },
                    {
                        title: this.$t('delete'),
                        icon: 'trash',
                        type: 'modal',
                        component: 'app-confirmation-modal',
                        modalId: 'app-confirmation-modal',
                        name: 'delete',
                        url: `/${email_logs}`,
                        modifier: () => this.$can('delete_emails')
                    }
                ],
            }
        }
    },


    created() {
        this.$store.dispatch('getStatuses', 'email');
    },
}
