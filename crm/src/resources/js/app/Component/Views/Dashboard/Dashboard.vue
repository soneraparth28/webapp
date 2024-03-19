<template>
    <div class="content-wrapper">
        <app-breadcrumb :page-title="$t('dashboard')" icon="pie-chart"/>
        <div class="row" v-if="subscriptions.length">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <template v-for="subscription in subscriptions">
                            {{ $t('pending_subscription_from_sns') }}
                            <app-submit-button @click="confirmSubscription(subscription.id)"
                                               :label="$t('confirm')"
                                               :loading="loading"/>
                        </template>
                    </div>
                </div>
            </div>
        </div>
        <br v-if="subscriptions.length">
        <div class="dashboard-first-row mb-primary">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-5 col-lg-5 col-xl-4 first-column">
                    <app-widget class="mb-primary"
                                type="app-widget-with-icon"
                                :label="$totalLabel('campaigns')"
                                :number="numberFormatter(campaigns.gross_count || 0)"
                                icon="sun"
                                v-if="permissions.campaigns_count"
                    />

                    <app-widget class="mb-primary"
                                type="app-widget-with-icon"
                                :label="$totalLabel('subscribers')"
                                :number="numberFormatter($optional(subscriber_stats, 'subscribed') || 0)"
                                icon="message-circle"
                                v-if="permissions.subscribers_count"
                    />

                    <app-widget class="mb-primary"
                                v-if="!alias"
                                type="app-widget-with-icon"
                                :label="$totalLabel('brands')"
                                :number="numberFormatter(total_brands || 0)"
                                icon="layers"
                    />

                    <app-widget class="mb-primary"
                                v-else-if="permissions.view_brand_segment_counts"
                                type="app-widget-with-icon"
                                :label="$totalLabel('segments')"
                                :number="numberFormatter(total_segments || 0)"
                                icon="layers"
                    />

                </div>
                <div class="col-12 col-sm-12 col-md-7 col-lg-7 col-xl-8">
                    <app-campaign-overview
                        v-if="permissions.email_statistics"
                        :chart-filter-options="chartFilterOptions"
                        :alias="alias"
                        :permissions="permissions"
                    />
                </div>
            </div>
        </div>

        <div class="dashboard-second-row" v-if="permissions.email_statistics">
            <div class="row">
                <app-email-counter
                    :count="numberFormatter($optional(email_gross_stats, 'status_sent', 'count'))"
                    :title="$totalLabel('sent')"
                />

                <app-email-counter
                    :count="numberFormatter($optional(email_gross_stats, 'status_delivered', 'count'))"
                    :title="$totalLabel('deliveries')"
                />

                <app-email-counter
                    :count="numberFormatter($optional(email_gross_stats, 'status_bounced', 'count'))"
                    :title="$totalLabel('bounces')"
                />

                <app-email-counter
                    :count="numberFormatter($optional(email_gross_stats, 'status_open', 'count'))"
                    :title="$totalLabel('opens')"
                />

                <app-email-counter
                    :count="numberFormatter($optional(email_gross_stats, 'status_clicked', 'count'))"
                    :title="$totalLabel('clicks')"
                />

                <app-email-counter
                    :count="numberFormatter($optional(subscriber_stats, 'unsubscribed'))"
                    :title="$totalLabel('unsubscribers')"
                />

            </div>
        </div>

        <div class="dashboard-third-row" v-if="permissions.email_statistics">
            <div class="row">
                <app-email-progress-bar
                    :rate="numberFormatter($optional(email_gross_stats, 'status_sent', 'rate'))"
                    :title="$rateLabel('sending')"
                />

                <app-email-progress-bar
                    :rate="numberFormatter($optional(email_gross_stats, 'status_delivered', 'rate'))"
                    :title="$rateLabel('delivery')"
                />

                <app-email-progress-bar
                    :rate="numberFormatter($optional(email_gross_stats, 'status_bounced', 'rate'))"
                    :title="$rateLabel('bounce')"
                />

                <app-email-progress-bar
                    :rate="numberFormatter($optional(email_gross_stats, 'status_open', 'rate'))"
                    :title="$rateLabel('open')"
                />

                <app-email-progress-bar
                    :rate="numberFormatter($optional(email_gross_stats, 'status_clicked', 'rate'))"
                    :title="$rateLabel('click')"
                />

                <app-email-progress-bar
                    :rate="numberFormatter(parseInt(
                        (
                            parseInt($optional(subscriber_stats, 'unsubscribed'))
                            /
                            (
                                parseInt($optional(subscriber_stats, 'subscribed'))
                                + parseInt($optional(subscriber_stats, 'unsubscribed'))
                                )
                            ) * 100))"
                    :title="$rateLabel('unsubscribing')"
                />
            </div>
        </div>

        <div class="dashboard-fourth-row">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-7 col-lg-7 col-xl-8 first-column" v-if="permissions.subscribers_count">
                    <app-audiences-overview :chart-filter-options="chartFilterOptions"
                                            :alias="alias"
                                            :permissions="permissions"/>
                </div>
                <div class="col-12 col-sm-12 col-md-5 col-lg-5 col-xl-4" v-if="permissions.campaigns_count">
                    <div class="card card-with-shadow border-0 h-100">
                        <div class="card-header p-primary bg-transparent d-flex flex-wrap align-items-baseline">
                            <h5 class="card-title mb-0 mr-2 d-inline-block">
                                {{ $fieldTitle('campaign', 'overview', true) }}
                            </h5>
                            <span class="badge badge-success badge-sm badge-pill ml-auto d-inline-block">
                                {{ $t('last_24_hours') }}
                            </span>
                        </div>
                        <div class="card-body" v-if="!loading_campaign_count">
                            <app-chart type="bar-chart"
                                       :height="260"
                                       :labels="campaign_chart.label"
                                       :data-sets="campaign_chart.dataset"
                                       :without-decimal-point="true"
                            />
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center" v-else>
                            <loader/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapState} from 'vuex'
    import {numberFormatter} from '../../../Helpers/helpers'
    import CoreLibrary from "../../../../core/helpers/CoreLibrary";
    import {brand_sns_subscriptions, sns_subscription} from "../../../config/apiUrl";
    import Loader from "../../Helper/Loader/Loader";
    import ChartFilterOptionMixin from "./Mixins/ChartFilterOptionMixin";

    export default {
        extends: CoreLibrary,
        name: "Dashboard",
        mixins: [ChartFilterOptionMixin],
        components: {
            Loader,
            'app-email-counter': require('../../Helper/Dashboard/EmailCounter').default,
            'app-email-progress-bar': require('../../Helper/Dashboard/EmailRateProgressbar').default,
            'app-campaign-overview': require('./Components/CampaignOverview').default,
            'app-audiences-overview': require('./Components/AudienceOverview').default
        },
        props: {
            alias: {
                default: false
            },
            permissions: {}
        },
        data() {
            return {
                chart_current: {
                    audience: 1,
                    campaign: 1
                },
                campaign_chart: {
                    label: [this.$t('status_sent'), this.$t('status_delivered'), this.$t('status_bounced'), this.$t('status_open'), this.$t('status_clicked')],
                    dataset: []
                },
                numberFormatter,
                loading: false
            }
        },
        methods: {
            confirmSubscription(id) {
                this.loading = true;
                const url = this.alias ? `${brand_sns_subscriptions}/confirm` : `${sns_subscription}/confirm`;
                this.axiosPost({url, data: {sns_id: id}}).then(res => {
                    this.$store.dispatch('getSubscriptions', {alias: this.alias ? 'brand' : 'app'});
                }).finally(() => {
                    this.loading = false;
                })
            },

        },
        computed: {
            ...mapState({
                subscribers: state => state.dashboard.subscribers,
                total_unsubscribers: state => state.dashboard.total_unsubscribers,
                total_brands: state => state.dashboard.total_brands,
                total_segments: state => state.dashboard.total_segments,
                subscriptions: state => state.dashboard.subscriptions,
                loading_campaign_count: state => state.dashboard.loading_campaign_count,
                email_gross_stats: ({stats}) => stats.email_gross_stats,
                subscriber_stats: ({stats}) => stats.subscriber_stats
            }),
            campaigns() {
                const campaigns = this.$store.state.dashboard.total_campaigns || {};
                this.campaign_chart.dataset = [{
                    label: this.$t('total'),
                    backgroundColor: '#19A526',
                    data: [
                        this.$optional(campaigns, 'status_wise', 'status_sent'),
                        this.$optional(campaigns, 'status_wise', 'status_delivered'),
                        this.$optional(campaigns, 'status_wise', 'status_bounced'),
                        this.$optional(campaigns, 'status_wise', 'status_open'),
                        this.$optional(campaigns, 'status_wise', 'status_clicked'),
                    ]
                }];
                return campaigns;
            },

        },
        created() {
            if (!this.alias)
                this.$store.dispatch('dispatchAppDashboard', this.permissions);
            else
                this.$store.dispatch('dispatchBrandDashboard', this.permissions);
        }
    }
</script>

<style scoped>

</style>
