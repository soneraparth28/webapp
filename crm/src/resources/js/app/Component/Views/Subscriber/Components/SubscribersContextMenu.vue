<template>
    <div>
        <div class="bulk-floating-action-wrapper">
            <div v-if="contextMenu === 'subscribers'"
                 :class="{'loading-opacity': loading}"
                 class="actions">

                <app-context-button
                    v-if="$can('add_to_lists_subscribers')"
                    icon="align-justify"
                    :title="this.$t('add_to_new_list')"
                >
                    <div class="form-group p-primary mb-0">
                        <app-input
                            v-model="newListName"
                            :placeholder="$t('type_your_new_list_name')"
                            @keydown.enter="handleLists(newListName)"
                        />
                        <app-message :message="$errorMessage(errors, 'name')" type='error'/>
                    </div>
                </app-context-button>

                <app-context-button
                    icon="user-x"
                    type="button"
                    :title="this.$t('add_to_blacklist')"
                    @action="addToBlacklist"
                />

                <app-context-button
                    icon="flag"
                    :title="this.$t('subscribe_or_unsubscribe')"
                >
                    <a class="dropdown-item"
                       href="#"
                       :title="this.$t('make_subscribed')"
                       @click.prevent="changeStatus('subscribed')">
                        {{ $t('status_subscribed') }}
                    </a>
                    <a class="dropdown-item"
                       href="#"
                       :title="this.$t('make_unsubscribed')"
                       @click.prevent="changeStatus('unsubscribed')">
                        {{ $t('unsubscribed') }}
                    </a>
                </app-context-button>

                <app-context-button
                    :dropdown-menu-class="'dropdown-menu-with-search'"
                    :keep-open="true"
                    icon="x-square"
                    :title="$t('remove_from_list')"
                >
                    <app-list-dropdown
                        v-model="detachableLists"
                        :lists="removableLists"
                        :title="$t('remove_from_this_list')"
                        @filteredFlag="detachFlag = $event"
                    />
                    <template v-if="detachWatcher && detachFlag">
                        <div class="dropdown-divider my-0"/>
                        <div class="p-primary text-right">
                            <app-submit-button
                                :label="$t('submit')"
                                :loading="loading"
                                btn-class="btn btn-primary"
                                @click="handleLists('detach')"
                            />
                        </div>
                    </template>

                </app-context-button>

                <app-context-button
                    :class="{'loading-opacity': loading}"
                    :keep-open="true"
                    dropdown-menu-class="dropdown-menu-with-search"
                    icon="plus-square"
                    :title="$t('add_to_list')"
                >

                    <app-list-dropdown
                        v-model="attachableLists"
                        :lists="lists"
                        :title="$t('add_to_this_list')"
                        @filteredFlag="attachFlag = $event"
                    />
                    <template v-if="attachWatcher && attachFlag">
                        <div class="dropdown-divider my-0"/>
                        <div class="p-primary text-right">
                            <app-submit-button
                                :label="$t('submit')"
                                :loading="loading"
                                btn-class="btn btn-primary"
                                @click="handleLists('attach')"
                            />
                        </div>
                    </template>
                </app-context-button>

                <app-context-button
                    icon="trash-2"
                    type="button"
                    :title="this.$t('delete_subscriber')"
                    @action="confirmationModalActive = true"
                />

                <app-overlay-loader v-if="loading"/>
            </div>

            <div v-else-if="contextMenu === 'blacklist'"
                 class="actions"
                 :class="{'loading-opacity': loading}">

                <app-context-button
                    icon="user-plus"
                    type="button"
                    :title="$t('add_to_subscriber_list')"
                    @action="changeStatus('subscribed', 'blacklisted')"
                />
                <app-context-button
                    icon="user-minus"
                    type="button"
                    :title="$t('add_to_unsubscribe_list')"
                    @action="changeStatus('unsubscribed', 'blacklisted')"
                />
                <app-context-button
                    v-if="$can('bulk_destroy_subscribers')"
                    icon="trash-2"
                    type="button"
                    :title="$t('delete_subscriber')"
                    @action="bulkDestroy('blacklisted')"
                />

                <app-overlay-loader v-if="loading"/>

            </div>
        </div>
        <app-confirmation-modal
            v-if="confirmationModalActive"
            @confirmed="bulkDestroy(['subscribed', 'unsubscribed'])"
            modal-id="app-confirmation-modal"
            :message="$t('subscriber_bulk_delete_confirmation')"
            @cancelled="confirmationModalActive = false"
        />
    </div>
