<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Domain;

final class CodewordSequence
{
    /**
     * @param list<int> $dataCodewords
     * @param list<int> $ecCodewords
     * @param list<int> $interleaved
     */
    public function __construct(
        private readonly array $dataCodewords,
        private readonly array $ecCodewords,
        private readonly array $interleaved,
    ) {
    }

    /** @return list<int> */
    public function dataCodewords(): array
    {
        return $this->dataCodewords;
    }

    /** @return list<int> */
    public function ecCodewords(): array
    {
        return $this->ecCodewords;
    }

    /** @return list<int> */
    public function interleaved(): array
    {
        return $this->interleaved;
    }

    /** @return list<int> */
    public function allBits(): array
    {
        $bits = [];

        foreach ($this->interleaved as $byte) {
            for ($i = 7; $i >= 0; $i--) {
                $bits[] = ($byte >> $i) & 1;
            }
        }

        return $bits;
    }
}
