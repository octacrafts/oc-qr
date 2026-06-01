<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Correction;

use Octacrafts\QrEngine\Contracts\ReedSolomonEncoderInterface;

final class ReedSolomonEncoder implements ReedSolomonEncoderInterface
{
    private GaloisField $field;

    public function __construct(?GaloisField $field = null)
    {
        $this->field = $field ?? new GaloisField();
    }

    public function encode(array $dataCodewords, int $ecCodewordCount): array
    {
        $divisor = $this->field->computeDivisor($ecCodewordCount);
        $remainder = array_fill(0, $ecCodewordCount, 0);

        foreach ($dataCodewords as $byte) {
            $factor = ($byte ^ $remainder[0]) & 0xFF;
            array_shift($remainder);
            $remainder[] = 0;

            for ($i = 0; $i < $ecCodewordCount; $i++) {
                $remainder[$i] ^= $this->field->multiply($divisor[$i], $factor);
            }
        }

        return $remainder;
    }
}
