<?php

declare(strict_types=1);

namespace LLPhant;

use GuzzleHttp\Client;

class AnthropicConfig
{
    final public const CLAUDE_3_HAIKU = 'claude-3-haiku-20240307';

    final public const CLAUDE_3_5_SONNET = 'claude-3-5-sonnet-20240620';

    final public const CLAUDE_3_SONNET = 'claude-3-sonnet-20240229';

    final public const CLAUDE_3_OPUS = 'claude-3-opus-20240229';

    /**
     * @param  array<string, mixed>  $modelOptions
     */
    public function __construct(
        public readonly string $model = self::CLAUDE_3_HAIKU,
        public readonly int $maxTokens = 1024,
        public readonly array $modelOptions = [],
        public readonly ?string $apiKey = null,
        public readonly ?Client $client = null, )
    {
    }
}
