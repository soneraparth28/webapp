<template>
    <div class="font-size-default">
        <div class="form-group">
            <div class="row">
                <div class="col-xl-2 col-md-3 col-12 pt-2">
                    <label class="text-left d-block pt-2">{{ $fieldLabel('campaign') }}</label>
                </div>
                <div class="col-md-9 col-xl-7 col-12">
                    <app-input
                        :error-message="$errorMessage(errors, 'name')"
                        :placeholder="$placeholder('name')"
                        type="text"
                        v-model="campaign.name"
                    />
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-xl-2 col-md-3 col-12 pt-2">
                    <label class="text-left d-block pt-2">{{ $t('subject') }}</label>
                </div>
                <div class="col-md-9 col-xl-7 col-12">
                    <app-input
                        :error-message="$errorMessage(errors, 'subject')"
                        :placeholder="$placeholder('subject_for_this_email')"
                        v-model="campaign.subject"
                    />
                </div>
            </div>
        </div>
        <div class="form-group mb-5">
            <div class="row">
                <div class="col-xl-2 col-md-3 col-12 pt-2">
                    <label class="text-left d-block mb-2 mb-lg-0">
                        {{ $t('attachment') }}<br>
                        <small class="text-muted font-italic">{{ $t('campaign_allowed_mimes') }}</small>
                    </label>
                </div>
                <div class="col-md-9 col-xl-7 col-12">
                    <app-input
                        type="dropzone"
                        v-model="attachments"
                        v-if="dropZoneBoot"
                        :error-message="error_message"
                        :generate-file-url="false"
                    />
                </div>
            </div>
        </div>

        <app-cancel-button @click="redirectToList"/>
        <app-submit-button
            :label="this.$t('save_and_next')"
            :loading="loading"
            btn-class="ml-2"
            @click="submitData"
        />
    </div>
</template>

<script>
import FormHelperMixins from "../../../../Mixins/Global/FormHelperMixins";
import {campaigns, campaigns_front_end} from "../../../../config/apiUrl";
import {dropZoneErrorGenerator, formDataAssigner} from "../../../../Helpers/helpers";
import {urlGenerator} from "../../../../Helpers/AxiosHelper";

export default {
    name: "CampaignDetails",
    mixins: [FormHelperMixins],
    props: {
        props: {
            default: {}
        }
    },
    data() {
        return {
            preloader: false,
            url: '',
            attachments: [],
            campaign: {
                name: '',
                subject: ''
            },
            isEditState: false,
            dropZoneBoot: true,
            error_message: "",
            allAttachments: []
        };
    },
    methods: {
        submitData() {
            this.loading = true;
            this.message = '';
            this.errors = {};
            let formData = formDataAssigner(new FormData, this.campaign);
            if (this.attachments.length) {
                this.attachments.forEach(attachment => {
                    attachment instanceof File ?
                        formData.append(`attachments[]`, attachment) :
                        formData.append(`dont_delete[]`, this.allAttachments.find(file => file.url === attachment.dataURL).id)
                })
            }

            return this.submitFromFixin(
                'post',
                this.isEditState ? this.url : campaigns,
                formData
            );
        },

        afterSuccess({data}) {
            this.toastAndReload(data.message);
            if (this.isEditState) {
                this.go();
            } else {
                window.location = urlGenerator(`${campaigns}/${data.campaign.id}/edit?state=1`)
            }
        },
        afterFinalResponse() {
            this.loading = false;
        },
        afterError(response) {
            this.error_message = dropZoneErrorGenerator(response.data.errors)
            this.loading = false;
            if (response.status != 422) {
                this.$toastr.e(response.data.message || response.statusText)
            } else {
                this.errors = response.data.errors;
            }
        },
        go() {
            const url = window.location.protocol + "//" + window.location.host + window.location.pathname + '?state=1';
            window.history.pushState({path: url}, '', url);
            this.$emit('next', true)
        },
        redirectToList() {
            window.location = urlGenerator(campaigns_front_end)
        }
    },

    watch: {
        props: {
            handler: function (props) {
                this.isEditState = !!Object.keys(props).length;
                if (typeof props === "object" && this.isEditState) {
                    this.dropZoneBoot = false
                    this.campaign = {
                        name: props.name,
                        subject: props.subject,
                        _method: 'PATCH'
                    }
                    this.attachments = props.attachments.map(e => {
                        this.allAttachments.push({
                            id: e.id,
                            url: e.full_url
                        })
                        return e.full_url;
                    })
                    this.url = `${campaigns}/${props.id}`
                    setTimeout(() => this.dropZoneBoot = true)
                }
            },
            immediate: true,
            deep: true
        }
    }
}
</script>

<style scoped>

</style>
