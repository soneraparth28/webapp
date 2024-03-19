<div class="modal fade" id="tipForm" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
	<div class="modal-dialog modal- modal-dialog-centered modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-body p-0">
				<div class="card bg-theme-card shadow border-0">
					<div class="card-header pb-2 border-0 position-relative" style="height: 100px; background: {{$settings->color_default}} @if (auth()->user()->cover != '')  url('{{Helper::getFile(config('path.cover').auth()->user()->cover)}}') @endif no-repeat center center; background-size: cover;">

					</div>
					<div class="card-body px-lg-5 py-lg-5 position-relative">

						<div class="text-muted text-center mb-3 position-relative modal-offset">
							<img src="{{Helper::getFile(config('path.avatar').auth()->user()->avatar)}}" width="100" class="avatar-modal rounded-circle mb-1">
							<h6>
								{{trans('general.send_tip')}} <span class="userNameTip"></span>
							</h6>
						</div>

						<form method="post" action="{{url('send/tip')}}" id="formSendTip">

							<input type="hidden" name="id" class="userIdInput" value="{{auth()->user()->id}}"  />

							@if (request()->is('messages/*'))
								<input type="hidden" name="isMessage" value="1" />

							@elseif (request()->route()->named('live'))
								<input type="hidden" name="isLive" value="1" />

								@if ($live)
									<input type="hidden" name="liveId" value="{{ $live->id }}"  />
								@endif
                            @else
                                <input type="hidden" name="recipient_id" class="userIdInput" value="{{$user->id}}"  />
							@endif

							<input type="number" min="{{$settings->min_donation_amount}}" data-min-tip="{{$settings->min_tip_amount}}" data-max-tip="{{$settings->max_tip_amount}}"  autocomplete="off" id="onlyNumber" class="form-control mb-3 tipAmount" name="amount" placeholder="{{trans('general.tip_amount')}} ({{ __('general.minimum') }} {{ Helper::amountWithoutFormat($settings->min_tip_amount) }})">
							@csrf


                            @if ((int)Helper::userWallet('balance') !== 0)
                                <div class="custom-control custom-radio mb-3">
                                    <input name="payment_gateway_tip" value="wallet" id="live_radio0" class="custom-control-input" type="radio">
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
                                <input name="payment_gateway_tip" value="card" id="live_radio1" class="custom-control-input" type="radio" checked>
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



							<div class="bg-dark color-light border-radius-5px display-none mb-0 my-3 py-2 px-3" id="errorTip">
                                <ul class="list-unstyled m-0" id="showErrorsTip"></ul>
                            </div>
                            <div id="tip_details">
                                <div class="text-center">
                                    <button type="button" class="light-btn px-3 py-2 mt-4" data-dismiss="modal">{{trans('admin.cancel')}}</button>
                                    <button type="submit" id="tipBtn" class="dark-btn px-3 py-2 mt-4 tipBtn"><i></i> {{trans('auth.send')}}</button>
                                </div>
                            </div>
                            <div id="spinner" style="display:none;text-align:center;"><img src="{{ asset('public/img/spinner.gif') }}" /></div>
							<div class="text-center" id="card-fields" style="display:none;">
								<input id="cardholder_name"  name="cardholder_name" class="theme form-control" tabindex="1" aria-required="true" required type="text" placeholder="Cardholder Name">
								<div id="card-field-tip" class="text-center mt-3"></div> 
								
								<div class="text-center">
                                    <button type="button" class="light-btn px-3 py-2 mt-4" data-dismiss="modal">{{trans('admin.cancel')}}</button>
								    <button type="button" class="dark-btn px-3 py-2 mt-4" id="go-to-tip-payment"><i></i> {{trans('auth.send')}}</button>
								</div>
								<div id="tip-payment-request" class="text-center mt-3"></div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div><!-- End Modal Tip -->
