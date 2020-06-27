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

  namespace ClicShopping\Apps\Communication\PageManager\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;

  class PageManagerAdmin
  {

    protected int $pages_id;
    protected int $language_id;

    /**
     * Title Name of the submit
     *
     * @param string $pages_id , $language_id
     * @return string product['products_head_title_tag'], description name
     * @access public
     */
    public static function getPageManagerHeadTitleTag(int $pages_id, int $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if ($language_id == 0) $language_id = $CLICSHOPPING_Language->getId();

      $QpageManager = $CLICSHOPPING_Db->prepare('select page_manager_head_title_tag
                                                  from :table_pages_manager_description
                                                  where pages_id = :pages_id
                                                  and language_id = :language_id
                                                 ');
      $QpageManager->bindInt(':pages_id', (int)$pages_id);
      $QpageManager->bindInt(':language_id', (int)$language_id);

      $QpageManager->execute();

      return $QpageManager->value('page_manager_head_title_tag');
    }

    /**
     * Description Name
     *
     * @param string $pages_id , $language_id
     * @return string $page_manager['products_head_desc_tag'], description name
     * @access public
     */
    public static function getPageManagerHeadDescTag(int $pages_id, int $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if ($language_id == 0) $language_id = $CLICSHOPPING_Language->getId();

      $QpageManager = $CLICSHOPPING_Db->prepare('select page_manager_head_desc_tag
                                                from :table_pages_manager_description
                                                where pages_id = :pages_id
                                                and language_id = :language_id
                                               ');
      $QpageManager->bindInt(':pages_id', (int)$pages_id);
      $QpageManager->bindInt(':language_id', (int)$language_id);

      $QpageManager->execute();

      return $QpageManager->value('page_manager_head_desc_tag');
    }

    /**
     * keywords Name
     *
     * @param string $pages_id , $language_id
     * @return string $page_manager['products_head_keywords_tag'], keywords name
     * @access public
     */
    public static function getPageManagerHeadKeywordsTag(int $pages_id, int $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if ($language_id == 0) $language_id = $CLICSHOPPING_Language->getId();

      $QpageManager = $CLICSHOPPING_Db->prepare('select page_manager_head_keywords_tag
                                                from :table_pages_manager_description
                                                where pages_id = :pages_id
                                                and language_id = :language_id
                                               ');
      $QpageManager->bindInt(':pages_id', (int)$pages_id);
      $QpageManager->bindInt(':language_id', (int)$language_id);

      $QpageManager->execute();

      return $QpageManager->value('page_manager_head_keywords_tag');
    }
  }