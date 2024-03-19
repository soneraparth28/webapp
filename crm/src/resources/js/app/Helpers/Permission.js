export default class Permission {
    getPermissions() {
        return window.localStorage.getItem('permissions');
    }

    can(ability) {
        if (this.permissions().is_brand_admin && ability === 'view_brands') {
            return  false;
        }

        if (this.permissions().is_app_admin || this.permissions().is_brand_admin) {
            return true
        }
        return this.permissions()[ability];
    }

    permissions() {
        return JSON.parse(this.getPermissions());
    }
}
