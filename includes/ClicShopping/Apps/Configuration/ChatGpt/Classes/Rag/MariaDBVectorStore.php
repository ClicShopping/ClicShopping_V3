<?php

namespace ClicShopping\Apps\Configuration\ChatGpt\Classes\Rag;

use Doctrine\DBAL\ParameterType;
use LLPhant\Embeddings\Document;
use LLPhant\Embeddings\EmbeddingGenerator\EmbeddingGeneratorInterface;
use LLPhant\Embeddings\VectorStores\VectorStoreBase;
use Doctrine\DBAL\Connection;
use ClicShopping\Apps\Configuration\ChatGpt\Classes\Rag\DoctrineOrm;

/**
 * MariaDBVectorStore Class
 *
 * A vector store implementation using MariaDB for storing and retrieving document embeddings.
 * This class extends VectorStoreBase and provides functionality for managing document
 * embeddings in a MariaDB database with vector similarity search capabilities.
 *
 * Features:
 * - Document storage with vector embeddings
 * - Similarity search using vector operations
 * - Document metadata management
 * - Support for different entity types (products, categories, page manager)
 * - Document CRUD operations
 *
 * Requirements:
 * - MariaDB 11.8.0 or higher with vector support
 * - Doctrine ORM configuration
 * - Valid embedding generator implementation
 *
 * @package ClicShopping\Apps\Configuration\ChatGpt\Classes\Rag
 */
class MariaDBVectorStore extends VectorStoreBase
{
  private Connection $connection;
  private string $tableName;
  private EmbeddingGeneratorInterface $embeddingGenerator;

  /**
   * Constructor for MariaDBVectorStore
   *
   * Initializes the vector store with a connection to MariaDB and creates
   * the necessary table structure if it doesn't exist.
   *
   * @param EmbeddingGeneratorInterface $embeddingGenerator The embedding generator to use
   * @param string $tableName Optional custom table name (defaults to 'rag_embeddings')
   * @throws \Exception If database connection or table creation fails
   */
  public function __construct(EmbeddingGeneratorInterface $embeddingGenerator, string $tableName = 'rag_embeddings')
  {
    $this->embeddingGenerator = $embeddingGenerator;
    $this->tableName = $tableName;

    // Récupération de la connexion Doctrine
    $entityManager = DoctrineOrm::getEntityManager();
    $this->connection = $entityManager->getConnection();

    // Vérification et création de la structure de la base de données si nécessaire
    DoctrineOrm::createTableStructure($this->tableName);
  }

