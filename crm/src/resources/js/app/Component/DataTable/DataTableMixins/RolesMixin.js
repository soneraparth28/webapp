import DatatableHelperMixin from "../../../Mixins/Global/DatatableHelperMixin";
import {brand_roles} from "../../../config/apiUrl";

export default {
    mixins: [DatatableHelperMixin],
    data() {
        const url = this.alias === 'brand' ? brand_roles : 'admin/auth/roles?type=app'
        return {
            manage: false,
            options: {
                name: 'roles',
                url,
                columns: [
                    {
                        title: this.$fieldTitle('role', 'name'),
                        type: 'text',
                        key: 'name',
                        sortAble: true,
                    },
                    this.$can('update_roles') ?
                    {
                        title: this.$t('permission'),
                        type: 'button',
                        key: 'id',
                        className: 'btn btn-sm btn-primary px-3 py-1',
                        actionType: 'manage',
                        modifier: (id, role) => {
                            return role.is_default ? false : this.$t('manage')
                        }
                    }
                    : {},
                    {
                        title: this.$t('users'),
                        type: 'component',
                        key: 'users',
                        isVisible: true,
                        componentName: 'image-group',
                    },
                    {
                        title: this.$t('actions'),
                        type: 'action',
                        key: 'invoice',
                        isActive: true
                    },
                ],
                datatableWrapper: false,
                showHeader: true,
                tableShadow: false,
                showSearch: false,
                showFilter: false,
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
                        component: 'app-roles-modal',
                        modalId: 'role-modal',
                        url: '',
                        name: 'edit',
                        modifier: role => this.$can('update_roles') && !role.is_default
                    },
                    {
                        title: this.$t('delete'),
                        icon: 'trash',
                        component: 'app-confirmation-modal',
                        type: 'modal',
                        modalId: 'role-delete-modal',
                        url: '',
                        name: 'delete',
                        modifier: role => this.$can('delete_roles') && !role.is_default
                    },
                    {
                        title: this.$fieldTitle('manage', 'users', true),
                        icon: 'edit',
                        type: 'none',
                        modalId: 'role-manage-modal',
                        url: '',
                        name: 'manage-roles',
                        modifier: () => this.$can('attach_users_to_roles')
                    }
                ],
            },
        }
    }
}
