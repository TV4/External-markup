<?php
/*
Plugin Name: External markup
Description: Retrive parts of external content and include in blog framework
Author: TV4 AB
Version: 2.0
Author URI: http://www.tv4.se
*/

function em_Admin(){
	if(isset($_POST["submitted"])){
		$em_external_src = $_POST["input_external_src"];
		$em_external_src_cache = $_POST["input_external_src_cache"];
		
		update_option("em_external_src", $em_external_src);
		
		if(is_numeric($em_external_src_cache)){
			update_option("em_external_src_cache", $em_external_src_cache);
			$numeric_error = 0;
		}else{
			$numeric_error = 1;
		}
		
		if($numeric_error == 0){
			echo "<div id=\"message\" class=\"updated fade\"><p><strong>External markup settings is updated.</strong></p></div>";
		}else{
			echo "<div id=\"message\" class=\"updated fade\"><p><strong>External Cache must be numeric.</strong></p></div>";
		}
	}
?>
	<style type="text/css">
		th{font-weight: normal;padding-right: 10px;text-align: left;}
		table input{font-size: 11px;width: 200px;}
		div.external-markup-text{float: left;margin-left: 50px;width: 300px;}
		div.external-markup-text h4{margin: 0;padding: 0;}
		div.external-markup-text p{font-size: 11px; margin: 0 0 10px 0;padding: 0;}
		div.clear-float{clear: both;}
	</style>
	<div class="wrap">
		<h2>External markup settings</h2>
		<form method="post" name="options" target="_self">
			<table align="left">
				<tr>
					<th><label for="input_external_src">External URL:</label></th>
					<td><input id="input_external_src" name="input_external_src" type="text" value="<?php echo get_option("em_external_src"); ?>" /></td>
				</tr>
				<tr>
					<th><label for="input_external_src_cache">External Cache (seconds):</label></th>
					<td><input id="input_external_src_cache" name="input_external_src_cache" type="text" value="<?php echo get_option("em_external_src_cache"); ?>" /></td>
				</tr>
			</table>
			
			<div class="external-markup-text">
				<h4>Usage</h4>
				<p>The markup from external source must be tagged with specific html-comments. Plugin retrives markup from start/end comments.</p>

				<p>The htmlfile path is default set to /themes/TEMPLATENAME/em_cache/. Create folder /themes/TEMPLATENAME/em_cache/ and make it writeable (chmod 777)</p>

				<h4>Example</h4>
				<p>For retriving navigation markup from http://www.tv4.se:<br />Function call in template:  em_showContent("main-navigation start","main-navigation end",EM_EXAMPLE,FALSE)</p>

				<p><b>For more details, see readme.txt</b></p>
			</div>
			
			<div class="clear-float">&nbsp;</div>
			
			<p class="submit">
				<input name="submitted" type="hidden" value="yes" />
				<input type="submit" name="Submit" value="Update Options &raquo;" />
			</p>
		</form>
	</div>
<?php 
	}
	
	function em_Reg_Admin() {
		add_submenu_page('options-general.php', 'External markup', 'External markup', 10, __FILE__, 'em_Admin');
	}

	add_action('admin_menu', 'em_Reg_Admin');
?>
