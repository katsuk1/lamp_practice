<?php
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// dbデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'db.php';

// DB利用

/**
 * 指定の商品の商品データを取得
 * 
 * @param obj $db PDO
 * @param int $item_id 商品ID
 * @return array 商品データ配列
 */
function get_item($db, $item_id){
  $sql = "
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
    WHERE
      item_id = :item_id
  ";
  $params = array(':item_id' => $item_id);
  return fetch_query($db, $sql, $params);
}

/**
 * 商品データ全てorステータスが公開の商品データを全て取得
 * 
 * @param obj $db PDO
 * @param bool $is_open フラグ
 * @return array 結果配列データ
 */
function get_items($db, $is_open = false){
  $sql = '
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
  ';
  if($is_open === true){
    $sql .= '
      WHERE status = 1
    ';
  }

  return fetch_all_query($db, $sql);
}

/**
 * 商品データを全て取得
 * 
 * @param obj $db PDO
 * @return array 商品データ配列
 */
function get_all_items($db){
  return get_items($db);
}

/**
 * ステータスが公開の商品データを配列で取得
 * 
 * @param obj $db PDO
 * @return array 結果配列データ
 */
function get_open_items($db){
  return get_items($db, true);
}

/**
 * ランダムな画像ファイル名を取得し、商品の各バリデーション後、itemsテーブルに登録、画像保存
 * 
 * @param obj $db PDO
 * @param str $name 商品名
 * @param int $price 価格
 * @param int $stock 在庫数
 * @param str $status ステータス
 * @param str $image アップロードされた画像ファイル名
 * @param str $filename 生成したランダムなファイル名
 * @return bool 成功すればtrue
 */
function regist_item($db, $name, $price, $stock, $status, $image){
  $filename = get_upload_filename($image);
  if(validate_item($name, $price, $stock, $filename, $status) === false){
    return false;
  }
  return regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename);
}

/**
 * itemsテーブルへの商品データ書き込みと画像ファイル保存のトランザクション
 * 
 * @param obj $db PDO
 * @param str $name 商品名
 * @param int $price 価格
 * @param int $stock 在庫数
 * @param str $status ステータス
 * @param str $image アップロードされた画像ファイル名
 * @param str $filename 生成したランダムなファイル名
 * @return bool 成功すればtrue
 */
function regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename){
  $db->beginTransaction();
  if(insert_item($db, $name, $price, $stock, $filename, $status) 
    && save_image($image, $filename)){
    $db->commit();
    return true;
  }
  $db->rollback();
  return false;
  
}

/**
 * itemsテーブルに商品を登録
 * 
 * @param obj $db PDO
 * @param str $name 商品名
 * @param int $price 価格
 * @param int $stock 在庫数
 * @param str $status ステータス
 * @param str $filename 生成したランダムなファイル名
 * @return bool 成功すればtrue
 */
function insert_item($db, $name, $price, $stock, $filename, $status){
  $status_value = PERMITTED_ITEM_STATUSES[$status];
  $sql = "
    INSERT INTO
      items(
        name,
        price,
        stock,
        image,
        status
      )
    VALUES(:name, :price, :stock, :filename, :status_value);
  ";
  $params = array(':name' => $name, ':price' => $price, ':stock' => $stock, ':filename' => $filename, ':status_value' => $status_value);
  //dd($params);
  return execute_query($db, $sql, $params);
}

/**
 * 商品のステータスを更新
 * 
 * @param obj $db PDO
 * @param int $item_id 商品ID
 * @param int ステータス
 * @return bool 成功すればtrue
 */
function update_item_status($db, $item_id, $status){
  $sql = "
    UPDATE
      items
    SET
      status = :status
    WHERE
      item_id = :item_id
    LIMIT 1
  ";
  $params = array(':status' => $status, ':item_id' => $item_id);
  return execute_query($db, $sql, $params);
}

/**
 * 在庫数を更新
 * 
 * @param obj $db PDO
 * @param int $item_id 商品ID
 * @param int $stock 在庫数
 * @return bool 成功すればtrue,失敗すればfalse
 */
