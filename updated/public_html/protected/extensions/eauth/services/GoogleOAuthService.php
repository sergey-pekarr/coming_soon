<?php
/**
 * GoogleOAuthService class file.
 * 
 * Register application: https://code.google.com/apis/console/
 * 
 * @author Maxim Zemskov <nodge@yandex.ru>
 * @link http://code.google.com/p/yii-eauth/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

require_once dirname(dirname(__FILE__)).'/EOAuth2Service.php';

/**
 * Google provider class.
 * @package application.extensions.eauth.services
 */
class GoogleOAuthService extends EOAuth2Service {	
	
	protected $use = 0;//oleg 2012-03-02
    
    protected $name = 'google_oauth';
	protected $title = 'Google';
	protected $type = 'OAuth';
	protected $jsArguments = array('popup' => array('width' => 500, 'height' => 450));

	protected $client_id = '';
	protected $client_secret = '';
	protected $scope = 'https://www.googleapis.com/auth/userinfo.profile';
	protected $providerOptions = array(
		'authorize' => 'https://accounts.google.com/o/oauth2/auth',
		'access_token' => 'https://accounts.google.com/o/oauth2/token',
	);
	
	protected function fetchAttributes() {
		$info = (array)$this->makeSignedRequest('https://www.googleapis.com/oauth2/v1/userinfo');
        //$info = (array)$this->makeSignedRequest('https://gdata.youtube.com');
				
		$this->attributes['id'] = $info['id'];
		$this->attributes['name'] = $info['name'];
		
		if (!empty($info['link']))
			$this->attributes['url'] = $info['link'];
		
		/*if (!empty($info['gender']))
			$this->attributes['gender'] = $info['gender'] == 'male' ? 'M' : 'F';
		
		if (!empty($info['picture']))
			$this->attributes['photo'] = $info['picture'];
		
		$info['given_name']; // first name
		$info['family_name']; // last name
		$info['birthday']; // format: 0000-00-00
		$info['locale']; // format: en*/
//CHelperSite::vd($info);        
        $this->attributes['me'] = $info;  //oleg 2012-02-28
        
        
        //https://developers.google.com/youtube/2.0/developers_guide_protocol_oauth2#OAuth2_Calling_a_Google_API
        $youTubeInfo = (array)$this->makeSignedRequest('https://gdata.youtube.com/feeds/api/users/default/uploads');
//CHelperSite::vd($youTubeInfo);          
        //https://developers.google.com/youtube/2.0/developers_guide_protocol_profiles
        $userYouTubeInfo = (array)$this->makeSignedRequest('https://gdata.youtube.com/feeds/api/users/default/uploads');
CHelperSite::vd($userYouTubeInfo);

        
/*$info = (array)$this->makeSignedRequest('https://www.googleapis.com/oauth2/v1/userinfo.email');
CHelperSite::vd($info);*/


	}

	protected function getCodeUrl($redirect_uri) {
		$this->setState('redirect_uri', $redirect_uri);
		$url = parent::getCodeUrl($redirect_uri);
		if (isset($_GET['js']))
			$url .= '&display=popup';
		return $url;
	}
	
	protected function getTokenUrl($code) {
		return $this->providerOptions['access_token'];
	}
	
	protected function getAccessToken($code) {
		$params = array(
			'client_id' => $this->client_id,
			'client_secret' => $this->client_secret,
			'grant_type' => 'authorization_code',
			'code' => $code,
			'redirect_uri' => $this->getState('redirect_uri'),
		);
		return $this->makeRequest($this->getTokenUrl($code), array('data' => $params));
	}
	
	/**
	 * Save access token to the session.
	 * @param stdClass $token access token array.
	 */
	protected function saveAccessToken($token) {
		$this->setState('auth_token', $token->access_token);
		$this->setState('expires', time() + $token->expires_in - 60);
		$this->access_token = $token->access_token;
	}
		
	/**
	 * Makes the curl request to the url.
	 * @param string $url url to request.
	 * @param array $options HTTP request options. Keys: query, data, referer.
	 * @param boolean $parseJson Whether to parse response in json format.
	 * @return string the response.
	 */
	protected function makeRequest($url, $options = array(), $parseJson = true) {
		$options['query']['alt'] = 'json';
		return parent::makeRequest($url, $options, $parseJson);
	}
	
	/**
	 * Returns the error info from json.
	 * @param stdClass $json the json response.
	 * @return array the error array with 2 keys: code and message. Should be null if no errors.
	 */
	protected function fetchJsonError($json) {
		if (isset($json->error)) {
			return array(
				'code' => $json->error->code,
				'message' => $json->error->message,
			);
		}
		else
			return null;
	}
}