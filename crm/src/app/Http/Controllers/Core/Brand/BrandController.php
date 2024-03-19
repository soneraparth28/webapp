<?php

namespace App\Http\Controllers\Core\Brand;

use App\Filters\Brand\BrandFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Core\Brand\BrandRequest;
use App\Jobs\BrandCreated;
use App\Models\Core\App\Brand\Brand;
use App\Notifications\Brand\BrandNotification;
use App\Repositories\App\StatusRepository;
use App\Services\Core\Brand\BrandService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;

class BrandController extends Controller
{
    public function __construct(BrandService $brand, BrandFilter $filter)
    {
        $this->service = $brand;
        $this->filter = $filter;
    }


    public function list()
    {
        return view('application.views.brands.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $status_id = resolve(StatusRepository::class)->subscriberSubscribed();
        return $this->service
            ->with('status:id,name,class')
            ->withCount([
                'subscribers' => function(Builder $builder) use($status_id) {
                    $builder->where('status_id', $status_id);
                },
                'campaigns'
            ])
            ->filters($this->filter)
            ->latest('id')
            ->paginate(request('per_page', 15));
    }



    public function store(BrandRequest $request)
    {
        $brand = $this->service
            ->save();

        if ($brand) {
            $this->service
                ->cloneGlobalSettings();
            dispatch(
                (new BrandCreated($brand))->onConnection('sync')
            );
        }

        notify()
            ->on('brand_created')
            ->with($brand)
            ->send(BrandNotification::class);

        return created_responses('brand');
    }


    public function show(Brand $brand)
    {
        return $brand;
    }



    public function update(Brand $brand, BrandRequest $request)
    {
        $brand->fill($request->only('name', 'brand_group_id'));
        if ($brand->isDirty()) {
            notify()
                ->on('brand_updated')
                ->with($brand)
                ->send(BrandNotification::class);
        }
        $brand->save();

        return updated_responses('brand');
    }



    public function destroy(Brand $brand)
    {
//        if ($brand->delete()) {
//            notify()
//                ->on('brand_deleted')
//                ->with((object)$brand->toArray())
//                ->send(BrandNotification::class);
//            return deleted_responses('brand');
//        }

        return failed_responses();
    }
}
