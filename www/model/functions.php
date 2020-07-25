<?php
/**
 * 変数の中身の詳細をvar_dumpで確認し、それ以降のスクリプトの実行を停止
 * 
 * @param $var 確認したい変数 
 */
function dd($var){
  var_dump($var);
  exit();
}

/**
 * 指定のURLにリダイレクト
 * 
 * @param str $url リダイレクト先のURL
 */
function redirect_to($url){
  header('Location: ' . $url);
  exit;
}

/**
 * getメソッドでデータが送信された場合、データを取得
 * 
 * @param $name getメソッドで送信されたデータ
 * @return getメソッドで送信されたデータ
 */
function get_get($name){
  if(isset($_GET[$name]) === true){
    return $_GET[$name];
  };
  return '';
}

/**
 * postメソッドでデータが送信された場合、データを取得
 * 
 * @param $name postメソッドで送信されたデータ
 * @return postメソッドで送信されたデータ
 */
function get_post($name){
  if(isset($_POST[$name]) === true){
    return $_POST[$name];
  };
  return '';
}

/**
 * ファイルが送信された場合、データを取得
 * 
 * @param $name 送信されたファイル
 * @return 送信されたファイル
 */
function get_file($name){
  if(isset($_FILES[$name]) === true){
    return $_FILES[$name];
  };
  return array();
}

/**
 * セッション変数がセットされている場合、セッション変数を取得
 * 
 * セッション変数がセットされていない場合、空で返す。
 * 
 * @param str $name ユーザーID
 * @return str セットされているセッション変数
 */
function get_session($name){
  if(isset($_SESSION[$name]) === true){
    return $_SESSION[$name];
  };
  return '';
}

/**
 * セッション変数をセット
 * 
 * @param $name セッション変数名
 * @param $value  セッション変数にセットする値
 */
function set_session($name, $value){
  $_SESSION[$name] = $value;
}

/**
 * セッション変数にエラーを保存
 * 
 * @param str $error エラー文
 */
function set_error($error){
  $_SESSION['__errors'][] = $error;
}

function get_errors(){
  $errors = get_session('__errors');
  if($errors === ''){
    return array();
  }
  set_session('__errors',  array());
  return $errors;
}

function has_error(){
  return isset($_SESSION['__errors']) && count($_SESSION['__errors']) !== 0;
}

function set_message($message){
  $_SESSION['__messages'][] = $message;
}

function get_messages(){
  $messages = get_session('__messages');
  if($messages === ''){
    return array();
  }
  set_session('__messages',  array());
  return $messages;
}

/**
 * セッション変数を確認し、ログインチェック
 * 
 * @return str セッション変数がセットされていない場合のみ、空で返す
 */
function is_logined(){
  return get_session('user_id') !== '';
}

function get_upload_filename($file){
  if(is_valid_upload_image($file) === false){
    return '';
  }
  $mimetype = exif_imagetype($file['tmp_name']);
  $ext = PERMITTED_IMAGE_TYPES[$mimetype];
  return get_random_string() . '.' . $ext;
}

function get_random_string($length = 20){
  return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
}

function save_image($image, $filename){
  return move_uploaded_file($image['tmp_name'], IMAGE_DIR . $filename);
}

function delete_image($filename){
  if(file_exists(IMAGE_DIR . $filename) === true){
    unlink(IMAGE_DIR . $filename);
    return true;
  }
  return false;
  
}


/**
 * 文字列の長さのバリデーション
 * 
 * @param $string バリデーションする文字列
 * @param $minimum_length 最小値
 * @param $maximum_length 最大値
 * @return bool 最小値以上最大値以上ならばtrue
 */
function is_valid_length($string, $minimum_length, $maximum_length = PHP_INT_MAX){
  $length = mb_strlen($string);
  return ($minimum_length <= $length) && ($length <= $maximum_length);
}

/**
 * 半角英数字のバリデーション
 * 
 * @param str $string チェック前文字列
 * @return int 正規表現のパターンにマッチした場合は1,しなかった場合は0
 */
function is_alphanumeric($string){
  return is_valid_format($string, REGEXP_ALPHANUMERIC);
}

function is_positive_integer($string){
  return is_valid_format($string, REGEXP_POSITIVE_INTEGER);
}

/**
 * バリデーションのための正規表現フォーマット
 * 
 * @param str $string チェックしたい文字列
 * @param str $format 正規表現
 * @return int 正規表現のパターンにマッチした場合は1,しなかった場合は0
 */
function is_valid_format($string, $format){
  return preg_match($format, $string) === 1;
}


function is_valid_upload_image($image){
  if(is_uploaded_file($image['tmp_name']) === false){
    set_error('ファイル形式が不正です。');
    return false;
  }
  $mimetype = exif_imagetype($image['tmp_name']);
  if( isset(PERMITTED_IMAGE_TYPES[$mimetype]) === false ){
    set_error('ファイル形式は' . implode('、', PERMITTED_IMAGE_TYPES) . 'のみ利用可能です。');
    return false;
  }
  return true;
}


function h($string){
  return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}