<template>
    <div>
        <app-form-group
                :label="$fieldTitle('api_region')"
                page="page"
                type="text"
                v-model="delivery.api_region"
                :placeholder="$placeholder('api_region')"
                :error-message="$errorMessage(errors, 'api_region')"
                :disabled="alias === 'brand'"
        />

        <app-form-group
                :label="$t('access_key_id')"
                page="page"
                type="text"
                v-model="delivery.access_key_id"
                :placeholder="$placeholder('access_key_id')"
                :error-message="$errorMessage(errors, 'access_key_id')"
                :disabled="alias === 'brand'"
        />
        <app-form-group
                :label=" $t('secret_access_key')"
                page="page"
                type="text"
                v-model="delivery.secret_access_key"
                :placeholder="$placeholder('secret_access_key')"
                :error-message="$errorMessage(errors, 'secret_access_key')"
                :disabled="alias === 'brand'"
        />

        <app-form-group
                :label="$t('configuration_set')"
                page="page"
                type="text"
                v-model="delivery.configuration_set"
                :placeholder="$placeholder('configuration_set')"
                :error-message="$errorMessage(errors, 'configuration_set')"
                :disabled="alias === 'brand'"
        />
    </div>

</template>

<script>
    import DeliveryMixin from "./DeliveryMixin";

    export default {
        mixins: [DeliveryMixin],
        name: "AmazonSES",
        data() {
            return {
                delivery: {}
            }
        }
    }
</script>

<style scoped>

</style>