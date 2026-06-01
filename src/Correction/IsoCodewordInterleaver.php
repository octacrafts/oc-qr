<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Correction;

use Octacrafts\QrEngine\Contracts\CodewordInterleaverInterface;
use Octacrafts\QrEngine\Contracts\ReedSolomonEncoderInterface;

final class IsoCodewordInterleaver implements CodewordInterleaverInterface
{
    public function __construct(private readonly ReedSolomonEncoderInterface $reedSolomon)
    {
    }

    public function interleave(array $blocks): array
    {
        $withEc = [];

        foreach ($blocks as $block) {
            $ec = $this->reedSolomon->encode($block['data'], $block['ecCount']);
            $withEc[] = ['data' => $block['data'], 'ec' => $ec];
        }

        $result = [];
        $maxData = max(array_map(fn (array $b) => count($b['data']), $withEc));
        $maxEc = max(array_map(fn (array $b) => count($b['ec']), $withEc));

        for ($i = 0; $i < $maxData; $i++) {
            foreach ($withEc as $block) {
                if (isset($block['data'][$i])) {
                    $result[] = $block['data'][$i];
                }
            }
        }

        for ($i = 0; $i < $maxEc; $i++) {
            foreach ($withEc as $block) {
                if (isset($block['ec'][$i])) {
                    $result[] = $block['ec'][$i];
                }
            }
        }

        return $result;
    }
}
