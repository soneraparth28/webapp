export default {
    props: {
        value: {
            type: Object,
            required: true
        },
        alias: {
            required: true
        },
        errors: {}
    },
    data() {
        return {
            delivery: {}
        }
    },
    watch: {
        delivery: {
            handler: function (delivery) {
                this.$emit('input', delivery)
            },
            deep: true
        },
        value: {
            handler: function (value) {
                this.delivery = value;
            },
            immediate: true
        }
    },
    created() {
        this.delivery = {...this.value}
    }
}