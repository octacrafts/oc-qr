<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Masking;

use Octacrafts\QrEngine\Contracts\MaskPatternInterface;

final class MaskPatterns
{
    /** @return list<MaskPatternInterface> */
    public static function all(): array
    {
        return [
            new FormulaMaskPattern(0, fn (int $r, int $c) => ($r + $c) % 2 === 0),
            new FormulaMaskPattern(1, fn (int $r, int $c) => $r % 2 === 0),
            new FormulaMaskPattern(2, fn (int $r, int $c) => $c % 3 === 0),
            new FormulaMaskPattern(3, fn (int $r, int $c) => ($r + $c) % 3 === 0),
            new FormulaMaskPattern(4, fn (int $r, int $c) => (int) (floor($r / 2) + floor($c / 3)) % 2 === 0),
            new FormulaMaskPattern(5, fn (int $r, int $c) => ($r * $c) % 2 + ($r * $c) % 3 === 0),
            new FormulaMaskPattern(6, fn (int $r, int $c) => (($r * $c) % 2 + ($r * $c) % 3) % 2 === 0),
            new FormulaMaskPattern(7, fn (int $r, int $c) => (($r + $c) % 2 + ($r * $c) % 3) % 2 === 0),
        ];
    }

    public static function get(int $id): MaskPatternInterface
    {
        return self::all()[$id];
    }
}
