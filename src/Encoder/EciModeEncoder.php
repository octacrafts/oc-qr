<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Encoder;

use Octacrafts\QrEngine\Contracts\ModeEncoderInterface;
use Octacrafts\QrEngine\Domain\BitBuffer;
use Octacrafts\QrEngine\Domain\EncodingMode;
use Octacrafts\QrEngine\Domain\QrVersion;
use Octacrafts\QrEngine\Domain\Segment;

final class EciModeEncoder implements ModeEncoderInterface
{
    public function supports(EncodingMode $mode): bool
    {
        return $mode === EncodingMode::Eci;
    }

    public function encode(Segment $segment, QrVersion $version): BitBuffer
    {
        $buffer = new BitBuffer();
        $assignmentNumber = (int) $segment->data();

        $buffer->appendBits(EncodingMode::Eci->indicatorBits(), 4);

        if ($assignmentNumber < 128) {
            $buffer->appendBits($assignmentNumber, 8);
        } elseif ($assignmentNumber < 16384) {
            $buffer->appendBits(2, 2);
            $buffer->appendBits($assignmentNumber, 14);
        } else {
            $buffer->appendBits(6, 3);
            $buffer->appendBits($assignmentNumber, 21);
        }

        return $buffer;
    }
}
