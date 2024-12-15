<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Sites\Shop;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;
use Transliterator;
use function defined;
use function is_null;

/**
 * Class RewriteUrl
 *
 * Provides functionality for URL rewriting, including encoding and decoding UTF-8 strings
 * and converting accented characters to ASCII equivalents.
 */
class RewriteUrl
{
  protected string|null $title = null;

  /**
   * Determines if a given string appears to be encoded in UTF-8.
   *
   * @param string $str The string to check for UTF-8 encoding.
   * @return bool Returns true if the string seems to be in UTF-8 encoding, otherwise false.
   */
  private static function seemsUtf8(string $str): bool
  {
    if (preg_match('!\S!u', $str)) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Removes accents from a given string based on the specified locale or the default locale if not provided.
   *
   * @param string $string The input string from which accents will be removed.
   * @param string $locale Optional. The locale to be used for accent removal. Defaults to the system's locale.
   * @return string Returns the processed string without accents.
   */
  private static function getRemoveAccents(string $string, string $locale = ''): string
  {
    $CLICSHOPPING_Language = Registry::get('Language');

    if (empty($locale)) {
      $locale = $CLICSHOPPING_Language->getLocale();
    }

    if (!preg_match('/[\x80-\xff]/', $string)) {
      return $string;
    }

    if (static::seemsUtf8($string)) {
    /*
    * Unicode sequence normalization from NFD (Normalization Form Decomposed)
    * to NFC (Normalization Form [Pre]Composed), the encoding used in this function.
    */
      if ( function_exists( 'normalizer_is_normalized' ) && function_exists( 'normalizer_normalize' )) {
        if (!normalizer_is_normalized($string)) {
          $string = normalizer_normalize($string);
        }
      }

      $chars = [
        // Decompositions for Latin-1 Supplement
        'ª' => 'a',
        'º' => 'o',
        'À' => 'A',
        'Á' => 'A',
        'Â' => 'A',
        'Ã' => 'A',
        'Ä' => 'A',
        'Å' => 'A',
        'Æ' => 'AE',
        'Ç' => 'C',
        'È' => 'E',
        'É' => 'E',
        'Ê' => 'E',
        'Ë' => 'E',
        'Ì' => 'I',
        'Í' => 'I',
        'Î' => 'I',
        'Ï' => 'I',
        'Ð' => 'D',
        'Ñ' => 'N',
        'Ò' => 'O',
        'Ó' => 'O',
        'Ô' => 'O',
        'Õ' => 'O',
        'Ö' => 'O',
        'Ù' => 'U',
        'Ú' => 'U',
        'Û' => 'U',
        'Ü' => 'U',
        'Ý' => 'Y',
        'Þ' => 'TH',
        'ß' => 's',
        'à' => 'a',
        'á' => 'a',
        'â' => 'a',
        'ã' => 'a',
        'ä' => 'a',
        'å' => 'a',
        'æ' => 'ae',
        'ç' => 'c',
        'è' => 'e',
        'é' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'ì' => 'i',
        'í' => 'i',
        'î' => 'i',
        'ï' => 'i',
        'ð' => 'd',
        'ñ' => 'n',
        'ò' => 'o',
        'ó' => 'o',
        'ô' => 'o',
        'õ' => 'o',
        'ö' => 'o',
        'ø' => 'o',
        'ù' => 'u',
        'ú' => 'u',
        'û' => 'u',
        'ü' => 'u',
        'ý' => 'y',
        'þ' => 'th',
        'ÿ' => 'y',
        'Ø' => 'O',
        // Decompositions for Latin Extended-A
        'Ā' => 'A',
        'ā' => 'a',
        'Ă' => 'A',
        'ă' => 'a',
        'Ą' => 'A',
        'ą' => 'a',
        'Ć' => 'C',
        'ć' => 'c',
        'Ĉ' => 'C',
        'ĉ' => 'c',
        'Ċ' => 'C',
        'ċ' => 'c',
        'Č' => 'C',
        'č' => 'c',
        'Ď' => 'D',
        'ď' => 'd',
        'Đ' => 'D',
        'đ' => 'd',
        'Ē' => 'E',
        'ē' => 'e',
        'Ĕ' => 'E',
        'ĕ' => 'e',
        'Ė' => 'E',
        'ė' => 'e',
        'Ę' => 'E',
        'ę' => 'e',
        'Ě' => 'E',
        'ě' => 'e',
        'Ĝ' => 'G',
        'ĝ' => 'g',
        'Ğ' => 'G',
        'ğ' => 'g',
        'Ġ' => 'G',
        'ġ' => 'g',
        'Ģ' => 'G',
        'ģ' => 'g',
        'Ĥ' => 'H',
        'ĥ' => 'h',
        'Ħ' => 'H',
        'ħ' => 'h',
        'Ĩ' => 'I',
        'ĩ' => 'i',
        'Ī' => 'I',
        'ī' => 'i',
        'Ĭ' => 'I',
        'ĭ' => 'i',
        'Į' => 'I',
        'į' => 'i',
        'İ' => 'I',
        'ı' => 'i',
        'Ĳ' => 'IJ',
        'ĳ' => 'ij',
        'Ĵ' => 'J',
        'ĵ' => 'j',
        'Ķ' => 'K',
        'ķ' => 'k',
        'ĸ' => 'k',
        'Ĺ' => 'L',
        'ĺ' => 'l',
        'Ļ' => 'L',
        'ļ' => 'l',
        'Ľ' => 'L',
        'ľ' => 'l',
        'Ŀ' => 'L',
        'ŀ' => 'l',
        'Ł' => 'L',
        'ł' => 'l',
        'Ń' => 'N',
        'ń' => 'n',
        'Ņ' => 'N',
        'ņ' => 'n',
        'Ň' => 'N',
        'ň' => 'n',
        'ŉ' => 'n',
        'Ŋ' => 'N',
        'ŋ' => 'n',
        'Ō' => 'O',
        'ō' => 'o',
        'Ŏ' => 'O',
        'ŏ' => 'o',
        'Ő' => 'O',
        'ő' => 'o',
        'Œ' => 'OE',
        'œ' => 'oe',
        'Ŕ' => 'R',
        'ŕ' => 'r',
        'Ŗ' => 'R',
        'ŗ' => 'r',
        'Ř' => 'R',
        'ř' => 'r',
        'Ś' => 'S',
        'ś' => 's',
        'Ŝ' => 'S',
        'ŝ' => 's',
        'Ş' => 'S',
        'ş' => 's',
        'Š' => 'S',
        'š' => 's',
        'Ţ' => 'T',
        'ţ' => 't',
        'Ť' => 'T',
        'ť' => 't',
        'Ŧ' => 'T',
        'ŧ' => 't',
        'Ũ' => 'U',
        'ũ' => 'u',
        'Ū' => 'U',
        'ū' => 'u',
        'Ŭ' => 'U',
        'ŭ' => 'u',
        'Ů' => 'U',
        'ů' => 'u',
        'Ű' => 'U',
        'ű' => 'u',
        'Ų' => 'U',
        'ų' => 'u',
        'Ŵ' => 'W',
        'ŵ' => 'w',
        'Ŷ' => 'Y',
        'ŷ' => 'y',
        'Ÿ' => 'Y',
        'Ź' => 'Z',
        'ź' => 'z',
        'Ż' => 'Z',
        'ż' => 'z',
        'Ž' => 'Z',
        'ž' => 'z',
        'ſ' => 's',
        // Decompositions for Latin Extended-B
        'Ș' => 'S',
        'ș' => 's',
        'Ț' => 'T',
        'ț' => 't',
        // Euro Sign
        '€' => 'E',
        // GBP (Pound) Sign
        '£' => '',
        // Vowels with diacritic (Vietnamese)
        // unmarked
        'Ơ' => 'O',
        'ơ' => 'o',
        'Ư' => 'U',
        'ư' => 'u',
        // grave accent
        'Ầ' => 'A',
        'ầ' => 'a',
        'Ằ' => 'A',
        'ằ' => 'a',
        'Ề' => 'E',
        'ề' => 'e',
        'Ồ' => 'O',
        'ồ' => 'o',
        'Ờ' => 'O',
        'ờ' => 'o',
        'Ừ' => 'U',
        'ừ' => 'u',
        'Ỳ' => 'Y',
        'ỳ' => 'y',
        // hook
        'Ả' => 'A',
        'ả' => 'a',
        'Ẩ' => 'A',
        'ẩ' => 'a',
        'Ẳ' => 'A',
        'ẳ' => 'a',
        'Ẻ' => 'E',
        'ẻ' => 'e',
        'Ể' => 'E',
        'ể' => 'e',
        'Ỉ' => 'I',
        'ỉ' => 'i',
        'Ỏ' => 'O',
        'ỏ' => 'o',
        'Ổ' => 'O',
        'ổ' => 'o',
        'Ở' => 'O',
        'ở' => 'o',
        'Ủ' => 'U',
        'ủ' => 'u',
        'Ử' => 'U',
        'ử' => 'u',
        'Ỷ' => 'Y',
        'ỷ' => 'y',
        // tilde
        'Ẫ' => 'A',
        'ẫ' => 'a',
        'Ẵ' => 'A',
        'ẵ' => 'a',
        'Ẽ' => 'E',
        'ẽ' => 'e',
        'Ễ' => 'E',
        'ễ' => 'e',
        'Ỗ' => 'O',
        'ỗ' => 'o',
        'Ỡ' => 'O',
        'ỡ' => 'o',
        'Ữ' => 'U',
        'ữ' => 'u',
        'Ỹ' => 'Y',
        'ỹ' => 'y',
        // acute accent
        'Ấ' => 'A',
        'ấ' => 'a',
        'Ắ' => 'A',
        'ắ' => 'a',
        'Ế' => 'E',
        'ế' => 'e',
        'Ố' => 'O',
        'ố' => 'o',
        'Ớ' => 'O',
        'ớ' => 'o',
        'Ứ' => 'U',
        'ứ' => 'u',
        // dot below
        'Ạ' => 'A',
        'ạ' => 'a',
        'Ậ' => 'A',
        'ậ' => 'a',
        'Ặ' => 'A',
        'ặ' => 'a',
        'Ẹ' => 'E',
        'ẹ' => 'e',
        'Ệ' => 'E',
        'ệ' => 'e',
        'Ị' => 'I',
        'ị' => 'i',
        'Ọ' => 'O',
        'ọ' => 'o',
        'Ộ' => 'O',
        'ộ' => 'o',
        'Ợ' => 'O',
        'ợ' => 'o',
        'Ụ' => 'U',
        'ụ' => 'u',
        'Ự' => 'U',
        'ự' => 'u',
        'Ỵ' => 'Y',
        'ỵ' => 'y',
        // Vowels with diacritic (Chinese, Hanyu Pinyin)
        'ɑ' => 'a',
        // macron
        'Ǖ' => 'U',
        'ǖ' => 'u',
        // acute accent
        'Ǘ' => 'U',
        'ǘ' => 'u',
        // caron
        'Ǎ' => 'A',
        'ǎ' => 'a',
        'Ǐ' => 'I',
        'ǐ' => 'i',
        'Ǒ' => 'O',
        'ǒ' => 'o',
        'Ǔ' => 'U',
        'ǔ' => 'u',
        'Ǚ' => 'U',
        'ǚ' => 'u',
        // grave accent
        'Ǜ' => 'U',
        'ǜ' => 'u',
      ];

      if ('de_DE' == $locale || 'de_DE_formal' == $locale || 'de_CH' == $locale || 'de_CH_informal' == $locale) {
        $chars['Ä'] = 'Ae';
        $chars['ä'] = 'ae';
        $chars['Ö'] = 'Oe';
        $chars['ö'] = 'oe';
        $chars['Ü'] = 'Ue';
        $chars['ü'] = 'ue';
        $chars['ß'] = 'ss';
      } elseif ('da_DK' === $locale) {
        $chars['Æ'] = 'Ae';
        $chars['æ'] = 'ae';
        $chars['Ø'] = 'Oe';
        $chars['ø'] = 'oe';
        $chars['Å'] = 'Aa';
        $chars['å'] = 'aa';
      } elseif ('ca' === $locale) {
        $chars['l·l'] = 'll';
      } elseif ('sr_RS' === $locale || 'bs_BA' === $locale) {
        $chars['Đ'] = 'DJ';
        $chars['đ'] = 'dj';
      }
      $string = strtr($string, $chars);
    } else {
      $chars = [];
      // Assume ISO-8859-1 if not UTF-8
      $chars['in'] = "\x80\x83\x8a\x8e\x9a\x9e"
        . "\x9f\xa2\xa5\xb5\xc0\xc1\xc2"
        . "\xc3\xc4\xc5\xc7\xc8\xc9\xca"
        . "\xcb\xcc\xcd\xce\xcf\xd1\xd2"
        . "\xd3\xd4\xd5\xd6\xd8\xd9\xda"
        . "\xdb\xdc\xdd\xe0\xe1\xe2\xe3"
        . "\xe4\xe5\xe7\xe8\xe9\xea\xeb"
        . "\xec\xed\xee\xef\xf1\xf2\xf3"
        . "\xf4\xf5\xf6\xf8\xf9\xfa\xfb"
        . "\xfc\xfd\xff";
      $chars['out'] = 'EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy';
      $string = strtr($string, $chars['in'], $chars['out']);
      $double_chars = [];
      $double_chars['in'] = ["\x8c", "\x9c", "\xc6", "\xd0", "\xde", "\xdf", "\xe6", "\xf0", "\xfe"];
      $double_chars['out'] = ['OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th'];
      $string = str_replace($double_chars['in'], $double_chars['out'], $string);
    }

    return $string;
  }

  /**
   * Processes a given string to remove accents and special characters, returning a sanitized and lowercase version of the string.
   *
   * @param string $str The input string to be processed.
   * @param string $charset The character encoding of the string. Defaults to 'utf-8'.
   * @return string The processed string with accents and special characters removed.
   */
  private function getSkipAccents(string $str, string $charset = 'utf-8'): string
  {
    if (extension_loaded('intl')) {
      $transliterator = Transliterator::create('Any-Latin; Latin-ASCII');
      $str = $transliterator->transliterate(mb_convert_encoding(htmlspecialchars_decode($str), $charset, 'auto'));
      $str = static::getRemoveAccents($str);
    } else {
      $str = static::getRemoveAccents($str);
    }

    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
    $str = preg_replace('#&[^;]+;#', '', $str);
    $str = preg_replace('/[^A-Za-z0-9\-]/', '', $str); // Removes special chars

    return mb_strtolower($str);
  }

  /**
   * Replaces spaces in the given string with hyphens, removes accents,
   * and ensures no consecutive hyphens are present.
   *
   * @param string|null $str The input string to modify. Can be null.
   * @return string The modified string with spaces replaced by hyphens, accents removed, and no consecutive hyphens.
   */
  private function replaceString(?string $str): string
  {
    $string = str_replace(' ', '-', $str);
    $string = $this->getSkipAccents($string);

    $string = str_replace('--', '-', $string);

    return $string;
  }

  /**
   * Generates a product-specific URL based on product information, parameters, and defined configurations for SEO-friendly URLs.
   *
   * @param int|string $products_id The ID of the product for which the URL is to be generated.
   * @param string $parameters Additional query parameters to append to the generated URL.
   * @return string The generated URL for the specified product.
   */

  public function getProductNameUrl($products_id, string $parameters = ''): string
  {
    $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
    $CLICSHOPPING_Language = Registry::get('Language');
    $CLICSHOPPING_Db = Registry::get('Db');

    if (defined('SEARCH_ENGINE_FRIENDLY_URLS') && SEARCH_ENGINE_FRIENDLY_URLS == 'true' && CLICSHOPPING::getSite() != 'ClicShoppingAdmin') {
      if (defined('SEARCH_ENGINE_FRIENDLY_URLS_PRO') && SEARCH_ENGINE_FRIENDLY_URLS_PRO == 'true') {
        $Qseo = $CLICSHOPPING_Db->prepare('select products_seo_url
                                             from :table_products_description
                                             where products_id = :products_id
                                             and language_id = :language_id
                                           ');
        $Qseo->bindInt(':products_id', $products_id);
        $Qseo->bindInt(':language_id', $CLICSHOPPING_Language->getId());

        $Qseo->execute();

        $products_seo_url = $Qseo->value('products_seo_url');

        if (empty($products_seo_url) || is_null($products_seo_url)) {
          $products_name = $CLICSHOPPING_ProductsCommon->getProductsName($products_id);
          $products_name = $this->replaceString($products_name);
          $products_url_rewrited = 'Products&Description&' . $products_name . '&Id=' . $products_id;
        } else {
          $products_name = $this->replaceString($products_seo_url);
          $products_url_rewrited = 'Products&Description&' . $products_name . '&Id=' . $products_id;
        }
      } else {
        $products_url_rewrited = 'Products&Description&Id=' . $products_id;
      }
    } else {
      $products_url_rewrited = 'Products&Description&Id=' . $products_id;
    }

    $url = CLICSHOPPING::link(null, $products_url_rewrited . $parameters);

    return $url;
  }

  /**
   * Generates the content URL for a page manager using the given page ID and optional parameters.
   *
   * @param int|string $page_id The unique identifier of the page for which the content URL is to be created.
   * @param string $parameters Optional additional URL parameters to append to the generated URL.
   * @return string The generated page manager content URL.
   */

  public function getPageManagerContentUrl($page_id, string $parameters = ''): string
  {
    $CLICSHOPPING_PageManagerShop = Registry::get('PageManagerShop');

    if (defined('SEARCH_ENGINE_FRIENDLY_URLS') && SEARCH_ENGINE_FRIENDLY_URLS == 'true' && CLICSHOPPING::getSite() != 'ClicShoppingAdmin') {
      if (defined('SEARCH_ENGINE_FRIENDLY_URLS_PRO') && SEARCH_ENGINE_FRIENDLY_URLS_PRO == 'true') {
        $page_title = $CLICSHOPPING_PageManagerShop->pageManagerDisplayTitle($page_id);
        $page_title = $this->replaceString($page_title);
        $content_url_rewrited = 'Info&Content&' . $page_title . '&pagesId=' . $page_id;
      } else {
        $content_url_rewrited = 'Info&Content&pagesId=' . $page_id;
      }
    } else {
      $content_url_rewrited = 'Info&Content&pagesId=' . $page_id;
    }

    $url = CLICSHOPPING::link(null, $content_url_rewrited . $parameters);

    return $url;
  }

  /**
   * Sets the category tree title and returns it.
   *
   * @param string $title The title of the category tree to be set.
   * @return string The title that was set.
   */
  public function getCategoryTreeTitle(string $title): string
  {
    $this->title = $title;

    return $title;
  }


  /**
   * Generates a URL for the category tree based on the provided category ID and optional parameters.
   * Utilizes search engine friendly URLs if configured and available.
   *
   * @param string $categories_id The category ID used to generate the URL.
   * @param string $parameters Optional query parameters to append to the URL.
   * @return string The generated category tree URL.
   */

  public function getCategoryTreeUrl(string $categories_id, string $parameters = ''): string
  {
    $CLICSHOPPING_Language = Registry::get('Language');
    $CLICSHOPPING_Db = Registry::get('Db');

    if (defined('SEARCH_ENGINE_FRIENDLY_URLS') && SEARCH_ENGINE_FRIENDLY_URLS == 'true' && CLICSHOPPING::getSite() != 'ClicShoppingAdmin') {
      if (defined('SEARCH_ENGINE_FRIENDLY_URLS_PRO') && SEARCH_ENGINE_FRIENDLY_URLS_PRO == 'true') {
        $Qseo = $CLICSHOPPING_Db->prepare('select categories_seo_url
                                             from :table_categories_description
                                             where categories_id = :categories_id
                                             and language_id = :language_id
                                           ');
        $Qseo->bindInt(':categories_id', $categories_id);
        $Qseo->bindInt(':language_id', $CLICSHOPPING_Language->getId());

        $Qseo->execute();

        $categories_seo_url = $Qseo->value('categories_seo_url');

        if (empty($categories_seo_url) || is_null($categories_seo_url)) {
          $link_title = $this->title;
          $link_title = $this->replaceString($link_title);

          $categories_url_rewrited = $link_title . '&cPath=' . $categories_id;
        } else {
          $link_title = $this->replaceString($categories_seo_url);
          $categories_url_rewrited = $link_title . '&cPath=' . $categories_id;
        }
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
   * Generates the URL for a category image based on the provided category ID and optional parameters.
   *
   * @param string $categories_id The ID of the category for which the image URL should be generated.
   * @param string $parameters Optional additional parameters to append to the URL.
   * @return string The generated URL for the category image.
   */

  public function getCategoryImageUrl(string $categories_id, string $parameters = ''): string
  {
    if (defined('SEARCH_ENGINE_FRIENDLY_URLS') && SEARCH_ENGINE_FRIENDLY_URLS == 'true' && CLICSHOPPING::getSite() != 'ClicShoppingAdmin') {
      if (defined('SEARCH_ENGINE_FRIENDLY_URLS_PRO') && SEARCH_ENGINE_FRIENDLY_URLS_PRO == 'true') {
        $link_title = $this->title;
        $link_title = $this->replaceString($link_title);
        $categories_url_rewrited = $link_title . '&' . $categories_id;
      } else {
        $categories_url_rewrited = $categories_id;
//          $categories_url_rewrited = 'cPath=' . $categories_id;	  
      }
    } else {
      $categories_url_rewrited = $categories_id;
//        $categories_url_rewrited = 'cPath=' . $categories_id;	
    }

    $url = CLICSHOPPING::link(null, $categories_url_rewrited . $parameters);

    return $url;
  }

  /**
   * Generates and returns the URL for a specified manufacturer based on the provided manufacturer ID and optional parameters.
   *
   * @param int $manufacturer_id The unique identifier of the manufacturer.
   * @param string $parameters Optional query string parameters to append to the URL.
   * @return string Returns the generated manufacturer URL.
   */
  public function getManufacturerUrl(int $manufacturer_id, string $parameters = ''): string
  {
    $CLICSHOPPING_Manufacturers = Registry::get('Manufacturers');

    if (defined('SEARCH_ENGINE_FRIENDLY_URLS') && SEARCH_ENGINE_FRIENDLY_URLS == 'true' && CLICSHOPPING::getSite() != 'ClicShoppingAdmin') {
      if (defined('SEARCH_ENGINE_FRIENDLY_URLS_PRO') && SEARCH_ENGINE_FRIENDLY_URLS_PRO == 'true') {
        $manufacturer_title = $CLICSHOPPING_Manufacturers->getTitle($manufacturer_id);
        $manufacturer_title = $this->replaceString($manufacturer_title);

        $manufacturer_url_rewrited = $manufacturer_title . '&manufacturersId=' . (int)$manufacturer_id;
      } else {
        $manufacturer_url_rewrited = 'manufacturersId=' . (int)$manufacturer_id;
      }
    } else {
      $manufacturer_url_rewrited = 'manufacturersId=' . (int)$manufacturer_id;
    }

    $url = CLICSHOPPING::link(null, $manufacturer_url_rewrited . $parameters);

    return $url;
  }
}