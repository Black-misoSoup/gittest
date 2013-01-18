<?php
	/* スレッド詳細ページクラス
	 * スレッドのタイトルとスレッドのコメント表示、投稿画像の表示を扱う
	*/

	// スレッドID取得
	$threadID = isset($_GET["thid"]) && is_numeric($_GET["thid"]) ? $_GET["thid"] : NULL;

	do{

		$msg   = NULL;

		if(!$threadID){
			$msg = "スレッドが見つかりませんでした";
			break;
		}
		// スレッドIDを検索条件に格納
		$threadSearchArr = array(
				"thread_id"		=> $threadID,
				"status"		=> "0"
		);

		// ページ指定
		$limit = "1";
		// スレッドの情報をオブジェクトの配列で取得
		$threadData = thread_list::getThreadList($dbh,$limit,$threadSearchArr,1);

		if(!$threadData){
			$msg = "スレッドが見つかりませんでした";
			break;
		}

		// コメント抽出条件
		$threadCommentSearchArr = array(
				"thread_id" => $threadID,
				"group_by_thread_id"	=> "group_by_thread_id",
				//				"comment_id" => "10",
		);

		// コメント表示用データ取得
		$threadComments = thread_list::getCommentList($dbh,COMMENT_BASE_NUM,$threadCommentSearchArr,2);

		if(!$_POST){
			break;
		}

		// コメント本文を取得
		$commentInput["bodyInput"] = isset($_POST["bodyInput"]) ? $_POST["bodyInput"] : NULL;

		if( ( $commentInput["bodyInput"] == "" ) ){
			$msg = "コメントが未入力です。";
			break;
		}
		// コメントデータ入力値を配列で作成 "new_thread"は0で固定(スレ立て時コメント:1,通常コメント:0)
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
			$threadCommentInput["file_name"] = $fileName;
		}
		if(thread_list::insertCommentBody($dbh,$threadCommentInput)){
			header("location:".$_SERVER["REQUEST_URI"]);
		}else{
			$msg = "登録失敗";
			error_log("__FILE__.__LINE__.接続に失敗,3,".ERROR_DIR);
		}
	}while(0);

	require_once("../html/thread_detail.html.php");
?>