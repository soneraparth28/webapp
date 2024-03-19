import {subscriber_store} from "../../config/apiUrl";

export const ListSubscriberMixin = {
    data: {
        SubscriberRules: {
            url: {
                type: String,
                default: subscriber_store
            },
            visible: {
                type: Object,
                default: () => {
                    return {
                        wrapper: false,
                        topBar: false,
                        options: {
                            action: false,
                        },
                    }
                }
            }
        }
    }
};
