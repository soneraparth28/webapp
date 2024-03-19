<?php
namespace App\Http\Controllers;

use App\Models\CourseCompletion;
use App\Models\CourseLesson;
use App\Models\MediaView;
use App\Models\PaymentGateways;
use App\Models\PayPerViews;
use App\Models\UpdateEmailFlows;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AdminSettings;
use App\Models\Notifications;
use App\Models\Comments;
use App\Models\Like;
use App\Models\Updates;
use App\Models\Reports;
use App\Models\Messages;
use App\Models\Media;
use App\Models\UpdateCategories;
use App\Models\MediaMessages;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\ArrayShape;
use League\Glide\Responses\LaravelResponseFactory;
use App\Notifications\NewPost;
use League\Glide\ServerFactory;
use App\Events\NewPostEvent;
use App\Jobs\EncodeVideo;
use Carbon\Carbon;
use App\Helper;
use Image;
use Form;
use Mail;
use stdClass;


class CourseContentController extends Controller
{
    use Traits\Functions;

    private const COMPLETION_TRIVIALITY  = 80;

    public function __construct(AdminSettings $settings, Request $request)
    {
        $this->settings = $settings::first();
        $this->request = $request;
    }




    public function dashboard() {
        if (auth()->guest()) return redirect('/login');
        $updates = Updates::where("user_id", auth()->id())->where("course", "yes")->get();
        $courses = [];
        if(!$updates->isEmpty()) {
            foreach ($updates as $update) $courses[] = $this->courseItemsByUpdatesId($update->id, true);
        }
        
        // echo "<pre>";
        // print_r(json_decode(json_encode($courses), true));
        // exit();

        return view('users.course-dashboard', ["courses" => $courses]);
    }


    public function dashboardSingleView() {
        if (auth()->guest()) return redirect('/login');
        $course = $this->courseItemsByUpdatesId($this->request->id, true);
        if(!auth()->user()->isAdmin() && $course["update"]->user()->id() != auth()->id()) return redirect('/');
        return view('users.course-dashboard', $course);
    }


    public function index() {
        if (auth()->guest()) return redirect('/login');
        return view('users.create-course', ['edit' => false]);
    }



    public function viewCourse() {
        // Home Guest
        if (auth()->guest()) return redirect('/login');

        $course = $this->courseItemsByUpdatesId($this->request->id);
        
        // echo "<pre>";
        // print_r(json_decode(json_encode($course), true));
        // exit();
        
        $update = $course["update"];
        $access = $update->access;
        $locked = $update->locked;

        $paymentAction = "none";
        if(!auth()->user()->isAdmin() && $update->user_id != auth()->id() && !((empty($access) || $access === "public") && $locked === "no")) {
            if($access === "paid" && PayPerViews::where("updates_id", $this->request->id)->where("user_id", auth()->id())->count() < 1) $paymentAction = "payment";
            elseif($access === "subscribers" && is_null(auth()->user()->checkSubscription($update->user()))) $paymentAction = "subscribe";
        }

        if($paymentAction !== "none") {
            $course["user"] = $update->user();
            $course["plans"] = $update->user()->plans()
                ->where('interval', '<>', 'monthly')
                ->whereStatus('1')
                ->get();
            $course["allPayment"] = PaymentGateways::where('enabled', '1')->whereSubscription('yes')->get();
        }
        if($paymentAction === "payment") {
            $course["updatePaymentData"] = [
                "price" => $update->price,
                "updateId" => $update->id
            ];
        }

        $course["accessAction"] = $paymentAction;
        
        // echo "<pre>"; 
        // print_r(json_decode(json_encode($course), true));
        // exit();
        
        return view('includes.view-course', $course);
    }

    public function viewEditCourse() {
        $courseId = $this->request->id;
        $update = Updates::whereId($courseId)->first();

        if(is_null($update) || $update->course !== "yes") abort(404);
        if(auth()->id() != $update->user_id) return redirect(url("course", $courseId));

        $data = $this->courseItemsByUpdatesId($this->request->id);
        $data["edit"] = true;
        
        // echo "<pre>";
        // print_r(json_decode(json_encode($data), true));
        // exit();
        
        return view('users.edit-course', $data);
    } 


    public function myCourses() {
        // Home Guest
        if (auth()->guest()) return redirect('/login');

        $userId = Auth()->id();
        $updates = [];

        if(auth()->user()->verified_id !== 'yes' || request()->is('my-courses')) {
            $completions = CourseCompletion::where("user_id", $userId)->get();
            $completedLessons = CourseLesson::where("user_id", $userId)->get();

            $completedUpdateIds = $startedButNotCompletedIds = [];
            if($completions->count() > 0) {
                foreach ($completions as $completion) $completedUpdateIds[] = $completion->updates_id;
            }
            if($completedLessons->count() > 0) {
                foreach ($completedLessons as $lesson) if(!in_array($lesson->updates_id, $startedButNotCompletedIds))
                    $startedButNotCompletedIds[] = $lesson->updates_id;
            }


            $updatesRaw = Updates::whereIn("id", array_merge($completedUpdateIds, $startedButNotCompletedIds), "OR")->get();
            if($updatesRaw->count() > 0) {
                foreach ($updatesRaw as $update) {
                    $hold = $update;
                    $hold->completed = in_array($update->id, $completedUpdateIds) ? "yes" : "no";
                    $hold->started = in_array($update->id, $startedButNotCompletedIds) || $hold->completed === "yes" ? "yes" : "no";
                    $updates[] = $hold;
                }
            }

//            $updatesCompleted = $updatesStarted = [];
//            if(!empty($completedUpdateIds)) {
//                foreach ($completedUpdateIds as $updateId) $updatesCompleted[] = Updates::where("id", $updateId)->get()->first();
//            }
//            if(!empty($startedButNotCompletedIds)) {
//                foreach ($startedButNotCompletedIds as $updateId) $updatesStarted[] = Updates::where("id", $updateId)->get()->first();
//            }

            $returnObject = [ "courses" => $updates, "userId" => $userId ];

//            $returnObject = ["updatesCompleted" => $updatesCompleted, "updatesStarted" => $updatesStarted, "userId" => $userId ];
        }
        else {
            foreach (Updates::where("user_id",$userId)->where("course", "yes")->get() as $update) {
                $hold = $update;
                $hold->completions = CourseCompletion::where("updates_id", $update->id)->get();
                $updates[] = $hold;
            }
            $returnObject = [ "courses" => $updates, "userId" => $userId ];
        }


        return view(
            'users.my_courses', $returnObject
        );
    }


