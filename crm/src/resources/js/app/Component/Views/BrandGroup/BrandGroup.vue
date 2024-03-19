<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('brand_group')" icon="aperture">
            <app-default-button
                :title="$addLabel('group')"
                @click="openModal()"
                v-if="$can('create_brand_groups')"
            />
        </app-page-top-section>

        <app-table
            id="brand-group-table"
            :options="options"
            @action="triggerActions"
        />

        <app-brand-group-modal
            v-if="isBrandGroupModalActive"
            v-model="isBrandGroupModalActive"
            :selected-url="selectedUrl"
            @close="isBrandGroupModalActive = false"
        />


        <app-confirmation-modal
            v-if="confirmationModalActive"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed('brand-group-table')"
            @cancelled="cancelled"
        />
    </div>
</template>

<script>
    import DatatableHelperMixin from "../../../Mixins/Global/DatatableHelperMixin";

    export default {
        mixins: [DatatableHelperMixin],
        components: {},
        data() {
            return {
                isBrandGroupModalActive:false,
                brandGroup:'',
                selectedUrl:'',
                options: {
                    name: this.$t('brand_groups'),
                    url: 'admin/app/brand-groups',
                    showHeader: true,
                    columns: [
                        {
                            title: this.$t('name'),
                            type: 'text',
                            key: 'name',
                            isVisible: true,
                        },
                        {
                            title: this.$t('number_of_brands'),
                            type: 'text',
                            key: 'brands_count',
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
                            option: ["today", "thisMonth", "last7Days", "nextYear"]
                        },
                    ],
                    paginationType: "loadMore",
                    responsive: true,
                    rowLimit: 10,
                    showAction: true,
                    orderBy: 'desc',
                    actionType: "default",
                    actions: [
                        {
                            title: this.$t('edit'),
                            icon: 'edit',
                            type: 'modal',
                            component: 'app-brand-group-modal',
                            modalId: 'app-modal',
                            modifier: () => this.$can('update_brand_groups')
                        },
                        {
                            title: this.$t('delete'),
                            icon: 'trash',
                            type: 'modal',
                            component: 'app-confirmation-modal',
                            modalId: 'app-confirmation-modal',
                            url: '/admin/app/brand-groups',
                            name: 'delete',
                            modifier: () => this.$can('delete_brand_groups')
                        }
                    ],
                }
            }
        },

        methods:{
            triggerActions(row, action, active){
                if (action.title === this.$t('edit')){
                    this.selectedUrl = `admin/app/brand-groups/${row.id}`;
                    this.isBrandGroupModalActive = true;
                } else {
                    this.getAction(row, action, active)
                }
            },

            openModal() {
                this.isBrandGroupModalActive = true;
                this.selectedUrl = ''
            },
        }

    }
</script>
