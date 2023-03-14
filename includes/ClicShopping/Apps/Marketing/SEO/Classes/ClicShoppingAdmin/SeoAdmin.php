<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Marketing\SEO\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;

  class SeoAdmin
  {
    protected int $manufacturers_id;
    protected int $language_id;
    protected int $product_id;
    protected int $category_id;

    /**
     * Get the default title H1 on index page
     * @param int $submit_id
     * @param int $language_id
     * @return string
     */
    public static function getSeoDefaultLanguageTitleH1(int $submit_id, int $language_id): string
    {
      $Qseo = Registry::get('Db')->get('seo', 'submit_defaut_language_title_h1', ['submit_id' => $submit_id, 'language_id' => $language_id]);

      return $Qseo->value('submit_defaut_language_title_h1');
    }

    /**
     * Get the default title
     * @param int $submit_id
     * @param int $language_id
     * @return string
     */
    public static function getSeoDefaultLanguageTitle(int $submit_id, int $language_id): string
    {
      $Qseo = Registry::get('Db')->get('seo', 'submit_defaut_language_title', ['submit_id' => $submit_id, 'language_id' => $language_id]);

      return $Qseo->value('submit_defaut_language_title');
    }

    /**
     * Get the default Description
     * @param int $submit_id
     * @param int $language_id
     * @return string
     */
    public static function getSeoDefaultLanguageDescription(int $submit_id, int $language_id): string
    {
      $Qseo = Registry::get('Db')->get('seo', 'submit_defaut_language_description', ['submit_id' => $submit_id, 'language_id' => $language_id]);

      return $Qseo->value('submit_defaut_language_description');
    }

    /**
     * Get the default keywords
     * @param int $submit_id
     * @param int $language_id
     * @return string
     */
    public static function getSeoDefaultLanguageKeywords(int $submit_id, int $language_id): string
    {
      $Qseo = Registry::get('Db')->get('seo', 'submit_defaut_language_keywords', ['submit_id' => $submit_id, 'language_id' => $language_id]);

      return $Qseo->value('submit_defaut_language_keywords');
    }

    /**
     * Get the default footer
     * @param int $submit_id
     * @param int $language_id
     * @return string
     */
    public static function getSeoDefaultLanguageFooter(int $submit_id, int $language_id): string
    {
      $Qseo = Registry::get('Db')->get('seo', 'submit_defaut_language_footer', ['submit_id' => $submit_id, 'language_id' => $language_id]);

      return $Qseo->value('submit_defaut_language_footer');
    }

    /**
     * Get the products info title
     * @param int $submit_id
     * @param int $language_id
     * @return string
     */
    public static function getSeoProductsInfoTitle(int $submit_id, int $language_id): string
    {
      $Qseo = Registry::get('Db')->get('seo', 'submit_language_products_info_title', ['submit_id' => $submit_id, 'language_id' => $language_id]);

      return $Qseo->value('submit_language_products_info_title');
    }


    /**
     * Get the products info description
     * @param int $submit_id
     * @param int $language_id
     * @return string
     */
    public static function getSeoProductsInfoDescription(int $submit_id, int $language_id): string
    {
      $Qseo = Registry::get('Db')->get('seo', 'submit_language_products_info_description', ['submit_id' => $submit_id, 'language_id' => $language_id]);

      return $Qseo->value('submit_language_products_info_description');
    }

    /**
     * Get the products info description
     * @param int $submit_id
     * @param int $language_id
     * @return string
     */
    public static function getSeoProductsInfoKeywords(int $submit_id, int $language_id): string
    {
      $Qseo = Registry::get('Db')->get('seo', 'submit_language_products_info_keywords', ['submit_id' => $submit_id, 'language_id' => $language_id]);

      return $Qseo->value('submit_language_products_info_keywords');
    }

    /**
     * Get the poducts new title
     * @param int $submit_id
     * @param int $language_id
     * @return string
     */
    public static function getSeoProductsNewTitle(int $submit_id, int $language_id): string
    {
      $Qseo = Registry::get('Db')->get('seo', 'submit_language_products_new_title', ['submit_id' => $submit_id, 'language_id' => $language_id]);

      return $Qseo->value('submit_language_products_new_title');
    }

    /**
     * Get the poducts new description
     * @param int $submit_id
     * @param int $language_id
     * @return string
     */
    public static function getSeoProductsNewDescription(int $submit_id, int $language_id): string
    {
      $Qseo = Registry::get('Db')->get('seo', 'submit_language_products_new_description', ['submit_id' => $submit_id, 'language_id' => $language_id]);

      return $Qseo->value('submit_language_products_new_description');
    }


    /**
     * Get the poducts new keywords
     * @param int $submit_id
     * @param int $language_id
     * @return string
     */
    public static function getSeoProductsNewKeywords(int $submit_id, int $language_id): string
    {
      $Qseo = Registry::get('Db')->get('seo', 'submit_language_products_new_keywords', ['submit_id' => $submit_id, 'language_id' => $language_id]);

      return $Qseo->value('submit_language_products_new_keywords');
    }

    /**
     * Get the poducts speciql title
     * @param int $submit_id
     * @param int $language_id
     * @return string
     */
    public static function getSeoProductsSpecialsTitle(int $submit_id, int $language_id): string
    {
      $Qseo = Registry::get('Db')->get('seo', 'submit_language_special_title', ['submit_id' => $submit_id, 'language_id' => $language_id]);

      return $Qseo->value('submit_language_special_title');
    }

    /**
     * Get the poducts speciql description
     * @param int $submit_id
     * @param int $language_id
     * @return string
     */
    public static function getSeoProductsSpecialsDescription(int $submit_id, int $language_id): string
    {
      $Qseo = Registry::get('Db')->get('seo', 'submit_language_special_description', ['submit_id' => $submit_id, 'language_id' => $language_id]);

      return $Qseo->value('submit_language_special_description');
    }

    /**
     * Get the poducts special keywords
     * @param int $submit_id
     * @param int $language_id
     * @return string
     */
    public static function getSeoProductsSpecialskeywords(int $submit_id, int $language_id): string
    {
      $Qseo = Registry::get('Db')->get('seo', 'submit_language_special_keywords', ['submit_id' => $submit_id, 'language_id' => $language_id]);

      return $Qseo->value('submit_language_special_keywords');
    }

    /**
     * Get the poducts review title
     * @param int $submit_id
     * @param int $language_id
     * @return string
     */
    public static function getSeoProductsReviewsTitle(int $submit_id, int $language_id): string
    {
      $Qseo = Registry::get('Db')->get('seo', 'submit_language_reviews_title', ['submit_id' => $submit_id, 'language_id' => $language_id]);

      return $Qseo->value('submit_language_reviews_title');
    }

    /**
     * Get the poducts review description
     * @param int $submit_id
     * @param int $language_id
     * @return string
     */
    public static function getSeoProductsReviewsDescription(int $submit_id, int $language_id): string
    {
      $Qseo = Registry::get('Db')->get('seo', 'submit_language_reviews_description', ['submit_id' => $submit_id, 'language_id' => $language_id]);

      return $Qseo->value('submit_language_reviews_description');
    }

    /**
     * Get the poducts review keywords
     * @param int $submit_id
     * @param int $language_id
     * @return string
     */
    public static function getSeoProductsReviewsKeywords(int $submit_id, int $language_id): string
    {
      $Qseo = Registry::get('Db')->get('seo', 'submit_language_reviews_keywords', ['submit_id' => $submit_id, 'language_id' => $language_id]);

      return $Qseo->value('submit_language_reviews_keywords');
    }


    /**
     * Get the poducts fqvorites title
     * @param int $submit_id
     * @param int $language_id
     * @return string
     */
    public static function getSeoFavoritesTitle(int $submit_id, int $language_id): string
    {
      $Qseo = Registry::get('Db')->get('seo', 'submit_language_favorites_title', ['submit_id' => $submit_id, 'language_id' => $language_id]);

      return $Qseo->value('submit_language_favorites_title');
    }

    /**
     * Get the poducts fqvorites description
     * @param int $submit_id
     * @param int $language_id
     * @return string
     */
    public static function getSeoFavoritesDescription(int $submit_id, int $language_id): string
    {
      $Qseo = Registry::get('Db')->get('seo', 'submit_language_favorites_description', ['submit_id' => $submit_id, 'language_id' => $language_id]);

      return $Qseo->value('submit_language_favorites_description');
    }

    /**
     * Get the poducts favorites keywords
     * @param int $submit_id
     * @param int $language_id
     * @return string
     */
    public static function getSeoFavoritesKeywords(int $submit_id, int $language_id): string
    {
      $Qseo = Registry::get('Db')->get('seo', 'submit_language_favorites_keywords', ['submit_id' => $submit_id, 'language_id' => $language_id]);

      return $Qseo->value('submit_language_favorites_keywords');
    }

    /**
     * Get the poducts featured Title
     * @param int $submit_id
     * @param int $language_id
     * @return string
     */
    public static function getSeoFeaturedTitle(int $submit_id, int $language_id): string
    {
      $Qseo = Registry::get('Db')->get('seo', 'submit_language_featured_title', ['submit_id' => $submit_id, 'language_id' => $language_id]);

      return $Qseo->value('submit_language_featured_title');
    }

    /**
     * Get the poducts featured description
     * @param int $submit_id
     * @param int $language_id
     * @return string
     */
    public static function getSeoFeaturedDescription(int $submit_id, int $language_id): string
    {
      $Qseo = Registry::get('Db')->get('seo', 'submit_language_featured_description', ['submit_id' => $submit_id, 'language_id' => $language_id]);

      return $Qseo->value('submit_language_featured_description');
    }


    /**
     * Get the poducts featured keywords
     * @param int $submit_id
     * @param int $language_id
     * @return string
     */
    public static function getSeoFeaturedkeywords(int $submit_id, int $language_id): string
    {
      $Qseo = Registry::get('Db')->get('seo', 'submit_language_featured_keywords', ['submit_id' => $submit_id, 'language_id' => $language_id]);

      return $Qseo->value('submit_language_featured_keywords');
    }

    /**
     * the manufacturer seo description
     *
     * @param int|null $manufacturers_id
     * @param int $language_id
     * @return string $manufacturer['manufacturers_seo_description']
     *
     */
    public static function getManufacturerSeoDescription(?int $manufacturers_id, int $language_id) :string
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qmanufacturers = $CLICSHOPPING_Db->prepare('select manufacturer_seo_description
                                                  from :table_manufacturers_info
                                                  where manufacturers_id = :manufacturers_id
                                                  and languages_id = :language_id
                                                ');

      $Qmanufacturers->bindInt(':manufacturers_id', $manufacturers_id);
      $Qmanufacturers->bindInt(':language_id', $language_id);
      $Qmanufacturers->execute();


      return $Qmanufacturers->value('manufacturer_seo_description');
    }

    /**
     * the manufacturer seo title
     *
     * @param int|null $manufacturer_id , $language_id
     * @return string $manufacturer['manufacturers_seo_title'],  seo title of the manufacturer
     *
     */
    public static function getManufacturerSeoTitle(?int $manufacturers_id, int $language_id)
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
     * @param int|null $manufacturers_id
     * @param int $language_id
     * @return string $manufacturer['manufacturers_seo_keyword'],  seo keyword of the manufacturer
     */
    public static function getManufacturerSeoKeyword(?int $manufacturers_id, int $language_id) :string
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
     * @param int $language_id
     * @return string product['products_head_title_tag'], description name
     */
    public static function getProductsSeoTitle(string $product_id, int $language_id) :string
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
     * @param int $language_id
     * @return string $product['products_head_desc_tag'], description name
     */
    public static function getProductsSeoDescription(string $product_id, int $language_id) :string
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
     * @param int $language_id
     * @return string $product['products_head_keywords_tag'], keywords name
     */
    public static function getProductsSeoKeywords(string $product_id, int $language_id) :string
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
     * @param int $language_id
     * @return string $product['products_head_tag'], keywords name
     */
    public static function getProductsSeoTag(string $product_id, int $language_id) :string
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

    /**
     * SEO URl
     * @param int|null $products_id
     * @param int $language_id
     * @return string
     */
    public static function getProductsSeoUrl(string $products_id, int $language_id) :string
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $Qseo = $CLICSHOPPING_Db->prepare('select products_seo_url
                                        from :table_products_description
                                        where products_id = :products_id
                                        and language_id = :language_id
                                      ');
      $Qseo->bindInt(':products_id', $products_id);
      $Qseo->bindInt(':language_id', $language_id);

      $Qseo->execute();

      return $Qseo->value('products_seo_url');
    }


    /**
     * SEO URl
     * @param int|null $category_id
     * @param int $language_id
     * @return string
     */
    public static function getCategoriesSeoUrl(?int $category_id, int $language_id) :string
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $Qseo = $CLICSHOPPING_Db->prepare('select categories_seo_url
                                        from :table_categories_description
                                        where categories_id = :categories_id
                                        and language_id = :language_id
                                      ');
      $Qseo->bindInt(':categories_id', $category_id);
      $Qseo->bindInt(':language_id', $language_id);

      $Qseo->execute();

      return $Qseo->value('categories_seo_url');
    }

    /**
     * @param int|null $category_id
     * @param int $language_id
     * @return string
     */
    public static function getCategoriesSeoTitle(?int $category_id, int $language_id) :string
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $Qseo = $CLICSHOPPING_Db->prepare('select categories_head_title_tag
                                          from :table_categories_description
                                          where categories_id = :categories_id
                                          and language_id = :language_id
                                        ');
      $Qseo->bindInt(':categories_id', $category_id);
      $Qseo->bindInt(':language_id', $language_id);

      $Qseo->execute();

      return $Qseo->value('categories_head_title_tag');
    }

    /**
     * @param int|null $category_id
     * @param int $language_id
     * @return string
     */
    public static function getCategoriesSeoDescription(?int $category_id, int $language_id) :string
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $Qseo = $CLICSHOPPING_Db->prepare('select categories_head_desc_tag
                                              from :table_categories_description
                                              where categories_id = :categories_id
                                              and language_id = :language_id
                                            ');
      $Qseo->bindInt(':categories_id', $category_id);
      $Qseo->bindInt(':language_id', $language_id);

      $Qseo->execute();

      return $Qseo->value('categories_head_desc_tag');
    }

    /**
     * @param int|null $category_id
     * @param int $language_id
     * @return string
     */
    public static function getCategoriesSeoKeywords(?int $category_id, int $language_id) :string
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $Qseo = $CLICSHOPPING_Db->prepare('select categories_head_keywords_tag
                                              from :table_categories_description
                                              where categories_id = :categories_id
                                              and language_id = :language_id
                                            ');
      $Qseo->bindInt(':categories_id', $category_id);
      $Qseo->bindInt(':language_id', $language_id);

      $Qseo->execute();

      return $Qseo->value('categories_head_keywords_tag');
    }
  }