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

	/**
	 * Optional config keys, without predefining any default values.
	 */
	public $optionals = array('redirect_uri', 'scope', 'state', 'subdomain');

	/**
	 * Optional config keys with respective default values, listed as associative arrays
	 * eg. array('scope' => 'email');
	 */
	public $defaults = array(
		'redirect_uri' => '{complete_url_to_strategy}oauth2callback',
		'scope' => 'user_basic'
	);

	/**
	 * Auth request
	 */
	public function request(){
		$url = 'https://authentication.onthecity.org/oauth/authorize';
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

		$this->clientGet($url, $params);
	}

	/**
	 * Internal callback, after OAuth
	 */
	public function oauth2callback(){
		if (array_key_exists('code', $_GET) && !empty($_GET['code'])){
			$code = $_GET['code'];
			$url = 'https://authentication.onthecity.org/oauth/token';
			$params = array(
				'code' => $code,
				'client_id' => $this->strategy['client_id'],
				'client_secret' => $this->strategy['client_secret'],
				'redirect_uri' => $this->strategy['redirect_uri'],
				'grant_type' => 'authorization_code'
			);
			$response = $this->serverPost($url, $params, null, $headers);

			$results = json_decode($response);
			if (!empty($results) && !empty($results->access_token)) {
				$userinfo = $this->userinfo($results->access_token);

				$this->auth = array(
					'uid' => $userinfo['id'],
					'info' => array(),
					'credentials' => array(
						'token' => $results->access_token,
						'expires' => date('c', time() + $results->expires_in)
					),
					'raw' => $userinfo
				);

				if (!empty($results->refresh_token))
				{
					$this->auth['credentials']['refresh_token'] = $results->refresh_token;
				}

				$this->mapProfile($userinfo, 'email', 'info.email');
				$this->mapProfile($userinfo, 'first', 'info.first_name');
				$this->mapProfile($userinfo, 'last', 'info.last_name');
				$this->mapProfile($userinfo, 'profile_picture', 'info.profile_picture');
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
	private function userinfo($access_token, array $options = array()){
		$userinfo = $this->serverGet('https://authentication.onthecity.org/authorization', array('access_token' => $access_token, 'format' => 'json'), NULL, $options);
		if (!empty($userinfo)){
			return $this->recursiveGetObjectVars(json_decode($userinfo));
		}
		else{
			$error = array(
				'code' => 'userinfo_error',
				'message' => 'Failed when attempting to query for user information',
				'raw' => array(
					'response' => $userinfo,
					'headers' => $headers
				)
			);

			$this->errorCallback($error);
		}
	}
}