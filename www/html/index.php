<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';
// itemデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'item.php';

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

// トータルページ数を取得
$pages_num = get_pages_num($db);

// 現在のページ数を取得
$now = get_now_page();

// itemsテーブルのレコード数を取得
$items_num = count_items_records($db);

// 商品データ取得の開始位置を取得
$start = get_limit_start($now);

// 商品一覧用のデータを取得
$items = pagenation($db);

// トークンを生成し、セッション変数に設定
$token = get_csrf_token();

// ビューを読み込み
include_once VIEW_PATH . 'index_view.php';