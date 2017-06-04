<?php
include 'info.php';
$json = json_encode($results,JSON_UNESCAPED_SLASHES);
echo $json;

?>
