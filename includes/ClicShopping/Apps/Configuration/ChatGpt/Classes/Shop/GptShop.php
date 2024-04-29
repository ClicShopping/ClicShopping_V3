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

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;
use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\Gpt;

use function defined;
use function is_null;

class GptShop
{
  /**
   * @return bool
   */
  public static function checkGptStatus(): bool
  {
    return Gpt::checkGptStatus();
  }

  /**
   * @param bool $chatGpt
   * @return string
   */
  public static function getAjaxUrl(bool $chatGpt = true): string
  {
    if ($chatGpt === true) {
      $url = CLICSHOPPING::getConfig('http_server', 'ClicShoppingAdmin') . CLICSHOPPING::getConfig('http_path', 'Shop') . 'ext/ajax/ai/chatbot_gpt.php';
    } else {
      $url = '';
    }

    return $url;
  }

  /**
   * @param string $question
   * @param int|null $maxtoken
   * @param float|null $temperature
   * @param string|null $engine
   * @param int|null $max
   * @return bool|string
   * @throws \LLPhant\Exception\MissingParameterExcetion
   */
  public static function getGptResponse(string $question, ?int $maxtoken = null, ?float $temperature = null)
  {
    if (self::checkGptStatus() === false) {
      return false;
    }

    $question = HTML::sanitize($question);

    if (is_null($maxtoken)) {
      $maxtoken = 200;
    }

    if (is_null($temperature)) {
      $temperature = 0.5;
    }

    $response = Gpt::getGptResponse($question, $maxtoken, $temperature);
/*
    try {
      $result = $response['choices'][0]['message']['content'];

      $array_usage = [
        'promptTokens' => $response->usage->promptTokens,
        'completionTokens' => $response->usage->completionTokens,
        'totalTokens' => $response->usage->totalTokens,
      ];

      static::saveData($question, $result, $array_usage);

      return $result;
    } catch (RuntimeException $e) {
      throw new \Exception('Error appears, please look the console error');

      return false;
    }
*/
    return $response;
  }









  /**
   * @param int $max_token
   * @return false
   */
  public static function checkMaxTokenPerDay(int $max_token): bool
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QtokenTotal = $CLICSHOPPING_Db->query('SELECT SUM(totalTokens) AS total
                                             FROM :table_gpt_usage
                                             WHERE DATE(date_added) = CURDATE()
                                            ');
    $total_accepted = $QtokenTotal->value('total');

    if ($total_accepted > $max_token) {
      return false;
    } else {
      return true;
    }
  }

  /**
   * @param string $prompt
   * @param string $result
   * @param array $usage
   */
  private static function saveData(string $question, string $result, array $usage): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $array_sql = [
      'question' => $question,
      'response' => $result,
      'date_added' => 'now()',
      'user_admin' => 'Chatbot Front Office'
    ];

    $CLICSHOPPING_Db->save('gpt', $array_sql);

    $QlastId = $CLICSHOPPING_Db->prepare('select gpt_id
                                           from :table_gpt
                                           order by gpt_id desc
                                           limit 1
                                          ');
    $QlastId->execute();

    $modelArray = self::getGptModel(); // Get the array of models
    $modelId = $modelArray[0]['id']; // Get the 'id' of the first model
    $engine = $modelId; // Assign the model ID to the $engine variable

    $array_usage_sql = [
      'gpt_id' => $QlastId->valueInt('gpt_id'),
      'promptTokens' => $usage['promptTokens'],
      'completionTokens' => $usage['completionTokens'],
      'totalTokens' => $usage['totalTokens'],
      'ia_type' => 'GPT',
      'model' => $engine,
      'date_added' => 'now()'
    ];

    $CLICSHOPPING_Db->save('gpt_usage', $array_usage_sql);
  }

  /**
   * @param string $question
   * @param string|array $result
   * @return string
   * @throws \Exception
   */
  public static function productSearch(string $question, string|array $result): string
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Language = Registry::get('Language');
    $CLICSHOPPING_Customer = Registry::get('Customer');

    if (is_string($result)) {
      $result = trim($result);
      $result = str_replace("'", '"', $result);

      $result_array = json_decode($result, true);
    } else {
      $result_array = $result;
    }

    if (is_array($result_array)) {
      $searchQueries = [];

      foreach ($result_array as $i => $word) {
        $searchQueries[] = '%' . $word . '%';

        if ($i >= 2) {
          break;
        }
      }

      if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
        $query = 'select p.products_id,
                           pd.products_name
                from :table_products p left join :table_products_groups g on p.products_id = g.products_id,
                     :table_products_description pd
                where p.products_status = 1
                and p.products_id = pd.products_id
                and pd.language_id = :language_id
                and g.products_group_view = 1
                and p.products_status = 1
                and p.products_archive = 0
                and (g.customers_group_id = :customers_group_id or g.customers_group_id = 99)            
                ';
      } else {
        $query = 'select p.products_id,
                            pd.products_name
                    from :table_products p left join :table_products_groups g on p.products_id = g.products_id,
                         :table_products_description pd
                    where p.products_status = 1
                    and p.products_id = pd.products_id
                    and pd.language_id = :language_id
                    and p.products_view = 1
                    and p.products_status = 1
                    and p.products_archive = 0
                  ';
      }

      if (!empty($searchQueries)) {
        $searchConditions = implode(' or ', array_map(function ($search) {
          return 'pd.products_name like \'' . $search . '\'';
        }, $searchQueries));

        $query .= ' and (' . $searchConditions . ')';
      }

      $query .= ' limit 1';

      $Qproducts = $CLICSHOPPING_Db->prepare($query);
      $Qproducts->bindInt(':language_id', $CLICSHOPPING_Language->getId());

      if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
        $Qproducts->bindInt(':customers_group_id', $CLICSHOPPING_Customer->getCustomersGroupID());
      }

      $Qproducts->execute();

      $question_result = $Qproducts->value('products_name');

      if (!empty($question_result)) {
        $result = CLICSHOPPING::getDef('text_chatbot_ok', ['question' => $question]);
        $result .= GptShop::getGptResponse($question);
      } else {
        $result = CLICSHOPPING::getDef('text_chatbot_not_ok');
      }
    } else {
      $result = CLICSHOPPING::getDef('text_chatbot_not_ok');
    }

    return $result;
  }
}