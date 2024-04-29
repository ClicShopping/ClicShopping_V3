<?php

namespace LLPhant\Embeddings\VectorStores\Milvus;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;

class MilvusClient
{
    final public const API_VERSION = 'v1';

    public ClientInterface $client;

    public function __construct(
        public string $host,
        public string $port,
        public string $user,
        public string $password,
        public string $database = 'default',
        public string $apiVersion = self::API_VERSION
    ) {
        $this->client = new Client([
            'base_uri' => "{$host}:{$port}/{$apiVersion}/",
        ]);
    }

    /**
     * @return array{code: int, data: mixed}
     */
    public function listCollections(): array
    {
        $path = 'vector/collections';

        return $this->sendRequest('GET', $path);
    }

    /**
     * @return array{code: int, data: mixed}
     */
    public function dropCollection(string $collectionName): array
    {
        $path = 'vector/collections/drop';
        $body = [
            'collectionName' => $collectionName,
        ];

        return $this->sendRequest('POST', $path, $body);
    }

    /**
     * @return array{code: int, data: mixed}
     */
    public function createCollection(
        string $collectionName,
        int $dimension,
        string $metricType,
        string $primaryField,
        string $vectorField
    ): array {
        $path = 'vector/collections/create';
        $body = [
            'dbName' => $this->database,
            'collectionName' => $collectionName,
            'dimension' => $dimension,
            'metricType' => $metricType,
            'primaryField' => $primaryField,
            'vectorField' => $vectorField,
        ];

        return $this->sendRequest('POST', $path, $body);
    }

    /**
     * @return array{code: int, data: mixed}
     *
     * @phpstan-ignore-next-line
     */
    public function insertData(string $collectionName, array $data): array
    {
        $path = 'vector/insert';
        $body = [
            'collectionName' => $collectionName,
            'data' => $data,
        ];

        return $this->sendRequest('POST', $path, $body);
    }

    /**
     * @return array{code: int, data: mixed}
     *
     * @phpstan-ignore-next-line
     */
    public function searchVector(
        string $collectionName,
        array $vector,
        int $limit,
        ?string $filter = null,
        ?array $outputFields = null
    ): array {
        $path = 'vector/search';
        $body = [
            'collectionName' => $collectionName,
            'vector' => $vector,
            'limit' => $limit,
        ];
        if ($outputFields !== null) {
            $body['outputFields'] = $outputFields;
        }
        if ($filter !== null) {
            $body['filter'] = $filter;
        }

        return $this->sendRequest('POST', $path, $body);
    }

    /**
     * @return array{code: int, data: mixed}
     *
     * @phpstan-ignore-next-line
     */
    public function getEntity(string $collectionName, string $id, ?array $outputFields = null): array
    {
        $path = 'vector/get';
        $body = [
            'collectionName' => $collectionName,
            'id' => $id,
        ];
        if ($outputFields !== null) {
            $body['outputFields'] = $outputFields;
        }

        return $this->sendRequest('POST', $path, $body);
    }

    /**
     * @return array{code: int, data: mixed}
     *
     * @phpstan-ignore-next-line
     */
    protected function sendRequest(string $method, string $path, array $body = []): array
    {
        $options = [
            'headers' => [
                'Authorization' => 'Bearer '.$this->user.':'.$this->password,
                'Accept: application/json',
                'Content-Type: application/json',
            ],
            'json' => $body,
        ];

        try {
            $response = $this->client->request($method, $path, $options);

            /** @var array{code: int, data: mixed} */
            return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $response->getBody()->getContents();
            throw new \Exception('Milvus API error: {$errorBody}', $e->getCode(), $e);
        }
    }
}
