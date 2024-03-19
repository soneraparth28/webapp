import DatatableHelperMixin from "../../../Mixins/Global/DatatableHelperMixin";
import {urlGenerator} from "../../../Helpers/AxiosHelper";

export default {
    mixins: [DatatableHelperMixin],
    data: function () {
        return {
            options: {
                name: this.$t('brands'),
                url: 'admin/app/brands',
                showHeader: true,
                columns: [
                    {
                        isVisible: true,
                        title: this.$t('name'),
                        type: 'custom-html',
                        key: 'name',
                        modifier: function (name, rowData) {
                            return `<a href="${urlGenerator(`/brands/${rowData.short_name}/dashboard`)}" >${name}</a>`;
                        }
                    },
                    {
                        title: this.$t('visit_brand'),
                        type: 'custom-html',
                        key: 'short_name',
                        modifier: short_name => {
                            return `<a href="${urlGenerator(`/brands/${short_name}/dashboard`)}" class="btn btn-sm btn-primary text-white"><i data-feather="arrow-right"></i>&nbsp;${this.$t('go')}&nbsp;&nbsp;</a>`
                        }
                    },
                    {
                        isVisible: true,
                        title: this.$t('short_name'),
                        type: 'text',
                        key: 'short_name',

                    },
                    {
                        isVisible: true,
                        title: this.$t('subscribers'),
                        type: 'text',
                        key: 'subscribers_count',

                    },
                    {
                        isVisible: true,
                        title: this.$t('campaigns'),
                        type: 'text',
                        key: 'campaigns_count',

                    },
                    {
                        title: this.$t('status'),
                        type: 'custom-html',
                        key: 'status',
                        isVisible: true,
                        modifier: status => {
                            let className = 'success';
                            let name = this.$t('active');
                            if (status) {
                                className = status.class
                                name = status.translated_name
                            }
                            return `<span class="badge badge-pill badge-${className}">
                                    ${name}
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
                        option: ["today", "thisMonth", "last7Days", "nextYear"]
                    },
                ],
                paginationType: "loadMore",
                responsive: true,
                rowLimit: 10,
                showAction: true,
                orderBy: 'desc',
                actionType: "dropdown",
                actions: [
                    {
                        title: this.$t('activate'),
                        icon: 'check',
                        type: 'modal',
                        url: 'admin/app/brands/{brand}/update-status',
                        name: 'status',
                        promptMessage: this.$t('you_are_going_to_activate_this_brand'),
                        promptIcon: 'check-circle',
                        modalClass: 'primary',
                        modifier: row => {
                            if (row.status) {
                                return row.status.name === "status_inactive"
                            }
                            return false;
                        },
                    },
                    {
                        title: this.$t('de_activate'),
                        icon: 'disable',
                        type: 'modal',
                        url: 'admin/app/brands/{brand}/update-status',
                        name: 'status',
                        promptMessage: this.$t('you_are_going_to_deactivate_this_brand'),
                        promptIcon: 'slash',
                        modalClass: 'warning',
                        modifier: row => {
                            if (row.status) {
                                return row.status.name === "status_active"
                            }
                            return true;
                        },
                    },

                    {
                        title: this.$t('edit'),
                        icon: 'edit',
                        type: 'modal',
                        component: 'app-brand-modal',
                        modalId: 'brand-modal',
                        url: 'admin/app/brands',
                        name: 'edit',
                        modifier: () => this.$can('update_brands')

                    },
                    // {
                    //     title: this.$t('delete'),
                    //     icon: 'trash',
                    //     type: 'modal',
                    //     component: 'app-confirmation-modal',
                    //     modalId: 'app-confirmation-modal',
                    //     url: `/admin/app/brands`,
                    //     name: 'delete',
                    //     promptMessage: '',
                    //     promptIcon: '',
                    //     modalClass: '',
                    //     modifier: () => this.$can('delete_brands')
                    // }
                ],
            }
        }
    }
}
