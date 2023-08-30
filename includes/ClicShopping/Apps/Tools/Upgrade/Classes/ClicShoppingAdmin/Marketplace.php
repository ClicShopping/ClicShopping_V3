<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


//https://aaronparecki.com/oauth-2-simplified/#web-server-apps
//https://invisioncommunity.com/developers/rest-api/index/
//https://www.invisionboard.fr/forums/topic/65167-43-sign-in-from-other-sites-using-oauth/
//https://invisioncommunity.com/search/?&q=oauth&page=5&quick=1&search_and_or=or&sortby=relevancy
//https://backstage.forgerock.com/knowledge/kb/article/a45882528

namespace ClicShopping\Apps\Tools\Upgrade\Classes\ClicShoppingAdmin;

use ClicShopping\OM\Registry;

class Marketplace
{
  private string $endpointFiles;
  private string $endpointCategories;
  private string $communityUrl;
  private mixed $upgrade;

  private mixed $messageStack;

  public function __construct()
  {
    $this->messageStack = Registry::get('MessageStack');
    $this->upgrade = Registry::get('Upgrade');

    $this->endpointFiles = '/downloads/files?perPage=300';
    $this->endpointCategories = '/downloads/categories?perPage=150';
    $this->communityUrl = 'https://www.clicshopping.org/forum/';
  }

  /**
   * @return mixed
   */
  public function getToken()
  {
    if (empty(CLICSHOPPING_APP_UPGRADE_UP_USERNAME) || empty(CLICSHOPPING_APP_UPGRADE_UP_PASSWORD)) {
      $this->messageStack->add($this->upgrade->getDef('text_error_api_connection'), 'error');
      $this->upgrade->redirect('Marketplace');
    }

    $text = CLICSHOPPING_APP_UPGRADE_UP_USERNAME;
    if (stripos($text, "@") === false) {
      $this->messageStack->add($this->upgrade->getDef('text_error_username'), 'error');
      $this->upgrade->redirect('Upgrade&Configure');
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->communityUrl . 'oauth/token/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);

    $array = [
      'grant_type' => 'password',
      'scope' => 'profile',
      'client_id' => '88e30f6be7dbdc3b0d90e7bb0e20007c',
      'client_secret' => '98ca912627fafe4eb5e044b780150534c284f91fb41568c9',
      'username' => CLICSHOPPING_APP_UPGRADE_UP_USERNAME,
      'password' => CLICSHOPPING_APP_UPGRADE_UP_PASSWORD,
    ];

    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($array));

    // execute cURL
    $result = curl_exec($ch);

    curl_close($ch);

    // decode JSON response
    $response = json_decode($result);

    if (isset($response->error)) {
      $this->messageStack->add($this->upgrade->getDef('text_error_api_connection'), 'error');
      $this->upgrade->redirect('Marketplace');
    } else {
      return $response->access_token;
    }
  }

  /**
   * @return string
   */
  public function getSessionToken()
  {
    if (!isset($_SESSION['token'])) {
      $_SESSION['token'] = $this->getToken();
      $token = $_SESSION['token'];
    } else {
      $token = $_SESSION['token'];
    }

    return $token;
  }

  /**
   * @param string $communityUrl
   * @param string $endpoint
   * @return bool|mixed|string
   */
  public function getResponse($communityUrl, $endpoint)
  {
    $token = $this->getSessionToken();

    if ($token !== null) {
      $curl = curl_init($communityUrl . 'api' . $endpoint);

      curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_USERAGENT => "MyUserAgent/1.0",
        CURLOPT_HTTPHEADER => array('Authorization: Bearer ' . $token),
      ));

      $response = curl_exec($curl);

      $result = json_decode($response, true);

