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

// 商品を購入し、在庫数の更新、カートから削除
if(purchase_carts($db, $carts) === false){
  set_error('商品が購入できませんでした。');
  // 失敗した場合、カートページへリダイレクト
  redirect_to(CART_URL);
} 

// カート内の合計価格を取得
$total_price = sum_carts($carts);

// ビューの読み込み
include_once '../view/finish_view.php';