    private function courseItemsByUpdatesId($updateId, $dashboardDetailedView = false) {
        $update = Updates::whereId($updateId)->first();
        // $lessons = CourseLesson::whereUpdatesId($updateId)->orderBy("lesson_index", "asc")->get(); 
        $lessons = CourseLesson::whereUpdatesId($updateId)->orderBy("order_number", "asc")->orderBy("lesson_index", "asc")->get(); 
        $modules = array(); $allLessons = array();
        $lessonCount = empty($lessons) ? 0 : count(json_decode(json_encode($lessons), true));
        $lessonMaxKey = $lessonCount -1;
        if(!empty($lessons)) {
            $i = 0;
            foreach ($lessons as $lesson) {
                $moduleId = $lesson->module_id;
                if(!array_key_exists($moduleId, $modules)) {
                    $modules[$moduleId] = array();
                    $modules[$moduleId]["module_title"] = $lesson->module_title;
                    $modules[$moduleId]["duration"] = $lesson->duration;
                    $modules[$moduleId]["user_id"] = $lesson->user_id;
                    $modules[$moduleId]["lessons_completed"] = 0;
                    $modules[$moduleId]["is_completed"] = false;
                }

                if(!array_key_exists("lessons", $modules[$moduleId])) $modules[$moduleId]["lessons"] = array();

                $nextIterate = $i +1;
                $previousIterate = $i -1;

                if($nextIterate > $lessonMaxKey) $next = null;
                else {
                    $nextLesson = $lessons[$nextIterate];
                    $next = $nextLesson->module_id . "-" . $nextLesson->lesson_id;
                }

                if($previousIterate < 0) $previous = null;
                else {
                    $previousLesson = $lessons[$previousIterate];
                    $previous = $previousLesson->module_id . "-" . $previousLesson->lesson_id;
                }

                $mediaItem = Media::whereId($lesson->media_id)->get()->first();
                if(empty($mediaItem)) { $media = ""; $file = ""; }
                else {
                    $media = !empty($mediaItem->video) ? asset("public/uploads/updates/videos/" . $mediaItem->video) :
                        (!empty($mediaItem->video_embed) ? $mediaItem->video_embed : "");
                }

                $lesson->current_id = $lesson->module_id . "-" . $lesson->lesson_id;
                $lesson->next = $next;
                $lesson->previous = $previous;
                $lesson->media = $media;
                $lesson->is_embed = !empty($mediaItem->video_embed);


                if(!$dashboardDetailedView) {
                    $watchedDetails = $this->mediaViewsWatched($updateId, $lesson->media_id);
                    $lesson->percentage_watched = $watchedDetails["percentage_watched"];
                    $lesson->is_completed = $watchedDetails["is_completed"];
                    if($lesson->is_completed) $modules[$moduleId]["lessons_completed"]++;
                }



                $modules[$moduleId]["lessons"][] = $lesson;
                $allLessons[] = $lesson;
                $i++;
            }
        }

        foreach ($modules as $n => $module) {
            if(count($module["lessons"]) === $module["lessons_completed"]) $modules[$n]["is_completed"] = true;
        }

        return [
            'update' => $update,
            'lessons' => $lessons,
            'modules' => $modules,
            'allLessons' => $allLessons,
            'courseCompletion' => $this->courseCompletionStatus($updateId),
            'courseStatistics' => $dashboardDetailedView ? $this->courseStatistics($update) : new stdClass()
        ];
    }

    public function mediaViewAjaxCreateForEmbed(Request $request) {
        if(!$this->mediaViewRowItemExists($request->update_id, $request->media_id)) $this->createMediaViewItem($request->update_id, $request->media_id);
        return $this->mediaViewsWatched($request->update_id, $request->media_id);
    }
    public function mediaView(Request $request) {
        return $this->mediaViewsWatched($request->update_id, $request->media_id);
    }
    public function courseCompletion(Request $request) {
        return $this->courseCompletionStatus($request->update_id);
    }


    public function complete(Request $request) {
        $updateId = $request->update_id;
        $userId = Auth()->id();
        $isCompleted = false;

        if(!empty(CourseCompletion::where("updates_id", $updateId)->where("user_id", $userId)->get()->first())) $isCompleted = true;
        elseif($this->courseCompletionStatus($updateId)["course_is_completed"]) {
            try {
                CourseCompletion::create([
                    'updates_id' => $updateId,
                    'user_id' => $userId,
                    'updated_at' => now(),
                    'created_at' => now()
                ]);
                $isCompleted = true;
            } catch (\Illuminate\Database\QueryException $exception) {
                $errorInfo = $exception->errorInfo;
                return response()->json(["status" => "error", "message" => $errorInfo]);
            }
        }

        if($isCompleted) $response = ["status" => "success", "redirect_url" => url("course-completion/$updateId")];
        else $response = ["status" => "error", "message" => "You have not completed the course"];

        return response()->json($response);
    }


    public function courseDiploma() {
        $userId = Auth()->id();
        $updateId = $this->request->id;
        if(empty($updateId)) {
            abort(404);
        }

        if(empty(CourseCompletion::where("updates_id", $updateId)->where("user_id", $userId)->get()->first())) abort(404);
        return view('users.course_diploma', [
            'completionDetails' => CourseCompletion::where("updates_id", $updateId)->where("user_id", $userId)->get()->first(),
            'update' => Updates::where("id", $updateId)->get()->first(),
            'user' => Auth::user()->get()->first()
        ]);
    }

    public function courseCompletionPage() {
        $userId = Auth()->id();
        $updateId = $this->request->id;
        if(empty($updateId)) {
            abort(404);
        }

        if(empty(CourseCompletion::where("updates_id", $updateId)->where("user_id", $userId)->get()->first())) abort(404);
        return view('includes.course_completion_page', [
            'completionDetails' => CourseCompletion::where("updates_id", $updateId)->where("user_id", $userId)->get()->first(),
            'update' => Updates::where("id", $updateId)->get()->first(),
            'user' => Auth::user()->get()->first()
        ]);
    }


    private function createMediaViewItem($updateId, $mediaId) {
        $media = Media::whereId($mediaId)->get()->first();
        if(empty($media)) return;

        MediaView::create([
            'updates_id' => $updateId,
            'media_id' => $mediaId,
            'user_id' => auth()->id(),
            'is_embed' => !empty($media->video_embed),
            'percentage_watched' => 100,
            'updated_at' => now()
        ]);
    }



