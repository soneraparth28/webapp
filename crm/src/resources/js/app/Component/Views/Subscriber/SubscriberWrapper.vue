<template>
    <app-subscribers :custom-field-column="custom_fields" v-if="loaded" />
</template>

<script>
    import {customFieldColumn} from "../../DataTable/DataTableMixins/Helper/CustomField";

    export default {
        name: "SubscriberWrapper",
        data(){
            return {
                custom_fields: [],
                loaded: false
            }
        },
        created() {
            customFieldColumn().then(response => {
                this.custom_fields = response;
                this.loaded = true;
            }).catch(() => {
                this.loaded = true;
            })
        }
    }
</script>

<style scoped>

</style>
