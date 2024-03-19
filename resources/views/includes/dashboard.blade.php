@extends('layouts.app')

@section('title') {{trans('admin.dashboard')}} -@endsection

@section('content')

    <?php $pageTitle = "Dashboard"; ?>

    <div class="session-main-wrapper">
        @include("includes.menus.main-creator-menu")




        <div class="session-main-page-wrapper">
            @include("includes.menus.main-creator-navbar")

            <div class="session-main-content-wrapper">

                <div class="row mt-3">
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="p-4 item-container-box bg-dark">
                            <div class="flex-row-between">
                                <div class="flex-col-start">
                                    <p class="font-18 color-light">Total balance</p>
                                    <p class="font-25 font-weight-bold color-light">$0</p>
                                </div>
                                <div class="flex-col-start">
                                    <button class="light-btn font-20">
                                        <i class="feather icon-dollar-sign font-20 mr-2"></i>
                                        Withdraw
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="p-4 item-container-box border-light-gray">
                            <div class="flex-row-between">
                                <div class="flex-col-start">
                                    <p class="font-18">Total balance</p>
                                    <p class="font-25 font-weight-bold">$0</p>
                                </div>
                                <div class="flex-col-start">
                                    <button class="lightest-gray-btn font-20">
                                        <i class="feather icon-dollar-sign font-20 mr-2"></i>
                                        Withdraw
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="p-4 item-container-box border-light-gray">
                            <div class="flex-row-between">
                                <div class="flex-col-start">
                                    <p class="font-18">Total balance</p>
                                    <p class="font-25 font-weight-bold">$0</p>
                                </div>
                                <div class="flex-col-start">
                                    <button class="lightest-gray-btn font-20">
                                        <i class="feather icon-dollar-sign font-20 mr-2"></i>
                                        Withdraw
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="col-12 col-lg-2 mt-1">
                        <div class="p-4 item-container-box border-light-gray">
                            <div class="flex-col-start">
                                <div class="flex-row-start flex-align-center">
                                    <i class="fa fa-money-bill"></i>
                                    <p class="font-18 mb-0 ml-2">Tip earnings</p>
                                </div>
                                <p class="font-25 font-weight-bold">$0</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-2 mt-1">
                        <div class="p-4 item-container-box border-light-gray">
                            <div class="flex-col-start">
                                <div class="flex-row-start flex-align-center">
                                    <i class="fa fa-money-bill"></i>
                                    <p class="font-18 mb-0 ml-2">Active subs</p>
                                </div>
                                <p class="font-25 font-weight-bold">$0</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-3 mt-1">
                        <div class="p-4 item-container-box border-light-gray">
                            <div class="flex-col-start">
                                <div class="flex-row-start flex-align-center">
                                    <i class="fa fa-money-bill"></i>
                                    <p class="font-18 mb-0 ml-2">Sub earnings</p>
                                </div>
                                <p class="font-25 font-weight-bold">$0</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-3 mt-1">
                        <div class="p-4 item-container-box border-light-gray">
                            <div class="flex-col-start">
                                <div class="flex-row-start flex-align-center">
                                    <i class="fa fa-money-bill"></i>
                                    <p class="font-18 mb-0 ml-2">Paid content earnings</p>
                                </div>
                                <p class="font-25 font-weight-bold">$0</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-2 mt-1">
                        <div class="p-4 item-container-box border-light-gray">
                            <div class="flex-col-start">
                                <div class="flex-row-start flex-align-center">
                                    <i class="fa fa-money-bill"></i>
                                    <p class="font-18 mb-0 ml-2">Net earnings</p>
                                </div>
                                <p class="font-25 font-weight-bold">$0</p>
                            </div>
                        </div>
                    </div>
                </div>



            </div>
        </div>




    </div>



{{--    <section class="section section-sm">--}}
{{--    </section>--}}

















{{--<section class="section section-sm">--}}
{{--    <div class="container">--}}
{{--      <div class="row justify-content-center text-center mb-sm">--}}
{{--        <div class="col-lg-8 py-5">--}}
{{--          <h2 class="mb-0 font-montserrat"><i class="bi bi-speedometer2 mr-2"></i> {{trans('admin.dashboard')}}</h2>--}}
{{--          <p class="lead text-muted mt-0">{{trans('general.dashboard_desc')}}</p>--}}
{{--        </div>--}}
{{--      </div>--}}
{{--      <div class="row">--}}

