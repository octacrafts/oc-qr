<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Domain;

final class GenerationOptions
{
    public function __construct(
        private readonly ErrorCorrectionLevel $errorCorrection = ErrorCorrectionLevel::M,
        private readonly ?QrVersion $forcedVersion = null,
        private readonly ?int $forcedMask = null,
    ) {
    }

    public function errorCorrection(): ErrorCorrectionLevel
    {
        return $this->errorCorrection;
    }

    public function forcedVersion(): ?QrVersion
    {
        return $this->forcedVersion;
    }

    public function forcedMask(): ?int
    {
        return $this->forcedMask;
    }

    public function withErrorCorrection(ErrorCorrectionLevel $level): self
    {
        return new self($level, $this->forcedVersion, $this->forcedMask);
    }

    public function withVersion(QrVersion $version): self
    {
        return new self($this->errorCorrection, $version, $this->forcedMask);
    }

    public function withMask(int $mask): self
    {
        return new self($this->errorCorrection, $this->forcedVersion, $mask);
    }
}
