import {segments} from "../../../config/apiUrl";
import {formatDateToLocal} from "../../../Helpers/helpers";
import DatatableHelperMixin from "../../../Mixins/Global/DatatableHelperMixin";
import {urlGenerator} from "../../../Helpers/AxiosHelper";

export default {
    mixins: [DatatableHelperMixin],
    data() {
        return {
            options: {
                name: this.$t('segments'),
                url: segments,
                showHeader: true,
                showCount: true,
                showClearFilter: true,
                columns: [
                    {
                        title: this.$t('name'),
                        type: 'custom-html',
                        key: 'name',
                        uniqueKey: 'id',
                        modifier: function (name, rowData) {
                            return `<a href="${urlGenerator(segments)}/${rowData.id}/edit">${name}</a>`
                        }
                    },
                    {
                        title: this.$t('number_of_rules'),
                        type: 'text',
                        key: 'segment_logic_count',
                        isVisible: true
                    },
                    {
                        title: this.$fieldTitle('subscribers'),
                        type: 'dynamic-content',
                        key: 'id',
                        isVisible: true,
                        label: this.$t('show'),
                        icon: 'eye',
                        className: 'btn btn-sm btn-success',
                        modifier: (count, row) => {
                            return{
                                isValue: false,
                                value:  `${segments}/${row.id}/subscribers/count`
                            }
                        }
                    },
                    {
                        title: this.$fieldTitle('added'),
                        type: 'custom-html',
                        key: 'created_at',
                        isVisible: true,
                        modifier: function (value) {
                            return formatDateToLocal(value)
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
                        "title": this.$t('created'),
                        "type": "range-picker",
                        "key": "date",
                        "option": ["today", "thisMonth", "last7Days", "thisYear"]
                    }
                ],
                paginationType: "pagination",
                responsive: true,
                rowLimit: 10,
                showAction: true,
                actionType: 'dropdown',
                actions: [
                    {
                        title: this.$t('copy'),
                        icon: 'copy',
                        type: 'page',
                        url: `/${segments}`,
                        name: 'copy',
                        modifier: () => this.$can('copy_segments')
                    },
                    {
                        title: this.$t('edit'),
                        icon: 'edit',
                        type: 'page',
                        url: `/${segments}`,
                        name: 'edit',
                        modifier: () => this.$can('update_segments')
                    },
                    {
                        title: this.$t('delete'),
                        icon: 'trash',
                        type: 'modal',
                        component: 'app-confirmation-modal',
                        modalId: 'app-confirmation-modal',
                        url: `/${segments}`,
                        name: 'delete',
                        modifier: () => this.$can('delete_segments')
                    }
                ],
            }
        }
    },
    created() {
        /*axiosGet(`${segments}/segment-rules-count`).then(response => {
            this.options.filters.push({
                "title": this.$t('number_of_rules'),
                "type": "range-filter",
                "key": "rules",
                "maxTitle": this.$t('max_subject', {subject: this.$t('number_of_rules')}),
                "minTitle": this.$t('min_subject', {subject: this.$t('number_of_rules')}),
                "maxRange": response.data.max_length,
                "minRange": response.data.min_length < response.data.max_length ? response.data.min_length: 0,
            })
        })*/
    }
}