</template>

<script>
import FormHelperMixins from "../../../../Mixins/Global/FormHelperMixins";
import {axiosPost} from "../../../../Helpers/AxiosHelper";
import {
    subscribers_add_to_lists,
    subscribers_bulk_destroy,
    subscribers_change_status,
    subscribers_remove_from_lists
} from "../../../../config/apiUrl";

export default {
    name: "SubscribersContextMenu",
    mixins: [FormHelperMixins],
    components: {
        'app-context-button': require('../../../Helper/Button/ContextButton').default,
        'app-list-dropdown': require('./ListDropdown').default
    },
    props: {
        subscribers: {
            default() {
                return [];
            }
        },
        lists: {
            default() {
                return [];
            }
        },
        isBulkAction: {
            default() {
                return false;
            }
        },
        contextMenu: String
    },
    data() {
        return {
            detachFlag: true,
            attachFlag: true,
            newListName: '',
            attachableLists: [],
            detachableLists: [],
            loading: false,
            confirmationModalActive: false,
        }
    },
    methods: {

        handleLists(type) {
            const {url, formData} = this.bulkListActionForm(type);
            this.loading = true
            axiosPost(url, formData).then(({data}) => {
                this.toastAndReload(data.message, 'subscriber-table')
                this.closeContextMenu();
            }).catch(({response}) => {
                if (response.data.message) {
                    this.toastException(response.data)
                }
            }).finally(() => {
                this.$store.dispatch('getSelectableLists')
                this.attachableLists = []
                this.loading = false
            })
        },


        bulkDestroy(pickOnly) {
            const data = this.getInitialData();
            data.pickOnly = pickOnly
            this.loading = true;
            axiosPost(subscribers_bulk_destroy, data).then(({data}) => {
                if (data.message && data.error_message) {
                    this.toastAndReload(this.$t('subscribers_deleted_partially'), 'subscriber-table')
                } else {
                    this.toastAndReload(data.message, 'subscriber-table')
                }
                this.closeContextMenu();
            }).catch(({response}) => {
                if (response.data.message) {
                    this.toastException(response.data)
                }
                if (response.data.hasOwnProperty('error_message')) {
                    if (response.data.error_message) {
                        this.$toastr.e(response.data.error_message)
                    }
                }
            }).finally(() => {
                this.loading = false

            })
        },

        changeStatus(status, pickOnly = []) {
            const data = this.getInitialData();
            data.status = status;
            data.pickOnly = pickOnly
            this.loading = true

            axiosPost(subscribers_change_status, data).then(({data}) => {
                this.toastAndReload(data.message, 'subscriber-table')
                this.closeContextMenu();
            }).catch(({response}) => {
                if (response.data.message) {
                    this.toastException(response.data)
                }
            }).finally(() => {
                this.loading = false
            })
        },

        addToBlacklist() {
            const data = this.getInitialData();
            this.$parent.addToBlacklist(data)
        },

        closeContextMenu() {
            this.$emit('close')
        },

        getInitialData() {
            return {
                isBulkAction: this.isBulkAction,
                subscribers: this.subscriberIds
            }
        },
        bulkListActionForm(type) {
            const formData = this.getInitialData();
            let url = '';

            if (type === 'attach') {
                formData.lists = this.attachableLists
                url = subscribers_add_to_lists
            } else if (type === 'detach') {
                formData.lists = this.detachableLists
                url = subscribers_remove_from_lists
            } else {
                formData.name = type
                url = subscribers_add_to_lists
            }
            return {
                url, formData
            }
        }
    },

    computed: {
        subscriberIds() {
            return this.collection(this.subscribers).pluck()
        },
        subscriberListIds() {
            const lists = this.subscribers
                .map(s => s.lists.map(l => l.id))
                .flat();
            return Array.from(new Set(lists))
        },
        removableLists() {
            if (this.isBulkAction) {
                return this.lists;
            }
            return this.lists.filter(l => this.subscriberListIds.includes(l.id));
        },
        attachWatcher() {
            return !!this.attachableLists.length
        },
        detachWatcher() {
            return !!this.detachableLists.length
        }
    },
}
</script>
