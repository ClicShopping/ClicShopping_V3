<?php

namespace LLPhant\Image;

use Exception;
use LLPhant\Image\Enums\OpenAIImageModel;
use LLPhant\Image\Enums\OpenAIImageSize;
use LLPhant\Image\Enums\OpenAIImageStyle;
use LLPhant\OpenAIConfig;
use OpenAI;
use OpenAI\Contracts\ClientContract;
use OpenAI\Responses\Images\CreateResponse;

use function getenv;

class OpenAIImage implements ImageInterface
{
    private readonly ClientContract $client;

    public string $model;

    /** @var array<string, mixed> */
    private array $modelOptions = [];

    public function __construct(?OpenAIConfig $config = null)
    {
        if ($config instanceof OpenAIConfig && $config->client instanceof ClientContract) {
            $this->client = $config->client;
        } else {
            $apiKey = $config->apiKey ?? getenv('OPENAI_API_KEY');
            if (! $apiKey) {
                throw new Exception('You have to provide a OPENAI_API_KEY env var to request OpenAI .');
            }

            $this->client = OpenAI::factory()
                ->withApiKey($apiKey)
                ->withHttpHeader('OpenAI-Beta', 'assistants=v2')
                ->withBaseUri($config->url ?? (getenv('OPENAI_BASE_URL') ?: 'api.openai.com/v1'))
                ->make();
        }
        $this->model = $config->model ?? OpenAIImageModel::DallE3->value;
        $this->modelOptions = $config->modelOptions ?? [];
    }

    public function generateImage(string $prompt, OpenAIImageStyle $style = OpenAIImageStyle::Vivid): Image
    {
        $answer = $this->client->images()->create([
            'prompt' => $prompt,
            'model' => $this->model,
            'n' => 1,
            'size' => $this->modelOptions['size'] ?? OpenAIImageSize::size_1024x1024->value,
            'style' => $style->value,
        ]);

        return $this->responseToImage($answer);
    }

    public function setModelOption(string $option, mixed $value): void
    {
        $this->modelOptions[$option] = $value;
    }

    private function responseToImage(CreateResponse $answer): Image
    {
        $dataArray = $answer->toArray();

        /** @var array{url?: string, b64_json?: string, revised_prompt?: string} $data */
        $data = $dataArray['data'][0];

        $image = new Image();
        $image->url = $data['url'] ?? null;
        $image->revisedPrompt = $data['revised_prompt'] ?? null;

        return $image;
    }
}
