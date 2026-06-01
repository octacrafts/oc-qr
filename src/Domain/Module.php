<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Domain;

enum Module: int
{
    case Unset = -1;
    case Light = 0;
    case Dark = 1;

    public function isSet(): bool
    {
        return $this !== self::Unset;
    }

    public function isDark(): bool
    {
        return $this === self::Dark;
    }
}
