<?php
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// DBに関する関数ファイルを読み込み
require_once MODEL_PATH . 'db.php';

/**
 * usersテーブルから指定のユーザーIDのデータを取得
 * 
 * @param obj $db PDO
 * @param int $user_id ユーザーID
 * @return array 結果配列データ
 */
function get_user($db, $user_id){
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      user_id = {$user_id}
    LIMIT 1
  ";

  return fetch_query($db, $sql);
}

/**
 * 指定したユーザー名のユーザーデータを配列で取得
 * 
 * @param obj $db PDO
 * @param str $name ユーザー名
 * @return array ユーザー配列データ
 */
function get_user_by_name($db, $name){
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      name = '{$name}'
    LIMIT 1
  ";

  return fetch_query($db, $sql);
}

/**
 * ログインできた場合、セッション変数にユーザーIDをセット
 * 
 * @param obj $db PDO
 * @param str $name ユーザー名
 * @param str $password パスワード
 * @return array $user ユーザー配列データ
 */
function login_as($db, $name, $password){
  $user = get_user_by_name($db, $name);
  if($user === false || $user['password'] !== $password){
    return false;
  }
  set_session('user_id', $user['user_id']);
  return $user;
}

/**
 * ログインユーザーのデータを取得
 * 
 * ログインユーザーのユーザーIDをチェックし、
 * usersテーブルからログインユーザーのデータを取得
 * 
 * @param obj $db PDO
 */
function get_login_user($db){
  $login_user_id = get_session('user_id');

  return get_user($db, $login_user_id);
}

/**
 * ユーザーのバリデーション後、正しければusersテーブルに書き込み
 * 
 * @param obj $db PDO
 * @param str $name ユーザー名
 * @param str $password パスワード
 * @param str $password_confirmation 確認用パスワード
 * @return bool usersデータに書き込めればtrue
 */
function regist_user($db, $name, $password, $password_confirmation) {
  if( is_valid_user($name, $password, $password_confirmation) === false){
    return false;
  }
  
  return insert_user($db, $name, $password);
}

function is_admin($user){
  return $user['type'] === USER_TYPE_ADMIN;
}

/**
 * ユーザー名とパスワードのバリデーション
 * 
 * @param str $name ユーザー名
 * @param str $password パスワード
 * @param str $password_confirmation 確認用パスワード
 * @return bool ユーザー名とパスワードが両方正しければtrue
 */
function is_valid_user($name, $password, $password_confirmation){
  // 短絡評価を避けるため一旦代入。
  $is_valid_user_name = is_valid_user_name($name);
  $is_valid_password = is_valid_password($password, $password_confirmation);
  return $is_valid_user_name && $is_valid_password ;
}

/**
 * ユーザー名のバリデーション
 * 
 * ユーザー名の文字数チェック、半角英数字チェック
 * 
 * @param str $name ユーザー名
 * @return bool $is_valid ユーザー名が正しければtrue
 */
function is_valid_user_name($name) {
  $is_valid = true;
  if(is_valid_length($name, USER_NAME_LENGTH_MIN, USER_NAME_LENGTH_MAX) === false){
    set_error('ユーザー名は'. USER_NAME_LENGTH_MIN . '文字以上、' . USER_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  if(is_alphanumeric($name) === false){
    set_error('ユーザー名は半角英数字で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

/**
 * パスワードのバリデーション
 * 
 * パスワードの文字数チェック、半角英数字チェック、確認用と一致するかチェック
 * 
 * @param str $password パスワード
 * @param str $password_confirmation 確認用パスワード
 * @return bool パスワードが正しいかつ確認用と一致すればtrue
 */
function is_valid_password($password, $password_confirmation){
  $is_valid = true;
  if(is_valid_length($password, USER_PASSWORD_LENGTH_MIN, USER_PASSWORD_LENGTH_MAX) === false){
    set_error('パスワードは'. USER_PASSWORD_LENGTH_MIN . '文字以上、' . USER_PASSWORD_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  if(is_alphanumeric($password) === false){
    set_error('パスワードは半角英数字で入力してください。');
    $is_valid = false;
  }
  if($password !== $password_confirmation){
    set_error('パスワードがパスワード(確認用)と一致しません。');
    $is_valid = false;
  }
  return $is_valid;
}

/**
 * usersテーブルにユーザーデータを書き込み
 * 
 * @param obj $db PDO
 * @param str $name ユーザー名
 * @param str $password パスワード
 * @return bool 実行できればtrue
 */
function insert_user($db, $name, $password){
  $sql = "
    INSERT INTO
      users(name, password)
    VALUES ('{$name}', '{$password}');
  ";

  return execute_query($db, $sql);
}

