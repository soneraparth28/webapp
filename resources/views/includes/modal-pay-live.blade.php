<!-- Start Modal payPerViewForm -->
<div class="modal fade" id="payLiveForm" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
	<div class="modal-dialog modal- modal-dialog-centered modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-body p-0">
				<div class="card bg-theme-card shadow border-0">
					<div class="card-header pb-2 border-0 position-relative" style="height: 100px; background: {{$settings->color_default}} @if (auth()->user()->cover != '')  url('{{Helper::getFile(config('path.cover').auth()->user()->cover)}}') @endif no-repeat center center; background-size: cover;">

					</div>
					<div class="card-body px-lg-5 py-lg-5 position-relative">

						<div class="mb-4 text-center position-relative">
							<div class="text-center position-relative mb-3 modal-offset">
								<div class="wrapper-live">
									<span class="live-span">{{ trans('general.live') }}</span>
									<div class="live-pulse"></div>
									<img src="{{Helper::getFile(config('path.avatar').$creator->avatar)}}" width="100" class="rounded-circle mb-1">
								</div>
						</div>

							<i class="bi bi-broadcast mr-1"></i> <strong>{{trans('general.Join_live_stream')}} {{ '@'.$creator->username }}</strong>

							<small class="w-100 d-block">
								"{{ $live->name }}"
							</small>
						</div>

						<form method="post" action="{{url('send/payment/live')}}" id="formPayLive">

							<input type="hidden" name="id" value="{{ $live->id }}" />
							@csrf


                            @if ((int)Helper::userWallet('balance') !== 0)
                                <div class="custom-control custom-radio mb-3">
                                    <input name="payment_gateway_live" value="wallet" id="live_radio0" class="custom-control-input" type="radio">
                                    <label class="custom-control-label" for="live_radio0">
									<span>
										<strong>
										<i class="fas fa-wallet mr-1 icon-sm-radio"></i> {{ __('general.wallet') }}
										<span class="w-100 d-block font-weight-light">
											{{ __('general.available_balance') }}: <span class="font-weight-bold mr-1 balanceWallet">{{Helper::userWallet()}}</span>

											@if ($settings->wallet_format <> 'real_money')
                                                <i class="bi bi-info-circle text-muted" data-toggle="tooltip" data-placement="top" title="{{Helper::equivalentMoney($settings->wallet_format)}}"></i>
                                            @endif
										</span>
									</strong>
									</span>
                                    </label>
                                </div>
                            @endif

                            <div class="custom-control custom-radio">
                                <input name="payment_gateway_live" value="card" id="live_radio1" class="custom-control-input" type="radio" checked>
                                <label class="custom-control-label" for="live_radio1">
									<span>
										<strong>
										    <i class="fas fa-wallet mr-1 icon-sm-radio"></i> {{ __('general.card') }}
									    </strong>
									</span>
                                </label>
                            </div>



                            @if (auth()->user()->isTaxable()->count())
                                <ul class="list-group list-group-flush border-dashed-radius">
                                    @foreach (auth()->user()->isTaxable() as $tax)
                                        <li class="list-group-item py-1 list-taxes">
                                            <div class="row">
                                                <div class="col">
                                                    <small>{{ $tax->name }} {{ $tax->percentage }}% {{ trans('general.applied_price') }}</small>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif



							<div class="bg-dark color-light border-radius-5px display-none mb-0 my-3 py-2 px-3" id="errorPayLive">
                                <ul class="list-unstyled m-0" id="showErrorsPayLive"></ul>
                            </div>
							<div id="livestream_details">
								<div class="text-center">
									<button type="submit" id="payLiveBtn" class="dark-btn py-2 px-3 mt-4 payLiveBtn">
										<i></i> {{trans('general.pay')}} {{Helper::amountFormatDecimal($live->price, true)}}
									</button>

									<div class="w-100 mt-2">
										<a href="{{ url('/') }}" class="btn e-none p-0">{{trans('admin.cancel')}}</a>
									</div>
								</div>
							</div>

							<div id="spinner" style="display:none;text-align:center;"><img src="{{ asset('public/img/spinner.gif') }}" /></div>
							<div class="text-center" id="card-fields" style="display:none;">
								<input id="cardholder_name"  name="cardholder_name" class="theme form-control" tabindex="1" aria-required="true" required type="text" placeholder="Cardholder Name">
								<div id="card-field-livestream" class="text-center mt-3"></div> 
								
								<div class="text-center">
									<button type="button" class="dark-btn mt-4 w-100" id="go-to-livestream-payment">
										<i></i> {{trans('general.pay')}} {{Helper::amountFormatDecimal($live->price, true)}}
									</button>
									<div class="w-100 mt-2">
										<a href="{{ url('/') }}" class="btn e-none p-0">{{trans('admin.cancel')}}</a>
									</div>
								</div>
								<div id="livestream-payment-request" class="text-center mt-3"></div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div><!-- End Modal PayLive -->
