<?php

namespace App\Service;

use App\Entity\Composition;
use App\Entity\User;
use App\Repository\RatesRepository;

class RatesService
{
    protected RatesRepository $ratesRepository;

    public function __construct(RatesRepository $ratesRepository)
    {
        $this->ratesRepository = $ratesRepository;
    }

    public function getUserRateForComposition(User $user, Composition $composition) :int
    {
        $rate = $this->ratesRepository->getUserRateForComposition($user, $composition);
        if (is_null($rate)) {
            return 0;
        }
        return $rate['rate'];
    }
}
