<?php
/**
 * The City strategy for Opauth
 * 
 * More information on Opauth: http://opauth.org
 * 
 * @copyright    Copyright © 2013 The City Dev Force
 * @link         http://opauth.org
 * @package      Opauth.TheCityStrategy
 * @license      MIT License
 */
require('./opauth/lib/Opauth/OpauthStrategy.php');

/**
 * Google strategy for Opauth
 * based on https://developers.google.com/accounts/docs/OAuth2
 * 
 * @package			Opauth.TheCity
 */
class TheCityStrategy extends OpauthStrategy{

	/**
	 * Compulsory config keys, listed as unassociative arrays
	 */
	public $expects = array('client_id', 'client_secret');

	var $city_base_url = 'https://authentication.onthecity.org';
	private $oauth_path;
	private $oauth_authorize_path;
	private $oauth_token_path;
	private $oauth_authorization_path;

	/**
	 * Construct method.
	 *
	 * Initialize default properties.
	 *
	 * @param array $strategy Strategy-specific configuration
	 * @param array $env Safe env values from Opauth, with critical parameters stripped out
	 */
	public function __construct($strategy, $env) {
		$this->oauth_path = $this->city_base_url . '/oauth';
		$this->oauth_authorize_path = $this->oauth_path . '/authorize';
		$this->oauth_token_path = $this->oauth_path . '/token';
		$this->oauth_authorization_path = $this->city_base_url . '/authorization';

		parent::__construct($strategy, $env);
	}

	/**
	 * Auth request
	 */
	public function request(){
		$params = array(
			'client_id' => $this->strategy['client_id'],
			'redirect_uri' => $this->strategy['redirect_uri'],
			'response_type' => 'code',
			'scope' => $this->strategy['scope']
		);

		if (isset($this->strategy['subdomain'])) {
		  $params['subdomain'] = $this->strategy['subdomain'];
    }

		foreach ($this->optionals as $key){
			if (!empty($this->strategy[$key])) $params[$key] = $this->strategy[$key];
		}
		$this->clientGet($this->oauth_authorize_path, $params);
	}

	/**
	 * Internal callback, after OAuth
	 */
	public function oauth2callback(){
		if (!empty($_GET['code'])){
			$code = $_GET['code'];
			$params = array(
				'code' => $code,
				'client_id' => $this->strategy['client_id'],
				'client_secret' => $this->strategy['client_secret'],
				'redirect_uri' => $this->strategy['redirect_uri'],
				'grant_type' => 'authorization_code'
			);
			$response = $this->serverPost($this->oauth_token_path, $params, null, $headers);
			$results = json_decode($response);
			if (!empty($results) && !empty($results->access_token)) {
				$user_info = $this->userInfo($results->access_token);

				$this->auth = array(
					'uid' => $user_info['id'],
					'info' => array(),
					'credentials' => array(
						'token' => $results->access_token,
						'expires' => date('c', time() + $results->expires_in)
					),
					'raw' => $user_info
				);

				if (!empty($results->refresh_token))
				{
					$this->auth['credentials']['refresh_token'] = $results->refresh_token;
				}

				$this->mapProfile($user_info, 'email', 'info.email');
				$this->mapProfile($user_info, 'first', 'info.first_name');
				$this->mapProfile($user_info, 'last', 'info.last_name');
				$this->mapProfile($user_info, 'profile_picture', 'info.profile_picture');
echo '<pre>';
print_r($this->auth);
echo '</pre>';die;
				$this->callback();
			}
			else{
				$error = array(
					'code' => 'access_token_error',
					'message' => 'Failed when attempting to obtain access token',
					'raw' => array(
						'response' => $response,
						'headers' => $headers
					)
				);

				$this->errorCallback($error);
			}
		}
		else{
			$error = array(
				'code' => 'oauth2callback_error',
				'raw' => $_GET
			);

			$this->errorCallback($error);
		}
	}

	/**
	 * Queries Google API for user info
	 *
	 * @param string $access_token 
	 * @return array Parsed JSON results
	 */
	private function userInfo($access_token, array $options = array(), string $responseHeaders = NULL){
		$userInfo = $this->serverGet(
			$this->oauth_authorization_path,
			array('access_token' => $access_token, 'format' => 'json'),
			NULL,
			$options
		);
		//$userinfo = $this->serverGet('https://authentication.onthecity.org/authorization', array('access_token' => $access_token, 'format' => 'json'), NULL, $options);
		if (!empty($userInfo)){
			return $this->recursiveGetObjectVars(json_decode($userInfo));
		}
		else{
			$error = array(
				'code' => 'userinfo_error',
				'message' => 'Failed when attempting to query for user information',
				'raw' => array(
					'response' => $userInfo,
					'headers' => $headers
				)
			);

			$this->errorCallback($error);
		}
	}
}