<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

  namespace ClicShopping\Sites\Shop;

  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Apps;

  class Template {

    protected $_template = 'template';
    protected $_dynamicTemplate = SITE_THEMA;

    protected $_directoryIncludes = 'includes';
    protected $_directoryModules = 'modules/';
    protected $_directoryTemplateSources = 'sources';
    protected $_directoryTemplate = 'template';
    protected $_directoryTemplateDefault = 'Default';
    protected $_directoryTemplateCss = 'css';
    protected $_directoryTemplateFiles = 'files';
    protected $_directoryTemplateLanguages = 'languages';
    protected $_directoryTemplateImages = 'images';
    protected $_directoryTemplateDownload = 'Download';

    protected $thema_directory;

    protected $_codeSail = 'Default';
    protected $_title;
    protected $_description;
    protected $_keywords;
    protected $_Newskeywords;

    protected $_blocks = [];
    protected $_content = [];
    protected $_grid_container_width = GRID_CONTAINER_WITH;
    protected $_grid_content_width = GRID_CONTENT_WITH;
    protected $_data = [];

    protected $width;
    protected $title;
    protected $description;
    protected $block;
    protected $group;
    protected $template_selected;
    protected $name;

    public function __construct() {
      $this->_title = STORE_NAME;
      $this->_description = '';
      $this->_keywords = '';
      $this->_Newskeywords = '';
      $this->addBlock('<meta name="generator" content="ClicShopping" />', 'header_tags');
      $this->addBlock('<meta name="author" content="Innov Concept" />', 'header_tags');
    }

    public function setCodeSail($_codeSail) {
      $this->_code = $_codeSail;
    }

    public function getCodeSail() {
     return $this->_codeSail;
   }

    public function getCode() {
      return $this->_template;
    }

    public function getTemplateSource() {
      return CLICSHOPPING::getConfig('dir_root', 'Shop') . $this->_directoryTemplateSources; //sources
    }

    public function getTemplateDirectory() {
      return $this->_directoryTemplateSources . '/' . $this->_directoryTemplate; //sources/template
    }

//sources/template/Default
    public function getDefaultTemplateDirectory() {
      return $this->_directoryTemplateSources . '/' . $this->_directoryTemplate . '/' . $this->_directoryTemplateDefault; // 'sources/template/Default
    }

//sources/template/templatename
    public function getDynamicTemplateDirectory() {
      return $this->_directoryTemplateSources . '/' . $this->_directoryTemplate . '/' . $this->_dynamicTemplate; // 'sources/template/SITE_THEMA
    }

// sources/images/
    public function getDirectoryTemplateImages() {
      return $this->_directoryTemplateSources . '/' .  $this->_directoryTemplateImages . '/'; //sources/images/
    }

//******************************************
//           Boostrap
//******************************************

    public function setGridContainerWidth($width) {
      $this->_grid_container_width = $width;
    }

    public function getGridContainerWidth() {
      return $this->_grid_container_width;
    }

    public function setGridContentWidth($width) {
      $this->_grid_content_width = $width;
    }

    public function getGridContentWidth() {
      return $this->_grid_content_width;
    }

    public function getGridColumnWidth() {
      $width = ((12 - GRID_CONTENT_WITH) / 2);
      return $width;
    }

    public function setTitle($title) {
      $this->_title = $title;
    }

    public function getTitle() {
      return $this->_title;
    }

    public function setDescription($description) {
      $this->_description = $description;
    }

    public function getDescription() {
      return $this->_description;
    }

    public function setKeywords($keywords) {
      $this->_keywords = $keywords;
    }

    public function getKeywords() {
      return $this->_keywords;
    }

    public function setNewsKeywords($Newskeywords) {
      $this->_Newskeywords = $Newskeywords;
    }

    public function getNewsKeywords() {
      return $this->_Newskeywords;
    }

    public function addBlock($block, $group) {
      $this->_blocks[$group][] = $block;
    }

    public function hasBlocks($group) {
      return (isset($this->_blocks[$group]) && !empty($this->_blocks[$group]));
    }


    public function getAppsHeaderTags() {
      if ( defined('MODULE_HEADER_TAGS_INSTALLED') && !is_null(MODULE_HEADER_TAGS_INSTALLED) ) {
        $header_tags_array = explode(';', MODULE_HEADER_TAGS_INSTALLED);

        foreach ($header_tags_array as $header) {
          if (strpos($header, '\\') !== false) {
            $class = Apps::getModuleClass($header, 'HeaderTags');
            $ad = new $class();

            if ( $ad->isEnabled() ) {
              echo $ad->getOutput();
            }
          }
        }
      }
    }

    public function getBlocks($group) {
      if ($this->hasBlocks($group)) {
        return implode("\n", $this->_blocks[$group]);
      }
    }

/*
 * getfile inside a directory
 * @param : $file name of the file
 * @param : $template : template directory
 * /www/
 */
    public function getFile($file, $template = null) {
      if (!isset($template)) {
        $template = $this->getCode();
      }

      return CLICSHOPPING::BASE_DIR . 'Sites/' . CLICSHOPPING::getSite() . '/Templates/' . $template . '/' . $file;
    }

/*
 * getPublicFile relative path
 * @param : $file name of the file
 * @param : $template : template directory
 * /www/
 */
    public function getPublicFile($file, $template = null) {
      if (!isset($template)) {
          $template = $this->getCode();
        }

      return CLICSHOPPING::linkPublic('Templates/' . $template . '/' . $file);
    }

/**
 * scan directory to create a dropdown
 * @param string $template_selected
 * @return string
 */
    public function getDropDownSelectedTemplateByCustomer($template_selected = 'Default') {
      $template_directory = CLICSHOPPING::getConfig('dir_root') .  $this->_directoryTemplateSources . '/' . $this->_directoryTemplate;
      $weeds = ['.', '..', '_notes', 'index.php', 'ExNewTemplate', '.htaccess', 'README'];
      $directories = array_diff(scandir($template_directory), $weeds);
      $filename_array = [];

      if (empty($template_selected)) {
        $template_selected = $this->_directoryTemplateDefault;
      }

      $filename_array[] =['id' => 0,
                          'text' => '-- Select --'
                         ];

      foreach($directories as $template_selected) {
        if(is_dir($template_directory.$template_selected)) {
          $filename_array[] = ['id' => $template_selected,
                              'text' => $template_selected
                              ];
        }
      }

      return HTML::selectMenu('TemplateCustomerSelected', $filename_array, $template_selected, 'onchange="this.form.submit();"');
    }

/**
 * Select a default template
 * to make : relation with the page and database
 * @param string $thema
 * @access public
 */
    public function setSiteThema($thema_directory = null) {

      if (is_null($thema_directory)) {
         $thema_directory = $this->_directoryTemplateSources . '/' . $this->_directoryTemplate . '/' . $this->_dynamicTemplate; //sources/template/SITE_THEMA
      } else {
// Use for multi template demonstration

        if (defined('MODULE_HEADER_SELECT_TEMPLATE_STATUS')) {
          if (MODULE_HEADER_SELECT_TEMPLATE_STATUS == 'True') {
            if ((isset($_POST['TemplateCustomerSelected'])) ) {
              $thema_directory = $this->_directoryTemplateSources . '/' . $this->_directoryTemplate . '/' . $_POST['TemplateCustomerSelected'];
            }
          } else {
            $thema_directory =  $this->_directoryTemplateSources . '/' . $this->_directoryTemplate . '/' . $thema_directory;
          }
        }
      }

      return $thema_directory;
    }

/**
 * Select the language directory
 *
 * @param string
 * DIR_WS_LANGUAGES - sources/langauges
 * @access public
 */

    public function getSiteTemplateLanguageDirectory() {
      return  $this->_directoryTemplateSources  . '/' . $this->_directoryTemplateLanguages; // sources/languages
    }

/**
 * Select the language directory
 *
 * @param string
 * DIR_WS_MODULES - includes/modules
 * @access public
 */
    public function getModuleDirectory() {
      return  $this->_directoryIncludes . '/' . $this->_directoryModules; // includes/modules
    }


/**
 * get path download
 *
 * @param string $modules_directory ,directory of the module
 * @access public
 * define('DIR_FS_DOWNLOAD_PUBLIC', CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/public/');
 * @return string
 */
    public function getPathDownloadShopDirectory($directory = null) {

      if (!is_null($directory)) {
        $path_shop_public_download_directory = $this->getTemplateSource() . '/' . $this->_directoryTemplateDownload . '/' . $directory . '/';
      } else {
        $path_shop_public_download_directory = $this->getTemplateSource() . '/' . $this->_directoryTemplateDownload . '/public/';
      }

      return $path_shop_public_download_directory;
    }


/**
 * Select the default template module directtory
 *
 * @param string
 * DIR_WS_TEMPLATE . SITE_THEMA . DIR_WS_TEMPLATE_MODULES
 * @access public
 */

    public function getSiteTemplateModuleDirectory() {
      return $this->_directoryTemplateSources . '/' . $this->_directoryTemplate . '/' . $this->_dynamicTemplate . '/' . $this->_directoryModules; // sources/template/SITE_THEMA/modules
    }

/**
 * Select the default template and verify if it exist
 *
 * @param string $thema
 * @access public
 */

    public function getPathDirectoryTemplateThema() {

      if (is_file(CLICSHOPPING::getConfig('dir_root', 'Shop') . static::setSiteThema() . '/' . $this->_directoryTemplateFiles . '/' . 'index.php') ) {
        $thema = static::setSiteThema();
      } elseif (is_file(CLICSHOPPING::getConfig('dir_root', 'Shop') . static::getDefaultTemplateDirectory() . '/' . $this->_directoryTemplateFiles . '/' . 'index.php') ) {
        $thema =  static::getDefaultTemplateDirectory();
      }  else {
        HTTP::redirect(HTTP::getShopUrlDomain()  . 'includes/error_documents/error_template.php');
        clearstatcache();
      }

// Use for multi template demonstration
      if (defined('MODULE_HEADER_SELECT_TEMPLATE_STATUS')) {
        if (MODULE_HEADER_SELECT_TEMPLATE_STATUS == 'True') {
          if ((isset($_POST['TemplateCustomerSelected'])) ) {
            $thema =  $this->_directoryTemplateSources . '/' . $this->_directoryTemplate . HTML::sanitize($_POST['TemplateCustomerSelected']);
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
 * @access public
 */
    public static function match($needles, $haystack)  {
      foreach($needles as $needle){
        if (! empty($needle) && strpos($haystack, $needle) !== false) {
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

    public function getReadModulesDefaultDirectories($source_folder = 'modules_') {
      $dir = $this->_directoryTemplateSources . '/' . $this->_template . '/' . $this->_codeSail . '/' . $this->_directoryModules;

      $exclude = [];

      $module_directories = array_diff(glob($dir . $source_folder . '*', GLOB_ONLYDIR), $exclude);

      $result = [];

      foreach ($module_directories as $value) {
        $result[] = str_replace($dir, '', $value);
      }

      return $result;
    }

    public function buildBlocks() {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Category = Registry::get('Category');

      if ( defined('TEMPLATE_BLOCK_GROUPS') && !is_null(TEMPLATE_BLOCK_GROUPS) ) {
        $tbgroups_array = explode(';', TEMPLATE_BLOCK_GROUPS);

        foreach ($tbgroups_array as $group) {
          $module_key = 'MODULE_' . strtoupper($group) . '_INSTALLED';

          if ( defined($module_key) && !is_null(constant($module_key)) ) {
            $modules_array = explode(';', constant($module_key));

            foreach ( $modules_array as $module ) {
// bug : create <br /> at the first line on html content code. Don't find solution to resolve that. come from $module
// Could a problem for example on the xml files but pass with google sitemap analyse but not all
              $class = basename($module, '.php');

// module language
              if ( !class_exists($class) ) {
                If (CLICSHOPPING::getSite('ClicShoppingAdmin')) {
                  $CLICSHOPPING_Language->loadDefinitions('modules/' . $group . '/' . pathinfo($module, PATHINFO_FILENAME));
                } else {
                  $CLICSHOPPING_Language->loadDefinitions('sources/template/Default/modules/' . $group . '/' . pathinfo($module, PATHINFO_FILENAME));
                }
//mode privee ou ouvert - affichage des boxes gauche ou droite
                if ( MODE_VENTE_PRIVEE == 'true' && $CLICSHOPPING_Customer->isLoggedOn() ) {
                  $modules_boxes = 'modules_boxes';
                } elseif (MODE_VENTE_PRIVEE == 'true' && !$CLICSHOPPING_Customer->isLoggedOn ) {
                  $modules_boxes = '';
                } else {
                  $modules_boxes = 'modules_boxes';
                }

                if ($group == $modules_boxes) {
//check the module exist inside the template or take default template
                  if (is_file(static::getPathDirectoryTemplateThema() . '/' . $this->_directoryModules  . $group . '/' . $class . '.php')) {
                    include(static::getPathDirectoryTemplateThema() . '/' . $this->_directoryModules  . $group . '/' . $class . '.php');
                  } elseif (is_file(static::getDefaultTemplateDirectory() . '/' . $this->_directoryModules  . $group . '/' . $class . '.php')) {
                    include(static::getDefaultTemplateDirectory() . '/' . $this->_directoryModules . $group . '/' . $class . '.php');
                  } else {
                    if ( is_file($this->_directoryIncludes . '/' . $this->_directoryModules . $group . '/' . $class . '.php') ) {
                      include($this->_directoryIncludes . '/' . $this->_directoryModules . $group . '/' . $class . '.php');
                    }
                  }
                } else {
//default module
                  if ( is_file($this->_directoryIncludes . '/' . $this->_directoryModules . $group . '/' . $class . '.php') ) {
                    include($this->_directoryIncludes . '/' . $this->_directoryModules . $group . '/' . $class . '.php');
                  }
                }

// exclude $modules_boxe and search if the modules exist
                if(is_numeric(array_search($group, $this->getReadModulesDefaultDirectories())) && $group != $modules_boxes) {
                  $result = array_search($group, $this->getReadModulesDefaultDirectories());

                  if (!is_null($result)) {
                    if (is_file(static::getPathDirectoryTemplateThema() . '/' . $this->_directoryModules  . $group . '/' . $class . '.php')) {
                      include(static::getPathDirectoryTemplateThema() . '/' . $this->_directoryModules  . $group . '/' . $class . '.php');
                    } elseif (is_file(static::getDefaultTemplateDirectory() . '/' . $this->_directoryModules  . $group . '/' . $class . '.php')) {
                      include(static::getDefaultTemplateDirectory() . '/' . $this->_directoryModules . $group . '/' . $class . '.php');
                    } else {
                      if ( is_file($this->_directoryIncludes . '/' . $this->_directoryModules . $group . '/' . $class . '.php') ) {
                        include($this->_directoryIncludes . '/' . $this->_directoryModules . $group . '/' . $class . '.php');
                      }
                    }
                  } else {
                    if ($group != $modules_boxes) {
                      if ( is_file($this->_directoryIncludes . '/' . $this->_directoryModules . $group . '/' . $class . '.php') ) {
                        include($this->_directoryIncludes . '/' . $this->_directoryModules . $group . '/' . $class . '.php');
                      }
                    }
                  }
                }
              }

              if ( class_exists($class) ) {
                $mb = new $class();

// Dynamic boxe
                if(!isset($mb->pages) && ($mb->isEnabled())){
                  $this->pages = 'all';
                  $mb->execute();
                } else {

// hide or display the box left / right
                  $page = explode(';' , $mb->pages);

                  if( ($mb->isEnabled() && $mb->pages == 'all') ) {
                    $mb->execute();
                  } else {
                    if ($mb->isEnabled() && isset($mb->pages)) {
                      $string = $_SERVER['QUERY_STRING'];

// categories
// identify a categorie like index page
                      if ($CLICSHOPPING_Category->getPath()) {
                        $string = 'index.php?Categories';
                      }

//index page
// Boxe does'nt work when the page is refreshed with a query_string
                      if (empty($string)) {
                        if (CLICSHOPPING::getBaseNameIndex()) {
                          $string = 'index.php';
                        }
                      }

                      if($this->match($page, $string) === true) {
                        $mb->execute();
                      } else {
                        $mb->notEnabled;
                      }
                    }
                  }
                } // end Dynamic Template
              }
            }
          }
        }
      }
    }


/**
   * Select the header or footer of the template
   *
   * @param string $name, header or footer of the template
   * @access public
 */
    public function getTemplateHeaderFooter($name) {

      if (file_exists(static::getPathDirectoryTemplateThema() . '/' . $name.'.php')) {
        $themaFiles = static::getPathDirectoryTemplateThema()  .  '/' . $name.'.php';
      } else {
        $themaFiles = static::getDefaultTemplateDirectory() . '/' . $name.'.php';
      }

      return $themaFiles;
    }

/**
   * Select the css in directory of the template by language
   *
   * @param string $themaGraphism, css directory in the template
   * @access public
 */
    public function getTemplategraphism() {
      $CLICSHOPPING_Language = Registry::get('Language');
      if (is_file(CLICSHOPPING::getConfig('dir_root', 'Shop') . '/' . static::getPathDirectoryTemplateThema() . '/' . $this->_directoryTemplateCss . '/' . $CLICSHOPPING_Language->get('directory') . '/' . 'compressed_css.php')) {
        $themaCSS = CLICSHOPPING::link(static::getPathDirectoryTemplateThema() . '/' . $this->_directoryTemplateCss . '/' . $CLICSHOPPING_Language->get('directory') . '/' . 'compressed_css.php');
      } else {
        $themaCSS = CLICSHOPPING::link(static::getDefaultTemplateDirectory() . '/' . $this->_directoryTemplateCss . '/' . $CLICSHOPPING_Language->get('directory') . '/' . 'compressed_css.php');
      }

// if current does'nt exist take default
      if (!is_file(CLICSHOPPING::getConfig('dir_root', 'Shop') . '/' .  static::getPathDirectoryTemplateThema() . '/' . $this->_directoryTemplateCss . '/' . $CLICSHOPPING_Language->get('directory') . '/' . 'compressed_css.php')) {
        $themaCSS = CLICSHOPPING::link(static::getPathDirectoryTemplateThema() . '/' . $this->_directoryTemplateCss . '/english/' . 'compressed_css.php');
      }

      return $themaCSS;
    }


/**
  * Select the the file in this directory Files
  *
  * @param string $themaGraphism, file in this directory Files
  * @access public
*/
    public function getTemplateFiles($name) {
      if (is_file(static::getPathDirectoryTemplateThema() . '/' . $this->_directoryTemplateFiles . '/' . $name . '.php')) {
        $themaFiles = static::getPathDirectoryTemplateThema()  .  '/' . $this->_directoryTemplateFiles . '/' . $name . '.php';
      } else {
        $themaFiles = static::getDefaultTemplateDirectory() . '/' . $this->_directoryTemplateFiles . '/' . $name . '.php';
      }

      return $themaFiles;
    }

/**
   * Select the the file in this directory module
   *
   * @param string $themaGraphism, file in this directory module
   * @access public
 */
    public function getTemplateModules($name) {

      if (is_file(static::getPathDirectoryTemplateThema() .  '/' . $this->_directoryModules . $name . '.php')) {
        $themaFiles = static::getPathDirectoryTemplateThema() .  '/' . $this->_directoryModules . $name . '.php';
      } else {
        $themaFiles = static::getDefaultTemplateDirectory() . '/' . $this->_directoryModules . $name . '.php';
      }

      return $themaFiles;
    }

/**
  * Select the the filename in this directory Modules
  *
  * @param string $themaFilename, filename in this module
  * @access public
*/
    public function getTemplateModulesFilename($name) {

      if (is_file(static::getPathDirectoryTemplateThema() . '/' . $this->_directoryModules . $name)) {
        $themaFilename = static::getPathDirectoryTemplateThema() . '/' . $this->_directoryModules . $name;
      } else {
        $themaFilename = static::getDefaultTemplateDirectory() . '/' . $this->_directoryModules . $name;
      }

      return $themaFilename;
    }

/**
 * Select the file language in function the the file for the template
 *
 * @param string $languagefiles, file language in function the the file for the template
 * @access public
 */

    public function GetPathDirectoryTemplatetLanguageFiles($name) {
      $CLICSHOPPING_Language = Registry::get('Language');

      if (is_file(CLICSHOPPING::getConfig('dir_root', 'Shop') . static::getPathDirectoryTemplateThema() . '/' . 'languages' . '/' . $CLICSHOPPING_Language->get('directory')  . '/' . $name . '.php') ) {
        $languagefiles = static::getPathDirectoryTemplateThema() . '/' . 'languages' . '/' .  $CLICSHOPPING_Language->get('directory') . '/' . $name . '.php';
          if (is_file(CLICSHOPPING::getConfig('dir_root', 'Shop') . $this->getSiteTemplateLanguageDirectory() . '/' . $CLICSHOPPING_Language->get('directory')  . '/' . $name . '.php')) {
          $languagefiles = $this->getSiteTemplateLanguageDirectory() . '/' . $CLICSHOPPING_Language->get('directory') . '/' . $name . '.php';
        }
      } else {
        $languagefiles = $this->getSiteTemplateLanguageDirectory() . '/' .$CLICSHOPPING_Language->get('directory') . '/' . $name . '.php';
      }

      return $languagefiles;
    }

/**
 * public
 * Select the javascript for all template
 * @param $name $name of the js
 * @return string $javascript, directory of javascript in the template directory
 */
    public function getTemplateDefaultJavaScript($name) {
      $javascript = $this->_directoryTemplateSources . '/javascript/' . $name;

      return $javascript;
    }

/**
 * public
 * Select the javascript inside a specific theme directory
 * @param $name $name of the js
 * @return string $javascript, directory of javascript in the template directory
 */
    public function getTemplateThemaJavaScript($name) {

      if (is_file(static::getPathDirectoryTemplateThema() . '/javascript/' . $name)) {
        $javascript = static::getPathDirectoryTemplateThema()  .  '/javascript/' . $name;
      } else {
        $javascript = static::getDefaultTemplateDirectory() . '/javascript/' . $name;
      }

      return $javascript;
    }

/**
 * Select all the files inside directory
 *
 * @param string $source_folder, directory
 * @param string $filename, name of the file
 *@param string $ext, file extension
 * @param return array, files name
 * @access public
 */
    public function getSpecificFiles($source_folder, $filename, $ext = 'php') {
      if( !is_dir( $source_folder ) ) {
        die ( "Invalid directory.\n\n" );
      }

      $FILES = glob($source_folder . $filename . '.' . $ext);

      foreach($FILES as $key => $file) {
        $result = str_replace($source_folder, '', $file);
        $FILE_LIST[$key]['name'] = str_replace('.' . $ext, '', $result);
      }

      return $FILE_LIST;
    }
  }