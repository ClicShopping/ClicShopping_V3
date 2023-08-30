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
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoDefaultLanguageTitleH1(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_defaut_language_title_h1', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_defaut_language_title_h1');
  }

  /**
   * Get the default title
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoDefaultLanguageTitle(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_defaut_language_title', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_defaut_language_title');
  }

  /**
   * Get the default Description
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoDefaultLanguageDescription(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_defaut_language_description', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_defaut_language_description');
  }

  /**
   * Get the default keywords
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoDefaultLanguageKeywords(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_defaut_language_keywords', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_defaut_language_keywords');
  }

  /**
   * Get the default footer
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoDefaultLanguageFooter(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_defaut_language_footer', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_defaut_language_footer');
  }

  /**
   * Get the products info title
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoProductsInfoTitle(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_language_products_info_title', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_language_products_info_title');
  }


  /**
   * Get the products info description
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoProductsInfoDescription(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_language_products_info_description', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_language_products_info_description');
  }

  /**
   * Get the products info description
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoProductsInfoKeywords(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_language_products_info_keywords', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_language_products_info_keywords');
  }

  /**
   * Get the poducts new title
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoProductsNewTitle(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_language_products_new_title', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_language_products_new_title');
  }

  /**
   * Get the poducts new description
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoProductsNewDescription(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_language_products_new_description', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_language_products_new_description');
  }


  /**
   * Get the poducts new keywords
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoProductsNewKeywords(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_language_products_new_keywords', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_language_products_new_keywords');
  }

  /**
   * Get the poducts speciql title
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoProductsSpecialsTitle(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_language_special_title', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_language_special_title');
  }

  /**
   * Get the poducts speciql description
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoProductsSpecialsDescription(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_language_special_description', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_language_special_description');
  }

  /**
   * Get the poducts special keywords
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoProductsSpecialskeywords(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_language_special_keywords', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_language_special_keywords');
  }

  /**
   * Get the poducts review title
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoProductsReviewsTitle(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_language_reviews_title', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_language_reviews_title');
  }

  /**
   * Get the poducts review description
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoProductsReviewsDescription(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_language_reviews_description', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_language_reviews_description');
  }

  /**
   * Get the poducts review keywords
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoProductsReviewsKeywords(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_language_reviews_keywords', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_language_reviews_keywords');
  }

  /**
   * Get the poducts fqvorites title
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoFavoritesTitle(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_language_favorites_title', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_language_favorites_title');
  }

  /**
   * Get the poducts fqvorites description
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoFavoritesDescription(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_language_favorites_description', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_language_favorites_description');
  }

  /**
   * Get the poducts favorites keywords
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoFavoritesKeywords(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_language_favorites_keywords', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_language_favorites_keywords');
  }

  /**
   * Get the poducts featured Title
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoFeaturedTitle(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_language_featured_title', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_language_featured_title');
  }

  /**
   * Get the poducts featured description
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoFeaturedDescription(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_language_featured_description', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_language_featured_description');
  }


  /**
   * Get the poducts featured keywords
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoFeaturedkeywords(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_language_featured_keywords', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_language_featured_keywords');
  }

  /**
   * the manufacturer seo description
   *
   * @param int|null $manufacturers_id
   * @param int $language_id
   * @return string $manufacturer['manufacturers_seo_description']
   *
   */
  public static function getManufacturerSeoDescription(?int $manufacturers_id, int $language_id): string
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
  public static function getManufacturerSeoKeyword(?int $manufacturers_id, int $language_id): string
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
  public static function getProductsSeoTitle(string $product_id, int $language_id): string
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
  public static function getProductsSeoDescription(string $product_id, int $language_id): string
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
  public static function getProductsSeoKeywords(string $product_id, int $language_id): string
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
  public static function getProductsSeoTag(string $product_id, int $language_id): string
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
  public static function getProductsSeoUrl(string $products_id, int $language_id): string
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
  public static function getCategoriesSeoUrl(?int $category_id, int $language_id): string
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
  public static function getCategoriesSeoTitle(?int $category_id, int $language_id): string
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
  public static function getCategoriesSeoDescription(?int $category_id, int $language_id): string
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
  public static function getCategoriesSeoKeywords(?int $category_id, int $language_id): string
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

  /**
   * Get the default title
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoRecommendationsLanguageTitle(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_language_recommendations_title', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_language_recommendations_title');
  }

  /**
   * Get the Recommendations Description
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoRecommendationsLanguageDescription(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_language_recommendations_description', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_language_recommendations_description');
  }

  /**
   * Get the Recommendations keywords
   * @param int $seo_id
   * @param int $language_id
   * @return string
   */
  public static function getSeoRecommendationstLanguageKeywords(int $seo_id, int $language_id): string
  {
    $Qseo = Registry::get('Db')->get('seo', 'seo_language_recommendations_keywords', ['seo_id' => $seo_id, 'language_id' => $language_id]);

    return $Qseo->value('seo_language_recommendations_keywords');
  }
}