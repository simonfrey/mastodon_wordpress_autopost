<?php
//Wordpress Security function
	defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Class mastodon_wordpress_networking_api
 *
 * PHP version 7.1
 *
 * Mastodon     https://mastodon.social/
 *
 * @author      L1am0   <meet@l1am0.net>
 * @copyright   L1am0
 * @license     https://raw.githubusercontent.com/L1am0/mastodon_wordpress_api/master/LICENCSE GPLv2
 * @link        https://github.com/L1am0/mastodon_wordpress_api/
 */
class mastodon_wordpress_networking_api {
	private $mastodon_url = '';
	private $client_id = '';
	private $client_secret = '';

	private $token = array();
	private $scopes = array();

	public function __construct () {}

	public function __destruct () {}

	/**
	 * _post
	 *
	 * curl post
	 *
	 * @param   string      $url
	 * @param   array       $data
	 *
	 * @return  array       $response
	 */
	private function _post ($url,$data = array()) {
		$parameters = array();
		$parameters[CURLOPT_POST] = 1;

		// set access_token
		if (isset($this->token['access_token'])) {
			$data['access_token'] = $this->token['access_token'];
		}

		if (count($data)) {
			$parameters[CURLOPT_POSTFIELDS] = $data;
		}

		$url = $this->mastodon_url.$url;
		$response = $this->get_content_curl($url,$parameters);
		return $response;
	}

	/**
	 * _get
	 *
	 * @param   string      $url
	 * @param   array       $data
	 *
	 * @return  array       $response
	 */
	private function _get ($url,$data = array()) {
		$parameters = array();

		// set authorization bearer
		if (isset($this->token['access_token'])) {
			$authorization = 'Authorization: '.$this->token['token_type'].' '.$this->token['access_token'];
			$parameters[CURLOPT_HTTPHEADER] = array('Content-Type: application/json',$authorization);
		}

		$url = $this->mastodon_url.$url;
		if (count($data)) {
			$url .= '?'.http_build_query($data);
		}

		$response = $this->get_content_curl($url,$parameters);
		return $response;
	}

	/**
	 * _patch
	 *
	 * @param   string      $url
	 * @param   array       $data
	 *
	 * @return  array       $parameters
	 */
	private function _patch ($url,$data = array()) {
		$parameters = array();
		$parameters[CURLOPT_CUSTOMREQUEST] = 'PATCH';

		// set authorization bearer
		if (isset($this->token['access_token'])) {
			$authorization = 'Authorization: '.$this->token['token_type'].' '.$this->token['access_token'];
			$parameters[CURLOPT_HTTPHEADER] = array('Content-Type: application/json',$authorization);
		}

		if (count($data)) {
			$parameters[CURLOPT_POSTFIELDS] = $data;
		}

		$url = $this->mastodon_url.$url;
		$response = $this->get_content_curl($url,$parameters);
		return $response;
	}

	/**
	 * _delete
	 *
	 * @param   string      $url
	 *
	 * @return  array       $response
	 */
	private function _delete ($url) {
		$parameters = array();
		$parameters[CURLOPT_CUSTOMREQUEST] = 'DELETE';

		// set authorization bearer
		if (isset($this->token['access_token'])) {
			$authorization = 'Authorization: '.$this->token['token_type'].' '.$this->token['access_token'];
			$parameters[CURLOPT_HTTPHEADER] = array('Content-Type: application/json',$authorization);
		}

		$url = $this->mastodon_url.$url;
		$response = $this->get_content_curl($url,$parameters);
		return $response;
	}

