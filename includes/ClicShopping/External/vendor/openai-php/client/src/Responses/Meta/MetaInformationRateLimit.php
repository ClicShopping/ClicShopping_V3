<?php

namespace OpenAI\Responses\Meta;

final class MetaInformationRateLimit
{
    private function __construct(
        public readonly  int|null $limit,
        public readonly int $remaining,
        public readonly ?string $reset,
    ) {}

    /**
     * @param  array{limit:  int|null, remaining: int, reset: ?string}  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            $attributes['limit'],
            $attributes['remaining'],
            $attributes['reset'],
        );
    }
}
