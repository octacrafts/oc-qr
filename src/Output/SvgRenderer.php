<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Output;

use Octacrafts\QrEngine\Contracts\RendererInterface;
use Octacrafts\QrEngine\Domain\OutputFormat;
use Octacrafts\QrEngine\Domain\QrMatrix;
use Octacrafts\QrEngine\Domain\RenderedOutput;
use Octacrafts\QrEngine\Domain\RenderOptions;

final class SvgRenderer implements RendererInterface
{
    public function supports(OutputFormat $format): bool
    {
        return $format === OutputFormat::Svg;
    }

    public function render(QrMatrix $matrix, RenderOptions $options): RenderedOutput
    {
        $dimensions = ModuleGridScaler::dimensions($matrix, $options);
        $moduleSize = $dimensions['moduleSize'];
        $imageSize = $dimensions['imageSize'];
        $margin = $dimensions['marginModules'];
        $fg = $options->foreground();
        $bg = $options->background();

        $rects = [];

        for ($row = 0; $row < $matrix->width(); $row++) {
            for ($col = 0; $col < $matrix->width(); $col++) {
                if (! $matrix->isDark($row, $col)) {
                    continue;
                }

                $x = ($col + $margin) * $moduleSize;
                $y = ($row + $margin) * $moduleSize;
                $rects[] = sprintf(
                    '<rect x="%d" y="%d" width="%d" height="%d"/>',
                    $x,
                    $y,
                    $moduleSize,
                    $moduleSize,
                );
            }
        }

        $svg = sprintf(
            '<?xml version="1.0" encoding="UTF-8"?>' .
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %1$d %1$d" width="%1$d" height="%1$d">' .
            '<rect width="100%%" height="100%%" fill="%2$s"/>' .
            '<g fill="%3$s">%4$s</g></svg>',
            $imageSize,
            $bg,
            $fg,
            implode('', $rects),
        );

        return new RenderedOutput(OutputFormat::Svg, $svg);
    }
}
