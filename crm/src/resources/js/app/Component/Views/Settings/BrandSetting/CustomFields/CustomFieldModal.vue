<template>
    <modal id="custom-field-modal"
           v-model="showModal"
           :title="generateModalTitle('custom_field')"
           @submit="submitData"
           :loading="loading"
           :preloader="preloader">
        <form ref="form"
              @submit.prevent="submitData"
              :data-url='selectedUrl ? selectedUrl : brand_custom_field'>
            <app-form-group
                :label="$fieldLabel('custom_field', 'type')"
                type="select"
                v-model="formData.custom_field_type_id"
                :list="custom_field_types"
                :error-message="$errorMessage(errors, 'custom_field_type_id')"
                @input="setCustomFieldType($event)"
            />

            <app-form-group
                v-if="custom_field_type.choose || formData.meta"
                :label="$fieldTitle('options')"
                :recommendation="$t('Use comma separated values as options of the field')"
                type="textarea"
                v-model="formData.meta"
                :error-message="$errorMessage(errors, 'meta')"
                :placeholder="$placeholder('custom_field', 'options')"
            />

            <app-form-group
                :label="$t('name')"
                v-model="formData.name"
                :error-message="$errorMessage(errors, 'name')"
                :placeholder="$placeholder('custom_field', 'name')"
            />

            <app-form-group
                :label="$t('context')"
                type="select"
                v-model="formData.context"
                :list="context"
                :error-message="$errorMessage(errors, 'context')"
            />

            <div class="form-group mb-0">
                <app-input type="checkbox"
                           v-model="formData.in_list"
                           :list="[{ id: 1, value: $t('Show in the data table') }]"
                           :error-message="$errorMessage(errors, 'in_list')"/>
            </div>
        </form>
    </modal>
</template>

<script>
    import {mapState} from 'vuex'
    import FormHelperMixins from "../../../../../Mixins/Global/FormHelperMixins";
    import ModalMixin from "../../../../../Mixins/Global/ModalMixin";
    import {brand_custom_field} from "../../../../../config/apiUrl";

    export default {
        props: ['selectedUrl'],
        name: "customFiledModal",
        mixins: [FormHelperMixins, ModalMixin],
        data() {
            return {
                formData: {
                    in_list: []
                },
                custom_field_type: {context: ''},
                brand_custom_field
            }
        },
        computed: {
            ...mapState({
                custom_field_types: state => state.custom_field.custom_field_types,
            }),
            context() {
                return [
                    {id: '', disabled: true, value: this.$t('chose_a', {field: this.$t('context')})},
                    ...this.$store.getters.getFormattedConfig('context').filter(f => f.id === 'subscriber')
                ]
            },
        },
        methods: {
            submitData() {
                this.loading = true;
                this.message = '';
                const customField = {
                    ...this.formData,
                    type: this.collection(this.custom_field_types).find(this.formData.custom_field_type_id).name
                }
                customField.meta = this.formData.meta?.split(',').map(m => m.trim()).filter(e => !!e).join(',');
                customField.in_list = this.formData.in_list.reduce((sum, carry) => sum + parseInt(carry), 0) ? 1 : 0;
                this.save(customField);
            },

            afterSuccess({data}) {
                this.formData = {in_list: []};
                this.$emit('input', false)
                $('#custom-field-modal').modal('hide')
                this.toastAndReload(data.message, 'brand-custom-fields');
            },
            setCustomFieldType(custom_field_type_id) {
                this.custom_field_type = this.collection(this.custom_field_types).find(custom_field_type_id);
                this.custom_field_type.choose = ['select', 'checkbox', 'radio'].includes(this.custom_field_type.name)
            },

            afterSuccessFromGetEditData({data}) {
                this.preloader = false;
                this.formData = {
                    ...data,
                    in_list: data.in_list ? [parseInt(data.in_list)] : []
                };
            }
        },
        mounted() {
            this.$store.dispatch('getCustomFieldTypes');
            this.$store.dispatch('getConfig');
            this.formData.context = 'subscriber';
            this.formData.custom_field_type_id = 1;
        },

    }
</script>

<style scoped>

</style>
