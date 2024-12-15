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

/**
 * Class Template manages the different components and settings related to website templates.
 */
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

  /**
   * Constructor method that initializes the title, description, keywords, and newskeywords properties,
   * and adds predefined meta tag blocks to the header tags.
   *
   * @return void
   */
  public function __construct()
  {
    $this->_title = HTML::sanitize(STORE_NAME);
    $this->_description = '';
    $this->_keywords = '';
    $this->_newskeywords = '';
    $this->addBlock('<meta name="generator" content="ClicShopping" />', 'header_tags');
    $this->addBlock('<meta name="author" content="ClicShopping" />', 'header_tags');
  }

  /**
   * Sets the value of the code sail.
   *
   * @param mixed $_codeSail The value to set for the code sail.
   * @return void
   */
  public function setCodeSail($_codeSail): void
  {
    $this->_code = $_codeSail;
  }

  /**
   * Retrieves the value of the code sail.
   *
   * @return string The current value of the code sail.
   */
  public function getCodeSail(): string
  {
    return $this->_codeSail;
  }

  /**
   * Retrieves the code associated with the object.
   *
   * @return string The code value of the object.
   */
  public function getCode(): string
  {
    return $this->_template;
  }

  /**
   * Retrieves the root directory path of the shop.
   *
   * @return string The root directory path.
   */
  public function getPathRoot(): string
  {
    $path_root = CLICSHOPPING::getConfig('dir_root', 'Shop');

    return $path_root;
  }

  /**
   * Retrieves the full path to the template source directory.
   *
   * @return string The full path of the template source directory.
   */
  public function getTemplateSource(): string
  {
    return $this->getPathRoot() . $this->_directoryTemplateSources; //sources
  }

  /**
   * Retrieves the template directory path.
   *
   * @return string The combined path of the template sources directory and the template directory.
   */
  public function getTemplateDirectory(): string
  {
    return $this->_directoryTemplateSources . $this->_directoryTemplate; //sources/template
  }

//sources/template/Default

  /**
   * Retrieves the default template directory path.
   *
   * @return string The concatenated path to the default template directory.
   */
  public function getDefaultTemplateDirectory(): string
  {
    return $this->_directoryTemplateSources . $this->_directoryTemplate . $this->_directoryTemplateDefault; // 'sources/template/Default
  }

//sources/template/templatename

  /**
   * Retrieves the directory path for the dynamic template.
   *
   * @return string The constructed path for the dynamic template directory.
   */
  public function getDynamicTemplateDirectory(): string
  {
    return $this->_directoryTemplateSources . $this->_directoryTemplate . $this->_dynamicTemplate; // 'sources/template/SITE_THEMA
  }

// sources/images/

  /**
   * Retrieves the concatenated path to the directory containing template images.
   *
   * @return string The complete path to the template images directory.
   */
  public function getDirectoryTemplateImages(): string
  {
    return $this->_directoryTemplateSources . $this->_directoryTemplateImages; //sources/images/
  }

