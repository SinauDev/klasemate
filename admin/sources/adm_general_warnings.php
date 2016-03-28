<?php

	## ---------------------------------------------------
	#  Klasemate
	## ---------------------------------------------------
	#  Developed by Laurensius Jeffrey Chandra
	#  File: adm_general_profiles.php
	#  License: GPLv2
	#  Copyright: (c) 2016 - Klasemate
	## ---------------------------------------------------

	$msg = (Http::Request("msg")) ? Http::Request("msg") : "";

	switch($msg) {
		case 1:
			$message = Html::Notification("The settings has been successfully changed.", "success");
			break;
		default:
			$message = "";
			break;
	}

?>

	<h1>Warnings</h1>

	<div id="content">
		<div class="grid-row">
			<form action="process.php?do=save" method="post">

				<?php echo $message ?>

				<table class="table-list">
					<tr>
						<th colspan="2">General Warning Settings</th>
					</tr>
					<tr>
						<td class="title-fixed">Maximum warning points<span class="title-desc">Reaching X warnings, the user is automatically banned from the community.</span></td>
						<td><input type="text" name="general_warning_max" class="nano" value="<?php echo $Admin->SelectConfig("general_warning_max") ?>"> warnings</td>
					</tr>
				</table>

				<div class="box fright"><input type="submit" value="Save Settings"></div>
			</form>
		</div>
	</div>
