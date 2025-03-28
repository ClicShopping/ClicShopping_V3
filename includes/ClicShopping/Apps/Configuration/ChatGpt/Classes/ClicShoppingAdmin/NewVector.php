<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin;

use ClicShopping\Custom\Common\DivisionByZeroError;
use ClicShopping\Custom\Common\InvalidArgumentException;
use ClicShopping\OM\CLICSHOPPING;
use LLPhant\Chat\OpenAIChat;
use LLPhant\Embeddings\DataReader\FileDataReader;
use LLPhant\Embeddings\Document;
use LLPhant\Embeddings\DocumentSplitter\DocumentSplitter;
use LLPhant\Embeddings\EmbeddingFormatter\EmbeddingFormatter;
use LLPhant\Embeddings\EmbeddingGenerator\OpenAI\OpenAI3LargeEmbeddingGenerator;
use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\Gpt;

use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\ORMSetup;

class NewVector
{
  /**
   * Generates embeddings for a set of documents or a text description. If a file path is provided, the content of the file
   * is read and converted into embeddings. If a text description is provided instead, it is directly processed for embedding generation.
   */
 public static function createEmbedding(string|null $path_file_upload, string|null $text_description, int $document_number = 3)
 {
   Gpt::getEnvironment();

     if (is_file($path_file_upload)) {
       $filePath = $path_file_upload;
       $reader = new FileDataReader($filePath);
       $documents = $reader->getDocuments();
       $splitDocuments = DocumentSplitter::splitDocuments($documents, 128);
       $formattedDocuments = EmbeddingFormatter::formatEmbeddings($splitDocuments);
       $embeddingGenerator = new OpenAI3LargeEmbeddingGenerator();
       $embeddedDocuments = $embeddingGenerator->embedDocuments($formattedDocuments);
     } else {
       // Branche pour traitement d'un texte brut
       $embeddingGenerator = new OpenAI3LargeEmbeddingGenerator();
       $embedded = $embeddingGenerator->embedText($text_description);

       $document = new Document();
       $document->content = $text_description;
       $document->embedding = $embedded;
       $document->sourceName = 'manual';
       $document->sourceType = 'manual';

       $splitDocuments = DocumentSplitter::splitDocument($document, 128);
       $formattedDocuments = EmbeddingFormatter::formatEmbeddings($splitDocuments);

       // Génération des embeddings sur le document découpé
       $embeddedDocuments = $embeddingGenerator->embedDocuments($formattedDocuments);
     }

     return $embeddedDocuments;
 }



  /**
   * Initializes and returns an OpenAIChat instance configured with specified parameters.
   *
   * @return OpenAIChat An instance of the OpenAIChat class configured for GPT functionality.
   */
  private static function chat(): OpenAIChat
  {
    $parameters = ['model' => 'gpt-4o'];
    $chat = Gpt::getOpenAiGpt($parameters);
    return $chat;
  }


  /**
   * Retrieves the content of a document either from a specified file path or from a text description.
   *
   * @param string|null $path_file_upload The file path to upload and read the document from. Can be null.
   * @param string|null $text_description The text description to use if the file path is null or invalid. Can be null.
   *
   * @return string Returns the content of the document, either read from the file or taken from the text description.
   */
  public static function getDocument(string|null $path_file_upload, string|null $text_description): string
  {
    if (is_file($path_file_upload)) {
      $filePath = $path_file_upload;
      $reader = new FileDataReader($filePath);
      $documents = $reader->getDocuments();
      $documents = $documents[0]->content;
    } else {
      $documents = $text_description;
    }

    return $documents;
  }


//***********
// Statistics
//***********
  /**
   * Calculates the mean (average) value of the given array of numbers.
   *
   * @param array $values The array of numerical values to calculate the mean from.
   * @return float The calculated mean value of the array.
   * @throws DivisionByZeroError If the array is empty, causing a division by zero.
   */
  private function calculateMean(array $values)
  {
    return array_sum($values) / count($values);
  }

  /**
   * Calculates the variance of a given array of numeric values.
   *
   * @param array $values An array of numeric values for which to calculate the variance.
   * @return float The calculated variance of the provided values.
   * @throws \InvalidArgumentException If the input array is empty.
   */
   private function calculateVariance(array $values): float
  {
    $mean = $this->calculateMean($values);
    $sum_of_squared_diff = 0;

    foreach ($values as $value) {
      $sum_of_squared_diff += pow($value - $mean, 2);
    }

    if (empty($values)) {
      throw new \InvalidArgumentException('The array should not be empty.');
    }

    return $sum_of_squared_diff / count($values);
  }

  /**
   * Calculates the standard deviation of the given array of values.
   *
   * @param array $values The array of numerical values to calculate the standard deviation for.
   * @return float The calculated standard deviation of the values.
   * @throws \InvalidArgumentException If the provided array is empty.
   */
  public function calculateStandardDeviation(array $values): float
  {
    $variance = $this->calculateVariance($values);

    if (empty($values)) {
      throw new \InvalidArgumentException('The array should not be empty.');
    }

    return sqrt($variance);
  }

  /**
   * Calculates the cosine similarity between two vectors.
   *
   * @param array $vec1 An array representing the first vector.
   * @param array $vec2 An array representing the second vector. Must have the same length as $vec1.
   * @return float The cosine similarity value, which ranges from -1 to 1. Returns 0.0 if either vector has zero magnitude.
   * @throws InvalidArgumentException If the input vectors do not have the same length.
   */
  public static function cosineSimilarity(array $vec1, array $vec2) :float
  {
    if (count($vec1) !== count($vec2)) {
      throw new InvalidArgumentException('Vectors must have the same length.');
    }

    $dot_product = 0;
    $magnitude_vec1 = 0;
    $magnitude_vec2 = 0;

    foreach ($vec1 as $i => $value) {
      $dot_product += $value * $vec2[$i];
      $magnitude_vec1 += $value * $value;
      $magnitude_vec2 += $vec2[$i] * $vec2[$i];
    }

    if ($magnitude_vec1 == 0 || $magnitude_vec2 == 0) {
      return 0.0; // Return 0 for vectors with no magnitude
    }

    return $dot_product / (sqrt($magnitude_vec1) * sqrt($magnitude_vec2));
  }
}