function update_item_stock($db, $item_id, $stock){
  $sql = "
    UPDATE
      items
    SET
      stock = :stock
    WHERE
      item_id = :item_id
    LIMIT 1
  ";
  $params = array(':stock' => $stock, ':item_id' => $item_id);
  return execute_query($db, $sql, $params);
}

/**
 * 商品データ、画像の削除のトランザクション
 * 
 * 指定の商品データを取得し、商品データと画像フォルダの画像ファイルを削除
 * 
 * @param obj $db PDO
 * @param int $item_id 商品ID
 * @return bool コミットに成功すればtrue
 */
function destroy_item($db, $item_id){
  $item = get_item($db, $item_id);
  if($item === false){
    return false;
  }
  $db->beginTransaction();
  if(delete_item($db, $item['item_id'])
    && delete_image($item['image'])){
    $db->commit();
    return true;
  }
  $db->rollback();
  return false;
}

/**
 * itemsテーブルから指定の商品データを削除
 * 
 * @param obj $db PDO
 * @param int $item_id 商品ID
 * @return bool 削除に成功すればtrue
 */
function delete_item($db, $item_id){
  $sql = "
    DELETE FROM
      items
    WHERE
      item_id = :item_id
    LIMIT 1
  ";
  $params = array(':item_id' => $item_id);
  return execute_query($db, $sql, $params);
}


// 非DB

/**
 * ステータスが公開かチェック
 * 
 * @param $item 商品データ
 * @return bool 公開であればtrue
 */
function is_open($item){
  return $item['status'] === 1;
}

/**
 * 商品名、価格、在庫数、ファイル名、ステータスのバリデーション
 * 
 * @param str $name 商品名
 * @param int $price 価格
 * @param int $stock 在庫数
 * @param str $filename ファイル名
 * @param int $status 公開ステータス
 * @return bool 全部正しければtrue
 */
function validate_item($name, $price, $stock, $filename, $status){
  $is_valid_item_name = is_valid_item_name($name);
  $is_valid_item_price = is_valid_item_price($price);
  $is_valid_item_stock = is_valid_item_stock($stock);
  $is_valid_item_filename = is_valid_item_filename($filename);
  $is_valid_item_status = is_valid_item_status($status);

  return $is_valid_item_name
    && $is_valid_item_price
    && $is_valid_item_stock
    && $is_valid_item_filename
    && $is_valid_item_status;
}

/**
 * 商品名のバリデーション
 * 
 * 商品名の文字列チェック
 * 
 * @param str $name 商品名
 * @return bool $is_valid 正しければtrue
 */
function is_valid_item_name($name){
  $is_valid = true;
  if(is_valid_length($name, ITEM_NAME_LENGTH_MIN, ITEM_NAME_LENGTH_MAX) === false){
    set_error('商品名は'. ITEM_NAME_LENGTH_MIN . '文字以上、' . ITEM_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  return $is_valid;
}

/**
 * 価格のバリデーション
 * 
 * 整数の正規表現にマッチするかチェック
 * 
 * @param int $price 商品価格
 * @return bool $is_valid 正の整数であればtrue
 */
function is_valid_item_price($price){
  $is_valid = true;
  if(is_positive_integer($price) === false){
    set_error('価格は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

/**
 * 在庫数のバリデーション
 * 
 * 整数の正規表現にマッチするかチェック
 * 
 * @param int $stock 在庫数
 * @return bool $is_valid 正の整数であればtrue
 */
function is_valid_item_stock($stock){
  $is_valid = true;
  if(is_positive_integer($stock) === false){
    set_error('在庫数は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

/**
 * ファイル名のバリデーション
 * 
 * ファイルが空かチェック
 * 
 * @param str $filename ファイル名
 * @return bool $is_valid 空でなければtrue
 */
function is_valid_item_filename($filename){
  $is_valid = true;
  if($filename === ''){
    $is_valid = false;
  }
  return $is_valid;
}

/**
 * 公開ステータスのバリデーション
 * 
 * 存在するか、存在した場合openまたはcloseかチェック
 * @param str $status 公開ステータス
 * @return bool $is_valid 正しければtrue
 */
function is_valid_item_status($status){
  $is_valid = true;
  if(isset(PERMITTED_ITEM_STATUSES[$status]) === false){
    $is_valid = false;
  }
  return $is_valid;
}