<?php

namespace App\Service;

use App\Entity\Composition;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CompositionService
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function compositionAverageRate(array $rates) :int
    {
        $compositionAverageRate = 0;

        if ($rates) {
            foreach ($rates as $item) {
                $compositionAverageRate += $item['rate'];
            }
            $countRates = count($rates);
            $compositionAverageRate /= $countRates;
        }

        return $compositionAverageRate;
    }

    public function compositionCreate(Composition $composition, User $user) :void
    {
        $composition->setUser($user);
        $composition->setUpdatedAt(new \DateTime());

        $this->em->persist($composition);
        $this->em->flush();
    }

    public function compositionEdit(Composition $composition) :void
    {
        $composition->setUpdatedAt(new \DateTime());

        $this->em->persist($composition);
        $this->em->flush();
    }
}
