<template>
    <div class="font-size-default">
        <form ref="form" enctype="multipart/form-data">

            <app-template-choose-modal
                alias="brand"
                @selected="setContent"
                @close="isChooseModalOpen = false"
                v-if="isChooseModalOpen"
                modal-id="template-lists"
            />

            <div class="row d-flex justify-content-between">
                <div class="col-6">
                    <app-button
                        :label="$t('choose', {field: $t('template')})"
                        className="btn-primary"
                        :isDisabled="false"
                        @submit="isChooseModalOpen = true"
                    />
                </div>
                <div class="col-2 text-right">

                </div>
                <div class="col-12 mt-4 mb-1">
                    <app-input
                        id="text-editor-for-template"
                        type="text-editor"
                        v-if="textEditorReboot"
                        v-model="campaign.template_content"
                        :error-message="$errorMessage(errors, 'template_content')"
                        :text-editor-hints="textEditorHints"
                    />
                </div>
                <div class="col-12 mb-5 text-center"  v-if="typeof props.tags === 'string'">
                    <button
                        type="button"
                        class="btn btn-sm btn-primary px-3 py-1 margin-left-2 mt-2"
                        v-for="tag in JSON.parse(props.tags)"
                        @click="addTag(tag)"
                    >{{ tag }}</button>
                </div>
            </div>
            <app-cancel-button
                @click="go('back')"
                :label="$t('back')"
            />
            <app-submit-button
                :label="this.$t('save_and_next')"
                :loading="loading"
                @click="submitData"
                btn-class="ml-2"
            />
        </form>
    </div>
</template>

<script>
    import FormHelperMixins from "../../../../Mixins/Global/FormHelperMixins";
    import {campaigns, templates} from "../../../../config/apiUrl";
    import {mapState} from "vuex";
    import {axiosGet} from "../../../../Helpers/AxiosHelper";
    import {textEditorHints} from "../../../../Helpers/helpers";
    import {purify} from "../../../../../core/helpers/purifier/HTMLPurifyHelper";

    export default {
        name: "CampaignContent",
        mixins: [FormHelperMixins],
        props: {
            props: {
                default: {}
            }
        },
        data() {
            return {
                isChooseModalOpen: false,
                textEditorReboot: false,
                url: '',
                loading: false,
                campaign: {
                    template_content: ''
                },
                textEditorHints: textEditorHints(this.props.tags ? JSON.parse(this.props.tags) : [])
            }
        },
        methods: {
            submitData() {
                this.fieldStatus.isSubmit = true;
                this.loading = true;
                this.message = '';
                this.errors = {};
                this.campaign.template_content = purify(this.campaign.template_content);
                return this.submitFromFixin(
                    'post',
                    this.url,
                    this.campaign
                );
            },
            afterSuccess({data}) {
                this.go('next');

            },
            go(place) {
                const state = place === 'back' ? 2 : 4;
                const url = window.location.protocol + "//" + window.location.host + window.location.pathname + '?state='+state;
                window.history.pushState({path:url},'', url);
                this.$emit(place, true)
            },

            setContent(template) {
                this.textEditorReboot = false
                axiosGet(`${templates}/${template.id}/content`).then(({data}) => {
                    this.campaign.template_content = data.body
                }).finally(() => this.textEditorReboot = true)
            },
            addTag(tag_name = '{company_logo}') {
                $("#text-editor-for-template").summernote('editor.saveRange');
                $("#text-editor-for-template").summernote('editor.restoreRange');
                $("#text-editor-for-template").summernote('editor.focus');
                $("#text-editor-for-template").summernote('editor.insertText', tag_name);

            }
        },

        computed: {
            ...mapState({
                templates: ({template}) => template.templates.data
            })
        },

        watch: {
            props: {
                handler: function (props) {
                    if (typeof props === "object"){
                        this.campaign = {...props}
                        delete this.campaign.audiences;
                        this.url = `${campaigns}/${props.id}/template`
                        setTimeout(() => this.textEditorReboot = true)
                    }
                },
                immediate: true
            },
            'campaign.template_content': function (content) {
                if (content) {
                    this.textEditorReboot = true
                }
            },
        },
    }
</script>

<style scoped>
    .close-btn {
        font-size: 20px;
    }
    .margin-left-2 {
        margin-left: 4px;
    }
    .margin-left-2:first-child {
        margin-left: 0;
    }
</style>
