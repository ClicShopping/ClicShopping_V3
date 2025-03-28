<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Sites\Common;

use ClicShopping\OM\HTML;
use function strlen;
/**
 * Class HTMLOverrideCommon
 *
 * Provides methods to process and manipulate HTML content. It extends the `HTML` class
 * and adds functionality to strip HTML tags, clean and minify HTML, and minify JavaScript code.
 */
class HTMLOverrideCommon extends HTML
{

  /**
   * Nettoie une chaîne HTML en supprimant les balises, le JavaScript et les entités HTML.
   *
   * @param string $html Le contenu HTML à nettoyer.
   * @param int|null $maxLength Longueur maximale du texte nettoyé.
   * @return string Texte nettoyé et éventuellement tronqué.
   */
  public static function cleanHtmlOptimized(string $html, ?int $maxLength = null): string
  {
    // Supprime les balises <script> et <style>
    $clean = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
    $clean = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $clean);

    // Supprime toutes les autres balises HTML
    $clean = strip_tags($clean);

    // Décodage des entités HTML pour récupérer du texte lisible
    $clean = html_entity_decode($clean, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Normalisation des espaces
    $clean = preg_replace('/\s+/', ' ', trim($clean));

    // Tronquer si une longueur max est spécifiée
    if ($maxLength !== null && mb_strlen($clean, 'UTF-8') > $maxLength) {
      $clean = mb_substr($clean, 0, $maxLength - 3, 'UTF-8') . '...';
    }

    // Sécurisation XSS (pour affichage web)
    return htmlspecialchars($clean, ENT_QUOTES | ENT_HTML5, 'UTF-8');
  }

  /**
   * Nettoie un texte HTML en supprimant le contenu inutile pour l'embedding.
   * - Supprime les scripts, styles, images, iframes, liens externes.
   * - Conserve uniquement le texte utile pour l'indexation et la recherche.
   *
   * @param string $html Le contenu HTML à nettoyer.
   * @return string Texte nettoyé et structuré pour l'embedding.
   */
  public static function cleanHtmlForEmbedding(string $html): string
  {
    // Supprime les scripts, styles, iframes, objets et balises inutiles
    $clean = preg_replace('/<(script|style|iframe|object|embed|noscript|svg|canvas|meta|link|form|button|input|select|textarea)[^>]*>.*?<\/\1>/is', '', $html);

    // Supprime les balises <img> (images) et les balises de liens (<a>)
    $clean = preg_replace('/<img[^>]*>/i', '', $clean);
    $clean = preg_replace('/<a\b[^>]*>(.*?)<\/a>/i', '\1', $clean); // Conserve le texte du lien

    // Supprime toutes les autres balises HTML
    $clean = strip_tags($clean);

    // Décodage des entités HTML
    $clean = html_entity_decode($clean, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Supprime les caractères non alphanumériques inutiles
    $clean = preg_replace('/[^\p{L}\p{N}\s,.!?-]/u', '', $clean);

    // Normalisation des espaces
    $clean = preg_replace('/\s+/', ' ', trim($clean));

    return $clean;
  }

  /**
   * Nettoie un texte HTML pour l'optimisation SEO.
   * - Supprime scripts, styles, iframes et balises inutiles.
   * - Conserve les titres, descriptions et mots-clés.
   * - Garde certains caractères spéciaux utiles pour le SEO (- , / |).
   *
   * @param string $html Le contenu HTML à nettoyer.
   * @return string Texte nettoyé et structuré pour le SEO.
   */
  public static function cleanHtmlForSEO(string $html): string
  {
    // Supprime les balises nuisibles au SEO (scripts, styles, iframes, objets, boutons)
    $clean = preg_replace('/<(script|style|iframe|object|embed|noscript|svg|canvas|meta|link|button|form|input|select|textarea)[^>]*>.*?<\/\1>/is', '', $html);

    // Supprime les balises <img> (images)
    $clean = preg_replace('/<img[^>]*>/i', '', $clean);

    // Supprime les balises <a> mais garde le texte du lien
    $clean = preg_replace('/<a\b[^>]*>(.*?)<\/a>/i', '\1', $clean);

    // Supprime toutes les autres balises HTML
    $clean = strip_tags($clean);

    // Décodage des entités HTML
    $clean = html_entity_decode($clean, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Supprime uniquement les caractères spéciaux non pertinents (évite de supprimer - , / |)
    $clean = preg_replace('/[^\p{L}\p{N}\s,\/|.-]/u', '', $clean);

    // Normalisation des espaces
    $clean = preg_replace('/\s+/', ' ', trim($clean));

    return $clean;
  }

  /**
   * Minifies the given HTML by removing unnecessary whitespaces and optimizing formatting while preserving functionality.
   *
   * @param string $input The HTML string to be minified.
   * @return string The minified HTML string.
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
   * Minifies a block of JavaScript code by removing unnecessary characters such as comments,
   * extra whitespaces, and semicolons. Also converts certain JavaScript notations to more concise formats.
   *
   * @param string $input The JavaScript code to be minified.
   * @return string The minified JavaScript code.
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