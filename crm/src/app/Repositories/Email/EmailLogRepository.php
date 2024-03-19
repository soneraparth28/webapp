<?php


namespace App\Repositories\Email;

use App\Models\Email\EmailLog;
use App\Repositories\App\AppRepository;
use App\Repositories\App\StatusRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QBuilder;
use Illuminate\Support\Facades\DB;

class EmailLogRepository extends AppRepository
{
    public function __construct(EmailLog $emailLog)
    {
        $this->model = $emailLog;
        $this->setBuilder($emailLog);
    }

    public function filterByBrand($brand_id = null)
    {
        $brand_id = $brand_id ?: request('brand_id');

        $this->builder->when($brand_id, function (Builder $builder) use ($brand_id) {
            $builder->whereHas('campaign', function (Builder $builder) use ($brand_id) {
                $builder->where('brand_id', $brand_id);
            });
        });

        return $this;
    }

    public function whereStatus($names)
    {
        $names = is_array($names) ? $names : func_get_args();

        $this->builder->whereHas('status', function (Builder $builder) use ($names) {
            $builder->where('type', 'email')
                ->whereIn('name', $names);
        });

        return $this;
    }

    public function fillableStatuses($isIds = false)
    {
        $names = ['status_sent', 'status_open', 'status_delivered', 'status_bounced', 'status_clicked'];
        if ($isIds) {
            return array_keys(resolve(StatusRepository::class)->email(...$names));
        }
        return collect($names);
    }

    public function filteredEmailLogs($campaign_id = null, $range = null)
    {
        $statuses = $this->fillableStatuses(true);

        return DB::table('email_logs')
            ->selectRaw('email_logs.updated_at, statuses.name as status, open_count, click_count')
            ->when(request()->brand_id, function (QBuilder $builder) {
                $builder->join('campaigns', 'email_logs.campaign_id', '=', 'campaigns.id')
                    ->where('brand_id', request()->brand_id);
            })->whereIn('email_logs.status_id', $statuses)
            ->when($campaign_id, function (QBuilder $builder) use ($campaign_id){
                $builder->where('campaign_id', $campaign_id);
            })
            ->join('statuses', 'email_logs.status_id', '=', 'statuses.id')
            ->when($range, function (QBuilder $query) use ($range) {
                $query->whereBetween('email_logs.updated_at', $range );
            })
            ->get();
    }
}
