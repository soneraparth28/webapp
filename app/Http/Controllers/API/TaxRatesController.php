<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Functions;
use Illuminate\Http\Request;
use App\Models\TaxRates;
use App\Models\States;
use App\Models\Countries;
use App\Models\AdminSettings;

class TaxRatesController extends Controller
{
    use Functions;
    public function __construct(AdminSettings $settings)
    {
        $this->settings = $settings::first();
    }
    public function show()
    {
        $taxes = TaxRates::orderBy('id', 'desc')->get();
        $response = [
            'success' => true,
            'data' => [
                'taxes' => $taxes,
            ],
            'message' => 'Taxes Rates Data.',
        ];
        return response()->json($response , 200);
//        return view('admin.tax-rates')->withTaxes($taxes);
    }
    public function edit($id)
    {
        $tax = TaxRates::findOrFail($id);
        $response = [
            'success' => true,
            'data' => [
                'tax' => $tax,
            ],
            'message' => 'Tax Rate Data.',
        ];
        return response()->json($response , 200);
//        return view('admin.edit-tax')->withTax($tax);
    }
}
