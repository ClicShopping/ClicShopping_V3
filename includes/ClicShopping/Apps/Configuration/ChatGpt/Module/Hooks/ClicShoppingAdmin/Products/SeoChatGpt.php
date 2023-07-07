<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT

   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\ChatGpt\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Configuration\ChatGpt\ChatGpt as ChatGptApp;

  use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\ChatGptAdmin;
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
      $CLICSHOPPING_ProductsAdmin = Registry::get('ProductsAdmin');

      if (ChatGptAdmin::checkGptStatus() === false) {
        return false;
      }

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Products/seo_chat_gpt');

      if (isset($_GET['pID'])) {
        $id = HTML::sanitize($_GET['pID']);

        $question = $this->app->getDef('text_seo_page_title_question');
        $question_tag = $this->app->getDef('text_seo_page_tag_question');
        $question_keywords = $this->app->getDef('text_seo_page_keywords_question');
        $question_summary_description = $this->app->getDef('text_seo_page_summary_description_question');
        $translate_language = $this->app->getDef('text_seo_page_translate_language');

        $product_name = $CLICSHOPPING_ProductsAdmin->getProductsName($id);

        $url = ChatGptAdmin::getAjaxUrl(false);
        $urlMultilanguage = ChatGptAdmin::getAjaxSeoMultilanguageUrl();

        $content = '<button type="button" class="btn btn-primary btn-sm submit-button" data-index="0">';
        $content .= '<i class="bi-chat-square-dots" title="' . $this->app->getDef('text_seo_action') . '"></i>';
        $content .= '</button>';

        $getProductsSeoTitle = ChatJsAdminSeo::getProductsSeoTitle($content, $urlMultilanguage, $translate_language, $question, $product_name, $url);
        $getProductsSeoSummaryDescription = ChatJsAdminSeo::getProductsSummaryDescription($content, $urlMultilanguage, $translate_language, $question_summary_description, $product_name, $url);
        $getProductsSeoDescription = ChatJsAdminSeo::getProductsSeoDescription($content, $urlMultilanguage, $translate_language, $question_summary_description, $product_name, $url);
        $getProductsSeoKeywords = ChatJsAdminSeo::getProductsSeoKeywords($content, $urlMultilanguage, $translate_language, $question_keywords, $product_name, $url);
        $getProductsSeoTags = ChatJsAdminSeo::getProductsSeoTags($content, $urlMultilanguage, $translate_language, $question_tag, $product_name, $url);

        $output = <<<EOD
<!------------------>
<!-- ChatGpt start tag-->
<!------------------>
<!-- products seo title meta tag -->
{$getProductsSeoTitle}
<!-- product seo meta summary descript

ion -->
{$getProductsSeoSummaryDescription}
<!-- product seo meta description -->
{$getProductsSeoDescription}
<!-- product seo  meta keyword -->
{$getProductsSeoKeywords}
<!-- product seo tag -->
{$getProductsSeoTags}

EOD;
    } else {
     $tab_title = $this->app->getDef('tab_gpt_options');
     $title = $this->app->getDef('text_gpt_options');

     $content = '
            <div class="separator"></div>     
            <div class="row" id="productOptionGptDescription">
              <div class="col-md-9">
                <div class="form-group row">
                  <label for="' . $this->app->getDef('text_options_gpt_description') . '"
                         class="col-7 col-form-label">' . $this->app->getDef('text_options_gpt_description') . '</label>
                  <div class="col-md-2">
                    <ul class="list-group-slider list-group-flush">
                      <li class="list-group-item-slider">
                        <label class="switch">
                          ' . HTML::checkboxField('option_gpt_description', '1', true, 'class="success"') . '
                          <span class="slider"></span>
                        </label>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            
           <div class="separator"></div>     
            <div class="row" id="productOptionGptSummary">
              <div class="col-md-9">
                <div class="form-group row">
                  <label for="' . $this->app->getDef('text_options_gpt_summary_description') . '"
                         class="col-7 col-form-label">' . $this->app->getDef('text_options_gpt_summary_description') . '</label>
                  <div class="col-md-2">
                    <ul class="list-group-slider list-group-flush">
                      <li class="list-group-item-slider">
                        <label class="switch">
                          ' . HTML::checkboxField('option_gpt_summary_description', '1', true, 'class="success"') . '
                          <span class="slider"></span>
                        </label>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="separator"></div>     
            <div class="row" id="productOptionGptSeoTitle">
              <div class="col-md-9">
                <div class="form-group row">
                  <label for="' . $this->app->getDef('text_options_gpt_seo_title') . '"
                         class="col-7 col-form-label">' . $this->app->getDef('text_options_gpt_seo_title') . '</label>
                  <div class="col-md-2">
                    <ul class="list-group-slider list-group-flush">
                      <li class="list-group-item-slider">
                        <label class="switch">
                          ' . HTML::checkboxField('option_gpt_seo_title', '1', true, 'class="success"') . '
                          <span class="slider"></span>
                        </label>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="separator"></div>     
            <div class="row" id="productOptionGptSeoDescription">
              <div class="col-md-9">
                <div class="form-group row">
                  <label for="' . $this->app->getDef('text_options_gpt_seo_description') . '"
                         class="col-7 col-form-label">' . $this->app->getDef('text_options_gpt_seo_description') . '</label>
                  <div class="col-md-2">
                    <ul class="list-group-slider list-group-flush">
                      <li class="list-group-item-slider">
                        <label class="switch">
                          ' . HTML::checkboxField('option_gpt_seo_description', '1', true, 'class="success"') . '
                          <span class="slider"></span>
                        </label>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="separator"></div>     
            <div class="row" id="productOptionGptSeokeywords">
              <div class="col-md-9">
                <div class="form-group row">
                  <label for="' . $this->app->getDef('text_options_gpt_seo_keywords') . '"
                         class="col-7 col-form-label">' . $this->app->getDef('text_options_gpt_seo_keywords') . '</label>
                  <div class="col-md-2">
                    <ul class="list-group-slider list-group-flush">
                      <li class="list-group-item-slider">
                        <label class="switch">
                          ' . HTML::checkboxField('option_gpt_seo_keywords', '1', true, 'class="success"') . '
                          <span class="slider"></span>
                        </label>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="separator"></div>     
            <div class="row" id="productOptionGptSeotags">
              <div class="col-md-9">
                <div class="form-group row">
                  <label for="' . $this->app->getDef('text_options_gpt_seo_tags') . '"
                         class="col-7 col-form-label">' . $this->app->getDef('text_options_gpt_seo_tags') . '</label>
                  <div class="col-md-2">
                    <ul class="list-group-slider list-group-flush">
                      <li class="list-group-item-slider">
                        <label class="switch">
                          ' . HTML::checkboxField('option_gpt_seo_tags', '1', true, 'class="success"') . '
                          <span class="slider"></span>
                        </label>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="separator"></div>
            <div class="row" id="productOptionGptCreateImage">
              <div class="col-md-9">
                <div class="form-group row">
                  <label for="' . $this->app->getDef('text_options_gpt_image') . '"
                         class="col-7 col-form-label">' . $this->app->getDef('text_options_gpt_image') . '</label>
                  <div class="col-md-2">
                    <ul class="list-group-slider list-group-flush">
                      <li class="list-group-item-slider">
                        <label class="switch">
                          ' . HTML::checkboxField('option_gpt_create_image', '1', false, 'class="success"') . '
                          <span class="slider"></span>
                        </label>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
           
            <div class="separator"></div>
            <div class="alert alert-info" role="alert">
              <div><h4><i class="bi bi-question-circle" title="' . $this->app->getDef('title_help_seo') . '"></i></h4> ' . $this->app->getDef('title_help_seo') .'</div>
              <div class="separator"></div>
              <div>' . $this->app->getDef('text_help_seo') .'</div>
            </div>
     ';

$output = <<<EOD
<!-- ######################## -->
<!--  Start OptionsGptApp  -->
<!-- ######################## -->
<div class="tab-pane" id="section_OptionsGptApp_content">
  <div class="mainTitle">
    <span class="col-md-2">{$title}</span>
  </div>
  {$content}
</div>
<script>
$('#section_OptionsGptApp_content').appendTo('#productsTabs .tab-content');
$('#productsTabs .nav-tabs').append('    <li class="nav-item"><a data-bs-target="#section_OptionsGptApp_content" role="tab" data-bs-toggle="tab" class="nav-link">{$tab_title}</a></li>');
</script>
<!-- ######################## -->
<!-- End OptionsGptApp  -->
<!-- ######################## -->
EOD;
      }

      return $output;
    }
  }
