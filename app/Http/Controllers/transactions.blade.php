@extends('admin.layout')

@section('css')
    <link href="{{ asset('public/plugins/iCheck/all.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('public/plugins/tagsinput/jquery.tagsinput.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('public/plugins/datatables/ext/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('public/plugins/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h4>
           {{ trans('admin.admin') }} <i class="fa fa-angle-right margin-separator"></i> {{ trans('admin.transactions') }} ({{$data->total()}})
          </h4>

        </section>

        <!-- Main content -->
        <section class="content">
          @if (session('success_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                                </button>
               <i class="fa fa-check margin-separator"></i>  {{ session('success_message') }}
            </div>
        @endif


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


          <div class="row">
              <div class="col-xs-12">
                  <div class="card bg-theme-card">
                      <div class="card-body p-3">
                          <form name="myform" id="transaction_filter" method="post" action="{{route("testing_filter")}}" class="row">
                              <div class="col-xs-12 col-md-4 col-lg-2">
                                  <select name="filter_year" id="filter_year" class="form-control">
                                      @for($i = (int)date("Y"); $i > ((int)date("Y") - 10); $i--)
                                          <option value="{{$i}}">Year: {{$i}}</option>
                                      @endfor
                                  </select>
                              </div>
                              <div class="col-xs-12 col-md-4 col-lg-2">
                                  <select name="filter_month" id="filter_month" class="form-control">
                                      <option value="all">Month: All</option>
                                      @for($i = 1 ; $i <= 12; $i++)
                                          <option value="{{$i}}">Month: {{ucfirst(date("F", strtotime("2022-" . ($i < 10 ? "0" . $i : $i) . "-01")))}}</option>
                                      @endfor
                                  </select>
                              </div>
                              <div class="col-xs-12 col-md-4 col-lg-2">
                                  <select name="filter_country" id="filter_country" class="form-control">
                                      <option value="all">Country: All</option>
                                      @foreach($countries as $country)
                                          <option value="{{$country->country_name}}">Country: {{$country->country_name}}</option>
                                      @endforeach
                                  </select>
                              </div>
                          </form>

                          <div class="row">
                              <div class="col-xs-12 mt-3">
                                <div id="transactionFilterChart" style="width: 100%; min-height: 300px;"></div>
                              </div>
                          </div>

                      </div>
                  </div>
              </div>

              <div class="col-xs-12 mt-3">
                  <div class="card bg-theme-card">
                      <div class="card-body p-3">

                          <div class="mt-1">
                              <p style="font-size: 18px; font-weight: bold;">Table showing data for period</p>
                              <input type="text" class="DP_RANGE form-control" value="{{date("d/m/Y") . " - " . date("d/m/Y")}}" style="margin-top: .25rem;"
                                     name="date_selector" data-preset-start-time="today"/>
                          </div>


                          <div class="table-responsive mt-3">
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
                                      <th>Invoice lookup</th>
                                  </tr>
                                  </thead>
                                  <tbody></tbody>
                              </table>
                          </div>
                      </div>
                  </div>
              </div>
          </div>





        	<div class="row mt-4">
            <div class="col-xs-12">
              <div class="card bg-theme-card box">
                  <div class="card-body">
                <div class="box">
                <div class="box-header">
                  <h3 class="box-title">
                  		{{ trans('admin.transactions') }}
                  	</h3>

                  <div class="box-tools">
                   @if( $data->total() !=  0 )
                      <!-- form -->
                      <form role="search" autocomplete="off" action="{{ url('panel/admin/transactions') }}" method="get">
  	                 <div class="input-group input-group-sm w-150">
  	                  <input type="text" name="q" class="form-control pull-right" placeholder="{{ trans('admin.transaction_id') }}">

  	                  <div class="input-group-btn">
  	                    <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
  	                  </div>
  	                </div>
                  </form><!-- form -->
                  @endif
                </div>

                </div><!-- /.box-header -->

                <div class="box-body table-responsive no-padding">
                  <table class="table table-hover">
               <tbody>

               	@if ($data->total() !=  0 && $data->count() != 0)
                   <tr>
                      <th class="active">ID</th>
                      <th class="active">{{ trans('admin.transaction_id') }}</th>
                      <th class="active">{{ trans('general.user') }}</th>
                      <th class="active">{{ trans('general.creator') }}</th>
                      <th class="active">{{ trans('admin.type') }}</th>
                      <th class="active">{{ trans('admin.amount') }}</th>
                      <th class="active">{{ trans('admin.earnings_admin') }}</th>
                      <th class="active">{{ trans('general.payment_gateway') }}</th>
                      <th class="active">{{ trans('admin.date') }}</th>
                      <th class="active">{{ trans('admin.status') }}</th>
                    </tr><!-- /.TR -->

                  @foreach ($data as $transaction)
                    <tr>
                      <td>{{ str_pad($transaction->id, 4, "0", STR_PAD_LEFT) }}</td>
                      <td>{{ $transaction->txn_id }}</td>
                      <td>
                        @if (! isset($transaction->user()->username))
                          <em>{{ trans('general.no_available') }}</em>
                        @else
                          <a href="{{url($transaction->user()->username)}}" target="_blank">
                          {{$transaction->user()->name}} <i class="fa fa-external-link-square"></i>
                        </a>
                        @endif
                    </td>
                    <td>
                      @if (! isset($transaction->subscribed()->username))
                        <em>{{ trans('general.no_available') }}</em>
                      @else
                        <a href="{{url($transaction->subscribed()->username)}}" target="_blank">
                        {{$transaction->subscribed()->name}} <i class="fa fa-external-link-square"></i>
                      </a>
                      @endif
                  </td>
                      <td>{{ __('general.'.$transaction->type) }}
                      </td>
                      <td>{{ Helper::amountFormatDecimal($transaction->amount) }}</td>
                      <td>
                        {{ Helper::amountFormatDecimal($transaction->earning_net_admin) }}

                        @if ($transaction->referred_commission)
                          <span class="margin-left-5" data-toggle="tooltip" data-placement="top" title="{{trans('general.referral_commission_applied')}}">
                            <i class="fa fa-info-circle"></i>
                          </span>
                        @endif
                      </td>
                      <td>{{ $transaction->payment_gateway }}</td>
                      <td>{{ Helper::formatDate($transaction->created_at) }}</td>
                      <td>
                        @if ($transaction->approved == '0')
                        <span class="label label-warning ml-1 mt-1">{{trans('admin.pending')}}</span>
                      @elseif ($transaction->approved == '1')
                        <span class="label label-success ml-1 mt-1">{{trans('admin.approved')}}</span>
                      @else
                        <span class="label label-danger ml-1 mt-1">{{trans('general.canceled')}}</span>
                      @endif

                    @if ($transaction->approved == '1')
                          {!! Form::open([
        			            'method' => 'POST',
        			            'url' => url('panel/admin/transactions/cancel', $transaction->id),
        			            'class' => 'displayInline'
  				              ]) !!}
  	                   {!! Form::submit(trans('admin.cancel'), ['class' => 'btn btn-danger btn-xs padding-btn cancel_payment ml-1 mt-1']) !!}

  	        	           {!! Form::close() !!}
                       @endif
                       </td>
                    </tr><!-- /.TR -->
                    @endforeach

                    @else
                    <hr />
                    	<h3 class="text-center no-found">{{ trans('general.no_results_found') }}</h3>

                    @endif

                  </tbody>

                  </table>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
                </div><!-- /.card-body -->
              </div><!-- /.card -->



              @if ($data->hasPages())
             {{ $data->links() }}
             @endif
            </div>
          </div>
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
@endsection

