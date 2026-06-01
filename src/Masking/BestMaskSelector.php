<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Masking;

use Octacrafts\QrEngine\Contracts\FormatInformationWriterInterface;
use Octacrafts\QrEngine\Contracts\MaskApplicatorInterface;
use Octacrafts\QrEngine\Contracts\MaskSelectorInterface;
use Octacrafts\QrEngine\Contracts\PenaltyCalculatorInterface;
use Octacrafts\QrEngine\Domain\ErrorCorrectionLevel;
use Octacrafts\QrEngine\Domain\GenerationOptions;
use Octacrafts\QrEngine\Domain\QrMatrix;
use Octacrafts\QrEngine\Domain\QrVersion;

final class BestMaskSelector implements MaskSelectorInterface
{
    public function __construct(
        private readonly MaskApplicatorInterface $maskApplicator,
        private readonly PenaltyCalculatorInterface $penaltyCalculator,
        private readonly FormatInformationWriterInterface $formatWriter,
    ) {
    }

    public function selectAndApply(
        QrMatrix $matrix,
        ErrorCorrectionLevel $level,
        GenerationOptions $options,
    ): QrMatrix {
        if ($options->forcedMask() !== null) {
            $mask = $options->forcedMask();
            $masked = $this->maskApplicator->apply($matrix, $mask);
            $this->formatWriter->write($masked, $level, $mask);

            return $masked;
        }

        $bestMask = 0;
        $lowestPenalty = PHP_INT_MAX;
        $bestMatrix = $matrix;

        for ($mask = 0; $mask < 8; $mask++) {
            $candidate = $this->maskApplicator->apply($matrix->clone(), $mask);
            $penalty = $this->penaltyCalculator->calculate($candidate);

            if ($penalty < $lowestPenalty) {
                $lowestPenalty = $penalty;
                $bestMask = $mask;
                $bestMatrix = $candidate;
            }
        }

        $this->formatWriter->write($bestMatrix, $level, $bestMask);

        return $bestMatrix;
    }
}
