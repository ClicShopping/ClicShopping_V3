<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
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
   * @param int $pages_id
   * @param int|null $language_id
   * @return string
   */
  public static function getPageManagerTitle(int $pages_id,  int|null $language_id): string
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Language = Registry::get('Language');

    if ($language_id == 0) $language_id = $CLICSHOPPING_Language->getId();

    $QpageManager = $CLICSHOPPING_Db->prepare('select pages_title
                                                  from :table_pages_manager_description
                                                  where pages_id = :pages_id
                                                  and language_id = :language_id
                                                 ');
    $QpageManager->bindInt(':pages_id', (int)$pages_id);
    $QpageManager->bindInt(':language_id', (int)$language_id);

    $QpageManager->execute();

    return $QpageManager->value('pages_title');
  }


  /**
   * @param int $pages_id
   * @param int|null $language_id
   * @return string
   */
  public static function getPageManagerHeadTitleTag(int $pages_id,  int|null $language_id): string
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
   * @param int $pages_id
   * @param int|null $language_id
   * @return string
   */
  public static function getPageManagerHeadDescTag(int $pages_id,  int|null $language_id): string
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
   * @param int $pages_id
   * @param int|null $language_id
   * @return string
   */
  public static function getPageManagerHeadKeywordsTag(int $pages_id,  int|null $language_id): string
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