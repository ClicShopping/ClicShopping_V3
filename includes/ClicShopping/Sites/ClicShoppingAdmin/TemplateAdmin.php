<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Sites\ClicShoppingAdmin;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\HTTP;

  class TemplateAdmin extends \ClicShopping\Sites\Shop\Template
  {
    protected string $directoryAdminLanguages = 'languages/';
    protected string $directoryAdmin = 'ClicShoppingAdmin/';
    protected string $directoryAdminBoxes = 'boxes/';
    protected string $directoryAdminImages = 'images/';
    protected string $directoryAdminIncludes = 'includes/';
    protected string $directoryAdminModules = 'modules/';
    protected string $directoryAdminSources = 'sources/';
    public $default;
    public $key;

    /**********************************************
     * Path
     ************************************************/

    /**
     * Get the path for the default template HTML directory
     *
     * @param string $themaFilename , filename in this module
     *
     * @return string
     */
    public function getDirectoryPathShopDefaultTemplateHtml(): string
    {
      return parent::getPathRoot() . parent::getDefaultTemplateDirectory(); // /sources/template/default
    }

    /**
     * get the catalog modules directory
     *
     * @param string $language_directory ,directory of the language
     *
     *  define('parent::getPathRoot() 'sources/languages/');
     * @return string
     */
    public function getPathLanguageShopDirectory(): string
    {
      $path_shop_languages_directory = parent::getPathRoot() . $this->directoryAdminSources . $this->directoryAdminLanguages;

      return $path_shop_languages_directory;
    }

    /**
     * get path public download
     *
     * @param string $modules_directory ,directory of the module
     *
     * define('DIR_FS_DOWNLOAD_PUBLIC', parent::getPathRoot() . 'sources/download/');
     * @return string
     */
    public function getPathDownloadShopDirectory($directory = null): string
    {
      $path_shop_public_download_directory = parent::getPathDownloadShopDirectory($directory);

      return $path_shop_public_download_directory;
    }

    /**
     * Verify if module directory exist in shop template
     *
     * @param string
     *
     * @return string
     */

    public function getDirectoryPathModuleShopTemplateHtml(string $name): string
    {
      if (file_exists(parent::getPathRoot() . parent::getDynamicTemplateDirectory() . '/'. $this->directoryAdminModules . $name . '/template_html/')) {
        $template_directory = parent::getPathRoot() . parent::getDynamicTemplateDirectory() . '/'. $this->directoryAdminModules . $name . '/template_html/';
      } else {
        $template_directory = parent::getPathRoot() . $this->getDefaultTemplateDirectory() . '/'. $this->directoryAdminModules . $name . '/template_html/';
      }

      return $template_directory;
    }

    /**
     * Verify if the image exist in shop
     * @return string
     */

    public function getDirectoryPathTemplateShopImages(): string
    {
      return parent::getPathRoot() . parent::getDirectoryTemplateImages(); // CLICSHOPPING::getConfig('dir_root', 'Shop1') . 'sources/images/
    }


    /**
     * Verify if the image exist in shop
     * @return string
     */

    public function getHttpTemplateShopImages(): string
    {
      return HTTP::getShopUrlDomain() . parent::getDirectoryTemplateImages(); // CLICSHOPPING::getConfig('dir_root', 'Shop1') . 'sources/images/
    }

    /**
     * Verify if the language exist in shop
     * @return string
     */

    public function getDirectoryPathLanguage(): string
    {
      return parent::getTemplateSource() . 'languages/';
    }

    /**
     * get the catalog modules directory
     *
     * @param string $language_directory ,directory of the language
     *
     * @return string
     */
    public function getDirectoryPathModuleShop(): string
    {
      $modules_catalog_directory = $this->getModulesDirectory() . '/'. $this->directoryAdminModules;

      return $modules_catalog_directory;
    }


    /**********************************************
     * Relative / virtual Path
     *********************************************
     *
     *
     * getFileAdmin inside a directory
     * @param : $file name of the file
     * @param : $template : template directory
     * /www/
     */
    public function getTemplateHeaderFooterAdmin(string $file, string $template = 'Default'): string
    {

      if (isset($template)) {
        $template = CLICSHOPPING::BASE_DIR . 'Sites/' . CLICSHOPPING::getSite() . '/Templates/' . $template . '/' . $file;
      }

      return $template;
    }

    /**
     * get the Relative Path template directory
     *
     * @param string $themaFilename , filename in this module
     *
     * /sources/template
     * @return string
     */
    public function getTemplateDirectory(): string
    {
      return parent::getTemplateDirectory(); //sources/template
    }

    /*
    * get the Relative Path for dynamic template directory
    *
    * @param string $themaFilename , filename in this module
    *
    * //sources/template/SITE_THEMA
    * @return string
    */
    public function getDynamicTemplateDirectory(): string
    {
      return parent::getDynamicTemplateDirectory(); //sources/template/SITE_THEMA
    }

    /*
    * get the Relative Path for image directory
    *
    * @param string $themaFilename , filename in this module
    *
    * @return string
    */
    public function getImageDirectory(): string
    {
      return CLICSHOPPING::getConfig('http_server') . CLICSHOPPING::getConfig('http_path', 'Shop') . $this->directoryAdminImages . $this->directoryAdmin;
    }

    /**
     * get the boxes directory
     *
     * @param string $language_directory ,directory of the language
     *
     * define('DIR_WS_BOXES', 'includes/boxes/');
     * @return string
     */
    public function getBoxeDirectory(): string
    {
      $directory = $this->directoryAdminIncludes . $this->directoryAdminBoxes; //'includes/boxes/'

      return $directory;
    }

    /**
     * get the language directory
     *
     * @param string $language_directory ,directory of the language
     *
     * define('DIR_WS_LANGUAGES', 'includes/languages/');
     * @return string
     */
    public function getLanguageDirectory(): string
    {
      $directory = $this->directoryAdminIncludes . $this->directoryAdminLanguages; //'includes/languages/'

      return $directory;
    }

    /**
     * get the modules directory
     *
     * @param string $modules_directory ,directory of the module
     *
     *   define('DIR_WS_MODULES', 'includes/modules/');
     * @return string
     */
    public function getModulesDirectory(): string
    {
      $directory = parent::getPathRoot() . $this->directoryAdminIncludes;

      return $directory;
    }

    /**
     * get the the shop image directory
     *
     * @param string
     *
     * @return string
     */
    public function getDirectoryShopTemplateImages(): string
    {
      $directory = CLICSHOPPING::getConfig('http_server') . CLICSHOPPING::getConfig('http_path', 'Shop') . parent::getDirectoryTemplateImages(); //'CLICSHOPPING::getConfig('https_path', 'Shop')  . 'sources/images/'

      return $directory;
    }

    /**
     * get the the shop sources directory
     *
     * @param string
     *
     * @return string
     */
    public function getDirectoryShopSources(): string
    {
      $directory = parent::getTemplateSource(); //' CLICSHOPPING::getConfig('dir_root') . 'sources/'

      return $directory;
    }

    /**
     * All files about the catalog
     *
     * @string $catalog_files, string, nwe specific files
     *
     * @param string|null $catalog_files
     * @return array, file list
     */
    public static function getCatalogFiles(?string $catalog_files = null): array
    {
      $file_array = [
        'Account&AddressBook',
        'Account&Create',
        'Account&CreatePro',
        'Account&CreateProSuccess',
        'Account&Delete',
        'Account&Edit',
        'Account&History',
        'Account&HistoryInfo',
        'Account&LogIn',
        'Account&MyFeedBack',
        'Account&Newsletter',
        'Account&NewsletterNoAccount',
        'Account&NewsletterNoAccountSuccess',
        'Account&Notification',
        'Account&Password',
        'Blog&Categories',
        'Blog&Content',
        'Categories',
        'cPath',
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
        'FlashSelling&ProductsFlashSelling',
        'Products&Description',
        'Products&Favorites',
        'Products&Featured',
        'Products&ProductsNew',
        'Products&Specials',
        'Products&TellAFriend',
        'Products&Recommendations',
        'search&AdvancedSearch',
        'search&Q',
      ];

      if (SEARCH_ENGINE_FRIENDLY_URLS_PRO == 'true' || SEARCH_ENGINE_FRIENDLY_URLS == 'true') {
        $file_array = str_replace(['&'], ['/'], $file_array);
      }

      if (!\is_null($catalog_files)) {
        $file_array = [$catalog_files];
      }

      return $file_array;
    }

    /**
     *  Dynamic Template System
     * Return an array of the catalog directory. mechanism for reading this.
     */

    public static function getListCatalogFilesNotIncluded(?string $boostrap_file = null): array
    {
      if (\is_null($boostrap_file)) $boostrap_file = CLICSHOPPING::getConfig('bootstrap_file');

      $file = static::getCatalogFiles();

      $result = [];

      $result[] = $boostrap_file;

      foreach ($file as &$value) {
        $result[] .= $value;
      }

      return $result;
    }

    /**
     * get all files inside a multi template directory
     * @param string $filename
     * @param string $module
     * @return array
     */
    public function getMultiTemplatePullDown(string $filename, string $module): array
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $key = $this->default;

      $template_directory = $this->getDirectoryPathModuleShopTemplateHtml($module);

      if ($contents = @scandir($template_directory)) {
        $fileTypes = ['php']; // Create an array of file types
        $found = []; // Traverse the folder, and add filename to $found array if type matches

        foreach ($contents as $item) {
          $fileInfo = pathinfo($item);
          if (array_key_exists('extension', $fileInfo) && \in_array($fileInfo['extension'], $fileTypes)) {
            $found[] = $item;
          }
        }

        if ($found) { // Check the $found array is not empty
          natcasesort($found); // Sort in natural, case-insensitive order, and populate menu
          $filename_array = [];

          foreach ($found as $filename) {
            $filename_array[] = [
              'id' => $filename,
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

      $filename_value = $QfileName->value('configuration_value');

      return HTML::selectMenu($this->key, $filename_array, $filename_value);
    }

    /**
     * @param string $source_folder
     * @param string $filename
     * @param string $ext
     * @return array|null
     */
    public function getSpecificFiles(string $source_folder, string $filename, string $ext = 'php')
    {
      $result = parent::getSpecificFiles($source_folder, $filename, $ext);

      return $result;
    }

    /**
     * Use Modules Hooks to call some element for the header or footer
     * @param string $source_folder
     * @param string $file_get_output
     * @param string $files_get_call
     * @param string $hook_call
     */
    public function useRecursiveModulesHooksForTemplate(string $source_folder, string $file_get_output, string $files_get_call, string $hook_call)
    {
      $result = parent::useRecursiveModulesHooksForTemplate($source_folder, $file_get_output, $files_get_call, $hook_call);

      return $result;
    }

    /**
     * look the directory template inside template directory
     * @param string $key
     * @param string $default
     * @param bool $config
     * @return string
     */
    public function getAllTemplate(string $key = '', string $default = '', $config = true) :string
    {
      if ($config === true ) {
        $name = (!empty($key) ? 'configuration[' . $key . ']' : 'configuration_value');
      } else {
        $name = $key;
      }

      $template_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . $this->getTemplateDirectory() . '/';

      $weeds = array('.', '..', '_notes', 'index.php', 'ExNewTemplate', '.htaccess', 'README');

      $directories = array_diff(scandir($template_directory), $weeds);

      if ($config === true ) {
        $filename_array = [];
      } else {
        $filename_array[] = [
          'id' => null,
          'text' => $default
        ];
      }

      foreach ($directories as $value) {
        if (is_dir($template_directory . $value)) {
          $filename_array[] = [
            'id' => $value,
            'text' => $value
          ];
        }
      }

      return HTML::selectMenu($name, $filename_array, $value);
    }

    /**
     * Update templatetheme
     * @param string $name
     * @param string $default
     * @param string|null $item_value
     * @return string
     */
    public function updateTemplate(string $name, string $default = '', ?string $item_value) :string
    {
      $template_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . $this->getTemplateDirectory() . '/';

      $weeds = array('.', '..', '_notes', 'index.php', 'ExNewTemplate', '.htaccess', 'README');

      $directories = array_diff(scandir($template_directory), $weeds);

      $filename_array[] = [
        'id' => null,
        'text' => $default
      ];

      if (empty($item_value)) {
        $item_value = null;
      }

      foreach ($directories as $value) {
        if (is_dir($template_directory . $value)) {
          $filename_array[] = [
            'id' => $value,
            'text' => $value
          ];
        }
      }

      return HTML::selectMenu($name, $filename_array, $item_value);
    }
  }