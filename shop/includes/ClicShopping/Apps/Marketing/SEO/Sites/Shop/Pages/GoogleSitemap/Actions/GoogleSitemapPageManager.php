<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

  namespace ClicShopping\Apps\Marketing\SEO\Sites\Shop\Pages\GoogleSitemap\Actions;

  use ClicShopping\OM\Registry;

  class GoogleSitemapPageManager extends \ClicShopping\OM\PagesActionsAbstract {

    protected $use_site_template = false;
    protected $rewriteUrl;

    public function execute() {
      $CLICSHOPPING_Db = Registry::get('Db');
      $this->rewriteUrl = Registry::get('RewriteUrl');

      if (MODE_VENTE_PRIVEE == 'false') {

        $xml = new \SimpleXMLElement("<?xml version='1.0' encoding='UTF-8' ?>\n".'<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9" />');

        $page_manager_array = [];

        $QpageManager = $CLICSHOPPING_Db->prepare('select pages_id,
                                                         page_type,
                                                   coalesce(NULLIF(last_modified, :last_modified),
                                                                   date_added) as last_modified
                                                    from :table_pages_manager
                                                    where status = 1
                                                    and customers_group_id = 0
                                                    and page_type = 3
                                                    order by last_modified desc
                                                   ');

        $QpageManager->bindValue(':last_modified', '');
        $QpageManager->execute();

        while ($QpageManager->fetch() ) {
          $location =  htmlspecialchars(utf8_encode($this->rewriteUrl->getPageManagerContentUrl($QpageManager->valueInt('pages_id'))));

          $page_manager_array[$QpageManager->valueInt('pages_id')]['loc'] = $location;
          $page_manager_array[$QpageManager->valueInt('pages_id')]['lastmod'] = $QpageManager->value('last_modified');
          $page_manager_array[$QpageManager->valueInt('pages_id')]['changefreq'] = 'weekly';
          $page_manager_array[$QpageManager->valueInt('pages_id')]['priority'] = '0.5';
        }

        foreach ($page_manager_array as $k => $v) {
          $url = $xml->addChild('url');
          $url->addChild('loc', $v['loc']);
          $url->addChild('lastmod', date("Y-m-d", strtotime($v['lastmod'])));
          $url->addChild('changefreq', 'weekly');
          $url->addChild('priority', '0.5');
        }

        header('Content-type: text/xml');
        echo $xml->asXML();
        exit;
      }
    }
  }

