<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Marketing\Recommendations\Module\Hooks\ClicShoppingAdmin\TopDashboard;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Marketing\Recommendations\Recommendations as RecommendationsApp;

  class DashboardTopRecommendations implements \ClicShopping\OM\Modules\HooksInterface
  {
    /**
     * @var bool|null
     */
    protected mixed $app;

    public function __construct()
    {
      if (!Registry::exists('Recommendations')) {
        Registry::set('Recommendations', new RecommendationsApp());
      }

      $this->app = Registry::get('Recommendations');

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/TopDashboard/dashboard_top_recommendations');
    }

    public function Display(): string
    {
      $Qrecommendations = $this->app->db->prepare('select count(id) as good_recommendations      
                                                   from :table_products_recommendations 
                                                   where score >= :score 
                                                   group by products_id

                                                  ');
      $Qrecommendations->execute();
      $Qrecommendations->bindDecimal(':score', (float)CLICSHOPPING_APP_RECOMMENDATIONS_PR_MIN_SCORE);

      $good_recommendations = $Qrecommendations->valueInt('good_recommendations');

      $QbRecommendations = $this->app->db->prepare('select count(id) as bad_recommendation 
                                                    from :table_products_recommendations 
                                                    where score < :score
                                                    ');
      $Qrecommendations->bindDecimal(':score', (float)CLICSHOPPING_APP_RECOMMENDATIONS_PR_MAX_SCORE);

      $QbRecommendations->execute();

      $bad_recommendations = $QbRecommendations->valueInt('bad_recommendation');

      $text = $this->app->getDef('text_recommendations');
      $text_view = $this->app->getDef('text_view');

      $output = '';

      if ($good_recommendations > 0 || $bad_recommendations > 0) {
        $output = '
<div class="col-md-2 col-12 m-1">
    <div class="card bg-danger">
      <div class="card-body">
        <div class="row">
          <h6 class="card-title text-white"><i class="bi bi-bag-heart-fill"></i> ' . $text . '</h6>
        </div>
        <div class="col-md-12">
          <span class="text-white"><strong>' . $good_recommendations . '  <i class="bi bi-emoji-smile-fill"></i>   /   ' . $bad_recommendations . '  <i class="bi bi-emoji-angry-fill"></i></strong></span>
          <span><small class="text-white">' . HTML::link(CLICSHOPPING::link(null, 'A&Marketing\Recommendations&Recommendations'), $text_view, 'class="text-white"') . '</small></span><br />
        </div>
      </div>
    </div>
</div>
';
      }

      return $output;
    }
  }