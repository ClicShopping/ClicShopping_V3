<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Sites\Shop;

use ClicShopping\OM\Apps;
use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;
use ClicShopping\OM\HTTP;
use ClicShopping\OM\Registry;
use ClicShopping\Sites\Common\HTMLOverrideCommon;
use function constant;
use function defined;
use function is_array;
use function is_null;

class Template
{
  protected string $_template = 'template/';
  protected string $_directoryTemplate = 'template/';

  protected $_dynamicTemplate = SITE_THEMA;
  protected string $_directoryTemplateDefault = 'Default';

  protected string $_directoryIncludes = 'includes/';
  protected string $_directoryModules = 'modules/';
  protected string $_directoryTemplateSources = 'sources/';
  protected string $_directoryTemplateCss = 'css/';
  protected string $_directoryTemplateFiles = 'files/';
  protected string $_directoryTemplateLanguages = 'languages/';
  protected string $_directoryTemplateImages = 'images/';
  protected string $_directoryTemplateDownload = 'Download/';
  protected string $_directoryJavascript = 'javascript/';
  protected string $thema_directory;
  protected string $template_selected;

  protected string $_codeSail = 'Default/';
  protected string $_title;
  protected string $_description;
  protected string $_keywords;
  protected string $_newskeywords;

  protected array $_blocks = [];
  protected array $_content = [];
  protected $_grid_container_width = GRID_CONTAINER_WITH;
  protected $_grid_content_width = GRID_CONTENT_WITH;
  protected array $_data = [];

  protected $width;
  protected string $title;
  protected string $description;
  protected string $block;
  public string $group;
  protected string $name;
  public $_code;
  protected string $pages;
  protected string $setSiteThema;
  protected string $getPathDirectoryTemplateThema;

  public function __construct()
  {
    $this->_title = HTML::sanitize(STORE_NAME);
    $this->_description = '';
    $this->_keywords = '';
    $this->_newskeywords = '';
    $this->addBlock('<meta name="generator" content="ClicShopping" />', 'header_tags');
    $this->addBlock('<meta name="author" content="ClicShopping" />', 'header_tags');
  }

  public function setCodeSail($_codeSail): void
  {
    $this->_code = $_codeSail;
  }

  public function getCodeSail(): string
  {
    return $this->_codeSail;
  }

  public function getCode(): string
  {
    return $this->_template;
  }

  public function getPathRoot(): string
  {
    $path_root = CLICSHOPPING::getConfig('dir_root', 'Shop');

    return $path_root;
  }

  public function getTemplateSource(): string
  {
    return $this->getPathRoot() . $this->_directoryTemplateSources; //sources
  }

  public function getTemplateDirectory(): string
  {
    return $this->_directoryTemplateSources . $this->_directoryTemplate; //sources/template
  }

//sources/template/Default
  public function getDefaultTemplateDirectory(): string
  {
    return $this->_directoryTemplateSources . $this->_directoryTemplate . $this->_directoryTemplateDefault; // 'sources/template/Default
  }

//sources/template/templatename
  public function getDynamicTemplateDirectory(): string
  {
    return $this->_directoryTemplateSources . $this->_directoryTemplate . $this->_dynamicTemplate; // 'sources/template/SITE_THEMA
  }

// sources/images/
  public function getDirectoryTemplateImages(): string
  {
    return $this->_directoryTemplateSources . $this->_directoryTemplateImages; //sources/images/
  }

//******************************************
//           Boostrap
//******************************************

  public function setGridContainerWidth($width): void
  {
    $this->_grid_container_width = $width;
  }

  public function getGridContainerWidth()
  {
    return $this->_grid_container_width;
  }

  public function setGridContentWidth($width): void
  {
    $this->_grid_content_width = $width;
  }

  public function getGridContentWidth()
  {
    return $this->_grid_content_width;
  }

  public function getGridColumnWidth(): float|int
  {
    $width = ((12 - GRID_CONTENT_WITH) / 2);
    return $width;
  }

  /**
   * @param string|null $title
   */
  public function setTitle(?string $title): void
  {
    $this->_title = $title;
  }

  /**
   * @return string|null
   */
  public function getTitle(): ?string
  {
    return $this->_title;
  }

