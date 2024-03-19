<?php

namespace App\Http\Controllers\Email;

use App\Filters\EmailLog\EmailLogFilter;
use App\Helpers\Traits\InteractsWithTemplate;
use App\Http\Controllers\Controller;
use App\Models\Email\EmailLog;
use App\Services\Email\EmailLogService;
use Illuminate\Database\Eloquent\Builder;

class EmailLogController extends Controller
{
    use InteractsWithTemplate;

    public function __construct(EmailLogService $service, EmailLogFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
        return $this->service
            ->select('id', 'email_date', 'subscriber_id', 'campaign_id', 'status_id', 'is_marked_as_spam')
            ->with('subscriber:id,email', 'campaign:id,name', 'status:id,name,class')
            ->whereHas('campaign', function (Builder $builder) {
                $builder->where('brand_id', request()->get('brand_id'));
            })
            ->filters($this->filter)
            ->latest('id')
            ->paginate(
                \request('per_page', 10)
            );
    }

    public function show(EmailLog $emailLog)
    {
        $template = $this->template($emailLog->email_content)
            ->removeTrackerElement()
            ->restoreTrackerAnchors()
            ->get();

        return [
            'body' => $template,
            'attachments' => $emailLog->campaign->attachments->pluck('path')
        ];
    }

    public function destroy(EmailLog $emailLog)
    {
        $emailLog->delete();

        return  deleted_responses('email_log');
    }
}
