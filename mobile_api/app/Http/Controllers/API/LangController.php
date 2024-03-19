<?php /** @noinspection ALL */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Languages;
use Illuminate\Support\Facades\Validator;

class LangController extends Controller
{
    public function index()
    {
        $data = Languages::all();
        $response = [
            'success' => true,
            'data' => [
                'languages' => $data,
            ],
            'message' => 'Languages Data.',
        ];
        return response()->json($response , 200);
//        return view('admin.languages')->withData($data);
    }
    public function create()
    {
        $response = [
            'success' => true,
            'data' => null,
            'message' => 'Page View.',
        ];
        return response()->json($response , 200);
//        return view('admin.add-languages');
    }
    public function edit($id)
    {
        $data = Languages::findOrFail($id);
        $response = [
            'success' => true,
            'data' => [
                'language' => $data,
            ],
            'message' => 'Language Data.',
        ];
        return response()->json($response , 200);
//        return view('admin.edit-languages')->withData($data);

    }
}
