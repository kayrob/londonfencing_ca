#!/usr/bin/php
<?php
    $migration_test_db = "migration_test";

    define('GREEN_BEGIN', "\033[01;32m");
    define('YELLOW_BEGIN', "\033[01;33m");
    define('RED_BEGIN', "\033[01;31m");
    define('COLOR_END', "\033[00m");

    $config = __DIR__ . DIRECTORY_SEPARATOR . 'db-connection.php';
    if (!is_file($config)) {
        $config = __DIR__ . DIRECTORY_SEPARATOR . 'db-connection.php.dist';
    }

    if (!is_file($config)) {
        die_red("Connection config file not found!");
    }
    require_once $config;

    if ($migration_test_db == MYMYSQL_DB) {
        die_red("$migration_test_db can't be the target database");
    }

    echo_yellow("Using environment " . WEB_ENV . "\n");
    echo_yellow(sprintf("Checking for database '%s'", MYMYSQL_DB));
    $database_result = mysql_query(sprintf("SHOW databases LIKE '%s'", MYMYSQL_DB));
    if (mysql_num_rows($database_result) == 0) {
        $cmd = sprintf("CREATE DATABASE %s", MYMYSQL_DB);
        echo_green($cmd);
        mysql_query($cmd) or die_red(mysql_error());
    }
    mysql_select_db(MYMYSQL_DB);

    $init_history = '';
    echo_yellow("Checking for table `migration_history`\n");
    $migration_result = mysql_query("SHOW TABLES LIKE 'migration_history'") or die_red(mysql_error());
    if (mysql_num_rows($migration_result) == 0) {
        $init_history = "
            CREATE TABLE migration_history (
              migration_nr INTEGER NOT NULL PRIMARY KEY,
              migrated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
            ) ENGINE=InnoDB
        ";
    }

    if (!is_dir('schemas')) {
        mkdir('schemas', 0755);
    }
    if (!is_dir(dirname(__FILE__) . '/dumps')) {
        mkdir(dirname(__FILE__) . '/dumps', 0775);
    }

    $migration_files = array();
    chdir(dirname(__FILE__) . '/schemas');
    $handle = opendir(dirname(__FILE__) . '/schemas');
    if ( ! $handle ) trigger_error("Opening current dir failed.", E_USER_ERROR);
    while (false !== ($file = readdir($handle))) {
        if (preg_match("/([0-9]+)_([_a-zA-Z0-9]*).mysql/", $file) ) {
            $migration_files[] = $file;
        }
    }

    $table_result = mysql_query("SHOW TABLES");
    $num_tables   = mysql_num_rows($table_result);

    if (count($migration_files) == 0 && (($num_tables > 0 && !empty($init_history)) || ($num_tables > 1 && empty($init_history)))) {
        echo_yellow(sprintf("Project seems to be in progress . . . setting %s as 001 schema", MYMYSQL_DB));

        $dump_command = sprintf("mysqldump --routines --user=%s --password=%s --host=%s %s > " . dirname(__FILE__) . "/schemas/001_init.mysql", MYMYSQL_USER, MYMYSQL_PASSWORD, MYMYSQL_HOST, MYMYSQL_DB);
//        echo_green($dump_command);
        system($dump_command, $dump_status);
        if ($dump_status != 0) {
            die_red("Initilization of schema failed");
        }

        if (!empty($init_history)) {
            echo_green($init_history);
            mysql_query($init_history) or die_red(mysql_error());
        }

        $cmd = "INSERT INTO `migration_history` VALUES (1, NOW())";
        echo_green($cmd);
        mysql_query($cmd) or die_red(mysql_error());

        echo_yellow("Checking for subversion");
        if (is_dir('schemas/.svn')) {
            $cmd = "svn add schemas/001_init.mysql";
            echo_green($cmd);
            system($cmd, $status);
            if ($status != 0) {
                die_red("subversion command failed");
            }

            echo_yellow("\nREMEMBER TO COMMIT SOON!");
        }

        die;
    }

    if (count($migration_files) > 0 && $num_tables > 0 && !empty($init_history)) {
        die_red("Your project seems to be in progress and in conflict with the migration process");
    }

    if (!empty($init_history)) {
        echo_green($init_history);
        mysql_query($init_history) or die_red(mysql_error());
    }


// Actual migration

$previous_version = intval(mymysql_select_value("SELECT MAX(migration_nr) FROM migration_history"));

chdir(dirname(__FILE__) . '/schemas');
$handle = opendir(dirname(__FILE__) . '/schemas');
if ( ! $handle ) trigger_error("Opening current dir failed.", E_USER_ERROR);

$migration_files = array();

while (false !== ($file = readdir($handle))) {
  if (preg_match("/([0-9]+)_([_a-zA-Z0-9]*).mysql/", $file) )
  {
    $migration_files[] = $file;
  }
}

