<template>
    <div>
        <app-form-group
            page="page"
            :label="$t('domain_name')"
            v-model="delivery.domain_name"
            :placeholder="$placeholder('domain_name')"
            :error-message="$errorMessage(errors, 'domain_name')"
            :disabled="alias === 'brand'"
        />

        <app-form-group
            page="page"
            type="text"
            :label="$t('api_key')"
            v-model="delivery.api_key"
            :placeholder="$placeholder('api_key')"
            :error-message="$errorMessage(errors, 'api_key')"
            :disabled="alias === 'brand'"
        />

        <app-form-group
            page="page"
            :label="$t('webhook_signing_key')"
            type="text"
            v-model="delivery.webhook_key"
            :placeholder="$placeholder('webhook_signing_key')"
            :error-message="$errorMessage(errors, 'webhook_key')"
            :disabled="alias === 'brand'"
        />
    </div>

</template>

<script>
    import DeliveryMixin from "./DeliveryMixin";
    export default {
        name: "Mailgun",
        mixins: [DeliveryMixin],
        data() {
            return {
                delivery: {

                }
            }
        }
    }
</script>

<style scoped>

</style>