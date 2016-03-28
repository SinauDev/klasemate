<?php

## -------------------------------------------------------
#  Klasemate
## -------------------------------------------------------
#  Created by Laurensius Jeffrey Chandra
#  http://klasemate.arcestia.id
#
#  File: Report.php
#  License: GPLv2
#  Copyright: (c) 2016 - Klasemate
## -------------------------------------------------------

class Report extends Application
{
	/**
	 * --------------------------------------------------------------------
	 * PERFORM ACTIONS BEFORE RUN API METHODS
	 * --------------------------------------------------------------------
	 */
	public function _BeforeAction()
	{
		// Yeah, to avoid SPAM guests cannot send reports
		$this->Session->NoGuest();
	}

	/**
	 * --------------------------------------------------------------------
	 * REPORT A POST
	 * --------------------------------------------------------------------
	 */
	public function Post($post_id)
	{
		$this->master = "ajax";

		// Return variables
		$this->Set("post_id", $post_id);
	}

	/**
	 * --------------------------------------------------------------------
	 * REPORT A THREAD
	 * --------------------------------------------------------------------
	 */
	public function Thread($thread_id)
	{
		$this->master = "ajax";

		// Return variables
		$this->Set("thread_id", $thread_id);
	}

	/**
	 * --------------------------------------------------------------------
	 * SAVE REPORT ON DATABASE
	 * --------------------------------------------------------------------
	 */
	public function Save()
	{
		$this->layout = false;

		// Check if user is reporting a post or a thread
		if(!Http::Request("post_id", true)) {
			$mode = "thread";
			$thread_id = Http::Request("thread_id", true);
		}
		else {
			$mode = "post";
			$post_id = Http::Request("post_id", true);

			$this->Db->Query("SELECT thread_id FROM c_posts WHERE p_id = {$post_id};");
			$result = $this->Db->Fetch();

			$thread_id = $result['thread_id'];
		}

		// Build report
		$report_info = array(
			"description" => Http::Request("description"),
			"reason"      => Http::Request("reason", true),
			"date"        => time(),
			"sender_id"   => Http::Request("member_id", true),
			"ip_address"  => $_SERVER['REMOTE_ADDR'],
			"post_id"     => ($post_id) ? $post_id : "0",
			"thread_id"   => ($thread_id) ? $thread_id : "0",
			"referer"     => $_SERVER['HTTP_REFERER']
		);

		// Save report on DB
		$this->Db->Insert("c_reports", $report_info);

		// Redirect to its respective notification
		if($mode == "thread") {
			$this->Core->Redirect("thread/" . $report_info['thread_id'] . "?m=1");
		}
		else {
			$this->Core->Redirect("thread/" . $report_info['thread_id'] . "?m=2");
		}
	}
}
