<?php
namespace App\DTO;

class ConvertDTO
{
    public string $chain;

    public float $result;

    public function __construct(string $chain, float $result) {
        $this->chain = $chain;
        $this->result = $result;
    }

}