{{--        <div class="col-lg-12 mb-5 mb-lg-0">--}}

{{--          <div class="content">--}}
{{--            <div class="row">--}}
{{--              <div class="col-lg-4 mb-2">--}}
{{--                <div class="card">--}}
{{--                  <div class="card-body">--}}
{{--                    <h4><i class="fas fa-hand-holding-usd mr-2 text-primary icon-dashboard"></i> {{ Helper::amountFormatDecimal($earningNetUser) }}</h4>--}}
{{--                    <small>{{ trans('admin.earnings_net') }}</small>--}}
{{--                  </div>--}}
{{--                </div><!-- card 1 -->--}}
{{--              </div><!-- col-lg-4 -->--}}

{{--              <div class="col-lg-4 mb-2">--}}
{{--                <div class="card">--}}
{{--                  <div class="card-body">--}}
{{--                    <h4><i class="fas fa-wallet mr-2 text-primary icon-dashboard"></i> {{ Helper::amountFormatDecimal(Auth::user()->balance) }}</h4>--}}
{{--                    <small>{{ trans('general.balance') }}--}}
{{--                      @if (Auth::user()->balance >= $settings->amount_min_withdrawal)--}}
{{--                      <a href="{{ url('settings/withdrawals')}}" class="link-border"> {{ trans('general.make_withdrawal') }}</a>--}}
{{--                    @endif--}}
{{--                    </small>--}}
{{--                  </div>--}}
{{--                </div><!-- card 1 -->--}}
{{--              </div><!-- col-lg-4 -->--}}

{{--              <div class="col-lg-4 mb-2">--}}
{{--                <div class="card">--}}
{{--                  <div class="card-body">--}}
{{--                    <h4><i class="fas fa-users mr-2 text-primary icon-dashboard"></i> <span title="{{$subscriptionsActive}}">{{ Helper::formatNumber($subscriptionsActive) }}</span></h4>--}}
{{--                    <small>{{ trans('general.subscriptions_active') }}</small>--}}
{{--                  </div>--}}
{{--                </div><!-- card 1 -->--}}
{{--              </div><!-- col-lg-4 -->--}}



{{--              <div class="col-lg-4 mb-2"> <!-- Extra columns start -->--}}
{{--                <div class="card">--}}
{{--                  <div class="card-body">--}}
{{--                    <h4><i class="fas fa-arrows-rotate mr-2 text-primary icon-dashboard"></i> {{ Helper::amountFormatDecimal($earningsFromSubscriptions) }}</h4>--}}
{{--                    <small>{{ trans('general.earnings_subscriptions') }}</small>--}}
{{--                  </div>--}}
{{--                </div><!-- card 1 -->--}}
{{--              </div><!-- col-lg-4 -->--}}

{{--              <div class="col-lg-4 mb-2">--}}
{{--                <div class="card">--}}
{{--                  <div class="card-body">--}}
{{--                    <h4><i class="fa fa-dollar mr-2 text-primary icon-dashboard"></i> {{ Helper::amountFormatDecimal($earningsFromTips) }}</h4>--}}
{{--                      <small>{{ trans('general.earnings_tips') }}</small>--}}
{{--                  </div>--}}
{{--                </div><!-- card 1 -->--}}
{{--              </div><!-- col-lg-4 -->--}}

{{--              <div class="col-lg-4 mb-2">--}}
{{--                <div class="card">--}}
{{--                  <div class="card-body">--}}
{{--                    <h4><i class="fas fa-lock mr-2 text-primary icon-dashboard"></i> {{ Helper::amountFormatDecimal($earningsFromPpv) }}</h4>--}}
{{--                      <small>{{ trans('general.earnings_ppv') }}</small>--}}
{{--                  </div>--}}
{{--                </div><!-- card 1 -->--}}
{{--              </div><!-- col-lg-4 --> <!-- Extra columns end -->--}}

