<?//����åɾܺ٤Υ�����ɽ�� ?>
<? //$threadData:����åɥǡ����Υ��֥������Ȥ�������ݻ� ?>
<? //$threadid:����åɥǡ����Υ���å�ID��������ݻ�?>
<? //$threadComments:�����ȥǡ����Υ��֥������Ȥ�������ݻ� ?>

	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja"
		dir="ltr">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<title>����åɾܺ�</title>
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
		����åɾܺ�
		</span>
		</div>
		<form method="post" action="" ENCTYPE="MULTIPART/FORM-DATA">
			<table>
				<? if($threadData){?>
				<? foreach (thread_list::getThidArray() as $threadid){?>
				<tr>
						<td>
						<div style="background-color: #D3DCE3; width: 250px;">����å��ֹ�</div>
						<div id="test">
						<?// ����å��ֹ��ɽ��?>
						<?= $threadData[$threadid]->getProp("thread_id");?>
							</div>
					</td>
				</tr>
				<tr>
					<td>
						<div style="background-color: #D3DCE3; width: 250px">�����ȥ�</div>
						<div>
							<?// ����åɥ����ȥ��ɽ��?>
							<?= h($threadData[$threadid]->getProp("thread_subject"));?>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div style="background-color: #D3DCE3; width: 250px">��ʸ</div>
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

					<?// �������ֹ��ɽ��?>
					<span>������No.<?= $value->getProp("comment_id")?></span></div>

					<?// ���������URL�����?>
					<? if($value->getPicsUrl("pic_name")){?>
					<span><a href="<?= $value->getPicsUrl("pic_name");?>">

					<?// ����ͥ����Ѳ�����󥯤����?>
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
						<div>�����Ȥ��</div> <textarea id="bodyInput" name="bodyInput"
							style="width: 250px; height: 100px"></textarea>
					</td>
				</tr>
				<tr>
					<td> <input type="submit" name="submit" value="����"></input>
						 <input type="hidden" NAME="MAX_FILE_SIZE" SIZE="655360"></input>
						 <input type="file" NAME="file_name" SIZE="42"></input>
					</td>
				</tr>
			</table>
		</form>
		<? }?>
		<div>
			<p>
					<a href="../thread_list.php">���������</a>
			</p>
		</div>
	</body>
	</html>

<?// print_r(get_included_files());
//print_r(get_defined_constants());?>
