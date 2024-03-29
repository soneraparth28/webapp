import {mapState} from "vuex";
import FormHelperMixins from "../../../../../../Mixins/Global/FormHelperMixins";
import Message from "../../../../../Helper/Message/Message";
import NotificationEventMixin from "../../Mixin/NotificationEventMixin";

export default {
    components: {Message},
    mixins: [FormHelperMixins, NotificationEventMixin],
    data() {
        return {
            loaded: false,
            template: {
                type: 'mail'
            },
        }
    },
    computed: {
        ...mapState({
            notification_channels: state => state.additional.notification_channels,
            notification_event: state => state.notification_event.notification_event,
            loader: state => state.loading
        }),
    },
    methods: {

        afterSuccess(response) {
            this.loading = false;
            this.$toastr.s("", response.data.message)
            $("#notification-template").modal('hide')
            this.$hub.$emit('reload-notification-template');
        },
    },
}
