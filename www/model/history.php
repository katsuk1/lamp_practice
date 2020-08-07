<?php 
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// dbデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'db.php';

/**
 * ログインユーザーの購入履歴データを配列で取得
 * 
 * @param obj $db PDO
 * @param int $user_id ユーザーID
 * @return array 結果配列データ 
 */
function get_user_histories($db, $user_id){
  $sql = "
    SELECT
      history_id,
      total_price,
      created
    FROM
      purchase_histories
    WHERE
      user_id = :user_id
    ORDER BY
      created DESC
  ";
  $params = array(':user_id' => $user_id);
  return fetch_all_query($db, $sql, $params);
}

/**
 * 全ての購入履歴データを配列で取得
 * 
 * @param obj $db PDO
 * @return array 結果配列データ 
 */
function get_all_histories($db){
  $sql = "
    SELECT
      history_id,
      total_price,
      purchase_histories.created,
      name
    FROM
      purchase_histories
    JOIN
      users
    ON
      purchase_histories.user_id = users.user_id
    ORDER BY
      purchase_histories.created DESC
  ";
  return fetch_all_query($db, $sql);
}


/**
 * 指定の注文番号の購入明細データを配列で取得
 * 
 * @param obj $db PDO
 * @param int $history_id 注文ID
 * @return array 結果配列データ 
 */
function get_history_details($db, $history_id){
  $sql = "
    SELECT
      purchase_details.item_id,
      purchase_details.price,
      purchase_details.amount,
      purchase_details.sub_total,
      items.name
    FROM
      purchase_details
    JOIN
      items
    ON
      purchase_details.item_id = items.item_id
    WHERE
      purchase_details.history_id = :history_id
  ";
  $params = array(':history_id' => $history_id);
  //var_dump($params);
  return fetch_all_query($db, $sql, $params);
}

/**
 * 購入履歴テーブルに購入履歴を登録
 * 
 * @param obj $db PDO
 * @param int $user_id ユーザーID
 * @param int $total_price カート内商品合計金額
 * @return bool クエリ実行結果
 */
function insert_purchase_history($db, $user_id, $total_price){
  $sql = "
    INSERT INTO
      purchase_histories(
        user_id,
        total_price
      )
    VALUES(:user_id, :total_price);
  ";
  $params = array(':user_id' => $user_id, ':total_price' => $total_price);
  return execute_query($db, $sql, $params);
}

/**
 * 購入明細テーブルに購入明細を登録
 * 
 * @param obj $db PDO
 * @param int $history_id 購入履歴ID
 * @param int $amount 購入数量
 * @param int $price 価格
 * @param int $sub_total 商品ごとの小計
 * @return bool クエリ実行結果
 */
function insert_purchase_detail($db, $history_id, $item_id, $amount, $price, $sub_total){
  $sql = "
    INSERT INTO
      purchase_details(
        history_id,
        item_id,
        amount,
        price,
        sub_total
      )
    VALUES(:history_id, :item_id, :amount, :price, :sub_total);
  ";
  $params = array(':history_id' => $history_id, ':item_id' => $item_id, ':amount' => $amount, ':price' => $price, ':sub_total' => $sub_total);
  return execute_query($db, $sql, $params);
}

/**
 * 購入明細テーブルに購入明細を購入商品の数だけ登録
 * 
 * @param obj $db PDO
 * @param int $history_id 購入履歴ID
 * @param array $carts カート内商品データ配列
 * @return bool クエリ実行結果
 */
function for_insert_purchase_detail($db, $history_id, $carts){
  foreach($carts as $cart){
    if(insert_purchase_detail(
      $db,
      $history_id, 
      $cart['item_id'], 
      $cart['amount'], 
      $cart['price'], 
      $cart['price'] * $cart['amount']
      ) === false){
      return false;
    }
  }
  return true;
}