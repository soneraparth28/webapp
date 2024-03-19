<template>
    <div class="content-wrapper">
        <app-page-top-section
            :title="$allLabel('campaigns')"
            :directory="$t('campaigns')"
            icon="sun"
        >
            <app-default-button
                :title="$addLabel('campaign')"
                :url="api.create"
                v-if="$can('create_campaigns')"
            />
        </app-page-top-section>

        <app-table
            :options="options"
            @action="triggerActions"
            id="campaigns-table"
        />

        <app-confirmation-modal
            @cancelled="cancelled"
            @confirmed="confirmed('campaigns-table')"
            modal-id="app-confirmation-modal"
            :message="$t('this_content_will_be_archived_permanently')"
            icon="trash-2"
            v-if="confirmationModalActive"
        />
        <app-confirmation-modal
            @cancelled="statusConfirmationModalActive = false"
            @confirmed="changeCurrentStatus()"
            modal-id="app-confirmation-modal"
            :message="statusModalMessage"
            icon="check-circle"
            modalClass="warning"
            v-if="statusConfirmationModalActive"
        />
    </div>
</template>

<script>
import {campaigns, campaigns_create} from "../../../config/apiUrl";
import {axiosGet, axiosPost, urlGenerator} from "../../../Helpers/AxiosHelper";
import CampaignMixins from "../../DataTable/DataTableMixins/CampaignMixin";

export default {
    name: "Campaigns",
    mixins: [CampaignMixins],
    data() {
        return {
            api: {
                create: campaigns_create,
                selectedUrl: ''
            },
            statusConfirmationModalActive: false,
            statusModalMessage: '',
            changeStatusUrl: ''
        }
    },
    methods: {
        triggerActions(row, action, active) {
            if (action.name === 'duplicate') {
                this.duplicate(row.id);
            } else if (action.name === 'change_status') {
                this.statusConfirmationModalActive = true;
                this.changeStatusUrl = `${campaigns}/${row.id}/change-status?status=${action.status}`
                this.statusModalMessage = this.$t('the_campaign_will_be_action',{action: action.status})
            } else {
                this.getAction(row, action, active)
            }
        },

        duplicate(id) {
            axiosPost(`${campaigns}/${id}/duplicate`).then(({data}) => {
                this.$toastr.s('', data.message);
                window.location = urlGenerator(`${campaigns}/${data.campaign.id}/edit?state=4`);
            })
        },
        changeCurrentStatus() {
            axiosGet(this.changeStatusUrl).then(({data}) => {
                this.toastAndReload(data.message, 'campaigns-table');
            }).finally(()=>{
                this.statusConfirmationModalActive = false;
                this.changeStatusUrl = ''
                this.statusModalMessage = ''
            })
        }
    },

    created() {
        this.$store.dispatch('getStatuses', 'campaign')
    }
}
</script>

<style scoped>

</style>
