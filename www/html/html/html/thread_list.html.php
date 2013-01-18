<? //スレッド一覧表示 VIEW ?>
<? //$pagingUrl :ページング用URLの配列?>
<? //$threadData:スレッドデータのオブジェクトを配列で保持 ?>
<? //$threadsArr:スレッドデータのスレッドIDを配列で保持?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja"
	dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<title>スレッド一覧</title>
<!--[if lte IE 6]>
<script type="text/javascript">
//<![CDATA[
};
//]]>
</script>
<![endif]-->
</head>
<body>

	<? if($msg){?>
	<?=$msg?>
	<div>
		<p>
			<a href="../thread_list.php">一覧に戻る</a>
		</p>
	</div>
	<? }else{?>
	<div>
		<span>

			<?foreach ($pagingUrl["current"] as $currentValue){?>
			<?= $currentValue;?>
			<?}?>
		</span>
	</div>
	<div>
		<span> <?foreach ($pagingUrl["pageeject"] as $pageejectValue){?> <?= $pageejectValue;?>
			<?}
			?>
		</span>
	</div>
	<table>
		<?php if($threadData){?>
		<?php foreach ($threadsArr as $threadid){?>
		<tr>
			<td style="background-color: #D3DCE3; width: 250px">
				<a href="<?= $threadData[$threadid]->getUrl()?>"><?= $threadData[$threadid]->getProp("thread_subject");?>
				<? if($threadComments){?>
				(<?=$threadComments[$threadid]->getProp("count(thread_id)")?>)
				<? }?>
				</a>
			</td>
		</tr>
		<?php };?>
		<?php }?>
	</table>
	<form method="post" action="../thread_list.php" ENCTYPE="MULTIPART/FORM-DATA">
		<table>
			<tr>
				<td style="background-color:#D3DCE3 ;width:250px">
				<div>タイトル</div>
				<input style="width:250px" id="subjectInput" type="text" name="subjectInput" >
				</td>
			</tr>
			<tr>
				<td style="background-color:#D3DCE3 ">
				<div>本文</div>
				<textarea id="bodyInput" name="bodyInput" style="width:250px;height:100px"></textarea>
				</td>
			</tr>
			<tr>
				<td><input type="submit" name="submit" value="書き込む"><span>
				<input type="file" name="file_name" size="25"></span>
				</td>
			</tr>
			<??>
			<??>
			</table>
		<div>
		<?}?>
		</div>
	</form>
</body>
</html>