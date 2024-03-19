<template>
    <div>
      <note
          :title="$t('note')"
          :description="$t('custom_field_warning_message')"
      />
      <br>
        <app-table id="brand-custom-fields"
                   :options="options"
                   @action="triggerAction"/>

        <app-brand-custom-field-modal v-if="isCustomFiledModalOpen"
                                      v-model="isCustomFiledModalOpen"
                                      :selected-url="selectedUrl"/>

        <app-confirmation-modal v-if="confirmationModalActive"
                                modal-id="app-confirmation-modal"
                                @confirmed="confirmed('brand-custom-fields')"
                                @cancelled="cancelled"/>
    </div>
</template>

<script>
    import {brand_custom_field} from "../../../../../config/apiUrl";
    import DatatableHelperMixin from "../../../../../Mixins/Global/DatatableHelperMixin";
    import Note from "../../../../Helper/Note/Note";

    export default {
        name: "CustomFields",
        mixins: [DatatableHelperMixin],
        props: ['id'],
        components: {
          Note,
            'app-brand-custom-field-modal': require('./CustomFieldModal').default,
        },
        data() {
            return {
                isCustomFiledModalOpen: false,
                selectedUrl: '',
                customField: {},
                options: {
                    name: this.$t('custom_fields'),
                    url: brand_custom_field,
                    showHeader: true,
                    tableShadow: false,
                    showManageColumn: false,
                    tablePaddingClass: 'px-0 py-primary',
                    columns: [
                        {
                            title: this.$t('name'),
                            type: 'text',
                            key: 'name',
                            uniqueKey: 'id',
                        },
                        {
                            title: this.$t('context'),
                            type: 'custom-html',
                            key: 'context',
                            isVisible: true,
                            modifier: (value) => {
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
                            component: 'app-brand-custom-field-modal',
                            modalId: 'custom-field-modal',
                            name: 'edit'
                        },
                        {
                            title: this.$t('delete'),
                            icon: 'trash',
                            type: 'modal',
                            component: 'app-confirmation-modal',
                            modalId: 'app-confirmation-modal',
                            url: `/${brand_custom_field}`,
                            name: 'delete'
                        }
                    ],
                }
            }
        },
        methods: {
            triggerAction(row, action, active) {
                if (action.name === 'edit') {
                    this.selectedUrl = `${brand_custom_field}/${row.id}`;
                    this.isCustomFiledModalOpen = true;
                } else {
                    this.getAction(row, action, active)
                }
            },

            showAddForm() {
                this.isCustomFiledModalOpen = true;
            },
        },

        mounted() {
            $('#custom-field-modal').on('hidden.bs.modal', (e) => {
                this.isCustomFiledModalOpen = false;
            });

            this.$hub.$on('headerButtonClicked-'+this.id, () => {
                this.isCustomFiledModalOpen = true
            })
        },
        watch: {
            isCustomFiledModalOpen: {
                handler: function (isCustomFiledModalOpen) {
                    if (!isCustomFiledModalOpen) {
                        this.selectedUrl = null;
                    }
                }
            }
        },
        beforeDestroy() {
            this.$hub.$off('headerButtonClicked-'+this.id);
        }

    }
</script>
