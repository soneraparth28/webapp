<?php

namespace App\Http\Controllers;

use App\Models\UserEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserEventController extends Controller
{


    public function index() {
        return view("admin.user-events", array("events" => UserEvent::all()));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): void
    {
        try {

            $eventType = isset($request->event_type) ? $request->event_type : "";
            $eventValue =  isset($request->event_value) ? $request->event_value : "";
            $pageOwner = "";


            if($eventType === "page_view") {
                if(str_starts_with($eventValue, "/")) $eventValue = substr($eventValue, 1);

                $pagesToSkip = ["my"];
                $pages = [
                    "dashboard", "messsages", "explore", "", "dashboard", "creators", "category",
                    "faq", "terms-and-conditions", "privacy-policy", "cookiepolicy", "about-us",
                    "panel", "logout", "notifications", "settings", "privacy", "block", "testing", "my"
                ];

                if(empty($eventValue))$eventValue = "dashboard";

                if(str_contains($eventValue, "/")) $basePath = explode("/", $eventValue)[0];
                else $basePath = $eventValue;

                if(in_array($basePath, $pagesToSkip)) return;
                if(!in_array($basePath, $pages)) {
                    $user = DB::table("users")->where("username", "=", $basePath)->first();
                    if(!empty($user)) {
                        $pageOwner = $user->id;
                    }
                }



            }
            else return;


            UserEvent::create(array("event_type" => $eventType, "event_value" => $eventValue, "page_owner" => $pageOwner, "user_id" => Auth::user()->id));
        } catch (\Exception $e) {

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserEvent  $userEvent
     * @return \Illuminate\Http\Response
     */
    public function show(UserEvent $userEvent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserEvent  $userEvent
     * @return \Illuminate\Http\Response
     */
    public function edit(UserEvent $userEvent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserEvent  $userEvent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserEvent $userEvent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserEvent  $userEvent
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserEvent $userEvent)
    {
        //
    }
}
