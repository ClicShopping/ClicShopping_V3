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

  namespace ClicShopping\Apps\Configuration\TemplateEmail\Module\Hooks\ClicShoppingAdmin\Langues;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Configuration\TemplateEmail\TemplateEmail as TemplateEmail;

  class Insert implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;
    protected $insert_language_id;

    public function __construct()
    {
      if (!Registry::exists('TemplateEmail')) {
        Registry::set('TemplateEmail', new TemplateEmail());
      }

      $this->app = Registry::get('TemplateEmail');
      $this->insert_language_id = HTML::sanitize($_POST['insert_id']);
      $this->lang = Registry::get('Language');
    }

    private function insert()
    {
      if (isset($this->insert_language_id)) {
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
          $cols['language_id'] = $this->insert_language_id;

          unset($cols['orig_template_email_id']);

          $this->app->db->save('template_email_description', $cols);
        }
      }
    }

    public function execute()
    {
      if (isset($_GET['Insert'])) {
        $this->insert();
      }
    }
  }