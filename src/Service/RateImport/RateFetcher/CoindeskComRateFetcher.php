<?php

namespace App\Service\RateImport\RateFetcher;

use App\DTO\RateDTO;

class CoindeskComRateFetcher extends RateFetcher
{
    //Depends on api usage this may be transfered to configs
    private const URL="https://api.coindesk.com/v1/bpi/historical/close.json";


    public function getData(): array
    {
        $response = $this->client->request("GET", self::URL);
        //@TODO wrap in our error type
        $data = $response->toArray(true);

        return $this->prepareData($data);
    }

    private function prepareData($data){
        $rate = null;

        try {
            $histRates = $data['bpi'];
            //a bit too much, but we are not sure order is correct
            ksort($histRates);
            $rate = end($histRates);
        } catch (\Exception $exception) {
            //@TODO logging
        }

        $btcusd = new RateDTO('BTC', 'USD', $rate);
        $this->validator->validate($btcusd);
        $result[] = $btcusd;

        $usdbtc = new RateDTO('USD', 'BTC', 1/$rate);
        $this->validator->validate($usdbtc);
        $result[] = $usdbtc;

        return $result;
    }
}
