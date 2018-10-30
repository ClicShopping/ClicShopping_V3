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

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class GoogleSitemapFavorites extends \ClicShopping\OM\PagesActionsAbstract {

    protected $use_site_template = false;

    public function execute() {
      $CLICSHOPPING_Db = Registry::get('Db');

      if (MODE_VENTE_PRIVEE == 'false') {

        $xml = new \SimpleXMLElement("<?xml version='1.0' encoding='UTF-8' ?>\n".'<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9" />');

        $products_array = [];

        $Qproducts = $CLICSHOPPING_Db->prepare('select products_id,
                                         coalesce(NULLIF(products_favorites_last_modified, :products_favorites_last_modified),
                                                         products_favorites_date_added) as last_modified
                                          from :table_products_favorites
                                          where status = 1
                                          and customers_group_id = 0
                                          order by last_modified DESC
                                          ');

        $Qproducts->bindValue(':products_favorites_last_modified', '');
        $Qproducts->execute();

        while ($Qproducts->fetch() ) {
          $location =  htmlspecialchars(utf8_encode(CLICSHOPPING::link('index.php', 'Products&Description&products_id=' . $Qproducts->valueInt('products_id'))));

          $products_array[$Qproducts->valueInt('products_id')]['loc'] = $location;
          $products_array[$Qproducts->valueInt('products_id')]['lastmod'] = $Qproducts->value('last_modified');
          $products_array[$Qproducts->valueInt('products_id')]['changefreq'] = 'weekly';
          $products_array[$Qproducts->valueInt('products_id')]['priority'] = '0.5';
        }

        foreach ($products_array as $k => $v) {
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