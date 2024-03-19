<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Models\User;
use App\Models\Pages;
use Illuminate\Support\Facades\Validator;

class PagesController extends Controller
{
    public function show($page)
	{
		// $response = Pages::where('slug','=', $page)->whereLang(session('locale'))->first();
        $response = Pages::where('slug','=', $page)->first();
		if(! $response) {
			$response = Pages::whereLang(env('DEFAULT_LOCALE'))->where('slug','=', $page)->first();
		}
		if(! $response) {
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'Error Occured',
            ];
            return response()->json($response , 404);
			// abort(404);
		}
		if($response->access == 'creators' && auth()->guest() || $response->access == 'creators' && auth()->check() && auth()->user()->verified_id != 'yes') {
			$response = [
                'success' => false,
                'data' => null,
                'message' => 'Error Occured',
            ];
            return response()->json($response , 404);
            abort(404);
		}
		if($response->access == 'members' && auth()->guest()) {
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'Error Occured',
            ];
            return response()->json($response , 404);
			abort(404);
		}
        $response = [
            'success' => true,
            'data' => [
                'page_data' => $response,
            ],
            'message' => 'Page Data.',
        ];
        return response()->json($response , 200);
		// return view('pages.show')->withResponse($response);
	}
    public function index()
	{
	 	$data = Pages::all();
        $response = [
            'success' => true,
            'data' => [
                'pages' => $data,
            ],
            'message' => 'Page Data.',
        ];
        return response()->json($response , 200);
		// return view('admin.pages')->withData($data);
	}
    public function create()
	{
        $response = [
            'success' => true,
            'data' => null,
            'message' => 'Create New Page View.',
        ];
        return response()->json($response , 200);
    	// return view('admin.add-page');
	}
    public function edit($id)
	{
		$data = Pages::findOrFail($id);
        $response = [
            'success' => true,
            'data' => [
                'page_data' => $data,
            ],
            'message' => 'Page Edit Data.',
        ];
        return response()->json($response , 200);
		// return view('admin.edit-page')->withData($data);
	}
}
