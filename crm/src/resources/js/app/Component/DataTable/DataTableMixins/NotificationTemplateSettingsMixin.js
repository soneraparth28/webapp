import {notification_events} from "../../../config/apiUrl";
import {collection} from "../../../Helpers/helpers";

export default {
    data() {
        return {
            options: {
                name: this.$fieldTitle('notification', 'template'),
                url: `${notification_events}?type=${this.props.alias}`,
                showHeader: true,
                tableShadow: false,
                showManageColumn: false,
                tablePaddingClass: 'px-0 py-primary',
                columns: [
                    {
                        title: this.$fieldTitle('event', 'name'),
                        type: 'text',
                        key: 'translated_name',
                        uniqueKey: 'id',
                    },
                    {
                        title: this.$fieldTitle('available', 'type'),
                        type: 'custom-html',
                        key: 'templates',
                        isVisible: true,
                        modifier: (templates) =>  {
                            return collection(templates).pluck('type').map(type => {
                                return `<span class="badge badge-pill ${type === 'database' ? 'badge-primary' : 'badge-success'}">${this.$t(type)}</span>`
                            }).join(' ')
                        }

                    },
                    {
                        title: this.$fieldTitle('mail', 'subject'),
                        type: 'custom-html',
                        key: 'templates',
                        isVisible: false,
                        modifier: (templates) =>  {
                            return collection(templates).pluck('subject').filter(subject => subject).join(', ')
                        }

                    },
                    {
                        title: this.$t('actions'),
                        type: 'action',
                        key: 'invoice',
                        isVisible: true
                    },

                ],
                filters: [

                ],
                paginationType: "loadMore",
                responsive: true,
                rowLimit: 10,
                showAction: true,
                actionType: "default",
                actions: [
                    {
                        title: this.$t('edit'),
                        icon: 'edit',
                        type: 'modal',
                        url: '',
                        actionType: 'edit'
                    }
                ],
            }
        }
    }
}
