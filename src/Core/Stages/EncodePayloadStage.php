<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Core\Stages;

use Octacrafts\QrEngine\Contracts\BitStreamAssemblerInterface;
use Octacrafts\QrEngine\Contracts\EncodingStrategyInterface;
use Octacrafts\QrEngine\Contracts\ModeEncoderInterface;
use Octacrafts\QrEngine\Contracts\PipelineStageInterface;
use Octacrafts\QrEngine\Contracts\VersionSelectorInterface;
use Octacrafts\QrEngine\Core\GenerationContext;

final class EncodePayloadStage implements PipelineStageInterface
{
    /** @var list<ModeEncoderInterface> */
    private readonly array $encoders;

    public function __construct(
        private readonly EncodingStrategyInterface $encodingStrategy,
        private readonly VersionSelectorInterface $versionSelector,
        private readonly BitStreamAssemblerInterface $bitStreamAssembler,
        ModeEncoderInterface ...$encoders,
    ) {
        $this->encoders = $encoders;
    }

    public function __invoke(GenerationContext $context): GenerationContext
    {
        $segments = $this->encodingStrategy->segments($context->payload);
        $version = $this->versionSelector->select(
            $segments,
            $context->options->errorCorrection(),
            $context->options,
        );

        $raw = $this->versionSelector->assembleBitStream($segments, $version, ...array_values($this->encoders));
        $final = $this->bitStreamAssembler->finalize(
            $raw,
            $version,
            $context->options->errorCorrection(),
        );

        return $context
            ->withSegments($segments)
            ->withVersion($version)
            ->withRawBitStream($raw)
            ->withFinalBitStream($final);
    }
}