closedir($handle);

sort($migration_files);

$dump_created = false;

foreach($migration_files as $filename)
{
  $current_version = intval(mymysql_select_value("SELECT MAX(migration_nr) FROM migration_history"));

  $matches = array();
  preg_match("/([0-9]+)_([_a-zA-Z0-9]*).mysql$/", $filename, $matches);
  $file_version = intval($matches[1]); // file_version is the number of the current migration file

  if ( $file_version == $previous_version ) {
    continue;
//    die_red("Duplicate versioned migration found, fool...");
  }

  if ( $file_version > $current_version )
  {
//    echo_yellow("Making dump of revsion $previous_version.");

    $dump_file = sprintf('../dumps/pre_migration_%s_%s.mysql', WEB_ENV, $file_version);
    if (!$dump_created || WEB_ENV != 'production') {
      echo_yellow("Backing up at version {$current_version}");
      $dump_status = null;
      $dump_command = sprintf("mysqldump --routines --user=%s --password=%s --host=%s %s > $dump_file", MYMYSQL_USER, MYMYSQL_PASSWORD, MYMYSQL_HOST, MYMYSQL_DB);
//      echo_green($dump_command);
      system($dump_command, $dump_status);
      if ($dump_status != 0) die_red("Dumping pre-migration dump file failed abysmally!");
      $dump_created = true;
    }

//    echo "\n";

    // If you have DB create permissions on your production server, it should be possible to remove this condition. The generated commands should adjust accordingly. But, test it to be sure.
    if ( WEB_ENV != "production")
    {
        echo_yellow("Testing version $previous_version to $file_version...");

//      echo_yellow("Testing the migration on a test database. Note: this test is only done in development, because you often can't create databases on your production server because of lack of permissions. So, always test your migrations on your development DB first.");

      // drop a possibly existing test db
      $drop_test_db_query = "DROP DATABASE IF EXISTS $migration_test_db;";
//      echo_green("$drop_test_db_query");
      if (! mysql_query($drop_test_db_query)) die_red("Could not delete $migration_test_db db.");

      // create a new test db, so you can be sure it's empty
      $create_test_db_query = "CREATE DATABASE $migration_test_db;";
//      echo_green("$create_test_db_query");
      if (! mysql_query($create_test_db_query)) die_red("Could not create $migration_test_db db.");
      
      // load the dump of development into the test db
      $load_into_test_command = sprintf("mysql --user=%s --password=%s --host=%s $migration_test_db < $dump_file", MYMYSQL_USER, MYMYSQL_PASSWORD, MYMYSQL_HOST);
//      echo_green("$load_into_test_command");
      $load_into_test_db_status = null;
      system($load_into_test_command, $load_into_test_db_status);
      if ($load_into_test_db_status != 0) die_red("Loading dump into test DB failed."); 

      // Run the migration on the test db
      $test_migration_status = null;
      $migration_test_command = sprintf("mysql --user=%s --password=%s --host=%s $migration_test_db < $filename", MYMYSQL_USER, MYMYSQL_PASSWORD, MYMYSQL_HOST);
//      echo_green($migration_test_command);
      system($migration_test_command, $test_migration_status);
      if ($test_migration_status != 0 ) die_red("Migration $file_version has errors in it. Aborting.");
      
      // At this point, we know the test succeeded, so we can continue safely.
//      echo_yellow("Testing of migration $file_version successful, there don't appear to be any errors. Continueing...");
        echo_green('Passed');
    }

    echo "\n";

/*
    echo_yellow("Committing pre-migration dump of ".WEB_ENV." version $previous_version.");
    $svn_commit_command = sprintf("svn commit -m 'Updated pre-migration dump of %s to $previous_version' $dump_file", WEB_ENV);
    echo_green("$svn_commit_command");
    $svn_commit_result = null;
    system($svn_commit_command, $svn_commit_result);
    if ($svn_commit_result != 0) die_red("Commiting backup dump into svn failed.");

    echo "\n";
*/

    // do stuff here that executes the migration.
    echo_yellow("Migrating from version $previous_version to $file_version...");
    $migration_status = null;
    $migration_command = sprintf("mysql --user=%s --password=%s --host=%s %s < $filename", MYMYSQL_USER, MYMYSQL_PASSWORD, MYMYSQL_HOST, MYMYSQL_DB);
//    echo_green($migration_command);
    system($migration_command, $migration_status);

    // If loading the migration failed.
    if ($migration_status != 0)
    {
      echo "\n";

      die_red("Migration failed! You now have a possible inconsistent database, because it did pass the initial test, but failed when the migration was actually run. You need to find out what statements of the migration did work, and bring it back to the state of the previous migration. For the record, migration $file_version is the one that failed. \n");

    }

//    echo "\n";

    // Insert the version number of the migration into the migration_history table.
//    echo_yellow("Inserting version $file_version into migration_history table");
    mymysql_insert("migration_history", array("migration_nr" => $file_version));
    echo_green("Success\n");
  }

  $previous_version = $file_version;
}

