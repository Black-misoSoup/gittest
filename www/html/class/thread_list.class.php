<?php
	/* スレッドクラス
	 * スレッドデータ、コメントデータ取得
	*/
	class thread_list
	{

		private  $_data				= null;
		private  $_thread_id		= null;
		private  $_commentsNo		= array();
		public static $_generateds	= array();
		public static $_threads_no	= null;
		public static $_threads_allno	= null;

		/* 検索に使用 (スレッド)
		 */
		private static $_tableClms	= array(
				"thread_id"		=> "thread_id",
				"thread_subject"	=> "thread_subject",
				"thread_body"	=> "thread_body",
				"writedate"	=> "writedate",
				"status"	=> "status",
		);

		/* 検索に使用 (コメント)
		 */
		private static $_tableClms_comment	= array(
				"thread_id"		=> "thread_id",
				"comment_id"	=> "comment_id",
				"comment_body"	=> "comment_body",
				"writedate"	=> "writedate",
				"status"	=> "status",
		);

		public function __construct($row = NULL){
			if(is_array($row)) $this->_data = $row;
		}

		// オブジェクトの指定フィールドの情報を取得
		public function getProp($key){
			return $this->_data[$key];
		}

		// スレッド一覧からコメントページへのリンクを取得
		public function getUrl(){
			$str[] = "thid=".$this->_thread_id;
			return URL.self::THREAD_DETAIL_URL."?".implode("&", $str);
		}
		// 画像リンクURLの取得
		public function getPicsUrl($key){
			if($this->_data[$key]){
				return URL.self::THREAD_DETAIL_PICS_URL."?img_name=".$this->_data[$key];
			}
		}
		// サムネイル表示URLの取得
		public function getThumPicsUrl($key){
			$thumnailname = $this->_data[$key];
			return URL.self::THREAD_DETAIL_PICS_URL."?img_name=".$thumnailname."&s=1";
		}

		public function setThreadid($_thread_id){
			if ($this->_thread_id != 0) throw Exception("thread is already set.");
			$this->_thread_id = $_thread_id;
			if (!isset(self::$_generateds[$_thread_id])) {
				self::$_generateds[$_thread_id]	= $this;
			}
		}
		// スレッドIDを配列で取得
		public static function getThidArray(){
			return array_keys(self::$_generateds);
		}

		public function setThreadsNo($_threads_no){
			self::$_threads_no = $_threads_no;
		}

		// スレッド番号取得
		public static function getThreadsNo(){
			return self::$_threads_no;
		}

		public function setThreadsAllNo($_threads_allno){
			self::$_threads_allno = $_threads_allno;
		}

		// スレッドの全件数取得
		public static function getThreadsAllNo(){
			return self::$_threads_allno;
		}

		public function setCommentsNo($_commentsNo){
			$this->_commentsNo[] = $_commentsNo;
		}

		// 取得データソート用定数
		const ORDER_TIME_DESC	= 1;
		const ORDER_TIME_ASC	= 2;
		const ORDER_RANDOM		= 3;

		// URL
		const THREAD_DETAIL_URL	= "thread_detail.php";
		const THREAD_DETAIL_PICS_URL	= "img.php";

		/* スレッド情報取得
		 *
		 */
		public static function getThreadList($dbh, $limit = NULL, array $searchArr = NULL, $order = NULL){

			$where = array();
			$clms = self::$_tableClms;
			if($searchArr){
				foreach ( $searchArr as $Clms => $val ) {
					$sub_query = array();
					if( isset($clms[$Clms]) ) {

						if ( !is_array($val) ) $val = array($val);

						// スレッドIDで検索(数字のみ入る)
						if ( $Clms == "thread_id" ) {
							$where[] = "thread_id IN (".implode(",",$val).")";
						}
						// スレッドタイトルで検索(文字列が入る)
						if ( $Clms == "thread_subject" ) {
							foreach ($val as $subjectword){
								if ( $subjectword ) $sub_query[] = "thread_subject LIKE '%".a($subjectword)."%'";
							}
							$where[] = implode("", $sub_query);
						}
						// ステータスで検索(数字のみ入る)
						if ( $Clms == "status" ) {
							$where[] = "status IN (".implode("','",$val ).")";
						}
					}
				}
			}

			$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM bbs WHERE ".implode(" AND ", $where );

			switch($order) {
				case self::ORDER_TIME_DESC :
					$sql .= " ORDER BY bbs.writedate DESC "; break;
				case self::ORDER_TIME_ASC :
					$sql .= " ORDER BY bbs.writedate ASC "; break;
				case self::ORDER_RANDOM :
					$sql .= " ORDER BY RAND() "; break;
			}

			if($limit){
				$sql .= " LIMIT ".$limit;
			}

//			echo $sql."<br>";
			$stmt = $dbh->prepare($sql);
			if($stmt->execute()){
				$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt = NULL;
				$sql = "SELECT FOUND_ROWS()";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$rowAll = $stmt->fetch(PDO::FETCH_NUM);

				$rows = count($row);
				$lists	= array();
				if($row){
					for ( $i=0; $i<$rows; $i++ ) {
						$thv = new thread_list($row[$i]);
						//
						$lists[$row[$i]['thread_id']] = $thv;
						$thv->setThreadid($row[$i]['thread_id']);
					}

					$thv->setThreadsNo($rows);
					$thv->setThreadsAllNo($rowAll);
				}

				return $lists;
			}else{
				return false;
			}
		}

		/* スレッドコメント取得
		 *
		*/
		public static function getCommentList($dbh, $limit= NULL, array $searchArr, $order = NULL ){

			$where = array();
			$clms = self::$_tableClms_comment;
			if($searchArr){
				foreach ( $searchArr as $Clms => $val ) {

					if( isset($clms[$Clms]) ) {
						//
						if ( !is_array($val) ) $val = array($val);
						// スレッドIDで検索(数字のみ入る)
						if ( $Clms == "thread_id" ) {
							$where[] = "thread_id IN ('".implode(",",$val)."')";
						}
						// コメントIDで検索(数字のみ入る)
						if ( $Clms == "comment_id" ) {
							$where[] = "comment_id IN (".implode(",",$val).")";
						}
						// コメント文を検索(文字列が入る)
						if ( $Clms == "comment_body" ) {
							$where[] = "comment_body LIKE "." '%".implode( "','", a($val) )."%' ";
						}
						// ステータスを検索(数字のみ入る)
						if ( $Clms == "status" ) {
							$where[] = "status IN (".implode("','",$val ).")";
						}
					}
				}
			}

			$sql = "SELECT * FROM bbs_comment WHERE ".implode(" AND ", $where );

			switch($order) {
				case self::ORDER_TIME_DESC :
					$sql .= " ORDER BY bbs_comment.writedate DESC "; break;
				case self::ORDER_TIME_ASC :
					$sql .= " ORDER BY bbs_comment.writedate ASC "; break;
				case self::ORDER_RANDOM :
					$sql .= " ORDER BY RAND() "; break;
			}

			if($limit){
				$sql .= " LIMIT ".$limit;
			}

			// 確認用
//			echo $sql."<br>";
			$stmt = $dbh->prepare($sql);
			if($stmt->execute()){
				$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$rows = count($row);

				$lists	= array();
				for ( $i=0; $i<$rows; $i++ ) {
					$thv = new thread_list($row[$i]);
					$lists[$row[$i]['comment_id']] = $thv;
					$thv->setThreadid($row[$i]['thread_id']);
				}

				return  $lists;
			}else{
				return false;
			}
		}

		/* スレッドデータを登録
		 */
		public static function insertBody($dbh,$input){

			$sql = 'INSERT INTO bbs (
			thread_subject,
			writedate
			)
			VALUES(
			?,
			NOW()
			)';

			$i = 1;
			$stmt = $dbh->prepare($sql);
			$stmt->bindValue($i++,$input["subjectInput"]);

			if($stmt->execute()){
				$lastId = $dbh->lastInsertId();
				return $lastId;
			}else{
				return false;
			}
		}

		/* コメントデータを登録
		 */
		public static function insertCommentBody($dbh,$input){

			$sql = 'INSERT INTO bbs_comment (
			thread_id,
			comment_body,
			writedate,
			pic_name,
			new_thread
			)VALUES (
			?,
			?,
			NOW(),
			?,
			?
			)';

			$i = 1;
			$stmt = $dbh->prepare($sql);
			$stmt->bindValue($i++,$input["thread_id"]);
			$stmt->bindValue($i++,$input["bodyInput"]);
			$stmt->bindValue($i++,$input["file_name"]);
			$stmt->bindValue($i++,$input["new_thread"]);

			if($stmt->execute()){
				$lastId = $dbh->lastInsertId();
				return $lastId;
			}else{
				return false;
			}
		}


		/* スレッド一覧でのページング用リンク取得
		 */
		public static function getPagingUrl($dbh,$searchArr){

			$currentPageNo = $searchArr["currentPageNo"];
			$threadsNo     = $searchArr["threadsNo"];

			for ($i=1;$i<=$threadsNo + 1;$i++){
				$numArray[] = $i;
			}
			// 全スレッド件数を表示数で割って切り上げしたものをページ件数とする
			$threadsAllNo = ceil($threadsNo / PAGEBASENUM);
			$srtCurrent[] = "[ ".$currentPageNo." / ".$threadsAllNo. "] ";

			$srtPageEject["back_five"][] = "<a href="."\"";
			$srtPageEject["back_five"][] = "thread_list.php?page=";
			if($currentPageNo - 5 <= 0){
				$pagingNum = 1;
			}else{
				$pagingNum = $currentPageNo - 5;
			}

			$srtPageEject["back_five"][] = $pagingNum;
			$srtPageEject["back_five"][] = "\">";
			$srtPageEject["back_five"][] = "<<5";
			$srtPageEject["back_five"][] = "</a>";
			$pagingNum = $currentPageNo;

			$srtPageEject["back_one"][] = "<a href="."\"";
			$srtPageEject["back_one"][] = "thread_list.php?page=";
			if($currentPageNo - 1 <= 0){
				$pagingNum = 1;
			}else{
				$pagingNum = $currentPageNo - 1;
			}
			$srtPageEject["back_one"][] = $pagingNum;
			$srtPageEject["back_one"][] = "\">";
			$srtPageEject["back_one"][] = "<1";
			$srtPageEject["back_one"][] = "</a>";
			$pagingNum = $currentPageNo;

			$srtPageEject["next_five"][] = "<a href="."\"";
			$srtPageEject["next_five"][] = "thread_list.php?page=";
			if($currentPageNo + 5 >= $threadsAllNo){
				$pagingNum = $threadsAllNo;
			}else{
				$pagingNum = $currentPageNo + 5;
			}

			$srtPageEject["next_five"][] = $pagingNum;
			$srtPageEject["next_five"][] = "\">";
			$srtPageEject["next_five"][] = ">>5";
			$srtPageEject["next_five"][] = "</a>";
			$pagingNum = $currentPageNo;

			$srtPageEject["next_one"][] = "<a href="."\"";
			$srtPageEject["next_one"][] = "thread_list.php?page=";
			if($currentPageNo + 1 >= $threadsAllNo){
				$pagingNum = $threadsAllNo;
			}else{
				$pagingNum = $currentPageNo + 1;
			}
			$srtPageEject["next_one"][] = $pagingNum;
			$srtPageEject["next_one"][] = "\">";
			$srtPageEject["next_one"][] = " 1>";
			$srtPageEject["next_one"][] = "</a>";
			$pagingNum = $currentPageNo;


			$pagingUrl["pageeject"]["back_five"] = implode("", $srtPageEject["back_five"]);
			$pagingUrl["pageeject"]["back_one"] = implode("", $srtPageEject["back_one"]);
			$pagingUrl["pageeject"]["next_one"] = implode("", $srtPageEject["next_one"]);
			$pagingUrl["pageeject"]["next_five"] = implode("", $srtPageEject["next_five"]);

			$pagingUrl["current"]["current"] = implode("", $srtCurrent);

			for ($i=0;$i< 5;$i++){

				$srtPagelist[] = "<a href="."\"";
				$srtPagelist[] = "thread_list.php?page=";
				$srtPagelist[] = $currentPageNo + $i;
				$srtPagelist[] = "\">";
				$srtPagelist[] = $currentPageNo + $i;
				$srtPagelist[] = "</a>";

				$pagingUrl["pagelist"][] = implode("", $srtPagelist);
				$srtPagelist = "";
			}

			return $pagingUrl;
		}
	}
?>