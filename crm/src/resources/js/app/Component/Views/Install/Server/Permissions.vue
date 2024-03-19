<template>
    <note :title="$t('attention')"
          :description="$t('server_permission_required', {
        replace: collection(permission_requirements).pluck('folder').join(', ')})"/>
</template>

<script>

    import Note from "../../../Helper/Note/Note";

    export default {
        name: "Permissions",
        components: {Note},
        props: {
            permission_requirements: {
                default: []
            }
        },
        data() {
            return {}
        },
        methods: {},
        created() {

        }
    }
</script>
