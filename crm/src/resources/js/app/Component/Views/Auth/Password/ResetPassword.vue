<template>
    <div class="login-form d-flex align-items-center">
        <form class="sign-in-sign-up-form w-100" ref="form" data-url="users/reset-password">
            <div class="text-center mb-4">
                <img :src="logoUrl" alt="" class="img-fluid logo">
            </div>
            <div class="form-row">
                <div class="form-group col-12">
                    <h6 class="text-center mb-0">{{ $t('reset_password') }}</h6>
                </div>
            </div>

            <app-form-group
                :label="$t('password')"
                type="password"
                :placeholder="$placeholder('your', 'password')"
                v-model="formData.password"
                :error-message="$errorMessage(errors, 'password')"
            />

            <app-form-group
                :label="$fieldTitle('confirm', 'password')"
                type="password"
                :placeholder="$placeholder('confirm', 'password')"
                v-model="formData.password_confirmation"
                :error-message="$errorMessage(errors, 'password_confirmation')"
            />

            <div class="form-row">
                <div class="form-group col-12">
                    <app-submit-button
                        btn-class="d-inline-flex btn-block text-center"
                        :label="$t('change')"
                        :loading="loading"
                        @click="submitData"
                    />
                </div>
            </div>
            <div class="form-group">
                <div class="col-12">
                    <p class="text-center mt-5 footer-copy">
                        {{ $t('copyright') }} @ {{ new Date().getFullYear() }} {{ $t('by') }} {{ companyName }}
                    </p>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    import ThemeMixin from "../../../../Mixins/Global/ThemeMixin";
    import FormHelperMixins from "../../../../Mixins/Global/FormHelperMixins";
    import Note from "../../../Helper/Note/Note";

    export default {
        name: "ResetPassword",
        components: {Note},
        props: {
            logoUrl: {
                required: false
            },
            companyName: {
                required: false
            },
            user: {
                required: true,
                type: String
            },
            token: {
                required: true,
                type: String
            }
        },
        mixins: [ThemeMixin, FormHelperMixins],
        methods: {
            afterSuccess(response) {
                this.message = response.data.message;
                window.location = response.data.redirect
            },
            afterFinalResponse(){},
        },
        watch: {
            user: {
                handler: function (user) {
                    user = JSON.parse(user);
                    this.formData.email = user.email;
                    this.formData.token = this.token;
                },
                immediate: true
            }
        }
    }
</script>

<style scoped>

</style>
