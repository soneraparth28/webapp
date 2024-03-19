<template>
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="card card-with-search border-0 card-with-shadow">
                    <div class="card-header d-flex align-items-center p-primary bg-transparent">
                        <div class="form-group form-group-with-search d-flex align-items-center mb-0 ml-auto">
                            <a href="#" class="btn btn-primary"
                               @click="$emit('showAddForm')"
                            >{{ $fieldTitle('add', 'new') }}</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-responsive-lg table-responsive-md">
                            <thead>
                                <tr>
                                    <th>{{ $t('name') }}</th>
                                    <th>{{ $t('use_for') }}</th>
                                    <th>{{ $fieldTitle('provider') }}</th>
                                    <th>{{ $fieldTitle('from', 'email') }}</th>
                                    <th>{{ $fieldTitle('from', 'name') }}</th>
                                    <th>{{ $t('actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(server, key) in servers">
                                    <td>{{ server.name }}</td>
                                    <td>{{ $t(server.use_for) }}</td>
                                    <td>{{ $t(server.context) }}</td>
                                    <td>{{ server.from_email }}</td>
                                    <td>{{ server.from_name }}</td>
                                    <td>
                                        <span class="fa fa-edit text-primary"
                                              style="cursor: pointer"
                                              @click="$emit('fetchData', key)"
                                        />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mail_settings_list } from '../../../../config/apiUrl'
    import { axiosGet} from '../../../../Helpers/AxiosHelper'

    export default {
        name: "MailServerLIst",
        props: {
            props: {
                default: function () {
                    return {
                        alias: 'app'
                    }
                }
            }
        },

        data() {
            return {
                servers: []
            }
        },

        methods: {
            getMailServerLists() {
                axiosGet(`${mail_settings_list}/${this.props.alias}`).then(res => {
                    this.servers = res.data;
                });
            }
        },

        created() {
            this.getMailServerLists();
        }

    }
</script>

<style scoped>

</style>
