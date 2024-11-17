<?php

declare(strict_types=1);

namespace Tests\Integration\Embeddings\VectorStores\Doctrine;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use LLPhant\Embeddings\DataReader\FileDataReader;
use LLPhant\Embeddings\DocumentSplitter\DocumentSplitter;
use LLPhant\Embeddings\EmbeddingFormatter\EmbeddingFormatter;
use LLPhant\Embeddings\EmbeddingGenerator\OpenAI\OpenAI3LargeEmbeddingGenerator;
use LLPhant\Embeddings\VectorStores\Doctrine\DoctrineVectorStore;

it('creates two entity with their embeddings and perform a similarity search', function () {
    $config = ORMSetup::createAttributeMetadataConfiguration(
        [__DIR__.'/src'],
        true
    );

    $connectionParams = [
        'dbname' => 'postgres',
        'user' => 'myuser',
        'password' => '!ChangeMe!',
        'host' => getenv('PGVECTOR_HOST') ?: 'localhost',
        'driver' => 'pdo_pgsql',
    ];

    $connection = DriverManager::getConnection($connectionParams);
    $connection->executeQuery('TRUNCATE TABLE test_place');
    $entityManager = new EntityManager($connection, $config);

    $vectorStore = new DoctrineVectorStore($entityManager, PlaceEntity::class);
    $embeddingGenerator = new OpenAI3LargeEmbeddingGenerator();

    $paris = new PlaceEntity();
    $paris->content = 'I live in Paris';
    $paris->type = 'city';
    $france = new PlaceEntity();
    $france->content = 'I live in France';
    $france->type = 'country';

    $embededDocuments = $embeddingGenerator->embedDocuments([$paris, $france]);

    $vectorStore->addDocuments($embededDocuments);

    $embedding = $embeddingGenerator->embedText('I live in Asia');
    /** @var PlaceEntity[] $result */
    $result = $vectorStore->similaritySearch($embedding, 2, ['type' => 'city']);

    // We check that the search return the correct entities in the right order
    expect($result[0]->content)->toBe('I live in Paris');
});

it('tests a full embedding flow with Doctrine', function () {
    $config = ORMSetup::createAttributeMetadataConfiguration(
        [__DIR__.'/src'],
        true
    );

    $connectionParams = [
        'dbname' => 'postgres',
        'user' => 'myuser',
        'password' => '!ChangeMe!',
        'host' => getenv('PGVECTOR_HOST') ?: 'localhost',
        'driver' => 'pdo_pgsql',
    ];

    $connection = DriverManager::getConnection($connectionParams);
    $connection->executeQuery('TRUNCATE TABLE test_place');
    $entityManager = new EntityManager($connection, $config);

    $filePath = __DIR__.'/../PlacesTextFiles';
    $reader = new FileDataReader($filePath, PlaceEntity::class);
    $documents = $reader->getDocuments();
    $splittedDocuments = DocumentSplitter::splitDocuments($documents, 100, "\n");
    $formattedDocuments = EmbeddingFormatter::formatEmbeddings($splittedDocuments);

    $embeddingGenerator = new OpenAI3LargeEmbeddingGenerator();
    $embededDocuments = $embeddingGenerator->embedDocuments($formattedDocuments);

    $vectorStore = new DoctrineVectorStore($entityManager, PlaceEntity::class);
    $vectorStore->addDocuments($embededDocuments);

    $embedding = $embeddingGenerator->embedText('France the country');
    /** @var PlaceEntity[] $result */
    $result = $vectorStore->similaritySearch($embedding, 2);

    // We check that the search return the correct entities in the right order
    expect(explode(' ', $result[0]->content)[0])->toBe('France');
});

it('can filter documents by chunk number', function () {
    $config = ORMSetup::createAttributeMetadataConfiguration(
        [__DIR__.'/src'],
        true
    );

    $connectionParams = [
        'dbname' => 'postgres',
        'user' => 'myuser',
        'password' => '!ChangeMe!',
        'host' => getenv('PGVECTOR_HOST') ?: 'localhost',
        'driver' => 'pdo_pgsql',
    ];

    $connection = DriverManager::getConnection($connectionParams);
    $connection->executeQuery('TRUNCATE TABLE test_doc');
    $entityManager = new EntityManager($connection, $config);

    $vectorStore = new DoctrineVectorStore($entityManager, SampleDocEntity::class);
    $vectorStore->addDocuments([
        SampleDocEntity::createDocument('catullo', 'basia', 'Vivamus mea Lesbia, atque amemus,', 0),
        SampleDocEntity::createDocument('catullo', 'basia', 'rumoresque senum severiorum', 1),
        SampleDocEntity::createDocument('catullo', 'basia', 'omnes unius aestimemus assis!', 2),
        SampleDocEntity::createDocument('catullo', 'basia', 'soles occidere et redire possunt:', 3),
        SampleDocEntity::createDocument('catullo', 'basia', 'nobis cum semel occidit brevis lux,', 4),
        SampleDocEntity::createDocument('catullo', 'basia', 'nox est perpetua una dormienda.', 5),
        SampleDocEntity::createDocument('catullo', 'odi', 'Odi et amo. Quare id faciam, fortasse requiris.', 0),
        SampleDocEntity::createDocument('catullo', 'odi', 'Nescio, sed fieri sentio et excrucior', 1),
    ]);

    $retrievedDocuments = $vectorStore->fetchDocumentsByChunkRange('catullo', 'basia', 3, 5);
    $retrievedTexts = \array_map(fn ($x) => $x->content, \iterator_to_array($retrievedDocuments));

    expect($retrievedTexts)->toMatchArray(
        [
            'soles occidere et redire possunt:',
            'nobis cum semel occidit brevis lux,',
            'nox est perpetua una dormienda.',
        ]
    );
});
