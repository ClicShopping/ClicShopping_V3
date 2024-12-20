<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ChatGpt\Classes\Shop;

use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\Gpt;

class ChatGptShop
{
  /**
   * Checks the operational status of the GPT system.
   *
   * @return bool Returns true if the GPT system is operational, otherwise false.
   */
  public static function checkGptStatus(): bool
  {
   return Gpt::checkGptStatus();
  }

  /**
   * Extracts the sentiment score from a provided sentiment label.
   *
   * @param array $sentimentLabel An array containing a single sentiment label as a string,
   *                              typically one of 'positive', 'neutral', or 'negative'.
   * @return float|null Returns 1.0 for 'positive', 0.0 for 'neutral', -1.0 for 'negative',
   *                    or null if the sentiment label is invalid or not provided.
   */
  protected static function extractSentimentScore(array $sentimentLabel): ?float
  {
    self::checkGptStatus();

    if (is_array($sentimentLabel)) {
      $sentimentLabel = $sentimentLabel[0] ?? null;
      $sentimentLabel = strtolower(trim($sentimentLabel));

      $result = match ($sentimentLabel) {
        'positive' => 1.0,
        'neutral' => 0.0,
        'negative' => -1.0,
        default => null,
      };
    } else {
      $result = null;
    }

    return $result;
  }

  /**
   * Performs sentiment prediction on an array of user comments by leveraging a GPT API for analysis.
   *
   * @param array $userComments An array of comments for which sentiment analysis needs to be performed.
   * @param int $max_token The maximum number of tokens for the GPT response (default is 5).
   * @param float $temperature The temperature parameter to adjust GPT response randomness (default is 0.2).
   * @return array The sentiment scores extracted from the GPT response for each comment.
   */
  public static function performSentimentPrediction(array $userComments, int $max_token = 5, float $temperature = 0.2)
  {
    $sentimentScores = [];

    foreach ($userComments as $comment) {
      $prompt = "Give me the sentiment of the following comment: '{$comment}' is: ";

      $apiResponse = GptShop::getGptResponse($prompt, $max_token, $temperature);

      if (isset($apiResponse)) {
        $replace = str_replace(' ', '', $apiResponse);
        $sentimentLabel[] = $replace;

        $sentimentScores = self::extractSentimentScore($sentimentLabel);
      } else {
        $sentimentScores = 0.0;
      }
    }

    return $sentimentScores;
  }
}