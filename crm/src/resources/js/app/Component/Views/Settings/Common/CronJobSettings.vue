<template>

    <app-pre-loader v-if="preloader"/>
    <div v-else>
        <app-note
            class="mb-primary"
            :title="$t('note')"
            :notes="$t('cron_job_setting_warning')"
        />
        <p>{{ this.$t('cron_job_setting_suggestion') }}</p>
        <a href="https://mailer.gainhq.com/documentation/important-settings.html#scheduler-queue" target="_blank"
           class="btn btn-primary mb-primary">
            <app-icon name="alert-circle" class="size-18 mr-2"/>
            {{ this.$t('see_documentation') }}</a>

        <div class="mb-primary">
            <h4>1. For Cpanel service provider</h4>
            <p class="pb-2 border-bottom">Please copy the command below and insert into your hosted server's crontab.</p>
            <p>
                {{ $t('commands_with_php_path') }}:
            </p>
                <div v-for="(cmd,index) in cpanel_commands" :key="'cpanel'+index">
                    <command-view-with-copy :command="cmd"/>
                </div>
        </div>

        <div class="mb-primary">
            <h4>2. For other service provider</h4>
            <p class="pb-2 border-bottom">If you are not using Cpanel and the php path of your hosted server is different from
                the Cpanel php path, then please identity your server's php path and add the path in front of the command below
                and then insert into your server's crontab.</p>
            <p>
                {{ $t('commands_without_php_path') }}:
            </p>

                <div v-for="(cmd,index) in commands" :key="'other'+index">
                    <command-view-with-copy :command="cmd"/>
                </div>

        </div>

        <div class="alert alert-warning text-dark">
            If you find any problem in running jobs please check your php.ini/php extension configuration.<br>
            Make sure there are no function that are called by the queue driver,
            such as, <kbd>proc_open</kbd>, <kbd>pcntl_alarm</kbd>, <kbd>pcntl_async_signals</kbd>,
            <kbd>pcntl_signal</kbd> in the <code>disable_functions</code>.<br>
            If there any you’ll need to remove/enable those functions. Or you can contact with your hosting service
            provider.
        </div>
    </div>

</template>

<script>

import {axiosGet} from "../../../../Helpers/AxiosHelper";
import {cron_job_settings} from "../../../../config/apiUrl";
import CommandViewWithCopy from "./CommandViewWithCopy";

export default {
    name: "CronJobSettings",
    components: {CommandViewWithCopy},
    data() {
        return {
            commands: [],
            cpanel_commands: [],
            preloader: false,
            isCopied: false,
            isCmdCopied: false,
        }
    },
    methods: {
        getCronJobSettings() {
            this.preloader = true;
            axiosGet(cron_job_settings).then(({data}) => {
                this.commands = data.commands;
                this.cpanel_commands = data.cpanel_commands;
            }).finally(() => {
                this.preloader = false;
            })
        },
        copy(id) {
            let copyText = document.getElementById(id);
            let input = document.createElement("textarea");
            input.value = copyText.textContent;
            document.body.appendChild(input);
            input.select();
            document.execCommand("Copy");
            input.remove();
            id === 'with_php_cmd' ?  this.isCopied = true : this.isCmdCopied = true;
        },
        afterCopied(id) {
            setTimeout(() => {
                id === 'with_php_cmd' ?  this.isCopied = false : this.isCmdCopied = false;
            }, 1000)
        }
    },
    created() {
        this.getCronJobSettings();
    }

}
</script>

<style scoped>

</style>