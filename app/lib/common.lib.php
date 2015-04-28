<?php

if (PHP_SAPI == 'cli') {
  $error_reporting = error_reporting();
  error_reporting($error_reporting & ~E_NOTICE);
}

// Tell PHP that we're using UTF-8 strings until the end of the script
mb_internal_encoding('UTF-8');

// Tell PHP that we'll be outputting UTF-8 to the browser
mb_http_output('UTF-8');

define('SERVER_NAME', 'http://'.SERVER_ROOT.'.menucosm'.SERVER_BASE);

// include directories
define('STATIC_DIR', ROOT_DIR.'static/');
define('CONFIG_DIR', ROOT_DIR.'config/');
define('APP_DIR', ROOT_DIR.'app/');
define('CLASSES_DIR', APP_DIR.'classes/');
define('CONTROLLERS_DIR', APP_DIR.'controllers/');
define('LIB_DIR', APP_DIR.'lib/');
define('MODELS_DIR', APP_DIR.'models/');
define('VIEWS_DIR', APP_DIR.'templates/');

// www directories
define('CSS_DIR', 'css/');
define('IMG_DIR', 'img/');
define('JS_DIR', 'js/');

define('IE', (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE));

date_default_timezone_set('UTC');

define('TRADE_QUEUE', 'trades');

/**
 * Function to autoload any classes, once they're used in the code.
 * Applies to models, then controllers, then helper classes
 *
 * @param  String  $class_name  Base name of the class
 *
 * @return (none)
 *
 */
function mc_autoload($class_name) {
  foreach (array(MODELS_DIR, CONTROLLERS_DIR, CLASSES_DIR) as $dir) {
    $file = $dir.$class_name.'.php';
    if (file_exists($file)) {
      include $file;
    }
  }
}
spl_autoload_register('mc_autoload');

/**
 * Function to get the include path for a given view
 *
 * @param   String  $file   Config file to include, e.g. 'server'
 *
 * @return  String  (none)  Config file path
 *
 */
function config($file) {
  return CONFIG_DIR.$file.'.php';
}

/**
 * Function to get the include path for a given lib file
 *
 * @param   String  $file   Lib file to include, e.g. 'search'
 *
 * @return  String  (none)  Lib file path
 *
 */
function lib($file) {
  return LIB_DIR.$file.'.lib.php';
}

/**
 * Function to get the include path for a given view
 *
 * @param   String  $file   Template file to include, e.g. 'header'
 * @param   String  $model  If loading a template for a specific model
 *
 * @return  String  (none)  View path
 *
 */
function view($file, $model='') {
  if (strlen($model)) {
    $model .= '/';
  }
  return VIEWS_DIR.$model.$file.'.tpl.php';
}

/**
 * Function to echo a web url of a given css file
 *
 * @param   String  $css       The css file we're looking for (without file extension)
 * @param   String  $version   Optional version number
 *
 * @return  (none)             Echoes full path from www root
 *
 */
function css($css, $version='') {
  echo add_version(
    '/'.CSS_DIR.$css.'.css', $version
  );
}

/**
 * Function to echo a web url of a given image file
 *
 * @param   String  $img       The image file we're looking for
 * @param   String  $version   Optional version number
 *
 * @return  (none)             Echoes full path from www root
 *
 */
function img($img, $version='') {
  echo add_version(
    '/'.IMG_DIR.$img, $version
  );
}

/**
 * Function to echo a web url of a given Javascript file
 *
 * @param   String  $js                The image file we're looking for
 * @param   String  $version           Optional version number
 *
 * @return  (none)                     Echoes full path from www root
 *
 */
function js($js, $version='') {
  echo add_version(
    '/'.JS_DIR.$js.'.js', $version
  );
}

/**
 * Function to add a version number to a given file path, if the version's set
 *
 * @param   String   $file     Base file name
 * @param   Integer  $version  Version no. to append
 *
 * @return  String   $file     Base file name with '?v=1' or whatever appended
 *
 */
function add_version($file, $version) {
  if ($version != '') {
    $file .= '?v='.$version;
  }
  return $file;
}

/**
 * Function to render the 404 error page
 *
 * @param  (none)
 *
 * @return (none)
 */
function _404() {
  $title = '404 File Not Found';
  header('HTTP/1.1 404 Not Found');
  include view('header');
  include view('404');
  include view('footer');
}

/**
 * Function to generate a URL safe string of encoded parameter data
 *
 * @param   Array   $params   Parameters to be encoded
 *
 * @return  String  $encoded  URL Safe encoded string
 *
 */
function generate_encoded_string($params) {
  $parameter_string = serialize($params);
  $md5_key = generate_md5_key($parameter_string);
  $encoded = base64_encode_url_safe($md5_key.','.$parameter_string);
  return $encoded;
}

