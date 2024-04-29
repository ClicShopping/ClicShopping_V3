<?php

namespace LLPhant\Chat;

use Exception;
use LLPhant\Chat\Enums\MistralAIChatModel;
use LLPhant\OpenAIConfig;
use OpenAI\Client;
use OpenAI\Factory;

use function getenv;

class MistralAIChat extends OpenAIChat
{
    private const BASE_URL = 'api.mistral.ai/v1';

    public function __construct(?OpenAIConfig $config = null)
    {
        if (! $config instanceof OpenAIConfig) {
            $config = new OpenAIConfig();
        }

        if (! $config->client instanceof Client) {
            $apiKey = $config->apiKey ?? getenv('MISTRAL_API_KEY');
            if (! $apiKey) {
                throw new Exception('You have to provide a MISTRAL_API_KEY env var to request Mistral AI.');
            }

            $clientFactory = new Factory();
            $config->client = $clientFactory
                ->withApiKey($apiKey)
                ->withBaseUri(self::BASE_URL)
                ->make();
        }

        $config->model ??= MistralAIChatModel::large->getModelName();
        parent::__construct($config);
    }
}
