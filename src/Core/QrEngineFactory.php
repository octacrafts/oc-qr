<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Core;

use Octacrafts\QrEngine\Correction\ErrorCorrectionService;
use Octacrafts\QrEngine\Correction\IsoBlockSplitter;
use Octacrafts\QrEngine\Correction\IsoCodewordInterleaver;
use Octacrafts\QrEngine\Correction\ReedSolomonEncoder;
use Octacrafts\QrEngine\Core\Stages\BuildMatrixStage;
use Octacrafts\QrEngine\Core\Stages\EncodePayloadStage;
use Octacrafts\QrEngine\Core\Stages\ErrorCorrectionStage;
use Octacrafts\QrEngine\Core\Stages\MaskAndFormatStage;
use Octacrafts\QrEngine\Core\Stages\PlaceDataStage;
use Octacrafts\QrEngine\Encoder\AlphanumericModeEncoder;
use Octacrafts\QrEngine\Encoder\ByteModeEncoder;
use Octacrafts\QrEngine\Encoder\EciModeEncoder;
use Octacrafts\QrEngine\Encoder\IsoBitStreamAssembler;
use Octacrafts\QrEngine\Encoder\IsoVersionSelector;
use Octacrafts\QrEngine\Encoder\KanjiModeEncoder;
use Octacrafts\QrEngine\Encoder\MixedModeEncodingStrategy;
use Octacrafts\QrEngine\Encoder\NumericModeEncoder;
use Octacrafts\QrEngine\Generator\FormatInformationWriter;
use Octacrafts\QrEngine\Generator\IsoDataModulePlacer;
use Octacrafts\QrEngine\Generator\IsoFunctionPatternPlacer;
use Octacrafts\QrEngine\Generator\QrMatrixInitializer;
use Octacrafts\QrEngine\Generator\VersionInformationWriter;
use Octacrafts\QrEngine\Masking\BestMaskSelector;
use Octacrafts\QrEngine\Masking\IsoMaskApplicator;
use Octacrafts\QrEngine\Masking\IsoPenaltyCalculator;
use Octacrafts\QrEngine\Output\PngRenderer;
use Octacrafts\QrEngine\Output\RendererRegistry;
use Octacrafts\QrEngine\Output\SvgRenderer;

final class QrEngineFactory
{
    public static function createDefault(): QrEngine
    {
        $encoders = [
            new NumericModeEncoder(),
            new AlphanumericModeEncoder(),
            new ByteModeEncoder(),
            new KanjiModeEncoder(),
            new EciModeEncoder(),
        ];

        $bitStreamAssembler = new IsoBitStreamAssembler();
        $versionSelector = new IsoVersionSelector($bitStreamAssembler, ...$encoders);
        $reedSolomon = new ReedSolomonEncoder();
        $errorCorrection = new ErrorCorrectionService(
            new IsoBlockSplitter(),
            new IsoCodewordInterleaver($reedSolomon),
        );

        $functionPlacer = new IsoFunctionPatternPlacer();
        $formatWriter = new FormatInformationWriter();

        $pipeline = new QrGenerationPipeline([
            new EncodePayloadStage(
                new MixedModeEncodingStrategy(),
                $versionSelector,
                $bitStreamAssembler,
                ...$encoders,
            ),
            new ErrorCorrectionStage($errorCorrection),
            new BuildMatrixStage(
                new QrMatrixInitializer($functionPlacer),
                new VersionInformationWriter(),
            ),
            new PlaceDataStage(new IsoDataModulePlacer()),
            new MaskAndFormatStage(
                new BestMaskSelector(
                    new IsoMaskApplicator(),
                    new IsoPenaltyCalculator(),
                    $formatWriter,
                ),
            ),
        ]);

        $registry = new RendererRegistry(
            new PngRenderer(),
            new SvgRenderer(),
        );

        return new QrEngine($pipeline, $registry);
    }
}
