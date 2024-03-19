<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Channels;
use App\Helper;

class ChannelsController extends Controller
{
    public function channels()
    {
        $channels = Channels::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->paginate(50);

        return view('users.channels.index', compact('channels'));
    }//<--- End Method

    public function addChannel()
    {
        return view('users.channels.add');
    }

    public function storeChannel(Request $request)
    {
        $messages = [
            'channel.required' => "The Channel field is required."
        ];

        $validated = $request->validate([
            'channel' => 'required',
        ], $messages);

        $data = [
            'user_id' => auth()->user()->id,
            'channel' => $request->channel,
        ];

        Channels::create($data);

        return redirect('my/channels')->withSuccessMessage(trans('admin.success_add'));
    }//<--- End Method

    public function editChannel($id)
    {
        $channel = Channels::findOrFail($id);
        return view('users.channels.edit', compact('channel'));
    }//<--- End Method

    public function updateChannel(Request $request)
    {
        $channel = Channels::findOrFail($request->id);
        $messages = [
            'channel.required' => "The Channel field is required."
        ];

        $validated = $request->validate([
            'channel' => 'required',
        ], $messages);

        $data = [
            'user_id' => auth()->user()->id,
            'channel' => $request->channel
        ];

        $channel->update($data);

        return redirect('my/channels')->withSuccessMessage(trans('admin.success_update'));
    }//<--- End Method

    public function deleteChannel($id)
    {
        $channel = Channels::findOrFail($id);

        $channel->delete();

        return redirect('my/channels');
    }//<--- End Method
}
