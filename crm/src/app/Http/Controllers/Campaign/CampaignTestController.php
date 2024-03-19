<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use App\Jobs\Mail\CampaignMailSender;
use App\Models\Campaign\Campaign;
use Illuminate\Http\Request;

class CampaignTestController extends Controller
{
    public function test(Campaign $campaign, Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $subscriber = (object) [
            'full_name' => 'John Doe',
            'email' => $request->get('email')
        ];

        CampaignMailSender::dispatch(
            $campaign,
            $subscriber,
            'disable-log'
        )->onQueue('high');

        return response(['status' => true, 'message' => trans('default.test_email_response')]);

    }
}
