<?php


namespace App\Managers\Quota;


interface QuotaContract
{
    public function whenExists(int $count) : object;

    public function whenNotExists(array $counts) : object;
}