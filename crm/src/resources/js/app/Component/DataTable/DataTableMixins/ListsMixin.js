import {list_view, lists} from "../../../config/apiUrl";
import {formatDateToLocal, numberFormatter} from "../../../Helpers/helpers";
import DatatableHelperMixin from "../../../Mixins/Global/DatatableHelperMixin";

export default  {
    mixins: [DatatableHelperMixin],
    data() {
        return {
            options: {
                name: 'ListTable',
                url: lists,
                showHeader: true,
                showCount: true,
                showClearFilter: true,
                columns: [
                    {
                        title: this.$t('name'),
                        type: 'link',
                        key: 'name',
                        modifier: (value, row) => {
                            return `${list_view}/${row.id}/view`;
                        }
                    },
                    {
                        title: this.$t('added'),
                        type: 'custom-html',
                        key: 'created_at',
                        isVisible: true,
                        modifier: date => formatDateToLocal(date)
                    },
                    {
                        title: this.$t('type'),
                        type: 'custom-html',
                        key: 'type',
                        modifier: (value) => {
                            if (value === 'dynamic')
                                return `<span class='badge badge-pill badge-success'>${this.$t('status_'+value)}</span>`;
                            return `<span class='badge badge-pill badge-purple'>${this.$t('status_'+value)}</span>`;
                        }
                    },
                    {
                        title: this.$totalLabel('subscribers'),
                        type: 'dynamic-content',
                        key: 'subscribers_count',
                        label: this.$t('show'),
                        icon: 'eye',
                        className: 'btn btn-sm btn-success',
                        modifier: (subscribers_count, row) => {
                            return{
                                isValue: row.type !== 'dynamic',
                                value: row.type !== 'dynamic' ? numberFormatter(subscribers_count) : `${lists}/${row.id}/subscribers/count`
                            }
                        }
                    },
                    {
                        title: this.$t('no_of_segment'),
                        type: 'text',
                        key: 'segments_count',
                    },
                    {
                        title: this.$t('actions'),
                        type: 'action',
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
                        title: this.$t('type'),
                        type: "radio",
                        key: "type",
                        header: {
                            "title": this.$t('Want to filter your subject', { subject: this.$t('list') }),
                            "description": this.$t("You can filter the list based on their type", { type:this.$t('type').toLowerCase() })
                        },
                        option: [
                            {
                                id: 'dynamic',
                                value: this.$t('status_dynamic')
                            },
                            {
                                id: 'imported',
                                value: this.$t('status_imported')
                            }
                        ]
                    },
                    {
                        title: this.$t('segment'),
                        type: "radio",
                        key: "with_segment",
                        header: {
                            "title": this.$t('Want to filter your subject', { subject: this.$t('list') }),
                            "description": this.$t("You can filter the list which have segment and which doesn't have")
                        },
                        option: [
                            {
                                id: 1,
                                value: this.$fieldTitle('with', 'segment')
                            },
                            {
                                id: 2,
                                value: this.$fieldTitle('without', 'segment')
                            },
                        ]
                    }
                ],
                paginationType: "pagination",
                responsive: true,
                rowLimit: 10,
                showAction: true,
                actionType: 'dropdown',
                orderBy: 'desc',
                actions: [
                    {
                        title: this.$t('copy'),
                        icon: 'copy',
                        type: 'page',
                        url: `/${lists}`,
                        name: 'copy',
                        modifier: () => this.$can('create_lists')
                    },
                    {
                        title: this.$t('edit'),
                        icon: 'edit',
                        type: 'page',
                        url: `/${lists}`,
                        name: 'edit',
                        modifier: () => this.$can('update_lists')
                    },
                    {
                        title: this.$t('delete'),
                        icon: 'trash',
                        type: 'modal',
                        component: 'app-confirmation-modal',
                        modalId: 'app-confirmation-modal',
                        url: `/${lists}`,
                        name: 'delete',
                        modifier: () => this.$can('delete_lists')
                    }
                ],
            }
        }
    }
}
