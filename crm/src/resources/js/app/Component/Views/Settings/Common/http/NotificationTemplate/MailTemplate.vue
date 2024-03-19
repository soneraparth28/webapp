<template>
    <form ref="form"
          @submit.prevent=""
          v-if="loaded"
    >
        <div class="form-group">
            <label class="d-block">{{ $fieldTitle('mail', 'subject') }}</label>
            <span class="text-muted font-size-90">{{$t('This field will be used as the email subject and identify the template')}}</span>
            <app-input
                v-model="template.subject"
                :placeholder="$placeholder('mail', 'subject')"
                :error-message="$errorMessage(errors, 'subject')"
            />
        </div>
        <div class="form-group">
            <label>{{ $t('contents') }}</label>
            <app-input
                type="text-editor"
                v-model="template.content"
                v-if="loaded"
                id="text-editor-for-template"
                :placeholder="$placeholder('template', 'content')"
                row="5"
                :error-message="$errorMessage(errors, 'custom_content')"
                :text-editor-hints="textEditorHints(Object.keys(tags))"
            />
        </div>
        <div class="form-group text-center">
            <button
                type="button"
                class="btn btn-sm btn-primary px-3 py-1 margin-left-2 mt-2"
                data-toggle="tooltip"
                data-placement="top"
                v-for="tag in all_tags"
                :title="tag.description"
                @click="addTag(tag.tag)"
            >{{ tag.tag }}</button>
        </div>
    </form>
    <div v-else>
        <app-pre-loader />
    </div>
</template>

<script>
    import TemplateMixins from "./TemplateMixins";
    import {textEditorHints} from "../../../../../../Helpers/helpers";

    export default {
        name: "MailTemplate",
        mixins: [TemplateMixins],
        props: {
            props: {}
        },
        data(){
            return {
                tags: {
                    '{name}': this.$t('The resource name of the event'),
                    '{action_by}': this.$t('The user who performed the action'),
                    '{app_name}': this.$t('Name of the app'),
                    '{app_logo}': this.$t('Logo of the app'),
                    '{receiver_name}': this.$t('The user who will receive the notification'),
                    '{resource_url}': this.$t('Page link of resource'),
                    '{invitation_url}': this.$t('Invitation url for the user'),
                    '{reset_password_url}': this.$t('Reset password url of user'),
                    '{brand_name}': this.$t('brand_name')
                },
                textEditorHints
            }
        },
        methods: {
            templateSetter(value) {
                this.loaded = false;
                let template = this.collection(this.notification_event.templates)
                    .find(value, 'type');

                if (template && Object.keys(template).length) {
                    this.template = {...template};
                    this.template.content = this.template.custom_content || this.template.default_content;
                } else {
                    this.template = {type: 'mail'};
                }
                this.loaded = true
            },
            addTag(tag_name = '{name}') {
                $("#text-editor-for-template").summernote('editor.saveRange');
                $("#text-editor-for-template").summernote('editor.restoreRange');
                $("#text-editor-for-template").summernote('editor.focus');
                $("#text-editor-for-template").summernote('editor.insertText', tag_name);
            }
        },
        computed: {
            all_tags() {
                const tags = Object.keys(this.tags).filter(tag => {
                    if ('user_invitation' === this.notification_event.name) {
                        return ['{action_by}', '{app_name}', '{app_logo}', '{receiver_name}', '{invitation_url}'].includes(tag)
                    }else if('password_reset' === this.notification_event.name) {
                        return ['{app_name}', '{app_logo}', '{receiver_name}', '{reset_password_url}'].includes(tag)
                    }else if (tag === '{brand_name}'){
                        return this.props.alias === 'brand';
                    }else {
                        return !['{reset_password_url}', '{invitation_url}'].includes(tag)
                    }
                })
                return tags.map(tag => { return { tag, description: this.tags[tag] } })
            }
        },
        created() {
            this.template.type = 'mail';
        },
        watch: {
            'notification_event.id': {
                handler: function (nEvent) {
                    this.templateSetter(this.template.type);
                },
                immediate: true
            },
            'template.type': {
                handler: function (type) {
                    if (this.notification_event && this.notification_event.templates) {
                        this.templateSetter(type);
                    }
                },
            },
            template: {
                handler: function (template) {
                    this.$hub.$emit('setTemplate', template)
                },
                deep: true,
                immediate: true
            }
        },
    }
</script>

<style scoped>
    .margin-left-2 {
        margin-left: 4px;
    }
    .margin-left-2:first-child {
        margin-left: 0;
    }
</style>
