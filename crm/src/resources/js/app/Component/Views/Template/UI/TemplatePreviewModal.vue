<template>
    <app-modal
        :modal-id="modalId"
        modalSize="fullscreen"
        @close-modal="$emit('close')"
    >
        <template slot="header">
            <h5 class="modal-title">
                {{ title }}
            </h5>
            <button aria-label="Close" class="close outline-none" data-dismiss="modal" type="button">
                <span>
                    <app-icon name="x"/>
                </span>
            </button>
        </template>
        <template slot="body">
            <div class="d-flex justify-content-center">
                <div v-if="false" class="col-lg-12">
                    <div class="dropdown options-dropdown float-right">
                        <button class="btn-option btn" data-toggle="dropdown" type="button">
                            <app-icon name="more-vertical"/>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right py-2 mt-1">
                            <a :href="api.edit" class="dropdown-item px-4 py-2">
                                {{ $t('edit') }}
                            </a>
                            <a class="dropdown-item px-4 py-2" @click.prevent="$emit('deletable')">
                                {{ $t('delete') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div v-if="!loading">
                    <div class="d-flex justify-content-center">
                        <span class="v-html-wrapper" v-html="purify(wrapperContent)"/>
                    </div>
                    <template v-if="attachments.length">
                        <div class="row justify-content-center mt-4">
                            <div class="col-12 col-lg-11">
                                <div class="row">
                                    <div v-for="attachment in attachments"
                                         class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-3">
                                        <div class="d-flex justify-content-center mb-primary">
                                            <img :src="attachmentTypeImage(attachment)
                                                        ? attachment
                                                        : getAppUrl('images/default-card-view-img.png')"
                                                 class="attachment"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <app-pre-loader v-else/>
            </div>
        </template>
    </app-modal>
</template>

<script>

import DeleteMixin from "../../../../Mixins/Global/DeleteMixin";
import {templates} from "../../../../config/apiUrl";
import {axiosGet} from "../../../../Helpers/AxiosHelper";
import CoreLibrary from "../../../../../core/helpers/CoreLibrary";
import {purify} from "../../../../../core/helpers/purifier/HTMLPurifyHelper";

export default {
    name: "TemplatePreviewModal",
    mixins: [DeleteMixin],
    extends: CoreLibrary,
    props: {
        id: {
            require: false
        },
        content: {
            type: String,
        },
        isActionsActive: {
            type: Boolean,
            default: true
        },
        modalId: {
            type: String,
            default: 'template-preview'
        },
        sourceURL: {
            type: String,
        },
        title: {
            type: String,
            default() {
                return this.$t('template')
            }
        },
    },
    data() {
        return {
            purify,
            loading: false,
            api: {
                edit: `/${templates}/${this.id}/edit`
            },
            body: '',
            attachments: [],

        }
    },
    methods: {
        getContent() {
            this.loading = true;
            axiosGet(this.sourceURL).then(({data}) => {
                this.body = data.body;
                this.attachments = data.attachments || [];
            }).catch(({response}) => {
                this.isEmailPreviewModalActive = true;
                this.$toastr.e('', response.data.message)
            }).finally(() => this.loading = false)
        },

        attachmentTypeImage(attachment) {
            let attachmentType = attachment.substr((attachment.lastIndexOf('.') + 1)).toLowerCase();
            let imgTypes = ['png', 'jpg', 'gif', 'jpeg'];
            return imgTypes.includes(attachmentType);
        }
    },
    mounted() {
        if (!this.content) {
            this.getContent()
        }
    },
    computed: {
        wrapperContent() {
            return this.content ? this.content : this.body;
        },
    }
}
</script>

<style scoped>
.attachment {
    width: 12.5rem;
    height: 11.25rem;
    border-radius: .625rem;
    object-fit: cover;
}
</style>