	/**
	 * get_content_curl
	 *
	 * @param   string      $url
	 * @param   array       $parameters
	 *
	 * @return  array       $returnData
	 */
	protected function get_content_curl ($url,$parameters = array()) {
		$returnData = array(); #the data we return from the function
		$args = array(); #the http headers args

		//Set standart values to args
	    		$args = array(
	    			'timeout' => '5',
	    			'redirection' => '5',
	    			'httpversion' => '1.0',
	    			'blocking' => true,
				    'cookies' => array()
				);

		//Set USERAGENT
			if (!isset($parameters[CURLOPT_USERAGENT])) {
				//There is no given useragent in the parameters
					if (isset($_SERVER['HTTP_USER_AGENT'])) {
						$args['user-agent'] = $_SERVER['HTTP_USER_AGENT'];
					} else {
						// default IE11
						$args['user-agent'] = 'Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko';
					}
			}else{
				//Take the useragent from the parameters
					$args['user-agent'] = $parameters[CURLOPT_USERAGENT];
			}

		//Set HEADER
			if(isset($parameters[CURLOPT_HEADER])){
				$args['headers'] = $parameters[CURLOPT_HEADER];
			}

		//Set BODY
			if(isset($parameters[CURLOPT_POSTFIELDS])){
				$args['body'] = $parameters[CURLOPT_POSTFIELDS];
			}


		//Request method: Standart GET
			if(isset($parameters[CURLOPT_POST])){ 
				//POST
				//Trigger the call
					$args['method'] = 'POST';
			}elseif(isset($parameters[CURLOPT_CUSTOMREQUEST])){
				//CUSTOM
				//Trigger the call
					$args['method'] = $parameters[CURLOPT_CUSTOMREQUEST];
			}else{
				//GET
				//Trigger the call
					$args['method'] = 'GET';
			}
		

		//Actually trigger request
			$response = wp_remote_request ( $url , $args );
		

		//Build return data
			$returnData['html'] = json_decode(wp_remote_retrieve_body($response),True);
			$returnData['response'] = json_decode(wp_remote_retrieve_response_code( $response ));
		//Return the response of the request	
			return $returnData;
	}


	/**
	 * get_client_id
	 *
	 */
	public function get_client_id() {
		return $this->client_id;
	}

	/**
	 * get_client_secret
	 *
	 */
	public function get_client_secret() {
		return $this->client_secret;
	}



	/**
	 * get_access_token
	 *
	 */
	public function get_access_token() {
		return $this->token['access_token'];
	}

	/**
	 * get_access_token
	 *
	 */
	public function get_token_type() {
		return $this->token['token_type'];
	}

	/**
	 * set_url
	 *
	 * @param   string      $path
	 */
	public function set_url ($path) {
		$this->mastodon_url = $path;
	}

	/**
	 * set_client
	 *
	 * @param   string      $id
	 * @param   string      $secret
	 */
	public function set_client ($id,$secret) {
		$this->client_id = $id;
		$this->client_secret = $secret;
	}

	/**
	 * set_token
	 *
	 * @param   string      $token
	 * @param   string      $type
	 */
	public function set_token ($token,$type) {
		$this->token['access_token'] = $token;
		$this->token['token_type'] = $type;
	}

	/**
	 * set_scopes
	 *
	 * @param   array       $scopes     read / write / follow
	 */
	public function set_scopes ($scopes) {
		$this->scopes = $scopes;
	}

	/**
	 * create_app
	 *
	 * @param   string      $client_name
	 * @param   array       $scopes             read / write / follow
	 * @param   string      $redirect_uris
	 * @param   string      $website
	 *
	 * @return  array       $response
	 *          int         $response['id']
	 *          string      $response['redirect_uri']
	 *          string      $response['client_id']
	 *          string      $response['client_secret']
	 */
	public function create_app ($client_name,$scopes = array(),$redirect_uris = '',$website = '') {
		$parameters = array();

		if (count($scopes) == 0) {
			if (count($this->scopes) == 0) {
				$scopes = array('read','write','follow');
			} else {
				$scopes = $this->scopes;
			}
		}

		$parameters['client_name'] = $client_name;
		$parameters['scopes'] = implode(' ',$scopes);

		if (empty($redirect_uris)) {
			$parameters['redirect_uris'] = 'urn:ietf:wg:oauth:2.0:oob';
		} else {
			$parameters['redirect_uris'] = $redirect_uris;
		}

		if ($website) {
			$parameters['website'] = $website;
		}

		$response = $this->_post('/api/v1/apps',$parameters);

		if (isset($response['html']['client_id'])) {
			$this->client_id = $response['html']['client_id'];
			$this->client_secret = $response['html']['client_secret'];
		}

		return $response;
	}

