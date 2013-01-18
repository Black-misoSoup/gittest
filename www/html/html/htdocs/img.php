<?php
	/* スレッド詳細コメント欄での画像表示用read_file処理
	 *
	*/

	// サムネイルのフラグ
	$image_thum	= isset($_GET["s"]) ? $_GET["s"] : NULL;

	// ファイル名を取得
	$image_name = isset($_GET["img_name"]) ? $_GET["img_name"] : NULL;

	// サムネイル用ファイル名作成
	$image_name_s = preg_replace("/\./","_s.",$image_name);

	// 通常サイズ画像パス作成
	$image_path = "../img/".$image_name;
	// サムネイル画像パス作成
	$image_path_s = "../img/".$image_name_s;

	do
	{
		if(!getimagesize($image_path)){
			break;
		}

		// 拡張子を調べて適切な画像形式を設定
		$extension = getimagesize($image_path);
		switch ($extension["mime"]){

			case "image/jpeg":
				$image = ImageCreateFromJPEG($image_path); //JPEG
				$extension = "jpeg";
				break;
			case "image/gif":
				$image = ImageCreateFromGIF($image_path); //GIF
				$extension = "gif";
				break;
			case "image/png":
				$image = ImageCreateFromPNG($image_path); //PNG
				$extension = "png";
				break;
		}

		if (!file_exists($image_path)) {
			break;
		}

		// サムネイルの場合
		if($image_thum){
			header('Content-Type: image/'.$extension);
			readfile($image_path_s);
		// 通常サイズ画像の場合
		}else{
		header('Content-Type: image/'.$extension);
		readfile($image_path);
		}

	}while(0);
?>