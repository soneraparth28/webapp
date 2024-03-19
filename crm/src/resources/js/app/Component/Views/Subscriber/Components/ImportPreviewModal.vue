<template>
    <div>
        <app-modal
            modal-alignment="top"
            modal-id="importPreviewModal"
            modal-size="fullscreen"
        >
            <template slot="header">
                <h5 class="modal-title" id="exampleModalLabel">{{$fieldTitle('preview', 'import')}}</h5>
                <button @click="$emit('close')" aria-label="Close" class="close outline-none" data-dismiss="modal"
                        type="button">
                    <span> <app-icon name="x"></app-icon> </span>
                </button>
            </template>

            <template slot="body">
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-6">
                                <h5> {{ $fieldTitle('filtered', 'subscribers') }} </h5>
                            </div>
                            <div class="col-6 text-right">
                                <app-button
                                    :label="$t('download')"
                                    className="btn btn-primary text-center"
                                    :is-disabled="false"
                                    @submit="download_csv"
                                />
                            </div>
                            <div
                                v-for="(column, parentIndex) in getExactColumns"
                                :class="`col-${columnWidth(parentIndex)}`"
                            >
                                <label>{{$t(column)}}</label>
                                <template v-for="(subscriber, index) in subscribersData.filtered">
                                    <app-input
                                        class="bottomFix"
                                        :id="`filtered-${index}-${column}`"
                                        :key="index"
                                        v-if="customFields(subscriber.custom_fields).includes(column)"
                                        v-model="subscriber.custom_fields[column]"
                                    />
                                    <app-input
                                        :id="`filtered-${index}-${column}`"
                                        :key="index"
                                        v-else-if="column in subscriber['errorBag']"
                                        v-model="subscriber[column]"
                                        :error-message="$errorMessage(subscriber['errorBag'], column)"
                                    />
                                    <app-input
                                        class="bottomFix"
                                        :id="`filtered-${index}-${column}`"
                                        :key="index"
                                        v-else
                                        v-model="subscriber[column]"
                                    />
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <template slot="footer">
                <button
                    @click="$emit('close')"
                    class="btn btn-secondary mr-4"
                    data-dismiss="modal"
                    type="button">{{$t('close')}}
                </button>
                <button
                    class="btn btn-primary"
                    type="button"
                    @click="submitData">{{$fieldTitle('save')}}</button>
            </template>
        </app-modal>
    </div>
</template>

<script>
    import {subscriber_bulk_import} from "../../../../config/apiUrl";

    export default {
        name: "ImportPreviewModal",
        props: {
            customFieldType: {
                default: 0
            },
            subscribers: {
                default() {
                    return {}
                }
            },
        },
        data() {
            return {
                subscribersData: [],
                type: 'error',
                message: ''
            }
        },

        methods: {
            submitData() {
                const formData = {
                    subscribers: [...this.filteredSubscribers()],
                    type: this.customFieldType
                }
                axios.post(subscriber_bulk_import, formData).then(({data}) => {
                    this.$emit('succeed', data)
                })
                .catch(({response}) => {
                    this.message = response.data.message
                    $(`.${this.type}`).toast('show');
                })
            },

            filteredSubscribers() {
                return [...this.subscribersData.filtered].map(subscriber => {
                    let data = {...subscriber};
                    delete data.errorBag;
                    return data;
                });
            },


            download_csv() {
                let columns = [...this.subscribers.columns]

                let csv = columns.join(',') + "\n";

                [...this.subscribers.filtered].forEach((row) => {

                    columns.forEach(column => {
                        if (column in row['errorBag']) {
                            csv += `${row[column]} (${row['errorBag'][column]}),`
                        }
                        else if (column in row['custom_fields']) {
                            csv += row['custom_fields'][column] +","
                        }
                        else {
                            csv += row[column] +","
                        }
                    })

                    csv += "\n"
                });

                this.$parent.downloadCSV(csv);
            },


            customFields(fields) {
                return typeof fields === 'object' ? Object.keys(fields) : [];
            },

            columnWidth(index) {
                const length = this.getExactColumns.length
                if(length > 6) {
                    if (index > 2) {
                        return 1;
                    }
                }
                return Math.ceil(12/length)
            }
        },
        computed: {
            subscriberObjectWatcher() {
                return Object.keys(this.subscribers).length;
            },

            getExactColumns() {
                if (!this.customFieldType) {
                    return this.subscribers.columns
                        .filter(e => ['first_name', 'last_name', 'email'].includes(e) )
                }
                return this.subscribers.columns;
            }

        },
        watch: {
            'subscriberObjectWatcher': {
                handler: function () {
                    this.subscribersData = {...this.subscribers}
                },
                immediate: true
            }
        },

    }
</script>

<style scoped>
    .bottomFix {
        height: 62px;
    }
</style>
