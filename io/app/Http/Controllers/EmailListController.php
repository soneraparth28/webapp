<?php

namespace App\Http\Controllers;

use App\Models\EmailList;
use Illuminate\Http\Request;

class EmailListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): void
    {
        $request->validate(["email" => "required|unique"]);

        EmailList::create($request->only("email"));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EmailList  $emailList
     * @return \Illuminate\Http\Response
     */
    public function show(EmailList $emailList)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EmailList  $emailList
     * @return \Illuminate\Http\Response
     */
    public function edit(EmailList $emailList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EmailList  $emailList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmailList $emailList)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EmailList  $emailList
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmailList $emailList)
    {
        //
    }
}
