<?php
// Database connection
$db = new mysqli("localhost", "beerme_db", "kE19s#t5", "beerme_db", 3306);

if ($db->connect_errno) {
  printf("Failed to connect to MySQL: (%s) %s", $db->connect_errno, $db->connect_error);
  echo "</body></html>";
  exit();
}

if (! $db->set_charset("utf8")) {
  printf("Error loading character set utf8: %s\n", $db->error);
}
?>