//******************************************
//           Boostrap
//******************************************

  /**
   * Sets the width of the grid container.
   *
   * @param mixed $width The desired width of the grid container.
   * @return void
   */
  public function setGridContainerWidth($width): void
  {
    $this->_grid_container_width = $width;
  }

  /**
   * Retrieves the width of the grid container.
   *
   * @return int|null The width of the grid container, or null if not set.
   */
  public function getGridContainerWidth()
  {
    return $this->_grid_container_width;
  }

  /**
   * Sets the content width of the grid.
   *
   * @param mixed $width The width to set for the grid content.
   * @return void
   */
  public function setGridContentWidth($width): void
  {
    $this->_grid_content_width = $width;
  }

  /**
   * Retrieves the width of the grid content.
   *
   * @return mixed The width of the grid content.
   */
  public function getGridContentWidth()
  {
    return $this->_grid_content_width;
  }

  /**
   * Calculates and retrieves the width of a grid column.
   *
   * @return float|int The calculated width of the grid column.
   */
  public function getGridColumnWidth(): float|int
  {
    $width = ((12 - GRID_CONTENT_WITH) / 2);
    return $width;
  }

  /**
   * Sets the title value.
   *
   * @param string|null $title The title to set, or null to unset the title.
   * @return void
   */
  public function setTitle(?string $title): void
  {
    $this->_title = $title;
  }

  /**
   * Retrieves the title.
   *
   * @return string|null The title, or null if not set.
   */
  public function getTitle(): ?string
  {
    return $this->_title;
  }

  /**
   * Sets the description for the object.
   *
   * @param string|null $description The description to set.
   * @return void
   */
  public function setDescription(?string $description): void
  {
    $this->_description = $description;
  }

  /**
   * Retrieves the description.
   *
   * @return string|null The description or null if not set.
   */
  public function getDescription(): ?string
  {
    return $this->_description;
  }

  /**
   * Sets the keywords property.
   *
   * @param string|null $keywords The keywords to be assigned, or null to unset it.
   * @return void
   */
  public function setKeywords(?string $keywords): void
  {
    $this->_keywords = $keywords;
  }

  /**
   * Retrieves the keywords.
   *
   * @return string|null The keywords or null if not set.
   */
  public function getKeywords(): ?string
  {
    return $this->_keywords;
  }

  /**
   *
   * @param string|null $Newskeywords The keywords related to the news item.
   * @return void
   */
  public function setNewsKeywords(?string $Newskeywords)
  {
    $this->_newskeywords = $Newskeywords;
  }

  /**
   * Retrieves the news keywords.
   *
   * @return string|null The news keywords if set, or null if not set.
   */
  public function getNewsKeywords(): ?string
  {
    return $this->_newskeywords;
  }

  /**
   * Adds a block to a specified group.
   *
   * @param string $block The block content to be added.
   * @param string $group The name of the group to which the block belongs.
   * @return void
   */
  public function addBlock(string $block, string $group): void
  {
    if (defined('CONFIGURATION_TEMPLATE_MINIFY_HTML') && CONFIGURATION_TEMPLATE_MINIFY_HTML == 'true') {
      $block = HTMLOverrideCommon::getMinifyHtml($block);
    }

    $this->_blocks[$group][] = $block;
  }

  /**
   * Checks if the specified group has associated blocks.
   *
   * @param string $group The group name to check for blocks.
   * @return bool True if the group has blocks, false otherwise.
   */
  public function hasBlocks(string $group): bool
  {
    return (isset($this->_blocks[$group]) && !empty($this->_blocks[$group]));
  }

  /**
   * Processes and outputs the header tags for installed modules.
   *
   * @return
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
   * Retrieves the blocks associated with a specific group.
   *
   * @param string $group The name of the group for which to retrieve the blocks.
   * @return string The concatenated string of blocks for the specified group. Returns
   */
  public function getBlocks($group): string
  {
    if ($this->hasBlocks($group)) {
      return "\n" . '<!-- block ' . $group . ' -->' . "\n" . implode("\n", $this->_blocks[$group]) . "\n" . '<!-- end block ' . $group . ' -->' . "\n";
    } else {
      return '';
    }
  }

  /**
   * Retrieves the full path to a specified file within a template directory.
   *
   * @param string $file The name of the file to retrieve the path for.
   * @param string|null $template The
   */
  public function getFile(string $file, string $template = null): string
  {
    if (!isset($template)) {
      $template = $this->getCode();
    }

    return CLICSHOPPING::BASE_DIR . 'Sites/' . CLICSHOPPING::getSite() . '/Templates/' . $template . DIRECTORY_SEPARATOR . $file;
  }

  /**
   * Retrieves the public file path based on the provided file name and template.
   *
   * @param string $file The name of the file to retrieve.
   * @param string|null $template The template to use for the
   */
  public function getPublicFile(string $file, string $template = null): string
  {
    if (!isset($template)) {
      $template = $this->getCode();
    }

    return CLICSHOPPING::linkPublic('Templates/' . $template . DIRECTORY_SEPARATOR . $file);
  }

  /**
   * Retrieves the selected template dropdown menu for a customer.
   *
   * This method scans the template directory, filters out unwanted files and directories,
   * and constructs an HTML dropdown menu for selecting templates. The dropdown includes
   * a default option and dynamically adds available template directories.
   *
   * @return string The HTML string for the dropdown menu of selected templates.
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
   * Sets the site theme directory.
   *
   * @param string|null $thema_directory The directory path for the site theme, or null to use the default setup.
   * @return string The resolved theme directory path.
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
   * Retrieves the directory path for site template languages.
   *
   * @return string The concatenated path of template source and language directories.
   */

  public function getSiteTemplateLanguageDirectory(): string
  {
    return $this->_directoryTemplateSources . $this->_directoryTemplateLanguages; // sources/languages
  }

  /**
   * Retrieves the module directory path.
   *
   * @return string The path to the module directory.
   */
  public function getModuleDirectory(): string
  {
    return $this->_directoryIncludes . $this->_directoryModules; // includes/modules
  }

  /**
   * Retrieves the path to the shop's download directory.
   *
   * @param string|null $directory The specific directory to append to the base path. If null, the default 'public' directory is used.
   * @return string The full path to the specified or default download directory.
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
   * Retrieves the directory path for the site template module.
   *
   * @return string The concatenated path to the site template module directory.
   */

  public function getSiteTemplateModuleDirectory(): string
  {
    return $this->_directoryTemplateSources . $this->_directoryTemplate . $this->_dynamicTemplate . $this->_directoryModules; // sources/template/SITE_THEMA/modules
  }

  /**
   * Retrieves the path to the directory of the theme template.
   *
   * Determines the theme directory path based on the existence of specific files
   * within the defined paths. If none are found, it redirects to an error page.
   * Will also use the demo template path if it is defined.
   *
   * @return string The path to the theme template directory.
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
   * Retrieves the path template for the selected theme or default theme based on the module configuration
   * and user selection.
   *
   * @return string The path to the selected theme template, or an empty string if no theme is selected or configured.
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
   * Checks if any of the specified needles are found within the haystack string.
   *
   * @param array $needles The array of strings to search for in the haystack.
   * @param string $haystack The string in which to search for the needles.
   * @return bool Returns true if at least one needle is found in the haystack; otherwise, returns false.
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
   * Retrieves the default directories for read modules based on the specified source folder.
   *
   * @param string $source_folder The prefix of the source folder to read. Defaults to 'modules_'.
   * @return array An array of module directory names relative to the base directory.
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
   * Builds and initializes the blocks for specific module groups.
   * This method dynamically includes and executes modules based on the configuration
   * and group types defined within the template block groups.
   *
   * @return void
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
   * Retrieves a URL without the Search Engine Friendly URL (SEFU) parameters.
   *
   * This method processes the current URL to generate a simplified version by removing
   * language, currency, or category query string parameters if present and adapting
   * the URL based on SEO configurations.
   *
   * @param string $string A delimiter character used for processing certain parts of the URL, defaulting to '/'.
   * @return string The modified URL string without SEFU parameters.
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
   * Retrieves the path of the template header or footer file based on the specified name.
   *
   * @param string $name The name of the template file to retrieve.
   * @return string The full path of the template file.
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
   * Retrieves the path to the template CSS file based on the current language and theme configuration.
   * If the specific CSS file is not found, it defaults to a predefined directory.
   *
   * @return string The URL to the template CSS file.
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
   * Retrieves the full path of a template file based on the given name.
   *
   * @param string $name The name of the template file to retrieve.
   * @return string The full path of the template file.
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
   * Retrieves the file path for a template module based on the provided module name.
   *
   * @param string $name The name of the template module to retrieve.
   * @return string The file path of
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
   * Retrieves the full path to the template module file based on the specified name.
   *
   * @param string $name The name of the template module file.
   * @return string The full path to the template
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
   * Gets the path to the directory of the template language files for a given file name.
   *
   * @param string $name The name of the language file to locate.
   * @return string The path to the template language file.
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
   * Retrieves the default JavaScript path for a given template name.
   *
   * @param string $name The name of the JavaScript file.
   * @return string The full path to the JavaScript file.
   */
  public function getTemplateDefaultJavaScript(string $name): string
  {
    $javascript = CLICSHOPPING::getSite('Shop') . DIRECTORY_SEPARATOR . $this->_directoryTemplateSources . $this->_directoryJavascript . $name;

    return $javascript;
  }

  /**
   * Retrieves the file path of the JavaScript file for the specified template theme.
   *
   * @param string $name The name of the JavaScript file to retrieve.
   * @return string The file path
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
   * Retrieves a list of specific files from a given folder, filtered by filename and extension.
   *
   * @param string $source_folder The folder path to search for files.
   * @param string $filename The name of the files to match.
   * @param string $ext The file extension to filter by. Defaults to 'php'.
   * @return array|null An array of specific files with their names or null if no valid files are found.
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
   * Executes recursive hooks for a given template by processing specific files
   * and invoking hook outputs and calls for each file.
   *
   * @param string $source_folder The directory to scan for specific files.
   * @param string $file_get_output The type of files to process for hook output.
   * @param string $files_get_call The type of files to process for hook calls.
   * @param string $hook_call The hook group or identifier to execute.
   * @return void
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