<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Communication\Newsletter\Module\Hooks\ClicShoppingAdmin\StatsDashboard;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Communication\Newsletter\Newsletter as NewsletterApp;

class PageTabContent implements \ClicShopping\OM\Modules\HooksInterface
{
  public mixed $app;

  public function __construct()
  {
    if (!Registry::exists('Newsletter')) {
      Registry::set('Newsletter', new NewsletterApp());
    }

    $this->app = Registry::get('Newsletter');

    $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/StatsDashboard/page_tab_content');
  }

  private function statsCountCustomersNewsletter()
  {
    $QcustomersNewsletter = $this->app->db->prepare('select count(customers_id) as count
                                                       from :table_customers
                                                       where customers_newsletter = 1
                                                       limit 1
                                                      ');
    $QcustomersNewsletter->execute();

    $customers_total_newsletter = $QcustomersNewsletter->valueInt('count');

    return $customers_total_newsletter;
  }

// Nbr de clients total
  private function statCountCustomersB2C()
  {
    $Qcustomer = $this->app->db->prepare('select count(customers_id) as count
                                            from :table_customers
                                            where customers_group_id = 0
                                            and customers_newsletter = 1
                                            limit 1
                                           ');
    $Qcustomer->execute();

    $customers_total = $Qcustomer->valueInt('count');

    return $customers_total;
  }

// Average newlstter subcribers vs total customers
  private function statAverageCustomersNewsletterB2C()
  {
    if ($this->statCountCustomersB2C() > 0 && $this->statsCountCustomersNewsletter() > 0) {
      $Average = round(($this->statCountCustomersB2C() / $this->statsCountCustomersNewsletter()) * 100, 2) . ' %';
      return $Average;
    }
  }

// Nbr de clients total
  private function statCountCustomersB2B()
  {
    $Qcustomer = $this->app->db->prepare('select count(customers_id) as count
                                             from :table_customers
                                             where customers_group_id > 0
                                             and customers_newsletter = 1
                                             limit 1
                                            ');
    $Qcustomer->execute();

    $customers_total = $Qcustomer->valueInt('count');

    return $customers_total;
  }

// Average newlstter subcribers vs total customers
  public function statAverageCustomersNewsletterB2B()
  {
    if ($this->statCountCustomersB2B() > 0 && $this->statsCountCustomersNewsletter() > 0) {
      $Average = round(($this->statCountCustomersB2B() / $this->statsCountCustomersNewsletter()) * 100, 2) . ' %';

      return $Average;
    }
  }

  public function display()
  {
    if (!\defined('CLICSHOPPING_APP_CUSTOMERS_CS_STATUS') || CLICSHOPPING_APP_CUSTOMERS_CS_STATUS == 'False') {
      return false;
    }

    if ($this->statsCountCustomersNewsletter() != 0) {
      $content = '
         <div class="row">
          <div class="col-md-11">
            <div class="form-group row">
              <label for="' . $this->app->getDef('box_text_newsletter') . '" class="col-9 col-form-label"><a href="' . $this->app->link('Newsletter') . '">' . $this->app->getDef('box_text_newsletter') . '</a></label>
              <div class="col-md-3">
                ' . $this->statsCountCustomersNewsletter() . '
              </div>
            </div>
          </div>
        </div>
        ';

      $content .= '
         <div class="row">
          <div class="col-md-11">
            <div class="form-group row">
              <label for="' . $this->app->getDef('box_entry_newsletter_b2c') . '" class="col-9 col-form-label"><a href="' . $this->app->link('Newsletter') . '">' . $this->app->getDef('box_entry_newsletter_b2c') . '</a></label>
              <div class="col-md-3">
                ' . $this->statAverageCustomersNewsletterB2C() . '
              </div>
            </div>
          </div>
        </div>
        ';

      if (MODE_B2B_B2C == 'True' || MODE_B2B == 'true') {
        $content .= '
           <div class="row">
            <div class="col-md-11">
              <div class="form-group row">
                <label for="' . $this->app->getDef('box_entry_newsletter_b2b') . '" class="col-9 col-form-label"><a href="' . $this->app->link('Newsletter') . '">' . $this->app->getDef('box_entry_newsletter_b2b') . '</a></label>
                <div class="col-md-3">
                  ' . $this->statAverageCustomersNewsletterB2B() . '
                </div>
              </div>
            </div>
          </div>
          ';
      }

//        $output .= '<div class="col-md-11 mainTable"></div>';

      $output = <<<EOD
  <!-- ######################## -->
  <!--  Start NewsletterAnonymous      -->
  <!-- ######################## -->
             {$content}
              <div class="col-md-11 mainTable"></div>
  <!-- ######################## -->
  <!--  Start NewsletterAnonymous      -->
  <!-- ######################## -->
EOD;
      return $output;
    }
  }
}