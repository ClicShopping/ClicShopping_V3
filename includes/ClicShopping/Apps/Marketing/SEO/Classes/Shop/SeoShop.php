<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Marketing\SEO\Classes\Shop;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class SeoShop
  {
    protected mixed $db;
    protected mixed $lang;

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
    protected string $seoDefaultSeofeaturedTitle;
    protected string $seoDefaultFavoritesdDescription;
    protected string $seoDefaultFavoritesKeywords;
    protected string $seoDefaultSeoFeaturedTitle;
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
     * get default seo element
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
     * @return string
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
     * @return string
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
     * @return string
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
     * get default seo element products new
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
     * title for products new
     * @return string
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
     * description for products new
     * @return string
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
     * keywords products new
     * @return string
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
     * get default seo element specials products
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
     * title for specials
     * @return string
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
     * description for specials
     * @return string
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
     * keywords specials
     * @return string
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
     * get default seo element revieys products
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
     * title for specials
     * @return string
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
     * description for specials
     * @return string
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
     * keywords Reviews
     * @return string
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
     * get default seo element favorites products
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
     * title for favorites
     * @return string
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
     * description for favorites
     * @return string
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
     * keywords favorites
     * @return string
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
     * get default seo element featured products
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
     * title for featured
     * @return string
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
     * description for featured
     * @return string
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
     * keywords featured
     * @return string
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
     * Get info about the Search Page
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
     * @return string|null
     */
    public function getAdvancedSearchTitle(): ?string
    {
      return $this->titleAdvancedPage;
    }

    /**
     * @return string|null
     */
    public function getAdvancedSearchDescription(): ?string
    {
      return $this->descriptionAdvancedPage;
    }

    /**
     * @return string|null
     */
    public function getAdvancedSearchKeywords(): ?string
    {
      return $this->keywordsAdvancedPage;
    }

    /**
     * Categories SEO URL
     * @param int $category_id
     * @param int $language_id
     * @return string
     */
    public function getCategoriesSeoUrl(int $category_id, int $language_id) :string
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
     * Products Seo URL
     * @param string|null $products_id
     * @param int $language_id
     * @return string
     */
    public function getProductsSeoUrl(?string $products_id, int $language_id) :string
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
