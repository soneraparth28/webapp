<?php

namespace App\Filters\EmailLog;


use App\Filters\FilterBuilder;
use App\Filters\Traits\StatusFilter;
use App\Repositories\App\StatusRepository;
use Illuminate\Database\Eloquent\Builder;

class EmailLogFilter extends FilterBuilder
{
    public function search($term = null)
    {
        $this->whereHasLike([
            'subscriber' => 'email',
            'campaign' => 'name'
        ], $term);
    }

    public function campaign($term = null)
    {
        $this->whereClause('campaign_id', $term);
    }

    public function date($date = null)
    {
        $date = json_decode(htmlspecialchars_decode($date), true);

        $this->builder->when($date, function (Builder $builder) use ($date) {
            $builder->whereBetween(\DB::raw('DATE(email_date)'), array_values($date));
        });
    }

    public function status($ids = null)
    {
        if ($ids) {
            $statuses = explode(',', $ids);
            $emails = resolve(StatusRepository::class)
                ->email()
                ->pluck('id', 'name');

            if (in_array($emails['status_sent'], $statuses)) {
                if (!in_array($emails['status_delivered'], $statuses))
                    $statuses[] = $emails['status_delivered'];
            }
            $this->whereInClause('status_id', $statuses );
        }
    }
}
