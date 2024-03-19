

{{--                                            @if ($userPlanMonthlyActive--}}
{{--                                                && $user->verified_id == 'yes'--}}
{{--                                                || $user->free_subscription == 'yes'--}}
{{--                                                && $user->verified_id == 'yes')--}}
@if (!$isMyOwnPage && ($userPlanMonthlyActive || $user->free_subscription == 'yes'))

    @if (auth()->check() && !$isMyOwnPage
        && ! $checkSubscription
        && ! $paymentIncomplete
        && $user->free_subscription == 'no'
        )
        <a href="javascript:void(0);" data-toggle="modal" data-target="#subscriptionForm" class="dark-btn mr-2 hideOnMobileBlock">
            <i class="feather icon-unlock mr-1 color-light"></i> {{trans('general.subscribe_month', ['price' => Helper::amountFormatDecimal($user->plan('monthly', 'price'))])}}
        </a>


    @elseif (auth()->check() && !$isMyOwnPage && $checkSubscription)

        @if ($checkSubscription->stripe_id == '' && $checkSubscription->free == 'yes')
            {!! Form::open([
              'method' => 'POST',
              'url' => "subscription/free/cancel/$checkSubscription->id",
              'class' => 'd-inline formCancel'
            ]) !!}

            {!! Form::button('<i class="feather icon-user-check mr-1"></i> '.trans('general.your_subscribed'), ['data-expiration' => trans('general.confirm_cancel_subscription'), 'class' => ' dark-btn mr-1 cancelBtn subscriptionActive']) !!}
            {!! Form::close() !!}


        @elseif ($paymentGatewaySubscription == 'Wallet' && $checkSubscription->cancelled == 'no')
            {!! Form::open([
              'method' => 'POST',
              'url' => "subscription/wallet/cancel/$checkSubscription->id",
              'class' => 'd-inline formCancel'
            ]) !!}

            {!! Form::button('<i class="feather icon-user-check mr-1"></i> '.trans('general.your_subscribed'), ['data-expiration' => trans('general.subscription_expire').' '.Helper::formatDate($checkSubscription->ends_at), 'class' => 'dark-btn mr-1 cancelBtn subscriptionActive']) !!}
            {!! Form::close() !!}


        @elseif ($checkSubscription->cancelled == 'yes' || $checkSubscription->stripe_status == 'canceled')
            <a href="javascript:void(0);" class="dark-btn mr-2 disabled">
                <i class="feather icon-user-check mr-1 color-light"></i> {{trans('general.subscribed_until')}} {{ Helper::formatDate($checkSubscription->ends_at) }}
            </a>
        @endif


    

    @elseif (auth()->check() && !$isMyOwnPage && $user->free_subscription == 'yes')
        <a href="javascript:void(0);" data-toggle="modal" data-target="#subscriptionFreeForm" class="dark-btn mr-2">
            <i class="feather icon-user-plus mr-1"></i> {{trans('general.subscribe_for_free')}}
        </a>
    @elseif (auth()->guest() && $user->updates()->count() != 0)
        <a href="{{url('login')}}" data-toggle="modal" data-target="#loginFormModal" class="dark-btn-mobile-light mr-2">
            @if ($user->free_subscription == 'yes')
                <i class="feather icon-user-plus mr-1 color-light"></i> {{trans('general.subscribe_for_free')}}
            @else
                <i class="feather icon-unlock mr-1 color-light"></i> {{trans('general.subscribe_month', ['price' => Helper::amountFormatDecimal($user->plan('monthly', 'price'))])}}
            @endif
        </a>
    @endif

@endif
