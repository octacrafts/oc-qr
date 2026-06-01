<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Contracts;

interface CodewordInterleaverInterface
{
    /**
     * @param list<array{data: list<int>, ec: list<int>}> $blocks
     * @return list<int>
     */
    public function interleave(array $blocks): array;
}
