<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Tools\Backup\Sites\ClicShoppingAdmin\Pages\Home\Actions\Backup;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\ObjectInfo;

  class BackupNow extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('Backup');
    }


    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

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
        '# Backup Date: ' . date($this->app->getDef('php_date_time_format')) . "\n\n";
      fputs($fp, $schema);

      $Qtables = $this->app->db->get(['INFORMATION_SCHEMA.TABLES t',
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

        $Qfields = $this->app->db->query('show fields from ' . $table);

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

        $Qkeys = $this->app->db->query('show keys from ' . $table);

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

          $columns = implode($info['columns'], ', ');

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
          $Qrows = $this->app->db->get($table, $table_list, null, null, null, null, ['prefix_tables' => false]);

          while ($Qrows->fetch()) {
            $schema = 'insert into ' . $table . ' (' . implode(', ', $table_list) . ') values (';

            foreach ($table_list as $i) {
              if (!$Qrows->hasValue($i)) {
                $schema .= 'NULL, ';
              } elseif (!is_null($Qrows->value($i))) {
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

      if (isset($_POST['download']) && ($_POST['download'] == 'yes')) {
        switch ($_POST['compress']) {
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
        switch ($_POST['compress']) {
          case 'gzip':
            exec(LOCAL_EXE_GZIP . ' ' . $backup_directory . $backup_file);
            break;
          case 'zip':
            exec(LOCAL_EXE_ZIP . ' -j ' . $backup_directory . $backup_file . '.zip ' . $backup_directory . $backup_file);
            unlink($backup_directory . $backup_file);
        }

        $CLICSHOPPING_MessageStack->add($this->app->getDef('success_database_saved'), 'success');
      }

      $this->app->redirect('Backup');
    }
  }