<?php
// Copyright (c) 2014 Adam Davis (adam@admataz.com)

// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:

// The above copyright notice and this permission notice shall be included in all
// copies or substantial portions of the Software.

// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
// SOFTWARE.
// 



/**
 * OnTheWeb Twitter - facilitates the connections, authentication and queries for getting tweets and timelines to embed on a site
 */
class OnTheWeb_Twitter extends OnTheWeb_Base {
  static $twitter_endpoint = 'https://api.twitter.com/1.1';
  static $twitter_auth_endpoint = 'https://api.twitter.com/oauth2/token';
  private $adaptor; 
  private $auth;


  public function __construct($adaptor, $auth){
    $this->adaptor = $adaptor;
    $this->$auth = $auth;
  }


  
  




  private function get_api_data($path, $q = array(), $method='GET') {
    $auth_token = $this->get_auth_token();
    if (!$auth_token) {
      return array();
    }
    $headers = array(
      'Authorization' => $auth_token
    );
    $method = $method;
    $url = $this->twitter_endpoint . $path;
    if (!empty($q) && $method == 'GET') {
      $url.= '?' . $this->adaptor::build_query($q);
    }
    $reqdata = null;
    if (!empty($q) && $method == 'POST') {
      $headers['Content-Type'] = 'application/x-www-form-urlencoded';
      $reqdata = $this->adaptor::build_query($q);
    }
    $response = $this->adaptor::req( $url,$method, $headers,$reqdata);
    if ($response->code != '200') {
      return false;
    }
    $data = $this->adaptor::json_decode($response->data);
    return $data;
  }


  public function get_auth_token() {
    $auth = 'Basic ' . base64_encode(urlencode($this->auth['consumer_key']) . ':' . urlencode($this->auth['consumer_secret']));
    $method = 'POST';
    $data = 'grant_type=client_credentials';
    
    $headers = array(
      'Authorization' => $auth,
      'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8'
    );

    $response = $this->adaptor::req($this->twitter_auth_endpoint);
    $response_obj = $this->adaptor::json_decode($response->data);

    if ($response_obj->token_type == 'bearer') {
      return 'Bearer ' . ($response_obj->access_token);
    }
    return false;
  }



public function get_followers_count($screen_name = 'theelders') {
    $saved_stats = variable_get('ontheweb_homestats', array());
    
    if (!isset($saved_stats['twitter'])) {
      return $this->do_get_followers_count();
    }
    
    $stat = $saved_stats['twitter'];
    if (time() - $stat['time'] > 550) {
      return $this->do_get_followers_count($screen_name);
    }
    
    return $saved_stats['twitter']['count'];
  }
  




  private function do_get_followers_count($screen_name) {
    $saved_stats = variable_get('ontheweb_homestats', array());
    $data = $this->get_api_data('/users/show.json', array('screen_name'=>$screen_name));
    $saved_stats['twitter'] = array(
      'time' => time() ,
      'count' => $data['followers_count']
    );
    variable_set('ontheweb_homestats', $saved_stats);
    return $data['followers_count'];
  }
  
