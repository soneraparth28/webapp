<div class="flex-col-start flex-align-center">
    <div class="flex-row-start flex-align-center flex-nowrap">
        <a href="{{url("settings/page")}}" class="mb-0 font-16 px-2 pb-3 border-bottom cursor-pointer hover-opacity-8 font-weight-bold
        @if(in_array(request()->path(), ["settings/page", "privacy/security", "my/referrals"])) color-dark border-dark @else color-light-gray border-med-light-gray @endif">
            Account
        </a>

        <a href="{{url("settings/subscription")}}" class="mb-0 font-16 px-2 pb-3 border-bottom cursor-pointer hover-opacity-8 font-weight-bold
        @if(in_array(request()->path(), ["settings/subscription", "my/subscribers", "my/subscriptions"])) color-dark border-dark @else color-light-gray border-med-light-gray @endif">
            Subscriptions
        </a>

        <a href="{{url("settings/addons")}}" class="mb-0 font-16 px-2 pb-3 border-bottom cursor-pointer hover-opacity-8 font-weight-bold
        @if(in_array(request()->path(), ["settings/addons", "my/payments", "my/payments/received", "settings/withdrawals"]) || str_contains(request()->path(), "settings/addon/"))
            color-dark border-dark @else color-light-gray border-med-light-gray @endif">
            Payment
        </a>
    </div>

    <div class="flex-row-start flex-align-center flex-nowrap overflow-auto hideScrollBar mb-3 hideOnDesktopFlex mt-2 mxw-100">
        @if(in_array(request()->path(), ["settings/page", "privacy/security", "my/referrals"]))
            <a href="{{url("settings/page")}}" class="lightest-gray-btn py-2 px-3 flex-row-start flex-align-center flex-nowrap">
                <i class="feather icon-user font-16 mr-2 @if(!request()->is("settings/page")) color-light-gray @endif"></i>
                <p class="mb-0 text-nowrap @if(!request()->is("settings/page")) color-light-gray @endif">My profile</p>
            </a>
            <a href="{{url("privacy/security")}}" class="lightest-gray-btn py-2 px-3 ml-3 flex-row-start flex-align-center flex-nowrap">
                <i class="bi bi-shield-check font-16 mr-2 @if(!request()->is("privacy/security")) color-light-gray @endif"></i>
                <p class="mb-0 text-nowrap @if(!request()->is("privacy/security")) color-light-gray @endif">{{trans('general.privacy')}}</p>
            </a>
            <a href="{{url("my/referrals")}}" class="lightest-gray-btn py-2 px-3 ml-3 flex-row-start flex-align-center flex-nowrap">
                <i class="bi bi-person-plus font-16 mr-2 @if(!request()->is("my/referrals")) color-light-gray @endif"></i>
                <p class="mb-0 text-nowrap @if(!request()->is("my/referrals")) color-light-gray @endif">{{trans('general.referrals')}}</p>
            </a>

        @elseif(in_array(request()->path(), ["settings/subscription", "my/subscribers", "my/subscriptions"]))
            <a href="{{url("settings/subscription")}}" class="lightest-gray-btn py-2 px-3 flex-row-start flex-align-center flex-nowrap">
                <i class="bi bi-cash-stack font-16 mr-2 @if(!request()->is("settings/subscription")) color-light-gray @endif"></i>
                <p class="mb-0 text-nowrap @if(!request()->is("settings/subscription")) color-light-gray @endif">{{trans('general.subscription_price')}}</p>
            </a>
            <a href="{{url("my/subscribers")}}" class="lightest-gray-btn py-2 px-3 ml-3 flex-row-start flex-align-center flex-nowrap">
                <i class="feather icon-users font-16 mr-2 @if(!request()->is("my/subscribers")) color-light-gray @endif"></i>
                <p class="mb-0 text-nowrap @if(!request()->is("my/subscribers")) color-light-gray @endif">{{trans('users.my_subscribers')}}</p>
            </a>
            <a href="{{url("my/subscriptions")}}" class="lightest-gray-btn py-2 px-3 ml-3 flex-row-start flex-align-center flex-nowrap">
                <i class="feather icon-user-check font-16 mr-2 @if(!request()->is("my/subscriptions")) color-light-gray @endif"></i>
                <p class="mb-0 text-nowrap @if(!request()->is("my/subscriptions")) color-light-gray @endif">{{trans('users.my_subscriptions')}}</p>
            </a>

        @elseif(in_array(request()->path(), ["settings/addons", "my/payments", "my/payments/received", "settings/withdrawals", "settings/payout/method"]) || str_contains(request()->path(), "settings/addon/"))
            <a href="{{url("settings/addons")}}" class="lightest-gray-btn py-2 px-3 flex-row-start flex-align-center flex-nowrap">
                <i class="bi bi-gift font-16 mr-2 @if(!request()->is("settings/addons") && !str_contains(request()->path(), "settings/addon/")) color-light-gray @endif"></i>
                <p class="mb-0 text-nowrap @if(!request()->is("settings/addons") && !str_contains(request()->path(), "settings/addon/")) color-light-gray @endif">Plan</p>
            </a>
            <a href="{{url("my/payments")}}" class="lightest-gray-btn py-2 px-3 ml-3 flex-row-start flex-align-center flex-nowrap">
                <i class="bi bi-receipt font-16 mr-2 @if(!request()->is("my/payments")) color-light-gray @endif"></i>
                <p class="mb-0 text-nowrap @if(!request()->is("my/payments")) color-light-gray @endif">My Payments</p>
            </a>
            <a href="{{url("my/payments/received")}}" class="lightest-gray-btn py-2 px-3 ml-3 flex-row-start flex-align-center flex-nowrap">
                <i class="bi bi-wallet font-16 mr-2 @if(!request()->is("my/payments/received")) color-light-gray @endif"></i>
                <p class="mb-0 text-nowrap @if(!request()->is("my/payments/received")) color-light-gray @endif">{{trans('general.payments_received')}}</p>
            </a>
            <a href="{{url("settings/payout/method")}}" class="lightest-gray-btn py-2 px-3 ml-3 flex-row-start flex-align-center flex-nowrap">
                <i class="bi bi-wallet font-16 mr-2 @if(!request()->is("settings/payout/method")) color-light-gray @endif"></i>
                <p class="mb-0 text-nowrap @if(!request()->is("settings/payout/method")) color-light-gray @endif">{{trans('users.payout_method')}}</p>
            </a>
            <a href="{{url("settings/withdrawals")}}" class="lightest-gray-btn py-2 px-3 ml-3 flex-row-start flex-align-center flex-nowrap">
                <i class="bi bi-arrow-left-right font-16 mr-2 @if(!request()->is("settings/withdrawals")) color-light-gray @endif"></i>
                <p class="mb-0 text-nowrap @if(!request()->is("settings/withdrawals")) color-light-gray @endif">{{trans('general.withdrawals')}}</p>
            </a>
        @endif
    </div>
</div>
