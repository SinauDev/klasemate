<?php

## -------------------------------------------------------
#  Klasemate
## -------------------------------------------------------
#  Created by Laurensius Jeffrey Chandra
#  http://klasemate.arcestia.id
#
#  File: tests.php
#  License: GPLv2
#  Copyright: (c) 2016 - Klasemate
## -------------------------------------------------------

$task = $_REQUEST['task'];

if($task == "test_database") {
	$data = array(
		'host'     => $_REQUEST['host'],
		'database' => $_REQUEST['database'],
		'username' => $_REQUEST['username'],
		'password' => $_REQUEST['password'],
		'port'     => $_REQUEST['port']
	);

	$link = @mysqli_connect($data['host'], $data['username'], $data['password'], $data['database'], $data['port']);

	if($link) {
		$return_data = array('status' => 1);
	} else {
		$return_data = array('status' => 0, 'message' => mysqli_connect_error());
	}

	echo json_encode($return_data);
}
