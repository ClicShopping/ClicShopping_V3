<?php

declare(strict_types=1);

namespace LLPhant\Embeddings\VectorStores\Doctrine;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use LLPhant\Embeddings\Document;
use LLPhant\Embeddings\VectorStores\VectorStoreBase;

final class DoctrineVectorStore extends VectorStoreBase
{
    /**
     * @template T of DoctrineEmbeddingEntityBase
     *
     * @param  class-string<T>  $entityClassName
     *
     * @throws Exception
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        public readonly string $entityClassName
    ) {
        if (! interface_exists(EntityManagerInterface::class)) {
            throw new \RuntimeException('To use this functionality, you must install the `doctrine/orm` package: `composer require doctrine/orm`.');
        }

        $conn = $entityManager->getConnection();
        $registeredTypes = Type::getTypesMap();
        if (! array_key_exists(VectorType::VECTOR, $registeredTypes)) {
            Type::addType(VectorType::VECTOR, VectorType::class);
            $conn->getDatabasePlatform()->registerDoctrineTypeMapping('vector', VectorType::VECTOR);
        }
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function addDocument(Document $document): void
    {
        $this->persistDocument($document);
        $this->entityManager->flush();
    }

    /**
     * @param  Document[]  $documents
     *
     * @throws \Exception
     */
    public function addDocuments(array $documents): void
    {
        if ($documents === []) {
            return;
        }
        foreach ($documents as $document) {
            $this->persistDocument($document);
        }

        $this->entityManager->flush();
    }

    /**
     * @param  float[]  $embedding  The embedding used to search closest neighbors
     * @param  array<string, string|int>  $additionalArguments
     * @return DoctrineEmbeddingEntityBase[]
     */
    public function similaritySearch(array $embedding, int $k = 4, array $additionalArguments = []): array
    {
        $this->entityManager->getConfiguration()->addCustomStringFunction('L2_DISTANCE', PgVectorL2OperatorDql::class);

        $repository = $this->entityManager->getRepository($this->entityClassName);
        $qb = $repository
            ->createQueryBuilder('e')
            ->orderBy('L2_DISTANCE(e.embedding, :embeddingString)', 'ASC')
            ->setParameter('embeddingString', VectorUtils::getVectorAsString($embedding))
            ->setMaxResults($k);

        foreach ($additionalArguments as $key => $value) {
            $paramName = 'where_'.$key;
            $qb
                ->andWhere(sprintf('e.%s = :%s', $key, $paramName))
                ->setParameter($paramName, $value);
        }

        /** @var DoctrineEmbeddingEntityBase[] */
        return $qb->getQuery()->getResult();
    }

    /**
     * @throws ORMException
     * @throws Exception
     */
    private function persistDocument(Document $document): void
    {
        if (empty($document->embedding)) {
            throw new Exception('Trying to save a document in a vectorStore without embedding');
        }

        if (! $document instanceof DoctrineEmbeddingEntityBase) {
            throw new Exception('Document needs to be an instance of DoctrineEmbeddingEntityBase');
        }

        $this->entityManager->persist($document);
    }
}
