<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Core;

use Octacrafts\QrEngine\Domain\ErrorCorrectionLevel;
use Octacrafts\QrEngine\Domain\GenerationOptions;
use Octacrafts\QrEngine\Domain\OutputFormat;
use Octacrafts\QrEngine\Domain\Payload;
use Octacrafts\QrEngine\Domain\RenderedOutput;
use Octacrafts\QrEngine\Domain\RenderOptions;

final class QrBuilder
{
    private Payload $payload;

    private GenerationOptions $generationOptions;

    private RenderOptions $renderOptions;

    private OutputFormat $format;

    private function __construct(string $content, private readonly QrEngine $engine)
    {
        $this->payload = Payload::fromString($content);
        $this->generationOptions = new GenerationOptions();
        $this->renderOptions = new RenderOptions();
        $this->format = OutputFormat::Png;
    }

    public static function create(string $content, ?QrEngine $engine = null): self
    {
        return new self($content, $engine ?? QrEngineFactory::createDefault());
    }

    public function errorCorrection(ErrorCorrectionLevel $level): self
    {
        $this->generationOptions = $this->generationOptions->withErrorCorrection($level);

        return $this;
    }

    public function size(int $pixels): self
    {
        $this->renderOptions = $this->renderOptions->withSize($pixels);

        return $this;
    }

    public function margin(int $modules): self
    {
        $this->renderOptions = $this->renderOptions->withMargin($modules);

        return $this;
    }

    public function format(OutputFormat $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function generate(): RenderedOutput
    {
        return $this->engine->generate(
            $this->payload,
            $this->generationOptions,
            $this->renderOptions,
            $this->format,
        );
    }
}
