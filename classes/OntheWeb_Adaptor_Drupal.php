<?php
// Copyright (C) 2014 Adam Davis (adam@admataz.com)

// This program is free software; you can redistribute it and/or modify it under
// the terms of the GNU General Public License as published by the Free Software
// Foundation; either version 2 of the License, or (at your option) any later
// version.

// This program is distributed in the hope that it will be useful, but WITHOUT
// ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
// FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

// You should have received a copy of the GNU General Public License along with
// this program; if not, write to the Free Software Foundation, Inc., 59 Temple
// Place, Suite 330, Boston, MA 02111-1307 USA

class OnTheWeb_Adaptor_Drupal{
	

	static function http_req($url, $method = 'POST', $headers = array(), $data=''){
		$response = drupal_http_request($url, array(
			'headers' => $headers,
			'method' => $method,
			'data' => $data
			));

		if (!$response->data) {
			return false;
		}
		return $response;
	}


	static public function build_query($q = ''){
		return drupal_http_build_query($q);
	}


	static public function json_encode($obj = array()){
		return drupal_json_encode($obj);
	}


	static public function json_decode($json = ''){
		return drupal_json_decode($json);
	}


}
