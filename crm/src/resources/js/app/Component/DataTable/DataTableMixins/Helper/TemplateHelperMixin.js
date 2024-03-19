import {templates} from "../../../../config/apiUrl";

export default  {
    methods: {
        deletable() {
            $('#template-preview').modal('hide')
            this.isDeleteFromModal = true
            this.delete_url = `/${templates}/${this.template_id}`;
            this.confirmationModalActive = true;
        },

        triggerConfirmed(id) {
            this.confirmed(id)
            this.isDeleteFromModal = false
        }
    },
    mounted() {
        $('#template-preview').on('hidden.bs.modal', () =>  {
            this.isPreviewModalActive = false
        })
    },
    computed: {
        reOpenModalWatcher() {
            return !this.confirmationModalActive && this.isDeleteFromModal
        }
    },
    watch: {
        reOpenModalWatcher: function (flag) {
            if (flag) {
                this.isDeleteFromModal = false
                this.isPreviewModalActive = true
            }
        }
    }
}
