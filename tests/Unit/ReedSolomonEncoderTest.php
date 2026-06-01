<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Tests\Unit;

use Octacrafts\QrEngine\Correction\ReedSolomonEncoder;
use PHPUnit\Framework\TestCase;

final class ReedSolomonEncoderTest extends TestCase
{
    public function test_produces_expected_ec_codeword_count(): void
    {
        $encoder = new ReedSolomonEncoder();
        $data = array_fill(0, 16, 0);

        self::assertCount(10, $encoder->encode($data, 10));
    }

    public function test_ec_is_deterministic(): void
    {
        $encoder = new ReedSolomonEncoder();
        $data = [32, 91, 11, 120, 209, 114, 220, 77, 192, 56, 70, 181, 25, 43, 76, 6];

        self::assertSame($encoder->encode($data, 10), $encoder->encode($data, 10));
    }
}
