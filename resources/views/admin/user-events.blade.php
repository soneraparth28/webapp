@extends('admin.layout')

@section('css')
<link href="{{ asset('public/plugins/iCheck/all.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('public/plugins/tagsinput/jquery.tagsinput.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('public/plugins/datatables/ext/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('public/plugins/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h4>
            {{ trans('admin.admin') }}
            <i class="fa fa-angle-right margin-separator"></i>
            {{ trans('general.sales') }}
        </h4>
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="content">

            <div class="row">

                <style>
                    .apexcharts-tooltip {
                        background: #f3f3f3;
                        color: #2e2e2e !important;
                    }
                    .daterangepicker thead *, .daterangepicker tbody td.in-range {
                        color: #999 !important;
                    }
                    .daterangepicker tbody td:not(.daterangepicker tbody td.available.active, .daterangepicker tbody td.available.in-range) {
                        color: #DFDFDF !important;
                    }
                </style>



{{--                <div style="margin-top: 1rem;">--}}
{{--                    <p style="font-size: 18px; font-weight: bold;">Table showing data for period</p>--}}
{{--                    <input type="text" class="DP_RANGE form-control" value="{{date("d/m/Y") . " - " . date("d/m/Y")}}" style="margin-top: .25rem;"--}}
{{--                    name="date_selector" data-preset-start-time="today"/>--}}
{{--                </div>--}}


                <div class="table-responsive mt-3" style="margin-top: 3rem;">
                    <table class="table table-hover dataTable prettyTable plainDataTable" id="transaction_table"
                           data-pagination-limit="30" data-sorting-col="0" data-sorting-order="desc">
                        <thead>
                            <tr>
                                <th>Event type</th>
                                <th>Value</th>
                                <th>Page owner</th>
                                <th>Viewed by</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($events as $event)
                            <tr>
                                <th>{{$event->event_type}}</th>
                                <th>{{$event->event_value}}</th>
                                <th>{{$event->page_owner}}</th>
                                <th>{{$event->user_id}}</th>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>












            </div><!-- /.row -->
        </div><!-- /.content -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection
@section('javascript')
<script src='{{asset("public/plugins/datatables/ext/jquery.dataTables.js")}}'></script>
<script src='{{asset("public/plugins/datatables/ext/dataTables.bootstrap4.js")}}'></script>
<script src='{{asset("public/plugins/datatables/ext/dataTablesInit.js")}}'></script>
<script src='{{asset("public/plugins/daterangepicker/moment.js")}}'></script>
<script src='{{asset("public/plugins/daterangepicker/daterangepicker.js")}}'></script>
<script src='{{asset("public/js/testing.js")}}'></script>
@endsection

<script>
    import Select_column from "../../../public/plugins/datatables/extensions/TableTools/examples/select_column.html";
    export default {
        components: {Select_column}
    }
</script>

