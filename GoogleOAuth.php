<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Isotope eCommerce Workgroup 2009-2011
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @author     Fred Bliss <fred.bliss@intelligentspark.com>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * Class GoogleOAuth - provides basic methods for working with Google OAuth API
 * Based on Abraham Williams' OAuth for Twitter's REST API (abraham@abrah.am) http://abrah.am
 *
 * @copyright  Winans Creative 2012
 * @author     Blair Winans <russ@winanscreative.com>
 * @author     Russell Winans <russ@winanscreative.com>
 * @package    Controller
 */
 

/* Load OAuth lib. You can find it at http://oauth.net */
require_once('OAuth.php');

/**
 * Google OAuth class
 */
class GoogleOAuth {
  /* Contains the last HTTP status code returned. */
  public $http_code;
  /* Contains the last API call. */
  public $url;
  /* Set up the API root URL. */
  public $host = "https://www.google.com/accounts/";
  /* Set timeout default. */
  public $timeout = 30;
  /* Set connect timeout. */
  public $connecttimeout = 30; 
  /* Verify SSL Cert. */
  public $ssl_verifypeer = FALSE;
  /* Respons format. */
  public $format = 'json';
  /* Decode returned json data. */
  public $decode_json = TRUE;
  /* Contains the last HTTP headers returned. */
  public $http_info;
  /* Set the useragnet. */
  public $useragent = 'Contao (+http://www.contao.org/)';
  
  function accessTokenURL()  { return 'https://www.google.com/accounts/OAuthGetAccessToken'; }
  function authorizeURL()    { return 'https://www.google.com/accounts/OAuthAuthorizeToken'; }
  function requestTokenURL() { return 'https://www.google.com/accounts/OAuthGetRequestToken'; }


  /**
   * construct GoogleOAuth object
   */
  function __construct($consumer_key, $consumer_secret, $oauth_token = NULL, $oauth_token_secret = NULL) {
	if($consumer_key == NULL || $consumer_secret==null){
		$this->disabled = true;
	}
	else{
		$this->disabled = false;
  		$this->scope = 'https://www.googleapis.com/auth/structuredcontent';
  	  	$this->sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
  	  	$this->consumer = new OAuthConsumer($consumer_key, $consumer_secret);
 	  	if (!empty($oauth_token) && !empty($oauth_token_secret)) {
  	  	 	$this->token = new OAuthConsumer($oauth_token, $oauth_token_secret);
  	  	} else {
  	    	$this->token = NULL;
  	 	}		
	}

  }


  /**
   * Get a request_token from Google
   *
   * @returns a key/value array containing oauth_token and oauth_token_secret
   */
  function getRequestToken($oauth_callback = NULL) {
    $parameters = array();
    if (!empty($oauth_callback)) {
      $parameters['oauth_callback'] = $oauth_callback;
    } 
   
    $parameters['scope']=$this->scope;    
    
    $request = $this->oAuthRequest($this->requestTokenURL(), 'GET', $parameters, NULL);

    $token = OAuthUtil::parse_parameters($request);
    $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']); 
    return $token;
  }

  /**
   * Get the authorize URL
   *
   * @returns a string
   */
  function getAuthorizeURL($token) {
    if (is_array($token)) {
      $token = $token['oauth_token'];
      }
	return $this->authorizeURL() . "?oauth_token={$token}";
  }

  /**
   * Exchange request token and secret for an access token and
   * secret, to sign API calls.
   */
  function getAccessToken($ttoken, $oauth_verifier = FALSE) {
    $parameters = array();
    if (!empty($oauth_verifier)) {
      $parameters['oauth_verifier'] = $oauth_verifier;
    }
    $parameters['scope']=$this->scope;
    $request = $this->oAuthRequest($this->accessTokenURL(), 'GET', $parameters, NULL);    
    $token = OAuthUtil::parse_parameters($request);
    $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
    return $token;
  }
  
  /**
   * GET wrapper for oAuthRequest.
   */
  function get($url, $data, $reqtoken) {
    $response = $this->oAuthRequest($url, 'GET', $parameters, $data);
    return $response;
  }
  
  /**
   * POST wrapper for oAuthRequest.
   */
  function post($url, $data, $reqtoken) {
    $response = $this->oAuthRequest($url, 'POST', $parameters, $data); 
    return $response;
  }

  /**
   * DELETE wrapper for oAuthReqeust.
   */
  function delete($url, $data, $reqtoken) {
    $response = $this->oAuthRequest($url, 'DELETE', $parameters, $data);
    return $response;
  }
  
  /**
   * Format and sign an OAuth / API request
   */
  function oAuthRequest($url, $method, $parameters, $data) {

    if (strrpos($url, 'https://') !== 0 && strrpos($url, 'http://') !== 0) {
      $url = "{$this->host}{$url}.{$this->format}";
    }
 
    $request = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $method, $url, $parameters);
      
    $request->sign_request($this->sha1_method, $this->consumer, $this->token);

    
    switch ($method) {
    case 'GET':

      return $this->http($request->to_url(), 'GET', $data=array(), $request);
      
    default:
      return $this->http($request->get_normalized_http_url(), $method, $data, $request);
    }
  }

 function http($url, $method, $data, $request) {
    switch ($method) {
    case 'GET':
    	/* Curl settings GET*/
    	$this->http_info = array();
    	$ci = curl_init();
    	curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
    	curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
    	curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
    	curl_setopt($ci, CURLOPT_HTTPHEADER, array('Expect:'));
   		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);    
    	curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
		curl_setopt($ci, CURLOPT_HEADER, FALSE);
		curl_setopt($ci, CURLOPT_URL, $url);
		$response = curl_exec($ci);    
    	$this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
    	$this->http_info = array_merge($this->http_info, curl_getinfo($ci));
    	$this->url = $url;
    	curl_close ($ci);    
    	return $response;
      	break;
    
	case 'POST':
		/* Curl settings POST*/
		$ci = curl_init($url . '?alt=atom');
		curl_setopt($ci, CURLOPT_POST, 1);
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ci, CURLOPT_HTTPHEADER, array('Content-Type: application/atom+xml', 
			$request->to_header()
			)
			);
		curl_setopt($ci, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ci, CURLOPT_HEADER, FALSE);
		$response = curl_exec($ci);
		curl_close($ci);
		return $response;
        break;
        
      case 'DELETE':
        $ci = curl_init();
    	/* Curl settings */
		$ci = curl_init($url);
		curl_setopt($ci, CURLOPT_POST, 1);
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);

		
		curl_setopt($ci, CURLOPT_HTTPHEADER, array('Content-Type: application/atom+xml', 
			$request->to_header(),
			'If-Match: *',
			)
			);
		curl_setopt($ci, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ci, CURLOPT_HEADER, FALSE);
        curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
		$response = curl_exec($ci);
		curl_close($ci);  
    	return $response;
      	break;
    }
  }

  /**
   * Get the header info to store.
   */
  function getHeader($ch, $header) {
    $i = strpos($header, ':');
    if (!empty($i)) {
      $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
      $value = trim(substr($header, $i + 2));
      $this->http_header[$key] = $value; 
    }
    return strlen($header);
  }
}
?>