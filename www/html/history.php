<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';
// historyデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'history.php';

// ログインチェックを行うため、セッションを開始する
session_start();

// ログインチェック用関数を利用
if(is_logined() === false){
  // ログインしていない場合はログインページにリダイレクト
  redirect_to(LOGIN_URL);
}

// PDOを取得
$db = get_db_connect();

// PDOを利用してログインユーザーのデータを取得
$user = get_login_user($db);

// ログインユーザーの購入履歴データを取得
if(is_admin($user) === true){
  // 管理ユーザーであれば全ユーザーの購入履歴データを取得
  $histories = get_all_histories($db);
  //var_dump($histories);
} else {
  // 一般ユーザーであればログインユーザーの購入履歴データを取得
  $histories = get_user_histories($db, $user['user_id']);
}

// トークンを生成し、セッション変数に設定
$token = get_csrf_token();

// ビューを読み込み
include_once VIEW_PATH . 'history_view.php';