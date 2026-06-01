<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Correction;

use Octacrafts\QrEngine\Contracts\BlockSplitterInterface;
use Octacrafts\QrEngine\Data\EcBlockTable;
use Octacrafts\QrEngine\Domain\ErrorCorrectionLevel;
use Octacrafts\QrEngine\Domain\QrVersion;

final class IsoBlockSplitter implements BlockSplitterInterface
{
    public function split(array $dataCodewords, QrVersion $version, ErrorCorrectionLevel $errorCorrection): array
    {
        return EcBlockTable::splitBlocks($dataCodewords, $version->number(), $errorCorrection);
    }
}
