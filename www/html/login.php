<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';

// ログインチェックのため、セッションを開始
session_start();

// ログインチェック
if(is_logined() === true){
  // ログインしていれば商品一覧ページへリダイレクト
  redirect_to(HOME_URL);
}

// トークンを生成し、セッション変数に設定
$token = get_csrf_token();

// ビューの読み込み
include_once VIEW_PATH . 'login_view.php';