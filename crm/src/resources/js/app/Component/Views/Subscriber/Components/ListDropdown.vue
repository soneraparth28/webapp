<template>
    <div class="" >
        <div class="form-group form-group-with-search px-primary py-half-primary">
                    <span class="form-control-feedback">
                        <app-icon :name="'search'"/>
                    </span>
            <input type="text"
                   class="form-control"
                   v-model="searchValue"
                   :placeholder="$t('search')"
            />
        </div>
        <div class="dropdown-divider my-0"/>
        <div class="dropdown-search-result-wrapper custom-scrollbar">
            <a v-for="(list, index) in searchAbleOption"
               :key="index"
               class="dropdown-item cursor-pointer text-truncate"
               @click.prevent="checkList(list.id)">
                {{list.name}}
                <span class="check-sign float-right" v-show="isChecked(list.id)">
                    <i class="fa fa-check"/>
                </span>
            </a>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ListDropdown",
        props: {
            value: {
                type: Array,
                required: true,
                default: function () {
                    return [];
                }
            },
            lists: {
                type: Array,
                required: true,
                default: function () {
                    return [];
                }
            },
        },
        data() {
            return {
                checkedList: [],
                searchValue: ''
            }
        },
        methods: {
            checkList(id) {
                if (this.checkedList.includes(id)) {
                    this.checkedList = this.checkedList.filter(l => l !== id)
                } else {
                    this.checkedList.push(id);
                }
                this.$emit('input', this.checkedList);
            },
            isChecked(id) {
                return this.value && this.value.includes(id)
            }
        },
        computed: {
            searchAbleOption() {
                if (this.searchValue) {
                    return this.lists.filter(option => {
                        return option.name.toLowerCase().includes(this.searchValue.toLowerCase())
                    })
                }
                return this.lists;
            },
            searchableOptionWatcher() {
                return !!this.searchAbleOption.length
            }
        },
        watch: {
            searchableOptionWatcher: function (flag) {
                this.$emit('filteredFlag', flag)
            }
        }
    }
</script>
