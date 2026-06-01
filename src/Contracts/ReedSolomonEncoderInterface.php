<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Contracts;

interface ReedSolomonEncoderInterface
{
    /**
     * @param list<int> $dataCodewords
     * @return list<int>
     */
    public function encode(array $dataCodewords, int $ecCodewordCount): array;
}
