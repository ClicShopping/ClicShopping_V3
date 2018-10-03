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

  namespace ClicShopping\Apps\Tools\Upgrade\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\FileSystem;
  use ClicShopping\OM\Cache;
  use ClicShopping\OM\HTML;

  use ClicShopping\Sites\ClicShoppingAdmin\ModuleDownload;

  use ClicShopping\Apps\Tools\Upgrade\Upgrade as UpgradeApp;

  class Github {

    protected $app;
    protected $github;
    protected $githubRepo;
    protected $context;
    protected $coreName;
    protected $githubRepoClicShoppingCore;
    protected $githubRepoName;
    protected $saveFileFromGithub;
    protected $cacheGithub;
    public $cacheGithubTemp;
    protected $ModuleInfosJson;

    public function __construct() {

      if (!Registry::exists('Upgrade')) {
        Registry::set('Upgrade', new UpgradeApp());
      }

      $this->app = Registry::get('Upgrade');

      $this->githubUrl = 'https://github.com';
      $this->githubApi = 'https://api.github.com';
      $this->githubRepo = 'repos';
      $this->context = stream_context_create(array('http' => array('header' => 'User-Agent: ClicShopping')));

      $this->coreName = 'ClicShopping';
      $this->githubRepoClicShoppingCore = 'ClicShopping_V3';

      if (isset($_POST['official'])) {
        if ($_POST['official'] == 'official') {
          $this->githubRepoName = 'ClicShoppingOfficialModulesV3';
        } else {
          $this->githubRepoName = 'ClicShoppingV3Community';
        }
      }

      $this->saveFileFromGithub = CLICSHOPPING::BASE_DIR . 'Work/Temp';
      $this->cacheGithub = CLICSHOPPING::BASE_DIR . 'Work/Cache/Github/';
      $this->cacheGithubTemp = CLICSHOPPING::BASE_DIR . 'Work/Cache/Github/Temp/';
      $this->ModuleInfosJson  = 'ModuleInfosJson';
    }

/*
* getGithubCoreRepo : ClicShopping Core
* @param
* @return  $url url about the ClicShopping Core
* @access
*/
    private function getGithubCoreRepo() {
      $url = $this->githubApi . '/' . $this->githubRepo . '/' . $this->coreName . '/' . $this->githubRepoClicShoppingCore;
      return $url;
    }

/*
* user agent in Header
* @param
* @return  user agent
* @access public
*/
    private function setContext() {
      return  $this->context;
    }


/**
 * get ClicShopping Core Version from Github
 * @param
 * @return $version version of new clicshopping core
 * @access public
 */
    public function getJsonCoreInformation() {
      $VersionCache = new Cache('clicshopping_core_information');

      if ($VersionCache->exists(30)) {
        $result = $VersionCache->get();
      } else {
        $json = @file_get_contents($this->getGithubCoreRepo() . '/contents/boutique/includes/ClicShopping/version.json?ref=master', true, $this->context);

        $url = json_decode($json);

        $url_download = @file_get_contents($url->download_url, true, $this->setContext()); //content of readme.
        $data = json_decode($url_download);

        $result = $VersionCache->save($data);
      }

      return $result;
    }

/**
 * Extract Zip file
 *
 * @uses ZipArchive
 * @param string $source
 * @param string $destination
 * @return bool
 */
    private function getExtractZip($source, $destination){
      $zip = new \ZipArchive;

      if($zip->open($source) === true) {
        $zip->extractTo($destination);
        $zip->close();
        return true;
      } else {
        return false;
      }
    }

/*
* user open or Close the store during the upgrade process
* @param $value,true / false
* @return
* @access public
*/
    private function getCloseOpenStore($value) {
      $CLICSHOPPING_Db = Registry::get('Db');

      if (defined('STORE_OFFLINE')) {
        $Qupdate = $CLICSHOPPING_Db->prepare('update :table_configuration
                                              set configuration_value = :configuration_value,
                                              last_modified = now()
                                              where configuration_key = :configuration_key
                                             ');
        $Qupdate->bindValue(':configuration_value', $value );
        $Qupdate->bindValue(':configuration_key', 'STORE_OFFLINE');
        $Qupdate->execute();
      }
    }

/**
 * Check directory if exist or not
 * @param
 * @return Boolean true / false
 * @access public
 */
    private function checkDirectoryOnlineUpdate() {
      if (!is_dir($this->saveFileFromGithub)) {
        mkdir($this->saveFileFromGithub, 0777, true);
        return true;
      } elseif (FileSystem::isWritable($this->saveFileFromGithub)) {
        return true;
      } else {
        return false;
      }

      if (!is_dir($this->cacheGithub)) {
        mkdir($this->cacheGithub, 0777, true);
        return true;
      } elseif (FileSystem::isWritable($this->cacheGithub)) {
        return true;
      } else {
        return false;
      }

      if (!is_dir($this->cacheGithubTemp)) {
        mkdir($this->cacheGithubTemp, 0777, true);
        return true;
      } elseif (FileSystem::isWritable($this->cacheGithubTemp)) {
        return true;
      } else {
        return false;
      }
    }



/**
 * get all modules directories for template
 * @param
 * @return $module,values of array
 * @access public
 */
    public function getModuleTemplateDirectory() {

      $default_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/template/Default/modules';
      $module[] = ['id' => '0',
                   'text' => $this->app->getDef('text_select_module_template')
                  ];

      $exclude = ['..', '.', 'customers_address', 'download', 'index.php', '_htaccess', '.htaccess'];
      $module_dir = array_diff(scandir($default_directory), $exclude);

      foreach ($module_dir as $filename) {
        $module[] = ['id' => $filename,
                     'text' => str_replace('_ ', '', $filename)
                    ];
      }

      return $module;
    }

/**
 * get all modules directories (fix modules)
 * @param
 * @return $module,values of array
 * @access public
 */
    public function getModuleDirectory() {
      $module = array(array('id' => '0', 'text' => $this->app->getDef('text_select_module_fix')),
                      array('id' => 'export', 'text' => 'Modules Export'),
                      array('id' => 'header_tags', 'text' => 'Modules Header Tags'),
                      array('id' => 'hooks', 'text' => 'Modules Hooks'),
                      array('id' => 'catalog', 'text' => 'Modules Apps Catalog'),
                      array('id' => 'communication', 'text' => 'Modules Apps Communication'),
                      array('id' => 'configuration', 'text' => 'Modules Apps Configuration'),
                      array('id' => 'customer', 'text' => 'Modules Apps Customer'),
                      array('id' => 'marketing', 'text' => 'Modules Apps Marketing'),
                      array('id' => 'order', 'text' => 'Modules Apps Orders'),
                      array('id' => 'total', 'text' => 'Modules Apps Orders Total'),
                      array('id' => 'payment', 'text' => 'Modules Apps Payment'),
                      array('id' => 'listing', 'text' => 'Modules Apps Products listing'),
                      array('id' => 'report', 'text' => 'Modules Apps Report'),
                      array('id' => 'shipping', 'text' => 'Modules Apps Shipping'),
                      array('id' => 'social', 'text' => 'Modules Apps Social network'),
                      array('id' => 'tools', 'text' => 'Modules Apps Tools'),
                      array('id' => 'service', 'text' => 'Modules Apps Web service'),
                      array('id' => 'other', 'text' => 'Modules Apps Others'),
                      array('id' => 'bookmarks', 'text' => 'Modules Social Bookmarks'),
                      array('id' => 'template', 'text' => 'Template Design'),
                     );

      return $module;
    }

//*************************************************************
// Limit
//*************************************************************

/*
 * display a message if the limited is not accepted by github
 * @param
 * @return  string $message, if the limited is not accepted by github
 * @access public
 */
/*
    public function getSearchLimit() {
      $searchLimit = $this->github->api('rate_limit')->getSearchLimit();

      return $searchLimit;
    }

    public function getRateLimits() {
      $rateLimits = $this->github->api('rate_limit')->getRateLimits();

      return $rateLimits;
    }

    public function getCoreLimit() {
      $CoreLimit = $this->github->api('rate_limit')->getCoreLimit();

      return $CoreLimit;
    }
*/
//*************************************************************
// Cache
//*************************************************************

/*
 * Unlik the cache if the delay is passed
 * @param $module_name name of the module
 * @return  $file, file information
 * @access private
 */
    private function getCheckDateCache($module_name) {
      $life = 864000; // 1 month

      if (is_file($this->cacheGithub . $module_name)) {
        $cache_file = $this->cacheGithub . $module_name;
        $count = filemtime($cache_file) > (time() - $life );

        if ($count < 1 && filesize($cache_file)) {
          unlink($this->cacheGithub . $module_name);
        }
      } elseif (is_file($this->cacheGithubTemp . $module_name)) {
        $cache_file = $this->cacheGithubTemp . $module_name;
        $count = filemtime($cache_file) > (time() - $life );

        if ($count < 1 && filesize($cache_file)) {
          unlink($this->cacheGithubTemp . $module_name);
        }
      }
    }

/*
 * Check if cache exit else create
 * @param $module_name name of the module
 * @return  $file, file information
 * @access private
 */
    public function getCheckCacheFile($module_name) {
      if (!empty($module_name)) {

          $this->getCheckDateCache($module_name);

          if (is_file($this->cacheGithub . $module_name)) {
            $result = 'cacheGithub';
          } elseif (is_file($this->cacheGithubTemp . $module_name)) {
            $result = 'cacheGithubTemp';
          } else {
            $file_name = $module_name;
            $file_array = explode ('.', $file_name);
            $extension = count($file_array) - 1;
            $filename = substr($file_name,0,strlen($file_name) - strlen ($file_array[$extension])-1);

            $content_json_file = @file_get_contents($this->getGithubRepo() . $filename . '/contents/'  . $this->ModuleInfosJson  . '/' . $module_name . '?ref=master', true, $this->context);
            $content_download_file = json_decode($content_json_file);

            $file_link = $content_download_file->download_url;

            $file_json_content = @file_get_contents($file_link, true, $this->context);

            if (!empty($file_link)) {
              $headers = get_headers($file_link, true);

              if ( isset($headers['Content-Length']) ) {
                @file_put_contents($this->cacheGithubTemp . '/' . $module_name, $file_json_content);
              }

              $result = false;
            } else {
              $result = true;
            }
          }

        return $result;
      }
    }

/*
 * get the json file infromation about temporary directory
 * @param $module_name name of the module
 * @return  $file, file information
 * @access public
 */
    public function getCacheFileTemp($module_name) {
      $check = $this->getCheckCacheFile($module_name);

      if ($check == 'cacheGithubTemp') {
        $file = @file_get_contents($this->cacheGithubTemp . $module_name, true, $this->context);
        $file = json_decode($file);
      }

      return $file;
    }

/*
 * get the json file information about file installed
 * @param $module_name name of the module
 * @return  $file, file information
 * @access public
 */
    public function getCacheFile($module_name) {
      $check = $this->getCheckCacheFile($module_name);

      if ($check == 'cacheGithub') {
        $file = @file_get_contents($this->cacheGithub . $module_name, true, $this->context);
        $file = json_decode($file);
      }

      return $file;
    }


//*************************************************************
// Limit
//*************************************************************


/*
* take $_POST form about the search
* @param
* @return  $result search item
* @access public
*/
    public function getSearchModule() {
      if (isset($_POST['module_search']) && (!empty($_POST['module_search']))) {
        $result = $_POST['module_search'];
      }

      if (isset($_POST['template_directory']) && $_POST['template_directory'] != '0') {
        $result = $_POST['template_directory'];
      }

      if (isset($_POST['install_module_template_directory']) && $_POST['install_module_template_directory'] != '0') {
        $result = $_POST['install_module_template_directory'];
      }

      if (isset($_POST['install_module_directory']) && $_POST['install_module_directory'] != '0') {
        $result = $_POST['install_module_directory'];
      }

      return $result;
    }

/*
* Search inside Github repo
* @param
* @return array $result : all ement about a github search on item search
* @access public
*/

    public function getSearchInsideRepo($name = null) {

      if (is_null($name)) {
        $search = $this->githubApi . '/search/repositories?q=org%3A' . $this->githubRepoName . '+' . $this->getSearchModule();
        $search_url = @file_get_contents($search, true, $this->setContext()); //content of readme.
      } else {
        $search_url = @file_get_contents($name, true, $this->setContext()); //content of readme.
      }

      $result = json_decode($search_url);

      return $result;
    }

/*
* Cout the total research
* @param
* @return int $count  count of total research
* @access public
*/
    public function getSearchTotalCount() {
      $result = $this->getSearchInsideRepo();
      $count = $result->total_count;

      return $count;
    }

/*
* getGithubRepo : Gisthub repo
* @param
* @return string  $url url about the github repo
 * @access public
*/
    private function getGithubRepo() {
      $url = $this->githubApi . '/' . $this->githubRepo . '/' . $this->githubRepoName . '/';
      return $url;
    }

/**
 * get the version if exist on local
 * @param $module_name, string, module name
 * @return $version, string module verison
 * @access public
 */
    public function getJsonModuleLocalVersion($module_name) {
      if (is_file($this->cacheGithub . $module_name . '.json')) {
        $json = file_get_contents($this->cacheGithub . $module_name . '.json', true, $this->context);
        $result = json_decode($json);
        $version = $result->version;
      } elseif (is_file($this->cacheGithubTemp . $module_name . '.json')) {
        $json = file_get_contents($this->cacheGithubTemp . $module_name . '.json', true, $this->context);
        $result = json_decode($json);
        $version = $result->version;
      } else {
        $version = -1;
      }

      return $version;
    }

//******************************************
// Module
//******************************************
//
/**
 * get the json of module content repository
 * @param $module_name, string, module name
 * @return $result,values of array
 * @access public
 */
    public function getJsonRepoContentInformationModule($module_name) {
      $json = @file_get_contents($this->getGithubRepo() . $module_name . '/contents/'  . $this->ModuleInfosJson  . '?ref=master', true, $this->context );
      $result = json_decode($json);

//include cache in Github Tmp
      $this->getCacheFile($module_name);

      return $result;
    }

/**
 * Github module download
 * @param $module_name, string, module name
 * @return $url, json element
 * @access public
 */

    public function getJsonModuleInformaton($url) {
      $json = @file_get_contents($url, true, $this->setContext() );
      $result_content_module = json_decode($json);

      return $result_content_module;
    }

/**
 * Github module download
 * @param $module_name, string, module name
 * @return $url, link to download the module
 * @access public
 */
    public function getModuleMasterArchive($module_name) {
      if (!empty($module_name) || !is_null($module_name)) {
        $url = HTML::sanitize($_POST['githubLink']);

        if (!empty($url)) {
          $authentication = @file_get_contents($url);

          file_put_contents($this->saveFileFromGithub . '/' . $module_name .'.zip', $authentication);
        } else {
          $this->app->rediret('Upgrade');
        }
      }
    }

/**
 * Extract ClicShopping Core Zip to install inside ClicShopping
 * @param $file, file to exptract
 * @access public
 */
    public function getInstallModuleTemplate($file) {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if ($this->checkDirectoryOnlineUpdate() === true) {
        $this->getCloseOpenStore('true');

        $this->getExtractZip($this->saveFileFromGithub . '/' . $file . '.zip', $this->saveFileFromGithub);

        if (is_dir($this->saveFileFromGithub . '/' . $file . '-master')) {
// copy json in cache
          $json_source = $this->saveFileFromGithub . '/' . $file . '-master/' . $this->ModuleInfosJson . '/';
          $json_destination = $this->cacheGithub;
          @ModuleDownload::smartCopy($json_source, $json_destination);

// copy files their directories
          $source = $this->saveFileFromGithub . '/' . $file . '-master';
          $dest_default_template = CLICSHOPPING::getConfig('dir_root', 'Shop');
          @ModuleDownload::smartCopy($source, $dest_default_template);

// copy in the current theme used
//          $dest_template = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'template/' . SITE_THEMA;
//          ModuleUpload::smartCopy($source, $dest_template);


          if (isset($source)) {
            @ModuleDownload::removeDirectory($source);
          }

          $CLICSHOPPING_MessageStack->add($this->app->getDef('success_files_installed'), 'success', 'header');
        } else {
          $CLICSHOPPING_MessageStack->add($this->app->getDef('error_file_not_installed'), 'error', 'header');
        }

        $this->getCloseOpenStore('false');
      }

      $this->app->redirect('CoreUpgrade');
    }


/**
 * Extract ClicShopping Core Zip to install inside ClicShopping
 * @param $file, rfile to exptract
 * @return $
 * @access public
 */

    public function getInstallModuleFixe($file) {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if ($this->checkDirectoryOnlineUpdate() === true) {
        $this->getCloseOpenStore('true');

        $this->getExtractZip($this->saveFileFromGithub . '/' . $file . '.zip', $this->saveFileFromGithub);

        if (is_dir($this->saveFileFromGithub . '/' . $file. '-master')) {
// copy json in cache
          $json_source = $this->saveFileFromGithub . '/' . $file . '-master/' . $this->ModuleInfosJson . '/';
          $json_destination = $this->cacheGithub;
          @ModuleDownload::smartCopy($json_source, $json_destination);

// copy files their directories
          $source = $this->saveFileFromGithub . '/' . $file . '-master';
          $dest = CLICSHOPPING::getConfig('dir_root', 'Shop');

          @ModuleDownload::smartCopy($source, $dest);

          if (isset($source)) {
            @ModuleDownload::removeDirectory($source);
          }

          $CLICSHOPPING_MessageStack->add($this->app->getDef('success_file_installed'), 'success', 'header');
        } else {
          $CLICSHOPPING_MessageStack->add($this->app->getDef('error_file_not_installed'), 'error', 'header');
        }

        $this->getCloseOpenStore('false');
      }

      $this->app->redirect('ModuleInstall');
    }
  }