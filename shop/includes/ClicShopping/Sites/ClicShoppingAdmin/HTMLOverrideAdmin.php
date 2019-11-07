<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Sites\ClicShoppingAdmin;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTTP;

  class HTMLOverrideAdmin extends HTML
  {

    protected $string;
    protected $name;
    protected $width;
    protected $height;
    protected $country;
    protected $form;
    protected $field;

    /*
     *  remplace les espaces par un +
     *
     * @param string $string
     * @return string $string,
     * @access public
     */

    public static function sanitizeReplace(string $string): string
    {
      $string = preg_replace("/ /", "+", $string);
      return preg_replace("/[<>]/", '_', $string);
    }

    /*
     *  Ckeditor
     *
     * @param string $string
     * @return string $string,
     * @access public
     *
     */
    public static function getCkeditor(): string
    {
      $script = '<script src="' . CLICSHOPPING::link('Shop/ext/ckeditor/ckeditor.js') . '"></script>';

      return $script;
    }

    /*
     * Outputs a form textarea field with ckeditor
     *
     * @param string $name The name and ID of the textarea field
     * @param string $value The default value for the textarea field
     * @param int $width The width of the textarea field
     * @param int $height The height of the textarea field
     * @param string $parameters Additional parameters for the textarea field
     * @param boolean $override Override the default value with the value found in the GET or POST scope
     * @access public
     */
    public static function textAreaCkeditor(string $name, ?string $value = null, int $width, int $height, ?string $text = null, ?string $parameters = null, bool $override = true): string
    {
      $height = '750';

      $field = '<textarea name="' . HTML::output($name) . '"';

      if (!is_null($parameters)) $field .= ' ' . $parameters;
      $field .= ' />';
      
      if (($override === true) && ((isset($_GET[$name]) && is_string($_GET[$name])) || (isset($_POST[$name]) && is_string($_POST[$name])))) {
        if (isset($_GET[$name]) && is_string($_GET[$name])) {
          $field .= HTML::outputProtected($_GET[$name]);
        } elseif (isset($_POST[$name]) && is_string($_POST[$name])) {
          $field .= HTML::outputProtected($_POST[$name]);
        }
      } elseif (!is_null($text)) {
        $field .= HTML::outputProtected($text);
      }
      
      $field .= '</textarea>';

      $url  = CLICSHOPPING::link('Shop/ext/elFinder-master/elfinder-cke.php?Admin=ClicShoppingAdmin');
      
      $field .= '<script>
        CKEDITOR.replace(\'' . HTML::output($name) . '\',
    {
        width : ' . $height . ',
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
     * @access public
     */

    public static function fileFieldImageCkEditor(string $name, ?string $value = null, ?int $width = null, ?int $height = null): string
    {
      if (is_null($height)) {
        $height = '250';
      }

      if (is_null($width)) {
        $width = '250';
      }

      $field = '<textarea name="' . HTML::output($name) . '" /></textarea>';

      $url  = CLICSHOPPING::link('Shop/ext/elFinder-master/elfinder-cke.php?Admin=ClicShoppingAdmin');

      $field .= '<script>
        CKEDITOR.replace(\'' . HTML::output($name) . '\',
      {
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
     * @access public
     */
    public static function getCkeditorImageAlone(string $image): string
    {
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

      if (!empty($image)) {
        $doc = new \DOMDocument();
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
      }

      return $image;
    }

    /**
     * Pulldown products
     *
     * @param string $name , $parameters, $exclude
     * @return string $select_string, the pulldown value of products
     * @access public
     */
    public static function selectMenuProductsPullDown(string $name, $parameters = '', $exclude = '', string $class = 'form-control'): string
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Currencies = Registry::get('Currencies');

      if (empty($exclude)) {
        $exclude = [];
      }

      $select_string = '<select name="' . $name . '"';

      if ($parameters) {
        $select_string .= ' ' . $parameters;
      }

      if (!empty($class)) $select_string .= ' class="' . $class . '"';

      $select_string .= ' />';

      $all_groups = [];

      $QcustomersGroups = $CLICSHOPPING_Db->prepare('select customers_group_name,
                                                             customers_group_id
                                                      from :table_customers_groups
                                                      order by customers_group_id
                                                    ');
      $QcustomersGroups->execute();

      while ($existing_groups = $QcustomersGroups->fetch()) {
        $all_groups[$existing_groups['customers_group_id']] = $existing_groups['customers_group_name'];
      }

      $Qproducts = $CLICSHOPPING_Db->prepare('select p.products_id,
                                                     pd.products_name,
                                                     p.products_price
                                              from :table_products p,
                                                   :table_products_description pd
                                              where p.products_id = pd.products_id
                                              and pd.language_id = :language_id
                                              and p.products_archive = 0
                                              order by products_name
                                             ');
      $Qproducts->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
      $Qproducts->execute();

      while ($Qproducts->fetch()) {

// Permettre le changement de groupe en mode B2B
        if (MODE_B2B_B2C == 'true') {
//B2BSuite modification
          if (!in_array($Qproducts->valueInt('products_id'), $exclude)) {

            $Qprice = $CLICSHOPPING_Db->prepare('select customers_group_price,
                                                  customers_group_id
                                          from :table_products_groups
                                          where products_id = :products_id
                                         ');
            $Qprice->bindInt(':products_id', $Qproducts->valueInt('products_id'));
            $Qprice->execute();

            $product_prices = [];

            while ($prices_array = $Qprice->fetch()) {
              $product_prices[$prices_array['customers_group_id']] = $prices_array['customers_group_price'];
            }

            $price_string = '';
            $sde = 0;
//while(list($sdek,$sdev)=each($all_groups)){
            foreach ($all_groups as $sdek => $sdev) {
              if (!in_array($Qproducts->valueInt('products_id') . ":" . (int)$sdek, $exclude)) {
                if ($sde)
                  $price_string .= ' - ';
                $price_string .= $sdev . ' : ' . $CLICSHOPPING_Currencies->format(isset($product_prices[$sdek]) ? $product_prices[$sdek] : $Qproducts->valueDecimal('products_price'));
                $sde = 1;
              }
            }

// Ajouter VISITOR_NAME . ': ' . $CLICSHOPPING_Currencies->format($Qproducts->valueDecimal('products_price')) pour permettre d'afficher le prix des clients qui ne font pas partie d'un groupe B2B(
            $select_string .= '<option value="' . $Qproducts->valueInt('products_id') . '">' . HTML::outputProtected($Qproducts->value('products_name')) . ' (' . CLICSHOPPING::getDef('visitor_name') . ': ' . $CLICSHOPPING_Currencies->format($Qproducts->valueDecimal('products_price')) . ' - ' . $price_string . ')</option>';
          }
        } else {
          if (!in_array($Qproducts->valueInt('products_id'), $exclude)) {
            $select_string .= '<option value="' . $Qproducts->valueInt('products_id') . '">' . HTML::outputProtected($Qproducts->value('products_name')) . ' (' . $CLICSHOPPING_Currencies->format($Qproducts->valueDecimal('products_price')) . ')</option>';
          }
        }

// ####### END  #######
      }

      $select_string .= '</select>';

      return $select_string;
    }


    /**
     * javascript to dynamically update the states/provinces list when the country is changed
     * TABLES: zones
     */
    public static function getJsZoneList(string $country, string $form, string $field): string
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcountries = $CLICSHOPPING_Db->prepare('select distinct zone_country_id
                                               from :table_zones
                                               where  zone_status = 0
                                               order by zone_country_id
                                              ');
      $Qcountries->execute();

      $num_country = 1;
      $output_string = '';

      while ($countries = $Qcountries->fetch()) {
        if ($num_country == 1) {
          $output_string .= '  if (' . $country . ' == "' . (int)$countries['zone_country_id'] . '") {' . "\n";
        } else {
          $output_string .= '  } else if (' . $country . ' == "' . (int)$countries['zone_country_id'] . '") {' . "\n";
        }

        $Qzone = $CLICSHOPPING_Db->prepare('select zone_name,
                                                   zone_id
                                            from :table_zones
                                            where  zone_country_id = :zone_country_id
                                            and zone_status = 0
                                            order by zone_name
                                          ');
        $Qzone->bindInt(':zone_country_id', $countries['zone_country_id']);

        $Qzone->execute();

        $num_state = 1;

        while ($states = $Qzone->fetch()) {
          if ($num_state == '1') $output_string .= '    ' . $form . '.' . $field . '.options[0] = new Option("' . CLICSHOPPING::getDef('text_selected') . '", "");' . "\n";
          $output_string .= '    ' . $form . '.' . $field . '.options[' . $num_state . '] = new Option("' . $states['zone_name'] . '", "' . $states['zone_id'] . '");' . "\n";
          $num_state++;
        }

        $num_country++;
      }

      $output_string .= '  } else {' . "\n" .
        '    ' . $form . '.' . $field . '.options[0] = new Option("' . CLICSHOPPING::getDef('text_select') . '", "");' . "\n" .
        '  }' . "\n";

      return $output_string;
    }
  }