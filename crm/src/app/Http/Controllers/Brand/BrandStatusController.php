<?php


namespace App\Http\Controllers\Brand;


use App\Http\Controllers\Controller;
use App\Models\Core\App\Brand\Brand;
use App\Notifications\Brand\BrandNotification;
use App\Repositories\App\StatusRepository;
use App\Services\Core\Brand\BrandService;
use Illuminate\Http\Request;

class BrandStatusController extends Controller
{
    public function __construct(BrandService $service)
    {
        $this->service = $service;
    }

    public function update(Brand $brand, Request $request)
    {
        $statuses = resolve(StatusRepository::class)->brand()
            ->pluck('id', 'name');

        $status = $brand->isActive() ? 'status_inactive' : 'status_active';

        $brand->update([
            'status_id' => $statuses[$status]
        ]);
        notify()
            ->on($status == 'status_active' ? 'brand_activated' : 'brand_deactivated')
            ->with($brand)
            ->send(BrandNotification::class);

        return status_response('brand', $status);
    }
}