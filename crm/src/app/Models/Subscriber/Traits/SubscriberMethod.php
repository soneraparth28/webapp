<?php


namespace App\Models\Subscriber\Traits;


use Illuminate\Database\Eloquent\Builder;

trait SubscriberMethod
{
    public function deliveredMailCount()
    {
        return $this->emailLogs()->whereHas('status', function (Builder $builder) {
            $builder->whereIn('name', [
                'status_delivered', 'status_open', 'status_clicked', 'status_new'
            ]);
        })->count();
    }

    public function sentMailCount()
    {
        return $this->emailLogs()->whereHas('status', function (Builder $builder) {
            $builder->whereIn('name', [
                'status_sent', 'status_delivered', 'status_open', 'status_clicked', 'status_new'
            ]);
        })->count();
    }


}
