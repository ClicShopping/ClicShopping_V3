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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class GoogleSitemapCategories extends \ClicShopping\OM\PagesActionsAbstract {

    protected $use_site_template = false;

    public function execute() {

      $CLICSHOPPING_Db = Registry::get('Db');

      $xml = new \SimpleXMLElement("<?xml version='1.0' encoding='UTF-8' ?>\n".'<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9" />');

      $category_array = [];

      $Qcategorie = $CLICSHOPPING_Db->prepare('select categories_id,
                                         coalesce(NULLIF(last_modified, :last_modified),
                                                         date_added) as last_modified
                                          from :table_categories
                                          where virtual_categories = :virtual_categories
                                          group by categories_id
                                          order by last_modified DESC
                                          ');

      $Qcategorie->bindValue(':last_modified', '');
      $Qcategorie->bindValue(':virtual_categories', '0');
      $Qcategorie->execute();

      while ($Qcategorie->fetch() ) {
        $location =  htmlspecialchars(utf8_encode(CLICSHOPPING::link(null, 'cPath=' . $Qcategorie->valueInt('categories_id'))));

        $category_array[$Qcategorie->valueInt('categories_id')]['loc'] = $location;
        $category_array[$Qcategorie->valueInt('categories_id')]['lastmod'] = $Qcategorie->value('last_modified');
        $category_array[$Qcategorie->valueInt('categories_id')]['changefreq'] = 'weekly';
        $category_array[$Qcategorie->valueInt('categories_id')]['priority'] = '0.5';
      }

      foreach ($category_array as $k => $v) {
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