<template>
    <modal id="brand-modal"
           v-model="showModal"
           :title="generateModalTitle('brand')"
           @submit="submitData"
           :loading="loading"
           :preloader="preloader"
    >
        <form :data-url='this.selectedUrl ? `admin/app/brands/${this.formData.id}` : `admin/app/brands`' ref="form">

            <app-form-group
                :label="$t('brand')"
                :placeholder="$placeholder('brand', 'name')"
                :required="true"
                v-model="formData.name"
                @focusout="getSuggestions()"
                :error-message="$errorMessage(errors, 'name')"
            />

            <app-form-group
                :label="$t('short_name')"
                :error-message="$errorMessage(errors, 'short_name')"
                :placeholder="$placeholder('short_name')"
                :required="true"
                v-model="formData.short_name"
                @focusout="getSuggestions()"
                @focusin="getSuggestions()"
                :disabled="Boolean(selectedUrl) && formData.short_name !== 'default-brand'"
            >
                <template #suggestion>
                    <p class="text-muted">{{ $t('brand_short_name_suggestion') }}</p>
<!--                    <div v-if="isVisibleSuggestionBlock && nameLength" class="suggestion-block">-->
<!--                    <span-->
<!--                        class="badge badge-pill default-font-color shadow mr-3 my-2 suggestion"-->
<!--                        v-for="name in shortNameSuggestions"-->
<!--                        @click="selectShortName(name)"-->
<!--                    >-->
<!--                        {{name}}-->
<!--                    </span>-->
<!--                    </div>-->
                </template>
            </app-form-group>

            <app-form-group
                :label="$fieldLabel('brand_group')"
                :list="brandGroups"
                :placeholder="$placeholder('brand_group', 'name')"
                type="select"
                v-model="formData.brand_group_id"
                list-value-field="name"

            />
        </form>
    </modal>
</template>

<script>
    import FormHelperMixins from "../../../Mixins/Global/FormHelperMixins";
    import Message from "../../Helper/Message/Message";
    import {generateShortNames} from "../../../config/apiUrl";
    import ModalMixin from "../../../Mixins/Global/ModalMixin";
    import {addChooseInSelectArray} from "../../../Helpers/helpers";

    export default {
        name: "BrandAddEditModal",
        components: {Message},
        mixins: [FormHelperMixins, ModalMixin],
        data() {
            return {
                formData: {
                    name: '',
                    short_name: '',
                    brand_group_id: ''
                },
                brandGroups: [],
                shortNameSuggestions: [],
                isVisibleSuggestionBlock: false,
            }
        },
        created() {
            this.getBrandGroup()
        },
        methods: {
            submitData() {
                this.fieldStatus.isSubmit = true;
                this.loading = true;
                this.message = '';
                this.errors = {};
                if(!this.formData.short_name) {
                    this.formData.short_name = this.shortNameSuggestions[0];
                }
                this.save(this.formData);
            },
            afterSuccess({data}) {
                this.toastAndReload(data.message, 'brand-table')
                this.formData = {};
                $('#brand-modal').modal('hide');
            },
            afterSuccessFromGetEditData(response) {
                this.preloader = false;
                this.formData = response.data;
                this.formData.brand_group_id = response.data.brand_group_id ? response.data.brand_group_id : ''
            },
            getBrandGroup() {
                this.axiosGet(`admin/app/brand-groups?per_page=100`).then(response => {
                    this.brandGroups = addChooseInSelectArray(response.data.data, 'name', 'group');
                })
            },
            getSuggestions() {
                if (this.nameLength && !this.selectedUrl) {
                    this.isVisibleSuggestionBlock = true
                    this.axiosGet(`${generateShortNames}?name=${this.formData.name}`).then(({data}) => {
                        this.shortNameSuggestions = data
                    })
                }
            },
            selectShortName(name) {
                this.formData.short_name = name
                this.isVisibleSuggestionBlock = false
            }


        },
        computed: {
            nameLength() {
                let length = this.formData.name.length
                return (length > 2) && (length < 195);

            }
        }

    }
</script>

<style scoped>
    .suggestion {
        cursor: pointer;
    }
    .suggestion:hover {
        --default-font-color: #4466F2;
    }
</style>
