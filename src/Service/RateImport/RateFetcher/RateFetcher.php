<?php

namespace App\Service\RateImport\RateFetcher;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class RateFetcher implements RateFetcherInterface
{
    //maybe better use Trait for this?
    protected HttpClientInterface $client;
    protected ValidatorInterface $validator;

    public function __construct(HttpClientInterface $client, ValidatorInterface $validator)
    {
        $this->client = $client;
        $this->validator = $validator;
    }

    public function getData(): array
    {
        return [];
    }
}