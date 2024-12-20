<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ChatGpt\Module\Hooks\ClicShoppingAdmin\ReviewsSentiment;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;
use ClicShopping\Apps\Configuration\ChatGpt\ChatGpt as ChatGptApp;
use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\AdministratorAdmin;
use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\Gpt;

use function count;

class Update implements \ClicShopping\OM\Modules\HooksInterface
{
  public mixed $app;

  /**
   * Constructor method for initializing the ChatGpt application.
   * Ensures that the ChatGpt instance is registered with the Registry and fetches it for use.
   *
   * @return void
   */
  public function __construct()
  {
    if (!Registry::exists('ChatGpt')) {
      Registry::set('ChatGpt', new ChatGptApp());
    }

    $this->app = Registry::get('ChatGpt');
  }

  /**
   * Retrieves all customer reviews for a specific review ID, sanitizing the input,
   * and returning the review texts concatenated and separated by "<br> - ".
   *
   * @return string A string containing all customer reviews related to the specified review ID,
   * concatenated and separated by "<br> - ".
   */
  private function getAllCustomerReviews(): string
  {
    $id = HTML::sanitize($_GET['rID']);

    $Qreview = $this->app->db->prepare('select rd.reviews_text
                                        from :table_reviews r, 
                                            :table_reviews_description rd
                                        where r.status = 1
                                        and rd.languages_id = 1
                                        and r.reviews_id = :reviews_id
                                        and r.reviews_id = rd.reviews_id
                                      ');
    $Qreview->bindInt(':reviews_id', $id);
    $Qreview->execute();

    $review_array = $Qreview->fetchAll();

    $review_texts = [];

    foreach ($review_array as $value) {
      $review_texts[] = $value['reviews_text'];
    }

// Output the review texts separated by <br>
    $result =  implode('<br> - ', $review_texts);

    return $result;
  }

  /**
   * Generates a sentiment summary based on customer product reviews.
   *
   * @param int $language_id The ID of the language in which the sentiment analysis should be written.
   * @param string $products_name The name of the product for which the sentiment analysis is performed.
   *
   * @return string The sentiment analysis summary based on the provided product reviews.
   */
  private function generateSentiment(int $language_id, string $products_name): string
  {
    $CLICSHOPPING_Language = Registry::get('Language');
    $language_name = $CLICSHOPPING_Language->getLanguagesName($language_id);
    $text_reviews = $this->getAllCustomerReviews();

    // Split the message into words
    $words = preg_split('/\s+/', $text_reviews, -1, PREG_SPLIT_NO_EMPTY);

// Check if the message exceeds 300 words
    if (count($words) > 2250) {
      $words = array_slice($words, 0, 300);
      $text_reviews = implode(' ', $words);
    }

    $message = 'Could you give me a summary about the customer sentiment analysis concerning this product reviews ' . $products_name . ' below. 
    remove the prompt engine message.
    remove the question of this request.
    Give me only the brut response.
    Write the answer in this language : ' . $language_name . '.
    Write the answer in 300 worlds maximum.
    Here customers products reviews for sentiment analysis : ';

    $prompt = $message . $text_reviews;

    $sentiment = Gpt::getGptResponse($prompt, 2300, 0.5);

    return $sentiment;
  }

  /**
   * Executes the process of managing and storing sentiment data for product reviews.
   * Depending on the existence of a record, it updates or creates new sentiment entries,
   * including multi-language support for the product's sentiment description.
   *
   * @return bool|void Returns false if GPT status is unavailable; otherwise, performs the execution process without return value.
   */
  public function execute()
  {
    $CLICSHOPPING_Language = Registry::get('Language');
    $CLICSHOPPING_ProductsAdmin = Registry::get('ProductsAdmin');

    if (Gpt::checkGptStatus() === false) {
      return false;
    }

    $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/SEO/seo_chat_gpt');

    $id = HTML::sanitize($_GET['rID']);
    $user_admin = AdministratorAdmin::getUserAdmin();
    $languages = $CLICSHOPPING_Language->getLanguages();

    $Qchek = $this->app->db->get('reviews_sentiment', 'id', ['reviews_id' => (int)$id]);
    $Qproduct = $this->app->db->get('reviews', 'products_id', ['reviews_id' => (int)$id]);
    $products_id = $Qproduct->valueInt('products_id');
//update
    if (!empty($Qchek->valueInt('id'))) {
      $sql_data_array = [
        'reviews_id' => (int)$id,
        'date_modified' => 'now()',
        'user_admin' => $user_admin,
      ];

      $this->app->db->save('reviews_sentiment', $sql_data_array, ['id' => (int)$Qchek->valueInt('id')]);

      for ($i = 0, $n = count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $products_name = $CLICSHOPPING_ProductsAdmin->getProductsName($products_id, $language_id);

        $sql_data_array = [
          'description' => $this->generateSentiment($language_id, $products_name),
        ];

        $insert_sql_data = [
          'id' => (int)$Qchek->valueInt('id'),
          'language_id' => $language_id
        ];

        $this->app->db->save('reviews_sentiment_description ', $sql_data_array, $insert_sql_data);
      }
    } else {
//insert
      $sql_data_array = [
        'reviews_id' => (int)$id,
        'date_added' => 'now()',
        'user_admin' => $user_admin,
        'products_id' => $products_id,
        'sentiment_status' => 1
      ];

      $this->app->db->save('reviews_sentiment', $sql_data_array);
      $last_id = $this->app->db->lastInsertId();

      for ($i = 0, $n = count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $products_name = $CLICSHOPPING_ProductsAdmin->getProductsName($products_id, $language_id);

        $sql_data_array = [
          'description' => $this->generateSentiment($language_id, $products_name)
        ];

        $insert_sql_data = [
          'id' => (int)$last_id,
          'language_id' => (int)$language_id
        ];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $this->app->db->save('reviews_sentiment_description ', $sql_data_array);
      }
    }
  }
}


