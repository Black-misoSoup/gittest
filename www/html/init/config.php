<?php
	/* ����ե���
	 *
	*/

	$dsn = 'mysql:dbname=***;host=****';
	$user = '******';
	$password = '*******';

	try{
		$dbh = new PDO($dsn, $user, $password);
		if ($dbh == null){
			error_log("__FILE__.__LINE__.��³�˼��Ԥ��ޤ���<br>".",3,".ERROR_DIR);
		}
	}catch (PDOException $e){
		print('Error:'.$e->getMessage());
		die();
	}

	// URL���
	define("URL","http://*****/");
    define("CLASSPATH", "/www/html/class/");
	define("ERROR_DIR", "/www/html/log/php_error.log");

	// ����åɰ���ɽ����
	define("PAGEBASENUM","10");

	// �ƥ���åɥ�����ɽ����
	define("COMMENT_BASE_NUM","10");

	function h($input){
		$result = htmlspecialchars($input,ENT_QUOTES,"EUC-JP");
		return $result;
	}

	function a($input){
		$result = addslashes($input);
		return $result;
	}

	function __autoload($class_name) {
		if (!$class_name) false;
		$classfile = CLASSPATH.$class_name.".class.php";
		if ( !file_exists($classfile) ) false;
		include $classfile;
	}

?>