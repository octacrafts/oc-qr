<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Tests\Integration;

use Octacrafts\QrEngine\Core\QrEngineFactory;
use Octacrafts\QrEngine\Domain\ErrorCorrectionLevel;
use Octacrafts\QrEngine\Domain\GenerationOptions;
use Octacrafts\QrEngine\Domain\OutputFormat;
use Octacrafts\QrEngine\Domain\Payload;
use Octacrafts\QrEngine\Domain\QrVersion;
use Octacrafts\QrEngine\Domain\RenderOptions;
use PHPUnit\Framework\TestCase;

final class GoldenQrTest extends TestCase
{
    public function test_hello_version_one_mask_zero_png_hash(): void
    {
        $engine = QrEngineFactory::createDefault();
        $output = $engine->generate(
            Payload::fromString('HELLO'),
            new GenerationOptions(ErrorCorrectionLevel::M, QrVersion::fromNumber(1), 0),
            new RenderOptions(300, 4),
            OutputFormat::Png,
        );

        self::assertSame('e768ef7b899020a8c62f3f1e0cb0aefe', md5($output->content()));
    }
}
