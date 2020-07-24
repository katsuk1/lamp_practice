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
  // 
  $result = regist_user($db, $name, $password, $password_confirmation);
  if( $result=== false){
    set_error('ユーザー登録に失敗しました。');
    redirect_to(SIGNUP_URL);
  }
}catch(PDOException $e){
  set_error('ユーザー登録に失敗しました。');
  redirect_to(SIGNUP_URL);
}

set_message('ユーザー登録が完了しました。');
login_as($db, $name, $password);
redirect_to(HOME_URL);