<?php
/*
Plugin Name: Tower of Hanoi-JSON API
Plugin URI: http://wordpress.org/
Description: A Tower of Hanoi RESTful API with short code for WordPress
Version: 1.1.1
Author: Sameer
Author URI: http://test.org/
*/

$dir = json_api_dir();
@include_once "$dir/class/Hanoi.php";
@include_once "$dir/class/HanoiRecursiveSolving.php";

@include_once "$dir/singletons/api.php";
@include_once "$dir/singletons/query.php";
@include_once "$dir/singletons/introspector.php";
@include_once "$dir/singletons/response.php";
@include_once "$dir/models/post.php";
@include_once "$dir/models/comment.php";
@include_once "$dir/models/category.php";
@include_once "$dir/models/tag.php";
@include_once "$dir/models/author.php";
@include_once "$dir/models/attachment.php";


function json_api_init() {
  global $json_api;
  if (phpversion() < 5) {
    add_action('admin_notices', 'json_api_php_version_warning');
    return;
  }
  if (!class_exists('JSON_API')) {
    add_action('admin_notices', 'json_api_class_warning');
    return;
  }
  add_filter('rewrite_rules_array', 'json_api_rewrites');
  $json_api = new JSON_API();
}

function json_api_php_version_warning() {
  echo "<div id=\"json-api-warning\" class=\"updated fade\"><p>Sorry, JSON API requires PHP version 5.0 or greater.</p></div>";
}

function json_api_class_warning() {
  echo "<div id=\"json-api-warning\" class=\"updated fade\"><p>Oops, JSON_API class not found. If you've defined a JSON_API_DIR constant, double check that the path is correct.</p></div>";
}

function json_api_activation() {
  // Add the rewrite rule on activation
  global $wp_rewrite;
  add_filter('rewrite_rules_array', 'json_api_rewrites');
  $wp_rewrite->flush_rules();
}

function json_api_deactivation() {
  // Remove the rewrite rule on deactivation
  global $wp_rewrite;
  $wp_rewrite->flush_rules();
}

function json_api_rewrites($wp_rules) {
  $base = get_option('json_api_base', 'api');
  if (empty($base)) {
    return $wp_rules;
  }
  $json_api_rules = array(
    "$base\$" => 'index.php?json=info',
    "$base/(.+)\$" => 'index.php?json=$matches[1]'
  );
  return array_merge($json_api_rules, $wp_rules);
}

function json_api_dir() {
  if (defined('JSON_API_DIR') && file_exists(JSON_API_DIR)) {
    return JSON_API_DIR;
  } else {
    return dirname(__FILE__);
  }
}

// Add initialization and activation hooks
add_action('init', 'json_api_init');
register_activation_hook("$dir/json-api.php", 'json_api_activation');
register_deactivation_hook("$dir/json-api.php", 'json_api_deactivation');

function form_creation(){
?>
<form method="post" action="">
Disk: <input type="number" name="disk" value="<?php echo $_POST['disk'];?>" required ><br>

<button type="submit"> Submit </button>
</form>

<?php
  if(isset($_POST['disk']) && $_POST['disk'] != ''){
    $time = microtime(true);
    $hanoi = new Hanoi($_POST['disk'], Hanoi::PEG_A);
    $hanoi->setDebug(true);
    $hanoiSolving = new HanoiRecursiveSolving($hanoi);
    $hanoiSolving->solve($hanoi->getNumberOfDisks(), $hanoi->getStartPosition(), Hanoi::PEG_C);

    echo '<b>Count of move - </b>'.$hanoi->getCountMove()."<br>";
    echo "<b>Time execute - </b>".(microtime(true) - $time)."<br>";

    foreach ($hanoi->getDebugInfo() as $infoMessage) {
        echo "$infoMessage <br>";
    } 
  }

}

add_shortcode('tower-hanoi', 'form_creation');

?>
