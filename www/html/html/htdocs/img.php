<?php
	/* ����åɾܺ٥�������Ǥβ���ɽ����read_file����
	 *
	*/

	// ����ͥ���Υե饰
	$image_thum	= isset($_GET["s"]) ? $_GET["s"] : NULL;

	// �ե�����̾�����
	$image_name = isset($_GET["img_name"]) ? $_GET["img_name"] : NULL;

	// ����ͥ����ѥե�����̾����
	$image_name_s = preg_replace("/\./","_s.",$image_name);

	// �̾掠���������ѥ�����
	$image_path = "../img/".$image_name;
	// ����ͥ�������ѥ�����
	$image_path_s = "../img/".$image_name_s;

	do
	{
		if(!getimagesize($image_path)){
			break;
		}

		// ��ĥ�Ҥ�Ĵ�٤�Ŭ�ڤʲ�������������
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

		// ����ͥ���ξ��
		if($image_thum){
			header('Content-Type: image/'.$extension);
			readfile($image_path_s);
		// �̾掠���������ξ��
		}else{
		header('Content-Type: image/'.$extension);
		readfile($image_path);
		}

	}while(0);
?>