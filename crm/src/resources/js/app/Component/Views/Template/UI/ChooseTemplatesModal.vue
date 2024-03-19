<template>
    <app-modal
        :modal-id="modalId"
        modalSize="fullscreen"
        @close-modal="closeModal"
    >
        <template slot="header">
            <h5 class="modal-title">
                <app-icon name="cpu" /> {{$fieldTitle('template', 'gallery')}}
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true" class="close-btn"> <app-icon name="x"/></span>
            </button>
        </template>
        <template slot="body">
            <div class="row">
                <div class="col-12">
                    <h4 class="mb-4">{{`${$fieldTitle('our', 'template')} ${$t('gallery')}`}}</h4>
                    <app-card-view
                        id="template-lists"
                        :properties="properties"
                        @selectedTemplateCard="selectTemplate"
                    />
                </div>
            </div>
        </template>
    </app-modal>
</template>

<script>
    import {app_templates, brand_templates} from "../../../../config/apiUrl";

    export default {
        name: "ChooseTemplatesModal",
        props: {
            modalId: {
                type: String,
                require: true
            },
            alias: {
                require: true,
            }
        },
        data() {
            const url = {
                brand: brand_templates,
                app: app_templates
            }[this.alias].split('/').filter(d => d).join('/');
            return {
                properties: {
                    filters: [],
                    showFilter: false,
                    url,
                    showAction: false,
                    cardLimit: 5,
                    previewType: 'html'
                }
            }
        },
        methods: {
            selectTemplate(payload) {
                this.$emit('selected', payload)
                this.closeModal()
            },
            closeModal() {
                this.$emit('close')
                $('#' + this.modalId).modal('hide')
            }
        }
    }
</script>

<style scoped>

</style>
