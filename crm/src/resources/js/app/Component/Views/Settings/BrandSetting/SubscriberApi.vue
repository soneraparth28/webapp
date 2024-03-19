<template>
    <div v-if="!loading">
        <note :title="$fieldTitle('subscriber', 'api', true)"
              :description="$t('subscriber_api_message')"
        />
        <div class="row mt-primary">
            <div class="col-md-12">
                <p>{{ this.$t('url') }} </p>
                <div class="row">
                    <div class="col-lg-1 pr-0">[POST]</div>
                    <div class="col-lg-10"><code>{{ api.url.store }}</code></div>
                </div>
                <div class="row">
                    <div class="col-lg-1 pr-0">[POST]</div>
                    <div class="col-lg-10"> <code>{{ api.url.update }}</code></div>
                </div>
            </div>
        </div>

        <div class="mt-primary">
            <p>{{ this.$t('api_key_message') }}</p>
            <code>{{ api.api_key }}</code>
            <button class="btn btn-sm btn-primary" @click="showConfirmation = true"><i class="feather-16" data-feather="refresh-ccw" ></i></button>
        </div>
        <div class="mt-primary">
            <p>{{ this.$t('header') }}</p>
            <code>
                {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"Content-Type": "application/json", //required<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"Accept": "application/json" <br>
                }
            </code>
        </div>
        <div class="mt-primary">
            <p>{{ this.$t('body') }}</p>
            <code>
                {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"api_key": "{{ api.api_key }}", //required<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"first_name": "John", //optional<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"last_name": "Doe", //optional<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"email": "email@example.com", //required<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"custom_fields": { //optional<br>
                <span v-for="field in fields">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"{{field.name}}": "{{field.value}}",<br></span>
                &nbsp;&nbsp;&nbsp;&nbsp;}<br>
                }
            </code>
        </div>
        <app-confirmation-modal
            v-if="showConfirmation"
            modal-id="app-confirmation-modal"
            @confirmed="getSubscriberUrl(true)"
            @cancelled="showConfirmation = false"
            :message="$t('api_regenerate_warning')"
        />
    </div>
    <app-pre-loader v-else />
</template>

<script>
    import Note from "../../../Helper/Note/Note";
    import {axiosGet} from "../../../../Helpers/AxiosHelper";
    import {subscriber_api_url} from '../../../../config/apiUrl'
    export default {
        name: "SubscriberApi",
        components: {Note},
        data() {
            return {
                api: {
                    store: '',
                    update: '',
                    custom_fields: ''
                },
                loading: true,
                showConfirmation: false
            }
        },
        methods: {
            getSubscriberUrl(regenerate = false) {
                const url = regenerate ? `${subscriber_api_url}/1` : subscriber_api_url;
                axiosGet(url).then(({data}) => {
                    this.api = data;
                }).finally(() => {
                    this.loading = false;
                })
            }
        },
        computed: {
            fields() {
                return this.api.custom_fields.map(cf => {
                    let meta = 'field_value';
                    if (cf.meta) {
                        meta = cf.meta.split(',')[0];
                    }
                    return {
                        name: cf.name,
                        value: meta
                    }
                })
            }
        },
        created() {
            this.getSubscriberUrl();
        }
    }
</script>

<style scoped>
    .feather-16{
        width: 16px;
        height: 16px;
        cursor: pointer;
    }
</style>
