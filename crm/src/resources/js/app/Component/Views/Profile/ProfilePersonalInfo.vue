<template>
    <form ref="form" data-url='admin/auth/users/change-settings' v-if="!preloader">
        <div class="form-group">
            <div class="row">
                <div class="col-lg-3 col-xl-3 d-flex align-items-center">
                    <label for="input-text-first-name" class="text-left d-block mb-lg-0">
                        {{$t('first_name')}}
                    </label>
                </div>
                <div class="col-lg-8 col-xl-8">
                    <app-input type="text"
                               id="input-text-first-name"
                               :placeholder="$placeholder('first_name', '')"
                               v-model="userProfileInfo.first_name"
                               :error-message="$errorMessage(errors, 'first_name')"/>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-lg-3 col-xl-3 d-flex align-items-center">
                    <label for="input-text-last-name" class="text-left d-block mb-2 mb-lg-0">
                        {{$t('last_name')}}
                    </label>
                </div>
                <div class="col-lg-8 col-xl-8">
                    <app-input type="text"
                               id="input-text-last-name"
                               :placeholder="$placeholder('last_name', '')"
                               v-model="userProfileInfo.last_name"
                               :error-message="$errorMessage(errors, 'last_name')"/>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-lg-3 col-xl-3 d-flex align-items-center">
                    <label for="input-text-email" class="text-left d-block mb-2 mb-lg-0">
                        {{$fieldTitle('enter', 'email')}}
                    </label>
                </div>
                <div class="col-lg-8 col-xl-8">
                    <app-input type="email"
                               id="input-text-email"
                               :placeholder="$placeholder('email', '')"
                               v-model="userProfileInfo.email"
                               :error-message="$errorMessage(errors, 'email')"/>
                </div>
            </div>

        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-lg-3 col-xl-3 d-flex align-items-center">
                    <label class="text-left d-block mb-2 mb-lg-0">
                        {{$t('gender')}}
                    </label>
                </div>
                <div class="col-lg-8 col-xl-8" v-if="userProfileInfo.profile">
                    <app-input type="radio"
                               :list="[{id:'male',
                            value: $t('male')},
                           {id:'female',
                            value:  $t('female')}]"
                               v-model="userProfileInfo.profile.gender"
                               :error-message="$errorMessage(errors, 'gender')"/>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-lg-3 col-xl-3 d-flex align-items-center">
                    <label for="input-text-contact" class="text-left d-block mb-2 mb-lg-0">
                        {{$fieldTitle('contact', 'number')}}
                    </label>
                </div>
                <div class="col-lg-8 col-xl-8" v-if="userProfileInfo.profile">
                    <app-input type="number"
                               id="input-text-contact"
                               :placeholder="$placeholder('contact', '')"
                               v-model="userProfileInfo.profile.contact"
                               :error-message="$errorMessage(errors, 'contact')"/>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-lg-3 col-xl-3 d-flex align-items-center">
                    <label for="input-text-address" class="text-left d-block mb-2 mb-lg-0">
                        {{$t('address')}}
                    </label>
                </div>
                <div class="col-lg-8 col-xl-8" v-if="userProfileInfo.profile">
                    <app-input type="text"
                               id="input-text-address"
                               :placeholder="$placeholder('address', '')"
                               v-model="userProfileInfo.profile.address"
                               :error-message="$errorMessage(errors, 'address')"
                    />
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-lg-3 col-xl-3 d-flex align-items-center">
                    <label class="text-left d-block mb-2 mb-lg-0">
                        {{$fieldTitle('date_of_birth')}}
                    </label>
                </div>
                <div class="col-lg-8 col-xl-8" v-if="userProfileInfo.profile">
                    <app-input type="date"
                               v-model="userProfileInfo.profile.date_of_birth"
                               :placeholder="$placeholder('date_of_birth', '')"
                               :error-message="$errorMessage(errors, 'date_of_birth')"
                    />
                </div>
            </div>
        </div>

        <div class="form-group mt-5 mb-0">
            <app-submit-button @click="submitData" :title="$t('save')" :loading="loading"/>
        </div>
    </form>
    <app-pre-loader class="p-primary" v-else />
</template>

<script>
    import moment from 'moment'
    import FormHelperMixins from "../../../Mixins/Global/FormHelperMixins";

    export default {
        name: "ProfilePersonalInfo",
        mixins: [FormHelperMixins],
        data() {
            return {
                userProfileInfo: {}
            }
        },
        methods: {
            submitData() {
                let profile = this.userProfileInfo;
                this.loading = true;
                profile.gender = this.userProfileInfo.profile.gender;
                profile.contact = this.userProfileInfo.profile.contact;
                profile.address = this.userProfileInfo.profile.address;
                profile.date_of_birth = this.userProfileInfo.profile.date_of_birth ? moment(this.userProfileInfo.profile.date_of_birth).format('YYYY-MM-DD') : '';
                this.save(profile);
            },
            afterSuccess(response) {
                this.loading = false;
                this.$toastr.s('', response.data.message);
                this.scrollToTop(false)
                setTimeout(() => location.reload())
            },

            cancelUser() {
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
            this.preloader = true
        },

        watch: {
            userInfo: {
                handler: function (user) {
                    if (user) this.preloader = false
                    this.userProfileInfo = {
                        ...user,
                        profile: {
                            ...user.profile,
                            date_of_birth: user.profile && user.profile.date_of_birth ? new Date(user.profile.date_of_birth) : ''
                        }
                    };
                },
                deep: true
            }
        }

    }
</script>


