<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';
// itemデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'item.php';
// cartデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'cart.php';
// historyデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'history.php';

// ログインチェックを行うため、セッションを開始する
session_start();

// ログインチェック用関数を利用
if(is_logined() === false){
  // ログインしていない場合はログインページへリダイレクト
  redirect_to(LOGIN_URL);
}

// hiddenで送信されたトークンを取得
$token = get_post('csrf_token');
// トークンのチェック
if(is_valid_csrf_token($token) === false){
  // 正しくなければログインページへリダイレクト
  redirect_to(LOGIN_URL);
}
// セッション変数に設定したトークンを削除
unset($_SESSION['csrf_token']);

// PDOを取得
$db = get_db_connect();

// PDOを利用してログインユーザーのデータを取得 
$user = get_login_user($db);

// PDOを利用してログインユーザーのカートデータを取得
$carts = get_user_carts($db, $user['user_id']);

// カート内の合計価格を取得
$total_price = sum_carts($carts);

// トランザクション開始
$db->beginTransaction();

// 購入履歴テーブルに登録
if(insert_purchase_history($db, $user['user_id'], $total_price) === false){
  set_error('商品の購入に失敗しました。');
  // 失敗した場合ロールバック処理
  $db->rollback();
  // 失敗した場合カートページにリダイレクト
  redirect_to(CART_URL);
}

// 最新の購入履歴IDを取得
$history_id = get_last_insert_id($db);

// 購入明細テーブルに登録
if(for_insert_purchase_detail($db, $history_id, $carts) === false){
  set_error('商品の購入に失敗しました。');
  // 失敗した場合ロールバック処理
  $db->rollback();
  // 失敗した場合カートページにリダイレクト
  redirect_to(CART_URL);
}

// 商品を購入し、在庫数の更新、カートから削除
if(purchase_carts($db, $carts) === false){
  set_error('商品が購入できませんでした。');
  // ロールバック処理
  $db->rollback();
  // 失敗した場合カートページにリダイレクト
  redirect_to(CART_URL);
} 
// コミット処理
$db->commit();


// ビューの読み込み
include_once '../view/finish_view.php';