<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Encoder;

use Octacrafts\QrEngine\Contracts\ModeEncoderInterface;
use Octacrafts\QrEngine\Domain\BitBuffer;
use Octacrafts\QrEngine\Domain\EncodingMode;
use Octacrafts\QrEngine\Domain\QrVersion;
use Octacrafts\QrEngine\Domain\Segment;

final class NumericModeEncoder implements ModeEncoderInterface
{
    public function supports(EncodingMode $mode): bool
    {
        return $mode === EncodingMode::Numeric;
    }

    public function encode(Segment $segment, QrVersion $version): BitBuffer
    {
        $buffer = new BitBuffer();
        $data = $segment->data();
        $length = strlen($data);
        $countBits = EncodingMode::Numeric->characterCountBits($version);

        $buffer->appendBits(EncodingMode::Numeric->indicatorBits(), 4);
        $buffer->appendBits($length, $countBits);

        for ($i = 0; $i < $length; $i += 3) {
            $remaining = min(3, $length - $i);

            if ($remaining === 3) {
                $value = ((int) $data[$i]) * 100
                    + ((int) $data[$i + 1]) * 10
                    + (int) $data[$i + 2];
                $buffer->appendBits($value, 10);
            } elseif ($remaining === 2) {
                $value = ((int) $data[$i]) * 10 + (int) $data[$i + 1];
                $buffer->appendBits($value, 7);
            } else {
                $buffer->appendBits((int) $data[$i], 4);
            }
        }

        return $buffer;
    }
}
