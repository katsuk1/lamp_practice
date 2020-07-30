<?php 
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// dbデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'db.php';

/**
 * ログインユーザーのカートデータを配列で取得
 * 
 * @param obj $db PDO
 * @param int $user_id ユーザーID
 * @return array 結果配列データ 
 */
function get_user_carts($db, $user_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = :user_id
  ";
  $params = array(':user_id' => $user_id);
  return fetch_all_query($db, $sql, $params);
}

/**
 * 指定のユーザーのカート内の指定の商品のデータを取得
 * 
 * @param obj $db PDO
 * @param int $user_id ユーザーID
 * @param int $item_id 商品ID
 * @return array 結果配列データ 
 */
function get_user_cart($db, $user_id, $item_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = :user_id
    AND
      items.item_id = :item_id
  ";
  $params = array(':user_id' => $user_id, ':item_id' => $item_id);
  return fetch_query($db, $sql, $params);

}

/**
 * カートに商品を追加
 * 
 * 商品データを取得し、商品が登録されていなければcartテーブルに登録
 * 登録されていれば、購入数量を+1
 * 
 * @param obj $db PDO
 * @param int $user_id ユーザーID
 * @param int $item_id 商品ID
 * @return bool 成功すればtrue
 */
function add_cart($db, $user_id, $item_id ) {
  $cart = get_user_cart($db, $user_id, $item_id);
  if($cart === false){
    return insert_cart($db, $user_id, $item_id);
  }
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

/**
 * カートに商品を追加
 * 
 * @param obj $db PDO
 * @param int $user_id ユーザーID
 * @param int $item_id 商品ID
 * @param int $amount 購入商品数
 * @return bool クエリ実行結果
 */
function insert_cart($db, $user_id, $item_id, $amount = 1){
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES(:item_id, :user_id, :amount)
  ";
  $params = array(':item_id' => $item_id, ':user_id' => $user_id, ':amount' => $amount);
  return execute_query($db, $sql, $params);
}

/**
 * カート内の商品数量を更新
 * 
 * @param obj $db PDO
 * @param int $cart_id カートID
 * @param int $amount 購入数
 * @return bool 成功した場合true,失敗した場合false
 */
function update_cart_amount($db, $cart_id, $amount){
  $sql = "
    UPDATE
      carts
    SET
      amount = :amount
    WHERE
      cart_id = :cart_id
    LIMIT 1
  ";
  $params = array(':amount' => $amount, ':cart_id' => $cart_id);
  //dd($params);
  return execute_query($db, $sql, $params);
}

/**
 * 指定のカートの商品を削除
 * 
 * @param obj $db PDO
 * @param int $cart_id カートID
 * @return bool 成功すればtrue,失敗すればfalse
 */
function delete_cart($db, $cart_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = :cart_id
    LIMIT 1
  ";
  $params = array(':cart_id' => $cart_id);
  return execute_query($db, $sql, $params);
}

/**
 * カート内の商品を購入
 * 
 * 購入商品のバリデーション、itemsテーブルの在庫数更新、カート削除
 * @param obj $db PDO
 * @param array $carts カート内商品データ
 */
function purchase_carts($db, $carts){
  if(validate_cart_purchase($carts) === false){
    return false;
  }
  foreach($carts as $cart){
    if(update_item_stock(
        $db, 
        $cart['item_id'], 
        $cart['stock'] - $cart['amount']
      ) === false){
      set_error($cart['name'] . 'の購入に失敗しました。');
    }
  }
  
  delete_user_carts($db, $carts[0]['user_id']);
}

/**
 * 指定のユーザーのカート内商品を全て削除
 * 
 * @param obj $db PDO
 * @param int $user_id ユーザーID
 */
function delete_user_carts($db, $user_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = :user_id
  ";
  $params = array(':user_id' => $user_id);
  execute_query($db, $sql, $params);
}

/**
 * カート内商品の合計価格を算出
 * 
 * @param array $carts カート内の商品データ
 * @return int $total_price カート内商品の合計価格
 */
function sum_carts($carts){
  $total_price = 0;
  foreach($carts as $cart){
    $total_price += $cart['price'] * $cart['amount'];
  }
  return $total_price;
}

/**
 * 購入商品のバリデーション
 * 
 * 購入商品の空チェック、ステータスチェック、在庫チェック、エラーチェック
 */
function validate_cart_purchase($carts){
  if(count($carts) === 0){
    set_error('カートに商品が入っていません。');
    return false;
  }
  foreach($carts as $cart){
    if(is_open($cart) === false){
      set_error($cart['name'] . 'は現在購入できません。');
    }
    if($cart['stock'] - $cart['amount'] < 0){
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  if(has_error() === true){
    return false;
  }
  return true;
}

