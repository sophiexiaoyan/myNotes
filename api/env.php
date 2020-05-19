<?php
// This file allows to read environment vairable from .env file
// In .env file you can set the value for the variable to connect to mysql
if (is_file('../.env')) {
  $env = parse_ini_file('../.env', true);    //parse .env file, name = PHP_KEY
  foreach ($env as $key => $val) {
    $name = strtoupper($key);
    if (is_array($val)) {
      foreach ($val as $k => $v) {    //if is an array, item = PHP_KEY_KEY
        $item = $name . '_' . strtoupper($k);
        putenv("$item=$v");
      }
    } else {
      putenv("$name=$val");
    }
  }
}
?>