    private function mediaViewsWatched($updateId, $mediaId, $triviality = self::COMPLETION_TRIVIALITY): array {
        $mediaViewRow = $this->mediaViewRowItem($updateId, $mediaId);
        $percentageWatched = !empty($mediaViewRow) ? $mediaViewRow->percentage_watched : 0;
        $isCompleted = $percentageWatched >= $triviality;

        return array_merge(
            [
                "percentage_watched" => $percentageWatched,
                "is_completed" => $isCompleted,
            ],
            $this->courseCompletionStatus($updateId)
        );
    }


    private function courseStatistics($update): stdClass {
        $res = new stdClass();
        $res->purchases = PayPerViews::where("updates_id", $update->id)->count();
        $res->completions = CourseCompletion::where("updates_id", $update->id)->count();
        return $res;
    }





    private function mediaViewRowItem($updateId, $mediaId) {
        return MediaView::whereUpdatesId($updateId)->whereUserId(auth()->id())->whereMediaId($mediaId)->get()->first();
    }
    private function mediaViewRowItemExists($updateId, $mediaId): bool { return !empty($this->mediaViewRowItem($updateId, $mediaId)); }


    private function courseCompletionStatus($updateId): array {
        $res = ["course_total_lessons" => 0, "course_completed_lessons" => 0, "course_is_completed" => false];
        $lessons = CourseLesson::whereUpdatesId($updateId)->get();
        if(empty($lessons)) return $res;;

        $res["course_total_lessons"] = $lessons->count();
        $mediaViews = MediaView::where("updates_id", $updateId)->where("user_id", Auth()->id())->get();

        if(!empty($mediaViews)) {
            $viewsMediaIds = [];
            foreach ($mediaViews as $mediaView) $viewsMediaIds[] = $mediaView->media_id;
            foreach ($lessons as $lesson) if(in_array($lesson->media_id, $viewsMediaIds)) $res["course_completed_lessons"]++;
        }

        if($res["course_total_lessons"] === $res["course_completed_lessons"]) $res["course_is_completed"] = true;
        return $res;
    }



    public function create() {
        $input = $this->request->all();
        if(!array_key_exists("course_content", $input)) return response()->json([
            'success' => false,
            'errors' => ['error' => trans('general.missing_elements')],
        ]);

        $courseContent = $input["course_content"];
        if(!is_array($courseContent)) $courseContent = json_decode($courseContent, true);


        $courseSettings = array_key_exists("settings", $courseContent) ? $courseContent["settings"] : null;
        $content = array_key_exists("content", $courseContent) ? $courseContent["content"] : null;
        $edit = array_key_exists("edit", $courseContent) ? (int)$courseContent["edit"] === 1 : false;
        $courseId = array_key_exists("courseId", $courseContent) ? $courseContent["courseId"] : 0;

        // Video URL of Youtube, Vimeo
        $videoUrl = '';

        // Currency Position
        if ($this->settings->currency_position == 'right') {
            $currencyPosition = 2;
        } else {
            $currencyPosition = null;
        }

        if (auth()->user()->verified_id != 'yes') {
            return response()->json([
                'success' => false,
                'errors' => ['error' => trans('general.error_post_not_verified')],
            ]);
        }


        $messages = [
            'settings.required' => trans('general.please_write_something'), //settings trans..!
            'content.required' => trans('general.please_write_something'), //content trans..!
            'cover_image.required' => trans('general.please_write_something'), //coverimaaag
            'description.required' => trans('general.please_write_something'),
            'description.min' => trans('validation.update_min_length'),
            'description.max' => trans('validation.update_max_length'),
            'price.min' => trans('general.amount_minimum' . $currencyPosition, ['symbol' => $this->settings->currency_symbol, 'code' => $this->settings->currency_code]),
            'price.max' => trans('general.amount_maximum' . $currencyPosition, ['symbol' => $this->settings->currency_symbol, 'code' => $this->settings->currency_code]),
        ];

        if(!$courseSettings) {
            return response()->json([
                'success' => false,
                'errors' => ['error' => trans($messages["settings.required"])],
            ]);
        }
        if(!$content) {
            return response()->json([
                'success' => false,
                'errors' => ['error' => trans($messages["content.required"])],
            ]);
        }


        if($edit) {
            $update = Updates::whereId($courseId)->where("user_id", auth()->id())->first();
            if(is_null($update) || $update->course !== "yes") return response()->json([
                'success' => false,
                'errors' => ['error' => 'You are not authorized to edit this course'],
            ]);

        }



        $validator = Validator::make($courseSettings, [
            'title' => 'required|min:1|max:65', //title length ?
            'description' => 'required|min:1|max:' . $this->settings->update_length,
            'price' => 'numeric|min:0|max:' . $this->settings->max_ppv_amount,
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ]);
        } //<-- Validator



        // Validating module content
        foreach ($content as $module) {
            foreach (array("title", "duration", "content_id", "lessons") as $requiredKeys)
                if(!array_key_exists($requiredKeys, $module)) return response()->json([
                    'success' => false,
                    'errors' => ['error' => ucfirst(trans("general.missing_elements"))],
                ]);

            $moduleTitle = trim($module["title"]);
            $moduleDuration = intval($module["duration"]);
            $moduleId = trim($module["content_id"]);

            $error = null;

            if(empty($moduleTitle) || strlen($moduleTitle) < 5) $error = "general.module_title_error_min_end";
            if(strlen($moduleTitle) > 65) $error = "general.module_title_error_max_end";

            if($error) return response()->json([
                'success' => false,
                'errors' => ['error' => trans("general.module_title_error_start") . " " . $moduleId . " " . trans($error)],
            ]);

            foreach ($module["lessons"] as $lesson) {
                foreach (array("title", "content_id", "video") as $requiredKeys)
                    if(!array_key_exists($requiredKeys, $lesson)) return response()->json([
                        'success' => false,
                        'errors' => ['error' => ucfirst(trans("general.missing_elements"))],
                    ]);

                $lessonTitle = trim($lesson["title"]);
                $lessonFile = array_key_exists("lesson_file", $lesson) ? trim($lesson["lesson_file"]) : "";
                $lessonDescription = array_key_exists("description", $lesson) ? trim($lesson["description"]) : "";
                $lessonId = trim($lesson["content_id"]);
                $lessonNo = explode("-", $lessonId)[1];


                if(empty($lessonTitle) || strlen($lessonTitle) < 2) $error = "general.module_title_error_min_end";
                if(strlen($lessonTitle) > 65) $error = "general.module_title_error_max_end";


                if($error) return response()->json([
                    'success' => false,
                    'errors' => [
                        'error' =>
                            trans("general.lesson_title_error_start") . " " .
                            $lessonNo . " " .
                            trans("general.in") . " " .
                            trans("general.module") . " " .
                            $moduleId . " " .
                            trans($error)
                    ],
                ]);


                if(strlen($lessonDescription) > $this->settings->update_length) $error = "general.lesson_description_error_max_end";
                if($error) return response()->json([
                    'success' => false,
                    'errors' => [
                        'error' =>
                            trans("general.lesson_description_error_start") . " " .
                            $lessonNo . " " .
                            trans("general.in") . " " .
                            trans("general.module") . " " .
                            $moduleId . " " .
                            trans($error) . " " .
                            $this->settings->update_length . " " .
                            trans("general.characters")
                    ],
                ]);
            }
        }




