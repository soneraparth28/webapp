<template>
    <div class="login-form d-flex align-items-center">
        <form class="sign-in-sign-up-form w-100" ref="form" data-url="admin/users/login" >
            <div class="text-center mb-4">
                <img :src="logoUrl" alt="" class="img-fluid logo">
            </div>
            <div class="form-row">
                <div class="form-group col-12">
                    <h6 class="text-center mb-0">{{ $t('hi', {object: $t('there')}) }}!</h6>
                    <label class="text-center d-block">{{ $t('login_to_your_dashboard') }}</label>
                </div>
            </div>

            <app-form-group
                type="select"
                v-if="demoCredentials"
                v-model="loginRole"
                :label="$t('login_as')"
                list-value-field="name"
                :list="selectableRole"
            />

            <app-form-group
                :label="$t('email')"
                type="email"
                :placeholder="$placeholder('your', 'email')"
                :required="true"
                v-model="login.email"
                :error-message="$errorMessage(errors, 'email')"
            />
            <app-form-group
                :label="$t('password')"
                type="password"
                :placeholder="$placeholder('your', 'password')"
                :required="true"
                v-model="login.password"
                :show-password="true"
                :error-message="$errorMessage(errors, 'password')"
            />
            <div class="form-row text-center">
                <app-message :type="type" :message="message" />
            </div>
            <div class="form-row">
                <div class="form-group col-12">
                    <app-submit-button
                        btn-class="d-inline-flex btn-block text-center"
                        :label="$t('login')"
                        :loading="loading"
                        @click="submitData"
                    />
                </div>
            </div>
            <div class="form-row">
                <div class="col-6">
                    <app-input
                        class="mb-primary"
                        type="single-checkbox"
                        :list-value-field="$t('remember_me')"
                        v-model="login.remember_me"
                    />
                </div>
                <div class="col-6 text-right">
                    <a :href="urlGenerator('/users/password-reset')" class="bluish-text">
                        <i data-feather="lock" class="pr-2"/>{{ $t('forgot', {subject: $t('password')}) }}?
                    </a>
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
    import ThemeMixin from "../../../Mixins/Global/ThemeMixin";
    import FormHelperMixins from "../../../Mixins/Global/FormHelperMixins";

    export default {
        name: "Login",

        mixins: [ThemeMixin, FormHelperMixins],
        props: {
            logoUrl: {
                required: false
            },
            companyName: {
                required: false
            },
            previousPage: {
                required: false
            },
            demo: {}
        },
        data() {
            return {
                login: {},
                loginRole: ''
            }
        },
        computed: {
            demoCredentials(){
                return this.demo ? JSON.parse(this.demo) : '';
            },
            selectableRole(){
                return [{ id: '', name: this.$t('select_role') }]
                    .concat(Object.keys(this.demoCredentials).map((item) => {
                        return {
                            id: item,
                            name: this.demoCredentials[item].role,
                        }
                    }))
            },
            loginRoleUpdate(){
                return this.loginRole
            }
        },
        methods: {
            submitData() {
                this.message = '';
                this.loading = true;
                this.save(this.login);
            },

            afterSuccess({ data }) {
                window.location.href = data;
            },
            afterFinalResponse(){},
        },
        watch: {
            loginRoleUpdate: {
                handler: function (role){
                    this.login.password = role ? this.demoCredentials[role].password : '';
                    this.login.email = role ? this.demoCredentials[role].email: '';
                }
            }
        }

    }
</script>

<style scoped>

</style>
