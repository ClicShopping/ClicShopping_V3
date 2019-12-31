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

  namespace ClicShopping\Apps\Marketing\SEO\Classes\Shop;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class SeoShop
  {

    /**
     * @var string
     */
    private $keywordsIndexPage;
    /**
     * @var string
     */
    private $titleIndexPage;
    /**
     * @var string
     */
    private $descriptionIndexPage;


    public function __construct()
      {
        $this->getDataIndexPage();
        $this->getDataAdvancedSearchPage();
      }

    /**
     * get Data for Index page
     */
    public function getDataIndexPage()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qsubmit = $CLICSHOPPING_Db->prepare('select submit_id,
                                                  language_id,
                                                  submit_defaut_language_title,
                                                  submit_defaut_language_keywords,
                                                  submit_defaut_language_description
                                              from :table_submit_description
                                              where submit_id = 1
                                              and language_id = :language_id
                                           ');

      $Qsubmit->bindInt(':language_id', $CLICSHOPPING_Language->getId());
      $Qsubmit->execute();
      $submit = $Qsubmit->fetch();

      if (empty($submit['submit_defaut_language_title'])) {
        $this->titleIndexPage = HTML::outputProtected(STORE_NAME);
      } else {
        $this->titleIndexPage = HTML::sanitize($submit['submit_defaut_language_title']);
      }

      if (empty($submit['submit_defaut_language_description'])) {
        $this->descriptionIndexPage = HTML::outputProtected(STORE_NAME);
      } else {
        $this->descriptionIndexPage = HTML::sanitize($submit['submit_defaut_language_description']);
      }

      if (empty($submit['submit_defaut_language_keywords'])) {
        $this->keywordsIndexPage = HTML::outputProtected(STORE_NAME);
      } else {
        $this->keywordsIndexPage = HTML::sanitize($submit['submit_defaut_language_keywords']);
      }
    }

    /**
     * @return string|null
     */
    public function getSeoIndexTitle(): ?string
    {
      return $this->titleIndexPage;
    }

    /**
     * @return string|null
     */
    public function getSeoIndexDescription(): ?string
    {
      return $this->descriptionIndexPage;
    }

    /**
     * @return string|null
     */
    public function getSeoIndexKeywords(): ?string
    {
      return $this->keywordsIndexPage;
    }

    /**
     * Get info about the Search Page
     */
    public function getDataAdvancedSearchPage()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qsubmit = $CLICSHOPPING_Db->prepare('select submit_id,
                                                language_id,
                                                submit_defaut_language_title,
                                                submit_defaut_language_keywords,
                                                submit_defaut_language_description
                                          from :table_submit_description
                                          where submit_id = :submit_id
                                          and language_id = :language_id
                                        ');
      $Qsubmit->bindInt(':submit_id', 1);
      $Qsubmit->bindInt(':language_id', $CLICSHOPPING_Language->getId());

      $Qsubmit->execute();

      if (isset($_POST['keywords'])) {
        $keywords = HTML::sanitize($_POST['keywords']);
      } else {
        $keywords = '';
      }

      if (!empty($keywords)) {
        if (empty($submit['submit_defaut_language_title'])) {
          $this->titleAdvancedPage = $keywords;
        } else {
          $this->titleAdvancedPage = $keywords . ',  ' . HTML::sanitize($Qsubmit->value('submit_defaut_language_title'));
        }
      } else {
        $this->titleAdvancedPage = HTML::sanitize($keywords);
      }

      if (empty($categories['categories_head_desc_tag'])) {
        if (empty($submit['submit_defaut_language_description'])) {
          $this->descriptionAdvancedPage = $keywords;
        } else {
          $this->descriptionAdvancedPage = $keywords . ', ' . HTML::sanitize($Qsubmit->value('submit_defaut_language_description'));
        }
      } else {
        $this->descriptionAdvancedPage = HTML::sanitize($keywords);
      }

      if (empty($categories['categories_head_keywords_tag'])) {
        if (empty($submit['submit_defaut_language_keywords'])) {
          $this->keywordsAdvancedPage = $keywords;
        } else {
          $this->keywordsAdvancedPage = $keywords . ', ' . HTML::sanitize($Qsubmit->value('submit_defaut_language_keywords'));
        }
      } else {
        $this->keywordsAdvancedPage = $keywords;
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



  }