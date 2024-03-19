<template>
    <div class="content-wrapper">
        <app-page-top-section
            :title="$allLabel('segments')"
            icon="menu"
            :directory="$t('segments')"
        >
            <app-default-button
                :url="`/${segments}/create`"
                :title="$addLabel('segment')"
                v-if="$can('create_segments')"
            />
        </app-page-top-section>

        <app-table
            id="segment-table"
            :options="options"
            @action="getAction"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed('segment-table')"
            @cancelled="cancelled"
        />

    </div>
</template>

<script>
    import {segments} from '../../../config/apiUrl'
    import SegmentsMixin from "../../DataTable/DataTableMixins/SegmentsMixin";

    export default {
        name: "Segments",
        mixins: [SegmentsMixin],
        data() {
            return {
                segments
            }
        },
    }
</script>
