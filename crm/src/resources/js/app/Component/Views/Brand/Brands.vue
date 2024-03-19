<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('brands')" icon="server">
            <app-default-button
                v-if="$can('create_brands')"
                :title="$addLabel('brand')"
                @click="isBrandModalActive = true"
            />
        </app-page-top-section>

        <app-table
            id="brand-table"
            :options="options"
            @action="triggerActions"
        />

        <app-brand-modal
            v-if="isBrandModalActive"
            v-model="isBrandModalActive"
            :selected-url="selectedUrl"
        />

        <app-confirmation-modal
            :message="promptMessage"
            :modal-class="modalClass"
            :icon="promptIcon"
            v-if="confirmationModalActive"
            modal-id="app-confirmation-modal"
            @confirmed="triggerConfirm"
            @cancelled="cancelled"
        />

        <attach-user
            v-if="attachUserModal"
            v-model="attachUserModal"
            :brand="brand"
        />
    </div>
</template>

<script>
    import BrandMixin from "../../DataTable/DataTableMixins/BrandMixin";
    import AttachUser from "./AttachUser";
    import {axiosPost} from "../../../Helpers/AxiosHelper";

    export default {
        mixins: [BrandMixin],
        components: { AttachUser },
        data() {
            return {
                promptMessage: '',
                modalClass: '',
                promptIcon: '',

                triggerActionType: '',

                isBrandModalActive:false,
                brand: '',
                selectedUrl: '',
                attachUserModal: false,
                url: ''
            }
        },
        methods:{
            triggerActions(row, action, active) {

                if (action.name === 'attach_user') {
                    this.attachUserModal = true
                    this.brand = row;
                }
                else if (action.name === 'status') {
                    this.url = action.url.replace('{brand}', row.id)
                    this.setPrompt(action);
                    this.confirmationModalActive = true;
                    this.triggerActionType = 'status'
                }
                else if (action.name === 'edit') {
                    this.selectedUrl = `${action.url}/${row.id}`;
                    this.isBrandModalActive = true;
                }
                else {
                    this.triggerActionType = 'delete'
                    this.setPrompt(action);
                    this.getAction(row, action, active)
                }
            },

            triggerConfirm() {
                if (this.triggerActionType === 'status') {
                    axiosPost(this.url).then(({data}) => {
                        this.toastAndReload(data.message, 'brand-table')
                    })
                }
                if (this.triggerActionType === 'delete') {
                    this.confirmed('brand-table')
                }
            },

            setPrompt(action) {
                this.promptMessage = action.promptMessage;
                this.promptIcon = action.promptIcon;
                this.modalClass = action.modalClass;
            }
        },
        watch: {
            isBrandModalActive: function (value) {
                if (!value){
                    this.selectedUrl = '';
                }
            }
        }


    }
</script>
