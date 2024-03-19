<template>
    <div class="unsubscriber-page">
        <div class="container-fluid h-100">
            <div class="main-panel h-100">
                <div class="content-wrapper p-0">
                    <div class="row justify-content-center align-items-center h-100">
                        <div class="col-12 col-sm-8 col-md-6 col-lg-6 col-xl-6">
                            <div class="card border-0 p-primary">
                                <div class="text-center">
                                    <p class="mb-0">{{ subscriber.full_name }}</p>
                                    <p class="mb-5 text-secondary font-size-90">{{ subscriber.email }}</p>

                                    <h5>{{ $t('successfully_unsubscribed') }}</h5>
                                    <p class="text-secondary">{{ $t("We'll stop sending you this type of email") }}</p>
                                   <!-- <p class="text-secondary mb-0">Did you unsubscribe by mistake? <a href="#">Resubscribe</a></p>
                                    <p class="text-secondary mb-0">You can also manage all notifications from Gain email marketing in your <a href="#">email setting</a></p>

                                    <a href="#" class="btn btn-primary mt-5">Back to gmail</a>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "UnSubscribe",
        props: {
            subscriber: {}
        }
    }
</script>

<style scoped>
    html, body {
        height: 100%;
        background-color: #fff !important;
    }
</style>
