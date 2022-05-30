<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RateDTO
{
    /**
     * @Assert\NotBlank
     */
    public string $from;

    /**
     * @Assert\NotBlank
     */
    public string $to;

    /**
     * @Assert\NotBlank
     */
    public float $rate;

    public function __construct(string $from, string $to, float|int $rate) {
        $this->from = $from;
        $this->to = $to;
        $this->rate = (float) $rate;
    }

}