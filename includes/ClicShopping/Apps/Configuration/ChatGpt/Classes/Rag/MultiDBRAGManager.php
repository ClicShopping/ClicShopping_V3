<?php

namespace ClicShopping\Apps\Configuration\ChatGpt\Classes\Rag;


use ClicShopping\Apps\Configuration\ChatGpt\ChatGpt;
use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\Gpt;
use ClicShopping\Apps\Configuration\ChatGpt\Classes\Rag\DoctrineOrm;
use ClicShopping\Apps\Configuration\ChatGpt\Classes\Rag\MariaDBVectorStore;

use LLPhant\Embeddings\Document;
use LLPhant\Embeddings\EmbeddingGenerator\EmbeddingGeneratorInterface;

/**
 * MultiDBRAGManager Class
 *
 * This class manages multiple vector databases for Retrieval-Augmented Generation (RAG).
 * It provides functionality for document management, similarity search, and question answering
 * across multiple vector stores using OpenAI embeddings.
 *
 * Key features:
 * - Multiple vector store management
 * - Document embedding and storage
 * - Similarity search across multiple databases
 * - Question answering using RAG
 * - Support for different languages and entity types
 *
 * @package ClicShopping\Apps\Configuration\ChatGpt\Classes\Rag
 */
class MultiDBRAGManager
{
  private $app;
  private $embeddingGenerator;
  private array $vectorStores = [];

  private string $systemMessageTemplate;

  /**
   * Constructor for MultiDBRAGManager
   * Initializes the RAG system with specified model and tables
   *
   * @param string|null $model OpenAI model to use (null for default configuration)
   * @param array $tableNames List of table names to use (empty for all embedding tables)
   * @param array $modelOptions Additional model options (temperature, etc.)
   * @throws \Exception If initialization fails
   */
  public function __construct(?string $model = null, array $tableNames = [], array $modelOptions = [])
  {
    // Initialisation de l'application ChatGpt via Registry
    if (!Registry::exists('ChatGpt')) {
      Registry::set('ChatGpt', new ChatGpt());
    }

    $this->app = Registry::get('ChatGpt');
    $this->systemMessageTemplate = CLICSHOPPING::getDef('text_rag_system_message_template');

    // Préparation des paramètres pour getOpenAiGpt
    $parameters = null;
    if (!is_null($model) || !empty($modelOptions)) {
      $parameters = $modelOptions;
      if (!is_null($model)) {
        $parameters['model'] = $model;
      } elseif (defined('CLICSHOPPING_APP_CHATGPT_CH_MODEL')) {
        $parameters['model'] = CLICSHOPPING_APP_CHATGPT_CH_MODEL;
      }
    }

    // Initialisation de l'environnement OpenAI via la classe Gpt existante
    Gpt::getOpenAiGpt($parameters);

    // Création d'un adaptateur pour utiliser gptOpenAiEmbeddings comme générateur d'embeddings
    $this->embeddingGenerator = new class(Gpt::class) implements EmbeddingGeneratorInterface {
      private $gptClass;

      public function __construct(string $gptClass) {
        $this->gptClass = $gptClass;
      }

      public function embedText(string $text): array {
        return call_user_func([$this->gptClass, 'gptOpenAiEmbeddings'], $text);
      }

      public function embedDocument(Document $document): Document {
        $document->embedding = $this->embedText($document->content);
        return $document;
      }

      public function embedDocuments(array $documents): array {
        $results = [];
        foreach ($documents as $document) {
          $results[] = $this->embedDocument($document);
        }
        return $results;
      }

      public function getEmbeddingLength(): int {
        return 1536; // Valeur par défaut pour OpenAI
      }
    };

    // Si aucune table n'est spécifiée, récupérer toutes les tables d'embedding disponibles
    if (empty($tableNames)) {
      try {
        $tableNames = DoctrineOrm::getEmbeddingTables();
        if (CLICSHOPPING_APP_CHATGPT_CH_DEBUG_RAG_MANAGER == 'True') {
          error_log("Embedding tables found: " . implode(", ", $tableNames));
        }
      } catch (\Exception $e) {
        if (CLICSHOPPING_APP_CHATGPT_CH_DEBUG_RAG_MANAGER == 'True') {
          error_log("Error while retrieving the embedding tables: " . $e->getMessage());
        }
        $tableNames = [];
      }
    }

    // Initialisation des vector stores pour chaque table
    foreach ($tableNames as $tableName) {
      try {
        $this->vectorStores[$tableName] = new MariaDBVectorStore($this->embeddingGenerator, $tableName);
        if (CLICSHOPPING_APP_CHATGPT_CH_DEBUG_RAG_MANAGER == 'True') {
          error_log("Vector store initialized for the table: " . $tableName);
        }
      } catch (\Exception $e) {
        if (CLICSHOPPING_APP_CHATGPT_CH_DEBUG_RAG_MANAGER == 'True') {
          error_log("Error while initializing the vector store for the table {$tableName}: " . $e->getMessage());
        }
      }
    }
  }

