<template>
    <div class="content-wrapper">
        <app-page-top-section
            :title="$fieldTitle(getActionType, 'segment', true)"
            :directory="`${$t('segment')} | <a href='${urlGenerator(segments_front_end)}/list'>${$t('back_to',
            {destination: $allLabel('segments')} )}</a>`"
            :hide-button="true"
            icon="menu"/>
        <create-edit-form :loading="loading"
                          :submit-data="submitData"
                          @cancel="redirectToList"
                          v-if="!preloader"
        >
            <div class="mb-primary">
                <note :title="$t('what_is', { subject: $t('segment') })"
                      :description="$t('segment_description')"/>
            </div>
            <div class="form-group row">
                <div class="col-sm-3 col-md-3 col-lg-2 col-xl-2 d-flex align-items-center">
                    <label class="text-left d-block mb-2 mb-lg-0">
                        {{ $fieldLabel('segment') }}
                    </label>
                </div>
                <div class="col-sm-9 col-md-9 col-lg-8 col-xl-8">
                    <app-input v-model="segment.name"
                               :error-message="$errorMessage(errors, 'name')"
                               :placeholder="$placeholder('segment', 'name')"/>
                </div>
            </div>
            <div class="form-group row mb-0">
                <div class="col-sm-3 col-md-3 col-lg-2 col-xl-2">
                    <label class="text-left d-block mb-2 mb-lg-0">
                        {{ $fieldTitle('segment', 'logic') }}
                    </label>
                </div>
                <div class="col-sm-9 col-md-9 col-lg-8 col-xl-8">
                    <div class="mb-primary" v-for="(segment, parentIndex) in segment.segment_logic">
                        <div class="row" v-for="(segmentOrLogic, childIndex) in segment">
                            <div class="col-lg-3 col-xl-3 mb-3">
                                <app-input type="select"
                                           :list="all_logics"
                                           v-model="segmentOrLogic[0]"
                                           @input="setOperators(parentIndex, childIndex)"/>
                            </div>
                            <div class="col-lg-3 col-xl-3 mb-3">
                                <app-input type="select"
                                           :list="list[parentIndex][childIndex]"
                                           v-model="segmentOrLogic[1]"
                                           @input="setValueField(parentIndex, childIndex)"/>
                            </div>
                            <div class="col-lg-3 col-xl-3 mb-3" v-if="operator[parentIndex][childIndex]">
                                <app-input v-if="getValueFieldType(parentIndex, childIndex) !== 'range'"
                                           :type="getValueFieldType(parentIndex, childIndex)"
                                           :class="getValueFieldType(parentIndex, childIndex) === 'radio' ? 'mt-2' : ''"
                                           v-model="segmentOrLogic[2]"
                                           :list="logicValues[parentIndex][childIndex]"
                                           :placeholder="operator[parentIndex][childIndex].type !== 'date' ? $placeholder('logic', 'value') : ''"/>
                                <app-input v-else
                                           type="date"
                                           v-model="segmentOrLogic[2]"
                                           date-mode="range"
                                           :placeholder="operator[parentIndex][childIndex].type !== 'date' ? $placeholder('logic', 'value') : ''"/>
                            </div>
                            <div class="col-lg-3 col-xl-3 mb-3 pt-1 text-right">
                                <div class="btn-group btn-group-action d-inline-block">
                                    <button type="button"
                                            class="btn"
                                            @click="deleteIndex(parentIndex, childIndex)"
                                            v-if="parseInt(childIndex) + parseInt(parentIndex) !== 0">
                                        <app-icon name="trash"/>
                                    </button>
                                </div>
                                <button class="btn btn-primary"
                                        type="button"
                                        v-if="checkLastOr(segment, childIndex)"
                                        @click="addOrCondition(parentIndex, childIndex)">
                                    <app-icon name="plus" width="17" height="17"/>
                                    {{ $t('capital_or') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-primary" @click="addAndCondition" type="button">
                                <app-icon name="plus" width="17" height="17"/>
                                {{ $t('capital_and') }}
                            </button>
                        </div>
                    </div>
                    <app-message type="error" :message="$errorMessage(errors, 'segment_logic')"/>
                </div>
            </div>
        </create-edit-form>
        <app-pre-loader class="p-primary" v-else />
    </div>
</template>

<script>
    import {segments_front_end, segments} from '../../../config/apiUrl'
    import FormHelperMixins from "../../../Mixins/Global/FormHelperMixins";
    import SegmentMixin from "../../../Mixins/Segment/SegmentMixin";
    import CreateEditForm from "../../Helper/Card/CreateEditForm";
    import moment from 'moment'
    import {collection} from "../../../Helpers/helpers";
    import Note from "../../Helper/Note/Note";
    import {urlGenerator} from "../../../Helpers/AxiosHelper";

    export default {
        name: "StoreUpdateSegment",
        components: {Note, CreateEditForm},
        mixins: [FormHelperMixins, SegmentMixin],
        props: {
            action: {
                required: false
            },
            actionUrl: {
                required: false,
                type: String
            }
        },
        data() {
            return {
                segment: {
                    segment_logic: [
                        [
                            ['', '', ''],
                        ]
                    ]
                },
                list: [[]],
                operator: [[]],
                all_logics: [],
                allowForEditing: false,
                logicValues: [[]],
                segments_front_end
            }
        },
        methods: {
            submitData() {
                this.fieldStatus.isSubmit = true;
                this.loading = true;
                this.message = '';
                this.errors = {};
                const segment_logic = this.segment.segment_logic.map(segment => {
                    return segment.map(segmentOr => {
                        const operator = collection(this.operators).find(segmentOr[1])
                        if (operator.type === 'date_range') {
                            const start = segmentOr[2].start ? moment(segmentOr[2].start).format('YYYY-MM-DD') : ''
                            const end = segmentOr[2].end ? moment(segmentOr[2].end).format('YYYY-MM-DD') : ''
                            segmentOr[2] = `${start},${end}`;
                        } else {
                            segmentOr[2] = segmentOr[2] ? operator.type === 'date' ? moment(segmentOr[2]).format('YYYY-MM-DD') : segmentOr[2] : '';
                        }
                        return segmentOr;
                    }).filter(segment => segment[2])
                }).filter(segment => segment.length)

                const formData = {...this.segment};
                formData.segment_logic = segment_logic;
                this.submitFromFixin(this.getRequestType, this.getRequestUrl, formData)
            },
            afterSuccess({data}) {
                this.toastAndReload(data.message);
                if (this.actionUrl) {
                    this.redirectToList()
                } else {
                    location.reload();
                }
            },
            async getUpdateData() {
                this.preloader = true;
                const response = await this.axiosGet(this.actionUrl);
                this.preloader = false;
                this.segment = response.data;
                this.segment.segment_logic = this.$optional(response.data, 'segment_logic').map((segment, parentIndex) => {
                    return segment.map((segmentOr, childIndex) => {
                        const logic = collection(this.all_logics).find(segmentOr[0]);
                        this.logicValues[parentIndex] = this.logicValues[parentIndex] || [];
                        this.logicValues[parentIndex][childIndex] = [];
                        if (!childIndex) {
                            this.list[parentIndex] = [];
                            this.operator[parentIndex] = [];
                        }
                        if (logic && logic.operator && logic.operator.length) {
                            this.setOperatorList(parentIndex, childIndex, logic.operator)
                        } else {
                            if (logic.meta) {
                                this.logicValues[parentIndex][childIndex] = this.getCustomFieldLogicValues(logic)
                            }
                            this.setOperatorList(parentIndex, childIndex, this.getCustomFieldOperators(logic))
                        }
                        this.setSegmentCurrentIndexOperatorName(parentIndex, childIndex, segmentOr[1]);
                        const operator = collection(this.operators).find(segmentOr[1]);
                        this.setOperatorCurrentIndex(parentIndex, childIndex, operator)
                        if (operator.type === 'date_range') {
                            const spited = Array.isArray(segmentOr[2]) ? segmentOr[2] : segmentOr[2].split(',')
                            segmentOr[2] = {
                                start: new Date(spited[0]),
                                end: new Date(spited[1])
                            }
                        } else {
                            segmentOr[2] = operator.type === 'date' ? new Date(segmentOr[2]) : segmentOr[2];
                        }
                        return segmentOr;
                    });
                });
                this.allowForEditing = true;
            },
            redirectToList() {
                window.location = `${urlGenerator(segments_front_end)}/list`;
            },
        },
        computed: {
            getActionType() {
                return this.action ? 'copy' : this.actionUrl ? 'edit' : 'add';
            },
            getRequestType() {
                return this.action ? 'post' : this.actionUrl ? 'patch' : 'post';
            },
            getRequestUrl() {
                return this.action ? segments : this.actionUrl ? `${segments}/${this.segment.id}` : segments;
            }
        },
        watch: {
            'all_logics.length': {
                handler: function (value) {
                    this.preloader = false
                    if (parseInt(value) > 1 && this.actionUrl) {
                        this.getUpdateData();
                    }
                }
            }
        }
    }
</script>
