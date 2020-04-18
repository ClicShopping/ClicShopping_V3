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

  namespace ClicShopping\Apps\Customers\Reviews\Module\Hooks\ClicShoppingAdmin\TopDashboard;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Customers\Reviews\Reviews as ReviewsApp;

  class DashboardTopReviews implements \ClicShopping\OM\Modules\HooksInterface
  {
    /**
     * @var bool|null
     */
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('Reviews')) {
        Registry::set('Reviews', new ReviewsApp());
      }

      $this->app = Registry::get('Reviews');

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/TopDashboard/dashboard_top_reviews');
    }

    public function Display(): string
    {
      $Qreviews = $this->app->db->prepare('select count(reviews_id) as num_reviews 
                                          from :table_reviews 
                                          where date_added >= (now() - INTERVAL 2 month) 
                                          and status = 0
                                          ');
      $Qreviews->execute();

      $number_of_reviews = $Qreviews->valueInt('num_reviews');

      $text = $this->app->getDef('text_number_of_reviews');
      $text_view = $this->app->getDef('text_view');

      $output = '';

      if ($number_of_reviews > 0) {
        $output = '
<span style="padding-right:0.5rem; padding-top:0.5rem">
    <div class="card bg-success">
      <div class="card-body">
        <div class="row">
          <h5 class="card-title text-white"><i class="far fa-comment"  aria-hidden="true"></i> ' . $text . '</h5>
        </div>
        <div class="col-md-12">
          <span class="text-white"><strong>' . $number_of_reviews . '</strong></span>
          <span><small class="text-white">' . HTML::link(CLICSHOPPING::link(null, 'A&Customers\Reviews&Reviews'), $text_view, 'class="text-white"') . '</small></span>
        </div>
      </div>
    </div>
</span>
';
      }

      return $output;
    }
  }