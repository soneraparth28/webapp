<template>
    <div class="content-wrapper">
        <app-page-top-section
            :title="$fieldTitle('import','subscribers', true)"
            :directory="`${$t('subscribers')} | <a href='${urlGenerator(subscribers_front_end)}'>${$t('back_to',
            {destination: $allLabel('subscribers', true)} )}</a>`"
            :hide-button="true"
            icon="message-circle"
        />
        <div class="card border-0 card-with-shadow">
            <div class="card-body">
                <div class="mb-primary">
                    <div class="note-title d-flex">
                        <app-icon name="book-open"/>
                        <h6 class="card-title pl-2">{{ $t('csv_format_guideline')}}</h6>
                    </div>
                    <div class="note note-warning p-4">
                        <p class="my-1">- {{ $t('csv_guideline_1') }}</p>
                        <p class="my-1">- {{ $t('csv_guideline_1.5') }}</p>
                        <p class="my-1">- {{ $t('csv_guideline_2') }}</p>
                        <p class="my-1">- {{ $t('csv_guideline_3') }}</p>
                        <p class="my-1">- {{ $t('csv_guideline_4') }}</p>
                        <p class="my-1">- {{ $t('csv_guideline_6', {fields: [...validKeys].join(', ')}) }}</p>
                        <p class="my-1">- {{ $t('csv_guideline_7') }}</p>
                        <p class="my-1">- {{ $t('csv_guideline_5') }}</p>
                    </div>
                </div>
                <div class="d-flex flex-column flex-sm-row align-items-sm-center mb-3">
                    {{$t('csv_download_label')}}
                    <app-input type="checkbox"
                               :list="[{id:1, value: $fieldTitle('include', 'custom_fields')}]"
                               v-model="sampleFileType"
                               :label-class="'mb-0'"
                               class="mx-sm-3"/>
                    <a class="text-primary cursor-pointer"
                       @click.prevent="sampleDownload">{{$t('download')}}
                    </a>
                </div>
                <div class="form-group">
                    <app-input
                        v-if="dropZoneBoot"
                        type="dropzone"
                        v-model="files"
                        :maxFiles="1"
                        :error-message="$errorMessage(errors, 'subscribers')"
                    />
                </div>
                <div class="form-group">
                    <app-input
                        class="mb-4"
                        type="radio"
                        :list="[
                                        {id:'0', value:  $t('default_fields_only')},
                                        {id:'1', value: $t('custom_fields_also')}
                                    ]"
                        :custom-radio-type="'mb-2 mb-sm-0 custom-radio-default'"
                        v-model="customFieldType"
                        :error-message="$errorMessage(errors, 'files')"
                    />
                </div>
                <div class="row">
                    <div class="col-12 mt-4">
                        <app-submit-button :loading="loading" @click="submitData"/>
                        <app-cancel-button @click="redirectToList" btn-class="ml-2"/>
                    </div>
                </div>
            </div>
        </div>

        <app-import-preview-modal
            @close="isPreviewModalActive = false"
            v-if="isPreviewModalActive"
            :subscribers="subscribers"
            :customFieldType="customFieldType"
            @succeed="afterImportSucceed"
        />
    </div>
</template>

<script>
    import FormHelperMixins from "../../../Mixins/Global/FormHelperMixins";
    import {
        subscribers_front_end,
        subscriber_view_imported,
        subscriber_import
    } from "../../../config/apiUrl";
    import {urlGenerator} from "../../../Helpers/AxiosHelper";

    export default {
        name: "Import",
        mixins: [FormHelperMixins],
        props: {
            validKeys: {
                type: Array
            }
        },
        data() {
            return {
                subscribers_front_end,
                files: [],
                subscribers: {},
                isPreviewModalActive: false,
                customFieldType: 0,
                sampleFileType: [],
                dropZoneBoot: true
            }
        },
        methods: {
            submitData() {
                this.fieldStatus.isSubmit = true;
                this.loading = true;
                this.message = '';
                this.errors = {};

                let formData = new FormData;

                formData.append('type', this.customFieldType);

                if (this.files.length) {
                    formData.append('subscribers', this.files[0])
                }

                this.submitFromFixin(
                    'post', subscriber_view_imported, formData
                )
            },
            afterSuccess({data}) {
                this.subscribers = data.subscribers
                this.toastAndReload(data.message)
                this.dropZoneBoot = false;
                this.files = [];
                setTimeout(() => this.dropZoneBoot = true)
                if (data.subscribers.filtered.length) {
                    this.isPreviewModalActive = true;
                }

            },

            afterImportSucceed(data) {
                this.toastAndReload(data.message)
                window.location = subscriber_import
            },


            sampleDownload() {
                let commas = ''
                let keys = ['first_name', 'last_name', 'email'];
                if (this.sampleFileType.length) {
                    keys = [...this.validKeys];
                    commas = ",".repeat(keys.slice(3).length)
                }
                this.downloadCSV(
                    `${keys.join(',')}\n` +
                    `John,Doe,john@gain.com${commas}\n` +
                    `Hobart,,rob@test.com${commas}\n` +
                    `James,MaCgil,saul@goodman.com${commas}\n` +
                    `Jesse,Pinkman,jesse@mail.com${commas}\n` +
                    `Jesse,Todd,jesse@mail.com${commas}\n`
                )
            },

            downloadCSV(csv) {
                let e = document.createElement('a');
                e.href = 'data:text/csv;charset=utf-8,' + encodeURI(csv);
                e.target = '_blank';
                e.download = `${this.$t('subscribers')}.csv`;
                e.click();
            },

            redirectToList() {
                window.location = urlGenerator(subscribers_front_end);
            }
        },

    }
</script>