  /**
   * @param string|null $description
   */
  public function setDescription(?string $description): void
  {
    $this->_description = $description;
  }

  /**
   * @return string
   */
  public function getDescription(): ?string
  {
    return $this->_description;
  }

  /**
   * @param string|null $keywords
   */
  public function setKeywords(?string $keywords): void
  {
    $this->_keywords = $keywords;
  }

  /**
   * @return string|null
   */
  public function getKeywords(): ?string
  {
    return $this->_keywords;
  }

  public function setNewsKeywords(?string $Newskeywords)
  {
    $this->_newskeywords = $Newskeywords;
  }

  /**
   * @return string|null
   */
  public function getNewsKeywords(): ?string
  {
    return $this->_newskeywords;
  }

  /**
   * @param $block
   * @param $group
   */
  public function addBlock(string $block, string $group): void
  {
    if (defined('CONFIGURATION_TEMPLATE_MINIFY_HTML') && CONFIGURATION_TEMPLATE_MINIFY_HTML == 'true') {
      $block = HTMLOverrideCommon::getMinifyHtml($block);
    }

    $this->_blocks[$group][] = $block;
  }

  /**
   * @param string $group
   * @return bool
   */
  public function hasBlocks(string $group): bool
  {
    return (isset($this->_blocks[$group]) && !empty($this->_blocks[$group]));
  }

  /**
   * return all HeaderTags files in apps Hooks
   */
  public function getAppsHeaderTags(): void
  {
    if (defined('MODULE_HEADER_TAGS_INSTALLED') && !is_null(MODULE_HEADER_TAGS_INSTALLED)) {
      $header_tags_array = explode(';', MODULE_HEADER_TAGS_INSTALLED);

      foreach ($header_tags_array as $header) {
        if (str_contains($header, '\\')) {
          $class = Apps::getModuleClass($header, 'HeaderTags');
          $ad = new $class();

          if ($ad->isEnabled()) {
            echo $ad->getOutput();
          }
        }
      }
    }
  }

  /**
   * @param $group
   * @return string
   */
  public function getBlocks($group): string
  {
    if ($this->hasBlocks($group)) {
      return "\n" . '<!-- block ' . $group . ' -->' . "\n" . implode("\n", $this->_blocks[$group]) . "\n" . '<!-- end block ' . $group . ' -->' . "\n";
    } else {
      return '';
    }
  }

  /*
   * getfile inside a directory
   * @param : $file name of the file
   * @param : $template : template directory
   * /www/
   */
  public function getFile(string $file, string $template = null): string
  {
    if (!isset($template)) {
      $template = $this->getCode();
    }

    return CLICSHOPPING::BASE_DIR . 'Sites/' . CLICSHOPPING::getSite() . '/Templates/' . $template . DIRECTORY_SEPARATOR . $file;
  }

  /*
   * getPublicFile relative path
   * @param : $file name of the file
   * @param : $template : template directory
   * /www/
   */
  public function getPublicFile(string $file, string $template = null): string
  {
    if (!isset($template)) {
      $template = $this->getCode();
    }

    return CLICSHOPPING::linkPublic('Templates/' . $template . DIRECTORY_SEPARATOR . $file);
  }

  /**
   * scan directory to create a dropdown
   * @return string
   */

