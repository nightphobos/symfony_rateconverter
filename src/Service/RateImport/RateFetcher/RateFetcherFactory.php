<?php

namespace App\Service\RateImport\RateFetcher;

use Exception;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RateFetcherFactory
{
    public const FETCHER_COINDESK_COM = 'coindesk.com';

    public const FETCHER_EUROPA_EU = 'europa.eu';

    private HttpClientInterface $client;
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->client = HttpClient::create();
        $this->validator = $validator;
    }

    /**
     * @throws Exception
     */
    public function build(string $source): RateFetcherInterface
    {
        switch ($source) {
            case self::FETCHER_COINDESK_COM:
                return new CoindeskComRateFetcher($this->client, $this->validator);
            case self::FETCHER_EUROPA_EU:
                return new EuropaEuRateFetcher($this->client, $this->validator);
            default:
                throw new Exception(sprintf('Source %f unknown', $source));
        }
    }
}
