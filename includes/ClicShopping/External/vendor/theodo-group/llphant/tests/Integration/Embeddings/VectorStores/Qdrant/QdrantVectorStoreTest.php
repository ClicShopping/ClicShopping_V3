<?php

declare(strict_types=1);

namespace Tests\Integration\Embeddings\VectorStores\Qdrant;

use LLPhant\Embeddings\DataReader\FileDataReader;
use LLPhant\Embeddings\Document;
use LLPhant\Embeddings\DocumentSplitter\DocumentSplitter;
use LLPhant\Embeddings\EmbeddingFormatter\EmbeddingFormatter;
use LLPhant\Embeddings\EmbeddingGenerator\OpenAI\OpenAIADA002EmbeddingGenerator;
use LLPhant\Embeddings\VectorStores\Qdrant\QdrantVectorStore;
use Qdrant\Config;
use Qdrant\Models\Filter\Condition\MatchString;
use Qdrant\Models\Request\VectorParams;

it('tests a full embedding flow with Qdrant', function () {
    $filePath = __DIR__.'/../PlacesTextFiles';
    $reader = new FileDataReader($filePath, Document::class);
    $documents = $reader->getDocuments();
    $splittedDocuments = DocumentSplitter::splitDocuments($documents, 100, "\n");
    $formattedDocuments = EmbeddingFormatter::formatEmbeddings($splittedDocuments);

    $embeddingGenerator = new OpenAIADA002EmbeddingGenerator();
    $embededDocuments = $embeddingGenerator->embedDocuments($formattedDocuments);

    $host = getenv('QDRANT_HOST');
    $apiKey = getenv('QDRANT_API_KEY');
    $config = new Config($host);
    $config->setApiKey($apiKey);

    $collectionName = 'places2';
    $vectorStore = new QdrantVectorStore($config, $collectionName);
    $vectorStore->createCollectionIfDoesNotExist($collectionName, $embeddingGenerator->getEmbeddingLength());
    $vectorStore->addDocuments($embededDocuments);
    $embedding = $embeddingGenerator->embedText('France the country');
    /** @var Document[] $result */
    $result = $vectorStore->similaritySearch($embedding, 2);

    // We check that the search return the correct entities in the right order
    expect(explode(' ', $result[0]->content)[0])->toBe('France');

    $condition = new MatchString('sourceName', 'paris.txt');

    $filter['must'] = [$condition];

    /** @var Document[] $searchResult2 */
    $searchResult2 = $vectorStore->similaritySearch($embedding, 2, $filter);
    expect(explode(' ', $searchResult2[0]->content)[0])->toBe('Paris');
});

it('tests a full embedding flow with Qdrant and euclid distance and null vector name', function () {
    $filePath = __DIR__.'/../PlacesTextFiles';
    $reader = new FileDataReader($filePath, Document::class);
    $documents = $reader->getDocuments();
    $splittedDocuments = DocumentSplitter::splitDocuments($documents, 100, "\n");
    $formattedDocuments = EmbeddingFormatter::formatEmbeddings($splittedDocuments);

    $embeddingGenerator = new OpenAIADA002EmbeddingGenerator();
    $embededDocuments = $embeddingGenerator->embedDocuments($formattedDocuments);

    $host = getenv('QDRANT_HOST');
    $apiKey = getenv('QDRANT_API_KEY');
    $config = new Config($host);
    $config->setApiKey($apiKey);

    $collectionName = 'places3';
    $vectorStore = new QdrantVectorStore($config, $collectionName, null, VectorParams::DISTANCE_EUCLID);
    $vectorStore->createCollectionIfDoesNotExist($collectionName, $embeddingGenerator->getEmbeddingLength());
    $vectorStore->addDocuments($embededDocuments);
    $embedding = $embeddingGenerator->embedText('France the country');
    /** @var Document[] $result */
    $result = $vectorStore->similaritySearch($embedding, 2);

    // We check that the search return the correct entities in the right order
    expect(explode(' ', $result[0]->content)[0])->toBe('France');

    $condition = new MatchString('sourceName', 'paris.txt');

    $filter['must'] = [$condition];

    /** @var Document[] $searchResult2 */
    $searchResult2 = $vectorStore->similaritySearch($embedding, 2, $filter);
    expect(explode(' ', $searchResult2[0]->content)[0])->toBe('Paris');
});
