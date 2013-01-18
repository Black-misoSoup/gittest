<?php
// エラーログ表示
// 件数指定で末尾から順に表示

$num = isset( $_GET["num"]) && is_numeric($_GET["num"]) ? $_GET["num"] : 100;
$fileVar = file(ERROR_DIR);
krsort($fileVar);
$i = 0;
require_once("../html/error_monitor.html.php");
?>