<?php /** @noinspection ALL */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Purchases;
use App\Models\User;
use App\Models\MediaProducts;
use App\Models\Notifications;
use App\Models\AdminSettings;
use App\Models\TaxRates;
use App\Models\Withdrawals;
use App\Models\ReferralTransactions;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Notifications\NewSale;
use Illuminate\Http\File;
use Illuminate\Validation\Rule;
use App\Helper;
use App\Http\Controllers\Traits\Functions;

class ProductsController extends Controller
{
    use Functions;
    public function __construct(AdminSettings $settings, Request $request)
    {
		$this->settings = $settings::first();
		$this->request = $request;
	}
    public function index()
    {
        if(! $this->settings->shop) {
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'Error Occured.',
            ];
            return response()->json($response , 200);
            // abort(404);
        }
        $tags = request('tags');
        $sort = request('sort');
        $products = Products::with('seller:name,username,avatar')->whereStatus('1');
        // Filter by tags
        $products->when(strlen($tags) > 2, function($q) use ($tags) {
            $q->where('tags', 'LIKE', '%'.$tags.'%');
        });
        // Filter by oldest
        $products->when($sort == 'oldest', function($q) {
            $q->orderBy('id', 'asc');
        });
        // Filter by lowest price
        $products->when($sort == 'priceMin', function($q) {
            $q->orderBy('price', 'asc');
        });
        // Filter by Highest price
        $products->when($sort == 'priceMax', function($q) {
            $q->orderBy('price', 'desc');
        });
        // Filter by Digital Products
        $products->when($sort == 'digital', function($q) {
            $q->where('type', 'digital');
        });
        // Filter by Custom Content
        $products->when($sort == 'custom', function($q) {
            $q->where('type', 'custom');
        });
        $products = $products->orderBy('id', 'desc')->paginate(15);
        $response = [
            'success' => true,
            'data' => [
                'products' => $products,
            ],
            'message' => 'Products Data.',
        ];
        return response()->json($response , 200);
        // return view('shop.products')->withProducts($products);
    }
    public function show($id)
    {
        if(! $this->settings->shop) {
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'Error Occured.',
            ];
            return response()->json($response , 404);
            // abort(404);
        }
        $product = Products::findOrFail($id);
        if(! $product->status && auth()->id() != $product->user()->id || ! $product->status && auth()->check() && auth()->user()->role == 'normal') {
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'Error Occured.',
            ];
            return response()->json($response , 404);
            // abort(404);
        }
        $uri = $this->request->path();
		if(str_slug($product->name) == '') {
			$slugUrl  = '';
		} else {
			$slugUrl  = '/'.str_slug($product->name);
		}
		$urlImage = 'shop/product/'.$product->id.$slugUrl;
        //<<<-- * Redirect the user real page * -->>>
		$uriImage     =  $this->request->path();
		$uriCanonical = $urlImage;
		if($uriImage != $uriCanonical) {
			return redirect($uriCanonical);
		}
        // Tags
        $tags = explode(',', $product->tags);
        // Previews
        $previews = count($product->previews);
        if(auth()->check()) {
            $verifyPurchaseUser = $product->purchases()->whereUserId(auth()->id())->first();
        }
        // Total Items of User
        $userProducts = $product->user()->products()->whereStatus('1');
        $response = [
            'success' => true,
            'data' => [
                'product' => $product,
                'userProducts' => $userProducts,
                'tags' => $tags,
                'previews' => $previews,
                'verifyPurchaseUser' => $verifyPurchaseUser ?? null,
                'totalProducts' => $userProducts->count(),
            ],
            'message' => 'Product Data.',
        ];
        return response()->json($response , 200);
        // return view('shop.show')->with([
        //     'product' => $product,
        //     'userProducts' => $userProducts,
        //     'tags' => $tags,
        //     'previews' => $previews,
        //     'verifyPurchaseUser' => $verifyPurchaseUser ?? null,
        //     'totalProducts' => $userProducts->count()
        // ]);
    }
    public function create()
    {
        if(auth()->check() && auth()->user()->verified_id != 'yes' || ! $this->settings->shop || ! $this->settings->digital_product_sale) {
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'Error Occured.',
            ];
            return response()->json($response , 404);
//            abort(404);
        }
        $response = [
            'success' => true,
            'data' => null,
            'message' => 'Product Create Page View.',
        ];
        return response()->json($response , 200);
//        return view('shop.add-product');
    }
    public function createCustomContent()
    {
        if(auth()->check() && auth()->user()->verified_id != 'yes' || ! $this->settings->shop || ! $this->settings->custom_content) {
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'Error Occured.',
            ];
            return response()->json($response , 404);
//            abort(404);
        }
        $response = [
            'success' => true,
            'data' => null,
            'message' => 'Create Custom Product Content Page View.',
        ];
        return response()->json($response , 200);
//        return view('shop.add-custom-content');
    }
    public function download($id)
    {
        $item = Products::whereId($id)->whereType('digital')->firstOrFail();
        $file = $item->purchases()->where('user_id', auth()->id())->first();
        if(! $file && auth()->user()->role != 'admin') {
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'Error Occured.',
            ];
            return response()->json($response , 404);
//            abort(404);
        }
        $pathFile = config('path.shop').$item->file;
        $headers = [
            'Content-Type:' => $item->mime,
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];
        $response = [
            'success' => true,
            'data' => [
                'header' => $headers,
                'path' => $pathFile,
                'file_name' => $item->name.'.'.$item->extension,
            ],
            'message' => 'Download File.',
        ];
        return response()->json($response , 200);
//        return Storage::download($pathFile, $item->name.'.'.$item->extension, $headers);

    }
}
