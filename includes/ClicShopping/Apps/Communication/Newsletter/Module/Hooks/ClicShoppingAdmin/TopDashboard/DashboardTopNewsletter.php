<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Communication\Newsletter\Module\Hooks\ClicShoppingAdmin\TopDashboard;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Communication\Newsletter\Newsletter as NewsletterApp;

  class DashboardTopNewsletter implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;

    public function __construct()
    {
      if (!Registry::exists('Newsletter')) {
        Registry::set('Newsletter', new NewsletterApp());
      }

      $this->app = Registry::get('Newsletter');

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/TopDashboard/dashboard_top_newsletter');
    }

    public function Display(): string
    {
      $Qustomers = $this->app->db->prepare('select count(customers_id) as count 
                                            from :table_customers 
                                            where customers_newsletter = 1
                                           ');
      $Qustomers->execute();

      $number_customers_newsletter = $Qustomers->valueInt('count');
      $output = '';

      if ($number_customers_newsletter > 0) {
        $text = $this->app->getDef('text_newsletter');
        $text_view = $this->app->getDef('text_view');

        $output = '
<div class="col-md-2 col-12 m-1">
    <div class="card bg-info">
      <div class="card-body">
        <div class="row">
          <h6 class="card-title text-white"><i class="bi bi-bell-fill"></i> ' . $text . '</h6>
        </div>
        <div class="col-md-12">
          <span class="text-white"><strong>' . $number_customers_newsletter . '</strong></span>
          <span><small class="text-white">' . HTML::link(CLICSHOPPING::link(null, 'A&Communication\Newsletter&Newsletter'), $text_view, 'class="text-white"') . '</small></span>
        </div>
      </div>
    </div>
</div>
';
      }

      return $output;
    }
  }