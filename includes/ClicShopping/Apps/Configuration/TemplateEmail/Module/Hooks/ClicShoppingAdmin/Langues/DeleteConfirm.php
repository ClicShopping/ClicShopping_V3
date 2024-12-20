<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\TemplateEmail\Module\Hooks\ClicShoppingAdmin\Langues;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\TemplateEmail\TemplateEmail as TemplateEmail;

class DeleteConfirm implements \ClicShopping\OM\Modules\HooksInterface
{
  public mixed $app;

  public function __construct()
  {
    if (!Registry::exists('TemplateEmail')) {
      Registry::set('TemplateEmail', new TemplateEmail());
    }

    $this->app = Registry::get('TemplateEmail');
  }

  /**
   * Deletes a record from the 'template_email_description' table based on the provided language ID.
   *
   * @param int $id The ID of the language to be deleted.
   * @return void
   */
  private function delete(int $id)
  {
    if (!\is_null($id)) {
      $this->app->db->delete('template_email_description', ['language_id' => $id]);
    }
  }

  /**
   * Executes the process of checking for a delete confirmation and triggers deletion if confirmed.
   *
   * @return void
   */
  public function execute()
  {
    if (isset($_GET['DeleteConfirm'])) {
      $id = HTML::sanitize($_GET['lID']);
      $this->delete($id);
    }
  }
}