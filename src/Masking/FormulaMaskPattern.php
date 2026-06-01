<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Masking;

use Octacrafts\QrEngine\Contracts\MaskPatternInterface;

final class FormulaMaskPattern implements MaskPatternInterface
{
    /** @var callable(int, int): bool */
    private $formula;

    private int $id;

    public function __construct(int $id, callable $formula)
    {
        $this->id = $id;
        $this->formula = $formula;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function shouldInvert(int $row, int $col): bool
    {
        return ($this->formula)($row, $col);
    }
}
