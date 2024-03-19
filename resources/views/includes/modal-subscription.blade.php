<div class="modal fade" id="subscriptionForm" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
    <div class="modal-dialog modal- modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="card bg-theme-card shadow border-0">
                    <div class="card-header pb-2 border-0 position-relative" style="height: 100px; background: {{$settings->color_default}} @if ($user->cover != '')  url('{{Helper::getFile(config('path.cover').$user->cover)}}') no-repeat center center @endif; background-size: cover;">

                    </div>
                    <div class="card-body px-lg-5 py-lg-5 position-relative">

                        <div class=" text-center mb-3 position-relative modal-offset">
                            <img src="{{Helper::getFile(config('path.avatar').$user->avatar)}}" width="100" alt="{{$user->hide_name == 'yes' ? $user->username : $user->name}}" class="avatar-modal rounded-circle mb-1">
                            <h6 class="font-weight-light">
                                {!! trans('general.subscribe_month', ['price' => '<span class="font-weight-bold">'.Helper::amountFormatDecimal($user->plan('monthly', 'price'), true).'</span>']) !!} {{trans('general.unlocked_content')}} {{$user->hide_name == 'yes' ? $user->username : $user->name}}
                            </h6>
                        </div>

                        <div id="subscription_details">

                            <div class="text-center mb-2">
                                <h5>{{trans('general.what_will_you_get')}}</h5>
                            </div>

                            <ul class="list-unstyled">
                                <li><i class="fa fa-check mr-2 @if (auth()->user()->dark_mode == 'on') text-white @else text-primary @endif"></i> {{trans('general.full_access_content')}}</li>
                                <li><i class="fa fa-check mr-2 @if (auth()->user()->dark_mode == 'on') text-white @else text-primary @endif"></i> {{trans('general.direct_message_with_this_user')}}</li>
                                <li><i class="fa fa-check mr-2 @if (auth()->user()->dark_mode == 'on') text-white @else text-primary @endif"></i> {{trans('general.cancel_subscription_any_time')}}</li>
                            </ul>


                            <form method="post" action="{{route('buy/subscription')}}" id="formSubscription">
                                @csrf

                                <input type="hidden" name="id" value="{{$user->id}}"  />
                                <input name="interval" value="monthly" id="plan-monthly" class="d-none" type="radio">

                                @foreach ($plans as $plan)
                                    <input name="interval" value="{{ $plan->interval }}" id="plan-{{ $plan->interval }}" class="d-none" type="radio">
                                @endforeach

                                @foreach ($allPayment as $payment)

                                    @php

                                        if ($payment->recurrent == 'no') {
                                        $recurrent = '<br><small>'.trans('general.non_recurring').'</small>';
                                        } else if ($payment->id == 1) {
                                        $recurrent = '<br><small>'.trans('general.redirected_to_paypal_website').'</small>';
                                        } else {
                                        $recurrent = '<br><small>'.trans('general.automatically_renewed').' ('.$payment->name.')</small>';
                                        }

                                        if ($payment->type == 'card' ) {
                                        $paymentName = '<i class="far fa-credit-card mr-1"></i> '.trans('general.debit_credit_card').$recurrent;
                                        } else if ($payment->id == 1) {
                                        $paymentName = '<img src="'.url('public/img/payments', auth()->user()->dark_mode == 'off' ? $payment->logo : 'paypal-white.png').'" width="70"/> <small class="w-100 d-block">'.trans('general.redirected_to_paypal_website').'</small>';
                                        } else {
                                        $paymentName = '<img src="'.url('public/img/payments', $payment->logo).'" width="70"/>'.$recurrent;
                                        }

                                    @endphp


                                @endforeach


                                <div class="alert alert-danger display-none" id="error">
                                    <ul class="list-unstyled m-0" id="showErrors"></ul>
                                </div>

                                <div class="custom-control custom-control-alternative custom-checkbox">
                                    <input class="custom-control-input" id=" customCheckLogin" name="agree_terms" type="checkbox">
                                    <label class="custom-control-label" for=" customCheckLogin">
                                        <span>{{trans('general.i_agree_with')}} <a href="{{$settings->link_terms}}" target="_blank">{{trans('admin.terms_conditions')}}</a></span>
                                    </label>
                                </div>

                                @if (auth()->user()->isTaxable()->count())
                                    <ul class="list-group list-group-flush border-dashed-radius mt-3">
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

                                <div class="text-center">
                                    <button type="submit" class="dark-btn mt-4 w-100 subscriptionBtn" onclick="$('#plan-monthly').trigger('click');">
                                        <i></i> {{trans('general.subscribe_month', ['price' => Helper::amountFormatDecimal($user->plan('monthly', 'price'), true)])}}
                                    </button>

                                    @if ($plans->count())
                                        <a class="d-block my-3 btn-arrow-expand-bi" data-toggle="collapse" href="#collapseSubscriptionBundles" role="button" aria-expanded="false" aria-controls="collapseExample">
                                            <i class="bi bi-box mr-1"></i> {{ trans('general.subscription_bundles') }} <i class="bi bi-chevron-down transition-icon"></i>
                                        </a>

                                        <div class="collapse" id="collapseSubscriptionBundles">
                                            @foreach ($plans as $plan)
                                                <button type="submit" class="btn btn-primary mt-2 w-100 subscriptionBtn" onclick="$('#plan-{{$plan->interval}}').trigger('click');">
                                                    <i></i> {{trans('general.subscribe_'.$plan->interval, ['price' => Helper::amountFormatDecimal($plan->price, true)])}}
                                                </button>

                                                @if (Helper::calculateSubscriptionDiscount($plan->interval, $user->plan('monthly', 'price'), $plan->price) > 0)
                                                    <small class="@if (auth()->user()->dark_mode == 'on') text-white @else text-success @endif subscriptionDiscount">
                                                        <em>{{ Helper::calculateSubscriptionDiscount($plan->interval, $user->plan('monthly', 'price'), $plan->price) }}% {{ trans('general.discount') }}  </em>
                                                    </small>
                                                @endif

                                            @endforeach
                                        </div>

                                    @endif

                                    <div class="w-100 mt-2">
                                        <button type="button" class="btn e-none p-0" data-dismiss="modal">{{trans('admin.cancel')}}</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div id="spinner" style="display:none;text-align:center;"><img src="{{ asset('public/img/spinner.gif') }}" /></div>
                        <div class="text-center" id="card-fields" style="display:none;">
                            <input id="cardholder_name"  name="cardholder_name" class="theme form-control" tabindex="1" aria-required="true" required type="text" placeholder="Cardholder Name">
                            <div id="card-field-subscription" class="text-center mt-3"></div> 
                            {{--<button type="button" class="btn btn-primary mt-2 w-100" id="go-to-subscription-payment">Submit</button>--}}
                            <div class="text-center">
                                <button type="button" class="dark-btn mt-4 w-100" id="go-to-subscription-payment">
                                    <i></i> {{trans('general.subscribe_month', ['price' => Helper::amountFormatDecimal($user->plan('monthly', 'price'), true)])}}
                                </button>
                            {{--<button type="button" class="light-btn px-3 py-2 mt-4" onclick="javascript:location.reload();">{{trans('admin.cancel')}}</button>--}}
                                <div class="w-100 mt-2">
                                    <button type="button" class="btn e-none p-0" data-dismiss="modal">{{trans('admin.cancel')}}</button>
                                </div>
                            </div>
                            <div id="subscription-payment-request" class="text-center mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><!-- End Modal Subscription -->


{{--<div class="modal fade" id="cardForm" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
    <div class="modal-dialog modal- modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="card bg-theme-card shadow border-0">
                    <div class="card-body px-lg-5 py-lg-5 position-relative">

                        <div id="spinner" style="display:none;text-align:center;"><img src="{{ asset('public/img/spinner.gif') }}" /></div>
                        <div class="text-center" id="card-fields" style="display:none;">
                            <input id="cardholder_name"  name="cardholder_name" class="theme form-control" tabindex="1" aria-required="true" required type="text" placeholder="Cardholder Name">
                            <div id="card-field-subscription" class="text-center mt-3"></div> 
                            <button type="button" class="dark-btn px-3 py-2 mt-4" id="go-to-subscription-payment">Submit</button>
                            <button type="button" class="light-btn px-3 py-2 mt-4" onclick="javascript:location.reload();">{{trans('admin.cancel')}}</button>
                            <div id="subscription-payment-request" class="text-center mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>--}}<!-- End Modal Card -->
