<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$fieldTitle('users_roles')" icon="user-check">
            <app-default-button btn-class="btn btn-info mr-2"
                                :title="$fieldTitle('invite', 'user', true)"
                                @click="isInviterOpenModalActive = true"
                                v-if="$can('invite_user')"
            />
            <app-default-button
                :title="$addLabel('role')"
                @click="isRoleModalActive = true"
                v-if="$can('create_roles')"
            />
        </app-page-top-section>

        <!--Users And Roles Pages Started Here....-->
        <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-5 col-xl-5">
                <app-users
                    :alias="alias"
                    :roleList="roleList"
                />
            </div>

            <div class="col-12 col-sm-12 col-md-12 col-lg-7 col-xl-7">
                <app-roles
                    :alias="alias"
                    @selectedUrl="openEditModal"
                    v-model="isRoleModalActive"
                />
            </div>
        </div>
        <app-user-invite-modal
            :alias="alias"
            v-if="isInviterOpenModalActive"
            v-model="isInviterOpenModalActive"
            :roles="roleList"
        />
    </div>
</template>

<script>

    import {axiosGet} from "../../../Helpers/AxiosHelper";
    import {brand_roles_select, roles} from "../../../config/apiUrl";

    export default {
        props: {
            alias: {
                require: true,
                default: 'app'
            }
        },
        components: {
            'app-users': require('./Users').default,
            'app-roles': require('./Roles').default,
        },
        data() {
            return {
                isInviterOpenModalActive: false,
                isRoleModalActive: false,
                selectedUrl: '',
                roleList: []
            }
        },
        methods: {
            openEditModal(url) {
                this.selectedUrl = url;
                this.isRoleModalActive = true
            },
            getAllRoles() {
                axiosGet(this.getRolesURL)
                    .then(({data}) => this.roleList = data)
            }
        },
        computed: {
            getRolesURL() {
                return {
                    brand: brand_roles_select,
                    app: roles,
                }[this.alias]
            }
        },
        mounted() {
            this.getAllRoles();
            this.$hub.$on('rolesAffected', () => this.getAllRoles())
        }
    }
</script>
