<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ChatGpt\Classes\Rag;

use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\Apps\Configuration\ChatGpt\Classes\Rag\VectorType;

/**
* Class DoctrineOrm
 *
 * This class manages database connections and operations using Doctrine ORM,
 * specifically adapted for use with LLPhant and MariaDB vector operations.
 * It provides functionality for:
  * - Database connection management
* - MariaDB version verification
* - Table structure management for RAG (Retrieval-Augmented Generation)
 * - Vector embedding table operations
*
 * Requirements:
 * - MariaDB version 11.8.0 or higher
* - Proper database credentials configuration
* - Vector support in MariaDB
*
 * @package ClicShopping\Apps\Configuration\ChatGpt\Classes\Rag
*/
class DoctrineOrm
{
  /**
   * Returns the current version of MariaDB from the connection.
   *
   * @return string MariaDB server version
   * @throws \Doctrine\DBAL\Exception If database connection fails
   */
  private static function getMariaDbVersion(): string
  {
    $entityManager = self::getEntityManager();
    $connection = $entityManager->getConnection();

    // Exécution d'une requête pour obtenir la version de MariaDB
    $version = $connection->executeQuery("SELECT VERSION()")->fetchOne();
    return $version;
  }

  /**
   * Checks if the MariaDB version meets the minimum required version (11.8.0).
   *
   * @throws \Exception If the MariaDB version is lower than the minimum required version
   */
  private static function checkMariaDbVersion()
  {
    // get the version of MariaDB
    $mariadbVersion = self::getMariaDbVersion();

    // Version minimale requise
    $minVersion = '11.8.0';

    // Comparer les versions
    if (version_compare($mariadbVersion, $minVersion, '<')) {
      throw new \Exception("The MariaDB ($mariadbVersion)version is inferior at the minimalversion reuired ($minVersion). You can not use the rag.");
    }
  }

  /**
   * Configures and initializes Doctrine ORM settings.
   * Sets up the database connection parameters and ORM configuration.
   *
   * @return array Array containing connection parameters and configuration
   * @throws \Exception If configuration cannot be initialized
   */
  private static function Orm()
  {
    // Vérifier la version de MariaDB avant de configurer la connexion
//    self::checkMariaDbVersion();

    // Configuration de Doctrine avec un pilote de métadonnées minimal
    $config = ORMSetup::createConfiguration(true, null, null);

    // Ajouter un pilote de métadonnées minimal (requis par Doctrine)
    $config->setMetadataDriverImpl(new \Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver([]));

    // Paramètres de connexion pour MariaDB
    $connectionParams = [
      'driver' => 'pdo_mysql',
      'user' => CLICSHOPPING::getConfig('db_server_username'),
      'password' => CLICSHOPPING::getConfig('db_server_password'),
      'dbname' => CLICSHOPPING::getConfig('db_database'),
      'host' => CLICSHOPPING::getConfig('db_server'),
      'charset' => 'utf8mb4',
      'driverOptions' => [
        \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
      ],
      // Options spécifiques pour MariaDB Vector
      'serverVersion' => '11.8.0' //$mariadbVersion, // Version exacte de MariaDB
    ];

    return ['connectionParams' => $connectionParams, 'config' => $config];
  }

  /**
   * Creates and returns an instance of the EntityManager.
   * Initializes the database connection and registers custom vector types.
   *
   * @return EntityManager The configured EntityManager instance
   * @throws \Doctrine\DBAL\Exception If connection cannot be established
   */
  public static function getEntityManager(): EntityManager
  {
    $orm = self::Orm();
    $connectionParams = $orm['connectionParams'];
    $config = $orm['config'];

    try {
      // Création de la connexion avec gestion des erreurs
      $connection = DriverManager::getConnection($connectionParams, $config);

      // Enregistrement du type personnalisé pour les vecteurs si nécessaire
      if (!Type::hasType('vector')) {
        Type::addType('vector', VectorType::class);
      }

      // Création de l'EntityManager
      $entityManager = new EntityManager($connection, $config);

      return $entityManager;
    } catch (\Doctrine\DBAL\Exception $e) {
      // Gestion des erreurs de connexion
      if (CLICSHOPPING_APP_CHATGPT_CH_DEBUG_RAG_MANAGER == 'True') {
        error_log('Erreur de connexion Doctrine : ' . $e->getMessage());
      }
      throw $e;
    }
  }

