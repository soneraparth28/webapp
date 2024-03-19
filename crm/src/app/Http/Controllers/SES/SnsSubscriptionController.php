<?php

namespace App\Http\Controllers\SES;

use App\Http\Controllers\Controller;
use App\Models\SES\SnsSubscription;
use App\Webhook\SES\Traits\SESSubscriptionConfirm;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SnsSubscriptionController extends Controller
{
    use SESSubscriptionConfirm;

    public function index()
    {
        if (auth()->user()->can('view_sns_subscription')) {
            return SnsSubscription::query()->select('id')->where('is_confirmed', 0)
                ->when(brand(), function (Builder $builder) {
                    $builder->where('brand_id', brand()->id);
                }, function (Builder $builder) {
                    $builder->whereNull('brand_id');
                })->get();
        }
        return [];
    }

    public function confirm(Request $request)
    {
        $this->whenSubscriptionConfirmed($request);

        return response()->json(['status' => true, 'message' => trans('default.subscription_confirmed_response')]);
    }
}
