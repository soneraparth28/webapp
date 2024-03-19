<template>
    <div class="card card-with-shadow border-0 h-100">
        <div class="card-header bg-transparent p-primary d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ $fieldTitle('campaign', 'overview', true) }}</h5>
            <ul class="nav tab-filter-menu justify-content-flex-end">
                <li class="nav-item" v-for="(item, index) in chartFilterOptions" :key="index">
                    <a href="#"
                       :class="`nav-link py-0 px-2 ${range == item.id ? 'active' : ''} ${loading_email_statistics ? 'disabled' : ''}`"
                       @click="getStatistics(item.id)">
                        {{ item.value }}
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body" v-if="!loading_email_statistics">
            <app-chart type="custom-line-chart"
                       :height="190"
                       :labels="campaign_chart.labels"
                       :data-sets="campaign_chart.dataSets"
                       class="mb-primary"
                       :without-decimal-point="true"
            />
            <div class="chart-data-list d-flex flex-wrap justify-content-center">
                <div class="data-group-item" v-for="count in campaign_chart.dataSets"
                     :style="`color: ${count.borderColor}`">
                    <span class="square" :style="`background-color: ${count.borderColor}`"/>
                    {{ count.label }}
                    <span class="value">
                        {{ count.data.reduce((pre, cur) => pre + cur, 0) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body d-flex align-items-center justify-content-center" v-else>
            <loader/>
        </div>
    </div>
</template>

<script>
    import {mapState} from 'vuex'
    import Loader from "../../../Helper/Loader/Loader";
    import {formatDateToLocal, isValidDate} from "../../../../Helpers/helpers";

    export default {
        name: "CampaignOverview",
        components: {Loader},
        props: {
            chartFilterOptions: {
                required: true,
            },
            alias: {},
            campaign: {},
            permissions: {}
        },
        data() {
            return {
                campaign_chart: {
                    labels: [],
                    dataSets: []
                },
                range: 1
            }
        },
        computed: {
            ...mapState({
                email_statics: state => state.dashboard.email_statics,
                loading_email_statistics: state => state.dashboard.loading_email_statistics,
            }),
        },
        methods: {
            getStatistics(id) {
                if (parseInt(this.range) !== parseInt(id)) {
                    this.range = id;
                    if (this.alias === 'campaign') {
                        this.$store.dispatch('getSingleCampaignEmailStatistics', {
                            id: this.campaign,
                            type: id, ...this.permissions
                        });
                    } else {
                        this.$store.dispatch('getEmailStatics', {range: id, alias: this.alias, ...this.permissions})
                    }
                }
            }
        },
        watch: {
            email_statics: {
                handler: function (email_statics) {
                    if (Object.keys(email_statics).length) {
                        this.campaign_chart.labels = Object.keys(email_statics).map(key => {
                          return isValidDate(key) ? formatDateToLocal(key) : this.$t(key);
                        })
                        let status_bounced = [];
                        let status_clicked = [];
                        let status_delivered = [];
                        let status_open = [];
                        let status_sent = [];
                        Object.keys(email_statics).forEach(stats => {
                            status_bounced.push(
                                email_statics[stats].counts
                                    ? email_statics[stats].counts.status_bounced.count :
                                    email_statics[stats].status_bounced.count
                            );
                            status_clicked.push(
                                email_statics[stats].counts
                                    ? email_statics[stats].counts.status_clicked.count :
                                    email_statics[stats].status_clicked.count
                            );
                            status_delivered.push(
                                email_statics[stats].counts
                                    ? email_statics[stats].counts.status_delivered.count :
                                    email_statics[stats].status_delivered.count
                            );
                            status_open.push(
                                email_statics[stats].counts ?
                                    email_statics[stats].counts.status_open.count :
                                    email_statics[stats].status_open.count
                            );
                            status_sent.push(
                                email_statics[stats].counts ?
                                    email_statics[stats].counts.status_sent.count :
                                    email_statics[stats].status_sent.count);
                        });
                        this.campaign_chart.dataSets = [{
                            label: this.$t('status_bounced'),
                            backgroundColor: "#FC5710",
                            borderColor: "#FC5710",
                            borderWidth: 1.5,
                            fill: false,
                            data: status_bounced
                        }, {
                            label: this.$t('status_clicked'),
                            backgroundColor: '#FFCC17',
                            borderColor: '#FFCC17',
                            fill: false,
                            borderWidth: 1.5,
                            data: status_clicked
                        }, {
                            label: this.$t('status_delivered'),
                            borderColor: '#27AE60',
                            backgroundColor: '#27AE60',
                            borderWidth: 1.5,
                            fill: false,
                            data: status_delivered
                        }, {
                            label: this.$t('status_open'),
                            backgroundColor: '#A45FFD',
                            borderColor: '#A45FFD',
                            borderWidth: 1.5,
                            fill: false,
                            data: status_open
                        }, {
                            label: this.$t('status_sent'),
                            borderColor: '#4466f2',
                            borderWidth: 1.5,
                            fill: false,
                            data: status_sent
                        }];
                    }
                },
                deep: true,
                immediate: true
            }
        }
    }
</script>

<style scoped>

</style>
