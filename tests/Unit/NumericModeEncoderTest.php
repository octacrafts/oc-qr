<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Tests\Unit;

use Octacrafts\QrEngine\Domain\EncodingMode;
use Octacrafts\QrEngine\Domain\QrVersion;
use Octacrafts\QrEngine\Domain\Segment;
use Octacrafts\QrEngine\Encoder\NumericModeEncoder;
use PHPUnit\Framework\TestCase;

final class NumericModeEncoderTest extends TestCase
{
    public function test_encodes_mode_indicator_and_count(): void
    {
        $encoder = new NumericModeEncoder();
        $buffer = $encoder->encode(new Segment(EncodingMode::Numeric, '123'), QrVersion::fromNumber(1));
        $bits = $buffer->bits();

        self::assertSame([0, 0, 0, 1], array_slice($bits, 0, 4));
        self::assertSame([0, 0, 0, 0, 0, 0, 0, 0, 1, 1], array_slice($bits, 4, 10));
    }
}