        $lessons = array_reduce($content, function ($initial, $module) { return array_merge((!isset($initial) ? array() : $initial), $module["lessons"]); });
        $files = array_map(function ($lesson) { return $lesson["video"]; }, $lessons);
        $lesson_files = array_map(function ($lesson) { return isset($lesson["lesson_file"]) ? $lesson["lesson_file"] : ''; }, $lessons);
        $files = array_merge($files, $lesson_files);
        $mediaFiles = array_values(array_filter($files, function ($file) {
            return !filter_var($file, FILTER_VALIDATE_URL);
        }));

        $mediaEmbed = array_values(array_filter($files, function ($file) {
            return filter_var($file, FILTER_VALIDATE_URL);
        }));

        $mediaBaseDirectory = base_path() . "/public/uploads/updates/";
        if(!empty($mediaFiles)){
            foreach ($mediaFiles as $file) {
                if($file != '')
                {
                    if(!file_exists($mediaBaseDirectory . "videos/$file") && !file_exists($mediaBaseDirectory . "files/$file")) {
                        return response()->json([
                            'success' => false,
                            'errors' => ['error' => trans($messages["content.required"])],
                        ]);
                    }
                }
            }
        }

        if($courseSettings["cover_image"] != "")
        {
            if(!file_exists($mediaBaseDirectory . "images/" . $courseSettings["cover_image"])) {
                return response()->json([
                    'success' => false,
                    'errors' => ['error' => trans($messages["cover_image.required"])],
                ]);
            }

            $mediaFiles[] = $courseSettings["cover_image"];
    }

        //<===== Locked Content
        if ($courseSettings["access"] !== "public") {
            $locked = 'yes';
        } else {
            $locked = 'no';
        }

        if($edit) {
            $updateParams = [
                "title" => trim(Helper::checkTextDb($courseSettings["title"])),
                "description" => trim(Helper::checkTextDb($courseSettings["description"])),
                "locked" => $locked,
                "price" => $courseSettings["price"],
                "access" => $courseSettings["access"],
                "strict_flow" => $courseSettings["access"] ? "yes" : "no",
                "media_downloadable" => $courseSettings["media_downloadable"] ? "yes" : "no",
                "image" => $courseSettings["cover_image"]
            ];
            Updates::where("id", $courseId)->update($updateParams);
            $post = Updates::where("id", $courseId)->first();
        }
        else {
            $post = new Updates();
            $post->user_id = auth()->id();
            $post->date = Carbon::now();
            $post->status = $this->settings->auto_approve_post == 'on' ? 'active' : 'pending';
            $post->course = "yes";
            $post->title = trim(Helper::checkTextDb($courseSettings["title"]));
            $post->description = trim(Helper::checkTextDb($courseSettings["description"]));
            $post->token_id = Str::random(150);
            $post->locked = $locked;
            $post->price = $courseSettings["price"];
            $post->update_category_id = NULL;
            $post->access = $courseSettings["access"];
            $post->strict_flow = $courseSettings["access"] ? "yes" : "no";
            $post->media_downloadable = $courseSettings["media_downloadable"] ? "yes" : "no";
            $post->image = $courseSettings["cover_image"];
            $post->save();
        }


        // Save blocked post option
        $user = auth()->user();
        $user->post_locked = $locked === "yes";
        $user->save();

        // Insert Files
        if (!empty($mediaFiles)) {
            foreach ($mediaFiles as $file) {
                if($file != '')
                {
                    Media::whereImage($file)
                        ->orWhere('video', $file)
                        ->orWhere('music', $file)
                        ->orWhere('file', $file)
                        ->update([
                            'updates_id' => $post->id,
                            'user_id' => auth()->id(),
                            'status' => 'active'
                        ]);
                }
            }
        }

        // Insert Video Embed Youtube or Vimeo
        if (!empty($mediaEmbed)) {
            foreach ($mediaEmbed as $embedUrl) {
                $token = Str::random(150) . uniqid() . now()->timestamp;

                if(Media::where("updates_id", $post->id)->where("user_id", auth()->id())->where("video_embed", $embedUrl)->count() < 1) {
                    Media::create([
                        'updates_id' => $post->id,
                        'user_id' => auth()->id(),
                        'type' => 'video',
                        'image' => '',
                        'video' => '',
                        'video_embed' => $embedUrl,
                        'music' => '',
                        'file' => '',
                        'file_name' => '',
                        'file_size' => '',
                        'img_type' => '',
                        'token' => $token,
                        'status' => 'active',
                        'created_at' => now()
                    ]);
                }
            }
        } // End Insert Video Embed Youtube or Vimeo


        $lessonIndex = 0;
        $lessonIds = [];
        foreach ($content as $module) {

            $moduleTitle = trim($module["title"]);
            $moduleDuration = intval($module["duration"]);
            $moduleId = trim($module["content_id"]);

            foreach ($module["lessons"] as $lesson) {
                $lessonIndex += 1;
                $lessonTitle = trim($lesson["title"]);
                $lessonFile = trim(array_key_exists("lesson_file", $lesson) ? trim($lesson["lesson_file"]) : "");
                $lessonDescription = trim(array_key_exists("description", $lesson) ? trim($lesson["description"]) : "");
                $file = trim($lesson["video"]);
                $lessonId = trim($lesson["content_id"]);
                $lessonNo = explode("-", $lessonId)[1];
                $token = Str::random(150) . uniqid() . now()->timestamp;
                $media = Media::whereUpdatesId($post->id)->where(function ($query) use ($file) {
                    $query->whereImage($file)->orWhere('video', $file)->orWhere('video_embed', $file)->orWhere('file', $file);
                }) ->get()->first();

                $existingLesson = CourseLesson::where("updates_id", $post->id)
                    ->where("user_id", auth()->id())
                    ->where("module_id", $moduleId)
                    ->where("lesson_id", $lessonNo)
                    ->where("updates_id", $post->id)->first();

                $lessonIds[] = $moduleId . "-" . $lessonNo;

                $lessonParams = [
                    'updates_id' => $post->id,
                    'media_id' => empty($media) ? 0 : $media->id,
                    'module_title' => $moduleTitle,
                    'duration' => $moduleDuration,
                    'lesson_title' => $lessonTitle,
                    'lesson_index' => $lessonIndex,
                    'lesson_description' => $lessonDescription,
                    'status' => 'active',
                    'lesson_file' => $lessonFile,
                ];


                if($existingLesson === null) {
                    $lessonParams["module_id"] = $moduleId;
                    $lessonParams["lesson_id"] = $lessonNo;
                    $lessonParams["token_id"] = $token;
                    $lessonParams["user_id"] = auth()->id();
                    CourseLesson::create($lessonParams);
                }
                else $existingLesson->update($lessonParams);

            }
        }