  /**
   * Adds a single document to the vector store
   *
   * Processes the document content, generates embeddings, and stores it in the database
   * along with its metadata and entity information.
   *
   * @param Document $document The document to add, containing content and metadata
   * @throws \Exception If document addition fails
   */
  public function addDocument(Document $document): void
  {
    // Génération de l'embedding pour le document
    $embedding = $this->embeddingGenerator->embedText($document->content);

    // Préparation des métadonnées
    $type = $document->sourceType ?? null;
    $sourcetype = $document->sourceType ?? 'manual';
    $sourcename = $document->sourceName ?? 'manual';
    $chunknumber = $document->chunkNumber ?? 128;
    $language_id = $document->language_id ?? 1;
    $date_modified = date('Y-m-d H:i:s');

    // Extraction des informations d'entité
    $entity_id = isset($document->metadata['entity_id']) ? $document->metadata['entity_id'] : null;

    // Rétrocompatibilité avec les anciens champs
    if ($type === null && isset($document->metadata['entity_id']) && $document->metadata['entity_id'] !== null) {
      $type = 'page_manager';
      $entity_id = $document->metadata['entity_id'];
    } elseif ($type === null && isset($document->metadata['entity_id']) && $document->metadata['entity_id'] !== null) {
      $type = 'category';
      $entity_id = $document->metadata['entity_id'];
    } elseif ($type === null && isset($document->metadata['entity_id']) && $document->metadata['entity_id'] !== null) {
      $type = 'products';
      $entity_id = $document->metadata['entity_id'];
    }

    // Conversion de l'embedding en JSON pour stockage
    $embeddingJson = json_encode($embedding);

    // Insertion dans la base de données
    $this->connection->executeStatement(
      "INSERT INTO {$this->tableName} 
            (content, type, sourcetype, sourcename, embedding, chunknumber, date_modified, entity_id, language_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
      [
        $document->content,
        $type,
        $sourcetype,
        $sourcename,
        $embeddingJson,
        $chunknumber,
        $date_modified,
        $entity_id,
        $language_id
      ]
    );
  }

  /**
   * Adds multiple documents to the vector store
   *
   * Processes and stores multiple documents in sequence.
   *
   * @param array $documents Array of Document objects to add
   * @throws \Exception If any document addition fails
   */
  public function addDocuments(array $documents): void
  {
    foreach ($documents as $document) {
      $this->addDocument($document);
    }
  }

  /**
   * Performs a similarity search for documents
   *
   * Searches for documents similar to the provided query using vector similarity.
   * Supports both text queries and direct embedding vectors.
   *
   * @param mixed $query Search query (string) or embedding vector (array)
   * @param int $k Maximum number of results to return
   * @param mixed $minScore Minimum similarity score (0-1) for results
   * @param callable|null $filter Optional callback function for filtering results
   * @return iterable Collection of matching Document objects with similarity scores
   * @throws \Exception If search operation fails
   */
  public function similaritySearch(mixed $query, int $k = 4, mixed $minScore = 0.0, ?callable $filter = null): iterable
  {
    try {
      // Déterminer si la requête est déjà un embedding ou un texte à convertir
      $embedding = is_array($query) ? $query : $this->embeddingGenerator->embedText($query);

      // Conversion de l'embedding en JSON pour la requête
      $embeddingJson = json_encode($embedding);

      // Construction de la requête SQL avec filtrage optionnel
      $sql = "SELECT *, 
                    (embedding <=> ?) AS distance 
                FROM {$this->tableName} 
                WHERE 1=1 
                ORDER BY distance ASC 
                LIMIT ?";

      // Préparation des paramètres
      $params = [$embeddingJson, $k];
      $types = [ParameterType::STRING, ParameterType::INTEGER];

      // Exécution de la requête
      $stmt = $this->connection->executeQuery($sql, $params, $types);
      $results = $stmt->fetchAllAssociative();

      // Conversion des résultats en objets Document
      $documents = [];
      foreach ($results as $result) {
        // Vérification du score minimum
        $similarity = 1 - $result['distance'];
        if ($similarity < $minScore) {
          continue;
        }

        // Création du document
        $document = new Document();
        $document->id = $result['id'];
        $document->content = $result['content'];
        $document->sourceType = $result['sourcetype'] ?? 'manual';
        $document->sourceName = $result['sourcename'] ?? 'manual';
        $document->chunkNumber = $result['chunknumber'] ?? 128;
        $document->type = $result['type']  ?? null;
        $document->language_id = $result['language_id'] ?? 1;

        // Ajout des métadonnées
        $document->metadata = [
          'id' => $result['id'],
          'type' => $result['type'] ?? null,
          'date_modified' => $result['date_modified'] ?? null,
          'entity_id' => $result['entity_id'] ?? null,
          'language_id' => $result['language_id'] ?? 1,
          'score' => $similarity,
          'table_name' => $this->tableName,
          'distance' => $result['distance']
        ];

        // Application du filtre personnalisé si fourni
        if ($filter !== null && !$filter($document->metadata)) {
          continue;
        }

        $documents[] = $document;
      }

      return $documents;
    } catch (\Exception $e) {
      if (CLICSHOPPING_APP_CHATGPT_CH_DEBUG_RAG_MANAGER == 'True') {
        error_log('Error while searching in the table ' . $this->tableName . ' : ' . $e->getMessage());
      }
      return [];
    }
  }

  /**
   * Deletes a document from the vector store
   *
   * Removes a document and its embeddings from the database.
   *
   * @param int $id ID of the document to delete
   * @return bool True if successful, false if deletion fails
   */
  public function deleteDocument(int $id): bool
  {
    try {
      $this->connection->executeStatement(
        "DELETE FROM {$this->tableName} WHERE id = ?",
        [$id]
      );
      return true;
    } catch (\Exception $e) {
      if (CLICSHOPPING_APP_CHATGPT_CH_DEBUG_RAG_MANAGER == 'True') {
        error_log('Error while deleting the document: ' . $e->getMessage());
      }
      return false;
    }
  }

  /**
   * Updates an existing document in the vector store
   *
   * Updates document content, regenerates embeddings, and updates metadata.
   * Maintains entity relationships and historical data.
   *
   * @param int $id ID of the document to update
   * @param string $content New content for the document
   * @param array $metadata Updated metadata for the document
   * @return bool True if successful, false if update fails
   */
  public function updateDocument(int $id, string $content, array $metadata = []): bool
  {
    try {
      // Génération du nouvel embedding
      $embedding = $this->embeddingGenerator->embedText($content);
      $embeddingJson = json_encode($embedding);

      // Préparation des métadonnées
      $type = $metadata['type'] ?? null;
      $sourcetype = $metadata['sourcetype'] ?? 'manual';
      $sourcename = $metadata['sourcename'] ?? 'manual';
      $chunknumber = $metadata['chunknumber'] ?? 128;
      $language_id = $metadata['language_id'] ?? 1;
      $date_modified = date('Y-m-d H:i:s');

      // Extraction des informations d'entité
      $entity_id = isset($metadata['entity_id']) ? $metadata['entity_id'] : null;

      // Rétrocompatibilité avec les anciens champs
      if ($type === null && isset($metadata['entity_id']) && $metadata['entity_id'] !== null) {
        $type = 'page_manager';
        $entity_id = $metadata['entity_id'];
      } elseif ($type === null && isset($metadata['categories_id']) && $metadata['entity_id'] !== null) {
        $type = 'category';
        $entity_id = $metadata['entity_id'];
      } elseif ($type === null && isset($metadata['entity_id']) && $metadata['entity_id'] !== null) {
        $type = 'products';
        $entity_id = $metadata['entity_id'];
      }

      // Mise à jour dans la base de données
      $this->connection->executeStatement(
        "UPDATE {$this->tableName} 
                SET content = ?, type = ?, sourcetype = ?, sourcename = ?, 
                embedding = ?, chunknumber = ?, date_modified = ?, 
                entity_type = ?, entity_id = ?,  language_id = ?,  
                WHERE id = ?",
        [
          $content,
          $type,
          $sourcetype,
          $sourcename,
          $embeddingJson,
          $chunknumber,
          $date_modified,
          $entity_id,
          $language_id,
          $id
        ]
      );
      return true;
    } catch (\Exception $e) {
      if (CLICSHOPPING_APP_CHATGPT_CH_DEBUG_RAG_MANAGER == 'True') {
        error_log('Error while updating the document: ' . $e->getMessage());
      }
      return false;
    }
  }
}
