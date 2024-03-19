<?php


namespace App\Models\Subscriber\Traits;


trait SubscriberAttribute
{
    public function getFullNameAttribute()
    {
        return $this->last_name
            ? $this->first_name.' '.$this->last_name
            : $this->first_name;
    }


    public function getListNamesAttribute()
    {
        return implode(', ', $this->lists->pluck('name')->toArray());
    }

    public function getLastActivityAttribute()
    {
        $latest_email = $this->emailLogs()->latest('updated_at')->first();

        return $latest_email
            ? $latest_email->updated_at
            : $this->updated_at;
    }


}
