<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Reviews\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class ReviewsSentiment extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Reviews = Registry::get('Reviews');

    $this->page->setFile('reviews_sentiment.php');
    $this->page->data['action'] = 'ReviewsSentiment';

    $CLICSHOPPING_Reviews->loadDefinitions('Sites/ClicShoppingAdmin/reviews_sentiment');
  }
}