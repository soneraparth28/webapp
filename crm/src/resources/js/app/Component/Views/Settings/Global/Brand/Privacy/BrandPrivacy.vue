<template>
    <div class="container-fluid p-0">
        <div class="row">
            <form method="post" ref="form" :data-url="brand_privacy" >
                <div class="col-12">
                    <div class="form-group">
                        <app-input
                            type="checkbox"
                            v-model="privacy"
                            :list="privacy_list"
                            :error-message="$errorMessage(errors, 'context')"
                        />
                    </div>

                    <div class="form-group mb-0">
                        <app-submit-button :loading="loading" @click="savePrivacy" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import FormHelperMixins from "../../../../../../Mixins/Global/FormHelperMixins";
    import {brand_privacy} from '../../../../../../config/apiUrl'

    export default {
        mixins: [FormHelperMixins],
        name: "BrandPrivacy",
        data() {
            return {
                privacy: [],
                privacy_list: [
                    {
                        id: 'track_open_in_campaigns',
                        value: this.$t('track_campaigns_message', {
                            'action': this.$t('opens').toLowerCase(),
                            'subject': this.$t('campaigns').toLowerCase()
                        })
                    },
                    {
                        id: 'track_clicks_in_your_campaigns',
                        value: this.$t('track_campaigns_message', {
                            'action': this.$t('clicks').toLowerCase(),
                            'subject': this.$t('campaigns').toLowerCase()
                        })
                    },
                    // {
                    //     id: 'track_location_in_your_campaigns',
                    //     value: this.$t('track_campaigns_message', {
                    //         'action': this.$t('location').toLowerCase(),
                    //         'subject': this.$t('campaigns').toLowerCase()
                    //     })
                    // }
                ],
                brand_privacy
            }
        },
        methods: {
            savePrivacy() {
                this.loading = false;
                this.message = '';
                this.privacy.context = 'privacy';
                this.save(this.privacy);
            },
            afterSuccess({data}) {
                this.scrollToTop(false);
                this.toastAndReload(data.message)
            }
        },
        mounted() {
            this.axiosGet(brand_privacy).then(res => {
                this.privacy = Object.keys(res.data);
            })
        }
    }
</script>

<style scoped>

</style>
