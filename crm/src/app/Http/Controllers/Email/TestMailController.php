<?php

namespace App\Http\Controllers\Email;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Jobs\Mail\TestMailJob;
use App\Mail\TestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TestMailController extends Controller
{
    public function send(Request $request)
    {
        validator($request->all(),[
            'email' => ['required','email'],
            'subject' => ['required'],
            'message' => ['required']
        ])->validate();

        try {
            TestMailJob::dispatchSync($request->email, $request->subject, $request->message);
            return response(['status' => true, 'message' => __t('email_sent_successfully')]);
        }catch (\Exception $exception){
            throw new GeneralException(__t('email_setup_is_not_correct'));
        }

    }
}
