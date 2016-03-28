<?php

## -------------------------------------------------------
#  Klasemate
## -------------------------------------------------------
#  Created by Laurensius Jeffrey Chandra
#  http://klasemate.arcestia.id
#
#  File: Community.php
#  License: GPLv2
#  Copyright: (c) 2016 - Klasemate
## -------------------------------------------------------

class Community extends Application
{
	// List of categories
	private $categories = array();

	// List of rooms of each category
	private $rooms = array();

	/**
	 * --------------------------------------------------------------------
	 * COMMUNITY HOME
	 * --------------------------------------------------------------------
	 */
	public function Main()
	{
		// Get rooms and categories
		$_rooms = $this->_GetRooms();

		// Return variables
		$this->Set("categories", $this->categories);
		$this->Set("rooms", $this->rooms);
		$this->Set("is_logged", $this->Session->IsMember());
	}

	/**
	 * --------------------------------------------------------------------
	 * ABOUT Klasemate
	 * --------------------------------------------------------------------
	 */
	public function About()
	{
		$this->master = "Ajax";

		$data = array(
			"version"  => VERSION . "-" . CHANNEL
		);

		// Return variables
		$this->Set("data", $data);
	}

	/**
	 * --------------------------------------------------------------------
	 * FOR LOGGED IN MEMBERS: MARK ALL THREADS AS READ
	 * --------------------------------------------------------------------
	 */
	public function MarkAllAsRead()
	{
		// Overwrite cookies
		$this->Session->CreateCookie("addictive_community_login_time", time(), 1);
		$this->Session->CreateCookie("addictive_community_read_threads", json_encode(array()), 1);

		// Go back to community
		$this->Core->Redirect("HTTP_REFERER");
	}

	/**
	 * --------------------------------------------------------------------
	 * RENDER XML FOR OPENSEARCH
	 * --------------------------------------------------------------------
	 */
	public function OpenSearch()
	{
		$this->layout = false;

		// XML content
		header('Content-Type: application/xml');
		$xml = '';

		$xml .= '<?xml version="1.0" encoding="UTF-8" ?>';
		$xml .= '<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/" xmlns:moz="http://www.mozilla.org/2006/browser/search/">';
		$xml .= '<ShortName>' . $this->Core->config['general_community_name'] . '</ShortName>';
		$xml .= '<Description>' . $this->Core->config['seo_description'] . '</Description>';
		$xml .= '<InputEncoding>UTF-8</InputEncoding>';
		$xml .= '<Image width="16" height="16" type="image/x-icon">' . $this->Core->config['general_community_url'] . 'favicon.png</Image>';
		$xml .= '<Url type="text/html" method="get" template="' . $this->Core->config['general_community_url'] . 'search?q={searchTerms}"></Url>';
		$xml .= '</OpenSearchDescription>';

		echo $xml;
	}

