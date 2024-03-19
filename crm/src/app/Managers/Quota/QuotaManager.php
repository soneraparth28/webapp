<?php


namespace App\Managers\Quota;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Helpers\Core\Traits\Memoization;
use Illuminate\Cache\RateLimiting\Limit;

class QuotaManager
{
    use Memoization, InstanceCreator;

    public array $quotas = [
        'hourly' => 0,
        'daily' => 0,
        'monthly' => 0,
    ];

    public array $builders = [
        'hourly' => Hourly::class,
        'daily' => Daily::class,
        'monthly' => Monthly::class,
    ];

    public function decideSuperiorLimiter() : Limit
    {
        $quotas = $this->get();

        if (!$quotas) {
            return Limit::none();
        }

        if ($quotas->hourly >= 1) {
            return Limit::perHour($quotas->hourly);
        }

        if ($quotas->daily >= 1) {
            return Limit::perDay($quotas->daily);
        }

        return Limit::perDay($quotas->monthly, now()->daysInMonth);
    }


    public function get()
    {
        if ($this->existsNothing()) {
            return $this->whenNothingIsExists();
        }

        if ($this->existsAll()) {
            return $this->whenAllExists();
        }

        if (['name' => $name, 'count' => $count] = $this->existsOnlyOne()) {

            return resolve($this->builders[$name])
                ->whenExists($count);
        }

        if (['name' => $name, 'counts' => $counts] = $this->existsExceptOne()) {
            return resolve($this->builders[$name])
                ->whenNotExists($counts);
        }

        return false;
    }

    private function whenAllExists()
    {
        ['monthly' => $monthly] = $this->quotas;

        $probableDaily = (int)($monthly / 30);

        $probableHourly =  (int)($probableDaily / 24);

        return (object) [
            'hourly' => $probableHourly,
            'daily' => (int) $probableDaily,
            'monthly' => $monthly,
        ];
    }

    public function setQuotas($quotas)
    {
        $this->quotas = $quotas;

        return $this;
    }

    public function filledQuotas()
    {
        return $this->memoize(
            'filtered',
            fn () => array_filter($this->quotas)
        );
    }

    public function whenNothingIsExists() : bool
    {
        return false;
    }

    public function existsAll() : bool
    {
        return count($this->filledQuotas()) === 3;
    }

    public function existsNothing() : bool
    {
        return count($this->filledQuotas()) === 0;
    }


    private function existsOnlyOne()
    {
        $filled = $this->filledQuotas();

        if (count($filled) == 1)  {
            $name = array_key_first($filled);
            return [
                'name' => $name,
                'count' => $filled[$name]
            ];
        }
        return false;
    }


    private function existsExceptOne()
    {
        $notFilled = array_filter($this->quotas, fn($e) => !$e);

        return count($notFilled) == 1 ? [
            'name' => array_key_first($notFilled),
            'counts' => $this->filledQuotas()
        ] : false;
    }

}
