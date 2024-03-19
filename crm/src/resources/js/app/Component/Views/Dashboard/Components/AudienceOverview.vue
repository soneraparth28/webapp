<template>
    <div class="card card-with-shadow border-0 h-100">
        <div class="card-header bg-transparent p-primary d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ $fieldTitle('audience', 'overview', true) }}</h5>
            <ul class="nav tab-filter-menu justify-content-flex-end">
                <li class="nav-item" v-for="(item, index) in chartFilterOptions" :key="index">
                    <a @click="getSubscribers(item.id)"
                       style="cursor: pointer"
                       :class="`nav-link py-0 px-2 ${parseInt(range) === parseInt(item.id) ? 'active' : ''} ${loading_subscriber_statistics ? 'disabled' : ''}`">
                        {{ item.value }}
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body" v-if="!loading_subscriber_statistics">
            <app-chart type="line-chart"
                       :height="210"
                       :labels="audience_chart.labels"
                       :data-sets="audience_chart.dataSets"
                       class="mb-primary"
                       :without-decimal-point="true"
            />

            <div class="chart-data-list d-flex flex-wrap justify-content-center mt-4">
                <div class="data-group-item" v-for="count in audience_chart.dataSets" :style="`color: ${count.borderColor}`">
                    <span class="square" :style="`background-color: ${count.backgroundColor}`"/>
                    {{ count.label }}
                    <span class="value">
                        {{ count.data.reduce((pre, cur) => pre + cur, 0) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body d-flex align-items-center justify-content-center" v-else>
            <loader />
        </div>
    </div>
</template>

<script>
    import {mapState} from 'vuex'
    import Loader from "../../../Helper/Loader/Loader";
    import {formatDateToLocal, isValidDate} from "../../../../Helpers/helpers";

    export default {
        name: "AudienceOverview",
        components: {Loader},
        props: {
            chartFilterOptions: {
                required: true,
            },
            alias: {},
            permissions: {}
        },
        data() {
            return {
                audience_chart: {
                    labels: [],
                    dataSets: []
                },
                range: 1
            }
        },
        computed: {
            ...mapState({
                loading_subscriber_statistics: state => state.dashboard.loading_subscriber_statistics,
                subscribers: state => state.dashboard.subscribers
            })
        },
        methods: {
            getSubscribers(id) {
                this.range = id;
                this.$store.dispatch('getTotalSubscriber', {alias: this.alias, range: id, ...this.permissions})
            }
        },
        watch: {
            subscribers: {
                handler: function (subscribers) {
                    if (subscribers) {
                        let subscribed = [];
                        let un_subscribed = [];
                        this.audience_chart.labels = Object.keys(subscribers).map(key => {
                          return isValidDate(key) ? formatDateToLocal(key) : this.$t(key);
                        });
                        Object.keys(subscribers).forEach(stat => {
                            subscribed.push(subscribers[stat].subscribed);
                            un_subscribed.push(subscribers[stat].unsubscribed);
                        });
                        this.audience_chart.dataSets = [
                            {
                                label: this.$t('unsubscribers'),
                                borderColor: '#1e8f24',
                                backgroundColor: '#19A526',
                                borderWidth:1.5,
                                data: un_subscribed
                            },
                            {
                                label: this.$t('subscribers'),
                                borderColor: '#1e8f24',
                                backgroundColor: '#19A52650',
                                borderWidth:1.5,
                                data: subscribed
                            }
                        ]
                    }
                },
                deep: true
            }
        }
    }
</script>

<style scoped>

</style>
