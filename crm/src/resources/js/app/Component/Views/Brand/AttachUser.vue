<template>
    <modal id="attach-user" v-model="showModal" :title="`${$fieldTitle('attach', 'user')} ${$t('to')} ${brand.name}`" @submit="submitData">
        <div class="row">
            <div class="col-12" v-if="!loader && roles.length">
                <div class="form-group">
                    <label>{{ $fieldTitle('user') }}</label>
                    <app-input type="multi-select"
                               v-model="attachUser.users"
                               :list="users"
                               list-value-field="full_name"
                               :error-message="$errorMessage(errors, 'users')"
                               :isAnimatedDropdown="true"
                    />
                </div>
                <div class="form-group">
                    <label>{{ $fieldTitle('roles') }}</label>
                    <app-input type="multi-select"
                               v-model="attachUser.roles"
                               :list="roles"
                               list-value-field="name"
                               :error-message="$errorMessage(errors, 'roles')"
                               :isAnimatedDropdown="true"
                    />
                </div>
            </div>
            <div class="col-12" v-else>
                <loader v-if="loader"></loader>
                <template v-else>
                    <h5>
                        {{
                        `
                        ${$t('not_found_message', { subject: $t('role').toLocaleLowerCase() })} ${$t('full_stop_mark')}
                        `
                        }}
                    </h5>
                    <a href="/admin/users/list">
                        {{ ` ${$fieldTitle('add', 'new')} ${$t('role').toLocaleLowerCase()} ${$t('to')} ${$t('the')}
                        ${$t('brand')}` }}
                    </a>
                </template>
            </div>
        </div>
    </modal>
</template>

<script>
    import {FormMixin} from "../../../../core/mixins/form/FormMixin";
    import FormHelperMixins from "../../../Mixins/Global/FormHelperMixins";
    import {mapState} from 'vuex'
    import Loader from "../../Helper/Loader/Loader";
    import ModalMixin from "../../../Mixins/Global/ModalMixin";

    export default {
        name: "AttachUser",
        components: {Loader},
        mixins: [FormHelperMixins, ModalMixin],
        props: {
            brand: {
                required: true
            },
            value: {
                required: true,
                type: Boolean
            }
        },
        data() {
            return {
               attachUser: {
                   roles: [],
                   users: []
               }
            }
        },
        methods: {
            submitData() {
                this.loading = true;
                this.message = null;
                this.errors = {};
                this.submitFromFixin('post', `admin/app/brands/${this.brand.id}/attach-users`, this.attachUser)
            },
            afterSuccess({data}) {
                this.toastAndReload(data.message)
                this.$emit('input', false);
            },
        },
        computed: {
            ...mapState({
                loader: state => state.loading,
                users: state => state.user.users,
                roles: state => state.role.roles
            })
        },
        watch: {
            value: {
                handler: function (value) {
                    if (value)
                        this.$store.dispatch('getRoles', this.brand.id);
                },
                immediate: true
            }
        },
        created() {
            this.$store.dispatch('getUsers');
        }
    }
</script>

<style scoped>

</style>
