<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\ObjectInfo;

  use ClicShopping\Apps\Communication\Newsletter\Module\ClicShoppingAdmin\Newsletter\Newsletter as NewsletterModule;

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_Newsletter = Registry::get('Newsletter');

  $action = $_GET['action'] ?? '';

  if (isset($_GET['nID'])) {
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

    if ($module->show_chooseAudience) {
      echo $module->chooseAudience();
    } else {
      echo $module->confirm();
    }
  }