	/**
	 * login
	 *
	 * @param   string      $id             E-mail Address
	 * @param   string      $password       Password
	 *
	 * @return  array       $response
	 *          string      $response['access_token']
	 *          string      $response['token_type']         bearer
	 *          string      $response['scope']              read
	 *          int         $response['created_at']         time
	 */
	public function login ($id,$password) {
		$parameters = array();
		$parameters['client_id'] = $this->client_id;
		$parameters['client_secret'] = $this->client_secret;
		$parameters['grant_type'] = 'password';
		$parameters['username'] = $id;
		$parameters['password'] = $password;

		if (count($this->scopes) == 0) {
			$parameters['scope'] = implode(' ',array('read','write','follow'));
		} else {
			$parameters['scope'] = implode(' ',$this->scopes);
		}

		$response = $this->_post('/oauth/token',$parameters);

		if (isset($response['html']['access_token'])) {
			$this->token['access_token'] = $response['html']['access_token'];
			$this->token['token_type'] = $response['html']['token_type'];
		}

		return $response;
	}

	/**
	 * accounts
	 *
	 * @see     https://your-domain/web/accounts/:id
	 *
	 * @param   int         $id
	 *
	 * @return  array       $response
	 *          int         $response['id']
	 *          string      $response['username']
	 *          string      $response['acct']
	 *          string      $response['display_name']           The name to display in the user's profile
	 *          bool        $response['locked']
	 *          string      $response['created_at']
	 *          int         $response['followers_count']
	 *          int         $response['following_count']
	 *          int         $response['statuses_count']
	 *          string      $response['note']                   A new biography for the user
	 *          string      $response['url']
	 *          string      $response['avatar']                 A base64 encoded image to display as the user's avatar
	 *          string      $response['avatar_static']
	 *          string      $response['header']                 A base64 encoded image to display as the user's header image
	 *          string      $response['header_static']
	 */
	public function accounts ($id) {
		$response = $this->_get('/api/v1/accounts/'.$id);
		return $response;
	}

	/**
	 * accounts_verify_credentials
	 *
	 * Getting the current user
	 *
	 * @return  array       $response
	 */
	public function accounts_verify_credentials () {
		$response = $this->_get('/api/v1/accounts/verify_credentials');
		return $response;
	}

	/**
	 * accounts_update_credentials
	 *
	 * Updating the current user
	 *
	 * @param   array       $parameters
	 *          string      $parameters['display_name']     The name to display in the user's profile
	 *          string      $parameters['note']             A new biography for the user
	 *          string      $parameters['avatar']           A base64 encoded image to display as the user's avatar
	 *          string      $parameters['header']           A base64 encoded image to display as the user's header image
	 *
	 * @return  array   $response
	 */
	public function accounts_update_credentials ($parameters) {
		$response = $this->_patch('/api/v1/accounts/update_credentials',$parameters);
		return $response;
	}

	/**
	 * accounts_followers
	 *
	 * @see     https://your-domain/web/accounts/:id
	 *
	 * @param   int         $id
	 *
	 * @return  array       $response
	 */
	public function accounts_followers ($id) {
		$response = $this->_get('/api/v1/accounts/'.$id.'/followers');
		return $response;
	}

	/**
	 * accounts_following
	 *
	 * @see     https://your-domain/web/accounts/:id
	 *
	 * @param   int         $id
	 *
	 * @return  array       $response
	 */
	public function accounts_following ($id) {
		$response = $this->_get('/api/v1/accounts/'.$id.'/following');
		return $response;
	}

	/**
	 * accounts_statuses
	 *
	 * @see     https://your-domain/web/accounts/:id
	 *
	 * @param   int         $id
	 *
	 * @return  array       $response
	 */
	public function accounts_statuses ($id) {
		$response = $this->_get('/api/v1/accounts/'.$id.'/statuses');
		return $response;
	}

	/**
	 * accounts_follow
	 *
	 * @see     https://your-domain/web/accounts/:id
	 *
	 * @param   int         $id
	 *
	 * @return  array       $response
	 */
	public function accounts_follow ($id) {
		$response = $this->_post('/api/v1/accounts/'.$id.'/follow');
		return $response;
	}

