<?php
	/* ����åɾܺ٥ڡ������饹
	 * ����åɤΥ����ȥ�ȥ���åɤΥ�����ɽ������Ʋ�����ɽ���򰷤�
	*/

	// ����å�ID����
	$threadID = isset($_GET["thid"]) && is_numeric($_GET["thid"]) ? $_GET["thid"] : NULL;

	do{

		$msg   = NULL;

		if(!$threadID){
			$msg = "����åɤ����Ĥ���ޤ���Ǥ���";
			break;
		}
		// ����å�ID�򸡺����˳�Ǽ
		$threadSearchArr = array(
				"thread_id"		=> $threadID,
				"status"		=> "0"
		);

		// �ڡ�������
		$limit = "1";
		// ����åɤξ���򥪥֥������Ȥ�����Ǽ���
		$threadData = thread_list::getThreadList($dbh,$limit,$threadSearchArr,1);

		if(!$threadData){
			$msg = "����åɤ����Ĥ���ޤ���Ǥ���";
			break;
		}

		// ��������о��
		$threadCommentSearchArr = array(
				"thread_id" => $threadID,
				"group_by_thread_id"	=> "group_by_thread_id",
				//				"comment_id" => "10",
		);

		// ������ɽ���ѥǡ�������
		$threadComments = thread_list::getCommentList($dbh,COMMENT_BASE_NUM,$threadCommentSearchArr,2);

		if(!$_POST){
			break;
		}

		// ��������ʸ�����
		$commentInput["bodyInput"] = isset($_POST["bodyInput"]) ? $_POST["bodyInput"] : NULL;

		if( ( $commentInput["bodyInput"] == "" ) ){
			$msg = "�����Ȥ�̤���ϤǤ���";
			break;
		}
		// �����ȥǡ��������ͤ�����Ǻ��� "new_thread"��0�Ǹ���(����Ω�ƻ�������:1,�̾拾����:0)
		$threadCommentInput = array(
				"thread_id" => $threadID,
				"bodyInput" => $commentInput["bodyInput"],
				"file_name" => NULL,
				"new_thread" => 0,
		);

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
			$threadCommentInput["file_name"] = $fileName;
		}
		if(thread_list::insertCommentBody($dbh,$threadCommentInput)){
			header("location:".$_SERVER["REQUEST_URI"]);
		}else{
			$msg = "��Ͽ����";
			error_log("__FILE__.__LINE__.��³�˼���,3,".ERROR_DIR);
		}
	}while(0);

	require_once("../html/thread_detail.html.php");
?>