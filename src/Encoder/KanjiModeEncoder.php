<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Encoder;

use Octacrafts\QrEngine\Contracts\ModeEncoderInterface;
use Octacrafts\QrEngine\Domain\BitBuffer;
use Octacrafts\QrEngine\Domain\EncodingMode;
use Octacrafts\QrEngine\Domain\QrVersion;
use Octacrafts\QrEngine\Domain\Segment;
use Octacrafts\QrEngine\Exceptions\InvalidPayloadException;

final class KanjiModeEncoder implements ModeEncoderInterface
{
    public function supports(EncodingMode $mode): bool
    {
        return $mode === EncodingMode::Kanji;
    }

    public function encode(Segment $segment, QrVersion $version): BitBuffer
    {
        $buffer = new BitBuffer();
        $data = $segment->data();
        $byteLength = strlen($data);

        if ($byteLength % 2 !== 0) {
            throw new InvalidPayloadException('Kanji mode requires an even number of bytes.');
        }

        $charCount = (int) ($byteLength / 2);
        $countBits = EncodingMode::Kanji->characterCountBits($version);

        $buffer->appendBits(EncodingMode::Kanji->indicatorBits(), 4);
        $buffer->appendBits($charCount, $countBits);

        for ($i = 0; $i < $byteLength; $i += 2) {
            $code = (ord($data[$i]) << 8) | ord($data[$i + 1]);
            $value = ($code >= 0x8140 && $code <= 0x9FFC)
                ? $code - 0x8140
                : (($code >= 0xE040 && $code <= 0xEBBF) ? $code - 0xC140 : 0);
            $buffer->appendBits(($value >> 8) * 0xC0 + ($value & 0xFF), 13);
        }

        return $buffer;
    }
}
