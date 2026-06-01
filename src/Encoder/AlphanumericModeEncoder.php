<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Encoder;

use Octacrafts\QrEngine\Contracts\ModeEncoderInterface;
use Octacrafts\QrEngine\Domain\BitBuffer;
use Octacrafts\QrEngine\Domain\EncodingMode;
use Octacrafts\QrEngine\Domain\QrVersion;
use Octacrafts\QrEngine\Domain\Segment;

final class AlphanumericModeEncoder implements ModeEncoderInterface
{
    private const CHARSET = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ $%*+-./:';

    public function supports(EncodingMode $mode): bool
    {
        return $mode === EncodingMode::Alphanumeric;
    }

    public function encode(Segment $segment, QrVersion $version): BitBuffer
    {
        $buffer = new BitBuffer();
        $data = $segment->data();
        $length = strlen($data);
        $countBits = EncodingMode::Alphanumeric->characterCountBits($version);

        $buffer->appendBits(EncodingMode::Alphanumeric->indicatorBits(), 4);
        $buffer->appendBits($length, $countBits);

        for ($i = 0; $i < $length; $i += 2) {
            if ($i + 1 < $length) {
                $value = $this->charValue($data[$i]) * 45 + $this->charValue($data[$i + 1]);
                $buffer->appendBits($value, 11);
            } else {
                $buffer->appendBits($this->charValue($data[$i]), 6);
            }
        }

        return $buffer;
    }

    private function charValue(string $char): int
    {
        $pos = strpos(self::CHARSET, $char);

        return $pos === false ? 0 : $pos;
    }
}
