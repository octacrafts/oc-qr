<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Contracts;

use Octacrafts\QrEngine\Domain\BitBuffer;
use Octacrafts\QrEngine\Domain\EncodingMode;
use Octacrafts\QrEngine\Domain\QrVersion;
use Octacrafts\QrEngine\Domain\Segment;

interface ModeEncoderInterface
{
    public function supports(EncodingMode $mode): bool;

    public function encode(Segment $segment, QrVersion $version): BitBuffer;
}
