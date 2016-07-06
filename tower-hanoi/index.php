<?php
/*
* Plugin Name: Tower of Hanaoi
* Description: Without rest api.
* Version: 1.0
* Author: Sameer
* Author URI: https://test.com
*/

require_once __DIR__.'/class/Hanoi.php';
require_once __DIR__.'/class/HanoiRecursiveSolving.php';

//WP Shortcode to display form on any page or post.
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