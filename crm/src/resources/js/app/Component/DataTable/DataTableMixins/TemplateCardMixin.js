import {templates} from "../../../config/apiUrl";
import DatatableHelperMixin from "../../../Mixins/Global/DatatableHelperMixin";

export default  {

    mixins: [DatatableHelperMixin],

    data() {
        return {
            properties: {
                filters: [
                    {
                        title: this.$t('created'),
                        type: 'range-picker',
                        key: 'date',
                        option: ['today', 'thisMonth', 'last7Days', 'thisYear']
                    }
                ],
                showFilter: true,
                url: templates,
                customContentKey: 'custom_content',
                defaultContentKey: 'default_content',
                showAction: true,
                showSearch: true,
                previewType: 'html',
                previewImageKey: {
                    relation: 'thumbnail',
                    key: 'full_url'
                },
                actions: [
                    {
                        title: this.$t('preview'),
                        icon: 'eye',
                        type: 'modal',
                        modalId: 'template-preview',
                        name: 'preview'
                    },
                    {
                        title: this.$t('edit'),
                        icon: 'edit',
                        type: 'edit',
                        url: `/${templates}`,
                        name: 'edit',
                        modifier: template => {
                            if (!template.brand_id)
                                return false;
                            return this.$can('update_templates')
                        }
                    },
                    {
                        title: this.$t('delete'),
                        icon: 'trash',
                        type: 'modal',
                        modalId: 'app-confirmation-modal',
                        url: `/${templates}`,
                        name: 'delete',
                        modifier: template => {
                            if (!template.brand_id)
                                return false;
                            return this.$can('delete_templates')
                        }
                    },
                    {
                        title: this.$t('duplicate'),
                        icon: 'copy',
                        type: 'page',
                        url: `/${templates}`,
                        name: 'copy',
                    },
                    {
                        title: this.$t('download'),
                        icon: 'download',
                        type: 'download',
                    }
                ],
                cardLimit: 10
            }
        }
    }
}
