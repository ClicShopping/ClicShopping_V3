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

  namespace ClicShopping\OM\Module\Hooks\Shop\Account;

  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class AccountGdprReviews
  {

    protected $countMyFeedback;
    protected $deleteMyFeedback;

    public function getCheck()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');

      $QReviews = $CLICSHOPPING_Db->prepare('select count(reviews_id) as count
                                              from :table_reviews
                                              where customers_id = :customers_id
                                             ');
      $QReviews->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
      $QReviews->execute();

      $this->count = $QReviews->valueInt('count');

      return $QReviews->fetch();
    }

    public function display()
    {
      if ($this->getCheck() === true) {
        $output = '<div>';
        $output .= '<label class="checkbox-inline">';
        $output .= HTML::checkboxField('delete_all_reviews');
        $output .= '</label>';
        $output .= CLICSHOPPING::getDef('module_account_customers_gdpr_delete_all_reviews') . ' (' . CLICSHOPPING::getDef('module_account_customers_gdpr_count_customers_reviews') . ' : ' . $this->count . ')';
        $output .= '</div>';

        return $output;
      }
    }
  }