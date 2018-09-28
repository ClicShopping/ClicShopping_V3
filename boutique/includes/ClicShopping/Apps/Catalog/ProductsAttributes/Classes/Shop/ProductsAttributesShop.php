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

  namespace ClicShopping\Apps\Catalog\ProductsAttributes\Classes\Shop;

  use ClicShopping\OM\Registry;

  class ProductsAttributesShop {
    protected $lang;
    protected $app;

    public function __construct() {
      $this->lang = Registry::get('Language');
      $this->db = Registry::get('Db');
      $this->productsCommon = Registry::get('ProductsCommon');
    }

/**
 * Count the number of attributes on product
 *
 * @param string
 * @return string $products_attributes['total'], total of attributes
 * @access public
 */
    private function setCountProductsAttributes($id = null) {

      if (is_null($id)) {
        $id = $this->productsCommon->getID();
      }

      $language_id = $this->lang->getId();

      $QproductsAttributes = $this->db->prepare('select count(*) as total
                                                 from :table_products_options popt,
                                                      :table_products_attributes patrib
                                                 where patrib.products_id = :products_id
                                                 and patrib.options_id = popt.products_options_id
                                                 and popt.language_id = :language_id
                                               ');
      $QproductsAttributes->bindInt(':products_id', $id);
      $QproductsAttributes->bindInt(':language_id', $language_id);

      $QproductsAttributes->execute();

      $products_attributes = $QproductsAttributes->fetch();

      return $products_attributes['total'];
    }

    Public function getCountProductsAttributes($id = null)  {
      return $this->setCountProductsAttributes($id);
    }

/**
 * Check if product has attributes
 * @param string $products_id
 * @return the checking of the products attributbes
 */
    public function getHasProductAttributes($id = null) {

      if (is_null($id)) {
        $id = $this->productsCommon->getID();
      }

      $Qattributes = $this->db->prepare('select products_id
                                         from :table_products_attributes
                                         where products_id = :products_id
                                         limit 1
                                        ');
      $Qattributes->bindInt(':products_id', $id);

      $Qattributes->execute();

      return $Qattributes->fetch() !== false;
    }


/**
 * Get attributes Information
 * @param int $products_id
 * @param int $option_id
 * @param int $options_values_id
 * @param int $language_id
 * @return mixed
 */

    public function getProductsAttributesInfo($products_id, $option_id, $options_values_id = null, $language_id) {
      if (!is_null($options_values_id)) {

        $Qattributes = $this->db->prepare('select distinct popt.products_options_name,
                                                           poval.products_options_values_name,
                                                           pa.options_values_price,
                                                           pa.price_prefix,
                                                           pa.products_attributes_reference
                                          from :table_products_options popt,
                                               :table_products_options_values poval,
                                               :table_products_attributes pa
                                          where pa.products_id = :products_id
                                          and pa.options_id = :options_id
                                          and pa.options_id = popt.products_options_id
                                          and pa.options_values_id = :options_values_id
                                          and pa.options_values_id = poval.products_options_values_id
                                          and popt.language_id = :language_id
                                          and poval.language_id = :language_id
                                         ');
        $Qattributes->bindInt(':products_id', $products_id);
        $Qattributes->bindInt(':options_id', $option_id);
        $Qattributes->bindInt(':options_values_id', $options_values_id);
        $Qattributes->bindInt(':language_id', $language_id);

        $Qattributes->execute();

      } else {
        $Qattributes = $this->db->prepare('select distinct pov.products_options_values_id,
                                                            pov.products_options_values_name,
                                                            pa.options_values_price,
                                                            pa.price_prefix,
                                                            pa.products_attributes_reference
                                           from :table_products_attributes pa,
                                                :table_products_options_values pov
                                           where pa.products_id = :products_id
                                           and pa.options_id = :options_id
                                           and pa.options_values_id = pov.products_options_values_id
                                           and pov.language_id = :language_id
                                           order by pa.products_options_sort_order
                                          ');

        $Qattributes->bindInt(':products_id', $products_id);
        $Qattributes->bindInt(':options_id', $option_id);
        $Qattributes->bindInt(':language_id', $language_id);

        $Qattributes->execute();
      }

      return $Qattributes;
    }

/**
 *
 * @param int $id
 * @return bool
 */
    public function getCheckProductsSStatus($id) {
      $Qcheck = $this->db->prepare('select products_id
                                    from :table_products
                                    where products_id = :products_id
                                    and products_status = 1
                                    and products_archive = 0
                                  ');

      $Qcheck->bindInt(':products_id', $id);
      $Qcheck->execute();

      return $Qcheck->fetch();
    }

/**
 * Check products attributes
 * @param int $products_id
 * @param int $option_id
 * @param int $options_values_id
 * @return bool
 */
    public function GetCheckProductsAttributes($products_id, $option_id, $options_values_id) {
      $Qcheck = $this->db->prepare('select products_attributes_id
                                    from :table_products_attributes
                                    where products_id = :products_id
                                    and options_id = :options_id
                                    and options_values_id = :options_values_id
                                    limit 1
                                   ');

      $Qcheck->bindInt(':products_id', $products_id);
      $Qcheck->bindInt(':options_id', $option_id);
      $Qcheck->bindInt(':options_values_id', $options_values_id);

      $Qcheck->execute();

      return $Qcheck->false;
    }

/**
 * Check products download
 * @param int $products_id
 * @param int $options_values_id
 * @return bool
 */
    public function getCheckProductsDownload($products_id, $options_values_id) {
      $Qcheck = $this->db->prepare('select pa.products_attributes_id
                                    from :table_products_attributes pa,
                                         :table_products_attributes_download pad
                                    where pa.products_id = :products_id
                                    and pa.options_values_id = :options_values_id
                                    and pa.products_attributes_id = pad.products_attributes_id
                                    limit 1
                                   ');
      $Qcheck->bindInt(':products_id', $products_id);
      $Qcheck->bindInt(':options_values_id', $options_values_id);

      $Qcheck->execute();

      return $Qcheck->fetch();
    }

/**
 * get the attributes price
 * @param in $products_id, the id of the products
 * @return $attributes_price the price of the attributes
 * @access public
 */
    public function getAttributesPrice($products_id) {
      $attributes_price = 0;

      if (isset($this->contents[$products_id]['attributes'])) {
        foreach ($this->contents[$products_id]['attributes'] as $option => $value) {

          $Qattributes = $this->db->prepare('select options_values_price,
                                                    price_prefix
                                              from :table_products_attributes
                                              where products_id = :products_id
                                              and options_id = :options_id
                                              and options_values_id = :options_values_id
                                             ');
          $Qattributes->bindInt(':products_id', $products_id);
          $Qattributes->bindInt(':options_id', $option);
          $Qattributes->bindInt(':options_values_id', $value);

          $Qattributes->execute();

          if ($Qattributes->fetch() !== false) {
            if ($Qattributes->value('price_prefix') == '+') {
              $attributes_price += $Qattributes->valueDecimal('options_values_price');
            } else {
              $attributes_price -= $Qattributes->valueDecimal('options_values_price');
            }
          }
        }
      }

      return $attributes_price;
    }


/**
 * get the attributes download - used payment
 * @param int $products_id
 * @param int $options_id
 * @param int $options_values_id
 * @param int $language_id
 * @return mixed
 */
    public function getAttributesDownloaded($products_id, $options_id, $options_values_id, $language_id) {
      if (DOWNLOAD_ENABLED == 'true') {
        $Qattributes = $this->db->prepare('select popt.products_options_name,
                                                  poval.products_options_values_name,
                                                  pa.options_values_price,
                                                  pa.price_prefix,
                                                  pa.products_attributes_reference,
                                                  pad.products_attributes_maxdays,
                                                  pad.products_attributes_maxcount,
                                                  pad.products_attributes_filename,
                                                  pa.products_attributes_reference
                                         from :table_products_options popt,
                                              :table_products_options_values poval,
                                              :table_products_attributes pa
                                                left join :table_products_attributes_download pad on pa.products_attributes_id = pad.products_attributes_id
                                         where pa.products_id = :products_id
                                          and pa.options_id = :options_id
                                          and pa.options_id = popt.products_options_id
                                          and pa.options_values_id = :options_values_id
                                          and pa.options_values_id = poval.products_options_values_id
                                          and popt.language_id = :language_id
                                          and popt.language_id = poval.language_id
                                       ');

        $Qattributes->bindInt(':products_id', $products_id);
        $Qattributes->bindInt(':options_id', $options_id);
        $Qattributes->bindInt(':options_values_id', $options_values_id);
        $Qattributes->bindInt(':language_id', $language_id);

        $Qattributes->execute();

      } else {
        $Qattributes = $this->db->prepare('select popt.products_options_name,
                                                 poval.products_options_values_name,
                                                 pa.options_values_price,
                                                 pa.price_prefix,
                                                   pa.products_attributes_reference
                                            from :table_products_options popt,
                                                 :table_products_options_values poval,
                                                 :table_products_attributes pa
                                            where pa.products_id = :products_id
                                            and pa.options_id = :options_id
                                            and pa.options_id = popt.products_options_id
                                            and pa.options_values_id = :options_values_id
                                            and pa.options_values_id = poval.products_options_values_id
                                            and popt.language_id = :language_id
                                            and popt.language_id = poval.language_id
                                          ');

        $Qattributes->bindInt(':products_id', $products_id);
        $Qattributes->bindInt(':options_id', $options_id);
        $Qattributes->bindInt(':options_values_id', $options_values_id);
        $Qattributes->bindInt(':language_id', $language_id);

        $Qattributes->execute();
      }

      return $Qattributes;
    }
  }