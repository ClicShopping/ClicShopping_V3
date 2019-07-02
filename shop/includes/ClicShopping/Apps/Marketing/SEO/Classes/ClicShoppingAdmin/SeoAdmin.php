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

  namespace ClicShopping\Apps\Marketing\SEO\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;

  class SeoAdmin
  {

    protected $manufacturers_id;
    protected $language_id;
    protected $product_id;
    protected $category_id;

    /**
     * the manufacturer seo description
     *
     * @param string $manufacturer_id , $language_id
     * @return string $manufacturer['manufacturers_seo_description'],  seo description of the manufacturer
     * @access public
     */
    public static function getManufacturerSeoDescription($manufacturers_id, $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qmanufacturers = $CLICSHOPPING_Db->prepare('select manufacturer_seo_description
                                                  from :table_manufacturers_info
                                                  where manufacturers_id = :manufacturers_id
                                                  and languages_id = :language_id
                                                ');

      $Qmanufacturers->bindInt(':manufacturers_id', (int)$manufacturers_id);
      $Qmanufacturers->bindInt(':language_id', (int)$language_id);
      $Qmanufacturers->execute();


      return $Qmanufacturers->value('manufacturer_seo_description');
    }

    /**
     * the manufacturer seo title
     *
     * @param string $manufacturer_id , $language_id
     * @return string $manufacturer['manufacturers_seo_title'],  seo title of the manufacturer
     * @access public
     */
    public static function getManufacturerSeoTitle($manufacturers_id, $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qmanufacturers = $CLICSHOPPING_Db->prepare('select manufacturer_seo_title
                                                    from :table_manufacturers_info
                                                    where manufacturers_id = :manufacturers_id
                                                    and languages_id = :language_id
                                                  ');

      $Qmanufacturers->bindInt(':manufacturers_id', $manufacturers_id);
      $Qmanufacturers->bindInt(':language_id', $language_id);
      $Qmanufacturers->execute();

      return $Qmanufacturers->value('manufacturer_seo_title');
    }


    /**
     * the manufacturer seo keyword
     *
     * @param string $manufacturer_id , $language_id
     * @return string $manufacturer['manufacturers_seo_keyword'],  seo keyword of the manufacturer
     * @access public
     */
    public static function getManufacturerSeoKeyword($manufacturers_id, $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qmanufacturers = $CLICSHOPPING_Db->prepare('select manufacturer_seo_keyword
                                                    from :table_manufacturers_info
                                                    where manufacturers_id = :manufacturers_id
                                                    and languages_id = :language_id
                                                  ');

      $Qmanufacturers->bindInt(':manufacturers_id', $manufacturers_id);
      $Qmanufacturers->bindInt(':language_id', $language_id);
      $Qmanufacturers->execute();

      return $Qmanufacturers->value('manufacturer_seo_keyword');
    }


    /**
     * Title Name of the submit
     *
     * @param string $product_id , $language_id
     * @return string product['products_head_title_tag'], description name
     * @access public
     */
    public static function getProductsSeoTitle($product_id, $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qproduct = $CLICSHOPPING_Db->prepare('select products_head_title_tag
                                             from :table_products_description
                                             where products_id = :products_id
                                             and language_id = :language_id
                                            ');
      $Qproduct->bindInt(':products_id', $product_id);
      $Qproduct->bindInt(':language_id', $language_id);

      $Qproduct->execute();

      return $Qproduct->value('products_head_title_tag');
    }

    /**
     * Description Name
     *
     * @param string $product_id , $language_id
     * @return string $product['products_head_desc_tag'], description name
     * @access public
     */
    public static function getProductsSeoDescription($product_id, $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qproduct = $CLICSHOPPING_Db->prepare('select products_head_desc_tag
                                             from :table_products_description
                                             where products_id = :products_id
                                             and language_id = :language_id
                                           ');
      $Qproduct->bindInt(':products_id', $product_id);
      $Qproduct->bindInt(':language_id', $language_id);

      $Qproduct->execute();

      return $Qproduct->value('products_head_desc_tag');
    }

    /**
     * keywords Name
     *
     * @param string $product_id , $language_id
     * @return string $product['products_head_keywords_tag'], keywords name
     * @access public
     */
    public static function getProductsSeoKeywords($product_id, $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qproduct = $CLICSHOPPING_Db->prepare('select products_head_keywords_tag
                                             from :table_products_description
                                             where products_id = :products_id
                                             and language_id = :language_id
                                           ');
      $Qproduct->bindInt(':products_id', $product_id);
      $Qproduct->bindInt(':language_id', $language_id);

      $Qproduct->execute();

      return $Qproduct->value('products_head_keywords_tag');
    }


    /**
     * Tag Name
     *
     * @param string $product_id , $language_id
     * @return string $product['products_head_tag'], keywords name
     * @access public
     */
    public static function getProductsSeoTag($product_id, $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qproduct = $CLICSHOPPING_Db->prepare('select products_head_tag
                                             from :table_products_description
                                             where products_id = :products_id
                                             and language_id = :language_id
                                           ');
      $Qproduct->bindInt(':products_id', $product_id);
      $Qproduct->bindInt(':language_id', $language_id);

      $Qproduct->execute();

      return $Qproduct->value('products_head_tag');
    }

    public static function getCategoriesSeoTitle($category_id, $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $Qcategory = $CLICSHOPPING_Db->prepare('select categories_head_title_tag
                                              from :table_categories_description
                                              where categories_id = :categories_id
                                              and language_id = :language_id
                                            ');
      $Qcategory->bindInt(':categories_id', $category_id);
      $Qcategory->bindInt(':language_id', $language_id);

      $Qcategory->execute();

      return $Qcategory->value('categories_head_title_tag');
    }

    public static function getCategoriesSeoDescription($category_id, $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $Qcategory = $CLICSHOPPING_Db->prepare('select categories_head_desc_tag
                                              from :table_categories_description
                                              where categories_id = :categories_id
                                              and language_id = :language_id
                                            ');
      $Qcategory->bindInt(':categories_id', $category_id);
      $Qcategory->bindInt(':language_id', $language_id);

      $Qcategory->execute();

      return $Qcategory->value('categories_head_desc_tag');
    }

    public static function getCategoriesSeoKeywords($category_id, $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $Qcategory = $CLICSHOPPING_Db->prepare('select categories_head_keywords_tag
                                              from :table_categories_description
                                              where categories_id = :categories_id
                                              and language_id = :language_id
                                            ');
      $Qcategory->bindInt(':categories_id', $category_id);
      $Qcategory->bindInt(':language_id', $language_id);

      $Qcategory->execute();

      return $Qcategory->value('categories_head_keywords_tag');
    }
  }