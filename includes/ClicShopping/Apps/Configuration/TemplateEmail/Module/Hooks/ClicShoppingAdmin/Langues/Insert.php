<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\TemplateEmail\Module\Hooks\ClicShoppingAdmin\Langues;

use ClicShopping\Apps\Configuration\TemplateEmail\TemplateEmail;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\Langues\Classes\ClicShoppingAdmin\LanguageAdmin;

class Insert implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $app;
  protected mixed $lang;

  public function __construct()
  {
    if (!Registry::exists('TemplateEmail')) {
      Registry::set('TemplateEmail', new TemplateEmail());
    }

    $this->app = Registry::get('TemplateEmail');
    $this->lang = Registry::get('Language');
  }

  private function insert()
  {
    $insert_language_id = LanguageAdmin::getLatestLanguageID();
    $QtemplateEmailDescription = $this->app->db->prepare('select t.template_email_id as orig_template_email_id,
                                                                     te.*
                                                              from :table_template_email t left join :table_template_email_description te on t.template_email_id = te.template_email_id
                                                              where te.language_id = :language_id
                                                             ');

    $QtemplateEmailDescription->bindInt(':language_id', (int)$this->lang->getId());
    $QtemplateEmailDescription->execute();

    while ($QtemplateEmailDescription->fetch()) {
      $cols = $QtemplateEmailDescription->toArray();

      $cols['template_email_id'] = $cols['orig_template_email_id'];
      $cols['language_id'] = (int)$insert_language_id;

      unset($cols['orig_template_email_id']);

      $this->app->db->save('template_email_description', $cols);
    }
  }

  public function execute()
  {
    if (!\defined('CLICSHOPPING_APP_TEMPLATE_EMAIL_TE_STATUS') || CLICSHOPPING_APP_TEMPLATE_EMAIL_TE_STATUS == 'False') {
      return false;
    }

    if (isset($_GET['Langues'], $_GET['Insert'])) {
      $this->insert();
    }
  }
}