<?php
require_once("../inc/config.php");
require_once("../inc/thread_list.class.php");

$page = 50;
$max = 5;
$now = @$_GET["now"];

function pageNum ($page, $max, $now){
	$to = $now + $max;

	for ( $i=0; $i<$max; $i++){

		if ( $to < $page ){
			$num = $now + $i + 1;
			echo $num." ";

		}else {
			$over = $page - $max;
			$num = $over + $i + 1;
			echo $num." ";
		}
	}
}

// function aaa ($page, $max, $now){

// 	$to = $now + $max;
// 	for ($i=0;$i<$max;$i++){
// 		if($to < $page){
// 			$num = $now + $i + 1;
// 			echo $num;
// 		}else {
// 			$over = $page - 5;
// 			$num = $over + $i;
// 			echo $num;
// 		}
// 	}
// }


pageNum($page, $max, $now);





?>