  private function normalise_posts($itm = array()) {
    $newObj = array();
    
    if (empty($itm)) {
      return null;
    }

    
    $newObj['url'] = 'http://twitter.com/' . $itm['user']['screen_name'] . '/status/' . $itm['id_str'];
    $newObj['title'] = 'Tweet by ' . $itm['user']['name'] . ' (@' . $itm['user']['screen_name'] . ')';
    $newObj['content'] = $this->remove_non_utf8($itm['text']);
    $newObj['imageUrl'] = '';
    // $newObj['geotags'] = $itm['geo'];
    $newObj['authorName'] = $itm['user']['name'];
    $newObj['usernameID'] = '@' . $itm['user']['screen_name'];
    $newObj['authorProfileUrl'] = 'http://twitter.com/' . $itm['user']['screen_name'];
    $newObj['authorAvatarUrl'] = $itm['user']['profile_image_url'];
    $newObj['sourcePlatform'] = 'twitter';
    $newObj['sourceHandle'] = $itm['user']['screen_name'];
    $newObj['sourceId'] = $itm['id_str'];
    $newObj['sourceSiteName'] = 'Twitter';
    $newObj['sourceSiteUrl'] = 'http://twitter.com';
    $newObj['sourceSiteLogoUrl'] = '';
    $newObj['inReplyTo'] = $itm['in_reply_to_status_id'];
    $newObj['dateCollected'] = date('Y-m-d');
    $newObj['datePosted'] = date('Y-m-d H:i:s', strtotime($itm['created_at']));
    $newObj['dateLastValidated'] = date('Y-m-d');
    $newObj['comment'] = '';
    $newObj['tags'] = '';
    $newObj['status'] = 0;
    
    if ($itm['entities']['media']) {
      $newObj['imageUrl'] = $itm['entities']['media'][0]['media_url'];
    }
    
    return $newObj;
  }
  
  

/**
 *     Get a user's timeline
 *     Pass in options according to the Twitter API (https://dev.twitter.com/rest/reference/get/statuses/user_timeline)
 *     
 *     Always specify either an user_id or screen_name when requesting a user timeline.
  *    user_id 
  *    The ID of the user for whom to return results for.
  *    Example Values: 12345
  *    
  *    screen_name 
  *    The screen name of the user for whom to return results for.
  *    Example Values: noradio
  *    
  *    since_id 
  *    Returns results with an ID greater than (that is, more recent than) the specified ID. There are limits to the number of Tweets which can be accessed through the API. If the limit of Tweets has occured since the since_id, the since_id will be forced to the oldest ID available.
  *    Example Values: 12345
  *    
  *    count 
  *    Specifies the number of tweets to try and retrieve, up to a maximum of 200 per distinct request. The value of count is best thought of as a limit to the number of tweets to return because suspended or deleted content is removed after the count has been applied. We include retweets in the count, even if include_rts is not supplied. It is recommended you always send include_rts=1 when using this API method.
  *    
  *    max_id 
  *    Returns results with an ID less than (that is, older than) or equal to the specified ID.
  *    Example Values: 54321
  *    
  *    trim_user 
  *    When set to either true, t or 1, each tweet returned in a timeline will include a user object including only the status authors numerical ID. Omit this parameter to receive the complete user object.
  *    Example Values: true
  *    
  *    exclude_replies 
  *    This parameter will prevent replies from appearing in the returned timeline. Using exclude_replies with the count parameter will mean you will receive up-to count tweets — this is because the count parameter retrieves that many tweets before filtering out retweets and replies. This parameter is only supported for JSON and XML responses.
  *    Example Values: true
  *    
  *    contributor_details 
  *    This parameter enhances the contributors element of the status response to include the screen_name of the contributor. By default only the user_id of the contributor is included.
  *    Example Values: true
  *    
  *    include_rts 
  *    Example Values: false
 */
  public function get_user_timeline($opts=array()) {
    
    if(!isset($opts['user_id']) && !isset($opts['screen_name'])){
      throw new Exception("You must specify a Twitter user_id or screen_name to get their timeline ", 1);
      return false;
    }
   
    // Get a user's timeline
    $data = $this->get_api_data('/statuses/user_timeline', $opts);
    
    return $data;

    // $webitems = array_map(array(
    //   $this,
    //   'normalise_posts'
    // ) , $data);

    // return $webitems;
  }
  



  public function get_search_result($options = array()) {
    $data = $this->get_api_data('/search/tweets.json', array(
      'q' => $query,
      'result_type' => 'recent',
      'count' => 100,
      'lang' => 'en'
    ));


    $webitems = array_map(array(
      $this,
      'normalise_posts'
    ) , $data['statuses']);
    
    return $webitems;
  }


public function bulk_check($itm_ids=''){
    $data = $this->get_api_data('/statuses/lookup.json', array('id'=>$itm_ids, 'map'=>true), 'POST' );
    $webitems = array_map(array(
      $this,
      'normalise_posts'
    ) , $data['id']);
    
    // xdebug_break();

    return $webitems;


  }





/**
 * from http://www.snipe.net/2009/09/php-twitter-clickable-links/
 */
private function twitterify( $ret, $hashes=TRUE, $ats=TRUE, $trim=0, $ellipsis='…' ) {
  if ( $trim ) {
    $words = explode( ' ', $ret );
    if ( count( $words ) > $trim ) {
      array_splice( $words, $trim );
      $ret = implode( ' ', $words );
      if ( is_string( $ellipsis ) ) {
        $ret .= $ellipsis;
      }
      elseif ( is_string( $ellipsis ) ) {
        $ret .= $ellipsis;
      }
    }
  }

  $ret = preg_replace( '~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $ret );
  $ret = preg_replace( '~&#([0-9]+);~e', 'chr("\\1")', $ret );

  $ret = preg_replace_callback( "#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", array($this,"shorten_url_title1"), $ret );
  $ret = preg_replace_callback( "#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", array($this, "shorten_url_title2"), $ret );

  if ( $hashes ) {
    $ret = preg_replace( "/@(\w+)/", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $ret );
  }

  if ( $ats ) {
    $ret = preg_replace( "/#(\w+)/", "<a href=\"http://twitter.com/search?q=\\1\" target=\"_blank\">#\\1</a>", $ret );
  }
  return $ret;
}




/**
 *
 */
private function shorten_url_title1( $match ) {
  if ( strlen( $match[2] ) > 16 ) {
    $title = substr( $match[2], 0, 20 ).'…';
  }else {
    $title = $match[2];
  }
  return "{$match[1]}<a href=\"{$match[2]}\" target=\"_blank\">".$title."</a>";
}





/**
 *
 */
private function shorten_url_title2( $match ) {
  if ( strlen( $match[2] ) > 16 ) {
    $title = substr( $match[2], 0, 20 ).'…';
  }else {
    $title = $match[2];
  }

  return "{$match[1]}<a href=\"{$match[2]}\" target=\"_blank\">".$title."</a>";
}
}



}
