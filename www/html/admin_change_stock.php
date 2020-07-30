<?php
// 定数ファイルの読み込み
require_once '../conf/const.php';
// 汎用関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルの読み込み
require_once MODEL_PATH . 'user.php';
// itemデータに関する関数ファイルの読み込み
require_once MODEL_PATH . 'item.php';

// ログインチェックのため、セッション開始
session_start();

// ログインチェック
if(is_logined() === false){
  // ログインしていない場合、ログインページへリダイレクト
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

// ログインユーザーのデータを取得
$user = get_login_user($db);

// 管理ユーザーかチェック
if(is_admin($user) === false){
  // 管理ユーザーでなければログインページへリダイレクト
  redirect_to(LOGIN_URL);
}

// postで送信された商品IDを取得
$item_id = get_post('item_id');
// postで送信された在庫数を取得
$stock = get_post('stock');

// 商品の在庫数を更新
if(update_item_stock($db, $item_id, $stock)){
  set_message('在庫数を変更しました。');
} else {
  set_error('在庫数の変更に失敗しました。');
}

// 商品管理ページへリダイレクト
redirect_to(ADMIN_URL);