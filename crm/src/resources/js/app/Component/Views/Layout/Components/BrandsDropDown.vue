<template>
    <div class="dropdown d-inline-block btn-dropdown btn-brand-dropdown">
        <button type="button"
                class="btn dropdown-toggle"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false">
            <span class="d-none d-sm-inline">{{ brand.name }}</span>
            <app-icon name="server" width="15" height="15" class="primary-text-color d-sm-none"/>
        </button>
        <div class="dropdown-menu p-0">
            <a class="dropdown-item dropdown-title"
               :href="urlGenerator('/admin/brands/list')"
               v-if="$can('view_brands')"
            >
                <app-icon name="tool" class="primary-text-color mr-2"/>
                {{ $t('manage_brands') }}
            </a>
            <div class="dropdown-divider" v-if="brands.length"></div>
            <div class="brand-items" v-if="brands.length">
                <a class="dropdown-item"
                   v-for="brand in brands"
                   :href="urlGenerator(`/brands/${brand.short_name}/dashboard`)">
                    {{ brand.name }}
                </a>
            </div>
        </div>
    </div>
</template>

<script>
    import {axiosGet} from "../../../../Helpers/AxiosHelper";
    import {user_brands} from '../../../../config/apiUrl'
    import {urlGenerator} from "../../../../Helpers/AxiosHelper";

    export default {
        name: "BrandsDropDown",
        data(){
            return {
                brands:{},
                urlGenerator
            }
        },
        computed: {
            brand(){
                return window.brand;
            }
        },
        methods: {
            getBrands(){
                axiosGet(user_brands).then(res => {
                    this.brands = res.data.filter(b => {
                        return this.$optional(b, 'id') !== this.$optional(brand, 'id')
                    }).filter(b => b);
                })
            }
        },
        created() {
            this.getBrands();
        }
    }
</script>

<style scoped>

</style>
