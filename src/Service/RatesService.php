<?php

namespace App\Service;

use App\Entity\Composition;
use App\Repository\RatesRepository;

class RatesService
{
    protected RatesRepository $ratesRepository;

    public function __construct(RatesRepository $ratesRepository)
    {
        $this->ratesRepository = $ratesRepository;
    }

    public function getUserRateForComposition($user, Composition $composition) :int
    {
        if (is_null($user)) {
            return 0;
        }
        $rate = $this->ratesRepository->getUserRateForComposition($user, $composition);
        if (is_null($rate)) {
            $rate['rate'] = 0;
        }
        return $rate['rate'];
    }
}
