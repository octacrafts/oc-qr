<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Output;

use Octacrafts\QrEngine\Domain\QrMatrix;
use Octacrafts\QrEngine\Domain\RenderOptions;

final class ModuleGridScaler
{
    /**
     * @return array{moduleSize: int, imageSize: int, marginModules: int}
     */
    public static function dimensions(QrMatrix $matrix, RenderOptions $options): array
    {
        $marginModules = $options->margin();
        $totalModules = $matrix->width() + ($marginModules * 2);
        $moduleSize = max(1, (int) floor($options->size() / $totalModules));
        $imageSize = $moduleSize * $totalModules;

        return [
            'moduleSize' => $moduleSize,
            'imageSize' => $imageSize,
            'marginModules' => $marginModules,
        ];
    }
}
