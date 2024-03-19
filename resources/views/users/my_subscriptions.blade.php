@extends('layouts.app')

@section('title') {{trans('users.my_subscriptions')}} -@endsection

@section('content')

    <?php $pageTitle = "Settings"; ?>

    <div class="session-main-wrapper">
        @include("includes.menus.main-creator-menu")



        <div class="session-main-page-wrapper">
            @include("includes.menus.main-creator-navbar")

            <div class="session-main-content-wrapper">

                <div class="row">
                    <div class="col-lg-4 col-xl-3 d-none d-lg-flex">
                        @include("includes.cards-settings")
                    </div>


                    <div class="col-12 col-lg-8 col-xl-9">
                        <div class="row">
                            <div class="col-12 d-lg-none">
                                @include("includes.card-settings-mobile")
                            </div>
                            <div class="col-12 mt-3 mt-lg-0">
                                <div class="flex-row-start flex-align-center">
                                    <i class="feather icon-user font-22"></i>
                                    <p class="font-22 font-weight-bold mb-0 ml-3">My subscriptions</p>
                                </div>


                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card border-radius-5px">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-striped m-0">
                                                        <thead>
                                                        <tr>
                                                            <th scope="col">{{trans('users.subscribed')}}</th>
                                                            <th scope="col">{{trans('admin.date')}}</th>
                                                            <th scope="col">{{trans('general.interval')}}</th>
                                                            <th scope="col">{{ trans('admin.ends_at') }}</th>
                                                            <th scope="col">{{trans('admin.status')}}</th>
                                                            <th scope="col" class="">Action</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>

                                                        @if ($subscriptions->count() != 0)
                                                            @foreach ($subscriptions as $subscription)
                                                                <tr>
                                                                    <td class="align-middle">
                                                                        @if (! isset($subscription->subscribed()->username))
                                                                            @if($subscription->name === "addon")
                                                                                {{trans('general.addon') . ": " . (\App\Models\Addons::whereId($subscription->stripe_id)->first())->title}}
                                                                            @else
                                                                                {{ trans('general.no_available') }}
                                                                            @endif
                                                                        @else
                                                                            <a href="{{url($subscription->subscribed()->username)}}">
                                                                                <img src="{{Helper::getFile(config('path.avatar').$subscription->subscribed()->avatar)}}" width="40" height="40" class="rounded-circle mr-2">
                                                                                {{$subscription->subscribed()->hide_name == 'yes' ? $subscription->subscribed()->username : $subscription->subscribed()->name}}
                                                                            </a>
                                                                        @endif
                                                                    </td>
                                                                    <td class="align-middle">{{Helper::formatDate($subscription->created_at)}}</td>
                                                                    <td class="align-middle">{{ $subscription->free == 'yes'? trans('general.not_applicable') : trans('general.'.$subscription->interval)}}</td>
                                                                    <td class="align-middle">
                                                                        @if ($subscription->ends_at)
                                                                            {{Helper::formatDate($subscription->ends_at)}}
                                                                        @elseif ($subscription->free == 'yes')
                                                                            {{ __('general.free_subscription') }}
                                                                        @else
                                                                            {{Helper::formatDate(auth()->user()->subscription('main', $subscription->stripe_price)->asStripeSubscription()->current_period_end, true)}}
                                                                        @endif
                                                                    </td>
                                                                    <td class="align-middle">
                                                                        @if (strtotime($subscription->ends_at) > strtotime(now()->format('Y-m-d H:i:s')) && $subscription->cancelled == 'no')
                                                                            <span class="border-radius-10px py-1 px-2 text-uppercase" style="background: #E9E9E9">{{trans('general.active')}}</span>
                                                                        @elseif ($subscription->stripe_id != '' && $subscription->stripe_status == 'incomplete')
                                                                            <span class="border-radius-10px py-1 px-2 text-uppercase" style="background: #E9E9E9">{{trans('general.incomplete')}}</span>

                                                                            <a class="border-radius-10px py-1 px-2 text-uppercase" style="background: #E9E9E9" href="{{ route('cashier.payment', $subscription->last_payment) }}">
                                                                                {{trans('general.confirm_payment')}}
                                                                            </a>
                                                                        @else
                                                                            <span class="border-radius-10px py-1 px-2 text-uppercase" style="background: #E9E9E9">{{trans('general.cancelled')}}</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="">
                                                                        @if (strtotime($subscription->ends_at) > strtotime(now()->format('Y-m-d H:i:s')) && $subscription->cancelled == 'no')
                                                                            <a class="ml-1 text-danger" href="{{route('cancel-mollie-subscription', $subscription->id)}}">Cancel</a>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                        @else
                                                            <tr>
                                                                <td>{{ trans('general.no_transactions_yet') }}</td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td class=""></td>
                                                            </tr>
                                                        @endif

                                                        </tbody>
                                                    </table>
                                                </div>

                                                @if ($subscriptions->hasPages())
                                                    {{ $subscriptions->links() }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
