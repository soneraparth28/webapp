<template>
    <div class="content-wrapper" v-if="Object.keys(formData).length">
        <app-page-top-section :title="formData.name"
                              :directory="`${$t('lists')} | <a href='${urlGenerator(lists_front_end)}'>${$t('back_to', {destination: $allLabel('lists')} )}</a>`"
                              icon="menu"
                              :hide-button="true"/>

        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card border-0 card-with-shadow h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-primary">
                            <h5 class="card-title mb-0">
                                {{ $t('details') }}
                            </h5>
                            <div class="dropdown options-dropdown ml-auto">
                                <button type="button" class="btn-option btn" data-toggle="dropdown">
                                    <app-icon name="more-vertical"/>
                                </button>

                                <div class="dropdown-menu dropdown-menu-right py-2 mt-1">

                                    <a class="dropdown-item px-4 py-2" href="#" v-if="$can('update_lists')" @click.prevent="redirectEditPage()">
                                        {{$t('edit')}}
                                    </a>

                                    <a class="dropdown-item px-4 py-2" href="#"
                                       v-if="$can('delete_lists')"
                                       @click.prevent="confirmationModalActive = true">
                                        {{$t('delete')}}
                                    </a>

                                </div>
                            </div>
                        </div>
                        <div class="mb-primary">
                            <p class="text-muted mb-0">{{ $t('name') }}</p>
                            <p>{{ formData.name ? formData.name: $t('not_added_yet') }}</p>
                        </div>
                        <div class="mb-primary">
                            <p class="text-muted mb-0">{{$t('description')}}</p>
                            <p class="text-justify">{{ formData.name ? formData.description: $t('not_added_yet') }}</p>
                        </div>
                        <div class="mb-primary">
                            <p class="text-muted mb-0">{{$t('type')}}</p>
                            <p>{{ formData.type ? $t(`status_${formData.type}`): $t('not_added_yet') }}</p>
                        </div>
                        <div class="mb-primary">
                            <p class="text-muted mb-0">{{$t('no_of_segment_used')}}</p>
                            <p>{{ formData.segments_count ? formData.segments_count : $t('not_added_yet') }}</p>
                        </div>
                        <div class="mb-primary">
                            <p class="text-muted mb-0">{{$t('subscribers')}}</p>
                            <p class="mb-0">{{$t('total')}}: {{ formData.subscribed_count }}</p>
                            <p class="mb-0">{{$t('manually_added')}}:
                                {{
                                parseInt(formData.subscribed_count) - parseInt(formData.segment_subscriber_count) <= 0 ?
                                0 : parseInt(formData.subscribed_count) - parseInt(formData.segment_subscriber_count)
                                }}
                            </p>
                            <p class="mb-0">{{$t('using_segment')}}: {{formData.segment_subscriber_count}}</p>
                        </div>
                        <div class="mb-primary">
                            <p class="text-muted mb-0">{{$t('unsubscribers')}}</p>
                            <p>{{formData.unsubscribed_count}}</p>
                        </div>
                        <div class="mb-primary">
                            <p class="text-muted mb-0">{{$t('creation_date')}}</p>
                            <p>{{creationDate}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-6">
                        <app-counter class="mb-primary"
                                     icon="user-plus"
                                     :title="$fieldTitle('total', 'subscribers')"
                                     :count="formData.subscribed_count"/>
                    </div>
                    <div class="col-6">
                        <app-counter class="mb-primary"
                                     icon="user-minus"
                                     :title="$fieldTitle('total', 'unsubscribers')"
                                     :count="formData.unsubscribed_count"/>
                    </div>
                </div>
                <div>
                    <app-subscribers v-bind="subscribersRules"></app-subscribers>
                </div>
            </div>
        </div>

        <app-confirmation-modal v-if="confirmationModalActive"
                                modal-id="app-confirmation-modal"
                                @confirmed="confirmDelete('list-table')"
                                @cancelled="cancelled"/>
    </div>
    <app-pre-loader v-else class="p-primary"/>
</template>

<script>
    import {lists, lists_front_end} from "../../../config/apiUrl";
    import {formatDateToLocal} from "../../../Helpers/helpers";
    import DeleteMixin from "../../../Mixins/Global/DeleteMixin";
    import {FormMixin} from "../../../../core/mixins/form/FormMixin";
    import {urlGenerator} from "../../../Helpers/AxiosHelper";

    export default {
        props: ['selectedUrl'],
        name: "Show",
        mixins: [FormMixin, DeleteMixin],
        components: {
            'app-counter': require('../../Helper/Dashboard/Counter').default,
            'app-subscribers': require('../Subscriber/Subscribers').default
        },
        data() {
            return {
                formData: {},
                lists_front_end,
                subscribersRules: {
                    url: '',
                    wrapper: false,
                    topBar: false,
                    actionColumn: false,
                    listColumn: false,
                    isRowsSelectable: false,
                    list_type: 'imported',
                    hideListFilter: true

                },
                urlGenerator
            }
        },
        created() {
            this.subscribersRules.url = `${this.selectedUrl}/subscribers`
        },
        computed: {
            creationDate() {
                return formatDateToLocal(this.formData.created_at);
            }
        },
        methods: {
            afterSuccessFromGetEditData(response) {
                this.formData = response.data;
                this.subscribersRules.list_type = response.data.type;
            },
            redirectEditPage() {
                window.location.replace(urlGenerator(`/${lists}/${this.formData.id}/edit`));
            },
            redirectToList() {
                window.location = urlGenerator(lists_front_end);
            },
            confirmDelete(id) {
                this.delete_url = `/${lists}/${this.formData.id}`;
                this.confirmed(id).then(response => window.location = urlGenerator(`/${lists_front_end}`))
            },
        },
    }
</script>


<style scoped>
    /deep/ .content-wrapper {
        padding: 0 !important;
    }
</style>


