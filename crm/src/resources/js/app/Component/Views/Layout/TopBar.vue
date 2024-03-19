<template>
    <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="navbar-menu-wrapper d-flex align-items-stretch">
            <a href="" class="align-self-center d-lg-none pl-0 navbar-brand">
                <img :src="logoIconSrc" alt="logo"/>
            </a>
            <button class="navbar-toggler align-self-center d-none d-lg-block pl-0"
                    type="button">
                <span v-if="leftMenuType === 'normal'" :key="'align-left'" @click.prevent="togglingLeftMenu('active-icon-only-menu')">
                    <app-icon :name="'align-left'"/>
                </span>
                <span v-else-if="leftMenuType === 'icon-only'" :key="'align-center'" @click.prevent="togglingLeftMenu('active-floating-menu')">
                    <app-icon :name="'align-center'"/>
                </span>
                <span v-else-if="leftMenuType === 'floating'" :key="'align-justify'" @click.prevent="togglingLeftMenu('active-normal-menu')">
                    <app-icon :name="'align-justify'"/>
                </span>
            </button>
            <button class="navbar-toggler align-self-center d-lg-none pl-0"
                    type="button"
                    data-toggle="offcanvas"
                    @click="sidebarOffcanvas">
                <app-icon :name="'align-left'"/>
            </button>
            <button class="navbar-toggler align-self-center pl-0"
                    type="button"
                    @click.prevent="toggleDarkMode">
                <span v-if="darkMode" :key="'sun'">
                    <app-icon :name="'sun'"/>
                </span>
                <span v-else :key="'moon'">
                    <app-icon :name="'moon'"/>
                </span>
            </button>
            <button class="navbar-toggler pl-0" style="box-shadow: none">
                <app-brands-dropdown v-if="brand"/>
            </button>
            <ul class="navbar-nav navbar-nav-right ml-auto">
                <span class="topbar-divider d-none d-lg-block"/>
                <li class="nav-item d-none d-lg-block">
                    <a v-if="fullScreen" class="d-flex align-items-center nav-link" href="#" :key="'maximize'" @click="fullscreen">
                        <app-icon :name="'minimize'"/>
                    </a>
                    <a v-else class="d-flex align-items-center nav-link" href="#" :key="'minimize'" @click="fullscreen">
                        <app-icon :name="'maximize'"/>
                    </a>
                </li>

                <span class="topbar-divider d-none d-sm-block"/>
                <app-navbar-language-dropdown :selected-language="userLanguage" :data="languages" class="d-none d-sm-block"/>

                <span class="topbar-divider d-none d-sm-block"/>
                <app-navbar-notification-dropdown :data="notificationData"/>

                <span class="topbar-divider d-none d-sm-block"/>
                <app-navbar-profile-dropdown :user="user" :data="profileData"/>
            </ul>
        </div>
    </nav>
</template>

<script>
    import ThemeMixin from "../../../Mixins/Global/ThemeMixin";
    import CoreLibrary from "../../../../core/helpers/CoreLibrary";
    import TopBarMixin from "./Mixins/TopBarMixin";
    import { mapActions, mapState } from 'vuex'
    import {urlGenerator} from "../../../Helpers/AxiosHelper";

    export default {
        name: "Navbar",
        mixins: [TopBarMixin, ThemeMixin],
        extends: CoreLibrary,
        props: {
            profileData: {},
            logoIconSrc: {
                type: String,
                default: '/images/logo.png'
            },
        },
        components: {
            'app-brands-dropdown': require('./Components/BrandsDropDown').default
        },
        data() {
            return {
                leftMenuType: 'normal',
                darkMode: false,
                fullScreen: false,
                settings: window.settings,
                user: {},
                notificationData: [],
            }
        },
        computed: {
            ...mapState({
                languages: state => state.additional.languages,
                brand: state => state.brand
            }),
            userLanguage() {
                return window.appLanguage.toUpperCase();
            }
        },
        methods: {
            ...mapActions([
               'getLanguages'
            ]),
            setUser() {
                this.user = {
                    full_name: this.$optional(window.user, 'full_name'),
                    img: this.$optional(window.user, 'profile_picture', 'full_url') ||
                        urlGenerator('/images/avatar.png'),
                    status: this.$t('online'),
                    role: window.user.roles && window.user.roles.length ? window.user.roles[0].name: ''
                };
            }
        },
        created() {
            this.getLanguages();
        },
        mounted() {
            this.setUser()
        }
    }
</script>
