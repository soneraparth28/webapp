<template>
    <div class="content-wrapper">
        <app-page-top-section
            :directory="`${$t('templates')} | <a href='${urlGenerator(api.cards)}'>${$t('back_to', {destination: $t('card_view')} )}</a>`"
            :title="$t('list_view')"
            icon="cpu">
            <app-default-button :url="api.create" :title="$addLabel('template')" />
        </app-page-top-section>

        <app-table
            id="template-table"
            :options="options"
            @action="triggerActions"
        />

        <app-template-preview-modal
            :sourceURL="preview_template_url"
            :id="template_id"
            :title="preview_template_title"
            @deletable="deletable"
            @close="isPreviewModalActive = false"
            v-if="isPreviewModalActive"
            :isActionsActive="false"
        />

        <app-confirmation-modal
            @cancelled="cancelled"
            @confirmed="triggerConfirmed('template-table')"
            modal-id="app-confirmation-modal"
            v-if="confirmationModalActive"
        />
    </div>
</template>

<script>
    import {template_create, templates, templates_card_view} from '../../../config/apiUrl';
    import {formatDateToLocal} from "../../../Helpers/helpers";
    import DatatableHelperMixin from "../../../Mixins/Global/DatatableHelperMixin";
    import TemplateHelperMixin from "../../DataTable/DataTableMixins/Helper/TemplateHelperMixin";
    import {urlGenerator} from "../../../Helpers/AxiosHelper";

    export default {
        mixins: [DatatableHelperMixin, TemplateHelperMixin],
        data() {
            return {
                api: {
                    cards: `/${templates_card_view}`,
                    create: `${template_create}?view=list_view`
                },
                template_id: '',
                preview_template_url: '',
                preview_template_title: '',
                isDeleteFromModal: false,
                isPreviewModalActive: false,
                templates,
                urlGenerator,
                options: {
                    name: this.$t('templates'),
                    url: templates,
                    showHeader: true,
                    columns: [
                        {
                            title: this.$t('subject'),
                            type: 'button',
                            key: 'subject',
                            sortAble: true,
                            isVisible: true,
                            className: 'btn btn-link text-primary px-0',
                            modifier: (subject) => {
                                return subject;
                            }
                        },
                        {
                            title: this.$t('added'),
                            type: 'custom-html',
                            key: 'created_at',
                            modifier: createdAt => formatDateToLocal(createdAt),
                            isVisible: true,

                        },
                        {
                            title: this.$t('download'),
                            type: 'custom-html',
                            modifier: function (value, row) {
                                return `<a
                                    href="${urlGenerator(templates)}/${row.id}/content" download="${row.subject}">
                                    <i data-feather="download"/>
                                </a>`
                            },
                            isVisible: true,
                        },
                        {
                            title: this.$t('duplicate'),
                            type: 'custom-html',
                            modifier: function (value, row) {
                                return `<a
                                    href="${urlGenerator(templates)}/${row.id}/copy">
                                    <i data-feather="copy"/>
                                </a>`
                            },
                            isVisible: true,
                        },
                        {
                            title: this.$t('actions'),
                            type: 'action',
                            key: 'invoice',
                            isVisible: true
                        },

                    ],
                    filters: [
                        {
                            title: this.$t('created'),
                            type: "range-picker",
                            key: "date",
                            option: ["today", "thisMonth", "last7Days", "thisYear"]
                        },
                    ],
                    paginationType: 'pagination',
                    responsive: false,
                    rowLimit: 10,
                    showAction: true,
                    orderBy: 'desc',
                    actionType: 'default',
                    actions: [
                        {
                            title: this.$t('edit'),
                            icon: 'edit',
                            type: 'edit',
                            name: 'edit',
                            url: `/${templates}`,
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
                            component: 'app-confirmation-modal',
                            modalId: 'app-confirmation-modal',
                            name: 'delete',
                            url: `/${templates}`,
                            modifier: template => {
                                if (!template.brand_id)
                                    return false;
                                return this.$can('delete_templates')
                            }
                        }
                    ],
                }
            }
        },

        methods: {
            triggerActions(row, action, active) {
                if (action.key === 'subject') {
                    this.template_id = row.id;
                    this.preview_template_url = `${templates}/${row.id}/content`;
                    this.preview_template_title = row.subject;
                    this.isPreviewModalActive = true;
                }else if (action.type === 'edit') {
                    if (!row.brand_id) {
                        this.$toastr.e('', `${this.$t('this_template_can_be_only_edited_from_app_side')} ${this.$t('You can duplicate it if you want')}`)
                    }else {
                        const baseURL = `${action.url}/${row.id}`;
                        window.location = urlGenerator(`${baseURL}/${action.name}`);
                    }
                }else {
                    this.getAction(row, action, active)
                }
            },

        },
        computed: {

        }



    }
</script>
