<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Domain;

enum OutputFormat: string
{
    case Png = 'image/png';
    case Svg = 'image/svg+xml';
}