  /**
   * Adds a document to the specified vector store
   *
   * @param string $content Document content to add
   * @param string $tableName Name of the table to store the document
   * @param string $type Document type
   * @param string $sourceType Source type of the document
   * @param string $sourceName Name of the source
   * @param string|null $entityType Entity type (page, category, product, etc.)
   * @param int|null $entityId Entity ID
   * @param int|null $languageId Language ID
   * @return bool True if successful, false otherwise
   */
  public function addDocument(
    string $content,
    string $tableName,
    string $type = 'text',
    string $sourceType = 'manual',
    string $sourceName = 'manual',
    ?string $entityType = null,
    ?int $entityId = null,
    ?int $languageId = null
  ): bool {
    try {
      // Vérifier si la table existe dans les vector stores
      if (!isset($this->vectorStores[$tableName])) {
        // Si la table n'existe pas, vérifier si elle existe dans la base de données
        if (!DoctrineOrm::checkTableStructure($tableName)) {
          // Si la table n'existe pas dans la base de données, la créer
          if (!DoctrineOrm::createTableStructure($tableName)) {
            throw new \Exception("Unable to create the table {$tableName}");
          }
        }

        // Ajouter la table aux vector stores
        $this->vectorStores[$tableName] = new MariaDBVectorStore($this->embeddingGenerator, $tableName);
      }

      // Création du document avec les métadonnées appropriées
      $document = new Document();
      $document->content = $content;
      $document->sourceType = $sourceType;
      $document->sourceName = $sourceName;
      $document->chunkNumber = 128;
      $document->metadata = [
        'type' => $type,
        'entity_type' => $entityType,
        'entity_id' => $entityId,
        'language_id' => $languageId,
        'date_modified' => 'now()'
      ];

      $this->vectorStores[$tableName]->addDocument($document);
      return true;
    } catch (\Exception $e) {
      error_log('Error while adding the document: ' . $e->getMessage());
      return false;
    }
  }

  /**
   * Searches for similar documents across all configured tables
   *
   * @param string $query Search query
   * @param int $limit Maximum number of results per table
   * @param float $minScore Minimum similarity score (0-1)
   * @param int|null $languageId Language ID for filtering results
   * @param string|null $entityType Entity type for filtering results
   * @return array Array of matching documents with similarity scores
   */
  public function searchDocuments(
    string $query,
    int $limit = 5,
    float $minScore = 0.7,
    ?int $languageId = null,
    ?string $entityType = null
  ): array {
    try {
      // Initialiser le tableau des résultats
      $allResults = [];
      if (CLICSHOPPING_APP_CHATGPT_CH_DEBUG_RAG_MANAGER == 'True') {
        error_log("Starting document search for query: " . $query);
      }
      // Vérifier si des vector stores sont disponibles
      if (empty($this->vectorStores)) {
        if (CLICSHOPPING_APP_CHATGPT_CH_DEBUG_RAG_MANAGER == 'True') {
          error_log("No vector store available");
        }
        return [];
      }
      if (CLICSHOPPING_APP_CHATGPT_CH_DEBUG_RAG_MANAGER == 'True') {
        error_log("Found embedding tables: " . implode(", ", array_keys($this->vectorStores)));
      }
      // Génération de l'embedding pour la requête
      $queryEmbedding = $this->embeddingGenerator->embedText($query);
      if (CLICSHOPPING_APP_CHATGPT_CH_DEBUG_RAG_MANAGER == 'True') {
        error_log("Generated embedding for query, length: " . count($queryEmbedding));
      }

      // Rechercher dans chaque vector store
      foreach ($this->vectorStores as $tableName => $vectorStore) {
        try {
          if (CLICSHOPPING_APP_CHATGPT_CH_DEBUG_RAG_MANAGER == 'True') {
            error_log("Table search: " . $tableName);
          }
          // Création d'une fonction de filtrage basée sur les critères
          $filter = function($metadata) use ($languageId, $entityType) {
            $match = true;

            // Filtrage par langue si spécifié
            if ($languageId !== null && isset($metadata['language_id'])) {
              $match = $match && ($metadata['language_id'] == $languageId);
            }

            // Filtrage par type d'entité si spécifié
            if ($entityType !== null && isset($metadata['entity_type'])) {
              $match = $match && ($metadata['entity_type'] == $entityType);
            }

            return $match;
          };

          $results = $vectorStore->similaritySearch($queryEmbedding, $limit, $minScore, $filter);
          if (CLICSHOPPING_APP_CHATGPT_CH_DEBUG_RAG_MANAGER == 'True') {
            error_log("Results found in table {$tableName}: " . count($results));
          }
          // Ajouter les résultats à la liste complète
          foreach ($results as $document) {
            $allResults[] = $document;
          }
        } catch (\Exception $e) {
          if (CLICSHOPPING_APP_CHATGPT_CH_DEBUG_RAG_MANAGER == 'True') {
            error_log("Error while searching in table {$tableName}: " . $e->getMessage());
            // Continuer avec les autres tables en cas d'erreur
          }
        }
      }

      // Trier les résultats par score de similarité (du plus élevé au plus bas)
      if (!empty($allResults)) {
        usort($allResults, function ($a, $b) {
          return $b->metadata['score'] <=> $a->metadata['score'];
        });
      }

      // Limiter le nombre total de résultats
      $finalResults = array_slice($allResults, 0, $limit);
      if (CLICSHOPPING_APP_CHATGPT_CH_DEBUG_RAG_MANAGER == 'True') {
        error_log("Total number of results found: " . count($finalResults));
      }

      return $finalResults;
    } catch (\Exception $e) {
      if (CLICSHOPPING_APP_CHATGPT_CH_DEBUG_RAG_MANAGER == 'True') {
        error_log('Error while searching documents: ' . $e->getMessage());
      }
      return [];
    }
  }

