<?php
/**
 * Intellectual Property of #Mastodon
 * 
 * @copyright (c) 2017, #Mastodon
 * @author V.A. (Victor) Angelier <victor@thecodingcompany.se>
 * @version 1.0
 * @license http://www.apache.org/licenses/GPL-compatibility.html GPL
 * 
 */
namespace theCodingCompany;

use theCodingCompany\HttpRequest;

/**
 * oAuth class for use at Mastodon
 */
trait oAuth
{
    
    /**
     * Our API to use
     * @var string
     */
    private $mastodon_api_url = "mastodon.social";
    
    /**
     * Default headers for each request
     * @var array
     */
    private $headers = array(
        "Content-Type" => "application/json; charset=utf-8", 
        "Accept" => "*/*"
    );
        
    /**
     * Holds our client_id and secret
     * @var array 
     */
    private $credentials = array(
        "client_id"     => "",
        "client_secret" => "",
        "bearer"        => ""
    );
    
    /**
     * App config
     * @var array
     */
    private $app_config = array(
        "client_name"   => "MastoTweet",
        "redirect_uris" => "urn:ietf:wg:oauth:2.0:oob",
        "scopes"        => "read write",
        "website"       => "https://www.thecodingcompany.se"
    );

    /**
     * Set credentials
     * @var array
     **/
     public function setCredentials(array $credentials)
     {
        $this->credentials = $credentials;
     }

     /**
     * Set credentials
     * @return array
     **/
     public function getCredentials()
     {
        return $this->credentials;
     }
    
    /**
     * Get the API endpoint
     * @return string
     */
    public function getApiURL(){
        return "https://{$this->mastodon_api_url}";
    }
    
    /**
     * Get Request headers
     * @return array
     */
    public function getHeaders(){
        if(isset($this->credentials["bearer"])){
            $this->headers["Authorization"] = "Bearer {$this->credentials["bearer"]}";
        }
        return $this->headers;
    }
    
    /**
     * Start at getting or creating app
     */
    public function getAppConfig(){
        //Get singleton instance
        $http = HttpRequest::Instance("https://{$this->mastodon_api_url}");
        $config = $http::post(
            "api/v1/apps", //Endpoint
            $this->app_config,
            $this->headers
        );
        //Check and set our credentials
        if(!empty($config) && isset($config["client_id"]) && isset($config["client_secret"])){
            $this->credentials['client_id'] = $config['client_id'];
            $this->credentials['client_secret'] = $config['client_secret'];
            return $this->credentials;
        }else{
            return false;
        }
    }
    
    /**
     * Set the correct domain name
     * @param string $domainname
     */
    public function setMastodonDomain($domainname = ""){
        if(!empty($domainname)){
            $this->mastodon_api_url = $domainname;
        }
    }
    
    /**
     * Create authorization url
     */
    public function getAuthUrl(){
        if(is_array($this->credentials) && isset($this->credentials["client_id"])){
            
            //Return the Authorization URL
            return "https://{$this->mastodon_api_url}/oauth/authorize/?".http_build_query(array(
                    "response_type"    => "code",
                    "redirect_uri"     => "urn:ietf:wg:oauth:2.0:oob",
                    "scope"            => "read write",
                    "client_id"        => $this->credentials["client_id"]
                ));
        }        
        return false;        
    }
    
    /**
     * Handle our bearer token info
     * @param array $token_info
     * @return string | boolean
     */
    private function _handle_bearer($token_info = null){
        if(!empty($token_info) && isset($token_info["access_token"])){
                
            //Add to our credentials
            $this->credentials["bearer"] = $token_info["access_token"];

            return $token_info["access_token"];
        }
        return false;
    }

    /**
     * Get access token
     * @param string $auth_code
     * @return string | bool
     */
    public function getAccessToken($auth_code = ""){
        
        if(is_array($this->credentials) && isset($this->credentials["client_id"])){
            
            //Request access token in exchange for our Authorization token
            $http = HttpRequest::Instance("https://{$this->mastodon_api_url}");
            $token_info = $http::Post(
                "oauth/token",
                array(
                    "grant_type"    => "authorization_code",
                    "redirect_uri"  => "urn:ietf:wg:oauth:2.0:oob",
                    "client_id"     => $this->credentials["client_id"],
                    "client_secret" => $this->credentials["client_secret"],
                    "code"          => $auth_code
                ),
                $this->headers
            );
            
            //Save our token info
            return $this->_handle_bearer($token_info);
        }
        return false;
    }

    /**
     * Authenticate a user by username and password
     * @param string $username usernam@domainname.com
     * @param string $password The password
     * @return bool
     */
    private function authUser($username = null, $password = null){
        if(!empty($username) && stristr($username, "@") !== FALSE && !empty($password)){
            

            if(is_array($this->credentials) && isset($this->credentials["client_id"])){

                //Request access token in exchange for our Authorization token
                $http = HttpRequest::Instance("https://{$this->mastodon_api_url}");
                $token_info = $http::Post(
                    "oauth/token",
                    array(
                        "grant_type"    => "password",
                        "client_id"     => $this->credentials["client_id"],
                        "client_secret" => $this->credentials["client_secret"],
                        "username"      => $username,
                        "password"      => $password,
                        "scope"         => "read write"
                    ),
                    $this->headers
                );
                
                return $this->credentials["bearer"] = $token_info["access_token"];
            }
        }        
        return false;
    }
}
