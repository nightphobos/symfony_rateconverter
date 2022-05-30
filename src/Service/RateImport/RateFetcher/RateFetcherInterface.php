<?php

namespace App\Service\RateImport\RateFetcher;

use App\DTO\RateDTO;

interface RateFetcherInterface
{
    /**
     * @return RateDTO[]
     */
    public function getData(): array;

}