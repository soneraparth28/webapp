<template>
    <div class="font-size-default">
        <div class="form-group">
            <div class="row">
                <div class="col-xl-2 col-md-3 col-12 pt-2">
                    <label class="text-left d-block pt-2">{{ $t('time_period') }}</label>
                </div>
                <div class="col-xl-7 col-md-9 col-12">
                    <app-input
                        v-model="campaign.time_period"
                        :error-message="$errorMessage(errors, 'time_period')"
                        :list="time_periods"
                        type="select"
                    />
                    <small v-if="timePeriodNote" class="default-font-color">{{ $t('note') }}:
                        {{ timePeriodNote }}</small>
                </div>
            </div>
        </div>

        <div v-if="isDateRangeVisible" class="form-group">
            <div class="row">
                <div class="col-xl-2 col-md-3 col-12 pt-2">
                    <label class="text-left d-block pt-2">
                        {{ $t(campaign.time_period !== 'once' ? 'send_email_between' : 'sending_date') }}
                    </label>
                </div>
                <div class="col-xl-7 col-md-9 col-12">
                    <app-input
                        v-model="send_email_between"
                        :error-message="$errorMessage(errors, 'start_at')"
                        :placeholder="$t('choose', {field: $t('campaign_start_and_end_date')})"
                        date-mode="range"
                        type="date"
                    />
                </div>
            </div>
        </div>
        <div v-if="isDateRangeVisible && campaign.time_period !== 'hourly'" class="form-group">
            <div class="row">
                <div class="col-xl-2 col-md-3 col-12 pt-2">
                    <label class="text-left d-block pt-2">{{ $t('send_email_at') }}</label>
                </div>
                <div class="col-xl-7 col-md-9 col-12">
                    <app-input
                        v-model="campaign.campaign_start_time"
                        :error-message="$errorMessage(errors, 'campaign_start_time')"
                        :placeholder="$placeholder('send_email_at')"
                        type="time"
                    />
                </div>
            </div>
        </div>

        <div class="mt-5">
            <app-cancel-button
                :label="$t('back')"
                @click="go('back')"
            />
            <app-submit-button
                :label="this.$t('save_and_next')"
                :loading="loading"
                btn-class="ml-2"
                @click="submitData"
            />
        </div>
    </div>
</template>

<script>
import FormHelperMixins from "../../../../Mixins/Global/FormHelperMixins";
import {campaigns} from "../../../../config/apiUrl";
import moment from "moment";
import {formatUtcToLocal, time_format} from "../../../../Helpers/helpers";

export default {
    name: "CampaignDelivery",
    mixins: [FormHelperMixins],
    props: {
        props: {
            default: ''
        }
    },
    data() {
        return {
            url: '',
            loading: false,
            send_email_between: null,
            campaign: {
                time_period: '',
                campaign_start_time: ''
            },
            time_periods: [
                {id: null, value: this.$t('chose_a', {field: this.$fieldTitle('', 'time_period')}), disabled: true},
                {id: 'immediately', value: this.$t('immediately'), note: this.$t('immediately_note')},
                {id: 'once', value: this.$t('once'), note: this.$t('once_note')},
                {id: 'hourly', value: this.$t('hourly'), note: this.$t('hourly_note')},
                {
                    id: 'daily',
                    value: this.$t('daily'),
                    note: this.$t('time_period_note', {type: this.$fieldTitle('', 'daily')})
                },
                {
                    id: 'weekly',
                    value: this.$t('weekly'),
                    note: this.$t('time_period_note', {type: this.$fieldTitle('', 'weekly')})
                },
                {
                    id: 'monthly',
                    value: this.$t('monthly'),
                    note: this.$t('time_period_note', {type: this.$fieldTitle('', 'monthly')})
                },
                {
                    id: 'yearly',
                    value: this.$t('yearly'),
                    note: this.$t('time_period_note', {type: this.$fieldTitle('', 'yearly')})
                },
            ],
        };
    },
    methods: {
        submitData() {
            this.fieldStatus.isSubmit = true;
            this.loading = true;
            this.message = '';
            this.errors = {};

            let formData = {...this.campaign};
            if (this.send_email_between) {
                formData.start_at = moment(this.send_email_between.start).format('YYYY-MM-DD')
                formData.end_at = moment(this.send_email_between.end).format('YYYY-MM-DD')
            }
            if (this.campaign.campaign_start_time) {
                formData.campaign_start_time = moment(this.campaign.campaign_start_time, time_format()).utc().format('HH:mm')
            }
            return this.submitFromFixin(
                'post',
                this.url,
                formData
            );
        },
        afterSuccess({data}) {
            this.go('next');
        },
        go(place) {
            const state = place === 'back' ? 0 : 2;
            const url = window.location.protocol + "//" + window.location.host + window.location.pathname + '?state=' + state;
            window.history.pushState({path: url}, '', url);
            this.$emit(place, true)
        }
    },
    computed: {
        isDateRangeVisible() {
            if (this.campaign.time_period !== 'immediately') return true;
            this.send_email_between = null
            this.campaign.campaign_start_time = null;
            return false;
        },

        timePeriodNote() {
            const id = this.campaign.time_period;
            if (id) {
                const timePeriod = this.time_periods.find(t => t.id === id);
                return this.$optional(timePeriod, 'note');
            }
            return false;
        }
    },
    watch: {
        props: {
            handler: function (props) {
                if (typeof props === "object") {
                    this.campaign = {
                        time_period: props.time_period,
                        campaign_start_time: props.campaign_start_time ? formatUtcToLocal(props.campaign_start_time) : '',
                    }
                    if (props.start_at) {
                        this.send_email_between = {
                            end: new Date(props.end_at),
                            start: new Date(props.start_at)
                        }
                    }

                    this.url = `${campaigns}/${props.id}/delivery-settings`
                }
            },
            immediate: true
        }
    }
}
</script>

<style scoped>

</style>