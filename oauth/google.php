<?php
require_once dirname(__DIR__) . "/vendor/autoload.php";
/**
 * Google Oauth class
 * usage:
 * $client = new googleOauth();
 * $client->functionName($params);
 * you can add your own functions here. use generateOauth2 to generate oauth2.
 */
class googleOauth {
	private $client;
	private $Oauth2;
	// constructor
	public function __construct() {
		$this->client = new Google_Client();
		$this->client->setClientId(
			"353540925058-rjjiqh9293el9qqn73100t8am2ahc4cm.apps.googleusercontent.com",
		);
		$this->client->setClientSecret("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");
		// $this->client->setRedirectUri("http://localhost/Task%20Management/main/google-callback.php");
		$this->client->setRedirectUri("http://dev.aisukuri.mu/oauth/google-callback.php");
		$this->client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);
		$this->client->addScope(Google_Service_Oauth2::USERINFO_PROFILE);
	}
	public function getURL() {
		$authUrl = $this->client->createAuthUrl();
		if (!empty($authUrl)) {
			return $authUrl;
		} else {
			return null;
		}
	}
	public function fetchToken($authCode) {
		if (empty($authCode) || !isset($authCode)) {
			echo "Error: Auth code is empty.";
			return null;
		}
		$token = $this->client->fetchAccessTokenWithAuthCode($authCode);
		if ($this->client->isAccessTokenExpired()) {
			return null;
		}
		$this->client->setAccessToken($token);
		return $token;
	}
	public function generateOauth2() {
		$this->Oauth2 = new Google_Service_Oauth2($this->client);
		// check if oauth is generated successfully
		if ($this->Oauth2 == null) {
			return null;
		}
		return $this->Oauth2;
	}
	public function getUserInfo() {
		$userInfo = $this->Oauth2->userinfo->get();
		if ($userInfo == null) {
			return null;
		}
		return $userInfo;
	}
}
?>