	/**
	 * --------------------------------------------------------------------
	 * RETURN LIST (ARRAY) OF ROOMS
	 * --------------------------------------------------------------------
	 */
	private function _GetRooms()
	{
		$now = time();

		// If member is Admin, show invisible rooms too
		if($this->Session->IsMember() && $this->Session->IsAdmin()) {
			$visibility = "";
		}
		else {
			$visibility = "AND invisible <> '1'";
		}

		// Get categories
		$categories_result = $this->Db->Query("SELECT * FROM c_categories
				WHERE visible = 1 ORDER BY order_n, c_id;");

		while($category = $this->Db->Fetch($categories_result)) {
			// Categories
			$this->categories[$category['c_id']] = $category;

			// Get rooms from DB
			$rooms_result = $this->Db->Query("SELECT c_rooms.*, c_members.m_id, c_members.username,
					c_threads.title, c_threads.start_date, c_threads.t_id, c_threads.slug,
					(SELECT COUNT(*) FROM c_threads WHERE room_id = c_rooms.r_id) AS thread_count FROM c_rooms
					LEFT JOIN c_members ON (c_members.m_id = c_rooms.last_post_member)
					LEFT JOIN c_threads
						ON c_threads.t_id = (SELECT t.t_id FROM c_threads AS t WHERE t.room_id = c_rooms.r_id
							AND t.start_date < {$now} ORDER BY t.last_post_date DESC LIMIT 1)
					WHERE category_id = {$category['c_id']}
					{$visibility} ORDER BY name ASC;");

			// Process data
			while($rooms = $this->Db->Fetch($rooms_result)) {
				$this->rooms[$category['c_id']][] = $this->_ParseRooms($rooms);
			}
		}
	}

	/**
	 * --------------------------------------------------------------------
	 * GET RAW ROOM INFO AND OUTPUTS READY CONTENT
	 * --------------------------------------------------------------------
	 */
	private function _ParseRooms($result)
	{
		// Get number of users online
		$online = $this->Db->Query("SELECT COUNT(*) AS total FROM c_sessions
				WHERE location_type IN ('room', 'thread') AND location_room_id = {$result['r_id']};");

		$result['online'] = $this->Db->Fetch($online);

		// If last post timestamp is not zero / no posts
		$result['last_post_date'] = ($result['last_post_date'] > 0) ? $this->Core->DateFormat($result['last_post_date']) : "---";

		// If thread and/or last poster username is empty, show dashes instead
		if($result['title'] == null) {
			$result['title'] = "---";
		}
		if($result['username'] == null) {
			$result['username'] = "---";
		}

		// Get moderators
		$moderators_array = unserialize($result['moderators']);
		if(!empty($moderators_array)) {
			$moderators = unserialize($result['moderators']);
			$moderator_list = array();

			// Build moderators list
			foreach($moderators as $member_id) {
				$mod_details = $this->Db->Query("SELECT m_id, username FROM c_members WHERE m_id = {$member_id};");
				$member = $this->Db->Fetch($mod_details);

				$moderator_list[] = "<a href='profile/{$member['m_id']}'>{$member['username']}</a>";
			}

			$result['moderators_list'] = "<div class='moderators'>Moderators: " . Text::ToList($moderator_list) . "</div>";
		}
		else {
			$result['moderators_list'] = "";
		}

		// Check if room has unread threads
		$has_unread_threads = ($result['thread_count'] > 0) ? $this->_CheckUnread($result['r_id']) : false;

		// Regular variables
		$result['room_link'] = "room/{$result['r_id']}";
		$result['redirect'] = ""; // Specific for redirect room

		// Is this room a read only, protected or invisible room?
		// The order of relevance is from down to up
		if($result['read_only'] == 1) {
			$result['icon']  = "<i class='fa fa-file-text-o fa-fw'></i>";
			$result['title'] = "<a href='thread/{$result['t_id']}-{$result['slug']}'>{$result['title']}</a>";
		}
		elseif($result['password'] != "") {
			$result['icon']  = "<i class='fa fa-lock fa-fw' title='Protected room'></i>";
			$result['title'] = "<em>" . i18n::Translate("C_PROTECTED_ROOM") . "</em>";
		}
		elseif($result['invisible'] == 1) {
			$result['icon']  = "<i class='fa fa-user-secret fa-fw' title='Invisible room'></i>";
			$result['title'] = "<a href='thread/{$result['t_id']}-{$result['slug']}'>{$result['title']}</a>";
		}
		elseif($result['url'] != "") {
			$result['icon']  = "<i class='fa fa-external-link fa-fw' title='Redirect room'></i>";
			$result['redirect'] = "<div class='redirect'>" . i18n::Translate("C_REDIRECT_TO") . ": {$result['url']}</div>";
			$result['room_link'] = $result['url'];
		}
		elseif($has_unread_threads) {
			$result['icon']  = "<i class='fa fa-comment fa-fw' title='Has unread threads'></i>";
			$result['title'] = "<a href='thread/{$result['t_id']}-{$result['slug']}'>{$result['title']}</a>";
		}
		else {
			$result['icon']  = "<i class='fa fa-comment-o fa-fw' title='Has no unread threads'></i>";
			$result['title'] = "<a href='thread/{$result['t_id']}-{$result['slug']}'>{$result['title']}</a>";
		}

		// Save result in array
		return $result;
	}

	/**
	 * --------------------------------------------------------------------
	 * CHECK IF ROOM HAS UNREAD THREADS
	 * --------------------------------------------------------------------
	 */
	private function _CheckUnread($room_id)
	{
		$has_unread = false;

		$threads = $this->Db->Query("SELECT t_id, last_post_date FROM c_threads WHERE room_id = {$room_id};");

		while($result = $this->Db->Fetch($threads)) {
			$read_threads_cookie = $this->Session->GetCookie("addictive_community_read_threads");
			if($read_threads_cookie) {
				$login_time_cookie = $this->Session->GetCookie("addictive_community_login_time");
				$read_threads = json_decode(html_entity_decode($read_threads_cookie), true);
				if(!in_array($result['t_id'], $read_threads) && $login_time_cookie < $result['last_post_date']) {
					$has_unread = true;
				}
			}
		}

		return $has_unread;
	}
}
