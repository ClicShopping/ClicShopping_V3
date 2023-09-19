<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ChatGpt\Module\Hooks\ClicShoppingAdmin\PageManager;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Communication\PageManager\Classes\ClicShoppingAdmin\PageManagerAdmin;
use ClicShopping\Apps\Configuration\ChatGpt\ChatGpt as ChatGptApp;
use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\ChatGptAdmin35;
use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\ChatJsAdminSeo;

class SeoChatGpt implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $app;

  public function __construct()
  {
    if (!Registry::exists('ChatGpt')) {
      Registry::set('ChatGpt', new ChatGptApp());
    }

    $this->app = Registry::get('ChatGpt');
  }

  public function display()
  {
    $CLICSHOPPING_Language = Registry::get('Language');

    if (ChatGptAdmin35::checkGptStatus() === false) {
      return false;
    }

    $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/PageManager/seo_chat_gpt');

    if (isset($_GET['bID'])) {
      $id = HTML::sanitize($_GET['bID']);
    } else {
      return false;
    }

    $question = $this->app->getDef('text_seo_page_title_question');
    $question_keywords = $this->app->getDef('text_seo_page_keywords_question');
    $question_summary_description = $this->app->getDef('text_seo_page_summary_description_question');
    $translate_language = $this->app->getDef('text_seo_page_translate_language');

    $page_manager_name = PageManagerAdmin::getPageManagerTitle($id, $CLICSHOPPING_Language->getId());

    $url = ChatGptAdmin35::getAjaxUrl(false);
    $urlMultilanguage = ChatGptAdmin35::getAjaxSeoMultilanguageUrl();

    $content = '<button type="button" class="btn btn-primary btn-sm submit-button" data-index="0">';
    $content .= '<i class="bi-chat-square-dots" title="' . $this->app->getDef('text_seo_page_title') . '"></i>';
    $content .= '</button>';

    $getPageManagerSeoTitle = ChatJsAdminSeo::getPageManagerSeoTitle($content, $urlMultilanguage, $translate_language, $question, $page_manager_name, $url);
    $getPageManagerSeoDescription = ChatJsAdminSeo::getPageManagerSeoDescription($content, $urlMultilanguage, $translate_language, $question_summary_description, $page_manager_name, $url);
    $getPageManagerSeoKeywords = ChatJsAdminSeo::getPageManagerSeoKeywords($content, $urlMultilanguage, $translate_language, $question_keywords, $page_manager_name, $url);

    $output = <<<EOD
<!------------------>
<!-- ChatGpt start tag-->
<!------------------>
<!-- page manager seo meta tilte  -->
  {$getPageManagerSeoTitle}
<!-- page manager seo meta description -->
  {$getPageManagerSeoDescription}
<!-- pqge manager seo  meta keyword -->
  {$getPageManagerSeoKeywords}
EOD;

    return $output;
  }
}
