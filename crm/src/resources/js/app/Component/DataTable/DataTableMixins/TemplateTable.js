import {app_templates, templates} from '../../../config/apiUrl'
import {formatDateToLocal} from "../../../Helpers/helpers";
import {urlGenerator} from "../../../Helpers/AxiosHelper";

export default {
    data() {
        return {
            options: {
                name: this.$t('templates'),
                url: app_templates,
                showHeader: true,
                showManageColumn: false,
                columns: [
                    {
                        title: this.$t('subject'),
                        type: 'button',
                        key: 'subject',
                        sortAble: true,
                        isVisible: true,
                        className: 'btn btn-link text-primary pl-0',
                        modifier: subject => subject
                    },
                    {
                        title: this.$t('created'),
                        type: 'custom-html',
                        key: 'created_at',
                        modifier: createdAt => formatDateToLocal(createdAt),
                        isVisible: true,

                    },
                    {
                        title: this.$t('download'),
                        type: 'custom-html',
                        modifier: function (value, row) {
                            return `<a
                                    href="${urlGenerator(`/admin/app/templates/${row.id}/content`)}" download="${row.subject}">
                                    <i data-feather="download"/>
                                </a>`
                        },
                        isVisible: true,
                    },
                    {
                        title: this.$t('duplicate'),
                        type: 'custom-html',
                        modifier: function (value, row) {
                            return `<a
                                href="${urlGenerator(app_templates)}/${row.id}/copy">
                                <i data-feather="copy"/>
                            </a>`
                        },
                        isVisible: true,
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
                ],
                paginationType: "pagination",
                responsive: true,
                rowLimit: 10,
                showAction: true,
                actionType: "default",
                actions: [
                    {
                        title: this.$t('edit'),
                        icon: 'edit',
                        type: 'page',
                        url: '/admin/app/templates',
                        actionType: 'edit'
                    }, {
                        title: this.$t('delete'),
                        icon: 'trash',
                        component: 'app-confirmation-modal',
                        type: 'modal',
                        modalId: 'app-confirmation-modal',
                        url: '/admin/app/templates',
                        actionType: 'delete'
                    }
                ],
            }
        }
    }
}
