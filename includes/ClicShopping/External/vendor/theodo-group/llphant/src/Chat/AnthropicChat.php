<?php

namespace LLPhant\Chat;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Utils;
use LLPhant\AnthropicConfig;
use LLPhant\Chat\FunctionInfo\FunctionFormatter;
use LLPhant\Chat\FunctionInfo\FunctionInfo;
use LLPhant\Exception\HttpException;
use LLPhant\Utility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class AnthropicChat implements ChatInterface
{
    private const DEFAULT_URL = 'https://api.anthropic.com';

    private const CURRENT_VERSION = '2023-06-01';

    private ?Message $systemMessage = null;

    /** @var array<string, mixed> */
    private array $modelOptions = [];

    public Client $client;

    private readonly string $model;

    private readonly int $maxTokens;

    /** @var FunctionInfo[] */
    private array $tools = [];

    public function __construct(AnthropicConfig $config = new AnthropicConfig())
    {
        $this->modelOptions = $config->modelOptions;
        $this->model = $config->model;
        $this->maxTokens = $config->maxTokens;

        if ($config->client instanceof Client) {
            $this->client = $config->client;
        } else {
            $this->client = new Client([
                'base_uri' => self::DEFAULT_URL,
                'headers' => [
                    'x-api-key' => $config->apiKey ?? getenv('ANTHROPIC_API_KEY'),
                    'Content-Type' => 'application/json',
                    'anthropic-version' => self::CURRENT_VERSION,
                ],
            ]);
        }
    }

    public function generateText(string $prompt): string
    {
        return $this->generateChat([Message::user($prompt)]);
    }

    /** @param Message[] $messages */
    public function generateChat(array $messages): string
    {
        $params = $this->createParams($messages, false);

        $json = $this->getJsonMessagesResponse($params);

        return $json['content'][0]['text'];
    }

    public function generateStreamOfText(string $prompt): StreamInterface
    {
        return $this->generateChatStream([Message::user($prompt)]);
    }

    public function generateChatOrReturnFunctionCalled(array $messages): string|FunctionInfo
    {
        // TODO: Implement generateChatOrReturnFunctionCalled() method.
        throw new \RuntimeException('Not yet implemented');
    }

    public function generateChatStream(array $messages): StreamInterface
    {
        $params = $this->createParams($messages, true);

        $response = $this->sendRequest($params, true);

        return $this->decodeStreamOfChat($response);
    }

    public function generateTextOrReturnFunctionCalled(string $prompt): string|FunctionInfo
    {
        return $this->generateChatOrReturnFunctionCalled([Message::user($prompt)]);
    }

    public function setSystemMessage(string $message): void
    {
        $this->systemMessage = Message::system($message);
    }

    /**
     * @param  FunctionInfo[]  $tools
     */
    public function setTools(array $tools): void
    {
        $this->tools = $tools;
    }

    public function addTool(FunctionInfo $functionInfo): void
    {
        $this->tools[] = $functionInfo;
    }

    /** @param FunctionInfo[] $functions */
    public function setFunctions(array $functions): void
    {
        $this->setTools($functions);
    }

    public function addFunction(FunctionInfo $functionInfo): void
    {
        $this->addTool($functionInfo);
    }

    public function setModelOption(string $option, mixed $value): void
    {
        $this->modelOptions[$option] = $value;
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array<mixed>
     *
     * @throws HttpException
     * @throws \LLPhant\Exception\FormatException
     */
    protected function getJsonMessagesResponse(array $params): array
    {
        $response = $this->sendRequest($params, false);

        $contents = $response->getBody()->getContents();

        return Utility::decodeJson($contents);
    }

    /**
     * @param  array<string, mixed>  $json
     *
     * @throws HttpException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function sendRequest(array $json, bool $stream): ResponseInterface
    {
        $response = $this->client->request('POST', 'v1/messages', ['stream' => $stream, 'json' => $json]);
        $status = $response->getStatusCode();
        if ($status < 200 || $status >= 300) {
            throw new HttpException(
                "HTTP error Anthropic ({$status}): ".$response->getBody()->getContents(),
            );
        }

        return $response;
    }

    /**
     * @param  Message[]  $messages
     * @return array<array<string, mixed>>
     */
    private function createMessagesArray(array $messages): array
    {
        $response = [];

        foreach ($messages as $msg) {
            $response[] = [
                'role' => $msg->role,
                'content' => $msg->content,
            ];
        }

        return $response;
    }

    /**
     * @param  Message[]  $messages
     * @return array<string, mixed>
     **/
    private function createParams(array $messages, bool $stream): array
    {
        $params = [
            ...$this->modelOptions,
            'model' => $this->model,
            'messages' => $this->createMessagesArray($messages),
            'tools' => FunctionFormatter::formatFunctionsToOpenAI($this->tools),
            'max_tokens' => $this->maxTokens,
            'stream' => $stream,
        ];

        if ($this->systemMessage instanceof Message) {
            $params['system'] = $this->systemMessage->content;
        }

        return $params;
    }

    private function decodeStreamOfChat(ResponseInterface $response): StreamInterface
    {
        $streamResponse = new AnthropicStreamResponse($response);

        return Utils::streamFor($streamResponse->getIterator());
    }
}
