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

  namespace ClicShopping\Sites\ClicShoppingAdmin;

  use ClicShopping\OM\Apps;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class TemplateAdmin extends \ClicShopping\Sites\Shop\Template {

    protected $_default_template_directory = 'Default';
    protected $thema;
    protected $name;
    protected $_directoryAdminLanguages = 'languages';
    protected $_directoryAdminmodules = 'modules';
    protected $_directoryAdmin = 'ClicShoppingAdmin';
    protected $_directoryAdminBoxes = 'boxes';
    protected $_directoryAdminImages = 'images';
    protected $_directoryAdminIncludes = 'includes';

/**********************************************
 * Path
 ************************************************/

/**
 * Get the path for the default template HTML directory
 *
 * @param string $themaFilename , filename in this module
 * @access public
 * @return string
 */
    public function getDirectoryPathShopDefaultTemplateHtml() {
      return CLICSHOPPING::getConfig('dir_root','Shop') . parent::getDefaultTemplateDirectory(); // /sources/template/default
    }

/**
 * get the catalog modules directory
 *
 * @param string $language_directory ,directory of the language
 * @access public
 *  define('CLICSHOPPING::getConfig('dir_root', 'Shop') 'sources/languages/');
 * @return string
 */
    public function getPathLanguageShopDirectory() {
      $path_shop_languages_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/' . $this->_directoryAdminLanguages;

      return $path_shop_languages_directory;
    }

/**
 * get path public download
 *
 * @param string $modules_directory ,directory of the module
 * @access public
 * define('DIR_FS_DOWNLOAD_PUBLIC', CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/download/');
 * @return string
 */
    public function getPathDownloadShopDirectory($directory = null) {
      $path_shop_public_download_directory = parent::getPathDownloadShopDirectory($directory);

      return $path_shop_public_download_directory;
    }

/**
 * Verify if module directory exist in shop template
 *
 * @param string
 * @access public
 * @return string
 */

    public function getDirectoryPathModuleShopTemplateHtml($name) {

      if (file_exists(CLICSHOPPING::getConfig('dir_root', 'Shop') . parent::getDynamicTemplateDirectory() . '/modules/' . $name . '/template_html/')) {
        $template_directory =  CLICSHOPPING::getConfig('dir_root', 'Shop') . parent::getDynamicTemplateDirectory() . '/modules/' . $name . '/template_html/';
      } else {
        $template_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . static::getDefaultTemplateDirectory() . '/modules/' . $name . '/template_html/';
      }

      return $template_directory;
    }

/**
 * Verify if the timage direcotyr exist in shop
 *
 * @param string
 * @access public
 * @return string
 */

    public function getDirectoryPathTemplateShopImages() {
      return CLICSHOPPING::getConfig('dir_root', 'Shop') . parent::getDirectoryTemplateImages(); // CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/images/
    }

/**
 * get the catalog modules directory
 *
 * @param string $language_directory ,directory of the language
 * @access public

 * @return string
 */
    public function getDirectoryPathModuleShop() {
      $modules_catalog_directory =  $this->getModulesDirectory() . '/modules';

      return $modules_catalog_directory;
    }


/**********************************************
* Relative / virtual Path
***********************************************

/*
 * getFileAdmin inside a directory
 * @param : $file name of the file
 * @param : $template : template directory
 * /www/
 */
    public function getTemplateHeaderFooterAdmin($file, $template = 'Default') {

      if (isset($template)) {
        $template = CLICSHOPPING::BASE_DIR . 'Sites/' . CLICSHOPPING::getSite() . '/Templates/' . $template . '/' . $file;
      }

      return $template;
    }

/**
* get the Relative Path template directory
*
* @param string $themaFilename , filename in this module
* @access public
* /sources/template
* @return string
*/
    public function getTemplateDirectory() {
      return parent::getTemplateDirectory(); //sources/template
    }

/*
* get the Relative Path for dynamic template directory
*
* @param string $themaFilename , filename in this module
* @access public
* //sources/template/SITE_THEMA
* @return string
*/
    public function getDynamicTemplateDirectory() {
      return parent::getDynamicTemplateDirectory(); //sources/template/SITE_THEMA
    }

/*
* get the Relative Path for image directory
*
* @param string $themaFilename , filename in this module
* @access public
* @return string
*/
    public function getImageDirectory() {
      return CLICSHOPPING::getConfig('http_server') . CLICSHOPPING::getConfig('http_path', 'Shop') . $this->_directoryAdminImages . '/' . $this->_directoryAdmin .'/';
    }

/**
 * get the boxes directory
 *
 * @param string $language_directory ,directory of the language
 * @access public
 * define('DIR_WS_BOXES', 'includes/boxes/');

 * @return string
 */
    public function getBoxeDirectory() {
      $directory = $this->_directoryAdminIncludes . '/' . $this->_directoryAdminBoxes; //'includes/boxes/'

      return $directory;
    }

/**
 * get the language directory
 *
 * @param string $language_directory ,directory of the language
 * @access public
 * define('DIR_WS_LANGUAGES', 'includes/languages/');
 * @return string
 */
    public function getLanguageDirectory() {
      $directory = $this->_directoryAdminIncludes . '/' . $this->_directoryAdminLanguages; //'includes/languages/'

      return $directory;
    }

/**
 * get the modules directory
 *
 * @param string $modules_directory ,directory of the module
 * @access public
 *   define('DIR_WS_MODULES', 'includes/modules/');
 * @return string
 */
    public function getModulesDirectory() {
      $directory= CLICSHOPPING::getConfig('dir_root','Shop') . $this->_directoryAdminIncludes;

      return $directory;
    }

/**
 * get the the shop image directory
 *
 * @param string
 * @access public
 * @return string
 */
    public function getDirectoryShopTemplateImages() {
      $directory = CLICSHOPPING::getConfig('http_server') . CLICSHOPPING::getConfig('http_path', 'Shop') . parent::getDirectoryTemplateImages(); //'CLICSHOPPING::getConfig('https_path', 'Shop')  . 'sources/images/'
      return $directory;
    }

/**
 * get the the shop sources directory
 *
 * @param string
 * @access public
 * @return string
 */
    public function getDirectoryShopSources() {
      $directory = parent::getTemplateSource(); //' CLICSHOPPING::getConfig('dir_root') . 'sources/'

      return $directory;
    }

/**
 * All files about the catalog
 *
 * @string $catalog_files, string, nwe specific files
 * @access public
 * @return array, file list
 */
    public static function getCatalogFiles($catalog_files = null) {
      $file_array = ['Account&AddressBook',
                    'Account&Create',
                    'Account&CreatePro',
                    'Account&CreateProSuccess',
                    'Account&Delete',
                    'Account&Edit',
                    'Account&History',
                    'Account&HistoryInfo',
                    'Account&Login',
                    'Account&MyFeedBack',
                    'Account&Newsletter',
                    'Account&NewsletterNoAccount',
                    'Account&NewsletterNoAccountSuccess',
                    'Account&Notification',
                    'Account&Password',
                    'Blog&Categories',
                    'Blog&Content',
                    'Categories',
                    'Cart',
                    'Checkout&Shipping',
                    'Checkout&ShippingAddress',
                    'Checkout&Billing',
                    'Checkout&PaymentAddress',
                    'Checkout&Confirmation',
                    'Checkout&Success',
                    'Compare&ProductsCompare',
                    'Info&Contact',
                    'Info&Cookies',
                    'Info&Content',
                    'Info&SiteMap',
                    'Info&SSLcheck',
                    'Products&Description',
                    'Products&Favorites',
                    'Products&Featured',
                    'Products&ProductsNew',
                    'Products&Specials',
                    'search&AdvancedSearch',
                    'search&Q',
                  ];

      if (!is_null($catalog_files)) {
        $file_array = [$catalog_files];
      }

      return $file_array;
    }


/**
 *  Dynamic Template System
 * Return an array of the catalog directory. mechanism for reading this.
 */

    public static function getListCatalogFilesNotIncluded($boostrap_file = null) {

      if (is_null($boostrap_file)) $boostrap_file = CLICSHOPPING::getConfig('bootstrap_file');

      $file = static::getCatalogFiles();

      $result = [];

      $result[] = $boostrap_file;

      foreach ($file as &$value) {
        $result[] .= $value;
      }

      return $result;
    }

/*
 * get all files inside a multi template directory
 * @params : $filename ! filename of the template
 * @params : module, module about the template
 * $@return = return an array
 */
    public function getMultiTemplatePullDown($filename, $module) {
      $CLICSHOPPING_Db = Registry::get('Db');

      $key = $this->default;

      $template_directory = $this->getDirectoryPathModuleShopTemplateHtml($module);

      if ($contents = @scandir($template_directory)) {

        $found = []; //initialize an array for matching files
        $fileTypes = ['php']; // Create an array of file types
        $found = []; // Traverse the folder, and add filename to $found array if type matches

        $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

        foreach ($contents as $item) {
          $fileInfo = pathinfo($item);
          if (array_key_exists('extension', $fileInfo) && in_array($fileInfo['extension'], $fileTypes)) {
            $found[] = $item;
          }
        }

        if ($found) { // Check the $found array is not empty
          natcasesort($found); // Sort in natural, case-insensitive order, and populate menu
          $filename_array = [];

          foreach ($found as $filename) {
            $filename_array[] = ['id' => $filename,
                                 'text' => $filename
                                ];
          }
        }
      }

      $QfileName = $CLICSHOPPING_Db->prepare('select configuration_value
                                               from :table_configuration
                                               where configuration_key = :configuration_key
                                             ');
      $QfileName->bindValue(':configuration_key', $key);

      $QfileName->execute();

      $filename_value  = $QfileName->value('configuration_value');

      return HTML::selectMenu($this->key, $filename_array,  $filename_value);
    }


    public function getSpecificFiles($source_folder, $filename, $ext = 'php') {
      $result = parent::getSpecificFiles($source_folder, $filename, $ext);

      return $result;
    }

  }