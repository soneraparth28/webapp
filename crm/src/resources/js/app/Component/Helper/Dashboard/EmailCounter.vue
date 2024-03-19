<template>
    <div class="mb-primary" :class="`col-6 col-sm-6 col-md-${col} col-lg-${col} col-xl-${col}`">
        <div class="card card-with-shadow border-0 p-primary dashboard-widgets h-100">
            <div class="widget-data">
                <h6>{{ count }}</h6>
                <p>{{ title }}</p>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "EmailCounter",
        props: {
            count: {
                required: false,
                default: 0
            },
            title: {
                required: true
            },
            col: {
                default: 2
            }
        }
    }
</script>
