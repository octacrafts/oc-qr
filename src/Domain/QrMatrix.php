<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Domain;

final class QrMatrix
{
    /** @var list<list<Module>> */
    private array $modules;

    /** @var list<list<bool>> */
    private array $reserved;

    public function __construct(private readonly int $width)
    {
        $this->modules = array_fill(0, $width, array_fill(0, $width, Module::Unset));
        $this->reserved = array_fill(0, $width, array_fill(0, $width, false));
    }

    public function width(): int
    {
        return $this->width;
    }

    public function get(int $row, int $col): Module
    {
        return $this->modules[$row][$col];
    }

    public function set(int $row, int $col, Module $module): void
    {
        $this->modules[$row][$col] = $module;
    }

    public function isUnset(int $row, int $col): bool
    {
        return $this->modules[$row][$col] === Module::Unset;
    }

    public function isDark(int $row, int $col): bool
    {
        return $this->modules[$row][$col] === Module::Dark;
    }

    public function reserve(int $row, int $col): void
    {
        $this->reserved[$row][$col] = true;
    }

    public function isReserved(int $row, int $col): bool
    {
        return $this->reserved[$row][$col];
    }

    public function clone(): self
    {
        $clone = new self($this->width);

        for ($row = 0; $row < $this->width; $row++) {
            for ($col = 0; $col < $this->width; $col++) {
                $clone->modules[$row][$col] = $this->modules[$row][$col];
                $clone->reserved[$row][$col] = $this->reserved[$row][$col];
            }
        }

        return $clone;
    }

    /** @return list<list<Module>> */
    public function modules(): array
    {
        return $this->modules;
    }
}
