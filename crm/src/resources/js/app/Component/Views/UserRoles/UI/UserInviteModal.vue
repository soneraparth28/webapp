<template>
    <modal id="user-invite"
           v-model="showModal"
           :title="$fieldTitle('invite', 'user', true)"
           @submit="submitData"
           :btn-label="$t('invite')"
           :scrollable="false"
           :loading="loading"
           :preloader="preloader"
    >
        <form v-if="hasDeliverySetting" ref="form" :data-url='submitURL'>
            <div class="form-group">
                <label>{{ $t('email') }}</label>
                <app-input
                    type="email"
                    :placeholder="$placeholder('email', 'address')"
                    v-model="formData.email"
                    :required="true"
                    :error-message="$errorMessage(errors, 'email')"
                />
            </div>

            <div class="form-group">
                <label>{{ $t('roles') }}</label>
                <app-input
                    type="search-select"
                    :list="roles"
                    :placeholder="$t('chose_a_role')"
                    v-model="formData.roles"
                    :error-message="$errorMessage(errors, 'roles')"
                    list-value-field="name"
                    :isAnimatedDropdown="true"
                />
            </div>
        </form>
        <app-note v-else
                  :title="$t('no_delivery_settings_found')"
                  :notes="`${alias === 'app' ?
                  `<li>${$t('no_delivery_settings_warning', {
                      location: `<a href='${urlGenerator(mail_settings_view)}'>${$t('here')}</a>`
                  })}</li>` : `<li>${$t('no_delivery_settings_warning', {
                      location: `<a href='${urlGenerator(brand_mail_settings_view)}'>${$t('here')}</a>`
                  })}</li>`}
                  <li>${$t('cron_job_settings_warning', {
                    documentation: `<a href='https://mailer.gainhq.com/documentation/important-settings.html#schedulerAndQueue' target='_blank'>${this.$t('documentation')}</a>`
                  })}</li>`"
                  content-type="html"
        />
        <template v-if="!hasDeliverySetting" slot="footer">
            <button type="button"
                    class="btn btn-secondary"
                    data-dismiss="modal">
                {{ $t('close') }}
            </button>
        </template>
    </modal>
</template>

<script>
    import FormHelperMixins from "../../../../Mixins/Global/FormHelperMixins";
    import ModalMixin from "../../../../Mixins/Global/ModalMixin";
    import {
        brand_mail_settings_view,
        brand_user_invite,
        check_brand_mail_settings,
        check_mail_settings,
        mail_settings_view
    } from "../../../../config/apiUrl";
    import {axiosGet} from "../../../../Helpers/AxiosHelper";

    export default {
        name: "UserInviteModal",
        mixins: [FormHelperMixins, ModalMixin],
        props: ['roles', 'alias'],
        data() {
            return {
                formData: {
                    roles: []
                },
                loading: false,
                hasDeliverySetting: false,
                mail_settings_view,
                brand_mail_settings_view
            }
        },
        methods: {
            submitData() {
                this.fieldStatus.isSubmit = true;
                this.loading = true;
                this.message = '';
                this.errors = {};
                this.formData.roles = [Number(this.formData.roles)]
                this.save(this.formData);
            },
            afterSuccess({data}) {
                this.toastAndReload(data.message, 'user-table');
                $("#user-invite").modal('hide')
            },

            afterSuccessFromGetEditData({data}) {
                this.formData = data;
            },
            checkDeliverySetting(){
                this.preloader = true;
                axiosGet(this.alias === 'brand' ? check_brand_mail_settings : check_mail_settings)
                    .then(({data}) => {
                        if (this.alias === 'app' ? Number(data.app) : Number(data.app) || Number(data.brand)){
                           this.hasDeliverySetting = true;
                        }
                    }).finally(()=>{
                    this.preloader = false;
                })
            }
        },
        computed: {
            submitURL() {
                return this.alias === 'brand' ? brand_user_invite : `admin/auth/users/invite-user`;
            }
        },
        created() {
            this.checkDeliverySetting();
        }


    }
</script>

