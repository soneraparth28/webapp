<template>
    <div class="content-wrapper">
        <app-page-top-section
            :directory="`${$t('settings')}`"
            :title="$fieldTitle('brand', 'templates', true)"
            icon="settings">
            <app-default-button @click="redirectToCreate"
                                :title="$addLabel('templates')"
                                v-if="$can('create_templates')"
            />
        </app-page-top-section>

        <app-table id="app-templates"
                   :options="options"
                   @action="getAction" />

        <app-confirmation-modal
            v-if="isConfirmationModalActive"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed"
            @cancelled="cancelled"
        />


        <app-template-preview-modal
            :sourceURL="preview_template_url"
            :id="template.id"
            @close="isPreviewModalActive = false"
            v-if="isPreviewModalActive"
            :is-actions-active="false"
        />
    </div>
</template>

<script>
    import TemplateTable from "../../../../../DataTable/DataTableMixins/TemplateTable";
    import CoreLibrary from "../../../../../../../core/helpers/CoreLibrary";
    import {urlGenerator} from "../../../../../../Helpers/AxiosHelper";
    import { app_templates } from '../../../../../../config/apiUrl'

    export default {
        name: "Templates",
        mixins: [TemplateTable],
        extends: CoreLibrary,
        data() {
            return {
                isConfirmationModalActive: false,
                showModal: false,
                template: {},
                selected_url: '',
                preview_template_url: '',
                isPreviewModalActive: ''
            }
        },
        computed: {

        },
        methods: {
            showAddForm(){
                this.selected_url = '';
                this.showModal = true;
            },
            getAction(template, action, active){
                this.selected_url = '';
                if (action.actionType === 'edit') {
                    window.location = `${urlGenerator(app_templates)}/${template.id}/edit`
                }else if (action.actionType === 'delete') {
                    this.template = template;
                    this.isConfirmationModalActive = true;
                }else if (action.key === 'subject') {
                    this.preview_template_url = `${app_templates}/${template.id}/content`
                    this.isPreviewModalActive = true;
                }
            },
            confirmed() {
                this.axiosDelete(`${app_templates}/${this.template.id}`).then(res => {
                    this.$hub.$emit('reload-app-templates');
                    this.$toastr.s('', res.data.message);
                    this.isConfirmationModalActive = false;
                })
            },

            cancelled(){
                this.isConfirmationModalActive = false;
            },
            redirectToCreate() {
                window.location = urlGenerator('/admin/app/templates/create')
            }
        }
    }
</script>
