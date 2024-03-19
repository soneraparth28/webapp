<template>
    <div class="content-wrapper">
        <app-page-top-section
            :directory="`${getDirectoryName} | <a href='${getDestination.URL}'>${$t('back_to',{
                destination: getDestination.name})}</a>`"
            :title="$fieldTitle(getActionType, 'template', true)"
            :hide-button="true"
            icon="cpu"/>

        <form class="mb-0" :data-url="submitUrl" ref="form" v-if="!preloader">
            <div class="card border-0 card-with-shadow">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-9 col-xl-9">
                            <div class="form-group row">
                                <div class="col-lg-2 col-xl-2 d-flex align-items-center">
                                    <label class="mb-lg-0">
                                        {{ $fieldLabel('template') }}
                                    </label>
                                </div>
                                <div class="col-lg-10 col-xl-10">
                                    <app-input :error-message="$errorMessage(errors, 'subject')"
                                               :placeholder="$placeholder('name')"
                                               v-model="formData.subject"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-xl-3">
                            <div class="text-right">
                                <app-button :isDisabled="false"
                                            :label="$t('choose', {field: $t('template')})"
                                            @submit="isChooseModalOpen = true"
                                            className="btn-primary"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <app-input :error-message="$errorMessage(errors, 'custom_content')"
                                   type="text-editor"
                                   v-if="textEditorReboot"
                                   v-model="formData.custom_content"
                                   id="text-editor-for-template"
                                   :text-editor-hints="textEditorHints"/>
                    </div>
                    <div class="text-center">
                        <button type="button"
                                class="btn btn-sm btn-primary px-3 py-1 mt-2"
                                :class="{'ml-2': index !== 0}"
                                v-for="(tag, index) in JSON.parse(tags)"
                                @click="addTag(tag)">
                            {{ tag }}
                        </button>
                    </div>
                    <div class="mt-5">
                        <app-submit-button :loading="loading" @click="submitData"/>
                        <app-cancel-button @click="redirectToList" btn-class="ml-2"/>
                    </div>

                    <app-template-choose-modal
                        :alias="alias"
                        @selected="setContent"
                        @close="isChooseModalOpen = false"
                        v-if="isChooseModalOpen"
                        modal-id="template-lists"
                    />
                </div>
            </div>
        </form>
        <app-pre-loader class="p-primary" v-else />
    </div>
</template>


<script>
import FormHelperMixins from "../../../Mixins/Global/FormHelperMixins";
import {app_templates, templates, templates_card_view, templates_list_view} from '../../../config/apiUrl';
import {axiosGet, urlGenerator} from "../../../Helpers/AxiosHelper";
import {textEditorHints} from '../../../Helpers/helpers';
import {purify} from '../../../../core/helpers/purifier/HTMLPurifyHelper';

export default {
    name: "TemplateAddEditModal",
    mixins: [FormHelperMixins],
    props: ['selectedUrl', 'action', 'view', 'tags', 'alias'],
    data() {
        return {
            purify,
            isChooseModalOpen: false,
            textEditorReboot: false,
            formData: {
                custom_content: '',
                subject: ''
            },
            textEditorHints: textEditorHints(JSON.parse(this.tags))
        }
    },
    methods: {
        submitData() {
            this.fieldStatus.isSubmit = true;
            this.loading = true;
            this.message = '';
            this.errors = {};
            this.formData.custom_content = purify(this.formData.custom_content);
            this.submitFromFixin(
                this.isEditState ? 'patch' : 'post',
                this.submitUrl,
                this.formData
            );
        },

        afterSuccess({data}) {
            if (this.isCreateState) {
                this.textEditorReboot = false
                this.formData = {}
                setTimeout(() => this.textEditorReboot = true)
                this.redirectToList();
            }
            this.scrollToTop(false);
            this.toastAndReload(data.message)
            if (this.selectedUrl) {
              this.redirectToList();
            }
        },

        afterSuccessFromGetEditData(response) {
            this.preloader = false
            this.url = this.selectedUrl;
            this.formData = {
                subject: response.data.subject,
            }
            this.setContent(response.data)
        },

        setContent(template) {
            this.textEditorReboot = false
            axiosGet(`${this.getAliasedURL}/${template.id}/content`).then(({data}) => {
                this.formData.custom_content = data.body
            }).finally(() => this.textEditorReboot = true)
        },

        addTag(tag_name = '{company_logo}') {
            $("#text-editor-for-template").summernote('editor.saveRange');
            $("#text-editor-for-template").summernote('editor.restoreRange');
            $("#text-editor-for-template").summernote('editor.focus');
            $("#text-editor-for-template").summernote('editor.insertText', tag_name);
        },

        redirectToList() {
            window.location.replace(this.getDestination.URL)
        }

    },

    computed: {
        submitUrl() {
            if (this.isEditState) {
                return this.selectedUrl;
            }
            return this.getAliasedURL;
        },
        getAliasedURL() {
            return {
                brand: templates,
                app: app_templates
            }[this.alias]
        },
        getDirectoryName() {
            return {
                brand: this.$t('templates'),
                app: this.$t('settings')
            }[this.alias];
        },
        getDestination() {
            return {
                brand: {
                    name: this.$t(this.view ? this.view : 'list_view'),
                    URL: this.view === 'card_view'
                        ? urlGenerator(templates_card_view)
                        : urlGenerator(templates_list_view),
                },
                app: {
                    name: this.$fieldTitle('brand', 'templates', true),
                    URL: urlGenerator(`/admin/brand/templates`)
                }
            }[this.alias];
        },

        isEditState() {
            return this.action === 'edit';
        },
        isCreateState() {
            return (!Boolean(this.isEditState) && !Boolean(this.selectedUrl));
        },
        getActionType() {
            return this.isEditState ? 'edit' : this.isCreateState ? 'add' : 'copy';
        }
    },

    mounted() {
        if (this.isCreateState) {
            this.textEditorReboot = true
        }
    }

}
</script>
<style>
  .note-group-select-from-files {
    display: none;
  }
</style>
