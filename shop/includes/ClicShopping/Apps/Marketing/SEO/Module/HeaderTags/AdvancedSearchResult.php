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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Marketing\SEO\SEO as SEOApp;

  class AdvancedSearchResult extends \ClicShopping\OM\Modules\HeaderTagsAbstract {

    protected $lang;
    protected $app;
    protected $group;

    protected function init() {
      if (!Registry::exists('SEO')) {
        Registry::set('SEO', new SEOApp());
      }

      $this->app = Registry::get('SEO');

      $this->lang = Registry::get('Language');
      $this->group = 'header_tags'; // could be header_tags or footer_scripts

      $this->app->loadDefinitions('Module/header_tag/advanced_search_result');

      $this->title = $this->app->getDef('module_header_tags_advanced_search_result_title');
      $this->description = $this->app->getDef('module_header_tags_advanced_search_result_description');

      if (defined('MODULE_HEADER_TAGS_ADVANCED_SEARCH_RESULT_STATUS')) {
        $this->sort_order = MODULE_HEADER_TAGS_ADVANCED_SEARCH_RESULT_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_ADVANCED_SEARCH_RESULT_STATUS == 'True');
      }
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function getOutput() {

      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (isset($_GET['Search']) && isset($_GET['Q'])) {

        $Qsubmit = $this->app->db->prepare('select submit_id,
                                                language_id,
                                                submit_defaut_language_title,
                                                submit_defaut_language_keywords,
                                                submit_defaut_language_description
                                          from :table_submit_description
                                          where submit_id = :submit_id
                                          and language_id = :language_id
                                        ');
        $Qsubmit->bindInt(':submit_id', 1);
        $Qsubmit->bindInt(':language_id',  (int)$CLICSHOPPING_Language->getId() );
        $Qsubmit->execute();
        $submit = $Qsubmit->fetch();

// Definition de la variable de gestion des colonnes
        $tags_array = [];

        $keywords = HTML::outputProtected($_GET['keywords']);

        if (!empty($keywords) ) {

          if(empty($keywords)) {
            if (empty($submit['submit_defaut_language_title'])) {
              $tags_array['title']= $keywords .', ' . HTML::outputProtected(TITLE);
            } else {
              $tags_array['title'] = $keywords . ',  '. HTML::sanitize($submit['submit_defaut_language_title']);
            }
          } else {
            $tags_array['title'] = HTML::sanitize($keywords) .', ' . HTML::outputProtected(TITLE);
          }

          if(empty($categories['categories_head_desc_tag'])) {
            if (empty($submit['submit_defaut_language_description'])) {
              $tags_array['desc']= $keywords .', ' . HTML::outputProtected(TITLE);
            } else {
              $tags_array['desc'] = $keywords . ', ' .  HTML::sanitize($submit['submit_defaut_language_description']);
            }
          } else {
            $tags_array['desc'] = HTML::sanitize($keywords)  . ', ' . HTML::outputProtected(TITLE);
          }

          if(empty($categories['categories_head_keywords_tag'])) {
            if (empty($submit['submit_defaut_language_keywords'])) {
              $tags_array['keywords']= $keywords .', ' . HTML::outputProtected(TITLE);
            } else {
              $tags_array['keywords']= $keywords . ', ' . HTML::sanitize($submit['submit_defaut_language_keywords']);
            }
          } else {
            $tags_array['keywords']= $keywords . ', ' . HTML::outputProtected(TITLE);
          }

          $title = $CLICSHOPPING_Template->setTitle($tags_array['title'] . ', ' . $CLICSHOPPING_Template->getTitle());
          $description = $CLICSHOPPING_Template->setDescription($tags_array['desc'] . ', ' . $CLICSHOPPING_Template->getDescription());
          $keywords = $CLICSHOPPING_Template->setKeywords($tags_array['keywords'] . ', ' . $CLICSHOPPING_Template->getKeywords());
          $new_keywords = $CLICSHOPPING_Template->setNewsKeywords($tags_array['keywords'] . ', ' . $CLICSHOPPING_Template->getKeywords());

          $output =
 <<<EOD
{$title}
{$description}
{$keywords}
{$new_keywords}
EOD;

        }

        return $output;
      }
    }



    public function install() {

      $this->app->db->save('configuration', [
          'configuration_title' => 'Souhaitez vous activer ce module ?',
          'configuration_key' => 'MODULE_HEADER_TAGS_ADVANCED_SEARCH_RESULT_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Recherche avancée pour les metas tags',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $this->app->db->save('configuration', [
          'configuration_title' => 'Ordre de tri d\'affichage',
          'configuration_key' => 'MODULE_HEADER_TAGS_ADVANCED_SEARCH_RESULT_SORT_ORDER',
          'configuration_value' => '45',
          'configuration_description' => 'Ordre de tri pour l\'affichage (Le plus petit nombre est montré en premier)',
          'configuration_group_id' => '6',
          'sort_order' => '5',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      return $this->app->db->save('configuration', ['configuration_value' => '1'],
                                                   ['configuration_key' => 'WEBSITE_MODULE_INSTALLED']
                                );
    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array('MODULE_HEADER_TAGS_ADVANCED_SEARCH_RESULT_STATUS',
                   'MODULE_HEADER_TAGS_ADVANCED_SEARCH_RESULT_SORT_ORDER');
    }
  }
