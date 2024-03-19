<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\AdminSettings;
use App\Models\Conversations;
use App\Models\Notifications;
use App\Models\Messages;
use App\Models\MediaMessages;
use App\Models\User;
use App\Helper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Events\MassMessagesEvent;
use App\Jobs\EncodeVideoMessages;
use Image;
use Cache;

class MessagesController extends Controller
{
    protected function subscribedToYourContent($user)
  {
    return auth()->user()
      ->mySubscriptions()
        ->where('subscriptions.user_id', $user->id)
        ->where('stripe_id', '=', '')
        ->where('ends_at', '>=', now())
        ->whereIn('stripe_price', auth()->user()->plans()->pluck('name'))

        ->orWhere('stripe_status', 'active')
          ->where('subscriptions.user_id', auth()->user()->id)
          ->where('stripe_id', '<>', '')
          ->whereIn('stripe_price', $user->plans()->pluck('name'))

          ->orWhere('stripe_status', 'canceled')
            ->where('subscriptions.user_id', auth()->user()->id)
            ->where('ends_at', '>=', now())
            ->where('stripe_id', '<>', '')
            ->whereIn('stripe_price', $user->plans()->pluck('name'))

          ->orWhere('stripe_id', '=', '')
        ->where('stripe_price', $user->plan)
        ->where('free', '=', 'yes')
      ->where('subscriptions.user_id', auth()->user()->id)
      ->first();
  }

