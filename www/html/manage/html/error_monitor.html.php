<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja"
	dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<title>エラーモニター</title>
<!--[if lte IE 6]>
<script type="text/javascript">
//<![CDATA[
};
//]]>
</script>
<![endif]-->
</head>
<body>
<form action="../error_monitor.php">
<span>取得行数</span>
<input type="text" name="num" >
<span>行</span>
	<div>
		<table>
			<tr>
				<td>
				<? $i = 0;?>
				<? if($num){?>
				<? foreach ($fileVar as $row){?>
				<? if ($num<=$i){?>
				<? break;?>
				<? }?>
				<?	$i++;?> <?= $row."<br>";?>
				<?}	?>
				<? }?>
				</td>
			</tr>
		</table>
	</div>
</form>

</body>
</html>