        $existingLessonsInDb = $this->getCourseLessons($courseId);
        if(!empty($existingLessonsInDb)) {
            foreach ($existingLessonsInDb as $existingLesson) {
                if(!$this->courseLessonExists($existingLesson->module_id . "-" . $existingLesson->lesson_id, $lessonIds)) {
                    $mediaId = $existingLesson->media_id;
                    if(CourseLesson::where("media_id", $mediaId)->count() === 1) Media::where("id", $mediaId)->where("updates_id", $courseId)->delete();
                    CourseLesson::whereId($existingLesson->id)->delete();
                }
            }
        }


        // Get all videos of the post
        $videos = Media::whereUpdatesId($post->id)->where('video', '<>', '')->get();

        if ($videos->count() && $this->settings->video_encoding == 'on') {

            try {
                foreach ($videos as $video) {
                    $this->dispatch(new EncodeVideo($video));
                }

                // Change status Pending to Encode
                Updates::whereId($post->id)->update([
                    'status' => 'encode'
                ]);

                return response()->json([
                    'success' => true,
                    'pending' => true,
                    'encode' => true
                ]);

            } catch (\Exception $e) {
                \Log::info($e->getMessage());
            }

        }// End Videos->count

        if ($this->settings->auto_approve_post == 'off') {
            return response()->json([
                'success' => true,
                'pending' => true
            ]);
        }

        // Event to listen
        event(new NewPostEvent($post));

        // Send Notification Mention
        Helper::sendNotificationMention($post->description, $post->id);