  /**
   * Generates an answer to a question using RAG methodology
   *
   * This method:
   * 1. Searches for relevant documents
   * 2. Creates a context from found documents
   * 3. Generates a response using the OpenAI model
   * 4. Includes relevant links and sources in the response
   *
   * @param string $question User's question
   * @param int $limit Maximum number of documents to retrieve
   * @param float $minScore Minimum similarity score (0-1)
   * @param int|null $languageId Language ID for filtering results
   * @param string|null $entityType Entity type for filtering results
   * @param array $modelOptions Additional options for the model
   * @return string Generated answer
   */
  public function answerQuestion(
    string $question,
    int $limit = 5,
    float $minScore = 0.7,
    ?int $languageId = null,
    ?string $entityType = null,
    array $modelOptions = []
  ): string {
    try {
      // Recherche des documents pertinents
      $documents = $this->searchDocuments($question, $limit, $minScore, $languageId, $entityType);

      if (empty($documents)) {
        return CLICSHOPPING::getDef('text_rag_answer_question_not_found');
      }

      // Préparation du contexte et des liens
      $context = '';
      $links = '';
      foreach ($documents as $doc) {
        $tableName = $doc->metadata['table_name'] ?? 'inconnu';
        $score = round(($doc->metadata['score'] ?? 0) * 100, 2);

        // Générer des liens spécifiques selon le type d'entité
        $link = '';

        if (isset($doc->metadata['entity_id'], $doc->metadata['type'])) {
          switch ($doc->metadata['type']) {
            case 'products':
              $link = HTML::link(CLICSHOPPING::link(null, 'A&Catalog\Products&Products'), $doc->metadata['type']);
              $link = str_replace('%5C', '\\', $link);
              break;
            case 'category':
              $link = HTML::link(CLICSHOPPING::link(null, 'A&Catalog\Categories&Categories'), $doc->metadata['type']);
              $link = str_replace('%5C', '\\', $link);
              break;
            case 'Page Manager':
              $link = HTML::link(CLICSHOPPING::link(null, 'A&Catalog\Communication&PageManager'), $doc->metadata['type']);
              $link = str_replace('%5C', '\\', $link);
              break;
            default:
              $link = '';
          }
        }

        // Ajouter au contexte
        $context .= $doc->content . "\n\n";

        // Ajouter aux liens
        if (!empty($link)) {
          $link .= "<br>- {{$doc->metadata['entity_id']}: {$link} (pertinence: {$score}%)\n";
        }
      }

      // Utiliser la classe Gpt existante pour générer la réponse
      $prompt = str_replace(
        ['{context}', '{question}', '{links}'],
        [$context, $question, $link],
        $this->systemMessageTemplate
      );

      // Génération de la réponse via la classe Gpt existante
      // Si des options de modèle sont fournies, les utiliser pour cette requête spécifique
      if (!empty($modelOptions)) {
        // Sauvegarde de l'état actuel
        $currentChat = Gpt::getOpenAiGpt(null);

        // Utilisation des options spécifiques pour cette requête
        $specificChat = Gpt::getOpenAiGpt($modelOptions);

        // Génération de la réponse avec les options spécifiques
        $response = Gpt::getGptResponse($prompt);

        // Restauration de l'état précédent si nécessaire
        // Cette étape pourrait être omise selon l'implémentation de Gpt::getGptResponse

        return $response;
      } else {
        // Utilisation standard sans options spécifiques
        return Gpt::getGptResponse($prompt);
      }
    } catch (\Exception $e) {
      error_log('Erreur lors de la génération de réponse : ' . $e->getMessage());
      return CLICSHOPPING::getDef('text_rag_answer_question_error');
    }
  }

  /**
   * Sets a custom template for the system message
   *
   * @param string $template New system message template
   */
  public function setSystemMessageTemplate(string $template): void
  {
    $this->systemMessageTemplate = $template;
  }

  /**
   * Return the tablelist of embedding configuréd
   *
   * @return array Liste des noms de tables
   */
  public function getConfiguredTables(): array
  {
    return array_keys($this->vectorStores);
  }
}
