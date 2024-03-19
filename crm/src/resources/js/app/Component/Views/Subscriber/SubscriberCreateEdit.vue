<template>
    <div class="content-wrapper">
        <app-page-top-section
            :title="$fieldTitle(selectedUrl ? 'edit' : 'add', 'subscriber')"
            :directory="`${$t('subscribers')} | <a href='${urlGenerator(subscribers_front_end)}'>${$t('back_to',
            {destination: $allLabel('subscribers')} )}</a>`"
            :hide-button="true"
            icon="message-circle"
        />

        <create-edit-form
            :loading="loading"
            :submit-data="submitData"
            @cancel="redirectToList"
            v-if="!preloader"
        >
            <div class="form-group">
                <div class="row">
                    <div class="col-xl-2 col-md-3 col-sm-12 col-12 pt-2">
                        <label>{{ $t('email') }}</label>
                    </div>
                    <div class="col-xl-7 col-md-9 col-sm-12 col-xs-12">
                        <app-input
                            type="email"
                            :placeholder="$placeholder('email')"
                            v-model="formData.email"
                            :error-message="$errorMessage(errors, 'email')"
                        />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-xl-2 col-md-3 col-sm-12 col-12 pt-2">
                        <label>{{ $t('first_name') }}</label>
                    </div>
                    <div class="col-xl-7 col-md-9 col-sm-12 col-xs-12">
                        <app-input
                            type="text"
                            :placeholder="$placeholder('first_name')"
                            v-model="formData.first_name"
                            :error-message="$errorMessage(errors, 'first_name')"
                        />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-xl-2 col-md-3 col-sm-12 col-12 pt-2">
                        <label>{{ $t('last_name') }}</label>
                    </div>
                    <div class="col-xl-7 col-md-9 col-sm-12 col-xs-12">
                        <app-input
                            type="text"
                            :placeholder="$placeholder('last_name')"
                            v-model="formData.last_name"
                            :error-message="$errorMessage(errors, 'last_name')"
                        />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-xl-2 col-md-3 col-sm-12 col-12 pt-2">
                        <label>{{ $t('lists_label') }}</label>
                    </div>
                    <div class="col-xl-7 col-md-9 col-sm-12 col-xs-12">
                        <app-input
                            type="multi-select"
                            :list="list"
                            v-model="lists"
                            list-value-field='name'
                            :isAnimatedDropdown="true"
                        />
                    </div>
                </div>
            </div>

            <div class="form-group" v-if="!loader" v-for="field in customFields">
                <div class="row">
                    <div class="col-xl-2 col-md-3 col-sm-12 col-12 pt-2">
                        <label>{{field.name}}</label>
                    </div>

                    <div class="col-xl-7 col-md-9 col-sm-12 col-xs-12"
                         v-if="['text', 'textarea'].includes(field.type)"
                    >
                        <app-input
                            :type="field.type"
                            v-model="customFieldValue[field.name]"
                        />
                    </div>
                    <template v-else>
                        <div :class="`col-xl-7 col-md-9 col-sm-12 col-xs-12 ${field.type === 'radio' ? 'pt-2' : ''}`">
                            <app-input
                                :radio-checkbox-name="field.name"
                                :list="generateInputList(field)"
                                :type="field.type"
                                v-model="customFieldValue[field.name]"
                            />
                        </div>
                    </template>
                </div>
            </div>
            <div class="form-group" v-else>
                <app-pre-loader />
            </div>
        </create-edit-form>
        <app-pre-loader class="p-primary" v-else />
    </div>
</template>

<script>
    import FormHelperMixins from "../../../Mixins/Global/FormHelperMixins";
    import CreateEditForm from "../../Helper/Card/CreateEditForm";
    import {subscriber_store, subscribers_front_end} from "../../../config/apiUrl";
    import {mapGetters, mapState} from "vuex";
    import Templates from "../Template/Templates";
    import {isValidDate} from "../../../Helpers/helpers";
    import {urlGenerator} from "../../../Helpers/AxiosHelper";

    export default {
        name: "SubscriberCreateEdit",
        components: {Templates, CreateEditForm},
        mixins: [FormHelperMixins],

        data() {
            return {
                url: subscriber_store,
                formData: {
                    list: [],
                },
                customFieldValue: {},
                customFields: [],
                lists: [],
                subscribers_front_end
            }
        },
        methods: {
            submitData() {
                this.loading = true;
                this.message =  '';
                this.errors = [];
                const formData = {...this.formData, custom_fields: {...this.customFieldValue}, list: this.lists}
                this.submitFromFixin(
                    this.selectedUrl ? 'patch' : 'post',
                    this.url,
                    formData
                );
            },
            afterSuccess({data}) {
                if (!this.selectedUrl) {
                    this.formData = { list: [] }
                    this.lists = [];
                    this.customFieldValue = {}
                    this.scrollToTop(false);
                }
                this.toastAndReload(data.message);
                if (this.selectedUrl) {
                  this.redirectToList();
                }
            },
            afterSuccessFromGetEditData(response) {
                this.preloader = false;
                this.formData = response.data;
                this.lists = this.collection(response.data.lists).pluck();
                response.data.custom_fields.forEach(field => {
                    if (field.value){
                        this.customFieldValue[field.custom_field.name] = isValidDate(field.value) ? new Date(field.value) : field.value;
                    }
                })
                this.url = this.selectedUrl;
            },
            redirectToList() {
                window.location = urlGenerator(subscribers_front_end);
            },
            generateInputList({meta}) {
               if (meta) {
                   return Array.from(new Set(meta.split(','))).map(m => {
                       return { id: m, value: m }
                   })
               }
            }
        },
        computed: {
            ...mapState({
                list: state => state.lists.lists,
                loader: state => state.loading,
            }),
            ...mapGetters([
                'getFormattedCustomFields'
            ])

        },
        created() {
            this.$store.dispatch('getSelectableLists');
            this.$store.dispatch('getSubscriberCustomFields');
        },
        watch: {
            getFormattedCustomFields: {
                handler: function (fields) {
                    if (Object.keys(fields).length) {
                        this.customFields = [...fields]
                    }
                },
                immediate: true
            },
            loader: {
              handler: function (loader) {
                this.preloader = loader;
              },
              immediate: true
            }
        }
    }
</script>
