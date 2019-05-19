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

  namespace ClicShopping\Apps\Marketing\SEO\Module\Hooks\ClicShoppingAdmin\PageManager;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Marketing\SEO\Classes\ClicShoppingAdmin\SeoReport;

  use ClicShopping\Apps\Marketing\SEO\SEO as SEOApp;

  class PageTab implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;
    protected $lang;
    protected $db;
    protected $template;

    public function __construct()
    {
      if (!Registry::exists('SEO')) {
        Registry::set('SEO', new SEOApp());
      }

      $this->app = Registry::get('SEO');
      $this->lang = Registry::get('Language');
      $this->db = Registry::get('Db');

      $this->template = Registry::get('TemplateAdmin');
    }

    public function display()
    {
      if (!defined('CLICSHOPPING_APP_SEO_SE_STATUS') || CLICSHOPPING_APP_SEO_SE_STATUS == 'False') {
        return false;
      }

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/PageManager/page_tab');

      if (isset($_GET['Edit'])) {

        if (isset($_GET['bID'])) $bId = HTML::sanitize($_GET['bID']);
        if (isset($_POST['page_type'])) $bId = HTML::sanitize($_POST['page_type']);

        if ($bId == 2) {
          $link_url = HTTP::getShopUrlDomain();
        } else {
          $link_url = HTTP::getShopUrlDomain() . 'index.php?Info&Content&pages_id=' . (int)$bId;
        }


        $url_site = HTTP::getShopUrlDomain();

        $this->Report = new SeoReport($link_url, $url_site);

        $report = $this->Report->getSeoReport();

        $content = '<!-- SEO Page report -->';

        if (isset($report)) {
          $content .= $report;

          $tab_title = $this->app->getDef('tab_seo_report');
          $title = $this->app->getDef('tab_seo_report');

          $output = <<<EOD
<!-- ######################## -->
<!-- Start Report SEO APP  -->
<!-- ######################## -->
<div class="tab-pane" id="section_SEOReportApp_content">
  <div class="mainTitle">
    <span class="col-md-10">{$title}</span>
  </div>
  {$content}
</div>

<script>
$('#section_SEOReportApp_content').appendTo('#pageManagerTabs .tab-content');
$('#pageManagerTabs .nav-tabs').append('    <li class="nav-item"><a data-target="#section_SEOReportApp_content" role="tab" data-toggle="tab" class="nav-link">{$tab_title}</a></li>');
</script>
<!-- ######################## -->
<!--  End eport APP   -->
<!-- ######################## -->
EOD;

          return $output;
        }
      }
    }
  }
