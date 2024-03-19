import DatatableHelperMixin from "../../../Mixins/Global/DatatableHelperMixin";
import {brand_users} from "../../../config/apiUrl";

export default {
    mixins: [DatatableHelperMixin],
    data() {
        const url = this.alias === 'brand' ? brand_users : 'admin/auth/users'
        return {
            options: {
                name: this.$t('users'),
                datatableType: 'cardView',
                url,
                tablePaddingClass: 'pt-0',
                datatableWrapper: false,
                showHeader: false,
                tableShadow: false,
                showSearch: false,
                showFilter: false,
                columns: [
                    {
                        title: this.$t('user'),
                        type: 'component',
                        key: 'profile_picture',
                        componentName: 'app-user-media',
                    },
                    {
                        title: this.$t('status'),
                        type: 'custom-html',
                        key: 'status',
                        modifier: (value) => {
                            return `<span class="badge badge-pill badge-${value.class}">${value.translated_name}</span>`;

                        }
                    },
                    {
                        title: this.$t('actions'),
                        type: 'action',
                        key: 'invoice',
                        isActive: true
                    },

                ],
                filters: [ ],
                paginationType: "loadMore",
                responsive: true,
                rowLimit: 10,
                showAction: true,
                actionType: "dropdown",
                actions: [
                    {
                        title: this.$t('edit'),
                        icon: 'edit',
                        type: 'modal',
                        component: 'app-user-modal',
                        modalId: 'user-modal-open',
                        name: 'edit',
                        modifier: () => this.$can('update_users')
                    },
                    {
                        title: this.$t('delete'),
                        icon: 'trash',
                        type: 'modal',
                        component: 'app-confirmation-modal',
                        modalId: 'app-confirmation-modal',
                        name: 'delete',
                        url: this.alias === 'brand' ? brand_users : '/admin/auth/users',
                        modifier: () => this.$can('delete_users')
                    },
                    {
                        title: this.$t('activate'),
                        type: 'action',
                        alias: 'status_active',
                        modifier: row => (row.status.name === 'status_inactive' && this.$can('update_users'))
                    },
                    {
                        title: this.$t('de_activate'),
                        type: 'action',
                        alias: 'status_inactive',
                        modifier: row => (row.status.name === 'status_active' && this.$can('update_users'))
                    },
                    {
                        title: this.$fieldTitle('manage', 'role', true),
                        type: 'modal',
                        component: 'app-change-role-modal',
                        modalId: 'change-role-modal',
                        name: 'change_role',
                        modifier: () => this.$can('attach_roles_users')
                    }
                ],
            },
            userStatuses: []
        }
    }
}
