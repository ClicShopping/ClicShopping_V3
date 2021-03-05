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

  namespace ClicShopping\Sites\Shop\Pages\Search\Actions;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class Q extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {
      $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Search = Registry::get('Search');
      $CLICSHOPPING_Language = Registry::get('Language');

      if ($CLICSHOPPING_Search->hasKeywords() && empty($CLICSHOPPING_Search->hasKeywords()) &&
        ($CLICSHOPPING_Search->getDateFrom() && (empty($CLICSHOPPING_Search->getDateFrom()) || ($CLICSHOPPING_Search->getDateFrom() == CLICSHOPPING::getDef('dob_format_string')))) &&
        ($CLICSHOPPING_Search->getDateTo() && (empty($CLICSHOPPING_Search->getDateTo()) || ($CLICSHOPPING_Search->getDateTo() == CLICSHOPPING::getDef('dob_format_string')))) &&
        ($CLICSHOPPING_Search->getPriceFrom() && !is_numeric($CLICSHOPPING_Search->getPriceFrom())) &&
        ($CLICSHOPPING_Search->getPriceTo() && !is_numeric($CLICSHOPPING_Search->getPriceTo()))) {

        $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_at_least_one_input'), 'danger', 'search');
      } else {
        if (\defined('MODULE_PRODUCTS_SEARCH_MAX_DISPLAY')) {
          $max_display = MODULE_PRODUCTS_SEARCH_MAX_DISPLAY;
        } else {
          $max_display = 1;
        }

        $search = $CLICSHOPPING_Search->getResult();

        $listingTotalRow = $search['total'];

        $Qlisting = $CLICSHOPPING_Search->getListing();
        $Qlisting->setPageSet($max_display);

        $Qlisting->execute();
      }

// templates
      $this->page->setFile('Q.php');
//Content
      $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('advanced_search_result');
//language
      $CLICSHOPPING_Language->loadDefinitions('advanced_search');

      $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('navbar_title_1'), CLICSHOPPING::link(null, 'Search&AdvancedSearch'));
      $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('navbar_title_2'), CLICSHOPPING::link(null, CLICSHOPPING::getAllGET(), true));
    }
  }
