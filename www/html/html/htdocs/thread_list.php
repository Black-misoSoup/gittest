<?php

	/* ����åɰ����ڡ������饹
	 * ����åɰ���ɽ�������򰷤�
	* ����åɤ��¤Ӥϥ����Ȥ����դ����������ɽ��
	*/

	$currentPageNo = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
	do{

		$msg   = NULL;
		// �ڡ�������
		$limit = ($currentPageNo * PAGEBASENUM - PAGEBASENUM).",".PAGEBASENUM;

		// �������ѥ����ȥǡ����Υ��֥������ȼ���(�ǿ������ȤΥ���åɤ򥽡��Ȥ�������å�ID�ȥ���åɤΥ����ȿ�)
		$sql = "SELECT SQL_CALC_FOUND_ROWS thread_id,count(thread_id),MAX(comment_id),MAX(writedate) FROM bbs_comment WHERE status IN (0) GROUP BY thread_id ORDER BY MAX(writedate) DESC limit ".$limit;
		$stmt = $dbh->prepare($sql);

		if($stmt->execute()){
			$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$rows = count($row);

			$sql = "SELECT FOUND_ROWS()";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$rowAll = $stmt->fetch(PDO::FETCH_NUM);

			$lists	= array();
			for ( $i=0; $i<$rows; $i++ ) {
				$thv = new thread_list($row[$i]);
				$lists[$row[$i]['thread_id']] = $thv;
				$thv->setCommentsNo($row[$i]["count(thread_id)"]);
				$thv->setThreadid($row[$i]['thread_id']);
			}
			$stmt->closeCursor();
		}else{
			error_log("__FILE__.__LINE__.��³�˼���<br>,3,".ERROR_DIR);
			break;
		}

		// �ڡ����������
		$pagingSearchArr = array(
				"currentPageNo" => $currentPageNo,
				"threadsNo"     => $rowAll[0],
		);

		// �ڡ�����URL�����
		$pagingUrl = thread_list::getPagingUrl($dbh,$pagingSearchArr);

		// �����ȥǡ����Υ��֥������Ȥ�������ݻ�
		$threadComments = $lists;

		// �����ȥǡ����Υ��֥������ȤΥ���å�ID�Τߤ�������ݻ�
		$threadsArr = thread_list::getThidArray();

		// ����åɸ������
		$threadSearchArr = array(
				"thread_id" 	 => $threadsArr,
				//		"thread_subject" => "�����ȥ����о��",
				//		"thread_body"    => "��ʸ����о��",
				"status"      => "0",
		);

		// ����åɤξ���򥪥֥������Ȥ�����Ǽ���
		$threadData = thread_list::getThreadList($dbh,NULL,$threadSearchArr,2);
		if(!$threadData){
			$msg = "�ڡ��������Ĥ���ޤ���";
			break;
		}

	}while(0);

	//
	$input["subjectInput"] = isset($_POST['subjectInput']) ? $_POST['subjectInput'] : NULL;

	$inputComment["thread_id"] = NULL;
	$inputComment["bodyInput"] = isset($_POST['bodyInput']) ? $_POST['bodyInput'] : NULL;
	$inputComment["file_name"] = NULL;
	// ����åɤ�Ω�Ƥ����Υ����ȤΥե饰
	$inputComment["new_thread"] = 1;

	do {

		if(!$_POST){
			break;
		}

		if((!$input["subjectInput"]) || (!$inputComment["bodyInput"])) {
			$msg = "�����ȥ롢��ʸ��̤���ϤǤ�";
			break;
		}

		if($_FILES["file_name"]["size"]){

			$img_name = $_FILES["file_name"]["name"];
			$tempPath = $_FILES["file_name"]['tmp_name'];
			$extension = getimagesize($tempPath);

			// �ե�������������å�
			switch ($extension["mime"]){

				case "image/jpeg":
					$image = ImageCreateFromJPEG($tempPath); //JPEG�ե�������ɤ߹���
					$extension = "jpg";
					break;
				case "image/gif":
					$image = ImageCreateFromGIF($tempPath); //GIF�ե�������ɤ߹���
					$extension = "gif";
					break;
				case "image/png":
					$image = ImageCreateFromPNG($tempPath); //PNG�ե�������ɤ߹���
					$extension = "png";
					break;
			}

			$fileName = mt_rand()."_s.".$extension;
			// ����ͥ�������ȥꥵ����
			for ($i=0;$i<=1;$i++){

				$width = ImageSX($image);
				$height = ImageSY($image); //�����ʥԥ������

				if ( $i==0 ){
					$new_width = 100;
				}else{
					$new_width = $width;
					$fileName  = preg_replace("/_s/","",$fileName);
				}
				$rate = $new_width / $width; //������
				$new_height = $rate * $height;

				$new_image = ImageCreateTrueColor($new_width, $new_height);
				ImageCopyResampled($new_image,$image,0,0,0,0,$new_width,$new_height,$width,$height);

				$file_newpath = "../img/".$fileName;
				ImageJPEG($new_image, $file_newpath, 100);
			}
			$inputComment["file_name"] = $fileName;
		}

		if(!$lastID = thread_list::insertBody($dbh,$input)){
			$msg = "��Ͽ����";
			error_log("__FILE__.__LINE__.��³�˼���<br>,3,".ERROR_DIR);
			break;
		}

		$inputComment["thread_id"] = $lastID;

		if(!thread_list::insertCommentBody($dbh,$inputComment)){
			$msg = "��Ͽ����";
			error_log("__FILE__.__LINE__.��³�˼���<br>,3,".ERROR_DIR);
			break;
		}else{
			header("location:".$_SERVER["REQUEST_URI"]);
		}

	}while (0);

	require_once("../html/thread_list.html.php");
	// 	print_r(get_included_files());
	//  	print_r(get_defined_constants());
?>