  // Subscribed to my Content
  protected function subscribedToMyContent($user)
  {
    return auth()->user()
      ->userSubscriptions()
      ->whereIn('stripe_price', $user->plans()->pluck('name'))
      ->where('stripe_id', '=', '')
      ->where('ends_at', '>=', now())

      ->orWhere('stripe_status', 'active')
        ->where('stripe_id', '<>', '')
        ->where('user_id', $user->id)
        ->whereIn('stripe_price', auth()->user()->plans()->pluck('name'))

        ->orWhere('stripe_status', 'canceled')
          ->where('stripe_id', '<>', '')
          ->where('user_id', $user->id)
          ->where('ends_at', '>=', now())
          ->whereIn('stripe_price', auth()->user()->plans()->pluck('name'))

        ->orWhere('stripe_id', '=', '')
      ->where('stripe_price', auth()->user()->plan)
      ->where('free', '=', 'yes')
    ->whereUserId($user->id)
    ->first();
  }
    public function inbox()
    {
        $settings = AdminSettings::first();

		$messages = Conversations::has('messages')->where('user_1', auth()->user()->id)->orWhere('user_2', auth()->user()->id)->orderBy('updated_at', 'DESC')->orderBy('id', 'DESC')->paginate(10);
        foreach ($messages as $msg)
        {
            $allMediaMessages = $msg->last()->media()->get();
            if ($msg->last()->from_user_id == auth()->user()->id && $msg->last()->to()->id != auth()->user()->id) 
            {
                $avatar   = $msg->last()->to()->avatar;
                $name     = $msg->last()->to()->hide_name == 'yes' ? $msg->last()->to()->username : $msg->last()->to()->name;
                $userID   = $msg->last()->to()->id;
                $username = $msg->last()->to()->username;
                $verified_id = $msg->last()->to()->verified_id;
                $active_status_online = $msg->last()->to()->active_status_online == 'yes' ? true : false;
                $icon     = $msg->last()->status == 'readed' ? true : false;
                
            } 
            else if ($msg->last()->from_user_id == auth()->user()->id)
            {
		        $avatar   = $msg->last()->to()->avatar;
		        $name     = $msg->last()->to()->hide_name == 'yes' ? $msg->last()->to()->username : $msg->last()->to()->name;
		        $userID   = $msg->last()->to()->id;
		        $username = $msg->last()->to()->username;
		        $verified_id = $msg->last()->to()->verified_id;
		        $active_status_online = $msg->last()->to()->active_status_online == 'yes' ? true : false;
		        $icon = null;
	        } 
	        else 
	        {
		        $avatar   = $msg->last()->from()->avatar;
		        $name     = $msg->last()->from()->hide_name == 'yes' ? $msg->last()->from()->username : $msg->last()->from()->name;
		        $userID   = $msg->last()->from()->id;
		        $username = $msg->last()->from()->username;
		        $verified_id = $msg->last()->from()->verified_id;
		        $active_status_online = $msg->last()->from()->active_status_online == 'yes' ? true : false;
		        $icon = null;
	        }
	        $iconMedia = null;
	        $format = null;
	        foreach ($allMediaMessages as $media)
	        {
		        switch ($media->type)
		        {
			        case 'image':
				        $iconMedia = '<i class="feather icon-image"></i> ';
				        $format = trans('general.image');
				        break;
			        case 'video':
				        $iconMedia = '<i class="feather icon-video"></i> ';
				        $format = trans('general.video');
				    break;
			        case 'music':
				        $iconMedia = '<i class="feather icon-mic"></i> ';
				        $format = trans('general.music');
				    break;
			        case 'zip':
				        $iconMedia = '<i class="far fa-file-archive"></i> ';
				        $format = trans('general.zip');
					break;
		        }
	        }
	        if ($allMediaMessages->count() > 1) 
	        {
		        $iconMedia = '<i class="bi bi-files"></i> ';
		        $format = null;
	        }
	        if ($msg->last()->tip == 'yes') 
	        {
		        $iconMedia = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-coin" viewBox="0 0 16 16"> <path d="M5.5 9.511c.076.954.83 1.697 2.182 1.785V12h.6v-.709c1.4-.098 2.218-.846 2.218-1.932 0-.987-.626-1.496-1.745-1.76l-.473-.112V5.57c.6.068.982.396 1.074.85h1.052c-.076-.919-.864-1.638-2.126-1.716V4h-.6v.719c-1.195.117-2.01.836-2.01 1.853 0 .9.606 1.472 1.613 1.707l.397.098v2.034c-.615-.093-1.022-.43-1.114-.9H5.5zm2.177-2.166c-.59-.137-.91-.416-.91-.836 0-.47.345-.822.915-.925v1.76h-.005zm.692 1.193c.717.166 1.048.435 1.048.91 0 .542-.412.914-1.135.982V8.518l.087.02z"/> <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/> <path fill-rule="evenodd" d="M8 13.5a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zm0 .5A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"/> </svg> '.trans('general.tip');
	        }
	        if ($msg->last()->status == 'new' && $msg->last()->from()->id != auth()->user()->id)  
	        {
	            $styleStatus = ' font-weight-bold unread-chat';
	        } 
	        else 
	        {
		        $styleStatus = null;
	        }
	        $messagesCount = Messages::where('from_user_id', $userID)->where('to_user_id', auth()->user()->id)->where('status','new')->count();
	        $checkPayPerView = auth()->user()->payPerViewMessages()->where('messages_id', $msg->last()->id)->first();
	        $messageurl = url('api/messages/'.$userID, $username);
            $msg->url = $messageurl;
            $msg->onlinestatus =  $active_status_online;
            $msg->avatar = Helper::getFile(config('path.avatar').$avatar);
            $msg->name = $name;
            $msg->verified = $verified_id;
            $msg->timee =  date('c',strtotime( $msg->last()->created_at ) );
            $msg->countt = $messagesCount;
            if ($msg->last()->price != 0.00
				&& $allMediaMessages->count() == 0
				&& $msg->last()->to()->id == auth()->user()->id
				&& !$checkPayPerView)
			{
			    $msg->last = "Locked";	    
			}
			else
			{
			    $msg->last = $msg->last()->message;
			}
        }
        if(request()->ajax()) {
            $response = [
                'success' => true,
                'data' => [
                    'messages' => $messages,
                ],
                'message' =>  'Messages Data.'
            ]; 
            return response()->json($response , 200);
            // return view('includes.messages-inbox',['messagesInbox' => $messages])->render();
        }
        $response = [
            'success' => true,
            'data' => [
                'messages' => $messages,
            ],
            'message' =>  'Messages Data.'
        ]; 
        return response()->json($response , 200);
        // return view('users.messages', ['messagesInbox' => $messages]);
	}
    public function messages($id)
    {
        $user = User::whereId($id)->where('id', '<>', auth()->user()->id)->firstOrFail();
        $allMessages = Messages::where('to_user_id', auth()->user()->id)
	        ->where('from_user_id', $id)->whereMode('active')->orWhere( 'from_user_id', auth()->user()->id )
	        ->where('to_user_id', $id)->whereMode('active')->orderBy('messages.updated_at', 'ASC')
	        ->get();
        $messages = Messages::where('to_user_id', auth()->user()->id)->where('from_user_id', $id)
            ->whereMode('active')->orWhere( 'from_user_id', auth()->user()->id )->where('to_user_id', $id)
            ->whereMode('active')->take(10)->orderBy('messages.updated_at', 'DESC')->get();
        $messagesInbox = Conversations::has('messages')
            ->where('user_1', auth()->user()->id)->orWhere('user_2', auth()->user()->id)
		    ->orderBy('updated_at', 'DESC')->orderBy('id', 'DESC')->paginate(10);
        $data = [];
        if($messages->count()) {
  	        $data['reverse'] = collect($messages->values())->reverse();
  	    } else {
  	        $data['reverse'] = $messages;
  	    }
  	    $messages = $data['reverse'];
  	    $counter = ($allMessages->count() - 10);
        // UPDATE MESSAGE 'READED'
        $messageReaded = new Messages();
        $messageReaded->timestamps = false;
        $messageReaded->newModelQuery()->where('from_user_id', $id)->where('to_user_id', auth()->user()->id)
            ->where('status', 'new')->update(['status' => 'readed']);
        // Check if subscription exists
        $subscribedToYourContent = $this->subscribedToYourContent($user);
        $subscribedToMyContent = $this->subscribedToMyContent($user);
        $response = [
            'success' => true,
            'data' => [
                'messages' => $messages,
                'messagesInbox' => $messagesInbox,
                'user' => $user,
                'counter' => $counter,
                'allMessages' => $allMessages->count(),
                'subscribedToYourContent' => $subscribedToYourContent,
                'subscribedToMyContent' => $subscribedToMyContent,
            ],
            'message' =>  'Messages Data.'
        ]; 
        return response()->json($response , 200);
		// return view('users.messages-show', [
        //     'messages' => $messages,
        //     'messagesInbox' => $messagesInbox,
        //     'user' => $user,
        //     'counter' => $counter,
        //     'allMessages' => $allMessages->count(),
        //     'subscribedToYourContent' => $subscribedToYourContent,
        //     'subscribedToMyContent' => $subscribedToMyContent
        // ]);
	}
    public function loadmore(Request $request)
	{
	    $id   = $request->input('id');
		$skip = $request->input('skip');
        $user = User::whereId($id)->where('id', '<>', auth()->user()->id)->firstOrFail();
        $allMessages = Messages::where('to_user_id', auth()->user()->id)->where('from_user_id', $id)
            ->whereMode('active')->orWhere( 'from_user_id', auth()->user()->id)->where('to_user_id', $id)
            ->whereMode('active')->orderBy('messages.id', 'ASC')->get();
        $messages = Messages::where('to_user_id', auth()->user()->id)
			->where('from_user_id', $id)->whereMode('active')->orWhere( 'from_user_id', auth()->user()->id )
			->where('to_user_id', $id)->whereMode('active')->skip($skip)->take(10)
			->orderBy('messages.id', 'DESC')->get();
  	    $data = [];
  	    if($messages->count()) {
            $data['reverse'] = collect($messages->values())->reverse();
  	    } else {
  	        $data['reverse'] = $messages;
  	    }
  	    $messages = $data['reverse'];
  		$counter = ($allMessages->count() - 10 - $skip);
        $response = [
            'success' => true,
            'data' => [
                'messages' => $messages,
                'user' => $user,
                'counter' => $counter,
                'allMessages' => $allMessages->count(),
            ],
            'message' =>  'Messages Data.'
        ]; 
        return response()->json($response , 200);
        // return view('includes.messages-chat', [
        //     'messages' => $messages,
        //     'user' => $user,
        //     'counter' => $counter,
        //     'allMessages' => $allMessages->count()
        // ])->render();
	}
    public function ajaxChat(Request $request)
    {
        if(! auth()->check()) {
            $response = [
                'success' => true,
                'data' => [
                    'session_null' => true,
                ],
                'message' =>  'Messages Data.'
            ]; 
            return response()->json($response , 400);
            // return response()->json(['session_null' => true]);
        }
        $_sql = $request->get('first_msg') == 'true' ? '=' : '>';
        $message = Messages::where('to_user_id', auth()->user()->id)
        ->where('from_user_id', $request->get('user_id'))
        ->where('id', $_sql, $request->get('last_id'))
        ->whereMode('active')
        ->orWhere('from_user_id', auth()->user()->id )
        ->where('to_user_id', $request->get('user_id'))
        ->where('id', $_sql, $request->get('last_id'))
        ->whereMode('active')
        ->orderBy('messages.id', 'ASC')
        ->get();
        $count = $message->count();
        $_array = array();
        if($count != 0) {
            foreach($message as $msg) {
            // UPDATE HOW READ MESSAGE
                if($msg->to_user_id == auth()->user()->id) {
                    $readed = Messages::where('id', $msg->id)->where('to_user_id', auth()->user()->id)
                    ->where('status', 'new')->update(['status' => 'readed'], ['updated_at' => false]);
                }
                $_array[] = view('includes.messages-chat', [
       			        'messages' => $message,
       			        'allMessages' => 0,
       			        'counter' => 0
       			    ])->render();
            }//<--- foreach
        }//<--- IF != 0
        $user = User::findOrFail($request->get('user_id'));
        if($user->active_status_online == 'yes') {
            // Check User Online
            if(Cache::has('is-online-' . $request->get('user_id'))) {
                $userOnlineStatus = true;
            } else {
                $userOnlineStatus = false;
            }
        } else {
            $userOnlineStatus = null;
        }
        $response = [
            'success' => true,
            'data' => [
                'total'    => $count,
            'messages' => $message,
            'success' => true,
            'to' => $request->get('user_id'),
            'userOnline' => $userOnlineStatus,
            'last_seen' => date('c', strtotime($user->last_seen ?? $user->date)),
            ],
            'message' =>  'Messages Data.'
        ]; 
        return response()->json($response , 200);
        // return response()->json(array(
        //     'total'    => $count,
        //     'messages' => $_array,
        //     'success' => true,
        //     'to' => $request->get('user_id'),
        //     'userOnline' => $userOnlineStatus,
        //     'last_seen' => date('c', strtotime($user->last_seen ?? $user->date))
        //     ), 200);
    }
    public function loadAjaxChat($id)
    {
        if(! request()->ajax()) {
            $response = [
                'success' => false,
                'data' => null,
                'message' =>  'Error Occured.'
            ]; 
            return response()->json($response , 401);
            // abort(401);
        }
        $user = User::whereId($id)->where('id', '<>', auth()->user()->id)->firstOrFail();
 		$allMessages = Messages::where('to_user_id', auth()->user()->id)->where('from_user_id', $id)
            ->whereMode('active')->orWhere( 'from_user_id', auth()->user()->id )
 		    ->where('to_user_id', $id)->whereMode('active')
 		    ->orderBy('messages.id', 'ASC')->get();
 		$messages = Messages::where('to_user_id', auth()->user()->id)->where('from_user_id', $id)
            ->whereMode('active')->orWhere( 'from_user_id', auth()->user()->id )
 		    ->where('to_user_id', $id)->whereMode('active')->take(10)
 		    ->orderBy('messages.id', 'DESC')->get();
 		$data = [];
 		if($messages->count()) {
 			$data['reverse'] = collect($messages->values())->reverse();
 		} else {
 			$data['reverse'] = $messages;
 		}
 		$messages = $data['reverse'];
 		$counter = ($allMessages->count() - 10);
        $response = [
            'success' => true,
            'data' => [
                'messages' => $messages,
 			    'user' => $user,
 			    'allMessages' => $allMessages->count(),
 			    'counter' => $counter,
            ],
            'message' =>  'Messages Data.'
        ]; 
        return response()->json($response , 200);
 		// return view('includes.messages-chat', [
 		// 	'messages' => $messages,
 		// 	'user' => $user,
 		// 	'allMessages' => $allMessages->count(),
 		// 	'counter' => $counter
 		// ])->render();
    }
    public function downloadFileZip($id)
    {
        $msg = Messages::findOrFail($id);
        if($msg->to_user_id != auth()->user()->id && $msg->from_user_id != auth()->user()->id) {
            $response = [
                'success' => false,
                'data' => null,
                'message' =>  'Error Occured.'
            ]; 
            return response()->json($response , 404);
            // abort(404);
        }
        $media = MediaMessages::whereMessagesId($msg->id)->where('type', 'zip')->firstOrFail();
        $pathFile = config('path.messages').$media->file;
        $headers = [
            'Content-Type:' => 'application/x-zip-compressed',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];
        $response = [
            'success' => true,
            'data' => [
                'header' => $headers,
 			    'file_path' => $pathFile,
                'file_name' => $media->file_name.'.zip',
            ],
            'message' =>  'Download File Data.'
        ]; 
        return response()->json($response , 200);
        // return Storage::download($pathFile, $media->file_name.'.zip', $headers);
   }
   public function send(Request $request)
  {

    if (! auth()->check()) {
      return response()->json(['session_null' => true]);
    }

    $settings = AdminSettings::first();

    // PATHS
    $path = config('path.messages');

  	 // Find user in Database
  	 $user = User::findOrFail($request->get('id_user'));

     // Currency Position
     if ($settings->currency_position == 'right') {
       $currencyPosition =  2;
     } else {
       $currencyPosition =  null;
     }

			$messages = [
	            "required"    => trans('validation.required'),
	            "message.max"  => trans('validation.max.string'),
              'price.min' => trans('general.amount_minimum'.$currencyPosition, ['symbol' => $settings->currency_symbol, 'code' => $settings->currency_code]),
              'price.max' => trans('general.amount_maximum'.$currencyPosition, ['symbol' => $settings->currency_symbol, 'code' => $settings->currency_code]),
        	];

      // Setup the validator
			$rules = [
				'message'=> 'required|min:1|max:'.$settings->comment_length.'',
        'zip'    => 'mimes:zip|max:'.$settings->file_size_allowed.'',
        'price'  => 'numeric|min:'.$settings->min_ppv_amount.'|max:'.$settings->max_ppv_amount,
          ];

			$validator = Validator::make($request->all(), $rules, $messages);

			// Validate the input and return correct response
			if ($validator->fails()) {
			    return response()->json(array(
			        'success' => false,
			        'errors' => $validator->getMessageBag()->toArray(),
			    ));
			}

				// Verify Conversation Exists
				$conversation = Conversations::where('user_1', auth()->user()->id)
  				->where('user_2', $request->get('id_user'))
  				->orWhere('user_1', $request->get('id_user'))
  				->where('user_2', auth()->user()->id)->first();

				$time = Carbon::now();

        if (! isset($conversation)) {
          $newConversation = new Conversations();
          $newConversation->user_1 = auth()->user()->id;
          $newConversation->user_2 = $request->get('id_user');
          $newConversation->updated_at = $time;
          $newConversation->save();

          $conversationID = $newConversation->id;

        } else {
          $conversation->updated_at = $time;
          $conversation->save();

          $conversationID = $conversation->id;
        }

          $message = new Messages();
          $message->conversations_id = $conversationID;
  				$message->from_user_id    = auth()->user()->id;
  				$message->to_user_id      = $request->get('id_user');
  				$message->message         = trim(Helper::checkTextDb($request->get('message')));
  				$message->updated_at      = $time;
          $message->price           = $request->price;
          $message->save();

          // Insert Files
          $fileuploader = $request->input('fileuploader-list-media');
          $fileuploader = json_decode($fileuploader, TRUE);

          if ($fileuploader) {
            foreach ($fileuploader as $key => $media) {
              MediaMessages::whereFile($media['file'])->update([
                'messages_id' => $message->id,
                'status' => 'active'
              ]);
            }
          }

        //=== Upload File Zip
        if ($request->hasFile('zip')) {

          $fileZip         = $request->file('zip');
          $extension       = $fileZip->getClientOriginalExtension();
          $size            = Helper::formatBytes($fileZip->getSize(), 1);
          $originalName    = Helper::fileNameOriginal($fileZip->getClientOriginalName());
          $file            = strtolower(auth()->user()->id.time().Str::random(20).'.'.$extension);

          $fileZip->storePubliclyAs($path, $file);
          $token = Str::random(150).uniqid().now()->timestamp;

          // We insert the file into the database
          MediaMessages::create([
            'messages_id' => $message->id,
            'type' => 'zip',
            'file' => $file,
            'file_name' => $originalName,
            'file_size' => $size,
            'token' => $token,
            'status' => 'active',
            'created_at' => now()
          ]);
        } //=== End Upload File Zip

        // Get all videos of the message
        $videos = MediaMessages::whereMessagesId($message->id)->whereType('video')->get();

        if ($videos->count() && $settings->video_encoding == 'on') {

          try {
            foreach ($videos as $video) {
              $this->dispatch(new EncodeVideoMessages($video));
            }

            // Change status Pending to Encode
            Messages::whereId($message->id)->update([
                'mode' => 'pending'
            ]);

            return response()->json([
              'success' => true,
              'encode' => true
            ]);

          } catch (\Exception $e) {
            \Log::info($e->getMessage());
          }

        }// End Videos->count

      return response()->json([
        'success' => true,
        'fromChat' => true,
        'last_id' => $message->id,
        ], 200);

    }
    public function delete(Request $request)
  {
   $message_id = $request->get('message_id');
   $path   = config('path.messages');

   $data = Messages::where('from_user_id', auth()->user()->id)
   ->where('id', $message_id)
   ->orWhere('to_user_id', auth()->user()->id)
   ->where('id', $message_id)->first();

   // Delete Notifications
   Notifications::where('target', $message_id)
     ->where('type', '10')
     ->delete();

   if (isset($data)) {

     foreach ($data->media as $media) {

       $messageWithSameFile = MediaMessages::whereFile($media->file)
       ->where('id', '<>', $media->id)
       ->count();

       if ($messageWithSameFile == 0) {
         Storage::delete($path.$media->file);
         Storage::delete($path.$media->video_poster);
       }

       $media->delete();
     }

     $data->delete();

     $countMessages = Messages::where('conversations_id', $data->conversations_id)->count();

     $conversation = Conversations::find($data->conversations_id);

     if ($countMessages == 0) {
       $conversation->delete();
     } else {
       $message = Conversations::has('messages')
       ->whereId($data->conversations_id)
       ->where('user_1', auth()->user()->id)
 			->orWhere('user_2', auth()->user()->id)
      ->whereId($data->conversations_id)
 			->orderBy('updated_at', 'DESC')
 			->first();

       $conversation->updated_at = $message->last()->created_at;
       $conversation->save();
     }

       return response()->json([
         'success' => true,
         'total' => $countMessages
       ]);

     } else {
       return response()->json([
         'success' => false,
         'error' => trans('general.error')
       ]);
    }
 }
 public function deleteChat($id)
     {
      $path = config('path.messages');

      $messages = Messages::where('to_user_id', auth()->user()->id)
			->where('from_user_id', $id)
			->orWhere('from_user_id', auth()->user()->id)
			->where('to_user_id', $id)
			->get();

      if ($messages->count() != 0) {

        foreach ($messages as $msg) {

          foreach ($msg->media as $media) {

            $messageWithSameFile = MediaMessages::whereFile($media->file)
            ->where('id', '<>', $media->id)
            ->count();

            if ($messageWithSameFile == 0) {
              Storage::delete($path.$media->file);
            }

            $media->delete();
          }

          $msg->delete();
        }

          $conversation = Conversations::find($messages[0]->conversations_id);
          $conversation->delete();

          // Delete Notifications
          Notifications::where('destination', auth()->user()->id)
            ->where('type', '10')
            ->delete();

        return response()->json([
         'success' => true,
       ]);

      } else {
        return response()->json([
         'success' => true,
       ]);
      }
    }
    public function searchCreator(Request $request)
 {
   $settings = AdminSettings::first();
   $query = $request->get('user');
   $data = "";

   if ($query != '' && strlen($query) >= 2) {

     $sql = User::where('status','active')
         ->where('username','LIKE', '%'.$query.'%')
          ->where('id', '<>', auth()->user()->id)
          ->where('id', '<>', $settings->hide_admin_profile == 'on' ? 1 : 0)
          ->where('blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%')
          ->when(auth()->user()->verified_id <> 'yes', function ($query) {
            $query->where('verified_id', 'yes');
          })
          ->orWhere('status','active')
            ->where('name','LIKE', '%'.$query.'%')
              ->where('id', '<>', auth()->user()->id)
                ->where('id', '<>', $settings->hide_admin_profile == 'on' ? 1 : 0)
                ->where('blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%')
                ->whereHideName('no')
                ->when(auth()->user()->verified_id <> 'yes', function ($query) {
                  $query->where('verified_id', 'yes');
                })
              ->orderBy('verified_id','asc')
          ->take(5)
          ->get();

       if ($sql) {
         foreach ($sql as $user) {

           if ($user->active_status_online == 'yes') {
             if (Cache::has('is-online-' . $user->id)) {
               $userOnlineStatus = 'user-online';
             } else {
               $userOnlineStatus = 'user-offline';
             }
           } else {
             $userOnlineStatus = null;
           }

           $name = $user->hide_name == 'yes' ? $user->username : $user->name;
           $verified = $user->verified_id == 'yes' ? '<small class="verified"><i class="bi bi-patch-check-fill"></i></small>' : null;
           
           $user->messageurl = url('api/messages/'.$user->id, $user->username);
           $user->onlinestatus = $userOnlineStatus;
           $user->useravatar = Helper::getFile(config('path.avatar').$user->avatar);
         }
         return $sql;
        }
       }
     }
}
