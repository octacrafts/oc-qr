# OC-QR

**octacrafts/oc-qr** — A framework-agnostic PHP library for generating scannable QR codes (ISO/IEC 18004).

Pure PHP core with no Laravel or Symfony dependency. Use it in any PHP 8.1+ project, or wrap it in a Laravel package for your ecosystem.

## Features

- ISO/IEC 18004–compliant matrix generation (versions 1–40)
- Automatic encoding mode selection: numeric, alphanumeric, and byte (mixed-mode)
- Kanji and ECI encoders for extended payloads
- Reed–Solomon error correction (levels L, M, Q, H)
- Optimal mask pattern selection (8 patterns, penalty scoring)
- PNG and SVG output
- Clean architecture: pipeline stages, interfaces, and swappable components

## Requirements

| Requirement | Notes |
|-------------|--------|
| PHP | `^8.1` |
| ext-gd | Required for PNG output |
| ext-json | Required |

SVG output does not require GD.

## Installation

```bash
composer require octacrafts/oc-qr
```

## Quick start

```php
<?php

require 'vendor/autoload.php';

use Octacrafts\QrEngine\Core\QrBuilder;
use Octacrafts\QrEngine\Domain\ErrorCorrectionLevel;
use Octacrafts\QrEngine\Domain\OutputFormat;

$output = QrBuilder::create('https://example.com')
    ->errorCorrection(ErrorCorrectionLevel::M)
    ->size(300)
    ->margin(4)
    ->format(OutputFormat::Png)
    ->generate();

file_put_contents('qr.png', $output->content());
```

### SVG

```php
$svg = QrBuilder::create('Hello, World!')
    ->format(OutputFormat::Svg)
    ->size(300)
    ->margin(4)
    ->generate();

file_put_contents('qr.svg', $svg->content());
```

### Return an HTTP response (e.g. Slim, Laravel route)

```php
return response($output->content())
    ->header('Content-Type', $output->contentType());
```

## API reference (`QrBuilder`)

| Method | Description | Default |
|--------|-------------|---------|
| `QrBuilder::create(string $content)` | Build a QR from text or URL | — |
| `errorCorrection(ErrorCorrectionLevel $level)` | Error correction level | `M` |
| `size(int $pixels)` | Output image size in pixels | `300` |
| `margin(int $modules)` | Quiet zone (white border) in **modules**; use `4` or more for reliable scanning | `4` |
| `format(OutputFormat $format)` | `OutputFormat::Png` or `OutputFormat::Svg` | PNG |
| `generate()` | Returns `RenderedOutput` | — |

### Error correction levels

| Level | Enum | Typical use |
|-------|------|-------------|
| L (~7%) | `ErrorCorrectionLevel::L` | Clean prints, max data capacity |
| M (~15%) | `ErrorCorrectionLevel::M` | **Default** — general URLs and text |
| Q (~25%) | `ErrorCorrectionLevel::Q` | Logos, slight damage |
| H (~30%) | `ErrorCorrectionLevel::H` | Smallest logo overlay, harsh environments |

### `RenderedOutput`

```php
$output->content();      // Raw bytes (PNG) or XML string (SVG)
$output->format();       // OutputFormat enum
$output->contentType(); // e.g. image/png, image/svg+xml
```

## Advanced usage

For custom colors, forced QR version, or mask pattern, use `QrEngine` directly:

```php
use Octacrafts\QrEngine\Core\QrEngineFactory;
use Octacrafts\QrEngine\Domain\ErrorCorrectionLevel;
use Octacrafts\QrEngine\Domain\GenerationOptions;
use Octacrafts\QrEngine\Domain\OutputFormat;
use Octacrafts\QrEngine\Domain\Payload;
use Octacrafts\QrEngine\Domain\QrVersion;
use Octacrafts\QrEngine\Domain\RenderOptions;

$engine = QrEngineFactory::createDefault();

$output = $engine->generate(
    Payload::fromString('https://example.com'),
    (new GenerationOptions(ErrorCorrectionLevel::M))
        ->withVersion(QrVersion::fromNumber(2))  // optional: force version 1–40
        ->withMask(2),                            // optional: force mask 0–7
    new RenderOptions(
        size: 400,
        margin: 4,
        foreground: '#000000',
        background: '#ffffff',
    ),
    OutputFormat::Png,
);
```

Version and mask are chosen automatically when omitted.

## Architecture

Dependencies flow inward; the public entry point is **`QrBuilder`** (or **`QrEngine`** for full control).

| Layer | Responsibility |
|-------|----------------|
| **Core** | `QrBuilder`, `QrEngine`, generation pipeline |
| **Encoder** | Numeric, alphanumeric, byte, kanji, ECI; mixed-mode segmentation |
| **Correction** | Reed–Solomon, block split, codeword interleaving |
| **Generator** | Matrix, finder/timing/alignment patterns, data placement |
| **Masking** | Eight mask patterns, penalty rules, best-mask selection |
| **Output** | PNG (`ext-gd`) and SVG renderers |

Extension points live under `Octacrafts\QrEngine\Contracts\` for custom encoders, renderers, or pipeline stages.

## Laravel integration

This package does not ship Laravel bindings. For a Laravel app, create a thin wrapper package (service provider + facade) that depends on `octacrafts/oc-qr` and delegates to `QrBuilder` or `QrEngine`.

## Development

```bash
composer install
composer test
```

Generate sample files locally:

```bash
php examples/generate.php
```

Outputs are written to `examples/output/` (gitignored).

## License

MIT — see [LICENSE](LICENSE).

## Author

[Octacrafts](https://github.com/octacrafts) — `octacrafts/oc-qr`
