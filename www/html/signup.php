<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';

// ログインチェックを行うため、セッションを開始する
session_start();

// ログインチェック関数を利用
if(is_logined() === true){
  // ログインしている場合は商品一覧ページにリダイレクト
  redirect_to(HOME_URL);
}

// トークンを生成し、セッション変数に設定
$token = get_csrf_token();

// ビューを読み込み
include_once VIEW_PATH . 'signup_view.php';



