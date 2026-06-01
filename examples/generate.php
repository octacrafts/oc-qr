<?php

declare(strict_types=1);

/**
 * Example script — run from project root after `composer install`:
 *
 *   php examples/generate.php
 */

require dirname(__DIR__) . '/vendor/autoload.php';

use Octacrafts\QrEngine\Core\QrBuilder;
use Octacrafts\QrEngine\Domain\ErrorCorrectionLevel;
use Octacrafts\QrEngine\Domain\OutputFormat;

$outputDir = __DIR__ . '/output';

if (! is_dir($outputDir) && ! mkdir($outputDir, 0755, true) && ! is_dir($outputDir)) {
    throw new RuntimeException('Could not create output directory.');
}

$png = QrBuilder::create('https://example.com')
    ->errorCorrection(ErrorCorrectionLevel::M)
    ->size(300)
    ->margin(4)
    ->format(OutputFormat::Png)
    ->generate();

file_put_contents($outputDir . '/qr.png', $png->content());

$svg = QrBuilder::create('https://example.com')
    ->format(OutputFormat::Svg)
    ->size(300)
    ->margin(4)
    ->generate();

file_put_contents($outputDir . '/qr.svg', $svg->content());

echo "Saved examples/output/qr.png and examples/output/qr.svg\n";
echo 'PNG size: ' . strlen($png->content()) . " bytes\n";
