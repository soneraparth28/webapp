import {campaigns} from "../../../config/apiUrl";
import DatatableHelperMixin from "../../../Mixins/Global/DatatableHelperMixin";

export default {
    mixins: [DatatableHelperMixin],
    data() {
        return {
            options: {
                name: 'Campaigns',
                url: campaigns,
                showHeader: true,
                columns: [
                    {
                        title: this.$t('name'),
                        type: 'link',
                        key: 'name',
                        modifier: (value, row) => {
                            return `${campaigns}/${row.id}/view`;
                        },
                        sortAble: true,
                        isVisible: true
                    },
                    {
                        title: this.$t('status'),
                        type: 'custom-html',
                        key: 'status',
                        isVisible: true,
                        modifier: (status, row) => {
                            return row.current_status === 'archived' ? `<span class="badge badge-sm badge-pill badge-danger">
                                    ${this.$t('archived')}
                                </span>` : `<div class="d-flex justify-content-start"><span class="badge badge-sm badge-pill badge-${status.class}">
                                    ${status.translated_name}
                                </span> ${row.current_status !== 'active' ? `<span class="badge badge-sm badge-pill badge-secondary ml-1">
                                    ${this.$t(row.current_status)}
                                </span>` : ''}</div>`
                        }
                    },
                    {
                        title: this.$t('time_period'),
                        type: 'custom-html',
                        key: 'time_period',
                        isVisible: true,
                        modifier: time_period => time_period ? this.$t(time_period) : ''
                    },

                    {
                        title: this.$t('recipients'),
                        type: 'dynamic-content',
                        key: 'subscribers_count',
                        label: this.$t('show'),
                        icon: 'eye',
                        className: 'btn btn-sm btn-success',
                        modifier: (count, row) => {
                            return {
                                isValue: false,
                                value: `${campaigns}/${row.id}/subscribers/count`
                            }
                        }
                    },

                    {
                        title: this.$t('click_rate'),
                        type: 'dynamic-content',
                        key: 'click_rate',
                        label: this.$t('show'),
                        icon: 'eye',
                        className: 'btn btn-sm btn-secondary',
                        modifier: (count, row) => {
                            return {
                                isValue: false,
                                value: `${campaigns}/${row.id}/email-logs/rates?type=clicked`
                            }
                        }
                    },
                    {
                        title: this.$t('open_rate'),
                        type: 'dynamic-content',
                        key: 'open_rate',
                        label: this.$t('show'),
                        icon: 'eye',
                        className: 'btn btn-sm btn-secondary',
                        modifier: (count, row) => {
                            return {
                                isValue: false,
                                value: `${campaigns}/${row.id}/email-logs/rates?type=opened`
                            }
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
                    // {
                    //     title: this.$t('sending_rate'),
                    //     type: "range-filter",
                    //     key: "sending rate"
                    // },
                    {
                        title: this.$t('status'),
                        type: "checkbox",
                        key: "status",
                        option: [],
                        listValueField: 'value'
                    },
                    {
                        title: this.$t('time_period'),
                        type: "radio",
                        key: "time_period",
                        header: {
                            "title": this.$t('Want to filter your subject', {subject: this.$t('campaign')}),
                            "description": this.$t("You can filter the list based on their type", {type: this.$t('time_period').toLowerCase()})
                        },
                        option: [
                            {id: 'immediately', value: this.$t('immediately')},
                            {id: 'once', value: this.$t('once')},
                            {id: 'hourly', value: this.$t('hourly')},
                            {id: 'daily', value: this.$t('daily')},
                            {id: 'weekly', value: this.$t('weekly')},
                            {id: 'monthly', value: this.$t('monthly')},
                            {id: 'yearly', value: this.$t('yearly')}
                        ]
                    },
                    {
                        title : this.$t('show_archived'),
                        type: 'toggle-filter',
                        key: 'archived',
                        buttonLabel: {
                            active: 'Yes',
                            inactive: 'No'
                        },
                        header: {
                            title: this.$t('show_archived_campaign'),
                            description: this.$t('filter_data_which_are_archived')
                        }
                    },
                ],
                paginationType: "pagination",
                responsive: true,
                rowLimit: 10,
                showAction: true,
                actionType: 'dropdown',
                showCount: true,
                showClearFilter: true,
                actions: [
                    {
                        title: this.$t('copy'),
                        icon: 'copy',
                        type: 'none',
                        name: 'duplicate',
                        modifier: (row) => row.current_status !== 'archived' && this.$can('duplicate_campaigns')
                    },
                    {
                        title: this.$t('edit'),
                        icon: 'edit',
                        type: 'page',
                        name: 'edit',
                        url: `/${campaigns}`,
                        modifier: (row) => {
                            return row.current_status !== 'archived' && this.$can('update_campaigns')
                        }
                    },
                    {
                        title: this.$t('resume'),
                        icon: 'edit',
                        name: 'change_status',
                        status: 'resume',
                        url: `/${campaigns}`,
                        modifier: (row) => {
                            return row.current_status === 'paused' && this.$can('pause_or_resume_campaigns')
                        }
                    },
                    {
                        title: this.$t('pause'),
                        icon: 'edit',
                        name: 'change_status',
                        status: 'pause',
                        url: `/${campaigns}`,
                        modifier: (row) => {
                            return row.current_status === 'active'
                                && row.time_period !== 'immediately'
                                && row.status.name !== 'status_processing'
                                && row.status.name !== 'status_draft'
                                && this.$can('pause_or_resume_campaigns')
                        }
                    },
                    {
                        title: this.$t('archive'),
                        icon: 'trash',
                        type: 'modal',
                        component: 'app-confirmation-modal',
                        modalId: 'app-confirmation-modal',
                        name: 'delete',
                        url: `/${campaigns}`,
                        modifier: (row) => {
                            return row.current_status !== 'archived' && row.status.name !== 'status_processing' && this.$can('delete_campaigns')
                        }
                    }
                ],
            }
        }
    }
}
