<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Updates;
use App\Models\Messages;
use App\Models\AdminSettings;
use App\Models\Media;
use Carbon\Carbon;
use App\Helper;
use Image;
use FileUploader;

class UploadMediaPreviewShopController extends Controller
{
    public function __construct(AdminSettings $settings, Request $request)
    {
        $this->settings = $settings::first();
        $this->request = $request;
        $this->middleware('auth');
    }
    public function store()
    {
        $publicPath = public_path('temp/');
        $file = strtolower(auth()->id().uniqid().time().str_random(20));
        // initialize FileUploader
        $FileUploader = new FileUploader('preview', array(
            'limit' => 5,
            'fileMaxSize' => floor($this->settings->file_size_allowed / 1024),
            'extensions' => [
                'png',
                'jpeg',
                'jpg'
            ],
            'title' => $file,
            'uploadDir' => $publicPath
        ));
        // upload
        $upload = $FileUploader->upload();
        if ($upload['isSuccess']) {
            foreach($upload['files'] as $key=>$item) {
                $upload['files'][$key] = [
                    'extension' => $item['extension'],
                    'format' => $item['format'],
                    'name' => $item['name'],
                    'size' => $item['size'],
                    'size2' => $item['size2'],
                    'type' => $item['type'],
                    'uploaded' => true,
                    'replaced' => false
                ];
                $this->resizeImage($item['name'], $item['extension']);
            }// foreach
        }// upload isSuccess
        $response = [
            'success' => true,
            'data' => [
                'upload_data' => $upload,
            ],
            'message' => 'Upload Data.',
        ];
        return response()->json($upload , 200);
//        return response()->json($upload);
    }
}