/**
 * Function that takes an encoded string, validates it hasn't been tampered with and returns the data as an array
 *
 * @param   String  $str      String to be decoded
 *
 * @return  Array   $params   Data from the string
 *
 */
function generate_decoded_array($str) {
  $decoded = base64_decode_url_safe($str);
  list($md5_key, $parameter_string) = explode(',', $decoded);
  if ($md5_key == generate_md5_key($parameter_string)) {
    $params = unserialize($parameter_string);
  }
  else { // some one was messing
    $params = false;
  }
  return $params;
}

/**
 * Function that generates an md5 key for validation of encoded parameters
 *
 * @param   String  $str  Base string
 *
 * @return                The MD5 key
 */
function generate_md5_key($str) {
  return md5(sprintf("%s-%s", $str, ENCODING_KEY));
}

/**
 * Base64_encode the url to be used as MSL filename.
 * Also replace the characters which have special meanings in urls but
 * cause the msl to have issues with decoding.
 *
 * @param string $url - "filename" which shall be used by MSL
 *
 * @return string decoded url
 *
 */
function base64_encode_url_safe($url) {
  return str_replace(
    array('+', '/'),
    array('-', '_'),
    base64_encode($url)
  );
}


/**
 * Base64_decode the url received by the MSL system. Additionally, replace
 * the characters which have special meanings in urls but cause the msl
 * to get very confused.
 *
 * @param string $url - "filename" received by MSL
 *
 * @return string decoded url
 *
 */
function base64_decode_url_safe($url) {
  return base64_decode(
    str_replace(
      array('-', '_'),
      array('+', '/'),
      $url
    )
  );
}

/**
 * Function to remove any non numeric characters from a string
 *
 * @param   String  $str  String to modify
 *
 * @return  String        String with only numeric characters
 *
 */
function remove_non_numerics($str) {
  return preg_replace('/[^0-9]/', '', $str);
}

/**
 * Function to remove any non alpha-numeric characters from a string
 *
 * @param   String  $str  String to modify
 *
 * @return  String        String with only alpha-numeric characters
 *
 */
function remove_non_alpha_numerics($str) {
  return preg_replace('/[^0-9a-zA-Z-]/', '', $str);
}

/**
 * Function to convert a variable name to camel case
 *
 * @param   String  $var               String to be converted
 * @param   Bool    $first_char_lower  Whether or not the 1st character should be lower case
 *
 * @return  String  $cc                String converted to camel case
 *
 */
function camel_case($var, $first_char_lower = false) {
  $words = explode('_', strtolower($var));
  $cc = array_shift($words);
  if (!$first_char_lower) {
    $cc = ucfirst($cc);
  }
  foreach ($words as $word) {
    $cc .= ucfirst($word);
  }
  return $cc;
}

/**
 * Function to get a slug for a given string. Removes non-alpha-numerics and spaces, converts to lower case
 *
 * @param   String  $str  String to be formatted
 *
 * @return  String        Formatted string
 *
 */
function slug($str) {
  $slug = strtolower(
    remove_non_alpha_numerics(
      iconv('UTF-8', 'ASCII//TRANSLIT', str_replace(' ', '-', $str))
    )
  );
  $count = 0;
  do {
    $slug = str_replace('--', '-', $slug, $count);
  } while ($count);
  return $slug;
}

/**
 * Function to include all the necessary files for Pheanstalk
 *
 * @return  Phenstalk    A Pheanstalk object
 */
function pheanstalk() {
  ini_set('include_path', ini_get('include_path').':'.CLASSES_DIR);
  // echo ini_get('include_path');
  require_once('pheanstalk_init.php');
  return new Pheanstalk_Pheanstalk('127.0.0.1');
}

/**
 * Function to print handy debug info of passed parameters
 *
 * @param   Unlimited
 *
 * @return  (none)
 *
 */
function pre_print_r() {
  echo '<pre style="background: #fff; color: #000; font-family: courier; text-align: left; margin: 50px 0;">';
  foreach (func_get_args() as $arg) {
    print_r($arg);
  }
  echo '</pre>';

}

function derror() {
  $file = '/tmp/derror';

  if (!file_exists($file)) {
    touch($file);
    chmod($file, 0777);
  }

  $f = fopen($file, 'a');
  fwrite($f, date('Y-m-d G:i:s')."\n");
  foreach (func_get_args() as $obj) {
    if (is_array($obj) || is_object($obj)) {
      fwrite($f, print_r($obj,1)."\n\n");
    }
    else {
      fwrite($f, $obj."\n\n");
    }
  }
  fclose($f);
}

if (PHP_SAPI == 'cli') {
  error_reporting($error_reporting);
}

?>