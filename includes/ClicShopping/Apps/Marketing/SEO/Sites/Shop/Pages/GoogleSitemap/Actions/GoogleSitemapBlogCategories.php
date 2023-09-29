<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\SEO\Sites\Shop\Pages\GoogleSitemap\Actions;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class GoogleSitemapBlogCategories extends \ClicShopping\OM\PagesActionsAbstract
{
  protected $use_site_template = false;

  public function execute()
  {
    $this->page->setUseSiteTemplate(false); //don't display Header / Footer

    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Language = Registry::get('Language');

    if (MODE_VENTE_PRIVEE == 'false') {

      $xml = new \SimpleXMLElement("<?xml version='1.0' encoding='UTF-8' ?>\n" . '<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9" />');

      $products_array = [];

      $QBlogCategories = $CLICSHOPPING_Db->prepare('select bc.blog_categories_id,
                                                             bcd.blog_categories_name,
                                                      coalesce(NULLIF(last_modified, :last_modified),
                                                                     date_added) as last_modified
                                                      from :table_blog_categories bc,
                                                           :table_blog_categories_description bcd
                                                      where bc.customers_group_id = 0                                                
                                                      and bcd.blog_categories_id = bc.blog_categories_id
                                                      and bcd.language_id = :language_id
                                                      group by bc.blog_categories_id
                                                      order by bc.last_modified DESC
                                                    ');

      $QBlogCategories->bindValue(':last_modified', null);
      $QBlogCategories->bindInt(':language_id', $CLICSHOPPING_Language->getId());
      $QBlogCategories->execute();

      while ($QBlogCategories->fetch()) {

//          $this->rewriteUrl->getCategoryTreeTitle($QBlogCategories->value('blog_categories_name'));
//          $location =  htmlspecialchars(CLICSHOPPING::utf8Encode($this->rewriteUrl->getBlogCategoriesUrl($QBlogCategories->valueInt('blog_categories_id'))));
        $location = htmlspecialchars(CLICSHOPPING::utf8Encode(CLICSHOPPING::link(null, 'Blog&Categories&amp;current=' . $QBlogCategories->valueInt('blog_categories_id'))), ENT_QUOTES | ENT_HTML5);

        $products_array[$QBlogCategories->valueInt('blog_categories_id')]['loc'] = $location;
        $products_array[$QBlogCategories->valueInt('blog_categories_id')]['lastmod'] = $QBlogCategories->value('last_modified');
        $products_array[$QBlogCategories->valueInt('blog_categories_id')]['changefreq'] = 'weekly';
        $products_array[$QBlogCategories->valueInt('blog_categories_id')]['priority'] = '0.5';
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