<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\Subscriber\Subscriber;
use App\Repositories\App\StatusRepository;
use Illuminate\Http\Request;

class SubscriberStatusController extends Controller
{
    public function unsubscribe($brand_id, $email)
    {
        $subscriber = Subscriber::where('brand_id', $brand_id)->where('email', $email)->firstOrFail();
        $subscriber->status_id = resolve(StatusRepository::class)->subscriberUnsubscribed();
        $subscriber->save();

        return view('frontend.subscriber.unsubscribe', ['subscriber' => $subscriber]);
    }
}
