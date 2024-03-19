import {axiosGet} from "../../../../Helpers/AxiosHelper";
import {custom_field} from "../../../../config/apiUrl";
import {collection, formatDateToLocal, isValidDate, optional} from "../../../../Helpers/helpers";

export const getCustomField = async (payload = '') => {
    return axiosGet(`${custom_field}?in_list=1`);
}

export const customFieldColumn = () => {
    return getCustomField().then(response => {
        return response.data.map(field => {
            return {
                title: field.name,
                type: 'custom-html',
                key: 'custom_fields',
                isVisible: true,
                modifier: custom_fields => {
                    const value = optional(collection(custom_fields).find(field.id, 'custom_field_id'), 'value');
                    console.dir(custom_fields.find(l => console.log(l, field.id, field.name)))
                    return isValidDate(value) ? formatDateToLocal(value) : value;
                }
            }
        });
    }).catch(error => [])
}
