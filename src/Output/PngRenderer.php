<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Output;

use Octacrafts\QrEngine\Contracts\RendererInterface;
use Octacrafts\QrEngine\Domain\OutputFormat;
use Octacrafts\QrEngine\Domain\QrMatrix;
use Octacrafts\QrEngine\Domain\RenderedOutput;
use Octacrafts\QrEngine\Domain\RenderOptions;
use Octacrafts\QrEngine\Exceptions\RenderingException;

final class PngRenderer implements RendererInterface
{
    public function supports(OutputFormat $format): bool
    {
        return $format === OutputFormat::Png;
    }

    public function render(QrMatrix $matrix, RenderOptions $options): RenderedOutput
    {
        $dimensions = ModuleGridScaler::dimensions($matrix, $options);
        $moduleSize = $dimensions['moduleSize'];
        $imageSize = $dimensions['imageSize'];
        $margin = $dimensions['marginModules'];

        $image = imagecreatetruecolor($imageSize, $imageSize);

        if ($image === false) {
            throw new RenderingException('Failed to create PNG image.');
        }

        $background = $this->hexToColor($image, $options->background());
        $foreground = $this->hexToColor($image, $options->foreground());

        imagefill($image, 0, 0, $background);

        for ($row = 0; $row < $matrix->width(); $row++) {
            for ($col = 0; $col < $matrix->width(); $col++) {
                if (! $matrix->isDark($row, $col)) {
                    continue;
                }

                $x = ($col + $margin) * $moduleSize;
                $y = ($row + $margin) * $moduleSize;
                imagefilledrectangle(
                    $image,
                    $x,
                    $y,
                    $x + $moduleSize - 1,
                    $y + $moduleSize - 1,
                    $foreground,
                );
            }
        }

        ob_start();
        imagepng($image);
        $content = (string) ob_get_clean();
        imagedestroy($image);

        return new RenderedOutput(OutputFormat::Png, $content);
    }

    private function hexToColor(\GdImage $image, string $hex): int
    {
        $hex = ltrim($hex, '#');
        $r = (int) hexdec(substr($hex, 0, 2));
        $g = (int) hexdec(substr($hex, 2, 2));
        $b = (int) hexdec(substr($hex, 4, 2));

        return imagecolorallocate($image, $r, $g, $b);
    }
}
