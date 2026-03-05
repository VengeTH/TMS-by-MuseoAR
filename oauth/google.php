<?php
require_once dirname(__DIR__) . "/vendor/autoload.php";
require_once dirname(__DIR__) . "/helpers/env.php";
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

		$clientId = safeEnv("GOOGLE_CLIENT_ID", "353540925058-rjjiqh9293el9qqn73100t8am2ahc4cm.apps.googleusercontent.com");
		$clientSecret = safeEnv("GOOGLE_CLIENT_SECRET", "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");
		$redirectUri = safeEnv("OAUTH_REDIRECT_URI", "http://localhost/oauth/google-callback.php");

		$this->client->setClientId($clientId);
		$this->client->setClientSecret($clientSecret);
		$this->client->setRedirectUri($redirectUri);
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