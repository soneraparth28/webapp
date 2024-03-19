<template>
    <div :class="`col-6 col-sm-6 col-md-${col} col-lg-${col} col-xl-${col}`">
        <div class="card card-with-shadow border border-0 mb-primary py-primary">
            <div class="circular-progress-bar">
                <div class="progress" :data-percentage="parseInt(rate)">
                    <span class="progress-left">
                        <span class="progress-bar"></span>
                    </span>
                    <span class="progress-right">
                        <span class="progress-bar"></span>
                    </span>
                    <div class="progress-value">
                        <h6 class="m-0" v-if="rate">
                            {{ rate.replace('.', decimal_separator) }}%
                        </h6>
                    </div>
                </div>
                <small class="mt-3 text-center d-block">{{ title }}</small>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "EmailRateProgressbar",
        props: {
            rate: {
                required: true,
                default: function () {
                    return 0;
                }
            },
            title: {
                required: true
            },
            col: {
                default: 2
            }
        },
        computed: {
            decimal_separator(){
                return this.$store.state.settings.decimal_separator
            }
        }
    }
</script>

<style scoped>

</style>
