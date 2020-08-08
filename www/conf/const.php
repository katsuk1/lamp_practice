<?php
// ドキュメントルートとmodelフォルダへのパス
define('MODEL_PATH', $_SERVER['DOCUMENT_ROOT'] . '/../model/');
// ドキュメントルートとviewフォルダへのパス
define('VIEW_PATH', $_SERVER['DOCUMENT_ROOT'] . '/../view/');

// 画像フォルダのパス
define('IMAGE_PATH', '/assets/images/');
// CSSフォルダのパス
define('STYLESHEET_PATH', '/assets/css/');
// ドキュメントルートと画像フォルダのパス
define('IMAGE_DIR', $_SERVER['DOCUMENT_ROOT'] . '/assets/images/' );

// DBのホスト名
define('DB_HOST', 'mysql');
// DB名
define('DB_NAME', 'sample');
// DBのユーザー名
define('DB_USER', 'testuser');
// DBのパスワード
define('DB_PASS', 'password');
// DBの文字コード
define('DB_CHARSET', 'utf8');

// サインアップページのURL
define('SIGNUP_URL', '/signup.php');
// ログインページのURL
define('LOGIN_URL', '/login.php');
// ログアウトページのURL
define('LOGOUT_URL', '/logout.php');
// 商品一覧ページのURL
define('HOME_URL', '/index.php');
// カートページのURL
define('CART_URL', '/cart.php');
// 購入完了ページのURL
define('FINISH_URL', '/finish.php');
// 商品管理ページのURL
define('ADMIN_URL', '/admin.php');
// 購入履歴ページのURL
define('HISTORY_URL', '/history.php');
// 購入明細ページのURL
define('PURCHASE_DETAIL_URL', '/parchase_detail.php');

// 半角英数字の正規表現
define('REGEXP_ALPHANUMERIC', '/\A[0-9a-zA-Z]+\z/');
// 正の整数の正規表現
define('REGEXP_POSITIVE_INTEGER', '/\A([1-9][0-9]*|0)\z/');

// ユーザー名の最小値
define('USER_NAME_LENGTH_MIN', 6);
// ユーザー名の最大値
define('USER_NAME_LENGTH_MAX', 100);
// パスワードの最小値
define('USER_PASSWORD_LENGTH_MIN', 6);
// パスワードの最大値
define('USER_PASSWORD_LENGTH_MAX', 100);

// 管理ユーザーのユーザータイプ
define('USER_TYPE_ADMIN', 1);
// 一般ユーザーのユーザータイプ
define('USER_TYPE_NORMAL', 2);

// 商品名の最小値
define('ITEM_NAME_LENGTH_MIN', 1);
// 商品名の最大値
define('ITEM_NAME_LENGTH_MAX', 100);

// 公開ステータス
define('ITEM_STATUS_OPEN', 1);
// 非公開ステータス
define('ITEM_STATUS_CLOSE', 0);

// 公開ステータスの連想配列
define('PERMITTED_ITEM_STATUSES', array(
  'open' => 1,
  'close' => 0,
));

// 画像拡張子の連想配列
define('PERMITTED_IMAGE_TYPES', array(
  IMAGETYPE_JPEG => 'jpg',
  IMAGETYPE_PNG => 'png',
));