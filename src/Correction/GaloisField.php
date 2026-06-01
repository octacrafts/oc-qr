<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Correction;

/**
 * Galois field GF(256) for QR Reed-Solomon (primitive polynomial 0x11D).
 */
final class GaloisField
{
    /** @var list<int> */
    private array $expTable = [];

    /** @var list<int> */
    private array $logTable = [];

    public function __construct()
    {
        $x = 1;

        for ($i = 0; $i < 255; $i++) {
            $this->expTable[$i] = $x;
            $this->logTable[$x] = $i;
            $x <<= 1;

            if ($x > 255) {
                $x ^= 0x11D;
            }
        }

        for ($i = 255; $i < 512; $i++) {
            $this->expTable[$i] = $this->expTable[$i - 255];
        }
    }

    public function multiply(int $a, int $b): int
    {
        if ($a === 0 || $b === 0) {
            return 0;
        }

        return $this->expTable[($this->logTable[$a] + $this->logTable[$b]) % 255];
    }

    /**
     * @return list<int> divisor coefficients (length = degree)
     */
    public function computeDivisor(int $degree): array
    {
        $result = array_fill(0, $degree, 0);
        $result[$degree - 1] = 1;
        $root = 1;

        for ($i = 0; $i < $degree; $i++) {
            for ($j = 0; $j < $degree; $j++) {
                $result[$j] = $this->multiply($result[$j], $root);

                if ($j + 1 < $degree) {
                    $result[$j] ^= $result[$j + 1];
                }
            }

            $root = $this->multiply($root, 2);
        }

        return $result;
    }
}
