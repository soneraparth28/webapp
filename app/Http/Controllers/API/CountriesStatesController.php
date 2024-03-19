<?php /** @noinspection ALL */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminSettings;
use App\Models\States;
use App\Models\Countries;
use App\Models\User;
use App\Helper;

class CountriesStatesController extends Controller
{
    public function __construct(AdminSettings $settings)
    {
        $this->settings = $settings::first();
    }
    public function countries()
    {
        $countries = Countries::orderBy('id', 'desc')->paginate(50);
        $response = [
            'success' => true,
            'data' => [
              'countries' => $countries,
            ],
            'message' => 'Countries Data.',
        ];
        return response()->json($response , 200);
//        return view('admin.countries')->withCountries($countries);
    }
    public function editCountry($id)
    {
        $country = Countries::findOrFail($id);
        $response = [
            'success' => true,
            'data' => [
                'Country' => $country,
            ],
            'message' => 'Country Data.',
        ];
        return response()->json($response , 200);
//        return view('admin.edit-country')->withCountry($country);
    }
    public function states()
    {
        $states = States::orderBy('id', 'desc')->paginate(50);
        $response = [
            'success' => true,
            'data' => [
                'states' => $states,
            ],
            'message' => 'States Data.',
        ];
        return response()->json($response , 200);
//        return view('admin.states')->withStates($states);
    }
    public function editState($id)
    {
        $state = States::findOrFail($id);
        $response = [
            'success' => true,
            'data' => [
                'state' => $states,
            ],
            'message' => 'State Data.',
        ];
        return response()->json($response , 200);
//        return view('admin.edit-state')->withState($state);
    }
}
