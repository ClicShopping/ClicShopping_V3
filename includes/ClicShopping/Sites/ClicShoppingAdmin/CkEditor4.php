<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Sites\ClicShoppingAdmin;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class CkEditor4 extends HTML
  {
    /*
      *  Ckeditor cdn version
      *
      * @param string $string
      * @return string $string,
      */
    public static function getWysiwyg(): string
    {
      $script = '<script src="//cdn.ckeditor.com/4.19.1/full/ckeditor.js"></script>';

      return $script;
    }

    /**
     * @return string
     */
    public static function getWysiwygLanguage() :string
    {
      $CLICSHOPPING_Language = Registry::get('Language');
      $code = $CLICSHOPPING_Language->getCode();

      return $code;
    }


    /**
     * @param string $name
     * @return string
     */
    public static function getWysiwygId(string $name) :string
    {
      $result = str_replace('[', '', $name);
      $result = str_replace(']', '', $result);

      return $result;
    }

    /**
     * @return string
     */
    private static function getElFinderConnector() :string
    {
      $connector = CLICSHOPPING::link('Shop/ext/elFinder-master/elfinder-cke.php?Admin=ClicShoppingAdmin');

      return $connector;
    }

    /**
     * Script url
     * @return string
     *
     */
    public static function getWysiwygCustomizeJsURL(): string
    {
      $url = CLICSHOPPING::link('Shop/ext/javascript/cKeditor/ckeditor4_config.js');

      return $url;
    }

    /*
     * Outputs a form textarea field with ckeditor
     *
     * @param string $name The name and ID of the textarea field
     * @param string $value The default value for the textarea field
     * @param int $width The width of the textarea field
     * @param int $height The height of the textarea field
     * @param string $parameters Additional parameters CLICSHOPPING::getConfig('http_server', 'Shop') . CLICSHOPPING::getConfig('http_path','Shop')for the textarea field
     * @param boolean $override Override the default value with the value found in the GET or POST scope
     *
     */
    public static function textAreaCkeditor(string $name, ?string $value = null, int $width = 750, int $height = 200, ?string $text = null, ?string $parameters = null, bool $override = true): string
    {
      $field = '<textarea name="' . HTML::output($name) . '"';

      if (!\is_null($parameters)) $field .= ' ' . $parameters;
      $field .= ' />';

      if (($override === true) && ((isset($_GET[$name]) && \is_string($_GET[$name])) || (isset($_POST[$name]) && \is_string($_POST[$name])))) {
        if (isset($_GET[$name]) && \is_string($_GET[$name])) {
          $field .= HTML::outputProtected($_GET[$name]);
        } elseif (isset($_POST[$name]) && \is_string($_POST[$name])) {
          $field .= HTML::outputProtected($_POST[$name]);
        }
      } elseif (!\is_null($text)) {
        $field .= HTML::outputProtected($text);
      }

      $url = static::getElFinderConnector();

      $field .= '</textarea>';
      $field .= '<script>
          CKEDITOR.replace(\'' . HTML::output($name) . '\',
      {
          customConfig: "' . static::getWysiwygCustomizeJsURL()  . '",
          height : ' . $height . ',
          width : ' . $width . ',
          toolbar : "Full",
          filebrowserBrowseUrl :"' . $url . '",
      });
                 </script>';

      return $field;
    }

    /*
     * Create form textarea field with ckeditor for image icon and source only
     *
     * @param string $name The name and ID of the textarea field
     *
     */

    public static function fileFieldImageCkEditor(string $name, ?string $value = null, ?int $width = null, ?int $height = null): string
    {
      if (\is_null($height)) {
        $height = '250';
      }

      if (\is_null($width)) {
        $width = '250';
      }
  //ckeditor 4
      $url = static::getElFinderConnector();

      $field = '<textarea name="' . HTML::output($name) . '" /></textarea>';

      $field .= '<script>
          CKEDITOR.replace(\'' . HTML::output($name) . '\',
        {
          customConfig: "' . static::getWysiwygCustomizeJsURL()  . '",
          width : ' . $width . ',
          height : ' . $height . ',
          filebrowserBrowseUrl : "' . $url . '",
       });
              </script>';

      return $field;
    }

    /**
     * Clean html code image
     *
     * @param string $image
     * @return string $image, without html
     *
     */
    public static function getWysiwygImageAlone(string $image): string
    {
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

      if (!empty($image)) {
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);

        $doc->loadHTML($image);
        $xpath = new \DOMXPath($doc);

        $image = $xpath->evaluate("string(//img/@src)");
        $image = CLICSHOPPING::getConfig('http_server', 'Shop') . $image;

        $image = htmlspecialchars($image, ENT_QUOTES | ENT_HTML5);
        $image = strstr($image, $CLICSHOPPING_Template->getDirectoryShopTemplateImages());
        $image = str_replace($CLICSHOPPING_Template->getDirectoryShopTemplateImages(), '', $image);
        $image_end = strstr($image, '&quot;');
        $image = str_replace($image_end, '', $image);
        $image = str_replace($CLICSHOPPING_Template->getDirectoryShopSources(), '', $image);

        libxml_clear_errors();
      }

      return $image;
    }
  }