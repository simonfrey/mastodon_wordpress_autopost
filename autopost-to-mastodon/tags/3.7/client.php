<?php
class Client
{
	private $instance_url;
	private $access_token;
	private $app;

	public function __construct($instance_url, $access_token = '') {
		$this->instance_url = $instance_url;
		$this->access_token = $access_token;
	}

	public function register_app($redirect_uri) {

		$response = $this->_post('/api/v1/apps', array(
			'client_name' => 'Mastodon Share for WordPress',
			'redirect_uris' => $redirect_uri,
			'scopes' => 'write:statuses write:media read:accounts',
			'website' => $this->instance_url
		));

		if (!isset($response->client_id)){
			return "ERROR. Response: ".json_encode($response);
		}


		$this->app = $response;


		$params = http_build_query(array(
			'response_type' => 'code',
			'redirect_uri' => $redirect_uri,
			'scope' => 'write:statuses write:media read:accounts',
			'client_id' =>$this->app->client_id
		));

		return $this->instance_url.'/oauth/authorize?'.$params;
	}

	public function verify_credentials($access_token){

		$headers = array(
			'Authorization'=>'Bearer '.$access_token
		);

		$response = $this->_get('/api/v1/accounts/verify_credentials', null, $headers);

		return $response;
	}

	public function get_bearer_token($client_id, $client_secret, $code, $redirect_uri) {

		$response = $this->_post('/oauth/token',array(
			'grant_type' => 'authorization_code',
			'redirect_uri' => $redirect_uri,
			'client_id' => $client_id,
			'client_secret' => $client_secret,
			'code' => $code
		));

		return $response;
	}

	public function get_client_id() {
		return $this->app->client_id;
	}

	public function get_client_secret() {
		return $this->app->client_secret;
	}

	public function postStatus($status, $mode, $media = '', $spoiler_text = '') {

		$headers = array(
			'Authorization'=> 'Bearer '.$this->access_token
		);

		$response = $this->_post('/api/v1/statuses', array(
			'status' => $status,
			'visibility' => $mode,
			'spoiler_text' => $spoiler_text,
			'media_ids[]' => $media
		), $headers);

		return $response;
	}

	public function create_attachment($media_path, $description="") {

		$filename =basename($media_path);
		$mime_type = mime_content_type($media_path);

		$boundary ='hlx'.time();

		$headers = array (
			'Authorization'=> 'Bearer '.$this->access_token,
			'Content-Type' => 'multipart/form-data; boundary='. $boundary,
		);

		$nl = "\r\n";

		$data = '--'.$boundary.$nl;
		$data .= 'Content-Disposition: form-data; name="file"; filename="'.$filename.'"'.$nl;
		$data .= 'Content-Type: '. $mime_type .$nl;
		$data .= $nl;
		$data .= file_get_contents($media_path) .$nl;
		
		if($description) {
		    $data .= '--'.$boundary.$nl;
		    $data .= 'Content-Disposition: form-data; name="description"'.$nl;
		    $data .= $nl;
		    $data .= $description.$nl;
		}
		
		$data .= '--'.$boundary.'--';

		$response = $this->_post('/api/v1/media', $data, $headers);

		return $response;
	}

	private function _post($url, $data = array(), $headers = array()) {
		return $this->post($this->instance_url.$url, $data, $headers);
	}

	public function _get($url, $data = array(), $headers = array()) {
		return $this->get($this->instance_url.$url, $data, $headers);
	}

	private function post($url, $data = array(), $headers = array()) {
		$args = array(
		    'headers' => $headers,
		    'body'=> $data,
		    'redirection' => 5
		);

		$response = wp_remote_post( $this->getValidURL($url), $args );
		if ( is_wp_error( $response ) ) {
		    $error_message = $response->get_error_message();
		    
		} else {
		$responseBody = wp_remote_retrieve_body($response);
		return json_decode($responseBody);
	}

		return $response;
	}

	public function get($url, $data = array(), $headers = array()) {
		$args = array(
		    'headers' => $headers,
		    'redirection' => 5
		);
		$response = wp_remote_get( $this->getValidURL($url), $args );
		if ( is_wp_error( $response ) ) {
		    $error_message = $response->get_error_message();

		} else {
		$responseBody = wp_remote_retrieve_body($response);
		    return json_decode($responseBody);
		}

		return $response;
	}

	public function dump($value){
		echo '<pre>';
		print_r($value);
		echo '</pre>';
	}

	private function getValidURL($url){
		 if  ( $ret = parse_url($url) ) {
 			if ( !isset($ret["scheme"]) ){
				$url = "http://{$url}";
			}
		}
		return $url;

	}
}
