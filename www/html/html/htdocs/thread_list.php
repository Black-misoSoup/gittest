<?php

	/* スレッド一覧ページクラス
	 * スレッド一覧表示処理を扱う
	* スレッドの並びはコメントの日付が新しい順で表示
	*/

	$currentPageNo = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
	do{

		$msg   = NULL;
		// ページ指定
		$limit = ($currentPageNo * PAGEBASENUM - PAGEBASENUM).",".PAGEBASENUM;

		// ソート用コメントデータのオブジェクト取得(最新コメントのスレッドをソートしたスレッドIDとスレッドのコメント数)
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
			error_log("__FILE__.__LINE__.接続に失敗<br>,3,".ERROR_DIR);
			break;
		}

		// ページ送り引数
		$pagingSearchArr = array(
				"currentPageNo" => $currentPageNo,
				"threadsNo"     => $rowAll[0],
		);

		// ページングURLを作成
		$pagingUrl = thread_list::getPagingUrl($dbh,$pagingSearchArr);

		// コメントデータのオブジェクトを配列で保持
		$threadComments = $lists;

		// コメントデータのオブジェクトのスレッドIDのみを配列で保持
		$threadsArr = thread_list::getThidArray();

		// スレッド検索条件
		$threadSearchArr = array(
				"thread_id" 	 => $threadsArr,
				//		"thread_subject" => "タイトルの抽出条件",
				//		"thread_body"    => "本文の抽出条件",
				"status"      => "0",
		);

		// スレッドの情報をオブジェクトの配列で取得
		$threadData = thread_list::getThreadList($dbh,NULL,$threadSearchArr,2);
		if(!$threadData){
			$msg = "ページが見つかりません";
			break;
		}

	}while(0);

	//
	$input["subjectInput"] = isset($_POST['subjectInput']) ? $_POST['subjectInput'] : NULL;

	$inputComment["thread_id"] = NULL;
	$inputComment["bodyInput"] = isset($_POST['bodyInput']) ? $_POST['bodyInput'] : NULL;
	$inputComment["file_name"] = NULL;
	// スレッドを立てた時のコメントのフラグ
	$inputComment["new_thread"] = 1;

	do {

		if(!$_POST){
			break;
		}

		if((!$input["subjectInput"]) || (!$inputComment["bodyInput"])) {
			$msg = "タイトル、本文が未入力です";
			break;
		}

		if($_FILES["file_name"]["size"]){

			$img_name = $_FILES["file_name"]["name"];
			$tempPath = $_FILES["file_name"]['tmp_name'];
			$extension = getimagesize($tempPath);

			// ファイル形式チェック
			switch ($extension["mime"]){

				case "image/jpeg":
					$image = ImageCreateFromJPEG($tempPath); //JPEGファイルを読み込む
					$extension = "jpg";
					break;
				case "image/gif":
					$image = ImageCreateFromGIF($tempPath); //GIFファイルを読み込む
					$extension = "gif";
					break;
				case "image/png":
					$image = ImageCreateFromPNG($tempPath); //PNGファイルを読み込む
					$extension = "png";
					break;
			}

			$fileName = mt_rand()."_s.".$extension;
			// サムネイル作成とリサイズ
			for ($i=0;$i<=1;$i++){

				$width = ImageSX($image);
				$height = ImageSY($image); //縦幅（ピクセル）

				if ( $i==0 ){
					$new_width = 100;
				}else{
					$new_width = $width;
					$fileName  = preg_replace("/_s/","",$fileName);
				}
				$rate = $new_width / $width; //圧縮比
				$new_height = $rate * $height;

				$new_image = ImageCreateTrueColor($new_width, $new_height);
				ImageCopyResampled($new_image,$image,0,0,0,0,$new_width,$new_height,$width,$height);

				$file_newpath = "../img/".$fileName;
				ImageJPEG($new_image, $file_newpath, 100);
			}
			$inputComment["file_name"] = $fileName;
		}

		if(!$lastID = thread_list::insertBody($dbh,$input)){
			$msg = "登録失敗";
			error_log("__FILE__.__LINE__.接続に失敗<br>,3,".ERROR_DIR);
			break;
		}

		$inputComment["thread_id"] = $lastID;

		if(!thread_list::insertCommentBody($dbh,$inputComment)){
			$msg = "登録失敗";
			error_log("__FILE__.__LINE__.接続に失敗<br>,3,".ERROR_DIR);
			break;
		}else{
			header("location:".$_SERVER["REQUEST_URI"]);
		}

	}while (0);

	require_once("../html/thread_list.html.php");
	// 	print_r(get_included_files());
	//  	print_r(get_defined_constants());
?>