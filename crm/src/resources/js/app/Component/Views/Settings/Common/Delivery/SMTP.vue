<template>
    <div>
        <app-form-group
            :label="$t('host')"
            page="page"
            v-model="delivery.smtp_host"
            :error-message="$errorMessage(errors, 'smtp_host')"
            :placeholder="$placeholder('host')"
            :disabled="alias === 'brand'"
        />

        <app-form-group
            type="number"
            :label="$t('port')"
            page="page"
            v-model="delivery.smtp_port"
            :error-message="$errorMessage(errors, 'smtp_port')"
            :placeholder="$placeholder('port')"
            :disabled="alias === 'brand'"
        />

        <app-form-group
            :label="$t('encryption')"
            page="page"
            v-model="delivery.smtp_encryption"
            :error-message="$errorMessage(errors, 'smtp_encryption')"
            :placeholder="$placeholder('encryption')"
            :disabled="alias === 'brand'"
        />

        <app-form-group
            :label="$t('hourly_quota')"
            page="page"
            type="number"
            min="0"
            v-model="delivery.smtp_hourly_quota"
            :error-message="$errorMessage(errors, 'smtp_hourly_quota')"
            :placeholder="$placeholder('hourly_quota')"
            :disabled="alias === 'brand'"
        />

        <app-form-group
            :label="$t('daily_quota')"
            type="number"
            min="0"
            page="page"
            v-model="delivery.smtp_daily_quota"
            :error-message="$errorMessage(errors, 'smtp_daily_quota')"
            :placeholder="$placeholder('daily_quota')"
            :disabled="alias === 'brand'"
        />

        <app-form-group
            :label="$t('monthly_quota')"
            page="page"
            min="0"
            type="number"
            v-model="delivery.smtp_monthly_quota"
            :error-message="$errorMessage(errors, 'smtp_monthly_quota')"
            :placeholder="$placeholder('monthly_quota')"
            :disabled="alias === 'brand'"
        />

        <app-form-group
            :label="$fieldLabel('user', 'name')"
            page="page"
            v-model="delivery.smtp_user_name"
            :error-message="$errorMessage(errors, 'smtp_user_name')"
            :placeholder="$placeholder('user', 'name')"
            :disabled="alias === 'brand'"
        />

        <app-form-group
            type="password"
            :label="$t('password')"
            page="page"
            v-model="delivery.smtp_password"
            :error-message="$errorMessage(errors, 'smtp_password')"
            :placeholder="$placeholder('password')"
            :disabled="alias === 'brand'"
        />
    </div>
</template>

<script>
    import DeliveryMixin from "./DeliveryMixin";
    export default {
        mixins: [DeliveryMixin],
        name: "SMTP",
        data() {
            return {
                delivery: {}
            }
        }
    }
</script>

<style scoped>
</style>