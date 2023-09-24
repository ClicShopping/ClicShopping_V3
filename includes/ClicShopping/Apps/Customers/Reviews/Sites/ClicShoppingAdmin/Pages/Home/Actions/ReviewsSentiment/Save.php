<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Reviews\Sites\ClicShoppingAdmin\Pages\Home\Actions\ReviewsSentiment;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\AdministratorAdmin;
use function count;

class Save extends \ClicShopping\OM\PagesActionsAbstract
{
   public function execute()
  {
    $CLICSHOPPING_Reviews = Registry::get('Reviews');
    $CLICSHOPPING_Language = Registry::get('Language');
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

    $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

    if (isset($_GET['ReviewsSentiment'], $_GET['Save'], $_GET['rID'])) {
      $reviews_id = HTML::sanitize($_GET['rID']);
      $user_admin = AdministratorAdmin::getUserAdmin();
      $languages = $CLICSHOPPING_Language->getLanguages();

      $Qchek = $CLICSHOPPING_Reviews->db->get('reviews_sentiment', 'id', ['reviews_id' => (int)$reviews_id]);
      $id = $Qchek->valueInt('id');

      $sql_data_array = [
        'date_modified' => 'now()',
        'user_admin' => $user_admin,
      ];

      $CLICSHOPPING_Reviews->db->save('reviews_sentiment', $sql_data_array, ['id' => $id]);

      for ($i = 0, $n = count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $sentiment_description_array = $_POST['reviews_sentiment_description'];

        $insert_sql_data = [
          'id' => (int)$id,
          'language_id' => (int)$language_id
        ];

        $sql_data_array = [
          'description' => $sentiment_description_array
        ];

        $CLICSHOPPING_Reviews->db->save('reviews_sentiment_description ', $sql_data_array, $insert_sql_data);

        $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Reviews->getDef('text_success'), 'success', 'main');
      }

      $CLICSHOPPING_Reviews->redirect('ReviewsSentiment&page=' . $page);
    }
  }
}