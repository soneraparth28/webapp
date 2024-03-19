<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$allLabel('lists')" icon="menu" :directory="$t('lists')">
            <app-default-button
                :url="apiUrl.addNew"
                :title="$addLabel('list')"
                v-if="$can('create_lists')"
            />
        </app-page-top-section>

        <app-table
            id="list-table"
            :options="options"
            @action="getAction"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed('list-table')"
            @cancelled="cancelled"
        />
    </div>
</template>

<script>
    import {list_create, list_view} from "../../../config/apiUrl";
    import ListsMixin from "../../DataTable/DataTableMixins/ListsMixin";
    export default {
        name: "List",
        mixins: [ListsMixin],
        data() {
            return {
                list:'',
                apiUrl: {
                    addNew: `/${list_create}`,
                },
                list_view,
            }
        },
    }
</script>

