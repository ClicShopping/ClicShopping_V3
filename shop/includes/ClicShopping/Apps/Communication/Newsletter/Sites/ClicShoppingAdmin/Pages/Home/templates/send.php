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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\ObjectInfo;

  use ClicShopping\Apps\Communication\Newsletter\Module\ClicShoppingAdmin\Newsletter\Newsletter as NewsletterModule;

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_Newsletter = Registry::get('Newsletter');
  $CLICSHOPPING_Hooks = Registry::get('Hooks');

  if (!isset($_GET['page']) || !is_numeric($_GET['page'])) {
    $_GET['page'] = 1;
  }

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  $nID = HTML::sanitize($_GET['nID']);

  $Qnewsletter = $CLICSHOPPING_Newsletter->db->get('newsletters', [
                                                            'title',
                                                            'content',
                                                            'module'
                                                           ], [
                                                              'newsletters_id' => (int)$nID
                                                            ]
                                          );

  $nInfo = new ObjectInfo($Qnewsletter->toArray());

  $module_name = $nInfo->module;
  $module = new NewsletterModule($nInfo->title, $nInfo->content);

 if ($module->show_choose_audience) {
    echo $module->choose_audience();
  } else {
    echo $module->confirm();
  }
?>