  public function getDropDownSelectedTemplateByCustomer(): string
  {
    $template_directory = CLICSHOPPING::getConfig('dir_root') . $this->_directoryTemplateSources . $this->_directoryTemplate;
    $weeds = ['.', '..', '_notes', 'index.php', 'ExNewTemplate', '.htaccess', 'README'];
    $directories = array_diff(scandir($template_directory), $weeds);
    $filename_array = [];

    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 5) . 'GMT');

    $filename_array[] = [
      'id' => 0,
      'text' => '-- Select --'
    ];

    foreach ($directories as $template) {
      if (is_dir($template_directory)) {
        $filename_array[] = [
          'id' => $template,
          'text' => $template
        ];
      }
    }

    clearstatcache();

    return HTML::selectMenu('TemplateCustomerSelected', $filename_array, null, 'onchange="this.form.submit();"');
  }

  /**
   * Select a default template
   * @param string|null $thema_directory
   * @return string
   */
  public function setSiteThema(?string $thema_directory = null): string
  {
    if (is_null($thema_directory)) {
      $thema_directory = $this->_directoryTemplateSources . $this->_directoryTemplate . $this->_dynamicTemplate; //sources/template/SITE_THEMA
    } else {
      if (!empty($this->getPathTemplateDemo())) {
        $thema_directory = $this->getPathTemplateDemo();
      }
    }

    return $thema_directory;
  }

  /**
   * Select the language directory
   * @param string
   * DIR_WS_LANGUAGES - sources/languages
   */

  public function getSiteTemplateLanguageDirectory(): string
  {
    return $this->_directoryTemplateSources . $this->_directoryTemplateLanguages; // sources/languages
  }

  /**
   * Select the language directory
   * @param string
   * DIR_WS_MODULES - includes/modules
   *
   */
  public function getModuleDirectory(): string
  {
    return $this->_directoryIncludes . $this->_directoryModules; // includes/modules
  }

  /**
   * get path download
   * @param string|null $directory
   * @return string
   */
  public function getPathDownloadShopDirectory(?string $directory = null): string
  {
    if (!is_null($directory)) {
      $path_shop_public_download_directory = $this->getTemplateSource() . $this->_directoryTemplateDownload . $directory . '/';
    } else {
      $path_shop_public_download_directory = $this->getTemplateSource() . $this->_directoryTemplateDownload . 'public/';
    }

    return $path_shop_public_download_directory;
  }

  /**
   * Select the default template module directtory
   * @param string
   * DIR_WS_TEMPLATE . SITE_THEMA . DIR_WS_TEMPLATE_MODULES
   *
   */

  public function getSiteTemplateModuleDirectory(): string
  {
    return $this->_directoryTemplateSources . $this->_directoryTemplate . $this->_dynamicTemplate . $this->_directoryModules; // sources/template/SITE_THEMA/modules
  }

  /**
   * Select the default template and verify if it exist
   *
   * @return string
   */

  public function getPathDirectoryTemplateThema(): string
  {
    if (is_file($this->getPathRoot() . $this->setSiteThema() . DIRECTORY_SEPARATOR . $this->_directoryTemplateFiles . 'index.php')) {
      $thema = $this->setSiteThema() . '/';
    } elseif (is_file($this->getPathRoot() . $this->getDefaultTemplateDirectory() . DIRECTORY_SEPARATOR . $this->_directoryTemplateFiles . 'index.php')) {
      $thema = $this->getDefaultTemplateDirectory() . '/';
    } else {
      HTTP::redirect(CLICSHOPPING::getConfig('http_server', 'Shop') . CLICSHOPPING::getConfig('http_path', 'Shop') . 'error_documents/error_template.php');
      clearstatcache();
    }

    if (!empty($this->getPathTemplateDemo())) {
      $thema = $this->getPathTemplateDemo();
    }

    return $thema;
  }

  /**
   * Demo approach
   * @return string
   */
  public function getPathTemplateDemo()
  {
    $thema = '';

    if (defined('MODULE_HEADER_SELECT_TEMPLATE_STATUS')) {
      if (MODULE_HEADER_SELECT_TEMPLATE_STATUS == 'True') {
        if (isset($_POST['TemplateCustomerSelected'])) {
          if ($_SESSION['TemplateCustomerSelected'] != $_POST['TemplateCustomerSelected']) {
            $_SESSION['TemplateCustomerSelected'] = HTML::sanitize($_POST['TemplateCustomerSelected']);
            $thema = $this->_directoryTemplateSources . $this->_directoryTemplate . $_SESSION['TemplateCustomerSelected'] . '/';
          } else {
            unset($_SESSION['TemplateCustomerSelected']);
            $thema = $this->_directoryTemplateSources . $this->_directoryTemplate . HTML::sanitize($_POST['TemplateCustomerSelected']) . '/';
          }
        }
      } else {
        if (isset($_SESSION['TemplateCustomerSelected'])) {
          unset($_SESSION['TemplateCustomerSelected']);
        }
      }
    }

    return $thema;
  }

  /**
   * Compare a string with an array
   * @param array $needles
   * @param string $haystack
   * @return bool true / false
   *
   */
  public static function match(array $needles, string $haystack): bool
  {
    foreach ($needles as $needle) {
      if (!empty($needle) && str_contains($haystack, $needle)) {
        return true;
      }
    }
    return false;
  }

  /**
   * Scan all the directories inside the default template
   * @param string $source_folder
   * @return array
   */

  public function getReadModulesDefaultDirectories(string $source_folder = 'modules_'): array
  {
    $dir = $this->_directoryTemplateSources . $this->_template . $this->_codeSail . $this->_directoryModules;

    $exclude = [];

    $module_directories = array_diff(glob($dir . $source_folder . '*', GLOB_ONLYDIR), $exclude);

    $result = [];

    foreach ($module_directories as $value) {
      $result[] = str_replace($dir, '', $value);
    }

    return $result;
  }

  /**
   * return void
   */

  public function buildBlocks(): void
  {
    $CLICSHOPPING_Customer = Registry::get('Customer');
    $CLICSHOPPING_Language = Registry::get('Language');
    $CLICSHOPPING_Category = Registry::get('Category');

    if (defined('TEMPLATE_BLOCK_GROUPS') && !is_null(TEMPLATE_BLOCK_GROUPS)) {
      $tbgroups_array = explode(';', TEMPLATE_BLOCK_GROUPS);

      foreach ($tbgroups_array as $group) {
        $module_key = 'MODULE_' . mb_strtoupper($group) . '_INSTALLED';

        if (defined($module_key) && !is_null(constant($module_key))) {
          $modules_array = explode(';', constant($module_key));

          foreach ($modules_array as $module) {
// bug : create <br /> at the first line on html content code. Don't find solution to resolve that. come from $module
// Could a problem for example on the xml files but pass with google sitemap analyse but not all
            $class = basename($module, '.php');
// module language
            if (!class_exists($class)) {
              if (CLICSHOPPING::getSite('ClicShoppingAdmin')) {
                $CLICSHOPPING_Language->loadDefinitions('modules/' . $group . DIRECTORY_SEPARATOR . pathinfo($module, PATHINFO_FILENAME));
              } else {
                $CLICSHOPPING_Language->loadDefinitions('sources/template/Default/modules/' . $group . DIRECTORY_SEPARATOR . pathinfo($module, PATHINFO_FILENAME));
              }
//mode privee ou ouvert - affichage des boxes gauche ou droite
              if (MODE_VENTE_PRIVEE == 'true' && $CLICSHOPPING_Customer->isLoggedOn()) {
                $modules_boxes = 'modules_boxes';
              } elseif (MODE_VENTE_PRIVEE == 'true' && !$CLICSHOPPING_Customer->isLoggedOn) {
                $modules_boxes = '';
              } else {
                $modules_boxes = 'modules_boxes';
              }

              if ($group == $modules_boxes) {
//check the module exist inside the template or take default template
                if (is_file($this->getPathDirectoryTemplateThema() . $this->_directoryModules . $group . DIRECTORY_SEPARATOR . $class . '.php')) {
                  include($this->getPathDirectoryTemplateThema() . $this->_directoryModules . $group . DIRECTORY_SEPARATOR . $class . '.php');
                } elseif (is_file($this->getDefaultTemplateDirectory() . DIRECTORY_SEPARATOR . $this->_directoryModules . $group . DIRECTORY_SEPARATOR . $class . '.php')) {
                  include($this->getDefaultTemplateDirectory() . DIRECTORY_SEPARATOR . $this->_directoryModules . $group . DIRECTORY_SEPARATOR . $class . '.php');
                } else {
                  if (is_file($this->_directoryIncludes . $this->_directoryModules . $group . DIRECTORY_SEPARATOR . $class . '.php')) {
                    include($this->_directoryIncludes . $this->_directoryModules . $group . DIRECTORY_SEPARATOR . $class . '.php');
                  }
                }
              } else {
//default module
                if (is_file($this->_directoryIncludes . $this->_directoryModules . $group . DIRECTORY_SEPARATOR . $class . '.php')) {
                  include($this->_directoryIncludes . $this->_directoryModules . $group . DIRECTORY_SEPARATOR . $class . '.php');
                }
              }

// exclude $modules_boxe and search if the modules exist
              if (is_numeric(array_search($group, $this->getReadModulesDefaultDirectories())) && $group != $modules_boxes) {
                $result = array_search($group, $this->getReadModulesDefaultDirectories());

                if (!is_null($result)) {
                  if (is_file($this->getPathDirectoryTemplateThema() . $this->_directoryModules . $group . DIRECTORY_SEPARATOR . $class . '.php')) {
                    include($this->getPathDirectoryTemplateThema() . $this->_directoryModules . $group . DIRECTORY_SEPARATOR . $class . '.php');
                  } elseif (is_file($this->getDefaultTemplateDirectory() . DIRECTORY_SEPARATOR . $this->_directoryModules . $group . DIRECTORY_SEPARATOR . $class . '.php')) {
                    include($this->getDefaultTemplateDirectory() . DIRECTORY_SEPARATOR . $this->_directoryModules . $group . DIRECTORY_SEPARATOR . $class . '.php');
                  } else {
                    if (is_file($this->_directoryIncludes . $this->_directoryModules . $group . DIRECTORY_SEPARATOR . $class . '.php')) {
                      include($this->_directoryIncludes . $this->_directoryModules . $group . DIRECTORY_SEPARATOR . $class . '.php');
                    }
                  }
                } else {
                  if ($group != $modules_boxes) {
                    if (is_file($this->_directoryIncludes . $this->_directoryModules . $group . DIRECTORY_SEPARATOR . $class . '.php')) {
                      include($this->_directoryIncludes . $this->_directoryModules . $group . DIRECTORY_SEPARATOR . $class . '.php');
                    }
                  }
                }
              }
            }

            if (class_exists($class)) {
              $mb = new $class();

// Dynamic boxe
              if (!isset($mb->pages) && ($mb->isEnabled())) {
                $this->pages = 'all';
                $mb->execute();
              } else {

// hide or display the box left / right
                if (!empty($mb->pages)) {
                  $page = explode(';', $mb->pages);
                }

                if (($mb->isEnabled() && $mb->pages == 'all')) {
                  $mb->execute();
                } else {
                  if ($mb->isEnabled() && isset($mb->pages)) {

                    $string = $this->getUrlWithoutSEFU();
// categories
// identify a categorie like index page
                    if ($CLICSHOPPING_Category->getPath()) {
                      $string = CLICSHOPPING::getConfig('bootstrap_file') . '?Categories';
                    }

//index page
// Boxe does'nt work when the page is refreshed with a query_string
                    if (empty($string)) {
                      if (CLICSHOPPING::getBaseNameIndex()) {
                        $string = CLICSHOPPING::getConfig('bootstrap_file');
                      }
                    }

                    if ($this->match($page, $string) === true) {
                      $mb->execute();
                    } else {
                      $mb->isEnabled();
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
  }

  /**
   * Allow to display or remove information on the catalog
   * @param string $string
   * @return mixed
   */
  public function getUrlWithoutSEFU(string $string = '/'): string
  {
    if (empty($_SERVER['QUERY_STRING'])) {
      $url = $_SERVER['REQUEST_URI'];
// If SEO is activated
      if (SEARCH_ENGINE_FRIENDLY_URLS == 'true' || SEARCH_ENGINE_FRIENDLY_URLS_PRO == 'true') {
        $substring = '/';

        $index = strpos($url, $substring);

        if ($index !== false) {
          $replace = substr($url, $index + strlen($substring));
          $search = $replace;
//language
          if (str_contains($search, 'language')) {
            $replace = substr($replace, 0, strpos($replace, 'language'));
          }
//currency
          if (str_contains($search, 'currency')) {
            $replace = substr($replace, 0, strpos($replace, 'currency'));
          }
//categories
          if (str_contains($search, 'cPath')) {
            $replace = substr($replace, 0, strpos($replace, 'cPath'));
          }

          $url_string = $replace;
        } else {
          $url_string = $url;
        }
      } else {
        $replace = str_replace(CLICSHOPPING::getConfig('bootstrap_file'), '', $url);
        $replace = str_replace(CLICSHOPPING::getConfig('http_path'), '', $replace);
        $replace = substr($replace, 1);
        $replace = str_replace($string, '&', $replace);

        $search = $replace;
//language
        if (str_contains($search, 'language')) {
          $replace = substr($replace, 0, strpos($replace, 'language'));
        }
//currency
        if (str_contains($search, 'currency')) {
          $replace = substr($replace, 0, strpos($replace, 'currency'));
        }
//categories
        if (str_contains($search, 'cPath')) {
          $replace = substr($replace, 0, strpos($replace, 'cPath'));
        }

        $url_string = $replace;
      }
    } else {
      $url_string = $_SERVER['QUERY_STRING'];
    }

    return $url_string;
  }

  /**
   * Select the header or footer of the template
   *
   * @param string $name , header or footer of the template
   * sources/template/Default/header.php
   *
   */
  public function getTemplateHeaderFooter(string $name): string
  {

    if (file_exists($this->getPathDirectoryTemplateThema() . $name . '.php')) {
      $themaFiles = $this->getPathDirectoryTemplateThema() . $name . '.php';
    } else {
      $themaFiles = $this->getDefaultTemplateDirectory() . DIRECTORY_SEPARATOR . $name . '.php';
    }

    return $themaFiles;
  }

  /**
   * Select the css in directory of the template by language
   *
   * @param string $themaGraphism , css directory in the template
   *
   */
  public function getTemplateCSS(): string
  {
    $CLICSHOPPING_Language = Registry::get('Language');
    if (is_file($this->getPathRoot() . DIRECTORY_SEPARATOR . $this->getPathDirectoryTemplateThema() . $this->_directoryTemplateCss . $CLICSHOPPING_Language->get('directory') . DIRECTORY_SEPARATOR . 'compressed_css.php')) {
      $themaCSS = CLICSHOPPING::link($this->getPathDirectoryTemplateThema() . $this->_directoryTemplateCss . $CLICSHOPPING_Language->get('directory') . DIRECTORY_SEPARATOR . 'compressed_css.php');
    } else {
      $themaCSS = CLICSHOPPING::link($this->getDefaultTemplateDirectory() . DIRECTORY_SEPARATOR . $this->_directoryTemplateCss . $CLICSHOPPING_Language->get('directory') . DIRECTORY_SEPARATOR . 'compressed_css.php');
    }

// if current does'nt exist take default
    if (!is_file($this->getPathRoot() . DIRECTORY_SEPARATOR . $this->getPathDirectoryTemplateThema() . $this->_directoryTemplateCss . $CLICSHOPPING_Language->get('directory') . DIRECTORY_SEPARATOR . 'compressed_css.php')) {
      $themaCSS = CLICSHOPPING::link($this->getPathDirectoryTemplateThema() . $this->_directoryTemplateCss . 'english/' . 'compressed_css.php');
    }

    return $themaCSS;
  }


  /**
   * Select the the file in this directory Files
   *
   * @param string $name
   * @return string
   */
  public function getTemplateFiles(string $name): string
  {
    if (is_file($this->getPathDirectoryTemplateThema() . $this->_directoryTemplateFiles . $name . '.php')) {
      $themaFiles = $this->getPathDirectoryTemplateThema() . $this->_directoryTemplateFiles . $name . '.php';
    } else {
      $themaFiles = $this->getDefaultTemplateDirectory() . DIRECTORY_SEPARATOR . $this->_directoryTemplateFiles . $name . '.php';
    }

    return $themaFiles;
  }

  /**
   * Select the the file in this directory module
   *
   * @param string $name
   * @return string
   */
  public function getTemplateModules(string $name): string
  {

    if (is_file($this->getPathDirectoryTemplateThema() . $this->_directoryModules . $name . '.php')) {
      $themaFiles = $this->getPathDirectoryTemplateThema() . $this->_directoryModules . $name . '.php';
    } else {
      $themaFiles = $this->getDefaultTemplateDirectory() . DIRECTORY_SEPARATOR . $this->_directoryModules . $name . '.php';
    }

    return $themaFiles;
  }

  /**
   * Select the the filename in this directory Modules
   *
   * @param string $themaFilename , filename in this module
   * ex: sources/template/Default/modules/modules_header/template_html/multi_template_test.php
   *
   */
  public function getTemplateModulesFilename(string $name): string
  {

    if (is_file($this->getPathDirectoryTemplateThema() . $this->_directoryModules . $name)) {
      $themaFilename = $this->getPathDirectoryTemplateThema() . $this->_directoryModules . $name;
    } else {
      $themaFilename = $this->getDefaultTemplateDirectory() . DIRECTORY_SEPARATOR . $this->_directoryModules . $name;
    }

    return $themaFilename;
  }

  /**
   * Select the file language in function the the file for the template
   *
   * @param string $name
   * @return string
   */

  public function getPathDirectoryTemplatetLanguageFiles(string $name): string
  {
    $CLICSHOPPING_Language = Registry::get('Language');

    if (is_file($this->getPathRoot() . $this->getPathDirectoryTemplateThema() . $this->_directoryTemplateLanguages . $CLICSHOPPING_Language->get('directory') . DIRECTORY_SEPARATOR . $name . '.php')) {
      $languagefiles = $this->getPathDirectoryTemplateThema() . $this->_directoryTemplateLanguages . $CLICSHOPPING_Language->get('directory') . DIRECTORY_SEPARATOR . $name . '.php';
      if (is_file($this->getPathRoot() . $this->getSiteTemplateLanguageDirectory() . $CLICSHOPPING_Language->get('directory') . DIRECTORY_SEPARATOR . $name . '.php')) {
        $languagefiles = $this->getSiteTemplateLanguageDirectory() . $CLICSHOPPING_Language->get('directory') . DIRECTORY_SEPARATOR . $name . '.php';
      }
    } else {
      $languagefiles = $this->getSiteTemplateLanguageDirectory() . $CLICSHOPPING_Language->get('directory') . DIRECTORY_SEPARATOR . $name . '.php';
    }

    return $languagefiles;
  }

  /**
   * public
   * Select the javascript for all template
   * @param $name $name of the js
   * @return string $javascript, directory of javascript in the template directory
   */
  public function getTemplateDefaultJavaScript(string $name): string
  {
    $javascript = CLICSHOPPING::getSite('Shop') . DIRECTORY_SEPARATOR . $this->_directoryTemplateSources . $this->_directoryJavascript . $name;

    return $javascript;
  }

  /**
   * public
   * Select the javascript inside a specific theme directory
   * @param $name $name of the js string
   * @return string $javascript, directory of javascript in the template directory
   */
  public function getTemplateThemaJavaScript(string $name): string
  {
    if (is_file($this->getPathDirectoryTemplateThema() . $this->_directoryJavascript . $name)) {
      $javascript = $this->getPathDirectoryTemplateThema() . $this->_directoryJavascript . $name;
    } else {
      $javascript = $this->getDefaultTemplateDirectory() . DIRECTORY_SEPARATOR . $this->_directoryJavascript . $name;
    }

    return $javascript;
  }

  /**
   * Select all the files inside directory
   * @param string $source_folder , directory
   * @param string $filename , name of the file
   * @param string $ext , file extension
   * @return
   *
   */
  public function getSpecificFiles(string $source_folder, string $filename, string $ext = 'php')
  {
    if (is_dir($source_folder)) {
      $FILES = glob($source_folder . $filename . '.' . $ext);
      $FILE_LIST[] = [];

      if (is_array($FILES)) {
        foreach ($FILES as $key => $file) {
          $result = str_replace($source_folder, '', $file);
          $name = str_replace('.' . $ext, '', $result);

          if (!empty($name)) {
            $FILE_LIST[$key] = [];
            $FILE_LIST[$key]['name'] = $name;
          }
        }
      }

      if (is_array($FILE_LIST)) {
        return $FILE_LIST;
      }
    }
  }

  /**
   * Allow display or call Module Hooks for template
   * @param string $source_folder
   * @param string $file_get_output
   * @param string $files_get_call
   * @param string $hook_call
   */
  public function useRecursiveModulesHooksForTemplate(string $source_folder, string $file_get_output, string $files_get_call, string $hook_call)
  {
    $CLICSHOPPING_Hooks = Registry::get('Hooks');

    if (is_dir($source_folder)) {
      $files_get_output = $this->getSpecificFiles($source_folder, $file_get_output);
      $files_get_call = $this->getSpecificFiles($source_folder, $files_get_call);

      foreach ($files_get_output as $value) {
        if (!empty($value['name'])) {
          echo $CLICSHOPPING_Hooks->output($hook_call, $value['name'], null, 'display');
        }
      }

      foreach ($files_get_call as $value) {
        if (!empty($value['name'])) {
          $CLICSHOPPING_Hooks->call($hook_call, $value['name']);
        }
      }
    }
  }
}