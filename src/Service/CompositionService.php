<?php

namespace App\Service;

use App\Entity\Composition;
use App\Entity\User;
use App\Repository\RatesRepository;
use Doctrine\ORM\EntityManagerInterface;

class CompositionService
{
    protected EntityManagerInterface $em;
    protected RatesRepository $rateRepository;

    public function __construct(EntityManagerInterface $em, RatesRepository $rateRepository)
    {
        $this->em = $em;
        $this->rateRepository = $rateRepository;
    }

    public function compositionCreate(Composition $composition, User $user) :void
    {
        $composition->setUser($user);
        $composition->setUpdatedAt(new \DateTime());
        $composition->setLastRateUpdate(new \DateTime());
        $composition->setAverageRate(0);

        $this->em->persist($composition);
        $this->em->flush();
    }

    public function compositionEdit(Composition $composition) :void
    {
        $composition->setUpdatedAt(new \DateTime());

        $this->em->persist($composition);
        $this->em->flush();
    }

    public function compositionAverageRate(Composition $composition) :float
    {
        if (date_diff(new \DateTime(), $composition->getLastRateUpdate(), )->i > 15) {
            $rates = $this->rateRepository->findByComposition($composition);
            $averageRate = 0;
            foreach ($rates as $rate) {
                $averageRate += $rate->getRate();
            }
            $countRates = count($rates);
            $averageRate /= $countRates;
            $composition->setAverageRate($averageRate);
            $composition->setLastRateUpdate(new \DateTime());
            $this->em->persist($composition);
            $this->em->flush();
            return $averageRate;
        }
        return $composition->getAverageRate();
    }
}