{{--              <div class="col-lg-4 mb-2">--}}
{{--                <div class="card">--}}
{{--                  <div class="card-body">--}}
{{--                    <h6 class="{{$stat_revenue_today > 0 ? 'text-success' : 'text-danger' }}">--}}
{{--                      {{ Helper::amountFormatDecimal($stat_revenue_today) }}--}}
{{--                      <small class="float-right ml-2">--}}
{{--                        <i class="bi bi-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="{{ trans('general.compared_yesterday') }}"></i>--}}
{{--                      </small>--}}
{{--                        {!! Helper::PercentageIncreaseDecrease($stat_revenue_today, $stat_revenue_yesterday) !!}--}}
{{--                    </h6>--}}
{{--                    <small>{{ trans('general.revenue_today') }}</small>--}}
{{--                  </div>--}}
{{--                </div><!-- card 1 -->--}}
{{--              </div><!-- col-lg-4 -->--}}

{{--              <div class="col-lg-4 mb-2">--}}
{{--                <div class="card">--}}
{{--                  <div class="card-body">--}}
{{--                    <h6 class="{{$stat_revenue_week > 0 ? 'text-success' : 'text-danger' }}">--}}
{{--                      {{ Helper::amountFormatDecimal($stat_revenue_week) }}--}}
{{--                      <small class="float-right ml-2">--}}
{{--                        <i class="bi bi-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="{{ trans('general.compared_last_week') }}"></i>--}}
{{--                      </small>--}}
{{--                        {!! Helper::PercentageIncreaseDecrease($stat_revenue_week, $stat_revenue_last_week) !!}--}}
{{--                    </h6>--}}
{{--                    <small>{{ trans('general.revenue_week') }}</small>--}}
{{--                  </div>--}}
{{--                </div><!-- card 1 -->--}}
{{--              </div><!-- col-lg-4 -->--}}

{{--              <div class="col-lg-4 mb-2">--}}
{{--                <div class="card">--}}
{{--                  <div class="card-body">--}}
{{--                    <h6 class="{{$stat_revenue_month > 0 ? 'text-success' : 'text-danger' }}">--}}
{{--                      {{ Helper::amountFormatDecimal($stat_revenue_month) }}--}}
{{--                      <small class="float-right ml-2">--}}
{{--                        <i class="bi bi-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="{{ trans('general.compared_last_month') }}"></i>--}}
{{--                      </small>--}}
{{--                        {!! Helper::PercentageIncreaseDecrease($stat_revenue_month, $stat_revenue_last_month) !!}--}}
{{--                    </h6>--}}
{{--                    <small>{{ trans('general.revenue_month') }}</small>--}}
{{--                  </div>--}}
{{--                </div><!-- card 1 -->--}}
{{--              </div><!-- col-lg-4 -->--}}

{{--              <div class="col-lg-12 mt-3 py-4">--}}
{{--                 <div class="card">--}}
{{--                   <div class="card-body">--}}
{{--                     <h4 class="mb-4">{{ trans('general.earnings_this_month') }} ({{ $month }})</h4>--}}
{{--                     <div style="height: 350px">--}}
{{--                      <canvas id="Chart"></canvas>--}}
{{--                    </div>--}}
{{--                   </div>--}}
{{--                 </div>--}}
{{--              </div>--}}
{{--            </div><!-- end row -->--}}
{{--          </div><!-- end content -->--}}

{{--        </div><!-- end col-md-6 -->--}}

{{--      </div>--}}
{{--    </div>--}}
{{--  </section>--}}
@endsection

{{--@section('javascript')--}}
{{--  <script src="{{ asset('public/js/Chart.min.js') }}"></script>--}}

{{--  <script type="text/javascript">--}}

{{--function decimalFormat(nStr)--}}
{{--{--}}
{{--  @if ($settings->decimal_format == 'dot')--}}
{{--	 $decimalDot = '.';--}}
{{--	 $decimalComma = ',';--}}
{{--	 @else--}}
{{--	 $decimalDot = ',';--}}
{{--	 $decimalComma = '.';--}}
{{--	 @endif--}}

{{--   @if ($settings->currency_position == 'left')--}}
{{--   currency_symbol_left = '{{$settings->currency_symbol}}';--}}
{{--   currency_symbol_right = '';--}}
{{--   @else--}}
{{--   currency_symbol_right = '{{$settings->currency_symbol}}';--}}
{{--   currency_symbol_left = '';--}}
{{--   @endif--}}

{{--    nStr += '';--}}
{{--    x = nStr.split('.');--}}
{{--    x1 = x[0];--}}
{{--    x2 = x.length > 1 ? $decimalDot + x[1] : '';--}}
{{--    var rgx = /(\d+)(\d{3})/;--}}
{{--    while (rgx.test(x1)) {--}}
{{--        x1 = x1.replace(rgx, '$1' + $decimalComma + '$2');--}}
{{--    }--}}
{{--    return currency_symbol_left + x1 + x2 + currency_symbol_right;--}}
{{--  }--}}

{{--  function transparentize(color, opacity) {--}}
{{--			var alpha = opacity === undefined ? 0.5 : 1 - opacity;--}}
{{--			return Color(color).alpha(alpha).rgbString();--}}
{{--		}--}}

{{--  var init = document.getElementById("Chart").getContext('2d');--}}

{{--  const gradient = init.createLinearGradient(0, 0, 0, 300);--}}
{{--                    gradient.addColorStop(0, '{{$settings->color_default}}');--}}
{{--                    gradient.addColorStop(1, '{{$settings->color_default}}2b');--}}

{{--  const lineOptions = {--}}
{{--                        pointRadius: 4,--}}
{{--                        pointHoverRadius: 6,--}}
{{--                        hitRadius: 5,--}}
{{--                        pointHoverBorderWidth: 3--}}
{{--                    }--}}

{{--  var ChartArea = new Chart(init, {--}}
{{--      type: 'line',--}}
{{--      data: {--}}
{{--          labels: [{!!$label!!}],--}}
{{--          datasets: [{--}}
{{--              label: '{{trans('general.earnings')}}',--}}
{{--              backgroundColor: gradient,--}}
{{--              borderColor: '{{$settings->color_default}}',--}}
{{--              data: [{!!$data!!}],--}}
{{--              borderWidth: 2,--}}
{{--              fill: true,--}}
{{--              lineTension: 0.4,--}}
{{--              ...lineOptions--}}
{{--          }]--}}
{{--      },--}}
{{--      options: {--}}
{{--          scales: {--}}
{{--              yAxes: [{--}}
{{--                  ticks: {--}}
{{--                    min: 0, // it is for ignoring negative step.--}}
{{--                     display: true,--}}
{{--                      maxTicksLimit: 8,--}}
{{--                      padding: 10,--}}
{{--                      beginAtZero: true,--}}
{{--                      callback: function(value, index, values) {--}}
{{--                          return '@if($settings->currency_position == 'left'){{ $settings->currency_symbol }}@endif' + value + '@if($settings->currency_position == 'right'){{ $settings->currency_symbol }}@endif';--}}
{{--                      }--}}
{{--                  }--}}
{{--              }],--}}
{{--              xAxes: [{--}}
{{--                gridLines: {--}}
{{--                  display:false--}}
{{--                },--}}
{{--                display: true,--}}
{{--                ticks: {--}}
{{--                  maxTicksLimit: 15,--}}
{{--                  padding: 5,--}}
{{--                }--}}
{{--              }]--}}
{{--          },--}}
{{--          tooltips: {--}}
{{--            mode: 'index',--}}
{{--            intersect: false,--}}
{{--            reverse: true,--}}
{{--            backgroundColor: '#000',--}}
{{--            xPadding: 16,--}}
{{--            yPadding: 16,--}}
{{--            cornerRadius: 4,--}}
{{--            caretSize: 7,--}}
{{--              callbacks: {--}}
{{--                  label: function(t, d) {--}}
{{--                      var xLabel = d.datasets[t.datasetIndex].label;--}}
{{--                      var yLabel = t.yLabel == 0 ? decimalFormat(t.yLabel) : decimalFormat(t.yLabel.toFixed(2));--}}
{{--                      return xLabel + ': ' + yLabel;--}}
{{--                  }--}}
{{--              },--}}
{{--          },--}}
{{--          hover: {--}}
{{--            mode: 'index',--}}
{{--            intersect: false--}}
{{--          },--}}
{{--          legend: {--}}
{{--              display: false--}}
{{--          },--}}
{{--          responsive: true,--}}
{{--          maintainAspectRatio: false--}}
{{--      }--}}
{{--  });--}}
{{--  </script>--}}
{{--  @endsection--}}
