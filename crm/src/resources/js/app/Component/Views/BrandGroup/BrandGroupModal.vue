<template>
    <modal id="brand-group-modal"
           v-model="showModal"
           :title="generateModalTitle('group')"
           @submit="submitData" :loading="loading"
           :preloader="preloader"
    >
        <form ref="form"
              :data-url='selectedUrl ? selectedUrl : `admin/app/brand-groups`'
              @submit.prevent="submitData"
        >
            <app-form-group
                :label="$fieldLabel('brand_group')"
                :placeholder="$placeholder('brand_group', 'name')"
                v-model="formData.name"
                :required="true"
                :error-message="$errorMessage(errors, 'name')"
            />
        </form>
    </modal>
</template>

<script>
    import FormHelperMixins from "../../../Mixins/Global/FormHelperMixins";
    import ModalMixin from "../../../Mixins/Global/ModalMixin";

    export default {
        name: "BrandGroupAddEditModal",
        mixins: [FormHelperMixins, ModalMixin],
        methods: {
            afterSuccess({data}) {
                this.formData = {}
                $('#brand-group-modal').modal('hide')
                this.$emit('input', false)
                this.$parent.toastAndReload(data.message, 'brand-group-table')
            },

            afterSuccessFromGetEditData(response) {
                this.preloader = false;
                this.formData = response.data;
            },
        },
    }
</script>

