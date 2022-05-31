<?php

namespace App\Service\RateConvert;

use App\DTO\ConvertDTO;
use App\Service\RateImport\RateImportService;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;

class RateConvertService
{
    private FilesystemAdapter $cache;

    public function __construct()
    {
        $this->cache = new FilesystemAdapter('', 0, '/app/cache');
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function rateCount(string $from, string $to, float $amount): ?ConvertDTO
    {
        if($from === $to) {
            return new ConvertDTO($this->chainBuilder($from, $from), $amount);
        }
        /** @var CacheItem $cacheItem */
        $cacheItem = $this->cache->getItem(RateImportService::CACHE_KEY);
        $rates = $cacheItem->get();

        return $this->recursiveNDepth($rates, $from, $to, $amount);
    }

    private function recursiveNDepth($rates, $from, $to, $amount, $chain = '', $depthLimit = 3): ?ConvertDTO
    {
        --$depthLimit;

        if ($depthLimit < 0) {
            return null;
        }

        $chain = $this->chainBuilder($from, $chain);

        $seekSlice = $rates[$from];
        if (array_key_exists($to, $seekSlice)) {
            $chain = $this->chainBuilder($to, $chain);
            $amount *= $seekSlice[$to];

            return new ConvertDTO($chain, $amount);
        }

        //In cases $depthLimit > 2 more long transaction chain may be founded
        //And we have too much useless multiplications here. Very naive realisation, better re-write on cycles
        foreach ($seekSlice as $interCurrent => $interRate) {
            $maybeHere = $this->recursiveNDepth($rates, $interCurrent, $to, $amount * $interRate, $chain, $depthLimit);
            if (!is_null($maybeHere)) {
                return $maybeHere;
            }
        }

        return null;
    }

    private function chainBuilder( string $currency, string $chain=""): string {
        if (empty($chain)){
            return $currency;
        }

        return sprintf("%s->%s", $chain, $currency);
    }
}
