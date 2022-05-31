<?php

namespace App\Service\RateImport;

use App\DTO\RateDTO;
use App\Service\RateImport\RateFetcher\RateFetcherFactory;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class RateImportService
{
    public const CACHE_KEY = 'rates';
    private RateFetcherFactory $rateFetcherFactory;
    private FilesystemAdapter $cache;

    public function __construct(RateFetcherFactory $rateFetcherFactory)
    {
        $this->rateFetcherFactory = $rateFetcherFactory;
        $this->cache = new FilesystemAdapter('', 0, '/app/cache');
    }

    public function import(): int
    {
        $subresult = [];

        foreach ([RateFetcherFactory::FETCHER_EUROPA_EU, RateFetcherFactory::FETCHER_COINDESK_COM] as $fetcherClass) {
            $fetcher = $this->rateFetcherFactory->build($fetcherClass);
            $subresult[] = $fetcher->getData();
        }
        $all_rates = array_merge(...$subresult);

        //here we are building array containing all rates in format of ['from'=>[['to']=>rate,...],...]
        $rates = [];
        /** @var RateDTO $rate */
        foreach ($all_rates as $rate) {
            if (!array_key_exists($rate->from, $rates)) {
                $rates[$rate->from] = [];
            }
            $rates[$rate->from][$rate->to] = $rate->rate;
        }

        /* This is incorrect way of using cache, but it is acceptable because actually we are not using it as a cache
           Here we manually want to reload rates, so - we are cleaning cache and have no expiration */
        $cachedRates = $this->cache->getItem(self::CACHE_KEY);
        $cachedRates->set($rates);
        $this->cache->save($cachedRates);

        return count($rates);
    }
}
