<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\SEO\Sites\Shop\Pages\GoogleSitemap\Actions;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class GoogleSitemapSpecials extends \ClicShopping\OM\PagesActionsAbstract
{
  protected $use_site_template = false;
  protected $rewriteUrl;

  public function execute()
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $this->rewriteUrl = Registry::get('RewriteUrl');

    if (MODE_VENTE_PRIVEE == 'false') {
      $xml = new \SimpleXMLElement("<?xml version='1.0' encoding='UTF-8' ?>\n" . '<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9" />');

      $special_array = [];

      $Qproducts = $CLICSHOPPING_Db->prepare('select products_id,
                                                coalesce(NULLIF(specials_last_modified, :specials_last_modified),
                                                               specials_date_added) as last_modified
                                                from :table_specials
                                                where status = 1
                                                and customers_group_id = 0
                                                order by last_modified DESC
                                                ');

      $Qproducts->bindValue(':specials_last_modified', null);
      $Qproducts->execute();


      while ($Qproducts->fetch()) {
        $location = htmlspecialchars(CLICSHOPPING::utf8Encode($this->rewriteUrl->getProductNameUrl($Qproducts->valueInt('products_id'))), ENT_QUOTES | ENT_HTML5);

        $special_array[$Qproducts->valueInt('products_id')]['loc'] = $location;
        $special_array[$Qproducts->valueInt('products_id')]['lastmod'] = $Qproducts->value('last_modified');
        $special_array[$Qproducts->valueInt('products_id')]['changefreq'] = 'weekly';
        $special_array[$Qproducts->valueInt('products_id')]['priority'] = '0.5';
      }

      foreach ($special_array as $k => $v) {
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
