<?php


namespace App\Webhook\SES\Traits;


use App\Models\SES\SnsSubscription;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

trait SESSubscriptionConfirm
{
    public function whenSubscriptionConfirmation(Request $request)
    {
        return SnsSubscription::query()->create([
            'brand_id' => optional(brand())->id,
            'confirm_url' => json_decode($request->getContent())->SubscribeURL
        ]);
    }

    public function whenSubscriptionConfirmed(Request $request)
    {
        $sns = SnsSubscription::query()->find($request->sns_id);

        $client = new Client();

        if ($client->get($sns->confirm_url)->getStatusCode() === 200) {
            $sns->update([
                'is_confirmed' => 1
            ]);
        }
        return true;
    }
}
