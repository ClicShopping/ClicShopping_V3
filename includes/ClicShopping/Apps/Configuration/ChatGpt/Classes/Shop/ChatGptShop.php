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

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\ChatGptAdmin;

  class ChatGptShop
  {
    /**
     * @return bool
     */
    public static function checkGptStatus() :bool
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
     * @param array $apiResponse The GPT-3 API response.
     * @return float The sentiment score (-1 to 1) extracted from the response.
     */
    public static function extractSentimentScore(array $sentimentLabel): float
    {
      $text = $apiResponse['choices'][0]['text'];
      $sentimentScore = float($text);

      // Make sure the sentiment score is within the range -1 to 1
      $sentimentScore = max(-1.0, min(1.0, $sentimentScore));

      return $sentimentScore;
    }

    /**
     * Perform sentiment prediction on user comments using GPT-3 API (davinci engine).
     *
     * @param array $userComments An array containing user comments.
     * @return array An array of sentiment scores (-1 to 1) for each comment.
     */
    public static function performSentimentPrediction(array $userComments, int $max_token = 5, float $temperature = 0.2): array
    {
      $sentimentScores = [];

      foreach ($userComments as $comment) {
        $prompt = "Give me the sentiment of the following comment: '{$comment}' is: ";

        $apiResponse = ChatGptAdmin::getGptResponse($prompt, $max_token, $temperature);

        $sentimentScore = self::extractSentimentScore($apiResponse);

        $sentimentScores[] = $sentimentScore;
      }

      return $sentimentScores;
    }
  }