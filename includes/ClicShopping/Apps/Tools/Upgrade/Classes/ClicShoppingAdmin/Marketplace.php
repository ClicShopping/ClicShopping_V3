<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\Upgrade\Classes\ClicShoppingAdmin;

use ClicShopping\OM\Registry;
use function count;
use function is_array;

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
   * Retrieves an OAuth access token by communicating with the API server.
   * Validates required credentials before making the request and handles error responses.
   *
   * @return string|null Returns the access token on success, or null if an error occurs.
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
   * Retrieves the session token. If a token does not already exist in the session, generates a new token and stores it in the session.
   *
   * @return string The session token.
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
   * Fetches a response from the specified API endpoint.
   *
   * @param string $communityUrl The base URL of the community API.
   * @param string $endpoint The specific API endpoint to access.
   * @return array|bool Returns the API response decoded as an associative array, or true on error or invalid token.
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
   * Retrieves all categories from the API endpoint.
   *
   * This method interacts with an external API to fetch a list of categories. If there is an issue with
   * the API connection, an error message is added to the message stack, and the user is redirected
   * to the "Marketplace" page. Otherwise, the fetched data is returned.
   *
   * @return array|string Returns the list of categories on success, or redirects on API connection error.
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
   * Retrieves and saves categories into the database if none exist.
   *
   * @return bool Returns true if categories were successfully saved into the database, false otherwise.
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
   * Fetches files from a remote community URL using a specific endpoint and stores them in the database.
   *
   * The method checks if any files already exist in the 'marketplace_files' table.
   * If no files exist, it retrieves data from the remote source, parses the result,
   * and inserts it into the database. Each file includes details such as title, description,
   * category, author information, and additional metadata.
   *
   * @return bool Returns true if files are successfully fetched and saved into the database, or false if files already exist.
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
   * Fetches file information from an external source and stores it into the database
   * if it is not already present. Returns whether new file information was added.
   *
   * @param int $id The file ID to look up and potentially fetch information for.
   * @return bool Returns true if new file information was added, false otherwise.
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
   * Builds a hierarchical tree structure for labels/categories starting from a specified parent node.
   *
   * @param int|string $parent_id The parent ID from which to start building the tree. Defaults to '0'.
   * @param string $spacing The spacing string used to indent child labels. Defaults to an empty string.
   * @param array|string $exclude The ID or IDs to exclude from the tree. Defaults to an empty string.
   * @param array|string $category_tree_array The current state of the category tree array during recursion. Defaults to an empty string.
   * @param bool $include_itself Whether to include the parent node itself in the output. Defaults to false.
   * @return array The hierarchical tree of labels/categories.
   */
  public function getLabelTree(int|string $parent_id = '0', string $spacing = '', array|string $exclude = '', array|string $category_tree_array = '', bool $include_itself = false): array
  {
    if (!is_array($category_tree_array)) {
      $category_tree_array = [];
    }

    if ((count($category_tree_array) < 1) && ($exclude != '0')) {
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
   * Executes a cron job to clean up marketplace-related database tables.
   *
   * Deletes all records from the following tables:
   * - marketplace_categories
   * - marketplace_files
   * - marketplace_file_informations
   *
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