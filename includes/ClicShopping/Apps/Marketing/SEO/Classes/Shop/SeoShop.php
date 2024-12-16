<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\SEO\Classes\Shop;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class SeoShop
{
  private mixed $db;
  private mixed $lang;

  protected string $seoDefaultTitle;
  protected string $seoDefaultDescription;
  protected string $seoDefaultKeywords;
  protected string $seoDefaultProductsNewTitle;
  protected string $seoDefaultProductsNewDescription;
  protected string $seoDefaultProductsNewKeywords;
  protected string $getSeoSpecialsTitle;
  protected string $seoDefaultSpecialsDescription;
  protected string $seoDefaultSpecialsKeywords;
  protected string $seoDefaultReviewsTitle;
  protected string $seoDefaultReviewsDescription;
  protected string $seoDefaultReviewsKeywords;
  protected string $seoDefaultFavoritesTitle;
  protected string $seoDefaultFavoritesdDescription;
  protected string $seoDefaultFavoritesKeywords;
  protected string $seoDefaultFeaturedTitle;
  protected string $seoDefaultFeaturedDescription;
  protected string $seoDefaultFeaturedKeywords;
  protected string $keywordsAdvancedPage;
  protected string $descriptionAdvancedPage;
  protected string $titleAdvancedPage;

  public function __construct()
  {
    $this->db = Registry::get('Db');
    $this->lang = Registry::get('Language');

    $this->getDefaultSeo();
    $this->getDefaultSeoProductsNew();
    $this->getDefaultSeoSpecials();
    $this->getDefaultSeoReviews();
    $this->getDataAdvancedSearchPage();
    $this->getDefaultSeoFeatured();
  }

  /**
   * Retrieves and sets the default SEO metadata including title, description, and keywords based on the language.
   *
   * @return void
   */
  public function getDefaultSeo()
  {
    $Qseo = $this->db->prepare('select seo_defaut_language_title,
                                          seo_defaut_language_keywords,
                                          seo_defaut_language_description
                                    from :table_seo
                                    where seo_id = 1
                                    and language_id = :language_id
                                   ');

    $Qseo->bindInt(':language_id', $this->lang->getId());
    $Qseo->execute();

    $this->seoDefaultTitle = $Qseo->value('seo_defaut_language_title');
    $this->seoDefaultDescription = $Qseo->value('seo_defaut_language_description');
    $this->seoDefaultKeywords = $Qseo->value('seo_defaut_language_keywords');
  }

  /**
   * @return string|null
   */
  public function getSeoIndexTitle(): ?string
  {
    if (empty($this->seoDefaultTitle)) {
      $result = HTML::outputProtected(STORE_NAME);
    } else {
      $result = HTML::outputProtected($this->seoDefaultTitle) . ', ' . HTML::outputProtected(STORE_NAME);
    }

    return $result;
  }

  /**
   * @return string|null
   */
  public function getSeoIndexDescription(): ?string
  {
    if (empty($this->seoDefaultDescription)) {
      $result = HTML::outputProtected(STORE_NAME);
    } else {
      $result = HTML::outputProtected($this->seoDefaultDescription);
    }

    return $result;
  }

  /**
   * @return string|null
   */
  public function getSeoIndexKeywords(): ?string
  {
    if (empty($this->seoDefaultKeywords)) {
      $result = HTML::outputProtected(STORE_NAME);
    } else {
      $result = HTML::outputProtected($this->seoDefaultKeywords);
    }

    return $result;
  }

  /**
   * @return void
   */
  public function getDefaultSeoProductsNew()
  {
    $Qseo = $this->db->prepare('select seo_language_products_new_title,
                                        seo_language_products_new_keywords,
                                        seo_language_products_new_description
                                  from :table_seo
                                  where seo_id = 1
                                  and language_id = :language_id
                                ');
    $Qseo->bindInt(':language_id', $this->lang->getId());
    $Qseo->execute();

    $this->seoDefaultProductsNewTitle = $Qseo->value('seo_language_products_new_title');
    $this->seoDefaultProductsNewDescription = $Qseo->value('seo_language_products_new_description');
    $this->seoDefaultProductsNewKeywords = $Qseo->value('seo_language_products_new_keywords');
  }

  /**
   * @return string|null
   */
  public function getSeoProductsNewTitle(): ?string
  {
    if (empty($this->seoDefaultProductsNewTitle)) {
      $result = CLICSHOPPING::getDef('text_products_news_seo_title') . ', ' . HTML::outputProtected(STORE_NAME);
    } else {
      $result = HTML::outputProtected($this->seoDefaultProductsNewTitle) . ', ' . HTML::outputProtected(STORE_NAME);
    }

    return $result;
  }

  /**
   * @return string|null
   */
  public function getSeoProductsNewDescription(): ?string
  {
    if (empty($this->seoDefaultProductsNewDescription)) {
      $result = HTML::outputProtected(STORE_NAME);
    } else {
      $result = HTML::outputProtected($this->seoDefaultProductsNewDescription);
    }

    return $result;
  }

  /**
   * @return string|null
   */
  public function getSeoProductsNewKeywords(): ?string
  {
    if (empty($this->seoDefaultProductsNewKeywords)) {
      $result = HTML::outputProtected(STORE_NAME);
    } else {
      $result = HTML::outputProtected($this->seoDefaultProductsNewKeywords);
    }

    return $result;
  }

  /**
   * @return void
   */
  public function getDefaultSeoSpecials()
  {
    $Qseo = $this->db->prepare('select seo_language_special_title,
                                         seo_language_special_keywords,
                                         seo_language_special_description
                                  from :table_seo
                                  where seo_id = 1
                                  and language_id = :language_id
                                ');
    $Qseo->bindInt(':language_id', $this->lang->getId());
    $Qseo->execute();

    $this->getSeoSpecialsTitle = $Qseo->value('seo_language_special_title');
    $this->seoDefaultSpecialsKeywords = $Qseo->value('seo_language_special_keywords');
    $this->seoDefaultSpecialsDescription = $Qseo->value('seo_language_special_description');
  }

  /**
   * @return string|null
   */
  public function getSeoSpecialsTitle(): ?string
  {
    if (empty($this->getSeoSpecialsTitle)) {
      $result = CLICSHOPPING::getDef('text_specials_seo_title') . ', ' . HTML::outputProtected(STORE_NAME);
    } else {
      $result = HTML::outputProtected($this->getSeoSpecialsTitle) . ', ' . HTML::outputProtected(STORE_NAME);
    }

    return $result;
  }

  /**
   * @return string|null
   */
  public function getSeoSpecialsDescription(): ?string
  {
    if (empty($this->seoDefaultSpecialsKeywords)) {
      $result = HTML::outputProtected(STORE_NAME);
    } else {
      $result = HTML::outputProtected($this->seoDefaultSpecialsKeywords);
    }

    return $result;
  }

  /**
   * @return string|null
   */
  public function getSeoSpecialsKeywords(): ?string
  {
    if (empty($this->seoDefaultSpecialsDescription)) {
      $result = HTML::outputProtected(STORE_NAME);
    } else {
      $result = HTML::outputProtected($this->seoDefaultSpecialsDescription);
    }

    return $result;
  }

  /**
   * Retrieves the default SEO reviews values including title, description, and keywords
   * for a specific language based on the current language ID.
   *
   * @return void
   */
  public function getDefaultSeoReviews()
  {
    $Qseo = $this->db->prepare('select seo_language_reviews_title,
                                         seo_language_reviews_keywords,
                                         seo_language_reviews_description
                                  from :table_seo
                                  where seo_id = 1
                                  and language_id = :language_id
                                ');
    $Qseo->bindInt(':language_id', $this->lang->getId());
    $Qseo->execute();

    $this->seoDefaultReviewsTitle = $Qseo->value('seo_language_reviews_title');
    $this->seoDefaultReviewsDescription = $Qseo->value('seo_language_reviews_description');
    $this->seoDefaultReviewsKeywords = $Qseo->value('seo_language_reviews_keywords');
  }

  /**
   * @return string|null
   */
  public function getSeoReviewsTitle(): ?string
  {
    if (empty($this->seoDefaultReviewsTitle)) {
      $result = CLICSHOPPING::getDef('text_reviews_seo_title') . ', ' . HTML::outputProtected(STORE_NAME);
    } else {
      $result = HTML::outputProtected($this->seoDefaultReviewsTitle) . ', ' . HTML::outputProtected(STORE_NAME);
    }

    return $result;
  }

  /**
   * @return string|null
   */
  public function getSeoReviewsDescription(): ?string
  {
    if (empty($this->seoDefaultReviewsDescription)) {
      $result = HTML::outputProtected(STORE_NAME);
    } else {
      $result = HTML::outputProtected($this->seoDefaultReviewsDescription);
    }

    return $result;
  }

  /**
   *
   * @return string|null Returns the SEO reviews keywords if set, or the store name as a fallback, both output-protected.
   */
  public function getSeoReviewsKeywords(): ?string
  {
    if (empty($this->seoDefaultReviewsKeywords)) {
      $result = HTML::outputProtected(STORE_NAME);
    } else {
      $result = HTML::outputProtected($this->seoDefaultReviewsKeywords);
    }

    return $result;
  }

  /**
   * Retrieves the default SEO favorites data, including title, description, and keywords,
   * from the database for the current language.
   *
   * @return void
   */
  public function getDefaultSeoFavorites()
  {
    $Qseo = $this->db->prepare('select seo_language_favorites_title,
                                         seo_language_favorites_keywords,
                                         seo_language_favorites_description
                                  from :table_seo
                                  where seo_id = 1
                                  and language_id = :language_id
                                ');
    $Qseo->bindInt(':language_id', $this->lang->getId());
    $Qseo->execute();

    $this->seoDefaultSeoFavoritesTitle = $Qseo->value('seo_language_favorites_title');
    $this->seoDefaultFavoritesDescription = $Qseo->value('seo_language_favorites_description');
    $this->seoDefaultFavoritesKeywords = $Qseo->value('seo_language_favorites_keywords');
  }

  /**
   * Retrieves the SEO title for the favorites page.
   *
   * @return string|null Returns the generated SEO title for the favorites page, or null if not available.
   */
  public function getSeoFavoritesTitle(): ?string
  {
    if (empty($this->seoDefaultSeoFavoritesTitle)) {
      $result = CLICSHOPPING::getDef('text_favorites_seo_title') . ', ' . HTML::outputProtected(STORE_NAME);
    } else {
      $result = HTML::outputProtected($this->seoDefaultSeoFavoritesTitle) . ', ' . HTML::outputProtected(STORE_NAME);
    }

    return $result;
  }

  /**
   * Retrieves the SEO description for the favorites page. If a custom description is not provided,
   * it defaults to the protected output of the store name.
   *
   * @return string|null The SEO description for the favorites page or null if not set.
   */
  public function getSeoFavoritesDescription(): ?string
  {
    if (empty($this->seoDefaultFavoritesDescription)) {
      $result = HTML::outputProtected(STORE_NAME);
    } else {
      $result = HTML::outputProtected($this->seoDefaultFavoritesDescription);
    }

    return $result;
  }

  /**
   * Retrieves the SEO favorite keywords, with a fallback to the store name if none are defined.
   *
   * @return string|null The processed SEO favorite keywords or the store name if no keywords are set.
   */
  public function getSeoFavoritesKeywords(): ?string
  {
    if (empty($this->seoDefaultFavoritesKeywords)) {
      $result = HTML::outputProtected(STORE_NAME);
    } else {
      $result = HTML::outputProtected($this->seoDefaultFavoritesKeywords);
    }

    return $result;
  }

  /**
   * Retrieves the default SEO featured data such as title, keywords, and description for a specific language.
   *
   * @return void
   */
  public function getDefaultSeoFeatured()
  {
    $Qseo = $this->db->prepare('select seo_language_featured_title,
                                         seo_language_featured_keywords,
                                         seo_language_featured_description
                                  from :table_seo
                                  where seo_id = 1
                                  and language_id = :language_id
                                ');
    $Qseo->bindInt(':language_id', $this->lang->getId());
    $Qseo->execute();

    $this->seoDefaultSeoFeaturedTitle = $Qseo->value('seo_language_featured_title');
    $this->seoDefaultFeaturedDescription = $Qseo->value('seo_language_featured_description');
    $this->seoDefaultFeaturedKeywords = $Qseo->value('seo_language_featured_keywords');
  }


  /**
   * Retrieves the SEO featured title for a page, combining either the default SEO title
   * or a fallback text with the store name.
   *
   * @return string|null The generated SEO featured title or null if not set.
   */
  public function getSeoFeaturedTitle(): ?string
  {
    if (empty($this->seoDefaultSeoFeaturedTitle)) {
      $result = CLICSHOPPING::getDef('text_featured_seo_title') . ', ' . HTML::outputProtected(STORE_NAME);
    } else {
      $result = HTML::outputProtected($this->seoDefaultSeoFeaturedTitle) . ', ' . HTML::outputProtected(STORE_NAME);
    }

    return $result;
  }

  /**
   * Retrieves the SEO featured description, using a default value if none is set.
   *
   * @return string|null The protected output of the SEO featured description or the store name if no description is provided.
   */
  public function getSeoFeaturedDescription(): ?string
  {
    if (empty($this->seoDefaultFeaturedDescription)) {
      $result = HTML::outputProtected(STORE_NAME);
    } else {
      $result = HTML::outputProtected($this->seoDefaultFeaturedDescription);
    }

    return $result;
  }

  /**
   * Retrieves the SEO featured keywords for the store.
   *
   * @return string|null The processed SEO featured keywords or the store name if not set.
   */
  public function getSeoFeaturedKeywords(): ?string
  {
    if (empty($this->seoDefaultFeaturedKeywords)) {
      $result = HTML::outputProtected(STORE_NAME);
    } else {
      $result = HTML::outputProtected($this->seoDefaultFeaturedKeywords);
    }

    return $result;
  }

  /**
   * Processes the advanced search keywords from user input, sanitizes them,
   * and assigns a title for the advanced search page based on the provided keywords.
   *
   * @return void
   */
  public function getDataAdvancedSearchPage()
  {

    if (isset($_POST['keywords'])) {
      $keywords = HTML::sanitize($_POST['keywords']);
    } else {
      $keywords = '';
    }

    if (!empty($keywords)) {
      if (empty($this->seoDefaultTitle)) {
        $this->titleAdvancedPage = $keywords;
      } else {
        $this->titleAdvancedPage = $keywords . ',  ' . HTML::sanitize($this->seoDefaultTitle);
      }
    } else {
      $this->titleAdvancedPage = HTML::sanitize($keywords);
    }
  }

  /**
   *
   * @return string|null Returns the title of the advanced search page or null if not set.
   */
  public function getAdvancedSearchTitle(): ?string
  {
    return $this->titleAdvancedPage;
  }

  /**
   * Retrieves the description for the advanced search page.
   *
   * @return string|null Returns the description of the advanced search page or null if not set.
   */
  public function getAdvancedSearchDescription(): ?string
  {
    return $this->descriptionAdvancedPage;
  }

  /**
   *
   * @return string|null The advanced search keywords for the page.
   */
  public function getAdvancedSearchKeywords(): ?string
  {
    return $this->keywordsAdvancedPage;
  }

  /**
   * Retrieves the SEO URL for a specified category and language.
   *
   * @param int $category_id The ID of the category.
   * @param int $language_id The ID of the language. If not provided, the default language ID will be used.
   * @return string The SEO URL of the category.
   */
  public function getCategoriesSeoUrl(int $category_id, int $language_id): string
  {
    if (!$language_id) $language_id = $this->lang->getId();

    $Qseo = $this->db->prepare('select categories_seo_url
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
   * Retrieves the SEO URL for a specific product based on its ID and language ID.
   *
   * @param string|null $products_id The ID of the product for which the SEO URL is to be retrieved. Can be null.
   * @param int $language_id The ID of the language for which the SEO URL is to be retrieved.
   * @return string The SEO URL of the specified product.
   */
  public function getProductsSeoUrl(?string $products_id, int $language_id): string
  {
    if (!$language_id) $language_id = $this->lang->getId();

    $Qseo = $this->db->prepare('select products_seo_url
                                  from :table_products_description
                                  where products_id = :products_id
                                  and language_id = :language_id
                                ');
    $Qseo->bindInt(':products_id', $products_id);
    $Qseo->bindInt(':language_id', $language_id);

    $Qseo->execute();

    return $Qseo->value('products_seo_url');
  }
}
