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
 * @param str $name セッション変数名
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
 * セッション変数にエラー文を配列で保存
 * 
 * @param str $error エラー文
 */
function set_error($error){
  $_SESSION['__errors'][] = $error;
}

/**
 * セッションにセットされているエラー文を取得し、セッション変数を削除
 * 
 * @return array $erros エラーメッセージ配列
 */
function get_errors(){
  $errors = get_session('__errors');
  if($errors === ''){
    return array();
  }
  set_session('__errors',  array());
  return $errors;
}

/**
 * セッション変数にエラーが設定されていないかチェック
 * 
 * @return bool セッションにエラーが設定されていればtrue
 */
function has_error(){
  return isset($_SESSION['__errors']) && count($_SESSION['__errors']) !== 0;
}

/**
 * セッション変数にメッセージ配列設定
 * 
 * @param str $message メッセージ
 */
function set_message($message){
  $_SESSION['__messages'][] = $message;
}

/**
 * セッションにセットされているメッセージを取得し、セッション変数を削除
 * 
 * @return array $message メッセージ配列
 */
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

/**
 * アップロードされた画像タイプのバリデーション後、ランダムなファイル名を取得
 * 
 * @param str $file アップロードされた画像ファイル名
 * @return str ランダムなファイル名と拡張子($ext)
 */
function get_upload_filename($file){
  if(is_valid_upload_image($file) === false){
    return '';
  }
  $mimetype = exif_imagetype($file['tmp_name']);
  $ext = PERMITTED_IMAGE_TYPES[$mimetype];
  return get_random_string() . '.' . $ext;
}

/**
 * 20文字のランダムな文字列を取得
 * 
 * uniqidで生成した13文字の文字列をhashハッシュ化
 * ハッシュ化した値をbase_convertで16進数から36進数へ変換
 * 36進数へ変換した値の一部をsubstrで切り取り返す
 * 
 * @param str $length 生成する文字数
 */
function get_random_string($length = 20){
  return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
}

/**
 * アップロードされたファイルを画像ファイルに保存
 * 
 * @param str $image アップロードされたファイル名
 * @param str $filename 生成したランダムな画像ファイル名
 * @return bool 成功すればtrue
 */
function save_image($image, $filename){
  return move_uploaded_file($image['tmp_name'], IMAGE_DIR . $filename);
}

/**
 * 画像ファイルの削除
 * 
 * 画像ファイルが存在するかチェックし、画像ファイルを削除
 * 
 * @param str $filename 画像ファイル名
 * return bool 削除に成功すればtrue
 *  */ 
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

/**
 * 正の整数の正規表現チェック
 * 
 * @param $string チェックしたい文字列
 * return 正規表現にマッチした場合true
 */
function is_positive_integer($string){
  return is_valid_format($string, REGEXP_POSITIVE_INTEGER);
}

/**
 * バリデーションのための正規表現フォーマット
 * 
 * @param str $string チェックしたい文字列
 * @param str $format 正規表現
 * @return bool 正規表現のパターンにマッチした場合true
 */
function is_valid_format($string, $format){
  return preg_match($format, $string) === 1;
}

/**
 * アップロードされた画像のバリデーション
 * 
 * 画像かアップロードされたか確認後、画像ファイル形式を取得
 * ファイル形式がjpgかpngであればtrue
 * 
 * @param str $image アップロードされた画像ファイル名
 * @return bool
 */
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

/**
 * エスケープ処理
 * 
 * @params str $string エスケープ前文字列
 * @return str エスケープ後文字列
 */
function h($string){
  return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * トークンの生成
 * 
 * トークンを生成し、セッション変数に設定
 * 
 * @return str $token ランダム30文字で生成されたトークン
 */
function get_csrf_token(){
  // get_random_string()はユーザー定義関数。
  $token = get_random_string(30);
  // set_session()はユーザー定義関数。
  set_session('csrf_token', $token);
  return $token;
}

/**
 * トークンのチェック
 * 
 * トークンとセッション変数が一致するかチェック
 * 
 * @params str $token トークン
 * @return bool トークンとセッション変数が一致していればtrue
 */
function is_valid_csrf_token($token){
  if($token === '') {
    return false;
  }
  // get_session()はユーザー定義関数
  return $token === get_session('csrf_token');
}