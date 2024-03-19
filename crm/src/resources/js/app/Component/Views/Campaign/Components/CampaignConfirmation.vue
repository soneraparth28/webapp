<template>
    <div v-if="campaign.id" class="font-size-default">
        <div v-if="delivery_message" class="mb-primary">
            <div class="note-title d-flex">
                <app-icon name="book-open"/>
                <h6 class="card-title pl-2">{{ $t('no_delivery_settings_found') }}</h6>
            </div>
            <div class="note note-warning p-4">
                <p class="m-1">{{ delivery_message }}</p>
            </div>
        </div>
        <form>
            <div>
                <div class="mb-primary">
                    <h5 class="card-title">
                        {{ $t('details') }}
                    </h5>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="mb-primary">
                                <p class="text-muted mb-0">{{ $t('name') }}</p>
                                <p>{{ campaign.name }}</p>
                            </div>
                            <div>
                                <p class="text-muted mb-0">{{ $t('subject') }}</p>
                                <p>{{ campaign.subject }}</p>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div>
                                <p class="text-muted mb-0">{{ $t('attachments') }}</p>
                                <template v-if="attachments.length">
                                    <a v-for="attachment in attachments" :href="attachment.path"
                                       target="_blank">
                                        {{ attachment.name }} <br>
                                    </a>
                                </template>
                                <template v-else>
                                    {{ $t('not_attached_yet') }}
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-primary">
                    <h5>
                        {{ $t('delivery') }}
                    </h5>
                    <div class="row">
                        <div class="col-xl-6">
                            <div>
                                <p class="text-muted mb-0">{{ $t('time_period') }} ({{ $t('frequency') }})</p>
                                <p>{{ campaign.time_period ? $t(campaign.time_period) : $t('not_added_yet') }}</p>
                            </div>
                            <template v-if="!['immediately', 'hourly'].includes(campaign.time_period) ">
                                <div class="mb-2 mb-xl-0">
                                    <p class="text-muted mb-0">{{ $t('send_email_at') }}</p>
                                    <p class="my-0">{{ time ? time : $t('not_added_yet') }}</p>
                                </div>
                            </template>
                        </div>
                        <div v-if="campaign.time_period !== 'immediately'" class="col-xl-6">
                            <div v-if="start_at" class="mb-2">
                                <p class="text-muted mb-0">{{ $fieldTitle('start', 'at') }}</p>
                                <p>{{ start_at ? start_at : $t('not_added_yet') }}</p>
                            </div>
                            <div v-if="end_at" class="mb-2">
                                <p class="text-muted mb-0">{{ $fieldTitle('end', 'at') }}</p>
                                <p>{{ end_at ? end_at : $t('not_added_yet') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-show="false" class="mb-primary">
                    <h5 class="card-title my-4">
                        {{ $t('email_setup') }}
                    </h5>
                    <div class="row">
                        <div class="col-xl-6">
                            <div>
                                <p class="text-muted mb-0">{{ $t('sender_email') }}</p>
                                demo!@gmail.com
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div>
                                <p class="text-muted mb-0">{{ $t('receiver_email') }}</p>
                                demo2@gmail.com
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-primary">
                    <h5>
                        {{ $t('audiences') }}
                    </h5>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="mb-2 mb-xl-0">
                                <p class="text-muted mb-0">{{ $fieldTitle('from', 'list') }}</p>
                                <template>
                                    {{ campaign.list_subscriber_count || 0 }}
                                </template>
                            </div>
                            <div v-show="false">
                                <p class="text-muted mb-0">{{ $fieldTitle('from', 'segment') }}</p>
                                segment1, segment2
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div>
                                <p class="text-muted mb-0">{{ $totalLabel('subscribers') }}</p>
                                {{ $optional(campaign, 'counts', 'subscribers') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-primary">
                    <h5>{{ $t('content') }}</h5>
                    <div class="row">
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                            <div class="mb-primary">
                                <app-template-preview-card
                                    v-if="templateCard.custom_content"
                                    id="campaign-content"
                                    :card="templateCard"
                                    :show-action="false"
                                    previewType="html"
                                />
                                <p v-else>{{ $t('not_added_yet') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="!delivery_message" class="mb-primary">
                    <h5>
                        {{ $t('want_to_test') }}
                    </h5>
                    <div class="row">
                        <div class="col-xl-5 col-md-7 pr-0">
                            <div class="mb-primary">
                                <app-input
                                    v-model="email"
                                    :placeholder="$placeholder('enter', 'email')"
                                    type="text"
                                />
                                <app-message :message="$errorMessage(errors, 'email')" type="error"/>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-2">
                            <div class="mb-primary">
                                <app-submit-button
                                    :label="$t('send')"
                                    :loading="loading_test"
                                    btnClass="mt-1 "
                                    @click="testEmail"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <app-template-preview-modal
                v-if="isEmailPreviewModalActive"
                :content="preview_content"
                :title="$t('template')"
                modalId="template-preview"
                @close="isEmailPreviewModalActive = false"
            />

            <div class="mt-4">
                <app-cancel-button
                    :label="$t('back')"
                    @click="go('back')"
                />
                <app-submit-button
                    :label="this.$t('continue')"
                    :loading="loading"
                    btn-class="ml-2"
                    @click="confirmationModalActive = true"
                />
            </div>
            <app-confirmation-modal
                v-if="confirmationModalActive"
                :title="$t('gentle_reminder')"
                :message="$t('cron_job_settings_warning',{
                                documentation: $t('documentation')
                             })"
                modal-class="primary"
                icon="bell"
                :firstButtonName="$t('finish')"
                :hideSecondButton="true"
                modal-id="app-confirmation-modal"
                @confirmed="submitData"
                @cancelled="confirmationModalActive = false"
            />
        </form>
    </div>
    <app-pre-loader v-else class="p-primary"/>
</template>

<script>
import FormHelperMixins from "../../../../Mixins/Global/FormHelperMixins";
import {campaigns, campaigns_front_end} from "../../../../config/apiUrl";
import {formatDateToLocal, formatUtcToLocal} from "../../../../Helpers/helpers";
import moment from 'moment'
import {urlGenerator} from "../../../../Helpers/AxiosHelper";

export default {
    name: "CampaignConfirmation",
    mixins: [FormHelperMixins],
    props: {
        props: {
            default: {}
        }
    },
    data() {
        return {
            isEmailPreviewModalActive: false,
            preview_content: '',
            email: '',
            loading: false,
            loading_test: false,
            campaign: {
                attachments: [],
            },
            delivery_message: {},
            confirmationModalActive: false,
        };
    },
    methods: {
        submitData() {
            window.onbeforeunload = null;
            this.fieldStatus.isSubmit = true;
            this.loading = true;
            this.message = '';
            this.errors = {};
            return this.submitFromFixin(
                'post',
                this.url,
                {
                    ...this.campaign,
                    start_at: this.campaign.start_at ? moment(this.campaign.start_at)
                        .format('YYYY-MM-DD') : '',
                    end_at: this.campaign.end_at ? moment(this.campaign.end_at)
                        .format('YYYY-MM-DD') : '',
                    campaign_start_time: this.campaign.campaign_start_time ?
                        moment(this.campaign.campaign_start_time, 'H:mm')
                            .utc()
                            .format('HH:mm') : ''
                }
            );
        },
        afterSuccess({data}) {
            if (data.status) {
                this.toastAndReload(data.message);
            }
            window.location = urlGenerator(campaigns_front_end)
        },
        afterError(response) {
            this.message = '';
            this.loading = false;
            if (response.status != 422) {
                this.$toastr.e(response.data.message || response.statusText)
            } else {
                this.$toastr.e(this.$errorMessage(response.data.errors, 'audiences'))
            }

        },
        getCampaign(id) {
            this.url = `${campaigns}/${id}/confirm`;
            this.axiosGet(`${campaigns}/${id}?load_audiences=1&counts=1`).then(({data}) => {
                this.campaign = {...data}
            })
        },
        go(place) {
            const url = window.location.protocol + "//" + window.location.host + window.location.pathname + '?state=3';
            window.history.pushState({path: url}, '', url);
            this.$emit(place, true)
        },
        testEmail() {
            this.loading_test = true;
            this.errors = {}
            this.axiosPost({
                url: `${campaigns}/${this.campaign.id}/test`,
                data: {email: this.email}
            }).then(res => {
                this.$toastr.s('', res.data.message)
                this.email = '';
            }).catch(({response}) => this.errors = response.data.errors)
                .finally(() => this.loading_test = false)
        }
    },
    computed: {
        start_at() {
            return this.campaign.start_at ? formatDateToLocal(this.campaign.start_at) : '';
        },
        end_at() {
            return this.campaign.end_at ? formatDateToLocal(this.campaign.end_at) : '';
        },
        time() {
            return this.campaign.campaign_start_time ?
                formatUtcToLocal(this.campaign.campaign_start_time)
                : ''
        },
        attachments() {
            if (this.campaign.attachments !== undefined) {
                return this.campaign.attachments.map(attachment => {
                    return {
                        name: attachment.path.split('/').reverse()[0],
                        path: urlGenerator(attachment.path)
                    }
                })
            }
            return []
        },
        templateCard() {
            return {
                custom_content: this.campaign.template_content,
                default_content: '',
                subject: '',
                thumbnail: {
                    ...this.campaign.thumbnail
                }
            }
        }

    },

    created() {
        this.$hub.$on('selectedCard', ({card, campaign, message}) => {
            if (card === 'app-campaign-confirmation') {
                this.getCampaign(campaign.campaign_id)
                this.delivery_message = message;
            }
        })
        this.$hub.$on('TemplateCard-campaign-content', ({custom_content}) => {
            this.preview_content = custom_content
            this.isEmailPreviewModalActive = true;
        })
    }
}

</script>

<style scoped>

</style>