	/**
	 * accounts_unfollow
	 *
	 * @see     https://your-domain/web/accounts/:id
	 *
	 * @param   int         $id
	 *
	 * @return  array       $response
	 */
	public function accounts_unfollow ($id) {
		$response = $this->_post('/api/v1/accounts/'.$id.'/unfollow');
		return $response;
	}

	/**
	 * accounts_block
	 *
	 * @see     https://your-domain/web/accounts/:id
	 *
	 * @param   int         $id
	 *
	 * @return  array       $response
	 */
	public function accounts_block ($id) {
		$response = $this->_post('/api/v1/accounts/'.$id.'/block');
		return $response;
	}

	/**
	 * accounts_unblock
	 *
	 * @see     https://your-domain/web/accounts/:id
	 *
	 * @param   int         $id
	 *
	 * @return  array       $response
	 */
	public function accounts_unblock ($id) {
		$response = $this->_post('/api/v1/accounts/'.$id.'/unblock');
		return $response;
	}

	/**
	 * accounts_mute
	 *
	 * @see     https://your-domain/web/accounts/:id
	 *
	 * @param   int         $id
	 *
	 * @return  array       $response
	 */
	public function accounts_mute ($id) {
		$response = $this->_post('/api/v1/accounts/'.$id.'/mute');
		return $response;
	}

	/**
	 * accounts_unmute
	 *
	 * @see     https://your-domain/web/accounts/:id
	 *
	 * @param   int         $id
	 *
	 * @return  array       $response
	 */
	public function accounts_unmute ($id) {
		$response = $this->_post('/api/v1/accounts/'.$id.'/unmute');
		return $response;
	}

	/**
	 * accounts_relationships
	 *
	 * @see     https://your-domain/web/accounts/:id
	 *
	 * @param   array       $parameters
	 *          int         $parameters['id']
	 *
	 * @return  array       $response
	 *          int         $response['id']
	 *          bool        $response['following']
	 *          bool        $response['followed_by']
	 *          bool        $response['blocking']
	 *          bool        $response['muting']
	 *          bool        $response['requested']
	 */
	public function accounts_relationships ($parameters) {
		$response = $this->_get('/api/v1/accounts/relationships',$parameters);
		return $response;
	}

	/**
	 * accounts_search
	 *
	 * @param   array       $parameters
	 *          string      $parameters['q']
	 *          int         $parameters['limit']        default : 40
	 *
	 * @return  array       $response
	 */
	public function accounts_search ($parameters) {
		$response = $this->_get('/api/v1/accounts/search',$parameters);
		return $response;
	}

	/**
	 * blocks
	 *
	 * @return  array       $response
	 */
	public function blocks () {
		$response = $this->_get('/api/v1/blocks');
		return $response;
	}

	/**
	 * favourites
	 *
	 * @return  array       $response
	 */
	public function favourites () {
		$response = $this->_get('/api/v1/favourites');
		return $response;
	}

	/**
	 * follow_requests
	 *
	 * @return  array       $response
	 */
	public function follow_requests () {
		$response = $this->_get('/api/v1/follow_requests');
		return $response;
	}

	/**
	 * follow_requests_authorize
	 *
	 * @see     https://your-domain/web/accounts/:id
	 *
	 * @param   int         $id
	 *
	 * @return  array       $response
	 */
	public function follow_requests_authorize ($id) {
		$response = $this->_post('/api/v1/follow_requests/authorize',array('id'=>$id));
		return $response;
	}

	/**
	 * follow_requests_reject
	 *
	 * @see     https://your-domain/web/accounts/:id
	 *
	 * @param   int         $id
	 * @return  array       $response
	 */
	public function follow_requests_reject ($id) {
		$response = $this->_post('/api/v1/follow_requests/reject',array('id'=>$id));
		return $response;
	}

	/**
	 * follows
	 *
	 * Following a remote user
	 *
	 * @param   string      $uri            username@domain of the person you want to follow
	 * @return  array       $response
	 */
	public function follows ($uri) {
		$response = $this->_post('/api/v1/follows',array('uri'=>$uri));
		return $response;
	}

