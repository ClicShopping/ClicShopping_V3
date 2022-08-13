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

  class GoogleSitemapIndex extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $use_site_template = false;

    public function execute()
    {
      $this->page->setUseSiteTemplate(false); //don't display Header / Footer

      if (MODE_VENTE_PRIVEE == 'false') {
        $xml = new \SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?>\n" . '<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9" />');

        $location = CLICSHOPPING::link(null, 'Sitemap&GoogleSitemapCategories');
        $url = $xml->addChild('url');
        $url->addChild('loc', htmlspecialchars(utf8_encode($location), ENT_QUOTES | ENT_HTML5));
        $url->addChild('lastmod', date("Y-m-d", strtotime("now")));
        $url->addChild('changefreq', 'weekly');
        $url->addChild('priority', '0.5');

        $location = CLICSHOPPING::link(null, 'Sitemap&GoogleSitemapProducts');
        $url = $xml->addChild('url');
        $url->addChild('loc', htmlspecialchars(utf8_encode($location), ENT_QUOTES | ENT_HTML5));
        $url->addChild('lastmod', date("Y-m-d", strtotime("now")));
        $url->addChild('changefreq', 'weekly');
        $url->addChild('priority', '0.5');

        $location = CLICSHOPPING::link(null, 'Sitemap&GoogleSitemapSpecials');
        $url = $xml->addChild('url');
        $url->addChild('loc', htmlspecialchars(utf8_encode($location), ENT_QUOTES | ENT_HTML5));
        $url->addChild('lastmod', date("Y-m-d", strtotime("now")));
        $url->addChild('changefreq', 'weekly');
        $url->addChild('priority', '0.5');

        $location = CLICSHOPPING::link(null, 'Sitemap&GoogleSitemapFavorites');
        $url = $xml->addChild('url');
        $url->addChild('loc', htmlspecialchars(utf8_encode($location), ENT_QUOTES | ENT_HTML5));
        $url->addChild('lastmod', date("Y-m-d", strtotime("now")));
        $url->addChild('changefreq', 'weekly');
        $url->addChild('priority', '0.5');

        $location = CLICSHOPPING::link(null, 'Sitemap&GoogleSitemapManufacturers');
        $url = $xml->addChild('url');
        $url->addChild('loc', htmlspecialchars(utf8_encode($location), ENT_QUOTES | ENT_HTML5));
        $url->addChild('lastmod', date("Y-m-d", strtotime("now")));
        $url->addChild('changefreq', 'weekly');
        $url->addChild('priority', '0.5');

        $location = CLICSHOPPING::link(null, 'Sitemap&GoogleSitemapBlogCategories');
        $url = $xml->addChild('url');
        $url->addChild('loc', htmlspecialchars(utf8_encode($location), ENT_QUOTES | ENT_HTML5));
        $url->addChild('lastmod', date("Y-m-d", strtotime("now")));
        $url->addChild('changefreq', 'weekly');
        $url->addChild('priority', '0.5');

        $location = CLICSHOPPING::link(null, 'Sitemap&GoogleSitemapBlogContent');
        $url = $xml->addChild('url');
        $url->addChild('loc', htmlspecialchars(utf8_encode($location), ENT_QUOTES | ENT_HTML5));
        $url->addChild('lastmod', date("Y-m-d", strtotime("now")));
        $url->addChild('changefreq', 'weekly');
        $url->addChild('priority', '0.5');

        $location = CLICSHOPPING::link(null, 'Sitemap&GoogleSitemapPageManager');
        $url = $xml->addChild('url');
        $url->addChild('loc', htmlspecialchars(utf8_encode($location), ENT_QUOTES | ENT_HTML5));
        $url->addChild('lastmod', date("Y-m-d", strtotime("now")));
        $url->addChild('changefreq', 'weekly');
        $url->addChild('priority', '0.5');

        $location = CLICSHOPPING::link(null, 'Sitemap&GoogleSitemapFeatured');
        $url = $xml->addChild('url');
        $url->addChild('loc', htmlspecialchars(utf8_encode($location), ENT_QUOTES | ENT_HTML5));
        $url->addChild('lastmod', date("Y-m-d", strtotime("now")));
        $url->addChild('changefreq', 'weekly');
        $url->addChild('priority', '0.5');

        header('Content-type: text/xml');
        echo $xml->asXML();
        exit;
      }
    }
  }