  /**
   * Checks if the database has the necessary tables and structures for RAG.
   * Verifies both table existence and required index presence.
   *
   * @param string $tableName Name of the table to check
   * @return bool True if the structure is correct, false otherwise
   */
  public static function checkTableStructure(string $tableName): bool
  {
    try {
      $entityManager = self::getEntityManager();
      $connection = $entityManager->getConnection();

      // Vérifier si la table existe
      $tableExists = $connection->executeQuery("
        SELECT COUNT(*) 
        FROM information_schema.tables 
        WHERE table_schema = DATABASE() 
        AND table_name = ?
      ", [$tableName])->fetchOne();

      if (!$tableExists) {
        return false;
      }

      // Vérifier si l'index vectoriel existe
      $indexExists = $connection->executeQuery("
        SHOW INDEX FROM {$tableName} 
        WHERE Key_name = 'embedding_index'
      ")->fetchAllAssociative();

      return !empty($indexExists);
    } catch (\Exception $e) {
      if (CLICSHOPPING_APP_CHATGPT_CH_DEBUG_RAG_MANAGER ==  'true') {
        error_log('Error while checking the structure of the table ' . $tableName . ' : ' . $e->getMessage());
      }
      return false;
    }
  }

  /**
   * Returns a list of all available embedding tables in the database.
   * Queries the database to find tables that contain a VECTOR type embedding column.
   *
   * @return array List of table names containing vector embeddings
   * @throws \Exception If there is an error connecting to the database or executing the query
   */
  public static function getEmbeddingTables(): array
  {
    try {
      $entityManager = self::getEntityManager();
      $connection = $entityManager->getConnection();

      // Rechercher toutes les tables qui ont une colonne embedding de type VECTOR
      $tables = $connection->executeQuery("
        SELECT table_name 
        FROM information_schema.columns 
        WHERE table_schema = DATABASE() 
        AND column_name = 'embedding' 
        AND data_type LIKE '%vector%'
      ")->fetchFirstColumn();

      return $tables;
    } catch (\Exception $e) {
      if (CLICSHOPPING_APP_CHATGPT_CH_DEBUG_RAG_MANAGER ==  'true') {
        error_log('Error while retrieving the embedding tables: ' . $e->getMessage());
      }
      return [];
    }
  }

  /**
   * Creates the necessary database structure for RAG if it doesn't exist.
   * Sets up tables with appropriate columns and vector indices for embedding storage.
   *
   * @param string $tableName Name of the table to create
   * @return bool True if creation succeeds, false otherwise
   * @throws \Exception If table creation fails
   */
  public static function createTableStructure(string $tableName): bool
  {
    try {
      $entityManager = self::getEntityManager();
      $connection = $entityManager->getConnection();
/*
      // Vérifier si la table existe déjà
      $tableExists = $connection->executeQuery("
          SELECT COUNT(*) 
          FROM information_schema.tables 
          WHERE table_schema = DATABASE() 
          AND table_name = ?
      ", [$tableName])->fetchOne();
*/

      // Vérifier si la table existe
      $tableExists = $connection->executeQuery("
          SELECT COUNT(*) 
          FROM information_schema.tables 
          WHERE table_schema = DATABASE() 
          AND table_name = ?
      ", [$tableName], [ParameterType::STRING])->fetchOne();

      if (!$tableExists) {
        // Création de la table avec la structure recommandée
        $connection->executeStatement("
            CREATE TABLE IF NOT EXISTS {$tableName} (
                id SERIAL PRIMARY KEY,
                content TEXT DEFAULT NULL,
                type TEXT DEFAULT NULL,
                sourcetype TEXT DEFAULT 'manual',
                sourcename TEXT DEFAULT 'manual',
                embedding VECTOR(3072) NOT NULL,
                chunknumber INT DEFAULT 128,
                date_modified DATETIME DEFAULT NULL,
                entity_type VARCHAR(50) DEFAULT NULL,
                entity_id INT DEFAULT NULL
            )
        ");
      }

      // Vérifier si l'index vectoriel existe déjà
      $indexExists = $connection->executeQuery("
          SHOW INDEX FROM {$tableName} 
          WHERE Key_name = 'embedding_index'
      ")->fetchAllAssociative();

      // Créer l'index vectoriel seulement s'il n'existe pas déjà
      if (empty($indexExists)) {
        try {
          $connection->executeStatement("
              CREATE VECTOR INDEX embedding_index ON {$tableName} (embedding)
          ");
        } catch (\Exception $e) {
          if (CLICSHOPPING_APP_CHATGPT_CH_DEBUG_RAG_MANAGER ==  'true') {
            error_log("Warning: Unable to create the vector index on {$tableName}: " . $e->getMessage());
            // Continuer même si la création de l'index échoue
          }
        }
      }

      return true;
    } catch (\Exception $e) {
      if (CLICSHOPPING_APP_CHATGPT_CH_DEBUG_RAG_MANAGER ==  'true') {
        error_log('Error while creating the structure of the table ' . $tableName . ' : ' . $e->getMessage());
      }
      return false;
    }
  }
}
