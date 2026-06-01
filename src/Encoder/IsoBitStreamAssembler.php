<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Encoder;

use Octacrafts\QrEngine\Contracts\BitStreamAssemblerInterface;
use Octacrafts\QrEngine\Data\VersionCapacityTable;
use Octacrafts\QrEngine\Domain\BitBuffer;
use Octacrafts\QrEngine\Domain\ErrorCorrectionLevel;
use Octacrafts\QrEngine\Domain\QrVersion;

final class IsoBitStreamAssembler implements BitStreamAssemblerInterface
{
    public function finalize(
        BitBuffer $buffer,
        QrVersion $version,
        ErrorCorrectionLevel $errorCorrection,
    ): BitBuffer {
        $dataCodewords = VersionCapacityTable::dataCodewordCount(
            $version->number(),
            $errorCorrection,
        );
        $totalBits = $dataCodewords * 8;
        $remaining = $totalBits - $buffer->length();

        if ($remaining > 0) {
            $terminator = min(4, $remaining);
            $buffer->appendBits(0, $terminator);
            $remaining -= $terminator;
        }

        while ($buffer->length() % 8 !== 0) {
            $buffer->appendBits(0, 1);
        }

        $bytes = $buffer->toBytes();
        $padByte = 0xEC;

        while (count($bytes) < $dataCodewords) {
            $bytes[] = $padByte;
            $padByte = $padByte === 0xEC ? 0x11 : 0xEC;
        }

        $final = new BitBuffer();

        foreach ($bytes as $byte) {
            $final->appendBits($byte, 8);
        }

        return $final;
    }
}
