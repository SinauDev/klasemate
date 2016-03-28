<?php

	## ---------------------------------------------------
	#  Klasemate
	## ---------------------------------------------------
	#  Developed by Laurensius Jeffrey Chandra
	#  File: adm_templates_main.php
	#  License: GPLv2
	#  Copyright: (c) 2016 - Klasemate
	## ---------------------------------------------------

?>

	<h1>Templates Settings</h1>

	<div id="content">
		<?php echo Html::Notification("<b>Themes</b> are packages of CSS styles and image files, this is where you go if you want to change the colors and foreground/background images. <b>Templates</b> are HTML and front-end PHP files and variables (the page structure).", "info") ?>
		<div class="grid-row">
			<!-- LEFT -->
			<table class="table-list">
				<tr>
					<th>Settings Groups</th>
				</tr>
				<tr><td><a href="main.php?act=templates&p=themes"><b>Theme Manager</b></a><br>Manage theme sets: edit CSS sources, images, theme name and directories, etc.</td></tr>
				<tr><td><a href="main.php?act=templates&p=templates"><b>Templates</b></a><br>Manage template sets: edit HTML and front-end PHP sources (.phtml), template name and directories, etc.</td></tr>
				<tr><td><a href="main.php?act=templates&p=manager"><b>E-mails</b></a><br>Set up and manage your e-mail templates/content (such as Validation, Warnings, etc.).</td></tr>
				<tr><td><a href="main.php?act=templates&p=emoticons"><b>Emoticon Manager</b></a><br>This will manage (add and remove) emoticons and edit their settings.</td></tr>
				<tr><td><a href="main.php?act=templates&p=help"><b>Help Topics</b></a><br>This section allows you to manage help articles.</td></tr>
				<!-- <tr><td><a href="main.php?act=templates&p=import"><b>Import/Export</b></a><br>Import, install and export gzip/XML template files.</td></tr> -->
				<!-- <tr><td><a href="main.php?act=templates&p=tools"><b>Tools</b></a><br>Search and replace, logo changer, etc.</td></tr> -->
			</table>
		</div>

	</div>