	/**
	 * instance
	 *
	 * Getting instance information
	 *
	 * @return  array       $response
	 *          string      $response['uri']
	 *          string      $response['title']
	 *          string      $response['description']
	 *          string      $response['email']
	 */
	public function instance () {
		$response = $this->_get('/api/v1/instance');
		return $response;
	}

	/**
	 * media
	 *
	 * Uploading a media attachment
	 *
	 * @param   string      $file_path                  local path / http path
	 *
	 * @return  array       $response
	 *          int         $response['id']             ID of the attachment
	 *          string      $response['type']           One of: "image", "video", "gifv"
	 *          string      $response['url']            URL of the locally hosted version of the image
	 *          string      $response['remote_url']     For remote images, the remote URL of the original image
	 *          string      $response['preview_url']    URL of the preview image
	 *          string      $response['text_url']       Shorter URL for the image, for insertion into text (only present on local images)
	 */
	public function media ($file_path) {
		$url = $this->mastodon_url.'/api/v1/media';
		$parameters = $data = array();
		$parameters[CURLOPT_HTTPHEADER] = array('Content-Type'=>'multipart/form-data');
		$parameters[CURLOPT_POST] = true;

		// set access_token
		if (isset($this->token['access_token'])) {
			$parameters[CURLOPT_POSTFIELDS]['access_token'] = $this->token['access_token'];
		}

		if (is_file($file_path)) {
			$mime_type = mime_content_type($file_path);

			$cf = curl_file_create($file_path,$mime_type,'file');
			$parameters[CURLOPT_POSTFIELDS]['file'] = $cf;
		}

		$response = $this->get_content_curl($url,$parameters);
		return $response;
	}

	/**
	 * mutes
	 *
	 * Fetching a user's mutes
	 *
	 * @return  array       $response
	 */
	public function mutes () {
		$response = $this->_get('/api/v1/mutes');
		return $response;
	}

	/**
	 * notifications
	 *
	 * @param   int     $id
	 *
	 * @return  array   $response
	 */
	public function notifications ($id = 0) {
		$url = '/api/v1/notifications';

		if ($id > 0) {
			$url .= '/'.$id;
		}

		$response = $this->_get($url);
		return $response;
	}

	/**
	 * notifications_clear
	 *
	 * Clearing notifications
	 *
	 * @return  array   $response
	 */
	public function notifications_clear () {
		$response = $this->_post('/api/v1/notifications/clear');
		return $response;
	}

	/**
	 * get_reports
	 *
	 * Fetching a user's reports
	 *
	 * @return  array   $response
	 */
	public function get_reports () {
		$response = $this->_get('/api/v1/reports');
		return $response;
	}

	/**
	 * post_reports
	 *
	 * Reporting a user
	 *
	 * @param   array   $parameters
	 *          int     $parameters['account_id']       The ID of the account to report
	 *          int     $parameters['status_ids']       The IDs of statuses to report (can be an array)
	 *          string  $parameters['comment']          A comment to associate with the report.
	 *
	 * @return  array   $response
	 */
	public function post_reports ($parameters) {
		$response = $this->_post('/api/v1/reports',$parameters);
		return $response;
	}

	/**
	 * search
	 *
	 * Searching for content
	 *
	 * @param   array   $parameters
	 *          string  $parameters['q']            The search query
	 *          string  $parameters['resolve']      Whether to resolve non-local accounts
	 *
	 * @return  array   $response
	 */
	public function search ($parameters) {
		$response = $this->_get('/api/v1/search',$parameters);
		return $response;
	}

	/**
	 * statuses
	 *
	 * Fetching a status
	 *
	 * @param   int     $id
	 *
	 * @return  array   $response
	 */
	public function statuses ($id) {
		$response = $this->_get('/api/v1/statuses/'.$id);
		return $response;
	}

	/**
	 * statuses_context
	 *
	 * Getting status context
	 *
	 * @param   int     $id
	 *
	 * @return  array   $response
	 */
	public function statuses_context ($id) {
		$response = $this->_get('/api/v1/statuses/'.$id.'/context');
		return $response;
	}

