<?php

## -------------------------------------------------------
#  Klasemate
## -------------------------------------------------------
#  Created by Laurensius Jeffrey Chandra
#  http://klasemate.arcestia.id
#
#  File: Core.php
#  License: GPLv2
#  Copyright: (c) 2016 - Klasemate
## -------------------------------------------------------

class Core
{
	// Community configurations
	public $config = array();

	// Database class
	private $Db;

	// Logged member information
	private $member_info = array();

	/**
	 * --------------------------------------------------------------------
	 * CORE() CLASS CONSTRUCTOR
	 * --------------------------------------------------------------------
	 */
	public function __construct($database, $configurations, $member_info = array())
	{
		// Load database layer and configurations array
		$this->Db = $database;
		$this->config = $configurations;
		$this->member_info = $member_info;
	}

	/**
	 * --------------------------------------------------------------------
	 * REDIRECT TO AN SPECIFIC URL
	 * --------------------------------------------------------------------
	 */
	public function Redirect($url)
	{
		if($url == "HTTP_REFERER") {
			header("Location: " . $_SERVER['HTTP_REFERER']);
			exit;
		}
		else {
			$url = $this->config['general_community_url'] . $url;
			header("Location: " . $url);
			exit;
		}
	}

	/**
	 * --------------------------------------------------------------------
	 * USE CUSTOM DATE FORMATTING
	 * --------------------------------------------------------------------
	 */
	public function DateFormat($timestamp, $format = "long")
	{
		// Get long/short time formats from configurations table
		if($format == "long") {
			$format = $this->config['date_long_format'];  // Get long format date from $_config
		}
		else {
			$format = $this->config['date_short_format'];  // Get short format date from $_config
		}

		// Get timezone offset
		$user_offset = (isset($this->member_info['time_offset'])) ? $this->member_info['time_offset'] : $this->config['date_default_offset'];
		$timezone_offset = $user_offset * HOUR;

		// format and return it
		$date = date($format, $timestamp + $timezone_offset);

		return $date;
	}

	/**
	 * --------------------------------------------------------------------
	 * GET GRAVATAR, FACEBOOK OR UPLOADED MEMBER IMAGE PATH
	 * $info: is an array containing 'email', 'photo' and 'photo_type'
	 * $section: public|admin
	 * --------------------------------------------------------------------
	 */
	public function GetAvatar($info = array(), $size = 96, $section = "public", $d = "mm", $r = "g")
	{
		switch($info['photo_type']) {
			// Gravatar photo
			case "gravatar":
				$url = "http://www.gravatar.com/avatar/";
				$url .= md5(strtolower(trim($info['email'])));
				$url .= "?s={$size}&amp;d={$d}&amp;r={$r}";
				break;

			// Facebook photo
			case "facebook":
				$get_facebook = $this->Db->Query("SELECT im_facebook FROM c_members WHERE email = '{$info['email']}';");
				$facebook_photo = $this->Db->Fetch($get_facebook);
				$url = "https://graph.facebook.com/{$facebook_photo['im_facebook']}/picture?width={$size}&height={$size}";
				break;

			// Uploaded photo
			case "custom":
				if($section == "public") {
					$url = "public/avatar/{$info['photo']}";
				}
				else {
					// Modify relative path when viewing in Admin CP
					$url = "../public/avatar/{$info['photo']}";
				}
				break;
		}

		return $url;
	}

	/**
	 * --------------------------------------------------------------------
	 * PARSE EMOTICONS INSIDE POSTS AND MESSAGES :)
	 * --------------------------------------------------------------------
	 */
	public function ParseEmoticons($text, $emoticons = array())
	{
		if($this->config['thread_allow_emoticons']) {
			// Empty array to store emoticons :O
			$translate = array();

			// Folder where images are located in ;)
			$folder = "public/emoticons/" . $this->config['emoticon_default_set'];

			foreach($emoticons as $item) {
				$shortcut = Text::Sanitize($item['shortcut']);
				$translate[$shortcut] = "<img src='{$folder}/{$item['filename']}' class='emoticon' alt='{$item['shortcut']}'>";
			}
			$retval = strtr(html_entity_decode($text), $translate);
			return Text::RemoveHTMLElements($retval);
		}
		else {
			return Text::RemoveHTMLElements($text);
		}
	}

	/**
	 * --------------------------------------------------------------------
	 * CREATE CANONICAL TAG URL
	 * --------------------------------------------------------------------
	 */
	public function CanonicalTag($thread_id)
	{
		return $this->config['general_community_url'] . "index.php?module=thread&id=" . $thread_id;
	}

	/**
	 * --------------------------------------------------------------------
	 * CREATE CANONICAL TAG URL
	 * --------------------------------------------------------------------
	 */
	public function Breadcrumb($page_info = array())
	{
		$breadcrumb = "";

		if(!empty($page_info)) {
			foreach($page_info['bc'] as $item) {
				$breadcrumb .= " " . $this->config['general_bread_separator'] . " " . $item;
			}
		}

		return $breadcrumb;
	}

	/**
	 * --------------------------------------------------------------------
	 * CREATE CANONICAL TAG URL
	 * --------------------------------------------------------------------
	 */
	public function PageTitle($page_info = array())
	{
		return (isset($page_info['title'])) ? $page_info['title'] . " - " : "";
	}
}
