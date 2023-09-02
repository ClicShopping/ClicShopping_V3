<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Sites\Common;

use ClicShopping\OM\HTML;
use function strlen;

class HTMLOverrideCommon extends HTML
{
  /**
   * Function to remove HTML tags, javascript sections and convert some common HTML entities to their text equivalent
   * public function
   * @param string $str
   * @return a text replaced
   */
  static public function stripHtmlTags(string $str): string
  {

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

  /**
   * @param $CatList
   * @param string $length
   * @return mixed|string|string[]|null
   */
  public static function cleanHtml($CatList, string $length = '')
  {
    $clean = strip_tags($CatList);
    $clean = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $clean);
    $clean = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $clean);
    $clean = str_replace(' & ', ' &amp; ', html_entity_decode((htmlspecialchars_decode($clean))));
    $clean = preg_replace('/\s&nbsp;\s/i', ' ', $clean);
    $clean = preg_replace("[<(.*'?)>]", '', $clean);

    if (!empty ($length)) {
      if (strlen($clean) > $length) {
        $clean = substr($clean, 0, $length - 3) . "...";
      }
    }

    $clean = htmlspecialchars($clean, ENT_QUOTES | ENT_HTML5);

    return $clean;
  }

  /**
   * Minify HTML
   * @param string $input
   * @return string|string[]|null
   */
  public static function getMinifyHtml(string $input)
  {
    if (trim($input) === '') return $input;
    // Remove extra white-space(s) between HTML attribute(s)
    $input = preg_replace_callback('#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', function ($matches) {
      return '<' . $matches[1] . preg_replace('#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2]) . $matches[3] . '>';
    }, str_replace("\r", "", $input));

    if (str_contains($input, '</script>')) {
      $input = preg_replace_callback('#<script(.*?)>(.*?)</script>#is', function ($matches) {
        return '<script' . $matches[1] . '>' . static::getMinifyJS($matches[2]) . '</script>';
      }, $input);
    }

    $array_string = [
      // t = text
      // o = tag open
      // c = tag close
      // Keep important white-space(s) after self-closing HTML tag(s)
      '#<(img|input)(>| .*?>)#s',
      // Remove a line break and two or more white-space(s) between tag(s)
      '#(<!--.*?-->)|(>)(?:\n*|\s{2,})(<)|^\s*|\s*$#s',
      '#(<!--.*?-->)|(?<!\>)\s+(<\/.*?>)|(<[^\/]*?>)\s+(?!\<)#s', // t+c || o+t
      '#(<!--.*?-->)|(<[^\/]*?>)\s+(<[^\/]*?>)|(<\/.*?>)\s+(<\/.*?>)#s', // o+o || c+c
      '#(<!--.*?-->)|(<\/.*?>)\s+(\s)(?!\<)|(?<!\>)\s+(\s)(<[^\/]*?\/?>)|(<[^\/]*?\/?>)\s+(\s)(?!\<)#s', // c+t || t+o || o+t -- separated by long white-space(s)
      '#(<!--.*?-->)|(<[^\/]*?>)\s+(<\/.*?>)#s', // empty tag
      '#<(img|input)(>| .*?>)<\/\1>#s', // reset previous fix
      '#(&nbsp;)&nbsp;(?![<\s])#', // clean up ...
      '#(?<=\>)(&nbsp;)(?=\<)#', // --ibid
      // Remove HTML comment(s) except IE comment(s)
      '#\s*<!--(?!\[if\s).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s'
    ];

    $array_replace = [
      '<$1$2</$1>',
      '$1$2$3',
      '$1$2$3',
      '$1$2$3$4$5',
      '$1$2$3$4$5$6$7',
      '$1$2$3',
      '<$1$2',
      '$1 ',
      '$1',
      ""
    ];

    return preg_replace($array_string, $array_replace, $input);
  }

  /**
   * minify js
   * @param $input
   * @return string|string[]|null
   */
  public static function getMinifyJS(string $input)
  {
    if (trim($input) === '') return $input;

    $array_string = [
      // Remove comment(s)
      '#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
      // Remove white-space(s) outside the string and regex
      '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
      // Remove the last semicolon
      '#;+\}#',
      // Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
      '#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
      // --ibid. From `foo['bar']` to `foo.bar`
      '#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i'
    ];

    $array_replace = [
      '$1',
      '$1$2',
      '}',
      '$1$3',
      '$1.$3'
    ];

    return preg_replace($array_string, $array_replace, $input);
  }
}