<template>
   <modal id="user-modal"
          v-model="showModal"
          :title="generateModalTitle('user')"
          @submit="submitData"
          :loading="loading"
          :preloader="preloader"
   >
        <form ref="form" :data-url='selectedUrl || getBaseURL'>
            <div class="form-group">
                <label>{{ $fieldTitle('first', 'name') }}</label>
                <app-input
                    :placeholder="$placeholder('first', 'name')"
                    v-model="formData.first_name"
                    :required="true"
                    :error-message="$errorMessage(errors, 'first_name')"
                />
            </div>
            <div class="form-group">
                <label>{{ $fieldTitle('last', 'name') }}</label>
                <app-input
                    :placeholder="$placeholder('last', 'name')"
                    v-model="formData.last_name"
                    :required="true"
                    :error-message="$errorMessage(errors, 'last_name')"
                />
            </div>
        </form>
   </modal>
</template>

<script>
    import FormHelperMixins from "../../../../Mixins/Global/FormHelperMixins";
    import ModalMixin from "../../../../Mixins/Global/ModalMixin";
    import {brand_users} from "../../../../config/apiUrl";

    export default {
        name: "UserModal",
        mixins: [FormHelperMixins, ModalMixin],
        props: ['alias'],
        methods: {
            afterSuccess({data}) {
                this.toastAndReload(data.message, 'user-table')
                $('#user-modal').modal('hide');
            },

            afterSuccessFromGetEditData(response) {
                this.formData = response.data;
                this.preloader = false;
            }
        },
        computed: {
            getBaseURL() {
                return {
                    brand: brand_users,
                    app: `admin/auth/users`
                }[this.alias];
            }
        }
    }
</script>

