<?php

namespace App\Service\RateImport\RateFetcher;

use App\DTO\RateDTO;
use SimpleXMLElement;

class EuropaEuRateFetcher extends RateFetcher
{
    private const URL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     *
     * @return RateDTO[]
     */
    public function getData(): array
    {
        $response = $this->client->request('GET', self::URL);

        //@TODO wrap in our error type
        $data = simplexml_load_string($response->getContent());

        return $this->prepareData($data);
    }

    private function prepareData(SimpleXMLElement $data): array
    {
        $result = [];
        $baseCurrency = 'EUR';

        foreach ($data->Cube->Cube->children() as $child) {
            $currency = null;
            $rate = null;
            foreach ($child->attributes() as $a => $b) {
                if ('currency' === $a) {
                    $currency = $b;
                }
                if ('rate' === $a) {
                    $rate = (float) $b;
                }
            }

            $oneside = new RateDTO($baseCurrency, $currency, $rate);
            $this->validator->validate($oneside);
            $result[] = $oneside;

            $backside = new RateDTO($currency, $baseCurrency, 1 / $rate);
            $this->validator->validate($backside);
            $result[] = $backside;
        }

        return $result;
    }
}
