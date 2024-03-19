<template>
    <div class="content-wrapper">
        <app-page-top-section
            :title="$t('card_view')"
            :directory="`${$t('templates')} | <a href='${urlGenerator(api.list)}'>${$t('back_to', {destination: $t('list_view')})}</a>`"
            icon="cpu"
        >
            <app-default-button :url="api.create" :title="$addLabel('template')" />
        </app-page-top-section>

        <app-card-view
            :properties="properties"
            @action="triggerAction"
            id="template-card-view"
        />

        <app-template-preview-modal
            :isActionsActive="false"
            :sourceURL="preview_template_url"
            :title="preview_template_title"
            :id="template_id"
            @deletable="deletable"
            @close="isPreviewModalActive = false"
            v-if="isPreviewModalActive"
        />

        <app-confirmation-modal
            @cancelled="cancelled"
            @confirmed="triggerConfirmed('template-card-view')"
            modal-id="app-confirmation-modal"
            v-if="confirmationModalActive"
        />

    </div>
</template>

<script>
    import {template_create, templates, templates_list_view} from '../../../config/apiUrl';
    import TemplateCardMixin from "../../DataTable/DataTableMixins/TemplateCardMixin";
    import TemplateHelperMixin from "../../DataTable/DataTableMixins/Helper/TemplateHelperMixin";
    import {axiosGet, urlGenerator} from "../../../Helpers/AxiosHelper";

    export default {
        name: 'TemplateCardView',
        mixins: [TemplateCardMixin, TemplateHelperMixin],
        data() {
            return {
                api: {
                    create: `${template_create}?view=card_view`,
                    list: `/${templates_list_view}`
                },
                selectedTemplateContent: '',
                template_id: '',
                brand_id: '',
                isPreviewModalActive: false,
                preview_template_url: '',
                preview_template_title: '',
                urlGenerator
            }
        },
        methods: {
            triggerAction(row, action, active) {

                const content = row.custom_content ? row.custom_content : row.default_content;

                if(action.type === 'download') {
                    this.downloadTemplate(row)
                }
                else if (action.name === 'preview') {
                    this.template_id = row.id
                    this.brand_id = row.brand_id
                    this.preview_template_url = `${templates}/${row.id}/content`;
                    this.preview_template_title = row.subject;
                    this.isPreviewModalActive = true
                }else if (action.type === 'edit') {
                    if (!row.brand_id) {
                        this.$toastr.e('', `${this.$t('this_template_can_be_only_edited_from_app_side')} ${this.$t('You can duplicate it if you want')}`)
                    }else {
                        const baseURL = `${action.url}/${row.id}`;
                        window.location = urlGenerator(`${baseURL}/${action.name}?view=card_view`);
                    }
                }else if (action.type === 'page') {
                    const baseURL = `${action.url}/${row.id}`;
                    window.location = urlGenerator(`${baseURL}/${action.name}?view=card_view`)
                    return ;
                }
                this.getAction(row, action, active)
            },

            downloadTemplate(template) {
                axiosGet(`${templates}/${template.id}/content`).then(({data}) => {
                    let a = document.createElement('a');
                    a.href = 'data:text/plain;charset=utf-8,' + encodeURIComponent(data)
                    a.download = template.subject + '.html'
                    a.style.display = 'none';
                    document.body.appendChild(a).click();
                    document.body.removeChild(a);
                })
            },
        }
    }
</script>
