<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Encoder;

use Octacrafts\QrEngine\Contracts\EncodingStrategyInterface;
use Octacrafts\QrEngine\Domain\EncodingMode;
use Octacrafts\QrEngine\Domain\Payload;
use Octacrafts\QrEngine\Domain\Segment;

final class MixedModeEncodingStrategy implements EncodingStrategyInterface
{
    private const ALPHANUMERIC = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ $%*+-./:';

    public function segments(Payload $payload): array
    {
        $content = $payload->content();
        $length = strlen($content);

        if ($length === 0) {
            return [new Segment(EncodingMode::Byte, '')];
        }

        if ($this->isNumeric($content)) {
            return [new Segment(EncodingMode::Numeric, $content)];
        }

        if ($this->isAlphanumeric($content)) {
            return [new Segment(EncodingMode::Alphanumeric, $content)];
        }

        $mixed = $this->buildMixedSegments($content);

        if ($this->bitLength($mixed) < $this->bitLength([new Segment(EncodingMode::Byte, $content)])) {
            return $mixed;
        }

        return [new Segment(EncodingMode::Byte, $content)];
    }

    private function isNumeric(string $content): bool
    {
        return preg_match('/^\d+$/', $content) === 1;
    }

    private function isAlphanumeric(string $content): bool
    {
        $len = strlen($content);

        for ($i = 0; $i < $len; $i++) {
            if (strpos(self::ALPHANUMERIC, $content[$i]) === false) {
                return false;
            }
        }

        return true;
    }

    /** @return list<Segment> */
    private function buildMixedSegments(string $content): array
    {
        $segments = [];
        $length = strlen($content);
        $i = 0;

        while ($i < $length) {
            if (ctype_digit($content[$i])) {
                $start = $i;

                while ($i < $length && ctype_digit($content[$i])) {
                    $i++;
                }

                $segments[] = new Segment(EncodingMode::Numeric, substr($content, $start, $i - $start));
                continue;
            }

            if (strpos(self::ALPHANUMERIC, $content[$i]) !== false) {
                $start = $i;

                while ($i < $length && strpos(self::ALPHANUMERIC, $content[$i]) !== false) {
                    $i++;
                }

                $segment = substr($content, $start, $i - $start);

                if (strlen($segment) >= 2) {
                    $segments[] = new Segment(EncodingMode::Alphanumeric, $segment);
                    continue;
                }

                $i = $start;
            }

            $start = $i;

            while ($i < $length
                && ! ctype_digit($content[$i])
                && (strpos(self::ALPHANUMERIC, $content[$i]) === false || $this->alphanumericRunLength($content, $i) < 2)) {
                $i++;
            }

            $segments[] = new Segment(EncodingMode::Byte, substr($content, $start, $i - $start));
        }

        return $this->mergeAdjacentByteSegments($segments);
    }

    private function alphanumericRunLength(string $content, int $offset): int
    {
        $length = strlen($content);
        $run = 0;

        for ($i = $offset; $i < $length && strpos(self::ALPHANUMERIC, $content[$i]) !== false; $i++) {
            $run++;
        }

        return $run;
    }

    /**
     * @param list<Segment> $segments
     * @return list<Segment>
     */
    private function mergeAdjacentByteSegments(array $segments): array
    {
        $merged = [];

        foreach ($segments as $segment) {
            $last = $merged[array_key_last($merged)] ?? null;

            if ($last !== null
                && $last->mode() === EncodingMode::Byte
                && $segment->mode() === EncodingMode::Byte) {
                $merged[array_key_last($merged)] = new Segment(
                    EncodingMode::Byte,
                    $last->data() . $segment->data(),
                );
                continue;
            }

            $merged[] = $segment;
        }

        return $merged;
    }

    /**
     * Rough bit-length estimate for mode selection (version 1-9 bit widths).
     *
     * @param list<Segment> $segments
     */
    private function bitLength(array $segments): int
    {
        $bits = 0;

        foreach ($segments as $segment) {
            $length = strlen($segment->data());
            $bits += 4;

            $bits += match ($segment->mode()) {
                EncodingMode::Numeric => $length <= 9 ? 10 : 12,
                EncodingMode::Alphanumeric => $length <= 9 ? 9 : 11,
                EncodingMode::Byte => 8,
                default => 16,
            };

            $bits += match ($segment->mode()) {
                EncodingMode::Numeric => (int) (ceil($length / 3) * 10),
                EncodingMode::Alphanumeric => (int) (ceil($length / 2) * 11),
                EncodingMode::Byte => $length * 8,
                default => $length * 8,
            };
        }

        return $bits;
    }
}
