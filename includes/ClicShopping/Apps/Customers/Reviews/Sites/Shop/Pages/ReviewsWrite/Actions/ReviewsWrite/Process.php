<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Customers\Reviews\Sites\Shop\Pages\ReviewsWrite\Actions\ReviewsWrite;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class Process extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Reviews = Registry::get('Reviews');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');

      if (isset($_POST['action']) && ($_POST['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] === $_SESSION['sessiontoken'])) {
        $error = false;

        $CLICSHOPPING_Hooks->call('ReviewsWrite', 'PreAction');

        $rating = HTML::sanitize((int)$_POST['rating']);
        $review = HTML::sanitize($_POST['review']);

        if (isset($_POST['customer_agree_privacy'])) {
          $customer_agree_privacy = HTML::sanitize($_POST['customer_agree_privacy']);

          if ($customer_agree_privacy != 'on' && \defined('MODULES_PRODUCTS_REVIEWS_WRITE_CUSTOMER_AGREEMENT_STATUS') && MODULES_PRODUCTS_REVIEWS_WRITE_CUSTOMER_AGREEMENT_STATUS == 'True') {
            $error = true;
            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error'), 'error', 'reviews_write');
          }
        }

        if (\strlen($review) < (int)REVIEW_TEXT_MIN_LENGTH) {
          $error = true;
          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('js_review_text', ['min_length' => (int)REVIEW_TEXT_MIN_LENGTH]), 'error');
        }

        if (($rating < 1) || ($rating > 5)) {
          $error = true;
        }

        if ($error === false) {
          $CLICSHOPPING_Reviews->saveEntry();
          $CLICSHOPPING_Reviews->sendEmail();

          $CLICSHOPPING_Hooks->call('ReviewsWrite', 'Process');

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('message_customer'), 'success', 'rewiews_write');

          CLICSHOPPING::redirect(null, 'Products&ReviewsWrite&Success&products_id=' . $CLICSHOPPING_ProductsCommon->getID());
        }

        if ($error === true) {
          CLICSHOPPING::redirect(null, 'Products&ReviewsWrite&products_id=' . $CLICSHOPPING_ProductsCommon->getID());
        }
      }
    }
  }