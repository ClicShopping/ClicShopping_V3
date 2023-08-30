<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ChatGpt\Classes\Shop;

use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\ChatGptAdmin;

class ChatGptShop
{
  /**
   * @return bool
   */
  public static function checkGptStatus(): bool
  {
    if (!\defined('CLICSHOPPING_APP_CHATGPT_CH_STATUS') || CLICSHOPPING_APP_CHATGPT_CH_STATUS == 'False' || empty('CLICSHOPPING_APP_CHATGPT_CH_API_KEY')) {
      return false;
    } else {
      return true;
    }
  }

  /**
   * Extract the sentiment score from the GPT-3 API response.
   *
   * @param array $sentimentLabel The sentiment label from the GPT-3 API response (e.g., "positive", "neutral", "negative").
   * @return float|null The sentiment score (-1 to 1) extracted from the response.
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
   * Perform sentiment prediction on user comments using GPT-3 API (davinci engine).
   *
   * @param array $userComments An array containing user comments.
   * @param int $max_token
   * @param float $temperature
   * @return float An array of sentiment scores (-1 to 1) for each comment.
   * @throws \Exception
   */
  public static function performSentimentPrediction(array $userComments, int $max_token = 5, float $temperature = 0.2)
  {
    $sentimentScores = [];

    foreach ($userComments as $comment) {
      $prompt = "Give me the sentiment of the following comment: '{$comment}' is: ";

      $apiResponse = ChatGptAdmin::getGptResponse($prompt, $max_token, $temperature);

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