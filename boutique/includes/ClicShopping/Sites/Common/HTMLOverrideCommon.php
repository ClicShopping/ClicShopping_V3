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

  namespace ClicShopping\Sites\Common;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class HTMLOverrideCommon extends HTML {

/**
 * Function to remove HTML tags, javascript sections and convert some common HTML entities to their text equivalent
 * public function
 * @param string $string, $str of the text
 * @return a text replaced
 */

    static public function stripHtmlTags($str) {

      $search = ["'<script[^>]*?>.*?</script>'",  // Strip out javascript
                  "'<[/!]*?[^<>]*?>'si",          // Strip out HTML tags
                  //"'([rn])[s]+'",                // Strip out white space
                  "'&(quot|#34);'i",                // Replace HTML entities
                  "'&(amp|#38);'i",
                  "'&(lt|#60);'i",
                  "'&(gt|#62);'i",
                  "'&(nbsp|#160);'i",
                  "'&(iexcl|#161);'i",
                  "'&(cent|#162);'i",
                  "'&(pound|#163);'i",
                  "'&(copy|#169);'i",
                  "'&#(d+);'i"
                 ];

      $replace = ['',
                  '',
                  //"\1",
                  "\"",
                  '&',
                  '<',
                  '>',
                  ' ',
                  chr(161),
                  chr(162),
                  chr(163),
                  chr(169),
                  'ch(\1)'
                 ];

      return preg_replace($search, $replace, $str);
    }

// fonction de nettoyage des donnees si presence d'un editeur html
    public static function cleanHtml($CatList, $length = '') {

      $clean = strip_tags($CatList);
      $clean= preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $clean);
      $clean= preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $clean);
      $clean=str_replace(' & ', ' &amp; ', html_entity_decode((htmlspecialchars_decode($clean))));
      $clean = preg_replace('/\s&nbsp;\s/i', ' ',  $clean);
      $clean = preg_replace("[<(.*'?)>]", '',$clean);

      if (!empty ($length)) {
        if (strlen($clean) > $length) {
          $clean = substr($clean, 0, $length-3) . "...";
        }
      }
      return $clean;
    }

    public function starHeaderTagRateYo() {
      $CLICSHOPPING_Template = Registry::get('Template');

      $header_tag = '<!--   Rate Yo start -->' . "\n";
      $header_tag .= '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.4/jquery.rateyo.min.css">';
      $header_tag .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.4/jquery.rateyo.min.js"></script>';
      $header_tag .= '<!--   Rate Yo  end -->' . "\n";
      $CLICSHOPPING_Template->addBlock($header_tag, 'header_tags');

      return $CLICSHOPPING_Template->addBlock($header_tag, 'header_tags');
    }

    public function starTagRateYo($rating = null, $color = null, $readonly = true, $size = '20') {

      $star_rating = '<!--   Rate Yo start -->'."\n";
      $star_rating .='<script> ';
      $star_rating .= '$(function () { ';
      $star_rating .= '$("#rateYo").rateYo({ ';

      if (!is_null($rating)) {
        $star_rating .= 'rating: '. (int)$rating . ',  ';
      } else {
        $star_rating .= 'rating: 0, ';
      }

      $star_rating .= 'fullStar: true, ';
      $star_rating .= 'starWidth: "' . $size .'px", ';

      if ( $readonly === true) {
        $star_rating .= 'readOnly: ' . $readonly . ', ';
      }

      if (!is_null($color)) {
        $star_rating .= 'normalFill: "' . $color . '" ';
      }

      $star_rating .= '}) ';
      $star_rating .= '.on("rateyo.set", function (e, data) { ';
      $star_rating .= 'document.getElementById("rateyoid").value=data.rating; ';
      $star_rating .= '}); ';
      $star_rating .= '}); ';
      $star_rating .= '</script>';
      $star_rating .= '<span itemprop="ratingValue"><div id="rateYo"></div></span>';

      if ( $readonly === false) {
        $star_rating .= parent::hiddenField('rating', 1, 'id="rateyoid"');
      }

      $star_rating .= '<!--   Rate Yo End -->'."\n";

      return $star_rating;
    }
  }