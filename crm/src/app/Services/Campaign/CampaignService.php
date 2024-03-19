<?php


namespace App\Services\Campaign;


use App\Helpers\Traits\FileHandler;
use App\Models\Campaign\Campaign;
use App\Notifications\CampaignNotification;
use App\Repositories\Campaign\CampaignRepository;
use App\Services\AppService;
use Illuminate\Database\Query\Builder as QBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CampaignService extends AppService
{
    use FileHandler;

    protected CampaignRepository $repository;

    protected $process_attachment = false;

    public function __construct(Campaign $campaign, CampaignRepository $repository)
    {
        $this->model = $campaign;
        $this->repository = $repository;
    }

    public function save($attributes = [])
    {
        $campaign = parent::save($attributes);

        if (request()->hasFile('attachments')) {
            $this->uploadFiles($campaign);
        }
        return $campaign;
    }

    private function uploadFiles($campaign): void
    {
        $attachments = request()->file('attachments');
        if (is_array($attachments)) {
            $folder = 'files/' . date('Y/m/');
            foreach ($attachments as $attachment) {
                /** @var Campaign $campaign */
                $campaign->attachments()->create([
                    'type' => 'attachment',
                    'path' => $this->file($attachment)
                        ->setDirectory("{$folder}campaigns")
                        ->withOriginalName()
                        ->save()
                ]);
            }
        }
    }

    public function update($attributes, $id, $confirm = false)
    {
        $this->find($id);

        $this->model->fill($attributes);

        if ($this->model->isDirty()) {
            notify()
                ->on($confirm ? 'campaign_confirmed' : 'campaign_updated')
                ->with($this->model)
                ->send(CampaignNotification::class);
        }

        $this->model->save();

        if ($this->process_attachment) {

            $this->deleteAttachments(
                $this->model,
                $this->model
                    ->attachments
                    ->whereNotIn('id', request()->get('dont_delete', []))
                    ->pluck('path')
                    ->toArray()
            );

            if (request()->has('attachments') && isset(request()->allFiles()['attachments'])) {
                $this->uploadFiles($this->model);
            }
        }

        if (request()->has('audiences'))
            $this->audiences($id);

        return $this->model;
    }

    public function deleteAttachments($campaign, $paths = [], $field = 'path')
    {
        $this->file()->delete($paths);

        return $campaign->attachments()
            ->whereIn($field, $paths)
            ->delete();
    }

    public function audiences($id)
    {
        $this->find($id);
        $attributes = [];

        if (array_key_exists('subscriber', request('audiences'))) {
            $this->model->audiences()->where('audience_type', 'subscriber')->delete();
            $attributes['subscriber'] = request('audiences')['subscriber'];
        }

        if (array_key_exists('list', request('audiences'))) {
            $this->model->audiences()->where('audience_type', 'list')->delete();
            $attributes['list'] = request('audiences')['list'];
        }

        foreach ($attributes as $audience_type => $audiences)
            $this->model->audiences()->create(
                compact('audience_type', 'audiences')
            );

        return $this->model;
    }

    public function delete(Campaign $campaign): ?bool
    {
        if ((bool)$campaign->thumbnail) {
            $this->file()->delete($campaign->thumbnail->path);
            $campaign->thumbnail()->delete();
        }
        $this->deleteAttachments($campaign, $campaign->attachments->pluck('path')->toArray());

        return $campaign->delete();
    }

    public function subscribers(Campaign $campaign)
    {
        $this->repository->setModel($campaign);
        return $this->repository->audiences();
    }


    public function campaignsCount($last24Hours): Collection
    {
        $emailLogStatuses = DB::table('email_logs')
            ->selectRaw('email_logs.status_id, statuses.name as status, open_count, click_count')
            ->when(request('brand_id'), function (QBuilder $builder) {
                $builder->join('campaigns', 'email_logs.campaign_id', '=', 'campaigns.id')
                    ->where('brand_id', request()->brand_id);
            })->when($last24Hours, function (QBuilder $builder) {
                $builder->where('email_logs.updated_at', '>=', now()->subDay()->toDateTimeString());
            })->join('statuses', 'email_logs.status_id', '=', 'statuses.id')
            ->get();

        $total_open_count = $emailLogStatuses->where('open_count', '!=', 0)->count();
        $total_clicked_count = $emailLogStatuses->where('click_count', '!=', 0)->count();
        $total_sent_count = $emailLogStatuses->where('status', '<>', 'status_bounced')->count();

        $countByStatus = $emailLogStatuses
            ->countBy(function ($value) {
                return $value->status;
            })
            ->merge([
                'status_open' => $total_open_count,
                'status_clicked' => $total_clicked_count
            ]);
        $countByStatus['status_sent'] = $total_sent_count;

        $keys = $this->repository->getEmailStatuses()
            ->keys()
            ->concat(['status_open', 'status_clicked']);

        return $keys->map(function ($name) use ($countByStatus) {
            return [
                'name' => $name,
                'count' => $countByStatus->keys()->contains($name) ? $countByStatus[$name] : 0
            ];
        })
            ->pluck('count', 'name');
    }

    public function processAttachment($process = true): CampaignService
    {
        $this->process_attachment = $process;

        return $this;
    }

}