        return response()->json([
            'success' => true,
            'pending' => false,
            'total' => auth()->user()->updates()->count(),
        ]);

    }//<---- End Method



    private function getCourseLessons($courseId, $currentIdsOnly = false) {
        $lessons = CourseLesson::where("updates_id", $courseId)->get();
        if(!$currentIdsOnly) return $lessons;
        return $lessons->map(function ($lesson) { return $lesson->module_id . "-" . $lesson->lesson_id; });
    }

    private function courseLessonExists($lessonId, $lessonIds = null, $courseId = 0) {
        if($lessonIds === null) $lessonIds = $this->getCourseLessons($courseId, true);
        return in_array($lessonId, $lessonIds);
    }


    public function ajaxUpdates()
    {
        $id = $this->request->input('id');
        $skip = $this->request->input('skip');
        $total = $this->request->input('total');
        $media = $this->request->input('media');
        $mediaArray = ['photos', 'videos', 'audio', 'files'];

        $user = User::findOrFail($id);

        if (isset($media) && !in_array($media, $mediaArray)) {
            abort(500);
        }

        $page = $this->request->input('page');

        if (isset($media)) {
            $query = $user->media();
        } else {
            $query = $user->updates()->whereFixedPost('0');
        }

        //=== Photos
        $query->when($this->request->input('media') == 'photos', function ($q) {
            $q->where('media.image', '<>', '');
        });

        //=== Videos
        $query->when($this->request->input('media') == 'videos', function ($q) use ($user) {
            $q->where('media.video', '<>', '')->orWhere('media.video_embed', '<>', '')->where('media.user_id', $user->id);
        });

        //=== Audio
        $query->when($this->request->input('media') == 'audio', function ($q) {
            $q->where('media.music', '<>', '');
        });

        //=== Files
        $query->when($this->request->input('media') == 'files', function ($q) {
            $q->where('media.file', '<>', '');
        });

        // Sort by older
        $query->when(request('sort') == 'oldest', function ($q) {
            $q->orderBy('updates.id', 'asc');
        });

        // Sort by unlockable
        $query->when(request('sort') == 'unlockable', function ($q) {
            $q->where('updates.price', '<>', 0.00);
        });

        // Sort by free
        $query->when(request('sort') == 'free', function ($q) {
            $q->where('updates.locked', 'no');
        });

        $data = $query->orderBy('updates.id', 'desc')
            ->groupBy('updates.id')
            ->skip($skip)
            ->take($this->settings->number_posts_show)
            ->get();

        $counterPosts = ($total - $this->settings->number_posts_show - $skip);

        return view('includes.updates',
            ['updates' => $data,
                'ajaxRequest' => true,
                'counterPosts' => $counterPosts,
                'total' => $total
            ])->render();

    }//<--- End Method

    public function edit($id)
    {
        $data = auth()->user()->updates()->findOrFail($id);

        return view('users.edit-update')->withData($data);
    }

    public function postEdit()
    {

        $id = $this->request->input('id');
        $sql = Updates::whereId($id)->whereUserId(auth()->id())->firstOrFail();
        $videoUrl = '';

        // Currency Position
        if ($this->settings->currency_position == 'right') {
            $currencyPosition = 2;
        } else {
            $currencyPosition = null;
        }

        $messages = [
            'description.required' => trans('general.please_write_something'),
            '_description.required_if' => trans('general.please_write_something_2'),
            'description.min' => trans('validation.update_min_length'),
            'description.max' => trans('validation.update_max_length'),
            'price.min' => trans('general.amount_minimum' . $currencyPosition, ['symbol' => $this->settings->currency_symbol, 'code' => $this->settings->currency_code]),
            'price.max' => trans('general.amount_maximum' . $currencyPosition, ['symbol' => $this->settings->currency_symbol, 'code' => $this->settings->currency_code]),
            'price.required_if' => trans('validation.required'),
        ];

        $input = $this->request->all();
        $mediaFiles = $sql->media()->where('video_embed', '=', '')->count();

        $getAllMedia = $sql->media()
            ->where('image', '=', '')
            ->orWhere('video', '=', '')
            ->whereUpdatesId($id)
            ->orWhere('music', '=', '')
            ->whereUpdatesId($id)
            ->orWhere('file', '=', '')
            ->whereUpdatesId($id)
            ->first();

        $urlVideo = Helper::getFirstUrl($input['description']);
        $videoUrl = Helper::videoUrl($urlVideo) ? true : false;

        if ($mediaFiles == 0 && isset($getAllMedia) && $getAllMedia->video_embed != '' && !$videoUrl
            || $mediaFiles == 0 && !$getAllMedia && $videoUrl) {
            $input['_description'] = $videoUrl ? str_replace($urlVideo, '', $input['description']) : $input['description'];
            $input['_isVideoEmbed'] = $videoUrl ? 'yes' : 'no';
        }

        $input['is_ppv'] = $sql->price == 0.00 ? 'no' : 'yes';

        $validator = Validator::make($input, [
            'description' => 'required|min:1|max:' . $this->settings->update_length . '',
            '_description' => 'required_if:_isVideoEmbed,==,yes|min:1|max:' . $this->settings->update_length . '',
            'price' => 'required_if:is_ppv,==,yes|numeric|min:' . $this->settings->min_ppv_amount . '|max:' . $this->settings->max_ppv_amount,

        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ]);
        } //<-- Validator

        //<===== Locked Content
        if ($this->request->locked) {
            $this->request->locked = 'yes';
        } elseif ($this->request->price) {
            $this->request->locked = 'yes';
        } else {
            $this->request->locked = 'no';
        }

        $sql->description = trim(Helper::checkTextDb($this->request->description));
        $sql->user_id = auth()->id();
        $sql->token_id = Str::random(150);
        $sql->locked = $this->request->locked;
        $sql->price = $this->request->price;

        $sql->update_category_id = $this->request->update_category;

        $sql->save();

        $videoEmbed = $sql->media()->where('video_embed', '<>', '')->first();
        $isVideoEmbed = false;

        // Insert Video Embed Youtube or Vimeo
        if ($videoUrl && !$getAllMedia && !$videoEmbed) {

            $token = Str::random(150) . uniqid() . now()->timestamp;

            Media::create([
                'updates_id' => $sql->id,
                'user_id' => auth()->id(),
                'type' => 'video',
                'image' => '',
                'video' => '',
                'video_embed' => $urlVideo,
                'music' => '',
                'file' => '',
                'file_name' => '',
                'file_size' => '',
                'img_type' => '',
                'token' => $token,
                'status' => 'active',
                'created_at' => now()
            ]);

            $isVideoEmbed = $urlVideo;
        }

        if ($videoEmbed) {
            // Update URL the Video
            if ($videoEmbed->video_embed != $urlVideo) {
                $videoEmbed->video_embed = $urlVideo;
                $videoEmbed->save();
            }

            $isVideoEmbed = $videoEmbed->video_embed;
        }

        if ($videoEmbed && !$videoUrl) {
            $videoEmbed->delete();
            $isVideoEmbed = null;
        }

        return response()->json([
            'success' => true,
            'description' => Helper::linkText(Helper::checkText($sql->description, $isVideoEmbed)),
            'price' => $this->request->price ? Helper::amountFormatDecimal($this->request->price) : '',
            'locked' => $this->request->locked

        ]);

    }//<---- End Method

    public function delete($id)
    {
        if (!$this->request->expectsJson()) {
            abort(404);
        }

        if (auth()->user()->subscriptionsActive() && $this->settings->users_can_edit_post == 'off') {
            return response()->json([
                'success' => false,
                'message' => __('general.error_delete_post')
            ]);
        }

        $update = Updates::whereId($id)->whereUserId(auth()->id())->firstOrFail();
        $path = config('path.images');
        $pathVideo = config('path.videos');
        $pathMusic = config('path.music');
        $pathFile = config('path.files');

        $files = Media::whereUpdatesId($update->id)->get();

        foreach ($files as $media) {

            if ($media->image) {
                Storage::delete($path . $media->image);
                $media->delete();
            }

            if ($media->video) {
                Storage::delete($pathVideo . $media->video);
                Storage::delete($pathVideo . $media->video_poster);
                $media->delete();
            }

            if ($media->music) {
                Storage::delete($pathMusic . $media->music);
                $media->delete();
            }

            if ($media->file) {
                Storage::delete($pathFile . $media->file);
                $media->delete();
            }

            if ($media->video_embed) {
                $media->delete();
            }
        }

        // Delete Reports
        $reports = Reports::where('report_id', $id)->where('type', 'update')->get();

        if (isset($reports)) {
            foreach ($reports as $report) {
                $report->delete();
            }
        }

        // Delete Notifications
        Notifications::where('target', $id)
            ->where('type', '2')
            ->orWhere('target', $id)
            ->where('type', '3')
            ->orWhere('target', $id)
            ->where('type', '6')
            ->orWhere('target', $id)
            ->where('type', '7')
            ->orWhere('target', $id)
            ->where('type', '8')
            ->orWhere('target', $id)
            ->where('type', '9')
            ->delete();

        // Delete Likes Comments
        foreach ($update->comments()->get() as $key) {
            $key->likes()->delete();
        }

        // Delete Comments
        $update->comments()->delete();

        // Delete likes
        Like::where('updates_id', $id)->delete();

        // Delete Update
        $update->delete();

        if ($this->request->inPostDetail && $this->request->inPostDetail == 'true') {
            return response()->json([
                'success' => true,
                'inPostDetail' => true,
                'url_return' => url(auth()->user()->username)
            ]);
        } else {
            return response()->json([
                'success' => true
            ]);
        }

    }//<--- End Method

    public function report(Request $request)
    {

        $data = Reports::firstOrNew(['user_id' => auth()->id(), 'report_id' => $request->id]);

        $validator = Validator::make($this->request->all(), [
            'reason' => 'required|in:copyright,privacy_issue,violent_sexual',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'text' => __('general.error'),
            ]);
        }

        if ($data->exists) {
            return response()->json([
                'success' => false,
                'text' => __('general.already_sent_report'),
            ]);
        } else {

            $data->type = 'update';
            $data->reason = $request->reason;
            $data->save();

            return response()->json([
                'success' => true,
                'text' => __('general.reported_success'),
            ]);
        }
    }//<--- End Method

    public function image($id, $path)
    {
        try {

            $server = ServerFactory::create([
                'response' => new LaravelResponseFactory(app('request')),
                'source' => Storage::disk()->getDriver(),
                'cache' => Storage::disk()->getDriver(),
                'source_path_prefix' => '/uploads/updates/images/',
                'cache_path_prefix' => '.cache',
                'base_url' => '/uploads/updates/images/',
                'group_cache_in_folders' => false
            ]);

            $server->outputImage($path, $this->request->all());

        } catch (\Exception $e) {

            \Log::debug($e->getMessage());

            abort(404);
            $server->deleteCache($path);
        }
    }//<--- End Method

    public function messagesImage($id, $path)
    {
        try {

            $server = ServerFactory::create([
                'response' => new LaravelResponseFactory(app('request')),
                'source' => Storage::disk()->getDriver(),
                'cache' => Storage::disk()->getDriver(),
                'source_path_prefix' => '/uploads/messages/',
                'cache_path_prefix' => '.cache',
                'base_url' => '/uploads/messages/',
                'group_cache_in_folders' => false
            ]);

            $response = Messages::whereId($id)
                ->whereFromUserId(auth()->id())
                ->orWhere('id', '=', $id)->where('to_user_id', '=', auth()->id())
                ->firstOrFail();

            $server->outputImage($path, $this->request->all());

        } catch (\Exception $e) {

            abort(404);
            $server->deleteCache($path);
        }
    }//<--- End Method

    public function pinPost(Request $request)
    {
        $findPost = Updates::whereId($request->id)->whereUserId(auth()->id())->firstOrFail();
        $findCurrentPostPinned = Updates::whereUserId(auth()->id())->whereFixedPost('1')->first();

        if ($findPost->fixed_post == '0') {
            $status = 'pin';
            $findPost->fixed_post = '1';
            $findPost->update();

            // Unpin old post
            if ($findCurrentPostPinned) {
                $findCurrentPostPinned->fixed_post = '0';
                $findCurrentPostPinned->update();
            }

        } else {
            $status = 'unpin';
            $findPost->fixed_post = '0';
            $findPost->update();
        }

        return response()->json([
            'success' => true,
            'status' => $status,
        ]);
    }

    // Bookmarks Ajax Pagination
    public function ajaxBookmarksUpdates()
    {
        $skip = $this->request->input('skip');
        $total = $this->request->input('total');

        $data = auth()->user()->bookmarks()->orderBy('bookmarks.id', 'desc')->skip($skip)->take($this->settings->number_posts_show)->get();
        $counterPosts = ($total - $this->settings->number_posts_show - $skip);

        return view('includes.updates',
            ['updates' => $data,
                'ajaxRequest' => true,
                'counterPosts' => $counterPosts,
                'total' => $total
            ])->render();

    }//<--- End Method

    public function getFileMedia($typeMedia, $fileId)
    {
        $response = Media::findOrFail($fileId);
        $checkUserSubscription = auth()->check() ? auth()->user()->checkSubscription($response->updates->user()) : null;

        switch ($typeMedia) {
            case 'video':
                $pathFile = config('path.videos') . $response->video;
                $type = 'video/mp4';
                break;

            case 'audio':
                $pathFile = config('path.music') . $response->music;
                $type = 'audio/mpeg';
                break;
        }

        if (auth()->check()
            && auth()->id() == $response->updates->user_id

            || auth()->check()
            && $response->updates->locked == 'yes'
            && $checkUserSubscription
            && $response->updates->price != 0.00
            && $checkUserSubscription->free == 'no'

            || auth()->check()
            && $response->updates->locked == 'yes'
            && $checkUserSubscription
            && $response->updates->price == 0.00

            || auth()->check()
            && auth()->user()->payPerView()->where('updates_id', $response->updates->id)->first()

            || auth()->check()
            && auth()->user()->role == 'admin' && auth()->user()->permission == 'all'
            || $response->updates->locked == 'no'
        ) {
            $path = Helper::getFile($pathFile);

            try {
                header("Content-Disposition: inline; filename=\"$path\"");
                header("Content-type: $type");
                print file_get_contents($path);

            } catch (\FileNotFoundException $exception) {
                abort(404);
            }

        } else {
            abort(404);
        }
    }//<--- End Method

    public function explore()
    {
        $updates = Updates::leftjoin('users', 'updates.user_id', '=', 'users.id')
            ->where('updates.status', 'active')
            ->where('users.blocked_countries', 'NOT LIKE', '%' . Helper::userCountry() . '%');

        // Filter by hashtag
        $updates->when(strlen(request('q')) > 2, function ($q) {
            $q->where('description', 'LIKE', '%' . request('q') . '%');
        });

        // Sort by older
        $updates->when(request('sort') == 'oldest', function ($q) {
            $q->orderBy('updates.id', 'asc');
        });

        // Sort by unlockable
        $updates->when(request('sort') == 'unlockable', function ($q) {
            $q->where('updates.price', '<>', 0.00);
        });

        // Sort by free
        $updates->when(request('sort') == 'free', function ($q) {
            $q->where('updates.locked', 'no');
        });

        $updates = $updates->orderBy('updates.id', 'desc')
            ->select('updates.*')
            ->paginate($this->settings->number_posts_show);

        $users = $this->userExplore();

        return view('index.explore', ['updates' => $updates, 'users' => $users]);
    }//<--- End Method

    // Explore Ajax Pagination
    public function ajaxExplore()
    {
        $skip = $this->request->input('skip');
        $total = $this->request->input('total');

        $updates = Updates::leftjoin('users', 'updates.user_id', '=', 'users.id')
            ->where('updates.status', 'active')
            ->where('users.blocked_countries', 'NOT LIKE', '%' . Helper::userCountry() . '%');

        // Filter by hashtag
        $updates->when(strlen(request('q')) > 2, function ($q) {
            $q->where('description', 'LIKE', '%' . request('q') . '%');
        });

        // Sort by older
        $updates->when(request('sort') == 'oldest', function ($q) {
            $q->orderBy('updates.id', 'asc');
        });

        // Sort by unlockable
        $updates->when(request('sort') == 'unlockable', function ($q) {
            $q->where('updates.price', '<>', 0.00);
        });

        // Sort by free
        $updates->when(request('sort') == 'free', function ($q) {
            $q->where('updates.locked', 'no');
        });

        $updates = $updates->orderBy('updates.id', 'desc')
            ->skip($skip)
            ->take($this->settings->number_posts_show)
            ->select('updates.*')
            ->get();

        $counterPosts = ($total - $this->settings->number_posts_show - $skip);

        return view('includes.updates',
            ['updates' => $updates,
                'ajaxRequest' => true,
                'counterPosts' => $counterPosts,
                'total' => $total
            ])->render();

    }//<--- End Method

    public function imageFocus($type, $path)
    {
        try {

            switch ($type) {
                case 'photo':
                    $urlStorage = '/uploads/updates/images/';

                    $realPath = Media::findOrFail($path);
                    $path = $realPath->image;

                    break;

                case 'video':
                    $urlStorage = '/uploads/updates/videos/';
                    break;

                case 'message':
                    $urlStorage = '/uploads/messages/';

                    $realPath = MediaMessages::findOrFail($path);

                    if ($realPath->type == 'image') {
                        $path = $realPath->file;
                    } elseif ($realPath->type == 'video') {
                        $path = $realPath->video_poster;
                    }
                    break;
            }

            $server = ServerFactory::create([
                'response' => new LaravelResponseFactory(app('request')),
                'source' => Storage::disk()->getDriver(),
                'cache' => Storage::disk()->getDriver(),
                'source_path_prefix' => $urlStorage,
                'cache_path_prefix' => '.cache',
                'base_url' => $urlStorage,
                'group_cache_in_folders' => false
            ]);

            $server->outputImage($path, [
                    'w' => 250,
                    'h' => 150,
                    'blur' => 85
                ]
            );

        } catch (\Exception $e) {

            abort(404);
            $server->deleteCache($path);
        }
    }//<--- End Method

    public function createUpdateCategory()
    {

        $name = $this->request->name;
        $name = $this->request->name;
        $data = [];

        $categories_model = new UpdateCategories();

        $categories_model->name = $name;
        $categories_model->user_id = auth()->user()->id;
        $categories_model->save();

        $id = $categories_model->id;

        if (!empty($id)) {
            $data = [
                'id' => $id,
                'name' => $name,
            ];
        }

        echo json_encode($data);
    }









    public function getUpdateEmailFlows() {
        $flows = UpdateEmailFlows::where("updates_id", $this->request->id)->where("user_id", auth()->id())->get();
        if(!$flows->isEmpty()) foreach ($flows as $flow) $flow->html_content = file_get_contents($_SERVER["DOCUMENT_ROOT"] . $flow->template_node);

        return view("users.course-email-flow", [
            "flows" => $flows,
            "update" => Updates::where("id", $this->request->id)->where("user_id", auth()->id())->first()
        ]);
    }


    public function setUpdateEmailFlow(Request $request) {
        $content = $request->html_content;
        $title = $request->title;
        $flowId = $request->flow_id;
        $courseId = $request->course_id;
        $errorInfo = "";

        if(empty($content)) $errorInfo = "The content of the email cannot be empty";
        elseif(empty($title)) $errorInfo = "The title of the email cannot be empty";
        elseif(empty($courseId)) $errorInfo = "Failed to find the requested course's data";
        elseif(strlen($title) > 50) $errorInfo = "The length of the title of the template cannot be greater than 50 characters";

        $update = Updates::where("id", $courseId)->where("user_id", auth()->id())->first();
        if(is_null($update)) $errorInfo = "Failed to find the requested course";

        if(!empty($errorInfo)) return response()->json(["status" => "error", "message" => $errorInfo]);
        $existingFlow = (int)$flowId !== 0;

        if($existingFlow) {
            $flow = UpdateEmailFlows::where("id", $flowId)->where("updates_id", $courseId)->where("user_id", auth()->id());
            if($flow->count() < 1) return response()->json(["status" => "error", "message" => "Failed to find the requested flow"]);

            $filePath = $flow->first()->template_node;
            if(file_put_contents($_SERVER["DOCUMENT_ROOT"] . $filePath, $content) === false)
                return response()->json(["status" => "error", "message" => "Failed to store the contents of the template. Please contact support"]);
            $flow->update(["title" => $title]);
        }
        else {
            $path = "/storage/email-flow/";
            while(true) {
                $filename = md5(rand(100, 100000000) . uniqid());
                $filePath = $path . $filename . ".html";
                if(!file_exists($_SERVER["DOCUMENT_ROOT"] . $filePath)) break;
            }

            if(file_put_contents($_SERVER["DOCUMENT_ROOT"] . $filePath, $content) === false)
                return response()->json(["status" => "error", "message" => "Failed to store the contents of the template. Please contact support"]);


            UpdateEmailFlows::create([
                "title" => $title,
                "user_id" => auth()->id(),
                "updates_id" => $courseId,
                "template_node" => $filePath,
            ]);
        }



        return response()->json(["status" => "success", "message" => "Template created"]);
    }












    public function updateFlowDays(Request $request) {
        $value = (int)$request->new_value;
        $flowId = $request->flow_id;
        $courseId = $request->course_id;

        if($value < -1 || $value > 50) return;

        $flow = UpdateEmailFlows::where("id", $flowId)->where("updates_id", $courseId)->where("user_id", auth()->id());
        if($flow->count() < 1) return;
        $flow->update(["send_days_after_unlock" => $value]);
    }




    public function changeLessonsOrderNumbers(Request $request){
        
        $ids = $request->ids;
        
        foreach($ids as $k=>$id){
            
            $data = [
                'order_number' => $k+1
            ];
            
            CourseLesson::where(['id'=>$id])->update($data);
            
        }
        
    }
    
    
    
    public function showCourseInvitationRegistrationForm(Request $request){
        
        if (Auth::check()){
            return redirect('/');
        }
        
        $course_id = $request->id;
        
        $course = $this->courseItemsByUpdatesId($course_id);
        
        // print_r($course["update"]);
        
        return view('auth.course-invitation-signup', ["course" => $course]);
        
    }
    
    
    public function CourseInvitationRegisterStep2(Request $request){
        
        $countries_id = $request->countries_id;
        $address = $request->address;
        $city = $request->city;
        $zip = $request->zip;
        
        $user = User::find(auth()->user()->id);
        
        $user->update([
            'countries_id' => $countries_id,
            'address' => $address,
            'city' => $city,
            'zip' => $zip,
        ]);
        
        $taxes = auth()->user()->newIsTaxable();
        
        return response()->json([
            'success' => true,
            'name' => $user['name'],
            'email' => $user['email'],
        ]);
        
    }
    
    public function CourseInvitationGetTaxes(Request $request){
        
        $taxes = auth()->user()->newIsTaxable();
        
        return response()->json([
            'taxes' => json_decode(json_encode($taxes), true)
        ]);
        
    }
























}
