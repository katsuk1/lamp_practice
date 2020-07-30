<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';

// ログインチェックを行うため、セッションを開始する
session_start();

// ログインチェック関数を利用
if(is_logined() === true){
  // ログインしている場合は商品一覧ページにリダイレクト
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

// ユーザー名を取得
$name = get_post('name');
// パスワードを取得
$password = get_post('password');
// 確認用パスワードを取得
$password_confirmation = get_post('password_confirmation');

// PDOを取得
$db = get_db_connect();

// 例外処理
try{
  // ユーザー名、パスワードのバリデーション、usersテーブルへ書き込み
  $result = regist_user($db, $name, $password, $password_confirmation);
  if( $result=== false){
    set_error('ユーザー登録に失敗しました。');
    // 登録に失敗した場合、ユーザー登録ページにリダイレクト
    redirect_to(SIGNUP_URL);
  }
}catch(PDOException $e){
  set_error('ユーザー登録に失敗しました。');
  // 登録に失敗した場合、ユーザー登録ページにリダイレクト
  redirect_to(SIGNUP_URL);
}

set_message('ユーザー登録が完了しました。');
// ユーザー登録が完了した場合、ログインしてセッション変数にユーザーIDをセット
login_as($db, $name, $password);
// 商品一覧ページにリダイレクト
redirect_to(HOME_URL);