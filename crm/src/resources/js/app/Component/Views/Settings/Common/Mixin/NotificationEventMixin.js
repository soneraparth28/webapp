export default {
    methods: {
        getNotificationChannels() {
            return this.notification_channels.filter(channel => {
                if (channel.id === 'database' && ['user_invitation', 'password_reset'].includes(this.notification_event.name))
                    return false;
                return true;
            })
        }
    }
}