	/**
	 * statuses_card
	 *
	 * Getting a card associated with a status
	 *
	 * @param   int     $id
	 *
	 * @return  array   $response
	 */
	public function statuses_card ($id) {
		$response = $this->_get('/api/v1/statuses/'.$id.'/card');
		return $response;
	}

	/**
	 * statuses_reblogged_by
	 *
	 * Getting who reblogged a status
	 *
	 * @param   int     $id
	 *
	 * @return  array   $response
	 */
	public function statuses_reblogged_by ($id) {
		$response = $this->_get('/api/v1/statuses/'.$id.'/reblogged_by');
		return $response;
	}

	/**
	 * statuses_favourited_by
	 *
	 * Getting who favourited a status
	 *
	 * @param   int     $id
	 *
	 * @return  array   $response
	 */
	public function statuses_favourited_by ($id) {
		$response = $this->_get('/api/v1/statuses/'.$id.'/favourited_by');
		return $response;
	}

	/**
	 * post_statuses
	 *
	 * @param   array       $parameters
	 *          string      $parameters['status']               The text of the status
	 *          int         $parameters['in_reply_to_id']       (optional): local ID of the status you want to reply to
	 *          int         $parameters['media_ids']            (optional): array of media IDs to attach to the status (maximum 4)
	 *          string      $parameters['sensitive']            (optional): set this to mark the media of the status as NSFW
	 *          string      $parameters['spoiler_text']         (optional): text to be shown as a warning before the actual content
	 *          string      $parameters['visibility']           (optional): either "direct", "private", "unlisted" or "public"
	 *
	 * @return  array       $response
	 */
	public function post_statuses ($parameters) {
		$response = $this->_post('/api/v1/statuses',$parameters);
		return $response;
	}

	/**
	 * delete_statuses
	 *
	 * Deleting a status
	 *
	 * @param   int     $id
	 *
	 * @return  array   $response       empty
	 */
	public function delete_statuses ($id) {
		$response = $this->_delete('/api/v1/statuses/'.$id);
		return $response;
	}

	/**
	 * statuses_reblog
	 *
	 * Reblogging a status
	 *
	 * @param   int     $id
	 *
	 * @return  array   $response
	 */
	public function statuses_reblog ($id) {
		$response = $this->_post('/api/v1/statuses/'.$id.'/reblog');
		return $response;
	}

	/**
	 * statuses_unreblog
	 *
	 * Unreblogging a status
	 *
	 * @param   int     $id
	 *
	 * @return  array   $response
	 */
	public function statuses_unreblog ($id) {
		$response = $this->_post('/api/v1/statuses/'.$id.'/unreblog');
		return $response;
	}

	/**
	 * statuses_favourite
	 *
	 * Favouriting a status
	 *
	 * @param   int     $id
	 *
	 * @return  array   $response
	 */
	public function statuses_favourite ($id) {
		$response = $this->_post('/api/v1/statuses/'.$id.'/favourite');
		return $response;
	}

	/**
	 * statuses_unfavourite
	 *
	 * Unfavouriting a status
	 *
	 * @param   int     $id
	 *
	 * @return  array   $response
	 */
	public function statuses_unfavourite ($id) {
		$response = $this->_post('/api/v1/statuses/'.$id.'/unfavourite');
		return $response;
	}

	/**
	 * timelines_home
	 *
	 * @return  array   $response
	 */
	public function timelines_home () {
		$response = $this->_get('/api/v1/timelines/home');
		return $response;
	}

	/**
	 * timelines_public
	 *
	 * @param   array   $parameters
	 *          bool    $parameters['local']    Only return statuses originating from this instance
	 *
	 * @return  array   $response
	 */
	public function timelines_public ($parameters = array()) {
		$response = $this->_get('/api/v1/timelines/public',$parameters);
		return $response;
	}

	/**
	 * timelines_tag
	 *
	 * @param   string      $hashtag
	 * @param   array       $parameters
	 *          bool        $parameters['local']    Only return statuses originating from this instance
	 *
	 * @return  array       $response
	 */
	public function timelines_tag ($hashtag,$parameters = array()) {
		$response = $this->_get('/api/v1/timelines/tag/'.$hashtag,$parameters);
		return $response;
	}
}
