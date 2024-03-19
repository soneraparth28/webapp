<template>
    <div class="font-size-default">
        <div class="row">
            <div class="col-12">
                <div class="note-title d-flex">
                    <app-icon name="book-open"/>
                    <h6 class="card-title pl-2">{{ $t('who_are', { subject: $t('audience_of_campaign') }) }}</h6>
                </div>
                <div class="note note-warning p-4 mb-5">
                    <p class="m-1">{{ $fieldTitle('campaign_audience_description') }}</p>
                    <p  v-if="provider === 'smtp'"
                        class="m-1"
                    >{{ $fieldTitle('campaign_depends_on_quota') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-3 col-xl-2 col-12">
                    <label class="text-left d-block pt-2">{{ $fieldTitle('select', 'lists')}}</label>
                </div>
                <div class="col-xl-7 col-md-9 col-12">
                    <app-input
                        type="multi-select"
                        :error-message="$errorMessage(errors, 'list')"
                        :placeholder="$placeholder('select', 'list')"
                        v-model="formData.audiences.list"
                        :list="lists"
                        list-value-field="name"
                    />
                </div>
            </div>
        </div>
        <div class="form-group mb-5">
            <div class="row">
                <div class="col-md-3 col-xl-2 col-12">
                    <label class="text-left d-block pt-2">{{ $fieldTitle('select', 'subscriber')}}</label>
                </div>
                <div class="col-xl-7 col-md-9 col-12">
                    <app-input
                        type="search-and-select"
                        :placeholder=" selected || $t('select_subscribers')"
                        v-model="formData.audiences.subscriber"
                        :options="subscriberOptions"
                    />
                </div>
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
    </div>
</template>

<script>
    import FormHelperMixins from "../../../../Mixins/Global/FormHelperMixins";
    import {mapState} from "vuex";
    import {campaigns, select_subscribers_api} from "../../../../config/apiUrl";
    import {urlGenerator} from "../../../../Helpers/AxiosHelper";

    export default {
        name: "CampaignAudience",
        mixins: [FormHelperMixins],
        props: {
            props: {
                default: {}
            }
        },
        data() {
            return {
                url: '',
                formData: {
                    audiences: {
                        list: [],
                        subscriber: []
                    }
                },
                selected: '',
                subscriberOptions: {
                    url: urlGenerator(select_subscribers_api),
                    modifire: (v) => {
                        return { id: v.id, value: v.full_name + ' (' + v.email+ ')' }
                    },
                    multiple: true,
                    per_page: 10,
                    // queryName: 'last_name',
                    loader: 'app-pre-loader',
                    // params: {
                    //     'type': 'type1',
                    //     'isWanted': true
                    // }
                }
            };
        },
        methods: {
            submitData() {
                this.fieldStatus.isSubmit = true;
                this.loading = true;
                this.message = '';
                this.errors = {};
                return this.submitFromFixin(
                    'post',
                    this.url,
                    this.formData
                );
            },
            afterSuccess({data}) {
                this.go('next');
            },
            go(place) {
                const state = place === 'back' ? 1 : 3;
                const url = window.location.protocol + "//" + window.location.host + window.location.pathname + '?state='+state;
                window.history.pushState({path:url},'', url);
                this.$emit(place, true)
            }
        },
        computed: {
            ...mapState({
                subscribers: ({subscriber}) => subscriber.subscribers,
                lists: ({lists}) => lists.lists,
                provider: ({additional}) =>  additional.provider
            })
        },
        mounted() {
            this.$hub.$on('selectedCard', ({card, campaign, message}) => {
                if (card === 'app-campaign-audience') {
                    this.$store.dispatch('selectableSubscribers')
                    this.$store.dispatch('getSelectableLists')
                    this.$store.dispatch('getProvider')
                }
            })
        },
        watch: {
            props: {
                handler: function (props) {
                    if (typeof props === "object"){
                        this.url = `${campaigns}/${props.id}/audiences`
                        this.props.audiences && this.props.audiences.forEach(audience => {
                            if(audience.audience_type === 'subscriber'){
                                // this.formData.audiences.subscriber = audience.audiences.join(',')
                                this.selected = `${audience.audiences.length } ${this.$t('selected')}`
                            }else {
                                this.formData.audiences[audience.audience_type] = audience.audiences;
                            }
                        })
                    }
                },
                immediate: true
            }
        }
    }
</script>

<style scoped>

</style>
