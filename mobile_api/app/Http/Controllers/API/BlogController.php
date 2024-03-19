<?php /** @noinspection ALL */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Functions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\AdminSettings;
use App\Models\Blogs;
use App\Models\User;
use App\Helper;
use Carbon\Carbon;
use Mail;

class BlogController extends Controller
{
    use Functions;

    public function blog()
    {
        $blogs = Blogs::orderBy('id','desc')->paginate(10);
        $page = request()->get('page');
        if ($page > $blogs->lastPage()) {
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'Error Occured.',
            ];
            return response()->json($response , 404);
//            abort('404');
        }
        $response = [
            'success' => true,
            'data' => [
                'blogs' => $blogs,
            ],
            'message' => 'Blogs Data.',
        ];
        return response()->json($response , 200);
//        return view('index.blog', ['blogs' => $blogs]);
    }
    public function post($id)
    {
        $response = Blogs::whereId($id)->firstOrFail();
        $blogs    = Blogs::where('id','<>', $response->id)->inRandomOrder()->take(2)->get();
        $users    = $this->userExplore();
        $response = [
            'success' => true,
            'data' => [
                'response' => $response,
                'blogs' => $blogs,
                'users' => $users
            ],
            'message' => 'Blog Data.',
        ];
        return response()->json($response , 200);
//        return view('index.post', [
//            'response' => $response,
//            'blogs' => $blogs,
//            'users' => $users
//        ]);
    }
}
