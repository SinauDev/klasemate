<?php

	## ---------------------------------------------------
	#  Klasemate
	## ---------------------------------------------------
	#  Developed by Laurensius Jeffrey Chandra
	#  File: adm_general_email.php
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

	// "Authentication Method" drop-down element value
	$auth = $Admin->SelectConfig("general_email_auth_method");

?>

	<h1>E-mail</h1>

	<div id="content">
		<div class="grid-row">
			<form action="process.php?do=save" method="post">

				<?php echo $message ?>

				<table class="table-list">
					<tr>
						<th colspan="2">SMTP Settings</th>
					</tr>
					<tr>
						<td class="title-fixed">SMTP server</td>
						<td><input type="text" name="general_email_smtp" class="large" value="<?php echo $Admin->SelectConfig("general_email_smtp") ?>"></td>
					</tr>
					<tr>
						<td class="title-fixed">Username</td>
						<td><input type="text" name="general_email_username" class="small" value="<?php echo $Admin->SelectConfig("general_email_username") ?>"></td>
					</tr>
					<tr>
						<td class="title-fixed">Password</td>
						<td><input type="password" name="general_email_password" class="small" value="<?php echo $Admin->SelectConfig("general_email_password") ?>"></td>
					</tr>
					<tr>
						<td class="title-fixed">TCP port</td>
						<td><input type="text" name="general_email_port" class="tiny" value="<?php echo $Admin->SelectConfig("general_email_port") ?>"></td>
					</tr>
					<tr>
						<td class="title-fixed">Requires authentication</td>
						<td><label><?php echo $Admin->SelectCheckbox("general_email_authentication") ?> Enable SMTP authentication</label></td>
					</tr>
					<tr>
						<td class="title-fixed">Authentication method</td>
						<td>
							<select name="general_email_auth_method">
								<option value="tls" <?php echo ($auth == "tls") ? "selected" : "" ?>>TLS</option>
								<option value="ssl" <?php echo ($auth == "ssl") ? "selected" : "" ?>>SSL</option>
							</select>
						</td>
					</tr>
				</table>

				<table class="table-list">
					<tr>
						<th colspan="2">E-mail Identification</th>
					</tr>
					<tr>
						<td class="title-fixed">"From" e-mail address<span class="title-desc">Usually the same used in authentication.</span></td>
						<td><input type="text" name="general_email_from" class="medium" value="<?php echo $Admin->SelectConfig("general_email_from") ?>"></td>
					</tr>
					<tr>
						<td class="title-fixed">"From" name</td>
						<td><input type="text" name="general_email_from_name" class="medium" value="<?php echo $Admin->SelectConfig("general_email_from_name") ?>"></td>
					</tr>
				</table>

				<div class="box fright"><input type="submit" value="Save Settings"></div>
			</form>
		</div>
	</div>
