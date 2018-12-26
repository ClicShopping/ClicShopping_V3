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

  namespace ClicShopping\Apps\Tools\Backup\Sites\ClicShoppingAdmin\Pages\Home\Actions\Backup;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class RestoreNow extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct() {
      $this->app = Registry::get('Backup');
    }


    public function execute() {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      set_time_limit(0);

      $backup_directory = CLICSHOPPING::BASE_DIR . 'Work/Backups/';
      $read_from = $_GET['file'];

      if (is_file($backup_directory . $_GET['file'])) {
        $restore_file = $backup_directory . $_GET['file'];
        $extension = substr($_GET['file'], -3);

        if ( ($extension == 'sql') || ($extension == '.gz') || ($extension == 'zip') ) {
          switch ($extension) {
            case 'sql':
              $restore_from = $restore_file;
              $remove_raw = false;
              break;
            case '.gz':
              $restore_from = substr($restore_file, 0, -3);
              exec(LOCAL_EXE_GUNZIP . ' ' . $restore_file . ' -c > ' . $restore_from);
              $remove_raw = true;
              break;
            case 'zip':
              $restore_from = substr($restore_file, 0, -4);
              exec(LOCAL_EXE_UNZIP . ' ' . $restore_file . ' -d ' . $backup_directory);
              $remove_raw = true;
          }

          if (isset($restore_from) && is_file($restore_from) && (filesize($restore_from) > 15000)) {
            $fd = fopen($restore_from, 'rb');
            $restore_query = fread($fd, filesize($restore_from));
            fclose($fd);
          }
        }
      }

      if (isset($restore_query)) {
        $sql_array = [];
        $drop_table_names = [];
        $sql_length = strlen($restore_query);
        $pos = strpos($restore_query, ';');

        for ($i=$pos; $i<$sql_length; $i++) {
          if ($restore_query[0] == '#') {
            $restore_query = ltrim(substr($restore_query, strpos($restore_query, "\n")));
            $sql_length = strlen($restore_query);
            $i = strpos($restore_query, ';')-1;
            continue;
          }
          if ($restore_query[($i+1)] == "\n") {
            for ($j=($i+2); $j<$sql_length; $j++) {
              if (trim($restore_query[$j]) != '') {
                $next = substr($restore_query, $j, 6);
                if ($next[0] == '#') {
// find out where the break position is so we can remove this line (#comment line)
                  for ($k=$j; $k<$sql_length; $k++) {
                    if ($restore_query[$k] == "\n") break;
                  }
                  $query = substr($restore_query, 0, $i+1);
                  $restore_query = substr($restore_query, $k);
// join the query before the comment appeared, with the rest of the dump
                  $restore_query = $query . $restore_query;
                  $sql_length = strlen($restore_query);
                  $i = strpos($restore_query, ';')-1;
                  continue 2;
                }
                break;
              }
            }
            if ($next == '') { // get the last insert query
              $next = 'insert';
            }
            if ( (preg_match('/create/i', $next)) || (preg_match('/insert/i', $next)) || (preg_match('/drop t/i', $next)) ) {
              $query = substr($restore_query, 0, $i);

              $next = '';
              $sql_array[] = $query;
              $restore_query = ltrim(substr($restore_query, $i+1));
              $sql_length = strlen($restore_query);
              $i = strpos($restore_query, ';')-1;

              if (preg_match('/^create*/i', $query)) {
                $table_name = trim(substr($query, stripos($query, 'table ')+6));
                $table_name = substr($table_name, 0, strpos($table_name, ' '));

                $drop_table_names[] = $table_name;
              }
            }
          }
        }

        $this->app->db->exec('drop table if exists ' . implode(', ', $drop_table_names));

        for ($i=0, $n=count($sql_array); $i<$n; $i++) {
          $this->app->db->exec($sql_array[$i]);
        }

        session_write_close();

        $this->app->db->delete('whos_online');
        $this->app->db->delete('sessions');

        $this->app->db->delete('configuration', ['configuration_key' => 'DB_LAST_RESTORE']);

        $this->app->db->save('configuration', [
                                                'configuration_title' => 'Last Database Restore',
                                                'configuration_key' => 'DB_LAST_RESTORE',
                                                'configuration_value' => $read_from,
                                                'configuration_description' => 'Last database restore file',
                                                'configuration_group_id' => '6',
                                                'date_added' => 'now()'
                                              ]
                            );

        if (isset($remove_raw) && ($remove_raw === true)) {
          unlink($restore_from);
        }

        $CLICSHOPPING_MessageStack->add($this->app->getDef('success_database_restored'), 'success');
      }

      $this->app->redirect('Backup');
    }
  }