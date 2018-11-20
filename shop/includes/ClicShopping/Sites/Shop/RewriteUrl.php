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

  namespace ClicShopping\Sites\Shop;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class RewriteUrl {

    protected $title;

    public function __construct() {
    }

/**
 * Remove url accent
 * @param $str
 * @param string $charset
 * @return null|string|string[]
 */
    protected function getSkipAccents( $str, $charset='utf-8' ) {
      $str = htmlentities( $str, ENT_NOQUOTES, $charset );
      $str = preg_replace( '#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str );
      $str = preg_replace( '#&([A-za-z]{2})(?:lig);#', '\1', $str );
      $str = preg_replace( '#&[^;]+;#', '', $str );

      return $str;
    }

/**
 * @param $products_id
 * @param string $parameters, url parameters
 * @return string
 */
    public function getProductNameUrl($products_id, $parameters = '') {
      $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');

      if (defined('SEARCH_ENGINE_FRIENDLY_URLS') && SEARCH_ENGINE_FRIENDLY_URLS == 'true'  && CLICSHOPPING::getSite() != 'ClicShoppingAdmin') {
        if (defined('SEARCH_ENGINE_FRIENDLY_URLS_PRO') && SEARCH_ENGINE_FRIENDLY_URLS_PRO == 'true') {
          $products_name = $CLICSHOPPING_ProductsCommon->getProductsName($products_id);
          $products_name = str_replace(' ', '-', $products_name);
          $products_name = $this->getSkipAccents($products_name);
          $products_url_rewrited = 'Products&Description&' . $products_name . '&products_id=' . (int)$products_id;
        } else {
          $products_url_rewrited = 'Products&Description&products_id=' . (int)$products_id;
        }

      } else {
        $products_url_rewrited = 'Products&Description&products_id=' . (int)$products_id;
      }

      $url = CLICSHOPPING::link(null, $products_url_rewrited . $parameters);

      return $url;
    }

/**
 * @param $page_id , id of the content
 * @param string $parameters, url parameters
 * @return string
 */

    public function getPageManagerContentUrl($page_id, $parameters = '') {
      $CLICSHOPPING_PageManagerShop = Registry::get('PageManagerShop');

      if (defined('SEARCH_ENGINE_FRIENDLY_URLS') && SEARCH_ENGINE_FRIENDLY_URLS == 'true'  && CLICSHOPPING::getSite() != 'ClicShoppingAdmin') {
        if (defined('SEARCH_ENGINE_FRIENDLY_URLS_PRO') && SEARCH_ENGINE_FRIENDLY_URLS_PRO == 'true') {
          $page_title = $CLICSHOPPING_PageManagerShop->pageManagerDisplayTitle($page_id);
          $page_title = str_replace(' ', '-', $page_title);
          $page_title = $this->getSkipAccents($page_title);
          $content_url_rewrited = 'Info&Content&' . $page_title . '&pages_id=' . (int)$page_id;
        } else {
          $content_url_rewrited = 'Info&Content&pages_id=' . (int)$page_id;
        }
      } else {
        $content_url_rewrited = 'Info&Content&pages_id=' . (int)$page_id;
      }

      $url = CLICSHOPPING::link(null, $content_url_rewrited . $parameters);

      return $url;
    }

/**
 * categoryTree title
 * @param $title
 * @return mixed
 */
    public function getCategoryTreeTitle($title) {
      $this->title = $title;

      return $title;
    }


/**
 * @param $categories_id , id of the categories
 * @param string $parameters, url parameters
 * @return string
 */

    public function getCategoryTreeUrl($categories_id, $parameters = '') {
      if (defined('SEARCH_ENGINE_FRIENDLY_URLS') && SEARCH_ENGINE_FRIENDLY_URLS == 'true'  && CLICSHOPPING::getSite() != 'ClicShoppingAdmin') {
        if (defined('SEARCH_ENGINE_FRIENDLY_URLS_PRO') && SEARCH_ENGINE_FRIENDLY_URLS_PRO == 'true') {
          $link_title = $this->title;
          $link_title = str_replace(' ', '-', $link_title);
          $link_title = $this->getSkipAccents($link_title);

          $categories_url_rewrited = $link_title . '&cPath=' . $categories_id;
        } else {
          $categories_url_rewrited = 'cPath=' . $categories_id;
        }
      } else {
        $categories_url_rewrited = 'cPath=' . $categories_id;
      }

      $url = CLICSHOPPING::link(null, $categories_url_rewrited . $parameters);

      return $url;
    }

/**
 * @param $categories_id , id of the categories
 * @param string $parameters, url parameters
 * @return string
 */

    public function getCategoryImageUrl($categories_id, $parameters = '') {
      if (defined('SEARCH_ENGINE_FRIENDLY_URLS') && SEARCH_ENGINE_FRIENDLY_URLS == 'true'  && CLICSHOPPING::getSite() != 'ClicShoppingAdmin') {
        if (defined('SEARCH_ENGINE_FRIENDLY_URLS_PRO') && SEARCH_ENGINE_FRIENDLY_URLS_PRO == 'true') {
          $link_title = $this->title;
          $link_title = str_replace(' ', '-', $link_title);
          $link_title = $this->getSkipAccents($link_title);

          $categories_url_rewrited = $link_title . '&' . $categories_id;
        } else {
          $categories_url_rewrited = 'cPath=' . $categories_id;
        }
      } else {
        $categories_url_rewrited = 'cPath=' . $categories_id;
      }

      $url = CLICSHOPPING::link(null, $categories_url_rewrited . $parameters);

      return $url;
    }

/**
 * manufacturer url
 * @param $manufactuer_id , manufacturer id
 * @param string $parameters, url parameters
 * @return string
 */

    public function getManufacturerUrl($manufacturer_id, $parameters = '') {
      $CLICSHOPPING_Manufacturers = Registry::get('Manufacturers');

      if (defined('SEARCH_ENGINE_FRIENDLY_URLS') && SEARCH_ENGINE_FRIENDLY_URLS == 'true'  && CLICSHOPPING::getSite() != 'ClicShoppingAdmin') {
        if (defined('SEARCH_ENGINE_FRIENDLY_URLS_PRO') && SEARCH_ENGINE_FRIENDLY_URLS_PRO == 'true') {
          $manufacturer_title = $CLICSHOPPING_Manufacturers->getTitle($manufacturer_id);
          $manufacturer_title = str_replace(' ', '-', $manufacturer_title);
          $manufacturer_title = $this->getSkipAccents($manufacturer_title);

          $manufacturer_url_rewrited = $manufacturer_title . '&manufacturers_id=' . (int)$manufacturer_id;
        } else {
          $manufacturer_url_rewrited = 'manufacturers_id=' . (int)$manufacturer_id;
        }
      } else {
        $manufacturer_url_rewrited = 'manufacturers_id=' . (int)$manufacturer_id;
      }

      $url = CLICSHOPPING::link(null, $manufacturer_url_rewrited . $parameters);

      return $url;
    }

/**
 * manufacturer url
 * @param $manufactuer_id , manufacturer id
 * @param string $parameters, url parameters
 * @return string
 */

    public function getBlogContentUrl($blog_content_id, $parameters = '') {
/*
      if (defined('SEARCH_ENGINE_FRIENDLY_URLS') && SEARCH_ENGINE_FRIENDLY_URLS == 'true'  && CLICSHOPPING::getSite() != 'ClicShoppingAdmin') {
        if (defined('SEARCH_ENGINE_FRIENDLY_URLS_PRO') && SEARCH_ENGINE_FRIENDLY_URLS_PRO == 'true') {
//CLICSHOPPING::link(null, 'Blog&Content&blog_content_id =' . (int)$_GET['blog_content_id']
          $blog_title = $CLICSHOPPING_BlogContent->getBlogContentName($manufacturer_id);
          $blog_title = str_replace(' ', '-', $blog_title);
          $blog_title = $this->getSkipAccents($blog_title);

          $blog_url_rewrited = $blog_title . '&blog_content_id=' . (int)$blog_content_id;
        } else {
          $blog_url_rewrited = 'Blog&Content&blog_content_id=' . (int)$blog_content_id;
        }
      } else {
        $blog_url_rewrited = 'Blog&Content&blog_content_id=' . (int)$blog_content_id;
      }

      $url = CLICSHOPPING::link(null, $blog_url_rewrited . $parameters);

      return $url;
      }
*/
    }

/**
 * Blog categories url
 * @param $manufactuer_id , manufacturer id
 * @param string $parameters, url parameters
 * @return string
 */

    public function getBlogCategoriesUrl($id, $parameters = '') {
/*
      if (defined('SEARCH_ENGINE_FRIENDLY_URLS') && SEARCH_ENGINE_FRIENDLY_URLS == 'true'  && CLICSHOPPING::getSite() != 'ClicShoppingAdmin') {
        if (defined('SEARCH_ENGINE_FRIENDLY_URLS_PRO') && SEARCH_ENGINE_FRIENDLY_URLS_PRO == 'true') {

      //CLICSHOPPING::link(null, 'Blog&Categories&cPath= =' . (int)$_GET['$id']
          $blog_title = $CLICSHOPPING_BlogContent->getBlogContentName($id);
          $blog_title = str_replace(' ', '-', $blog_title);
          $blog_title = $this->getSkipAccents($blog_title);

          $blog_url_rewrited = $blog_title . '&blog_content_id=' . (int)$id;
        } else {
          $blog_url_rewrited = 'Blog&Categories&cPath=' . (int)$id;
        }
      } else {
        $blog_url_rewrited = 'Blog&Categories&cPath=' . (int)$id;
      }

       $url = CLICSHOPPING::link(null, $blog_url_rewrited . $parameters);
       return $url;
      }
*/
    }
  }