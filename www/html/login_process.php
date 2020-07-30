<?php
// 定数ファイルの読み込み
require_once '../conf/const.php';
// 汎用関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルの読み込み
require_once MODEL_PATH . 'user.php';

// ログインチェックのため、セッションを開始
session_start();

// ログインチェック
if(is_logined() === true){
  // ログインしていれば商品一覧ページへリダイレクト
  redirect_to(HOME_URL);
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

// postで送信されたユーザー名を取得
$name = get_post('name');
// postで送信されたパスワードを取得
$password = get_post('password');

// PDOを取得
$db = get_db_connect();

// ログインし、セッション変数にユーザーIDをセット
$user = login_as($db, $name, $password);
if( $user === false){
  set_error('ログインに失敗しました。');
  // ログインに失敗した場合、ログインページへリダイレクト
  redirect_to(LOGIN_URL);
}

set_message('ログインしました。');
if ($user['type'] === USER_TYPE_ADMIN){
  // ログインしたユーザーが管理ユーザーであれば商品管理ページへリダイレクト
  redirect_to(ADMIN_URL);
}
// ログインに成功した場合、商品一覧ページへリダイレクト
redirect_to(HOME_URL);