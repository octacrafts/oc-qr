<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Tests\Integration;

use Octacrafts\QrEngine\Core\QrBuilder;
use Octacrafts\QrEngine\Domain\ErrorCorrectionLevel;
use Octacrafts\QrEngine\Domain\OutputFormat;
use PHPUnit\Framework\TestCase;

final class QrGenerationTest extends TestCase
{
    public function test_generates_png_for_hello(): void
    {
        $output = QrBuilder::create('HELLO')
            ->errorCorrection(ErrorCorrectionLevel::M)
            ->size(200)
            ->generate();

        self::assertSame(OutputFormat::Png, $output->format());
        self::assertStringStartsWith("\x89PNG", $output->content());
        self::assertGreaterThan(500, strlen($output->content()));
    }

    public function test_generates_svg(): void
    {
        $output = QrBuilder::create('https://example.com')
            ->format(OutputFormat::Svg)
            ->generate();

        self::assertSame(OutputFormat::Svg, $output->format());
        self::assertStringContainsString('<svg', $output->content());
    }

    public function test_numeric_mode(): void
    {
        $output = QrBuilder::create('1234567890123')
            ->generate();

        self::assertNotEmpty($output->content());
    }

    public function test_alphanumeric_mode(): void
    {
        $output = QrBuilder::create('HELLO WORLD')
            ->generate();

        self::assertNotEmpty($output->content());
    }
}
