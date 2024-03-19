<?php


namespace App\Helpers\Traits;


use App\Exceptions\GeneralException;
use App\Models\Core\App\Brand\Brand;

trait BrandInactiveTrait
{
    public function actionIfInactive($brand_id = null)
    {
        $brand = $brand_id
            ? Brand::finByShortNameOrIdCached($brand_id)
            : \brand();

        if ($brand) {
            throw_if(
                $brand->isInactive(),
                new GeneralException(__t('inactive_brand_action'))
            );
        }
        return true;
    }
}
