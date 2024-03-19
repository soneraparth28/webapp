<template>
    <div class="content-wrapper">
        <app-page-top-section
            :title="$t('black_list')"
            icon="message-circle"
            :directory="$t('subscribers')"
        >
        </app-page-top-section>

        <app-subscribers
            v-bind="$data.rules"
        ></app-subscribers>
    </div>
</template>

<script>

    import {subscriber_store} from "../../../config/apiUrl";
    import FormHelperMixins from "../../../Mixins/Global/FormHelperMixins";

    export default {
        name: "BlackListedSubscribers",
        mixins: [FormHelperMixins],
        components: {
            'app-subscribers': require('./Subscribers').default
        },
        data() {
            return {
                rules: {
                    url: `${subscriber_store}?black_listed=true`,
                    wrapper: false,
                    topBar: false,
                    statusFilter: false,
                    customButtons: () => this.actionCallback,
                    editButton: false,
                    blacklistButton: false,
                    contextMenu: 'blacklist',
                    triggerActions: (row, action, active) => this.getAction(row, action, active)
                },
                formData: {
                    subscribers: ''
                }
            }
        },
        methods: {
            submitData() {
                this.loading = true;
                this.message = '';
                this.errors = {};
                this.submitFromFixin(
                    'post',
                    `${subscriber_store}/remove-from-blacklist`,
                    this.formData
                );
            },
            afterSuccess({data}) {
                this.toastAndReload(data.message, 'subscriber-table')
            },
            getAction(row, action, active) {
                if(action.name === 'add_to_subscriber') {
                    this.formData.subscribers = row.id
                    this.submitData()
                }
            }
        },
        computed: {
            actionCallback() {
                return [{
                    title: this.$t('add_to_subscriber'),
                    icon: 'user-plus',
                    type: 'modal',
                    name: 'add_to_subscriber'
                }];
            },


        }

    }
</script>

<style scoped>

</style>
