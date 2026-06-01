<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Encoder;

use Octacrafts\QrEngine\Contracts\ModeEncoderInterface;
use Octacrafts\QrEngine\Domain\BitBuffer;
use Octacrafts\QrEngine\Domain\EncodingMode;
use Octacrafts\QrEngine\Domain\QrVersion;
use Octacrafts\QrEngine\Domain\Segment;

final class ByteModeEncoder implements ModeEncoderInterface
{
    public function supports(EncodingMode $mode): bool
    {
        return $mode === EncodingMode::Byte;
    }

    public function encode(Segment $segment, QrVersion $version): BitBuffer
    {
        $buffer = new BitBuffer();
        $data = $segment->data();
        $countBits = EncodingMode::Byte->characterCountBits($version);

        $buffer->appendBits(EncodingMode::Byte->indicatorBits(), 4);
        $buffer->appendBits(strlen($data), $countBits);

        for ($i = 0, $len = strlen($data); $i < $len; $i++) {
            $buffer->appendBits(ord($data[$i]), 8);
        }

        return $buffer;
    }
}
