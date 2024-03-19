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

            <div class="d-lg-none flex-row-around mt-5 pr-2 mb-5 pb-4 position-relative">
                <img  src="{{url('public/img', $settings->logo)}}" class="mobile-sidebar-logo">
                <button type="button" class="navbar-toggler close-menu-mobile" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
                        style="position: absolute; top: 5px; right: 5px;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="flex-col-between flex-align-center mobile-menu-box-container">
                <a class="mobile-menu-box-item nav-link" href="https://mator.io">Home</a>
                <a class="mobile-menu-box-item nav-link" href="https://mator.io/platform">Platform</a>
                <a class="mobile-menu-box-item nav-link" href="https://mator.io/apply">Apply</a>
                <a class="mobile-menu-box-item nav-link" href="https://mator.io/contact/">Contact</a>
                <a class="mobile-menu-box-item nav-link @if (request()->is('signup')) active @endif" href="{{url("/signup")}}">{{trans("auth.signup")}}</a>
                <a class="mobile-menu-box-item nav-link @if (request()->is('login')) active @endif" href="{{url("/login")}}">{{trans("auth.login")}}</a>
            </div>

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



                    <div style="margin: .4rem 1rem 0 2rem; width: 50%;">
                        <form name="myform" id="transaction_filter" method="post" action="{{route("testing_filter")}}" class="row">
                            <div style="display: flex; flex-direction: row; justify-content: center; padding: 0 5.rem; ">
                                <div style="width: 33.3%; ">
                                    <select name="filter_year" id="filter_year" class="form-control">
                                        @for($i = 2022; $i > 2010; $i--)
                                            <option value="{{$i}}">Year: {{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div style="width: 33.3%; ">
                                    <select name="filter_month" id="filter_month" class="form-control">
                                        <option value="all">Month: All</option>
                                        @for($i = 1 ; $i <= 12; $i++)
                                            <option value="{{$i}}">Month: {{ucfirst(date("F", strtotime("2022-" . ($i < 10 ? "0" . $i : $i) . "-01")))}}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div style="width: 33.3%; ">
                                    <select name="filter_country" id="filter_country" class="form-control">
                                        <option value="all">Country: All</option>
                                        @foreach($countries as $country)
                                            <option value="{{$country->country_name}}">Country: {{$country->country_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>


                    <div class="col-12 mt-3">
                        <div id="transactionFilterChart" style="width: 100%; min-height: 300px;"></div>
                    </div>

                    <div style="margin-top: 1rem;">
                        <p style="font-size: 18px; font-weight: bold;">Table showing data for period</p>
                        <input type="text" class="DP_RANGE form-control" value="{{date("d/m/Y") . " - " . date("d/m/Y")}}" style="margin-top: .25rem;"
                               name="date_selector" data-preset-start-time="today"/>
                    </div>


                    <div class="table-responsive mt-3" style="margin-top: 3rem;">
                        <table class="table table-hover dataTable prettyTable plainDataTable" id="transaction_table"
                               data-pagination-limit="30" data-sorting-col="0" data-sorting-order="desc">
                            <thead>
                                <tr>
                                    <th>Country</th>
                                    <th>Sales incl. VAT</th>
                                    <th>Sales excl. VAT</th>
                                    <th>VAT rate</th>
                                    <th>VAT</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>












                </div><!-- /.row -->
            </div><!-- /.content -->
        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->

@endsection
@section('javascript')
    <script src='{{asset("public/plugins/ApexChart/apexcharts.min.js")}}'></script>
    <script src='{{asset("public/plugins/ApexChart/renderApexChart.js")}}'></script>
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