      if (isset($result['errorCode'])) {
        $_SESSION['error'] = $result['errorMessage'];

        if ($result['errorMessage'] == 'REVOKED_ACCESS_TOKEN') {
          unset($_SESSION['token']);
        }

        return true;
      } else {
        return $result;
      }
    } else {
      return true;
    }
  }

  /******************************************************************
   * Categories
   */

  /**
   * @return bool|mixed|string
   */
  public function getAllCategories()
  {
    $result = $this->getResponse($this->communityUrl, $this->endpointCategories);

    if ($result === true) {
      $this->messageStack->add($this->upgrade->getDef('text_error_api_connection') . ': ' . $_SESSION['error'], 'error');
      unset($_SESSION['error']);
      return $this->upgrade->redirect('Marketplace');
    } else {
      return $result;
    }
  }

  /**
   * @return bool
   */
  public function getCategories(): bool
  {
    $result = $this->getAllCategories();

    $check = $this->upgrade->db->get('marketplace_categories', 'categories_id');

    if ($check->rowCount() == 0) {
      $i = 1;

      if (is_array($result)) {
        foreach ($result as $value) {
          if (is_array($value)) {
            foreach ($value as $categories) {
              $sql_data_array = [
                'categories_id' => (int)$categories['id'],
                'parent_id' => (int)$categories['parentId'],
                'categories_name' => $categories['name'],
                'url' => $categories['url'],
                'date_added' => 'now()',
              ];

              $insert_sql_data = [
                'id' => $i++,
              ];

              $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

              $this->upgrade->db->save('marketplace_categories', $sql_data_array);
            }
          }
        }
      }

      return true;
    }

    return false;
  }

  /**
   * @return bool
   */
  public function getFiles(): bool
  {
    $result = $this->getResponse($this->communityUrl, $this->endpointFiles);

    $check = $this->upgrade->db->get('marketplace_files', 'file_id');

    if ($check->rowCount() == 0) {
      $i = 1;

      if (is_array($result)) {
        foreach ($result as $value) {
          if (is_array($value)) {
            foreach ($value as $file) {
              $description = html_entity_decode($file['description']);

              $sql_data_array = [
                'file_id' => (int)$file['id'],
                'file_categories_id' => (int)$file['category']['id'],
                'file_name' => $file['title'],
                'file_url' => $file['url'],
                'file_description' => $description,
                'file_author' => $file['author']['name'],
                'file_photo_url' => $file['author']['photoUrl'],
                'file_profil_url' => $file['author']['profileUrl'],
                'date_added' => 'now()',
              ];

              $insert_sql_data = [
                'id' => $i++,
              ];

              $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

              $this->upgrade->db->save('marketplace_files', $sql_data_array);
            }
          }
        }
      }
      return true;
    }

    return false;
  }

  /**
   * @param int $id
   * @return bool
   */
  public function getFilesInformations(int $id): bool
  {
    $check = $this->upgrade->db->get('marketplace_file_informations', 'id', ['file_id' => $id]);

//    /downloads/files/{id}/download
    if ($check->rowCount() == 0) {
      $result = $this->getResponse($this->communityUrl, '/downloads/files?id=' . $id . '&download&perPage=300');

      $i = 1;

      if (is_array($result)) {
        foreach ($result as $value) {
          if (is_array($value)) {
            foreach ($value as $file) {
              if (!empty($file['prices']['EUR'])) {
                $prices = $file['prices']['EUR'];
              } else {
                $prices = 0.00;
              }

              if (!empty($file['screenshotsThumbnails'][0]['url'])) {
                $screenshot = $file['screenshotsThumbnails'][0]['url'];
              } else {
                $screenshot = '';
              }

              if (!empty($file['url'])) {
                $url_download = $file['url'];
              } else {
                $url_download = '';
              }


              if ($file['isPaid'] === true) {
                $url_download = '';
              }

              $sql_data_array = [
                'file_id' => (int)$file['id'],
                'file_name' => $file['title'],
                'file_version' => $file['version'],
                'file_downloads' => $file['downloads'],
                'file_rating' => (int)$file['rating'],
                'file_prices' => $prices,
                'file_date_added' => 'now()',
                'file_url_screenshot' => $screenshot,
                'file_url_download' => $url_download,
              ];

              $insert_sql_data = [
                'id' => $i++,
              ];

              $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

              $this->upgrade->db->save('marketplace_file_informations', $sql_data_array);
            }
          }
        }
      }

      return true;
    } else {
      return false;
    }
  }

  /**
   * category tree
   * @param int|string $parent_id
   * @param string $spacing
   * @param array|string $exclude
   * @param array|string $category_tree_array
   * @param bool $include_itself
   * @return array
   */
  public function getLabelTree(int|string $parent_id = '0', string $spacing = '', array|string $exclude = '', array|string $category_tree_array = '', bool $include_itself = false): array
  {
    if (!\is_array($category_tree_array)) {
      $category_tree_array = [];
    }

    if ((\count($category_tree_array) < 1) && ($exclude != '0')) {
      $category_tree_array[] = [
        'id' => '0',
        'text' => $this->upgrade->getDef('text_top')
      ];
    }

    if ($include_itself) {
      $Qcategory = $this->upgrade->get('marketplace_categories', 'categories_name', ['id' => (int)$parent_id]);

      $category_tree_array[] = [
        'id' => $parent_id,
        'text' => $Qcategory->value('categories_name')
      ];
    }

    $Qcategories = $this->upgrade->db->prepare('select categories_id,
                                                       categories_name,
                                                       parent_id
                                                from :table_marketplace_categories
                                                where parent_id = :parent_id
                                                order by sort_order, categories_name
                                               ');

    $Qcategories->bindInt(':parent_id', $parent_id);
    $Qcategories->execute();

    while ($Qcategories->fetch()) {
      if ($exclude != $Qcategories->valueInt('categories_id'))
        $category_tree_array[] = [
          'id' => $Qcategories->valueInt('categories_id'),
          'text' => $spacing . $Qcategories->value('categories_name')
        ];

      $category_tree_array = $this->getLabelTree($Qcategories->valueInt('categories_id'), $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array);
    }

    return $category_tree_array;
  }

  /**
   * @return void
   */
  public static function Cronjob(): void
  {
    $CLICSHOPPING_db = Registry::get('Db');

    $CLICSHOPPING_db->delete('marketplace_categories ');
    $CLICSHOPPING_db->delete('marketplace_files');
    $CLICSHOPPING_db->delete('marketplace_file_informations');
  }
}