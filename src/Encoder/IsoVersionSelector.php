<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Encoder;

use Octacrafts\QrEngine\Contracts\BitStreamAssemblerInterface;
use Octacrafts\QrEngine\Contracts\ModeEncoderInterface;
use Octacrafts\QrEngine\Contracts\VersionSelectorInterface;
use Octacrafts\QrEngine\Data\VersionCapacityTable;
use Octacrafts\QrEngine\Domain\BitBuffer;
use Octacrafts\QrEngine\Domain\ErrorCorrectionLevel;
use Octacrafts\QrEngine\Domain\GenerationOptions;
use Octacrafts\QrEngine\Domain\QrVersion;
use Octacrafts\QrEngine\Domain\Segment;
use Octacrafts\QrEngine\Exceptions\CapacityExceededException;

final class IsoVersionSelector implements VersionSelectorInterface
{
    /** @var list<ModeEncoderInterface> */
    private array $encoders;

    public function __construct(
        private readonly BitStreamAssemblerInterface $assembler,
        ModeEncoderInterface ...$encoders,
    ) {
        $this->encoders = $encoders;
    }

    public function select(
        array $segments,
        ErrorCorrectionLevel $errorCorrection,
        GenerationOptions $options,
    ): QrVersion {
        if ($options->forcedVersion() !== null) {
            return $options->forcedVersion();
        }

        for ($version = 1; $version <= 40; $version++) {
            $qrVersion = QrVersion::fromNumber($version);
            $raw = $this->assembleBitStream($segments, $qrVersion, ...$this->encoders);
            $capacityBits = VersionCapacityTable::dataCodewordCount($version, $errorCorrection) * 8;

            if ($raw->length() > $capacityBits) {
                continue;
            }

            $bitsWithTerminator = $raw->length() + min(4, $capacityBits - $raw->length());
            $paddedBits = (int) (ceil($bitsWithTerminator / 8) * 8);

            while ($paddedBits < $capacityBits) {
                $paddedBits += 8;
            }

            if ($paddedBits > $capacityBits) {
                continue;
            }

            return $qrVersion;
        }

        throw new CapacityExceededException('Payload exceeds maximum QR capacity.');
    }

    public function assembleBitStream(
        array $segments,
        QrVersion $version,
        ModeEncoderInterface ...$encoders,
    ): BitBuffer {
        $encoders = $encoders ?: $this->encoders;
        $buffer = new BitBuffer();

        foreach ($segments as $segment) {
            $encoded = false;

            foreach ($encoders as $encoder) {
                if ($encoder->supports($segment->mode())) {
                    $segmentBuffer = $encoder->encode($segment, $version);
                    foreach ($segmentBuffer->bits() as $bit) {
                        $buffer->appendBits($bit, 1);
                    }
                    $encoded = true;
                    break;
                }
            }

            if (! $encoded) {
                throw new CapacityExceededException(
                    'No encoder registered for mode: ' . $segment->mode()->name,
                );
            }
        }

        return $buffer;
    }
}
