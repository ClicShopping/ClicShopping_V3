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

  namespace ClicShopping\Apps\Customers\Reviews\Module\ClicShoppingAdmin\Dashboard;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\DateTime;

  use ClicShopping\Apps\Customers\Reviews\Reviews as ReviewsApp;

  class Reviews extends \ClicShopping\OM\Modules\AdminDashboardAbstract
  {
    protected $lang;
    protected $app;
    public $group;

    protected function init()
    {
      if (!Registry::exists('Reviews')) {
        Registry::set('Reviews', new ReviewsApp());
      }

      $this->app = Registry::get('Reviews');
      $this->lang = Registry::get('Language');

      $this->app->loadDefinitions('Module/ClicShoppingAdmin/Dashboard/reviews');

      $this->title = $this->app->getDef('module_admin_dashboard_reviews_app_title');
      $this->description = $this->app->getDef('module_admin_dashboard_reviews_app_description');

      if (\defined('MODULE_ADMIN_DASHBOARD_REVIEWS_APP_STATUS')) {
        $this->sort_order = (int)MODULE_ADMIN_DASHBOARD_REVIEWS_APP_SORT_ORDER;
        $this->enabled = (MODULE_ADMIN_DASHBOARD_REVIEWS_APP_STATUS == 'True');
      }
    }

    public function getOutput()
    {
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

      $content_width = 'col-md-' . (int)MODULE_ADMIN_DASHBOARD_REVIEWS_APP_CONTENT_WIDTH;

      $content = '<div class="' . $content_width . '">';
      $content .= '<div class="separator"></div>';
      $content .= '<table 
        id="table"
        data-toggle="table"
        data-sort-name="added"
        data-sort-order="asc"
        data-toolbar="#toolbar"
        data-buttons-class="primary"
        data-show-toggle="true"
        data-show-columns="true"
        data-mobile-responsive="true">';
      $content .= '<thead class="dataTableHeadingRow">';
      $content .= '<tr>';

      $content .= '
          <th data-field="products" scope="col"> ' . $this->app->getDef('table_heading_products') . '</th>
          <th data-field="added" data-sortable="true" scope="col">' . $this->app->getDef('table_heading_date_added') . '</th>
          <th data-field="author" scope="col"> ' . $this->app->getDef('table_heading_review_author') . '</th>
          <th data-field="rating" scope="col">' . $this->app->getDef('table_heading_rating') . '</th>                    
          <th data-field="approved" scope="col"> ' . $this->app->getDef('table_heading_approved') . '</th>
          <th data-field="action" data-switchable="false" class="text-end">' . $this->app->getDef('table_heading_action') . '&nbsp;</th>
        </tr>
      ';

      $content .= '</thead>';
      $content .= '<tbody>';

      $Qreviews = $this->app->db->prepare('select r.reviews_id, 
                                                  r.date_added,
                                                  r.customers_name, 
                                                  r.reviews_rating, 
                                                  r.status,
                                                  pd.products_name 
                                            from :table_reviews r, 
                                                 :table_products_description pd 
                                             where pd.products_id = r.products_id 
                                            and pd.language_id = :language_id
                                            and r.status = 0
                                            order by r.date_added desc limit 6
                                          ');

      $Qreviews->bindInt(':language_id', $CLICSHOPPING_Language->getID());
      $Qreviews->bindInt(':language_id', $CLICSHOPPING_Language->getID());
      $Qreviews->execute();

      while ($Qreviews->fetch()) {
        if ($Qreviews->valueInt('status') == 1) {
          $status_icon = HTML::link($this->app->link('Reviews&SetFlag&flag=0&id=' . $Qreviews->valueInt('reviews_id')), '<i class="bi-check text-success"></i>');
        } else {
          $status_icon = HTML::link($this->app->link('Reviews&SetFlag&flag=1&id=' . $Qreviews->valueInt('reviews_id')), '<i class="bi bi-x text-danger"></i>');
        }

        $content .= '<tr class="dataTableRow backgroundBlank">' .
          '    <td class="dataTableContent">' . HTML::outputProtected($Qreviews->value('products_name')) . '</td>' .
          '    <td class="dataTableContent">' . DateTime::toShort($Qreviews->value('date_added')) . '</td>' .
          '    <td class="dataTableContent">' . HTML::outputProtected($Qreviews->value('customers_name')) . '</td>' .
          '    <td class="dataTableContent"><i>' . HTML::stars($Qreviews->valueInt('reviews_rating')) . '</i></td>' .
          '    <td class="dataTableContent text-center">' . $status_icon . '</td>' .
          '   <td class="dataTableContent text-end">' . HTML::link($this->app->link('&Edit&page=' . (int)$_GET['page'] . '&rID=' . $Qreviews->valueInt('reviews_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $this->app->getDef('icon_edit'))) . '</td>' .
          '  </tr>';

        $content .= ' </tbody>';
        $content .= '</table>';
        $content .= '</div>';
        $content .= '</div>';

        $output = <<<EOD
  <!-- ######################## -->
  <!--  Start Reviews     -->
  <!-- ######################## -->
             {$content}
  <!-- ######################## -->
  <!--  Start Reviews      -->
  <!-- ######################## -->
EOD;

        return $output;
      }
    }

    public function Install()
    {
      $this->app->db->save('configuration', [
          'configuration_title' => 'Do you want to enable this Module ?',
          'configuration_key' => 'MODULE_ADMIN_DASHBOARD_REVIEWS_APP_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this Module ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $this->app->db->save('configuration', [
          'configuration_title' => 'Select the width to display',
          'configuration_key' => 'MODULE_ADMIN_DASHBOARD_REVIEWS_APP_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Select a number between 1 to 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $this->app->db->save('configuration', [
          'configuration_title' => 'Sort Order',
          'configuration_key' => 'MODULE_ADMIN_DASHBOARD_REVIEWS_APP_SORT_ORDER',
          'configuration_value' => '45',
          'configuration_description' => 'Sort order of display. Lowest is displayed first.',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );
    }

    public function keys()
    {
      return ['MODULE_ADMIN_DASHBOARD_REVIEWS_APP_STATUS',
        'MODULE_ADMIN_DASHBOARD_REVIEWS_APP_CONTENT_WIDTH',
        'MODULE_ADMIN_DASHBOARD_REVIEWS_APP_SORT_ORDER'
      ];
    }
  }
