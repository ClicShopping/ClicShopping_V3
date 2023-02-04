<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Tools\Upgrade\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\FileSystem;

  use ClicShopping\Apps\Tools\Upgrade\Upgrade as UpgradeApp;

  use ClicShopping\Sites\ClicShoppingAdmin\ModuleDownload;

  class ExtractFile
  {
    private mixed $app;
    private string $saveFileFromGithub;
    private string $cacheGithub;
    private string $cacheGithubTemp;
    private string $ModuleInfosJson;

    public function __construct()
    {
      if (!Registry::exists('Upgrade')) {
        Registry::set('Upgrade', new UpgradeApp());
      }

      $this->app = Registry::get('Upgrade');
      $this->messageStack = Registry::get('MessageStack');


      $this->saveFileFromGithub = CLICSHOPPING::BASE_DIR . 'Work/Temp';
      $this->cacheGithub = CLICSHOPPING::BASE_DIR . 'Work/Cache/Marketplace/';
      $this->cacheGithubTemp = CLICSHOPPING::BASE_DIR . 'Work/Cache/Marketplace/Temp/';
      $this->ModuleInfosJson = 'ModuleInfosJson';
    }

   /**
     * Extract Zip file
     *
     * @param string $source
     * @param string $destination
     * @return bool
     * @uses ZipArchive
     */
    private function getExtractZip(string $source, string $destination) :bool
    {
      $zip = new \ZipArchive;

      if ($zip->open($source) === true) {
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
    */
    public function getCloseOpenStore(string $value): void
    {
      if (\defined('STORE_OFFLINE')) {
        $Qupdate = $this->app->db->prepare('update :table_configuration
                                            set configuration_value = :configuration_value,
                                            last_modified = now()
                                            where configuration_key = :configuration_key
                                           ');
        $Qupdate->bindValue(':configuration_value', $value);
        $Qupdate->bindValue(':configuration_key', 'STORE_OFFLINE');
        $Qupdate->execute();
      }
    }

    /**
     * Check directory if exist or not
     * @param
     * @return Boolean true / false
     */
    public function checkDirectory(): bool
    {
      if (!is_dir($this->saveFileFromGithub)) {
        if (!mkdir($concurrentDirectory = $this->saveFileFromGithub, 0777, true) && !is_dir($concurrentDirectory)) {
          throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
        return true;
      } elseif (FileSystem::isWritable($this->saveFileFromGithub)) {
        return true;
      } else {
        return false;
      }

      if (!is_dir($this->cacheGithub)) {
        if (!mkdir($concurrentDirectory = $this->cacheGithub, 0777, true) && !is_dir($concurrentDirectory)) {
          throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
        return true;
      } elseif (FileSystem::isWritable($this->cacheGithub)) {
        return true;
      } else {
        return false;
      }

      if (!is_dir($this->cacheGithubTemp)) {
        if (!mkdir($concurrentDirectory = $this->cacheGithubTemp, 0777, true) && !is_dir($concurrentDirectory)) {
          throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
        return true;
      } elseif (FileSystem::isWritable($this->cacheGithubTemp)) {
        return true;
      } else {
        return false;
      }
    }

    /**
     * @param string $file_url
     * @return string|void
     */
    public function downloadFile(string $file_url)
    {
      $downloaded_file = file_get_contents($file_url);

      if ($downloaded_file === false) {
        $this->messageStack->add($this->app->getDef('text_file_download_error'), 'error');

        CLICSHOPPING::redirect('Upgrade&Marketplace');
      } else {
        $path_parts = pathinfo($file_url);
        $file_name = $path_parts['filename'] . '.zip';
        $file_name = str_replace('_zip', '', $file_name);

        $filename_localisation = $this->saveFileFromGithub . '/' . $file_name;
        file_put_contents($filename_localisation, $downloaded_file);

        return $filename_localisation;
      }
    }

    /**
     * @param string $filename_localisation
     * @return void
     */
    public function installFiles(string $filename_localisation): void
    {
      $this->getExtractZip($filename_localisation, $this->saveFileFromGithub);

//check if not readme.zip
      $dir = str_replace('.zip', '', $filename_localisation);

      if (is_dir($dir)) {
// copy json in cache
        $json_source = $dir . '/' . $this->ModuleInfosJson . '/';
        $json_destination = $this->cacheGithub;
        
        @ModuleDownload::smartCopy($json_source, $json_destination);

// copy files their directories
        $source = $dir;
        $dest = CLICSHOPPING::getConfig('dir_root', 'Shop');

        @ModuleDownload::smartCopy($source, $dest);

        if (isset($source)) {
          @ModuleDownload::removeDirectory($source);
        }

        $this->messageStack->add($this->app->getDef('success_file_installed'), 'success', 'main');

        $this->app->redirect('MarketplaceSuccess');
      } else {
        $this->messageStack->add($this->app->getDef('error_file_not_installed'), 'error', 'main');
        $this->app->redirect('Marketplace');
      }
    }



    /**
     * @param string $url
     * @return bool|int
     */
    public function getJsonInfo(string $json_file): array|bool
    {
      $cache_directory = CLICSHOPPING::BASE_DIR . 'Work/Cache/Marketplace/';

      if (is_file($cache_directory . $json_file)) {
        $get_json_file = file_get_contents($cache_directory . $json_file, true);
        $result = json_decode($get_json_file);

        return $result;
      } else {
        $this->messageStack->add($this->app->getDef('error_json_file'), 'error', 'main');
        return false;
      }
    }
  }