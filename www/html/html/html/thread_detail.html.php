<?//スレッド詳細のコメント表示 ?>
<? //$threadData:スレッドデータのオブジェクトを配列で保持 ?>
<? //$threadid:スレッドデータのスレッドIDを配列で保持?>
<? //$threadComments:コメントデータのオブジェクトを配列で保持 ?>

	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja"
		dir="ltr">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<title>スレッド詳細</title>
	<!--[if lte IE 6]>
	<script type="text/javascript" src="./js/offspring.js?r7478"></script>
	<script type="text/javascript">
	//<![CDATA[


	//]]>
	</script>
	<![endif]-->

	<script type="text/javascript">

	</script>
	</head>
	<body>
		<? if($msg){?>
			<div>
				<p>
				<?=$msg?>
				</p>
			</div>
		<? }else {?>
		<div>
		<span>
		スレッド詳細
		</span>
		</div>
		<form method="post" action="" ENCTYPE="MULTIPART/FORM-DATA">
			<table>
				<? if($threadData){?>
				<? foreach (thread_list::getThidArray() as $threadid){?>
				<tr>
						<td>
						<div style="background-color: #D3DCE3; width: 250px;">スレッド番号</div>
						<div id="test">
						<?// スレッド番号を表示?>
						<?= $threadData[$threadid]->getProp("thread_id");?>
							</div>
					</td>
				</tr>
				<tr>
					<td>
						<div style="background-color: #D3DCE3; width: 250px">タイトル</div>
						<div>
							<?// スレッドタイトルを表示?>
							<?= h($threadData[$threadid]->getProp("thread_subject"));?>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div style="background-color: #D3DCE3; width: 250px">本文</div>
						<div>
							<?= h($threadData[$threadid]->getProp("thread_body"))?>
							<? if($threadData[$threadid]->getPicsUrl("pic_name")){?>
							<div>
								<span><a href="<?= $threadData[$threadid]->getPicsUrl("pic_name");?>">
								<img src="<?=$threadData[$threadid]->getThumPicsUrl("pic_name")?>">
								</a></span>
							</div>
							<?}?>
							<?}?>

						</div>
					</td>
				</tr>
				<tr>
					<td>
					<div>
					<?	foreach ($threadComments as $value){?>
					<div style="background-color: #C9F3A1; width: 250px">

					<?// コメント番号を表示?>
					<span>コメントNo.<?= $value->getProp("comment_id")?></span></div>

					<?// 画像リンク用URLを指定?>
					<? if($value->getPicsUrl("pic_name")){?>
					<span><a href="<?= $value->getPicsUrl("pic_name");?>">

					<?// サムネイル用画像リンクを指定?>
					<img src="<?=$value->getThumPicsUrl("pic_name")?>"></a></span>
					<?}?>
					<div><?=  h($value->getProp("comment_body"));?></div>
					<span></span>
					<?	}?>
					<?}?>
					</div>
					</td>
				</tr>
				<tr>
					<td>
						<div>コメントを書く</div> <textarea id="bodyInput" name="bodyInput"
							style="width: 250px; height: 100px"></textarea>
					</td>
				</tr>
				<tr>
					<td> <input type="submit" name="submit" value="送信"></input>
						 <input type="hidden" NAME="MAX_FILE_SIZE" SIZE="655360"></input>
						 <input type="file" NAME="file_name" SIZE="42"></input>
					</td>
				</tr>
			</table>
		</form>
		<? }?>
		<div>
			<p>
					<a href="../thread_list.php">一覧に戻る</a>
			</p>
		</div>
	</body>
	</html>

<?// print_r(get_included_files());
//print_r(get_defined_constants());?>
