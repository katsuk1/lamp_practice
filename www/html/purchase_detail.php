<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';
// itemデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'item.php';
// historyデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'history.php';

// ログインチェックを行うため、セッションを開始する
session_start();

// ログインチェック用関数を利用
if(is_logined() === false){
  // ログインしていない場合はログインページにリダイレクト
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

// hiddenで送信された注文番号を取得
$history_id = get_post('history_id');

// hiddenで送信された購入日時を取得
$created = get_post('created');

// hiddenで送信された合計金額を取得
$total_price = get_post('total_price');

// 購入明細内の商品データを取得
$details = get_purchase_details($db, $history_id);
//var_dump($details);



// ビューを読み込み
include_once VIEW_PATH . 'purchase_detail_view.php';