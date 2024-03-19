import {custom_fields} from "../../../config/apiUrl";
import DatatableHelperMixin from "../../../Mixins/Global/DatatableHelperMixin";

export default {
    mixins: [DatatableHelperMixin],
    data() {
        return {
            options: {
                name: this.$t('custom_fields'),
                url: custom_fields,
                showHeader: true,
                tableShadow: false,
                showManageColumn: false,
                tablePaddingClass: 'px-0 py-primary',
                columns: [
                    {
                        title: Vue.prototype.$t('name'),
                        type: 'text',
                        key: 'name',
                        uniqueKey: 'id',
                    },
                    {
                        title: this.$t('context'),
                        type: 'custom-html',
                        key: 'context',
                        isVisible: true,
                        modifier: (value) =>  {
                            return this.$t(value);
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

                ],
                paginationType: "loadMore",
                responsive: true,
                rowLimit: 10,
                showAction: true,
                actionType: "default",
                actions: [
                    {
                        title: this.$t('edit'),
                        icon: 'edit',
                        type: 'modal',
                        component: 'app-brand-add-edit-modal',
                        modalId: 'brand-add-edit-modal',
                        name: 'edit'
                    }, {
                        title: this.$t('delete'),
                        icon: 'trash',
                        type: 'modal',
                        component: 'app-confirmation-modal',
                        modalId: 'app-confirmation-modal',
                        url: `/${custom_fields}`,
                        name: 'delete'
                    }
                ],
            }
        }
    }
}
