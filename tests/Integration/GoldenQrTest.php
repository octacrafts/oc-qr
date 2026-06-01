<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Tests\Integration;

use Octacrafts\QrEngine\Core\QrEngine;
use Octacrafts\QrEngine\Core\QrEngineFactory;
use Octacrafts\QrEngine\Domain\ErrorCorrectionLevel;
use Octacrafts\QrEngine\Domain\GenerationOptions;
use Octacrafts\QrEngine\Domain\OutputFormat;
use Octacrafts\QrEngine\Domain\Payload;
use Octacrafts\QrEngine\Domain\QrMatrix;
use Octacrafts\QrEngine\Domain\QrVersion;
use Octacrafts\QrEngine\Domain\RenderOptions;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class GoldenQrTest extends TestCase
{
    public function test_hello_version_one_mask_zero_matrix_fingerprint(): void
    {
        $matrix = $this->generateMatrix(
            'HELLO',
            new GenerationOptions(ErrorCorrectionLevel::M, QrVersion::fromNumber(1), 0),
        );

        self::assertSame(21, $matrix->width());
        self::assertSame('6a013d4a4098c9fed041a45096d557d4', $this->matrixFingerprint($matrix));
    }

    public function test_hello_version_one_mask_zero_png_is_valid(): void
    {
        $engine = QrEngineFactory::createDefault();
        $output = $engine->generate(
            Payload::fromString('HELLO'),
            new GenerationOptions(ErrorCorrectionLevel::M, QrVersion::fromNumber(1), 0),
            new RenderOptions(300, 4),
            OutputFormat::Png,
        );

        self::assertStringStartsWith("\x89PNG\r\n\x1a\n", $output->content());
        self::assertGreaterThan(500, strlen($output->content()));
    }

    private function generateMatrix(string $content, GenerationOptions $options): QrMatrix
    {
        $engine = QrEngineFactory::createDefault();
        $reflection = new ReflectionClass(QrEngine::class);
        $pipelineProperty = $reflection->getProperty('pipeline');
        $pipelineProperty->setAccessible(true);
        $pipeline = $pipelineProperty->getValue($engine);

        return $pipeline->generate(Payload::fromString($content), $options);
    }

    private function matrixFingerprint(QrMatrix $matrix): string
    {
        $bits = '';

        for ($row = 0; $row < $matrix->width(); $row++) {
            for ($col = 0; $col < $matrix->width(); $col++) {
                $bits .= $matrix->isDark($row, $col) ? '1' : '0';
            }
        }

        return md5($bits);
    }
}
