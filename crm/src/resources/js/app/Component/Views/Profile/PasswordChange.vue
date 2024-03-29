<template>
    <div class="content">
        <form ref="form" :data-url='`admin/auth/users/${formData.id}/password/change`'>
            <div class="form-group">
                <div class="row">
                    <div class="col-lg-3 col-xl-3 d-flex align-items-center">
                        <label for="input-text-old-password" class="text-left d-block mb-2 mb-lg-0">
                            {{$t('old_password')}}
                        </label>
                    </div>
                    <div class="col-lg-8 col-xl-8">
                        <app-input type="password"
                                   id="input-text-old-password"
                                   :placeholder="$placeholder('old', 'password')"
                                   v-model="formData.old_password"
                                   :error-message="$errorMessage(errors, 'old_password')"
                                   :required="true"
                        />
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-lg-3 col-xl-3">
                        <label for="input-text-new-password" class="text-left d-block mb-2 mb-lg-0">
                            {{$fieldTitle('new', 'password')}}
                        </label>
                    </div>
                    <div class="col-lg-8 col-xl-8">
                        <app-input type="password"
                                   :required="true"
                                   id="input-text-new-password"
                                   :placeholder="$placeholder('new', 'password')"
                                   v-model="formData.password"
                                   :error-message="$errorMessage(errors, 'password')"/>

                        <div class="note note-warning p-4 mt-2">
                            <p class="m-1" v-html="purify($t('password_requirements_message'))"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-lg-3 col-xl-3 d-flex align-items-center">
                        <label for="input-text-confirm-password" class="text-left d-block mb-2 mb-lg-0">
                            {{$fieldTitle('confirm', 'password')}}
                        </label>
                    </div>
                    <div class="col-lg-8 col-xl-8">
                        <app-input type="password"
                                   id="input-text-confirm-password"
                                   :required="true"
                                   :placeholder="$placeholder('confirm', 'password')"
                                   v-model="formData.password_confirmation"/>
                    </div>
                </div>
            </div>

            <div class="form-group mt-5 mb-0">
                <app-submit-button :loading="loading" @click="submitData" />
            </div>
        </form>
    </div>
</template>

<script>

    import Note from "../../Helper/Note/Note";
    import FormHelperMixins from "../../../Mixins/Global/FormHelperMixins";
    import {purify} from "../../../../core/helpers/purifier/HTMLPurifyHelper";

    export default {
        name: "PasswordChange",
        components: {Note},
        mixins: [FormHelperMixins],
        data() {
            return {
                purify,
                formData: {},
                errors: {},
                loading: false
            }
        },
        methods:{
            submitData()
            {
                this.loading = true;
                this.errors = {};
                this.save(this.formData);
            },

            afterError(response) {
                this.loading = false;
                this.errors = response.data.errors;
            },

            afterSuccess(response) {
                this.loading = false;
                this.$toastr.s("", response.data.message);
                this.formData = {};
                this.scrollToTop()
            },

            cancelUser(){
                location.reload();
            },
        },
        computed: {
            userInfo() {
                return this.$store.getters.getUserInformation
            }
        },
        mounted() {
            this.$store.dispatch('getUserInformation');
        },

        watch: {
            userInfo: {
                handler: function (user) {
                    this.formData = user;
                },
                deep: true
            }
        }

    }
</script>
