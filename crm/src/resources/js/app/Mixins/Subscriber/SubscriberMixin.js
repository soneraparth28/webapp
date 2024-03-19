import {subscriber_create, subscriber_import, subscriber_store, subscribers} from "../../config/apiUrl";
import {collection, formatDateToLocal} from "../../Helpers/helpers";
import DatatableHelperMixin from "../Global/DatatableHelperMixin";
import {urlGenerator} from "../../Helpers/AxiosHelper";

export let SubscriberMixin = {
    mixins: [DatatableHelperMixin],
    props: {
        url: {
            type: String,
            default: subscriber_store
        },
        wrapper: {
            type: Boolean,
            default: true
        },
        topBar: {
            type: Boolean,
            default: true
        },
        listColumn: {
            default: true
        },
        actionColumn: {
            default: true
        },
        statusFilter: {
            default: true
        },
        hideListFilter:{
            default: false
        },
        isRowsSelectable: {
            default: true
        },
        blacklistButton: {
            default: true
        },
        editButton: {
            default: true
        },
        deleteButton: {
            default: true
        },
        subscribeButton: {
            default: true
        },
        contextMenu: {
            default: 'subscribers'
        },
        customButtons: {
            type: Function,
            default() {
                return []
            }
        },
        triggerActions: {
            type: Function,
            default: (...obj) => {}
        },
        customFieldColumn: {
            type: Array,
            default: function () {
                return [];
            }
        },
        list_type: {
            type: String,
            default: 'imported'
        }
    },

    data() {
        return {
            apiUrl: {
                subscriber_create, subscriber_import
            },


            options: {
                name: 'Subscribers',
                url: this.url,
                showHeader: true,
                bulkSelectCheckbox: true,
                bulkSelectCheckboxLabel: this.$t('bulk_subscriber_select_label'),
                columns: [
                    {
                        title: this.$t('name'),
                        type: 'custom-html',
                        key: 'full_name',
                        uniqueKey: 'id',
                        modifier: function (name, rowData) {
                            return `<a href="${urlGenerator(subscribers)}/${rowData.id}/edit">${name}</a>`
                        }
                    },
                    {
                        title: this.$t('email'),
                        type: 'text',
                        key: 'email',
                        isVisible: true
                    },
                    {
                        title: this.$t('status'),
                        type: 'custom-html',
                        key: 'status',
                        isVisible: true,
                        modifier: status => {
                            return `<span class="badge badge-pill badge-${status.class}">
                                ${status.translated_name}
                            </span>`
                        }
                    },
                    ...this.customFieldColumn,
                    {
                        title: this.$t('no_of_email_delivered_per_sent'),
                        type: 'object',
                        key: 'delivered_count',
                        isVisible: true,
                        modifier: (delivered_count, subscriber) => {
                            return (!this.wrapper && this.list_type === 'dynamic') ? `${subscriber.delivered.length}/${subscriber.sent.length}` :`${delivered_count}/${subscriber.sent_count}`
                        }
                    },
                    this.listColumn ?
                        {
                            title: this.$t('lists_label'),
                            type: 'object',
                            key: 'lists',
                            isVisible: true,
                            modifier: function(lists) {
                                return `${collection(lists).latest().limit(3).pluck('name').join(', ')}${lists.length > 3 ? '...': ''}`
                            }
                        } : {},
                    {
                        title: this.$t('last_activity'),
                        type: 'custom-html',
                        key: 'last_activity',
                        isVisible: false,
                        modifier: function (last_activity) {
                            if (last_activity){
                                return formatDateToLocal(last_activity.updated_at)
                            }
                            return null
                        }
                    },
                    this.actionColumn ?
                        {
                            title: this.$t('actions'),
                            type: 'action',
                            key: 'invoice',
                            isVisible: true
                        } : {},

                ],
                filters: [
                    {
                        "title": this.$t('created'),
                        "type": "range-picker",
                        "key": "date",
                        "option": ["today", "thisMonth", "last7Days", "thisYear"]
                    },
                    /*{
                        "title": this.$t('sending_rate'),
                        "type": "range-filter",
                        "key": "sending rate"
                    },*/
                    this.statusFilter ?
                        {
                            "title": this.$t('status'),
                            "type": "checkbox",
                            "key": "status",
                            "option": [],
                            "listValueField": 'value'
                        } : {},
                    this.hideListFilter ? {} :
                    {
                        title: this.$t('lists'),
                        type: "drop-down-filter",
                        key: "lists",
                        option: [],
                        listValueField: 'name'
                    },
                ],
                paginationType: "pagination",
                responsive: true,
                rowLimit: 15,
                showAction: true,
                actionType: 'dropdown',
                enableRowSelect: this.isRowsSelectable,
                showCount: true,
                showClearFilter: true,
                actions: [
                    ...this.customButtons(),
                    this.blacklistButton ?
                        {
                            title: this.$t('add_to_blacklist'),
                            icon: 'user-minus',
                            type: 'modal',
                            modalIcon: 'x-circle',
                            modalMessage: this.$t('the_subscriber_will_be_added_to_blacklist'),
                            modalClass: 'warning',
                            name: 'add_to_blacklist',
                            modifier: () => this.$can('add_to_blacklist_subscribers')
                        }: {},
                    this.editButton ?
                        {
                            title: this.$t('edit'),
                            icon: 'edit',
                            type: 'page',
                            name: 'edit',
                            url: `/${subscriber_store}`,
                            modifier: () => this.$can('update_subscribers')
                        } : {},
                    this.subscribeButton ?
                        {
                            title: this.$t('unsubscribe'),
                            icon: 'edit',
                            type: 'modal',
                            modalIcon: 'edit',
                            modalMessage: this.$t('the_subscribers_status_will_be_changed_to_unsubscribed'),
                            modalClass: 'primary',
                            name: 'change_status',
                            statusName: 'unsubscribed',
                            modifier: (row) => row.status.name === "status_subscribed" && this.$can('update_subscribers')
                        } : {},
                    this.subscribeButton ?
                        {
                            title: this.$t('subscribe'),
                            icon: 'edit',
                            type: 'modal',
                            modalIcon: 'check-circle',
                            modalMessage: this.$t('the_subscribers_status_will_be_changed_to_subscribed'),
                            modalClass: 'primary',
                            name: 'change_status',
                            statusName: 'subscribed',
                            modifier: (row) => row.status.name === "status_unsubscribed" && this.$can('update_subscribers')
                        } : {},
                    this.deleteButton ?
                        {
                            title: this.$t('delete'),
                            icon: 'trash',
                            type: 'modal',
                            component: 'app-confirmation-modal',
                            modalId: 'app-confirmation-modal',
                            name: 'delete',
                            url: `/${subscriber_store}`,
                            modifier: () => this.$can('delete_subscribers')
                        } : {}
                ],
            }
        }
    },

    computed: {
        statusObjectWatcher() {
            return 0;
        }
    },

    watch: {
        'statuses.length': {
            handler: function (length) {
                const statuses = this.statuses.filter(status => status.name !== 'status_blacklisted')
                this.options.filters.find(({key, option}) => {
                    if (key === 'status')  option.push(...statuses)
                })
            },
            immediate: true
        },
    }
};
