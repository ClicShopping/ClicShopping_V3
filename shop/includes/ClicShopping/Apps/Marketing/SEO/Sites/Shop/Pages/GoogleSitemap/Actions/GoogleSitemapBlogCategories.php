<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4 
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Apps\Marketing\SEO\Sites\Shop\Pages\GoogleSitemap\Actions;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class GoogleSitemapBlogCategories extends \ClicShopping\OM\PagesActionsAbstract {

    protected $use_site_template = false;

    public function execute() {
      $this->page->setUseSiteTemplate(false); //don't display Header / Footer

      $CLICSHOPPING_Db = Registry::get('Db');

      if (MODE_VENTE_PRIVEE == 'false') {

        $xml = new \SimpleXMLElement("<?xml version='1.0' encoding='UTF-8' ?>\n".'<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9" />');

        $products_array = [];

        $Qproducts = $CLICSHOPPING_Db->prepare('select blog_categories_id,
                                                coalesce(NULLIF(last_modified, :last_modified),
                                                               date_added) as last_modified
                                                from :table_blog_categories
                                                where customers_group_id = 0
                                                group by blog_categories_id
                                                order by last_modified desc
                                              ');

        $Qproducts->bindValue(':last_modified', '');
        $Qproducts->execute();

        while($Qproducts->fetch() ) {

          $location =  htmlspecialchars(utf8_encode(CLICSHOPPING::link(null, 'Blog&Categories&amp;current=' . $Qproducts->valueInt('blog_categories_id'))));

          $products_array[$Qproducts->valueInt('blog_categories_id')]['loc'] = $location;
          $products_array[$Qproducts->valueInt('blog_categories_id')]['lastmod'] = $Qproducts->value('last_modified');
          $products_array[$Qproducts->valueInt('blog_categories_id')]['changefreq'] = 'weekly';
          $products_array[$Qproducts->valueInt('blog_categories_id')]['priority'] = '0.5';
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