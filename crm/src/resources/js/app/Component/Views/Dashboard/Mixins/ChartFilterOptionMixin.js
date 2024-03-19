export default {
    data() {
        return {
            chartFilterOptions: [
                {id: 1, value: this.$t('last_7_days')},
                {id: 2, value: this.$t('this_week')},
                {id: 3, value: this.$t('last_week')},
                {id: 4, value: this.$t('this_month')},
                {id: 5, value: this.$t('last_month')},
                {id: 6, value: this.$t('this_year')},
                {id: 0, value: this.$t('Total')},
            ],
        }
    }
}
