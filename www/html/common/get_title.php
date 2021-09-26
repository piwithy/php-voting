<?php
include "mysql_driver.php";

$result = $mysqli->query("SELECT field_value FROM custom_fields WHERE field_name='index_title';");
$row = $result->fetch_assoc();
$title = $row["field_value"];

$result = $mysqli->query("SELECT field_value FROM custom_fields WHERE field_name='index_quote';");
$row = $result->fetch_assoc();
$quote = $row["field_value"];
$disp = "<h2>".$title."</h2><br>".$quote;
if(!$quote) $disp = "<h2>".$title."</h2>";

echo $disp;
