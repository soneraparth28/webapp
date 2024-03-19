<template>
    <div class="card card-with-shadow dashboard-widgets dashboard-icon-widget p-primary border-0">
        <div class="icon-wrapper">
            <app-icon :name="icon"/>
        </div>
        <div class="widget-data">
            <loader v-if="count < 0" />
            <h6 v-else>{{ count }}</h6>
            <p>{{ title }}</p>
        </div>
    </div>
</template>

<script>
    import Loader from "../Loader/Loader";
    export default {
        name: "Counter",
        components: {Loader},
        props: {
            title: {
                required: false
            },
            count: {
                default: function () {
                    return 0;
                }
            },
            icon: {
                required: true,
                default: 'sun'
            }
        }
    }
</script>

<style scoped>

</style>
