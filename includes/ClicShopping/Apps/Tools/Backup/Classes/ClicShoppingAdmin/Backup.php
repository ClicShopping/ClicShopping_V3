<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\Backup\Classes\ClicShoppingAdmin;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;
use function defined;
use function strlen;

class Backup
{
  /**
   * Creates a backup of the current database and stores it in the specified backup directory.
   * The backup process includes the structure and content of all tables in the database.
   * Optionally compresses the resulting file and provides a download option if requested.
   *
   * @return void
   */
  public static function backupNow(): void
  {
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_Db = Registry::get('Db');

    set_time_limit(0);

    $backup_directory = CLICSHOPPING::BASE_DIR . 'Work/Backups/';
    $backup_file = 'db_' . CLICSHOPPING::getConfig('db_database') . '-' . date('YmdHis') . '.sql';

    $fp = fopen($backup_directory . $backup_file, 'w');

    $schema = '# ClicShopping, E-Commerce Solutions' . "\n" .
      '# https://www.clicshopping.org' . "\n" .
      '#' . "\n" .
      '# Database Backup For ' . STORE_NAME . "\n" .
      '# Copyright (c) ' . date('Y') . ' ' . STORE_OWNER . "\n" .
      '#' . "\n" .
      '# Database: ' . CLICSHOPPING::getConfig('db_database') . "\n" .
      '# Database Server: ' . CLICSHOPPING::getConfig('db_server') . "\n" .
      '#' . "\n" .
      '# Backup Date: ' . date('m/d/Y H:i:s') . "\n\n";
    fputs($fp, $schema);

    $Qtables = $CLICSHOPPING_Db->get(['INFORMATION_SCHEMA.TABLES t',
      'INFORMATION_SCHEMA.COLLATION_CHARACTER_SET_APPLICABILITY ccsa'
    ],
      ['t.TABLE_NAME',
        't.ENGINE',
        't.TABLE_COLLATION',
        'ccsa.CHARACTER_SET_NAME'
      ],
      ['t.TABLE_SCHEMA' => CLICSHOPPING::getConfig('db_database'),
        't.TABLE_COLLATION' => [
          'rel' => 'ccsa.COLLATION_NAME'
        ]
      ], null, null, null,
      ['prefix_tables' => false]
    );

    while ($Qtables->fetch()) {
      $table = $Qtables->value('TABLE_NAME');

      $schema = 'drop table if exists ' . $table . ';' . "\n" .
        'create table ' . $table . ' (' . "\n";

      $table_list = [];

      $Qfields = $CLICSHOPPING_Db->query('show fields from ' . $table);

      while ($Qfields->fetch()) {
        $table_list[] = $Qfields->value('Field');

        $schema .= '  ' . $Qfields->value('Field') . ' ' . $Qfields->value('Type');

        if (strlen($Qfields->value('Default')) > 0) $schema .= ' default \'' . $Qfields->value('Default') . '\'';

        if ($Qfields->value('Null') != 'YES') $schema .= ' not null';

        if (strlen($Qfields->value('Extra')) > 0) $schema .= ' ' . $Qfields->value('Extra');

        $schema .= ',' . "\n";
      }

      $schema = preg_replace("/,\n$/", '', $schema);

      // add the keys
      $index = [];

      $Qkeys = $CLICSHOPPING_Db->query('show keys from ' . $table);

      while ($Qkeys->fetch()) {
        $kname = $Qkeys->value('Key_name');

        if (!isset($index[$kname])) {
          $index[$kname] = array('unique' => $Qkeys->valueInt('Non_unique') === 0,
            'fulltext' => ($Qkeys->value('Index_type') == 'FULLTEXT' ? '1' : '0'),
            'columns' => array());
        }

        $index[$kname]['columns'][] = $Qkeys->value('Column_name');
      }

      foreach ($index as $kname => $info) {
        $schema .= ',' . "\n";

        $columns = implode(', ', $info['columns']);

        if ($kname == 'PRIMARY') {
          $schema .= '  PRIMARY KEY (' . $columns . ')';
        } elseif ($info['fulltext'] == '1') {
          $schema .= '  FULLTEXT ' . $kname . ' (' . $columns . ')';
        } elseif ($info['unique']) {
          $schema .= '  UNIQUE ' . $kname . ' (' . $columns . ')';
        } else {
          $schema .= '  KEY ' . $kname . ' (' . $columns . ')';
        }
      }

      $schema .= "\n" . ') ENGINE=' . $Qtables->value('ENGINE') . ' CHARACTER SET ' . $Qtables->value('CHARACTER_SET_NAME') . ' COLLATE ' . $Qtables->value('TABLE_COLLATION') . ';' . "\n\n";

      fputs($fp, $schema);

      // dump the data
      if (($table != CLICSHOPPING::getConfig('db_table_prefix') . 'sessions') && ($table != CLICSHOPPING::getConfig('db_table_prefix') . 'whos_online')) {
        $Qrows = $CLICSHOPPING_Db->get($table, $table_list, null, null, null, null, ['prefix_tables' => false]);

        while ($Qrows->fetch()) {
          $schema = 'insert into ' . $table . ' (' . implode(', ', $table_list) . ') values (';

          foreach ($table_list as $i) {
            if (!$Qrows->hasValue($i)) {
              $schema .= 'NULL, ';
            } elseif (!\is_null($Qrows->value($i))) {
              $row = addslashes($Qrows->value($i));
              $row = preg_replace("/\n#/", "\n" . '\#', $row);

              $schema .= '\'' . $row . '\', ';
            } else {
              $schema .= '\'\', ';
            }
          }

          $schema = preg_replace('/, $/', '', $schema) . ');' . "\n";
          fputs($fp, $schema);
        }
      }
    }

    fclose($fp);

    if (isset($_POST['compress'])) {
      $compress = $_POST['compress'];
    } else {
      $compress = 'gzip';
    }

    if (!defined('LOCAL_EXE_GZIP')) {
      define('LOCAL_EXE_GZIP', 'gzip');
    }

    if (isset($_POST['download'])) {
      switch ($compress) {
        case 'gzip':
          exec(LOCAL_EXE_GZIP . ' ' . $backup_directory . $backup_file);
          $backup_file .= '.gz';
          break;
        case 'zip':
          exec(LOCAL_EXE_ZIP . ' -j ' . $backup_directory . $backup_file . '.zip ' . $backup_directory . $backup_file);
          unlink($backup_directory . $backup_file);
          $backup_file .= '.zip';
      }

      header('Content-type: application/x-octet-stream');
      header('Content-disposition: attachment; filename=' . $backup_file);

      readfile($backup_directory . $backup_file);
      unlink($backup_directory . $backup_file);

      exit;
    } else {
      switch ($compress) {
        case 'gzip':
          exec(LOCAL_EXE_GZIP . ' ' . $backup_directory . $backup_file);
          break;
        case 'zip':
          exec(LOCAL_EXE_ZIP . ' -j ' . $backup_directory . $backup_file . '.zip ' . $backup_directory . $backup_file);
          unlink($backup_directory . $backup_file);
      }

      $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('success_database_saved'), 'success');
    }
  }
}