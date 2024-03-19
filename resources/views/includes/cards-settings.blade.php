<div class="flex-col-start w-100">

    <div class="item-container-box w-100 color-gray settings-menu-container">
        <p class="font-16 font-weight-bold color-dark mt-2 mb-0">Account</p>

        <div class="flex-row-between flex-align-center link-item mt-3 @if (request()->is('settings/page')) active @endif">
            <div class="flex-row-start flex-align-center">
                <i class="feather icon-user font-16"></i>
                <a href="{{url("settings/page")}}" class="font-16 ml-3">My profile</a>
            </div>
            <i class="feather icon-chevron-right"></i>
        </div>

        @if(auth()->user()->role == 'admin')
            <div class="flex-row-between flex-align-center link-item mt-3 @if (request()->is('panel/admin')) active @endif">
                <div class="flex-row-start flex-align-center">
                    <i class="bi bi-key font-16"></i>
                    <a href="{{url("panel/admin")}}" class="font-16 ml-3">{{trans('admin.admin')}}</a>
                </div>
                <i class="feather icon-chevron-right"></i>
            </div>
        @endif

{{--        <div class="flex-row-between flex-align-center link-item mt-3 @if (request()->is('my/wallet')) active @endif">--}}
{{--            <div class="flex-row-start flex-align-center">--}}
{{--                <i class="iconmoon icon-Wallet font-16"></i>--}}
{{--                <a href="{{url('my/wallet')}}" class="font-16 ml-3">{{trans('general.wallet')}}</a>--}}
{{--            </div>--}}
{{--            <i class="feather icon-chevron-right"></i>--}}
{{--        </div>--}}

        <div class="flex-row-between flex-align-center link-item mt-3 @if (request()->is('privacy/security')) active @endif">
            <div class="flex-row-start flex-align-center">
                <i class="bi bi-shield-check font-16"></i>
                <a href="{{url('privacy/security')}}" class="font-16 ml-3">{{trans('general.privacy')}}</a>
            </div>
            <i class="feather icon-chevron-right"></i>
        </div>

        <div class="flex-row-between flex-align-center link-item mt-3 @if (request()->is('my/referrals')) active @endif">
            <div class="flex-row-start flex-align-center">
                <i class="bi-person-plus font-16"></i>
                <a href="{{url('my/referrals')}}" class="font-16 ml-3">{{trans('general.referrals')}}</a>
            </div>
            <i class="feather icon-chevron-right"></i>
        </div>

        <!--<div class="flex-row-between flex-align-center link-item mt-3 @if (request()->is('logout')) active @endif">-->
        <!--    <div class="flex-row-start flex-align-center">-->
        <!--        <i class="feather icon-log-out font-16"></i>-->
        <!--        <a href="{{url('logout')}}" class="font-16 ml-3">{{trans('auth.logout')}}</a>-->
        <!--    </div>-->
        <!--    <i class="feather icon-chevron-right"></i>-->
        <!--</div>-->
    </div>

    <div class="item-container-box w-100 color-gray settings-menu-container mt-4">
        <p class="font-16 font-weight-bold color-dark mt-2 mb-0">Subscriptions</p>

        <div class="flex-row-between flex-align-center link-item mt-3 @if (request()->is('settings/subscription')) active @endif">
            <div class="flex-row-start flex-align-center">
                <i class="bi bi-cash-stack font-16"></i>
                <a href="{{url("settings/subscription")}}" class="font-16 ml-3">{{trans('general.subscription_price')}}</a>
            </div>
            <i class="feather icon-chevron-right"></i>
        </div>

        <div class="flex-row-between flex-align-center link-item mt-3 @if (request()->is('my/subscribers')) active @endif">
            <div class="flex-row-start flex-align-center">
                <i class="feather icon-users font-16"></i>
                <a href="{{url('my/subscribers')}}" class="font-16 ml-3">{{trans('users.my_subscribers')}}</a>
            </div>
            <i class="feather icon-chevron-right"></i>
        </div>

        <div class="flex-row-between flex-align-center link-item mt-3 @if (request()->is('my/subscriptions')) active @endif">
            <div class="flex-row-start flex-align-center">
                <i class="feather icon-user-check font-16"></i>
                <a href="{{url('my/subscriptions')}}" class="font-16 ml-3">{{trans('users.my_subscriptions')}}</a>
            </div>
            <i class="feather icon-chevron-right"></i>
        </div>
    </div>

    <div class="item-container-box w-100 color-gray settings-menu-container mt-4">
        <p class="font-16 font-weight-bold color-dark mt-2 mb-0">Payment</p>

        <div class="flex-row-between flex-align-center link-item mt-3 @if (request()->is('settings/addons')) active @endif">
            <div class="flex-row-start flex-align-center">
                <i class="bi bi-gift font-16"></i>
                <a href="{{url("settings/addons")}}" class="font-16 ml-3">Plan</a>
            </div>
            <i class="feather icon-chevron-right"></i>
        </div>

        <div class="flex-row-between flex-align-center link-item mt-3 @if (request()->is('my/payments')) active @endif">
            <div class="flex-row-start flex-align-center">
                <i class="bi bi-receipt font-16"></i>
                <a href="{{url('my/payments')}}" class="font-16 ml-3">My Payments</a>
            </div>
            <i class="feather icon-chevron-right"></i>
        </div>

        <div class="flex-row-between flex-align-center link-item mt-3 @if (request()->is('my/payments/received')) active @endif">
            <div class="flex-row-start flex-align-center">
                <i class="bi bi-wallet font-16"></i>
                <a href="{{url('my/payments/received')}}" class="font-16 ml-3">{{trans('general.payments_received')}}</a>
            </div>
            <i class="feather icon-chevron-right"></i>
        </div>

        <div class="flex-row-between flex-align-center link-item mt-3 @if (request()->is('settings/payout/method')) active @endif">
            <div class="flex-row-start flex-align-center">
                <i class="bi bi-credit-card font-16"></i>
                <a href="{{url('settings/payout/method')}}" class="font-16 ml-3">{{trans('users.payout_method')}}</a>
            </div>
            <i class="feather icon-chevron-right"></i>
        </div>

        <div class="flex-row-between flex-align-center link-item mt-3 @if (request()->is('settings/withdrawals')) active @endif">
            <div class="flex-row-start flex-align-center">
                <i class="bi bi-arrow-left-right font-16"></i>
                <a href="{{url('settings/withdrawals')}}" class="font-16 ml-3">{{trans('general.withdrawals')}}</a>
            </div>
            <i class="feather icon-chevron-right"></i>
        </div>
    </div>
</div>
