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

  namespace ClicShopping\Apps\Customers\Reviews\Module\Hooks\ClicShoppingAdmin\StatsDashboard;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Customers\Reviews\Reviews as ReviewsApp;

  class PageTabContent implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('Reviews')) {
        Registry::set('Reviews', new ReviewsApp());
      }

      $this->app = Registry::get('Reviews');

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/StatsDashboard/page_tab_content');
    }

    private function statsCountReviews()
    {
      $Qreviews = $this->app->db->prepare('select count(reviews_id) as count
                                           from :table_reviews
                                           where status = 0
                                           limit 1
                                          ');
      $Qreviews->execute();

      $review_total = $Qreviews->valueInt('count');

      return $review_total;
    }

    public function display()
    {

      if (!defined('CLICSHOPPING_APP_REVIEWS_RV_STATUS') || CLICSHOPPING_APP_REVIEWS_RV_STATUS == 'False') {
        return false;
      }

      if ($this->statsCountReviews() != 0) {
        $content = '
          <div class="row">
            <div class="col-md-11 mainTable">
              <div class="form-group row">
                <label for="' . $this->app->getDef('box_entry_reviews') . '" class="col-9 col-form-label"><a href="' . $this->app->link('Reviews') . '">' . $this->app->getDef('box_entry_reviews') . '</a></label>
                <div class="col-md-3">
                  ' . $this->statsCountReviews() . '
                </div>
              </div>
            </div>
          </div>
         ';

        $output = <<<EOD
  <!-- ######################## -->
  <!--  Start Customer Review      -->
  <!-- ######################## -->
             {$content}
  <!-- ######################## -->
  <!--  Start Customer Review      -->
  <!-- ######################## -->
EOD;
        return $output;
      }
    }
  }