<?php
/* 
 * This file contains the database access information.
 * This file also establishes a connection to MySQL and selects the database.
 * This file also defines the escape_data() function.
*/

// Set the database access information as constants.
define('DB_HOST', 'localhost:3306');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_SCHEMA', 'clinic');

// Backup settings
define('BACKUP_DIR', 'backup/');

// Make the connection.
$GLOBALS['DB'] = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_SCHEMA);
if (!$GLOBALS['DB']) {
  echo 'Error No. ' . mysqli_connect_errno() . PHP_EOL . '<br>';
  echo 'Error: ' . mysqli_connect_error() . PHP_EOL . '<br>';
  exit('Error: Unable to connect to MySQL' . PHP_EOL);
}

// Set proper character sets
@mysqli_query($GLOBALS['DB'], 'SET NAMES utf8');
@mysqli_query($GLOBALS['DB'], 'SET CHARACTER SET utf8');
mysqli_set_charset($GLOBALS['DB'], 'utf8');

// Escape data
function escape_data($data) {
  // Address Magic Quotes.
  if (ini_get('magic_quotes_gpc')) {
    $data = stripslashes($data);
  }

  // Check for mysql_real_escape_string() support.
  if (function_exists('mysqli_real_escape_string')) {
    $data = mysqli_real_escape_string($GLOBALS['DB'], trim($data));
  } else {
    $data = mysqli_escape_string($GLOBALS['DB'], trim($data));
  }

  // Return the escaped value.
  return $data;
}


