<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Apps\Communication\PageManager\Sites\Shop\Pages\Contact\Actions;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class Contact extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');
      $CLICSHOPPING_PageManager = Registry::get('PageManager');

// select multiple contact for the form
      if (!empty(CONTACT_DEPARTMENT_LIST)){
        foreach(explode("," ,CONTACT_DEPARTMENT_LIST) as $k => $v) {
          $send_to_array[] = ['id' => $k,
                              'text' => preg_replace('/\<[^*]*/', '', $v)
                             ];
        }

        $_POST['send_to_array'] = $send_to_array;
      }

// templates
      $this->page->setFile('contact.php');
//Content
      $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('contact');
      $this->page->data['action'] = 'Process';
//language
      $CLICSHOPPING_PageManager->loadDefinitions('Sites/Shop/Contact/contact');

      $CLICSHOPPING_Breadcrumb->add($CLICSHOPPING_PageManager->getDef('navbar_title'), CLICSHOPPING::link(null, 'Info&Contact'));
    }
  }