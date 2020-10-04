<?php
  /**
 *
 *  @copyright 2008 - https =>//www.clicshopping.org
 *  @Brand  => ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info  => https =>//www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Marketing\SEO\Classes\Shop\SeoShop;

  // start the timer for the page parse time log
  define('PAGE_PARSE_START_TIME', microtime());
  define('CLICSHOPPING_BASE_DIR', __DIR__ . '/includes/ClicShopping/');

  require_once(CLICSHOPPING_BASE_DIR . 'OM/CLICSHOPPING.php');
  spl_autoload_register('ClicShopping\OM\CLICSHOPPING::autoload');

  CLICSHOPPING::initialize();

  CLICSHOPPING::loadSite('Shop');

  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');

  if (!Registry::exists('SeoShop')) {
    Registry::set('SeoShop', new SeoShop());
  }

  $CLICSHOPPING_seoShop = Registry::get('SeoShop');

  $title = HTML::removeFileAccents($CLICSHOPPING_seoShop->getSeoIndexTitle());
  $description = HTML::removeFileAccents($CLICSHOPPING_seoShop->getSeoIndexDescription());

  $siteName = HTML::removeFileAccents(STORE_NAME);

  if(empty($title)) {
    $store_name = HTML::removeFileAccents(STORE_NAME);
    $shortName = substr($store_name, 0, 30);
  } else {
    $shortName = substr($title, 0, 30);
  }

  if(empty($description)) {
    $description = HTML::removeFileAccents(STORE_NAME);
  }

  $scope = HTTP::getShopUrlDomain() . 'index.php';
  $start_url = HTTP::getShopUrlDomain() . 'index.php';

  $image_192 =  HTTP::getShopUrlDomain() . 'sources/images/logos/manifest/logo_192.png';
  $image_512 =  HTTP::getShopUrlDomain() . 'sources/images/logos/manifest/logo_512.png';
  
  $code_langue = $CLICSHOPPING_Language->getCode();

  $manifest = [
    "dir" => "ltr",
    "lang" => "{$code_langue}",
    "name" => "{$siteName}",
    "short_name" => "{$shortName}",
    "description" => "{$description}",
    "scope" => "{$scope}",
    "display" => "standalone",
    "start_url" => "{$start_url}",
    "theme_color" => "#317EFB",
    "orientation" => "any",
    "background_color" => "#fff",
    "related_applications" => [],
    "prefer_related_applications" => false,
    "screenshots" => [],
    "generated" => "true",
    "icons" => [[
      "src" => "{$image_512}",
      "sizes"=> "192x192 512x512",
      "type" => "image/png",
      "purpose" => "maskable"
    ]],
    "src" => "{$image_512}",
    "sizes" => "512x512",
    "type" => "image/png"
  ];

   $json_manifest = json_encode($manifest);

   echo $json_manifest;
