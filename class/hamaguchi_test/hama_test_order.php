<?php 
	class HamaTestOrder { 
		public $id;		// id
		public $name;		// 商品名aaa
		public $price;		// 売価
		public $priceStart;		// 売価（始）
		public $priceEnd;			// 売価（終）
		public $color;		// カラー
		public $salesDate;		// 販売日
		public $salesDateStart;		// 販売日（始）
		public $salesDateEnd;			// 販売日（終）
		public $sortKey;			// ソートキー
		public $sortOrder;			// ソート順序
		public $page;				// ページ番号
		public $offset;				// 件数
		public $hamaTestOrderData;		// 濱口テスト用オーダー
		public $hamaTestOrderDataCount;	// 濱口テスト用オーダー全件数
		public $DBOperation;		// データベース
		
		/*
		@brief  コンストラクタ
		*/
		function __construct($request){
			// リクエスト情報をセット
			$this->setRequest($request);
			// データベースの接続
			$this->DBOperation = new DBOperationClass(MODE_IS_MYSQL,DEFAULT_DB_COMMON,DEFAULT_DB_ID,DEFAULT_DB_PASS);
		}

		/*
		@brief  デストラクタ
		*/
		function __destruct(){
		}

		/*
		@brief  リクエスト情報をメンバー変数へセットする
		*/
		public function setRequest($request){

			// id
			if(isset($request['id'])){
				$this->id = $request['id'];
			}
			// 商品名
			if(isset($request['name'])){
				$this->name = $request['name'];
			}
			// 売価
			if(isset($request['price'])){
				$this->price = $request['price'];
			}
			// 売価（始）
			if(isset($request['price_start'])){
				$this->priceStart = $request['price_start'];
			}
			// 売価（終）
			if(isset($request['price_end'])){
				$this->priceEnd = $request['price_end'];
			}
			// カラー
			if(isset($request['color'])){
				$this->color = $request['color'];
			}
			// 販売日
			if(isset($request['sales_date'])){
				$this->salesDate = $request['sales_date'];
			}
			// 販売日（始）
			if(isset($request['sales_date_start'])){
				$this->salesDateStart = $request['sales_date_start'];
			}
			// 販売日（終）
			if(isset($request['sales_date_end'])){
				$this->salesDateEnd = $request['sales_date_end'];
			}
			// ソートキー
			if(isset($request['sort'])){
				$this->sortKey = $request['sort'];
			}
			// ソート順序
			if(isset($request['order'])){
				$this->sortOrder = $request['order'];
			}
			// ページ番号
			if(isset($request['page'])){
				$this->page = $request['page'];
			}
			// 件数
			if(isset($request['offset'])){
				$this->offset = $request['offset'];
			}
		}

		/*
		@brief  濱口テスト用オーダーをメンバー変数へセットする
		*/
		public function makeHamaTestOrderData(){
			
			$this->ErrorCode = "";
			$paramsArray = array();
			$subSql = null;
			$sortSql = null;
			$limitSql = null;

			// id
			if(isset($this->id)){
				$subSql .= " and id = ? ";
				$paramsArray[]	 = $this->id;
			}

			// 商品名
			if(isset($this->name)){
				$subSql .= " and name like ? ";
				$paramsArray[]	 = '%'.$this->name.'%';
			}

			// 売価
			if(isset($this->price)){
				$subSql .= " and price = ? ";
				$paramsArray[]	 = $this->price;
			}

			// 売価（範囲指定）
			if(isset($this->priceStart) and isset($this->priceEnd)){
				$subSql .= " and price >= ? and  price <= ? ";
				$paramsArray[]	 = $this->priceStart;
				$paramsArray[]	 = $this->priceEnd;
			}

			// カラー
			if(isset($this->color)){
				$subSql .= " and color = ? ";
				$paramsArray[]	 = $this->color;
			}

			// 販売日
			if(isset($this->salesDate)){
				$subSql .= " and sales_date = ? ";
				$paramsArray[]	 = $this->salesDate;
			}

			// 販売日（範囲指定）
			if(isset($this->salesDateStart) and isset($this->salesDateEnd)){
				$subSql .= " and sales_date >= ? and  sales_date <= ? ";
				$paramsArray[]	 = $this->salesDateStart;
				$paramsArray[]	 = $this->salesDateEnd;
			}

			// ソート
			if(isset($this->sortKey)){
				$sortSql = 'order by '.$this->sortKey.'';
				if(isset($this->sortOrder)){
					$sortSql .= ' '.$this->sortOrder.' ';
				}else{
					$sortSql .= ' asc ';
				}
			}
			// 件数
			if(isset($this->page) && isset($this->offset)){
				$limitSql = 'limit '.(($this->page*$this->offset)-$this->offset).', '.$this->offset.' ';
			}
			// SQL生成
			$sql= " select "
				. "  id "
				. "  ,name "
				. "  ,price "
				. "  ,color "
				. "  ,sales_date "
				. " from hama_test_order ";
			$sql.= " where id <> '' "
				. $subSql
				. $sortSql
				. $limitSql;

			if(!$this->DBOperation->Execute($sql, $paramsArray, $clsResultSet)){
				if($this->DBOperation->GetDbConnectStatus() === false){
					$this->ErrorCode = "hama_test_order_db_connect_error";
				}else{
					$this->ErrorCode = "hama_test_order_select_sql_error";
				}
				return $this->ErrorCode;
			}else{
				$this->hamaTestOrderData = $clsResultSet;
				$sqlCount= " select "
				. " 	count(id) as count "
				. " from hama_test_order ";
				$sqlCount.= " where id <> '' "
				. $subSql;
				if(!$this->DBOperation->Execute($sqlCount, $paramsArray, $clsResultSet)){
					if($this->DBOperation->GetDbConnectStatus() === false){
						$this->ErrorCode = "hama_test_order_db_connect_error";
					}else{
						$this->ErrorCode = "hama_test_order_select_count_sql_error";
					}
					return $this->ErrorCode;
				}else{
					$this->hamaTestOrderDataCount = $clsResultSet[0]['count'];
				}
				return true;
			}
		}

		/*
		@brief 濱口テスト用オーダーを返却する
		*/
		public function getHamaTestOrderData(){
			return $this->hamaTestOrderData;
		}

		/*
		@brief 濱口テスト用オーダーの全件数を返却する
		*/
		public function getHamaTestOrderDataCount(){
			return $this->hamaTestOrderDataCount;
		}

		/*
		@brief  濱口テスト用オーダーを更新する
		*/
		public function updateHamaTestOrderData(){

			$paramsArray	=	array();

			// チェック処理
			if($this->checkHamaTestOrderData() === false){
				$this->ErrorCode = "hama_test_order_update_check_error";
				return $this->ErrorCode;
			}
			if(isset($this->name)){
				$paramsArray[]	 = $this->name;
			}
			if(isset($this->price)){
				$paramsArray[]	 = $this->price;
			}
			if(isset($this->color)){
				$paramsArray[]	 = $this->color;
			}
			if(isset($this->salesDate)){
				$paramsArray[]	 = $this->salesDate;
			}
			if(empty($this->id)){
				$this->ErrorCode = "hama_test_order_update_key_error";
				return $this->ErrorCode;
			}
			$paramsArray[]	 = $this->id;
			$sql	=	"update hama_test_order set ";
			$sqlColum = array();
			if(isset($this->name)){
				$sqlColum[]	=	" name = ? ";
			}
			if(isset($this->price)){
				$sqlColum[]	=	" price = ? ";
			}
			if(isset($this->color)){
				$sqlColum[]	=	" color = ? ";
			}
			if(isset($this->salesDate)){
				$sqlColum[]	=	" sales_date = ? ";
			}
			$sql	.=	implode(",",$sqlColum);
			$sql	.=	"where "
						."id = ? ";

			if(!$this->DBOperation->Execute($sql, $paramsArray, $clsResultSet)){
				if($this->DBOperation->GetDbConnectStatus() === false){
					$this->ErrorCode = "hama_test_order_db_connect_error";
				}else{
					$this->ErrorCode = "hama_test_order_update_sql_error";
				}
				return $this->ErrorCode;
			}
			return true;

		}
		/*
		@brief  濱口テスト用オーダーを登録する
		*/
		public function insertHamaTestOrderData(){

			$paramsArray	=	array();

			// チェック処理
			if($this->checkHamaTestOrderData() === false){
				$this->ErrorCode = "hama_test_order_insert_check_error";
				return $this->ErrorCode;
			}
			if(isset($this->name)){
				$paramsArray[]	 = $this->name;
			}
			if(isset($this->price)){
				$paramsArray[]	 = $this->price;
			}
			if(isset($this->color)){
				$paramsArray[]	 = $this->color;
			}
			if(isset($this->salesDate)){
				$paramsArray[]	 = $this->salesDate;
			}
			$sql = " insert hama_test_order ( ";
			$sqlColum	=	array();
			if(isset($this->name)){
				$sqlColum[]	=	" name ";
			}
			if(isset($this->price)){
				$sqlColum[]	=	" price ";
			}
			if(isset($this->color)){
				$sqlColum[]	=	" color ";
			}
			if(isset($this->salesDate)){
				$sqlColum[]	=	" sales_date ";
			}
			$sql	.=	implode(",",$sqlColum);
			$sql	.=	" ) values (";
			$sqlColum	=	array();
			if(isset($this->name)){
				$sqlColum[]	=	" ? ";
			}
			if(isset($this->price)){
				$sqlColum[]	=	" ? ";
			}
			if(isset($this->color)){
				$sqlColum[]	=	" ? ";
			}
			if(isset($this->salesDate)){
				$sqlColum[]	=	" ? ";
			}
			$sql	.=	implode(",",$sqlColum);
			$sql	.=	" ) ";
			
			if(!$this->DBOperation->Execute($sql, $paramsArray, $clsResultSet)){
				if($this->DBOperation->GetDbConnectStatus() === false){
					$this->ErrorCode = "hama_test_order_db_connect_error";
				}else{
					$this->ErrorCode = "hama_test_order_insert_sql_error";
				}
				return $this->ErrorCode;
			}
			return true;
			
		}

		/*
		@brief  濱口テスト用オーダーを削除する
		*/
		public function deleteHamaTestOrderData(){

			$paramsArray	=	array();

			// チェック処理
			if($this->checkHamaTestOrderData() === false){
				return false;
			}
			if(empty($this->id)){
				return false;
			}
			$paramsArray[]	 = $this->id;
			$sql	=	"delete from hama_test_order ";
			$sql	.=	"where "
						."id = ? ";

			if(!$this->DBOperation->Execute($sql, $paramsArray, $clsResultSet)){
				$this->ErrorCode = "hama_test_order_delete_sql_error";
				return false;
			}

			return true;

		}

		/*
		@brief  インサートした自動連番IDを返却する
		*/
		public function getLastInsertId(){
			return $this->DBOperation->GetLastInsertId();
		}

		/*
		@brief  トランザクションを有効にする
		*/
		public function beginTransaction(){
			$this->DBOperation->BeginTransaction();
			return true;
		}

		/*
		@brief  コミットする
		*/
		public function commit(){
			$this->DBOperation->Commit();
			return true;
		}

		/*
		@brief  ロールバックする
		*/
		public function rollBack(){
			$this->DBOperation->RollBack();
			return true;
		}

		/*
		@brief  濱口テスト用オーダーのデータをチェックする
		*/
		public function checkHamaTestOrderData(){

			$checkFlag = true;

			// id
			if(isset($this->id)){
				// 空白チェック
				if(!strlen($this->id)){
					$checkFlag = false;
				}
				// データ長チェック
				if(strlen($this->id) > 11){
					$checkFlag = false;
				}
			}

			// 商品名
			if(isset($this->name)){
				// データ長チェック
				if(strlen($this->name) > 255){
					$checkFlag = false;
				}
			}

			// 売価
			if(isset($this->price)){
				// データ長チェック
				if(strlen($this->price) > 11){
					$checkFlag = false;
				}
			}

			// カラー
			if(isset($this->color)){
				// データ長チェック
				if(strlen($this->color) > 255){
					$checkFlag = false;
				}
			}

			// 販売日
			if(isset($this->salesDate)){
			}

			return $checkFlag;

		}

		/*
		@brief  濱口テスト用オーダーの情報を返却する
		*/
		public function getHamaTestOrderInfomation(){

			$arrayHamaTestOrderInfomation		 = array();

			$arrayHamaTestOrderInfomation['table_physical_name'] = 'hama_test_order';
			$arrayHamaTestOrderInfomation['table_logical_name']	 = '濱口テスト用オーダー';
			$arrayHamaTestOrderInfomation['colum_list'][0]['physical_name']	 = 'id';
			$arrayHamaTestOrderInfomation['colum_list'][0]['logical_name']	 = 'id';
			$arrayHamaTestOrderInfomation['colum_list'][1]['physical_name']	 = 'name';
			$arrayHamaTestOrderInfomation['colum_list'][1]['logical_name']	 = '商品名';
			$arrayHamaTestOrderInfomation['colum_list'][2]['physical_name']	 = 'price';
			$arrayHamaTestOrderInfomation['colum_list'][2]['logical_name']	 = '売価';
			$arrayHamaTestOrderInfomation['colum_list'][3]['physical_name']	 = 'color';
			$arrayHamaTestOrderInfomation['colum_list'][3]['logical_name']	 = 'カラー';
			$arrayHamaTestOrderInfomation['colum_list'][4]['physical_name']	 = 'sales_date';
			$arrayHamaTestOrderInfomation['colum_list'][4]['logical_name']	 = '販売日';
			return $arrayHamaTestOrderInfomation;

		}
	}
?>