import {collection} from "../../Helpers/helpers";
import {custom_field, logic_names, segments_front_end} from "../../config/apiUrl";

import Vue from "vue";
import {axiosGet} from "../../Helpers/AxiosHelper";

export default {
    methods: {
        setValueField(parentIndex, childIndex){
            const logic = this.getCurrentLogic(parentIndex, childIndex)
            if (logic && logic.operator && logic.operator.length){
                return this.$set(this.operator[parentIndex], childIndex, logic.operator[0])
            }
            const operator = this.segment.segment_logic[parentIndex][childIndex][1];
            if (!operator){
                return this.$set(this.operator[parentIndex], childIndex, this.operators[0])
            }
            this.$set(this.operator[parentIndex], childIndex, collection(this.operators).find(operator))
            this.$set(this.segment.segment_logic[parentIndex][childIndex], 2, '')
        },
        setOperators(parentIndex, childIndex){
            const logic = this.getCurrentLogic(parentIndex, childIndex)
            this.logicValues[parentIndex][childIndex] = [];
            if (logic && logic.operator){
                const operators = collection(logic.operator).shaper('translated_name', 'name');
                this.$set(
                    this.list[parentIndex],
                    childIndex,
                    operators
                );
                this.$set(this.segment.segment_logic[parentIndex][childIndex], 1, operators[0].name);
                this.$set(this.segment.segment_logic[parentIndex][childIndex], 2, null);
            }else {
                const operators = this.getCustomFieldOperators(logic);
                this.$set(this.list[parentIndex], childIndex, operators);
                if (operators.length){
                    this.$set(this.segment.segment_logic[parentIndex][childIndex], 1, operators[0].name);
                }
            }
            this.setValueField(parentIndex, childIndex)
        },
        async getLogics() {
            this.preloader = true
            const response = await this.axiosGet(logic_names);
            const fields = await this.getCustomFields();
            this.all_logics = collection(response.data)
                .shaper('translated_name', 'name');
                if(fields.length) {
                    this.all_logics = this.all_logics.concat([{"id":  'disabled', value:  Vue.prototype.$t('custom_fields'), disabled: true}])
                        .concat(fields);
                }
            this.setFirstIndex(0, 0)

        },
        getCurrentLogic(parentIndex, childIndex) {
            const name = this.segment.segment_logic[parentIndex][childIndex][0];
            return collection(this.all_logics).find(name)
        },
        async getCustomFields() {
            try {
                const response = await axiosGet(`${custom_field}`);
                return collection(response.data)
                    .shaper('name', 'name')
            }catch (e) {
                return []
            }
        },
        setFirstIndex(parentIndex, childIndex) {
            if (this.all_logics.length > 1 && (!this.actionUrl || this.allowForEditing)){
                const logic = this.all_logics[0];
                this.$set(this.segment.segment_logic[parentIndex][childIndex], 0, logic.name);
                if (logic && logic.operator && logic.operator.length){
                    this.setOperatorList(parentIndex, childIndex, logic.operator);
                    this.setSegmentCurrentIndexOperatorName(parentIndex, childIndex, logic.operator[0].name);
                    this.setOperatorCurrentIndex(parentIndex, childIndex, logic.operator[0]);
                }
            }
        },
        setOperatorList(parentIndex, childIndex, operators){
            this.$set(this.list[parentIndex], childIndex, collection(operators).shaper('translated_name', 'name'))
        },
        setSegmentCurrentIndexOperatorName(parentIndex, childIndex, operator_name) {
            this.$set(this.segment.segment_logic[parentIndex][childIndex], 1, operator_name);
        },
        setOperatorCurrentIndex(parentIndex, childIndex, operator) {
            this.$set(this.operator[parentIndex], childIndex, operator)
        },
        deleteIndex(parentIndex, childIndex) {
            this.segment.segment_logic[parentIndex].splice(childIndex, 1);
            if (this.segment.segment_logic[parentIndex].length === 0) {
                this.segment.segment_logic.splice(parentIndex, 1)
            }
        },
        addOrCondition(parentIndex, childIndex) {
            this.segment.segment_logic[parentIndex].push(['', '', '']);
            if (this.operator.length < parentIndex + 1) {
                this.operator[parentIndex] = [];
                this.list[parentIndex] = [];
            }
            this.setFirstIndex(parentIndex, parseInt(childIndex) + 1);
        },
        addAndCondition() {
            this.segment.segment_logic.push([
                ['', '', ''],
            ]);
            const parentIndex = this.segment.segment_logic.length - 1;
            this.list[parentIndex] = [];
            this.operator[parentIndex] = [];
            this.logicValues[parentIndex] = [];
            this.logicValues[parentIndex][0] = [];
            this.setFirstIndex(parentIndex, 0)
        },
        checkLastOr(segment, childIndex){
            return segment.length === (parseInt(childIndex) + 1);
        },
        getValueFieldType(parentIndex, childIndex) {
            const operator = collection(this.operators)
                .find(this.segment.segment_logic[parentIndex][childIndex][1])
            const logic = this.getCurrentLogic(parentIndex, childIndex);
            if (logic.operator){
                return operator.id === 'between' ? 'range' : operator.type
            }else{
                if (logic.meta) {
                    this.logicValues[parentIndex][childIndex] = this.getCustomFieldLogicValues(logic);
                }
                return logic.type;
            }
        },
        getCustomFieldOperators(logic) {
            const types = { number: 'text', select: 'text', date: 'date', radio: 'text', textarea: 'text', text: 'text'};
            return collection(this.operators).filter(types[logic.type], 'type').filter(operator => {
                if (['select', 'number', 'radio'].includes(logic.type)) {
                    if (['is', 'is not'].includes(operator.id))
                        return true
                    return false;
                }
                return true
            })
        },
        getCustomFieldLogicValues(logic) {
            return collection(logic.meta.split(',')).unique().map(l => {
                return { id: l, value: l }
            });
        },
    },
    computed: {
        operators() {
            return this.$store.getters.getFormattedOperators
        },
    },
    created() {
        this.$store.dispatch('dispatchAllAction')
        this.getLogics();
    },
}
