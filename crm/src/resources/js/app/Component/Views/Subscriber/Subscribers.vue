<template>
    <div :class="wrapper ? 'content-wrapper' : ''">
        <app-page-top-section v-if="topBar"
              :title="$allLabel('subscribers')"
              :directory="$t('subscribers')"
              icon="message-circle"
        >
            <app-default-button
                :url="apiUrl.subscriber_import"
                :title="$fieldTitle('import', 'subscribers', true)"
                btn-class="btn btn-info mr-2 text-white"
            />

            <app-default-button
                :url="apiUrl.subscriber_create"
                :title="$addLabel('subscriber')"
                v-if="$can('create_subscribers')"
            />
        </app-page-top-section>

        <app-table
            id="subscriber-table"
            :options="options"
            @action="subscriberActions"
            @getRows="getSelectedRows"
        />

        <app-subscribers-context-menu
            v-if="isContextMenuOpen"
            @close="isContextMenuOpen = false"
            :subscribers="selectedSubscribers"
            :lists="selectableLists"
            :isBulkAction="isBulkAction"
            :contextMenu="contextMenu"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed('subscriber-table')"
            @cancelled="cancelled"
        />
        <app-confirmation-modal
            v-if="actionConfirmationModalActive"
            :message="modalMessage"
            :modal-class="modalClass"
            :icon="modalIcon"
            modal-id="app-confirmation-modal"
            @confirmed="updateStatus"
            @cancelled="actionConfirmationModalActive = false"
        />
    </div>
</template>

<script>

    import {axiosPost} from "../../../Helpers/AxiosHelper";
    import {SubscriberMixin} from "../../../Mixins/Subscriber/SubscriberMixin"
    import {mapState} from "vuex";
    import {subscribers_add_to_blacklist, subscribers_change_status} from "../../../config/apiUrl";

    export default {
        name: "Subscribers",
        mixins: [SubscriberMixin],
        components: {
            'app-subscribers-context-menu': require('./Components/SubscribersContextMenu').default
        },
        data() {
            return  {
                isContextMenuOpen: false,
                selectedSubscribers: [],
                isBulkAction: false,
                actionConfirmationModalActive: false,
                modalIcon: '',
                modalClass: '',
                modalMessage: '',
                actionUrl: '',
                actionName: '',
                payloadData: {},
            }
        },
        methods: {
            subscriberActions(row, action, active) {
                if (action.name === 'add_to_blacklist' || action.name === 'change_status'){
                    this.actionConfirmationModalActive = true;
                    this.modalIcon = action.modalIcon;
                    this.modalClass = action.modalClass;
                    this.modalMessage = action.modalMessage;
                    this.actionName = action.name;
                    if (action.name === 'add_to_blacklist') {
                        this.payloadData = {
                            subscribers : row.id,
                            isBulkAction: this.isBulkAction
                        };
                        this.actionUrl = subscribers_add_to_blacklist;
                    } else {
                        this.payloadData = {
                            isBulkAction: this.isBulkAction,
                            status: action.statusName,
                            subscribers: [row.id],
                        }
                        this.actionUrl = subscribers_change_status;
                    }
                } else {
                    this.getAction(row, action, active)
                    this.triggerActions(row, action, active);
                }
            },
            updateStatus(){
                if (this.actionName === 'add_to_blacklist'){
                    this.addToBlacklist();
                }else if (this.actionName === 'change_status'){
                    this.changeSubscriptionStatus();
                }
            },
            changeSubscriptionStatus(){
                this.loading = true;
                axiosPost(subscribers_change_status, this.payloadData).then(({data}) => {
                    this.toastAndReload(data.message, 'subscriber-table')
                }).catch(({response}) => {
                    if (response.data.message) {
                        this.toastException(response.data)
                    }
                }).finally(() => {
                    this.setInitial();
                })
            },
            addToBlacklist(data = this.payloadData) {
                this.loading = true;
                axiosPost(this.actionUrl || subscribers_add_to_blacklist, data).then( ({data}) => {
                    this.toastAndReload(data.message,  'subscriber-table')
                    this.isContextMenuOpen = false;
                    this.handleBulkAction(false);
                }).catch(({response}) =>
                    this.toastException(response.data)
                ).finally(() => {
                    this.setInitial();
                })
            },
            setInitial(){
                this.loading = false;
                this.modalIcon = '';
                this.modalClass = '';
                this.modalMessage = '';
                this.actionUrl = '';
                this.actionName = '';
                this.payloadData = {};
            },
            getSelectedRows(data, isBulkSelected) {
                this.isBulkAction = isBulkSelected;
                if (!this.isContextMenuOpen) {
                    this.isContextMenuOpen = !!Array.from(data).length;
                }
                this.selectedSubscribers = data;
            },

            handleBulkAction(payload) {
                this.isBulkAction = payload
            }
        },

        created() {
            this.$store.dispatch('getStatuses', 'subscriber');
            this.$store.dispatch('getSelectableLists');
            AppCookie.remove('subscriber-table-columns')
        },
        computed: {
            ...mapState({
                selectableLists: ({lists}) => lists.lists //lists.lists
            }),
            selectableListWatcher() {
                return !!Object.keys(this.selectableLists).length;
            }
        },

        watch: {
            'selectedSubscribers.length': function (flag) {
                this.isContextMenuOpen = !!flag;
            },
            selectableListWatcher: function (flag) {
                if (flag) {
                    this.options.filters.find(({key, option}) => {
                        if (key === 'lists') option.push(...this.selectableLists)
                    })
                }
            }
        }
    }
</script>

<style scoped>

</style>
