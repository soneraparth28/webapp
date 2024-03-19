<?php /** @noinspection ALL */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\AdminSettings;
use App\Helper;
use Carbon\Carbon;

class InstallScriptController extends Controller
{
    public function requirements()
    {
        dd('he;lo');
        try {
            // Check Datebase
            $settings = AdminSettings::first();
            $response = [
                'success' => true,
                'data' => [
                    'redirect_url' => '/',
                ],
                'message' => 'Redirect URL',
            ];
            return response()->json($response , 200);
//            return redirect('/');
        } catch (\Exception $e) {
            // empty
        }
        $minVersionPHP     = '7.3.0';
        $currentVersionPHP = (int) str_replace('.', '', phpversion());
        $versionPHP = version_compare(phpversion(), $minVersionPHP, '>=') ? true : false;
        // Extensions
        $BCMath    =  extension_loaded('BCMath') ? true : false;
        $Ctype     =  extension_loaded('Ctype') ? true : false;
        $Fileinfo  =  extension_loaded('Fileinfo') ? true : false;
        $openssl   =  extension_loaded('openssl') ? true : false;
        $pdo       =  extension_loaded('pdo') ? true : false;
        $mbstring  =  extension_loaded('mbstring') ? true : false;
        $tokenizer =  extension_loaded('tokenizer') ? true : false;
        $json      =  extension_loaded('JSON') ? true : false;
        $xml       =  extension_loaded('XML') ? true : false;
        $curl      =  extension_loaded('cURL') ? true : false;
        $gd        = extension_loaded('gd') ? true : false;
        $exif      = extension_loaded('exif') ? true : false;
        $allow_url_fopen = ini_get('allow_url_fopen') ? true : false;
        $response = [
            'success' => true,
            'data' => [
                'versionPHP' => $versionPHP,
                'minVersionPHP' => $minVersionPHP,
                'BCMath' => $BCMath,
                'Ctype' => $Ctype,
                'Fileinfo' => $Fileinfo,
                'openssl' => $openssl,
                'pdo' => $pdo,
                'mbstring' => $mbstring,
                'tokenizer' => $tokenizer,
                'json' => $json,
                'xml' => $xml,
                'curl' => $curl,
                'gd' => $gd,
                'exif' => $exif,
                'allow_url_fopen' => $allow_url_fopen,
            ],
            'message' => 'Installation Requirments.',
        ];
        return response()->json($response , 200);
//        return view('installer.requirements', [
//            'versionPHP' => $versionPHP,
//            'minVersionPHP' => $minVersionPHP,
//            'BCMath' => $BCMath,
//            'Ctype' => $Ctype,
//            'Fileinfo' => $Fileinfo,
//            'openssl' => $openssl,
//            'pdo' => $pdo,
//            'mbstring' => $mbstring,
//            'tokenizer' => $tokenizer,
//            'json' => $json,
//            'xml' => $xml,
//            'curl' => $curl,
//            'gd' => $gd,
//            'exif' => $exif,
//            'allow_url_fopen' => $allow_url_fopen
//        ]);
    }
    public function database()
    {
        try {
            // Check Datebase
            $settings = AdminSettings::first();
            $response = [
                'success' => true,
                'data' => [
                    'redirect_url' => '/',
                ],
                'message' => 'Redirect URL',
            ];
            return response()->json($response , 200);
//            return redirect('/');
        } catch (\Exception $e) {
            // empty
        }
        $response = [
            'success' => true,
            'data' => [
                'Page' => 'installer.database',
            ],
            'message' => 'Page View.',
        ];
        return response()->json($response , 200);
//        return view('installer.database');
    }
}
