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

    /**
     * @return mixed
     */
    private function getCheck()
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

    /**
     * @return string
     */
    public function display(): string
    {
      $output = '';

      if ($this->getCheck() === true) {
        $output .= '<div>
                      <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                          <div class="separator"></div>
                             ' . CLICSHOPPING::getDef('module_account_customers_gdpr_delete_all_reviews') . ' (' . CLICSHOPPING::getDef('module_account_customers_gdpr_count_customers_reviews') . ' : ' . $this->count . ')' . '
                            <label class="switch">
                              ' . HTML::checkboxField('delete_all_reviews', null, null, 'class="success"') . '
                              <span class="slider"></span>
                            </label>
                        </li>
                      </ul>
                    </div>
                  ';
      }

      return $output;
    }
  }