echo_green("\n" . MYMYSQL_DB . " is up to date ($file_version)");

///////////////////////////////////////////////////////////////////////
// Copyright 2007, Wiebe Cazemier (http://www.halfgaar.net)
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// Original file: shell-output-helpers.php
///////////////////////////////////////////////////////////////////////

function echo_green($text) {
    echo GREEN_BEGIN . "$text" . COLOR_END . "\n";
}

function echo_yellow($text) {
    echo YELLOW_BEGIN . "$text" . COLOR_END . "\n";
}

function die_red($text) {
    die(RED_BEGIN . "$text" . COLOR_END . "\n");
}


///////////////////////////////////////////////////////////////////////
// Copyright 2007, Wiebe Cazemier (http://www.halfgaar.net), Rowan Rodrik van der Molen
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// Original file: mymysql-functions.php
///////////////////////////////////////////////////////////////////////

// My MySQL functions. These are supposed to make working with MySQL more convenient.

function mymysql_build_query($query) {
    if ( is_string($query) )
        $generated_sql = $query;
    elseif ( is_array($query) ) {
      $generated_sql = $query[0];
   
        foreach($query[1] as $key => $value) {
            if ( is_string($value) )
                $value = "'".mysql_real_escape_string($value)."'";
   
            // Replace the :keys with the values
            $generated_sql = preg_replace("/:$key\b/", " $value ", $generated_sql);
        }
    }
   
    return $generated_sql;
}

function mymysql_where($where) {
    if ( is_null($where) ) return '';
  
    $sql .= "WHERE ";
  
    // TODO: Look up primary key in information_schema
    if ( is_int($where) ) {
        $sql .= "id = $where";
    } else {
        $sql .= mymysql_build_query($where);
    }
  
    return $sql;
}

function mymysql_select($query) {
    $generated_sql = mymysql_build_query($query);
    $result = mysql_query($generated_sql);
  
    if (!$result) trigger_error("mysql_query() failed: " . mysql_error(), E_USER_ERROR);
  
    $rows = array();
    while ($row = mysql_fetch_assoc($result)) $rows[] = $row;
  
    return $rows;
}

function mymysql_select_one($query) {
    $rows = mymysql_select($query);

    return $rows[0];
}

function mymysql_select_value($query) {
    $generated_sql = mymysql_build_query($query);
    $result = mysql_query($generated_sql);
  
    if (!$result) trigger_error("mysql_query() failed: " . mysql_error(), E_USER_ERROR);
  
    $row = mysql_fetch_array($result);
  
    if ( isset($row[0]) )
        return $row[0];
    else
        return null;
}

function mymysql_insert($table_name, $row) {
    $column_names = array();
    $column_values = array();
  
    $sql = "INSERT INTO $table_name (";
  
    foreach ($row as $column_name => $column_value) {
        $column_names[] = $column_name;
        $column_values[] = "'" . mysql_real_escape_string($column_value) . "'";
    }
  
    $sql .= implode(', ', $column_names) . ") VALUES (" . implode(', ', $column_values) . ")";
  
    if ( !mysql_query($sql) ) trigger_error("mysql_query() failed: " . mysql_error(), E_USER_ERROR);
  
    return mysql_insert_id();
}

function mymysql_delete($table_name, $where) {
    $sql = "DELETE FROM $table_name " . mymysql_where($where);
  
    if ( !mysql_query($sql) ) trigger_error("mysql_query() failed: " . mysql_error(), E_USER_ERROR);
  
    return mysql_affected_rows();
}

function mymysql_update($table_name, $where, $row_update) {
    $updated_columns = array();
  
    $sql = "UPDATE $table_name SET ";
  
    foreach ($row_update as $column_name => $column_value) {
        if ( is_string($column_value) )
            $column_value = "'" . mysql_real_escape_string($column_value) . "'";
  
        $updated_columns[] = sprintf("%s = %s", $column_name, $column_value);
    }
  
    $sql .= implode(', ', $updated_columns) . " " . mymysql_where($where);
  
    if ( !mysql_query($sql) ) trigger_error("mysql_query() failed: " . mysql_error(), E_USER_ERROR);
  
    return mysql_affected_rows();
}

function mymysql_begin_transaction() {
    mysql_query('BEGIN');
}

function mymysql_commit_transaction() {
    mysql_query('COMMIT');
}

function mymysql_rollback_transaction() {
    mysql_query('ROLLBACK');

}