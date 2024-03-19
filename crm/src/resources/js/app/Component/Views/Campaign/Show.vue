<template>
    <div class="content-wrapper" v-if="campaign.id">
        <app-page-top-section
            :title="campaign.name"
            :directory="`${$t('campaigns')} | <a href='${urlGenerator(campaigns_front_end)}'>${$t('back_to', {destination:
            $allLabel('campaigns')} )}</a>`"
            icon="sun"
            :hide-button="true"/>

        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card border-0 card-with-shadow">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-primary">
                            <h5 class="card-title mb-0">
                                {{ $t('details') }}
                            </h5>
                            <div v-if="campaign.current_status !== 'archived'" class="dropdown options-dropdown">
                                <button type="button" class="btn-option btn" data-toggle="dropdown">
                                    <app-icon name="more-vertical"/>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right py-2 mt-1">
                                    <a class="dropdown-item px-4 py-2" href="#" @click.prevent="redirectEditPage()">
                                        {{ $t('edit') }}
                                    </a>
                                    <a v-if="campaign.current_status === 'paused' && this.$can('pause_or_resume_campaigns')"
                                       class="dropdown-item px-4 py-2" href="#"
                                       @click.prevent="changeStatusAction('resume')">
                                        {{ $t('resume') }}
                                    </a>
                                    <a v-if="campaign.current_status === 'active'
                                            && campaign.time_period !== 'immediately'
                                            && campaign.status.name !== 'status_processing'
                                            && campaign.status.name !== 'status_draft'
                                            && this.$can('pause_or_resume_campaigns')"
                                       class="dropdown-item px-4 py-2" href="#"
                                       @click.prevent="changeStatusAction('pause')">
                                        {{ $t('pause') }}
                                    </a>
                                    <a class="dropdown-item px-4 py-2" href="#"
                                       @click.prevent="confirmationModalActive = true">
                                        {{ $t('archive') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="mb-4">
                                <p class="text-muted mb-0">{{ $t('name') }}</p>
                                <p>{{ campaign.name ? campaign.name : $t('not_added_yet') }}</p>
                            </div>
                            <div class="mb-4">
                                <p class="text-muted mb-0">{{ $t('subject') }}</p>
                                <p class="text-left">
                                    {{ campaign.subject ? campaign.subject : $t('not_added_yet') }}
                                </p>
                            </div>
                            <div class="mb-4">
                                <p class="text-muted mb-1">{{ $t('status') }}</p>
                                <p v-if="campaign.current_status === 'archived'" class="ml-0">
                                    <span class="badge badge-sm badge-pill badge-danger">
                                    {{ this.$t('archived') }}
                                    </span>
                                </p>
                                <p v-else class="ml-0">
                                    <span :class="`badge badge-sm badge-pill badge-${campaign.status.class}`">
                                    {{ campaign.status.translated_name }}
                                    </span>
                                    <span v-if="campaign.current_status !== 'active'" class="badge badge-sm badge-pill badge-secondary">
                                    {{ this.$t(campaign.current_status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="mb-4">
                                <p class="text-muted mb-0">{{ $t('attachments') }}</p>
                                <template v-if="attachments.length">
                                    <a target="_blank" v-for="attachment in attachments" :href="attachment.path">
                                        {{ attachment.name }}
                                    </a>
                                </template>
                                <template v-else>
                                    <p>{{ $t('not_attached_yet') }}</p>
                                </template>
                            </div>
                            <div class="mb-4" v-if="creationDate">
                                <p class="text-muted mb-0">{{ $t('creation_date') }}</p>
                                <p>{{ creationDate }}</p>
                            </div>
                        </div>
                        <hr>
                        <div>
                            <h5 class="card-title my-4">
                                {{ $t('delivery') }}
                            </h5>
                            <div class="mb-4">
                                <p class="text-muted mb-0">{{ $fieldTitle('time', 'period') }}
                                    ({{ $t('frequency') }})</p>
                                <p>{{ campaign.time_period ? $t(campaign.time_period) : $t('not_added_yet') }}</p>
                            </div>
                            <div class="mb-4" v-if="campaign.time_period !== 'immediately'">
                                <p class="text-muted mb-0">{{
                                        $t(campaign.time_period !== 'once' ?
                                            'send_email_between' : 'sending_date')
                                    }}</p>
                                <p>{{
                                        (start_at && end_at) ? `${start_at} ${$t('to')} ${end_at}` :
                                            $t('not_added_yet')
                                    }}</p>
                            </div>
                            <div class="mb-4" v-if="!['immediately', 'hourly'].includes(campaign.time_period)">
                                <p class="text-muted mb-0">{{ $t('send_email_at') }}</p>
                                <p class="my-0">{{ time ? time : $t('not_added_yet') }}</p>
                            </div>
                        </div>
                        <hr>
                        <div>
                            <h5 class="card-title my-4">
                                {{ $t('audiences') }}
                            </h5>
                            <div class="mb-4">
                                <p class="text-muted mb-0">{{ $fieldTitle('from', 'list') }}</p>
                                <template v-if="lists && lists.length">
                                    <span v-for="list in lists">{{ list.name }},</span>
                                </template>
                                <template v-else>
                                    <p class="my-0">{{ $t('not_added_yet') }}</p>
                                </template>
                            </div>
                            <div class="mb-4">
                                <p class="text-muted mb-0">{{ $fieldTitle('total', 'subscribers') }}</p>
                                {{ campaign.counts.subscribers }}
                            </div>
                        </div>
                        <hr>
                        <div>
                            <h5 class="card-title my-primary">
                                {{ $t('content') }}
                            </h5>
                            <app-template-preview-card
                                id="campaign-content"
                                :card="templateCard"
                                :show-action="false"
                                previewType="html"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="row">
                    <div class="col-6">
                        <app-counter class="mb-primary"
                                     icon="sun"
                                     :title="$fieldTitle('total', 'sent')"
                                     :count="numberFormatter($optional(stats, 'status_sent', 'count'))"/>
                    </div>
                    <div class="col-6">
                        <app-counter class="mb-primary"
                                     icon="user-minus"
                                     :title="$fieldTitle('total', 'unsubscribers')"
                                     :count="campaign.counts.unsubscribed"/>
                    </div>
                </div>
                <div class="mb-primary">
                    <app-campaign-overview :chart-filter-options="chartFilterOptions"
                                           alias="campaign"
                                           :campaign="campaign.id"/>
                </div>
                <div class="row">
                    <app-email-counter :count="numberFormatter($optional(stats, 'status_delivered', 'count'))"
                                       :title="$fieldTitle('total', 'deliveries')"
                                       col="3"/>

                    <app-email-counter :count="numberFormatter($optional(stats, 'status_bounced', 'count'))"
                                       :title="$fieldTitle('total', 'bounces')"
                                       col="3"/>

                    <app-email-counter :count="numberFormatter($optional(stats, 'status_open', 'count'))"
                                       :title="$fieldTitle('total', 'opens')"
                                       col="3"/>

                    <app-email-counter :count="numberFormatter($optional(stats, 'status_clicked', 'count'))"
                                       :title="$fieldTitle('total', 'clicks')"
                                       col="3"/>
                </div>
                <div class="row">
                    <app-email-progress-bar
                        :rate="numberFormatter($optional(stats, 'status_delivered', 'rate'))"
                        :title="$fieldTitle('delivery', 'rate')"
                        col="3"/>

                    <app-email-progress-bar :rate="numberFormatter($optional(stats, 'status_bounced', 'rate'))"
                                            :title="$fieldTitle('bounce', 'rate')"
                                            col="3"/>

                    <app-email-progress-bar :rate="numberFormatter($optional(stats, 'status_open', 'rate'))"
                                            :title="$fieldTitle('open', 'rate')"
                                            col="3"/>

                    <app-email-progress-bar :rate="numberFormatter($optional(stats, 'status_clicked', 'rate'))"
                                            :title="$fieldTitle('click', 'rate')"
                                            col="3"/>
                </div>
                <div class="row">
                    <div class="col-12">
                        <app-emails v-bind="emailRules"></app-emails>
                    </div>
                </div>
            </div>
        </div>

        <app-template-preview-modal
            modalId="template-preview"
            :title="$t('template')"
            :content="campaign.template_content"
            v-if="isEmailPreviewModalActive"
            @close="isEmailPreviewModalActive = false"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            modal-id="app-confirmation-modal"
            :message="$t('this_content_will_be_archived_permanently')"
            icon="trash-2"
            @confirmed="confirmed"
            @cancelled="cancelled"
        />
        <app-confirmation-modal
            @cancelled="statusConfirmationModalActive = false"
            @confirmed="changeCurrentStatus()"
            modal-id="app-confirmation-modal"
            :message="statusModalMessage"
            icon="check-circle"
            modalClass="warning"
            v-if="statusConfirmationModalActive"
        />
    </div>
    <app-pre-loader class="p-primary" v-else/>
</template>

<script>
import {mapState} from "vuex";
import {formatDateToLocal, formatUtcToLocal, numberFormatter} from "../../../Helpers/helpers";
import {campaigns, campaigns_front_end} from "../../../config/apiUrl";
import ChartFilterOptionMixin from "../Dashboard/Mixins/ChartFilterOptionMixin";
import FormHelperMixins from "../../../Mixins/Global/FormHelperMixins";
import DeleteMixin from "../../../Mixins/Global/DeleteMixin";
import {axiosGet, urlGenerator} from "../../../Helpers/AxiosHelper";

export default {
    props: ['selectedUrl'],
    name: "Show",
    mixins: [FormHelperMixins, DeleteMixin, ChartFilterOptionMixin],
    components: {
        'app-counter': require('../../Helper/Dashboard/Counter').default,
        'app-campaign-overview': require('../Dashboard/Components/CampaignOverview').default,
        'app-email-counter': require('../../Helper/Dashboard/EmailCounter').default,
        'app-email-progress-bar': require('../../Helper/Dashboard/EmailRateProgressbar').default,
    },
    data() {
        return {
            emailRules: {
                wrapper: false,
                url: '',
                topBar: false,
                hideCampaignName: true,
                hideCampaignFilter: true
            },
            campaign: {
                audiences: [],
                attachments: [],
                name: '',
                subject: '',
                time_period: '',
                start_at: '',
                end_at: '',
                campaign_start_time: '',
                template_content: '',
                status: {},
                counts: {
                    subscribers: 0,
                    unsubscribed: 0
                },
                sent_count: 0
            },
            campaigns_front_end,
            url: {
                statistics: ''
            },
            numberFormatter,
            isEmailPreviewModalActive: false,
            statusConfirmationModalActive: false,
            statusModalMessage: '',
            changeStatusUrl: ''
        }
    },
    created() {
        this.url.statistics = `${this.getSelectedUrl}/email-statistics`
        this.emailRules.url = `${this.getSelectedUrl}/email-logs`
        this.$store.dispatch('getSelectableLists');
        this.$hub.$on('TemplateCard-campaign-content',
            () => this.isEmailPreviewModalActive = true
        )
    },

    computed: {
        creationDate() {
            return formatDateToLocal(this.campaign.created_at);
        },

        start_at() {
            return formatDateToLocal(this.campaign.start_at)
        },

        end_at() {
            return formatDateToLocal(this.campaign.end_at)
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

        ...mapState({
            selectableLists: ({lists}) => lists.lists,
            stats: ({stats}) => stats.campaign_stats
        }),

        lists() {
            if (this.campaign.audiences.length) {
                let lists = this.campaign.audiences
                    .find(audience => audience.audience_type === 'list')
                    .audiences;
                return this.selectableLists
                    .filter(list => lists.includes(list.id))
            }
        },

        getSelectedUrl() {
            return this.selectedUrl.split('?')[0];
        },

        templateCard() {
            return {
                custom_content: this.campaign.template_content,
                default_content: '',
                subject: ''
            }
        }
    },
    methods: {
        afterSuccessFromGetEditData({data}) {
            this.campaign = data;
            this.$store.dispatch('getSingleCampaignEmailStatistics', {id: data.id, type: 1});
            this.$store.dispatch('getCampaignStats', data.id)
        },

        redirectEditPage() {
            window.location.replace(urlGenerator(`${campaigns}/${this.campaign.id}/edit`));
        },

        redirectToList() {
            window.location = urlGenerator(campaigns_front_end);
        },

        confirmed() {
            this.axiosDelete(`${campaigns}/${this.campaign.id}`).then(response => {
                // window.location = urlGenerator(campaigns_front_end);
                location.reload();
                this.$toastr.s('', response.data.message)
                this.confirmationModalActive = false;
            });
        },
        changeStatusAction(action){
            this.statusConfirmationModalActive = true;
            this.changeStatusUrl = `${campaigns}/${this.campaign.id}/change-status?status=${action}`
            this.statusModalMessage = this.$t('the_campaign_will_be_action',{action: action})
        },
        changeCurrentStatus() {
            axiosGet(this.changeStatusUrl).then(({data}) => {
                this.$toastr.s('', data.message);
                location.reload();
            }).finally(()=>{
                this.statusConfirmationModalActive = false;
                this.changeStatusUrl = ''
                this.statusModalMessage = ''
            })
        }
    },
}
</script>


<style scoped>
</style>


