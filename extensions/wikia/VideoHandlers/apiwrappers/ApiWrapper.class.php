<?php

abstract class ApiWrapper {

	const RESPONSE_FORMAT_JSON = 0;
	const RESPONSE_FORMAT_XML = 1;
	const RESPONSE_FORMAT_PHP = 2;

	protected static $aspectRatio = 1.7777778;

	protected $videoId;
	protected $metadata;
	protected $interfaceObj = null;

	protected static $API_URL;
	protected static $CACHE_KEY;
	protected static $CACHE_KEY_VERSION = 0.1;
	protected static $CACHE_EXPIRY = 86400;
	protected static $RESPONSE_FORMAT = self::RESPONSE_FORMAT_JSON;

	/**
	 * Get appropriate ApiWrapper for the given URL
	 * @param string $url url without opening "http://"
	 * @return mixed ApiWrapper or null
	 */
	public static function newFromUrl( $url ) {
		// if $url is from this provider, return object
		return null;
	}

	/**
	 * Does hostname match a URL from this video provider
	 * @param string $hostname in lower case
	 * @return boolean 
	 */
	public static function isMatchingHostname( $hostname ) {
		return false;
	}

	/**
	 *
	 * @param string $videoId
	 * @param array $overrideMetadata one or more metadata fields that override API response
	 * In this case, metadata is passed through constructor, so $orverrideMetadata should be set.
	 */
	public function __construct( $videoId, $overrideMetadata = array() ) {
		$this->videoId = $this->sanitizeVideoId( $videoId );
		if ( !empty( $overrideMetadata[ 'ingestedFromFeed' ] )
			|| $this->isIngestedFromFeed()) {
			// don't connect to api
		} else {
			$this->initializeInterfaceObject();
		}
		if ( !is_array( $overrideMetadata ) ) {
			$overrideMetadata = array();
		}
		$this->loadMetadata( $overrideMetadata );
	}

	/**
	 *
	 * @return string 
	 */
	public function getTitle() {
		if ( !empty( $this->metadata['title'] ) ) {
			return $this->metadata['title'];
		}
		return $this->getVideoTitle();
	}
	
	abstract protected function getVideoTitle();
	
	abstract public function getDescription();
	
	abstract public function getThumbnailUrl();

	public function getProvider() {
		return strtolower(str_replace('ApiWrapper', '', get_class($this)));
	}

	public function getMimeType() {
		return 'video/'.$this->getProvider();
	}
	
	public function getVideoId() {
		if (!$this->videoId) {
			if (isset($this->metadata['videoId'])) {
				$this->videoId = $this->metadata['videoId'];
			}
		}
		
		return $this->videoId;
	}
	
	protected function isIngestedFromFeed() {
		// need to check cached metadata
		$memcKey = F::app()->wf->memcKey( $this->getMetadataCacheKey() );
		$metadata = F::app()->wg->memc->get( $memcKey );
		return !empty( $metadata['ingestedFromFeed'] );
	}

	protected function postProcess( $return ){
		return $return;
	}
	
	protected function sanitizeVideoId( $videoId ) {
		return $videoId;
	}

	protected function initializeInterfaceObject(){
		$this->interfaceObj = $this->getInterfaceObjectFromType( static::$RESPONSE_FORMAT );
	}

	protected function getInterfaceObjectFromType( $type ) {

		$apiUrl = $this->getApiUrl();
		$memcKey = F::app()->wf->memcKey( static::$CACHE_KEY, $apiUrl, static::$CACHE_KEY_VERSION );
		if ( empty($this->videoId) ){
			throw new EmptyResponseException($apiUrl);
		}
		$response = F::app()->wg->memc->get( $memcKey );
		$cacheMe = false;
		if ( empty( $response ) ){
			$cacheMe = true;
			$req = HttpRequest::factory( $apiUrl );
			$status = $req->execute();
			if( $status->isOK() ) {
				$response = $req->getContent();
				$this->response = $response;	// Only for migration purposes
				if ( empty( $response ) ) {
					throw new EmptyResponseException($apiUrl);
				} else {
					
				}
			} else {
				$this->checkForResponseErrors( $req->status, $req->getContent(), $apiUrl );
			}
		}
		$processedResponse = $this->processResponse( $response, $type );
		if ( $cacheMe ) F::app()->wg->memc->set( $memcKey, $response, static::$CACHE_EXPIRY );
		return $processedResponse;
	}
	
	protected function getApiUrl() {
		$apiUrl = str_replace( '$1', $this->videoId, static::$API_URL );
		return $apiUrl;
	}

	protected function checkForResponseErrors( $status, $content, $apiUrl ){
		if (is_array($status->errors)) {
			foreach ($status->errors as $error) {
				if (!empty($error['params']) && is_array($error['params'])) {
					switch ($error['params'][0]) {
						case '403':
							throw new VideoIsPrivateException($status, $content, $apiUrl);
							break;
						default:
					}
				}
			}
		}
		
		throw new NegativeResponseException( $status, $content, $apiUrl );
	}

	protected function processResponse( $response, $type ){
		$return = '';
		switch ( $type ){
			case self::RESPONSE_FORMAT_JSON :
				 $return = json_decode( $response, true );
			break;
			case self::RESPONSE_FORMAT_XML :
				$sp = new SimplePie();
				$sp->set_raw_data( $response );
				$sp->init();
				if ( $sp->error() ) {
					$return = $sp->data;
				} else {
					$oItem = $sp->get_item();
					if ( empty( $oItem ) ) $this->videoNotFound();
					$return = get_object_vars( $oItem->get_enclosure() );
				}
			break;
			case self::RESPONSE_FORMAT_PHP :
				$return = unserialize( $response );
			break;
			default: throw new UnsuportedTypeSpecifiedException();
		}
		
		return $this->postProcess( $return );
	}

	protected function videoNotFound(){
		throw new VideoNotFound();
	}

	// metadata
	public function getMetadata() {
		if (empty($this->metadata)) {
			$this->loadMetadata();
		}
		
		return $this->metadata;
	}
	
	protected function loadMetadata(array $overrideFields=array()) {
		$memcKey = F::app()->wf->memcKey( $this->getMetadataCacheKey() );
		$metadata = F::app()->wg->memc->get( $memcKey );
		$cacheMe = false;
		if ( empty( $metadata ) ){
			$cacheMe = true;
			$metadata = $overrideFields;	// $overrideFields may have more fields
							// than the standard ones, listed below.
							// This is ok.
			$this->metadata = $metadata;	// must do this to facilitate getters below
							// $this->metadata will be reset at end of this function

			if (!isset($metadata['videoId']))
				$metadata['videoId']		= $this->videoId;
			if (!isset($metadata['title']))
				$metadata['title']		= $this->getTitle();
			if (!isset($metadata['published']))
				$metadata['published']		= $this->getVideoPublished();
			if (!isset($metadata['category']))
				$metadata['category']		= $this->getVideoCategory();
			if (!isset($metadata['canEmbed']))
				$metadata['canEmbed']		= $this->canEmbed();
			if (!isset($metadata['hd']))
				$metadata['hd']			= $this->isHdAvailable();
			if (!isset($metadata['keywords']))
				$metadata['keywords']		= $this->getVideoKeywords();
			if (!isset($metadata['duration']))
				$metadata['duration']		= $this->getVideoDuration();
			if (!isset($metadata['aspectRatio']))
				$metadata['aspectRatio']	= $this->getAspectRatio();
			if (!isset($metadata['description']))
				$metadata['description']	= $this->getOriginalDescription();	
			// for providers that use diffrent video id for embeded code
			if (!isset($metadata['altVideoId']))
				$metadata['altVideoId']		= $this->getAltVideoId();
			if (!isset($metadata['trailerRating']))
				$metadata['trailerRating']	= $this->getTrailerRating();
			if (!isset($metadata['industryRating']))
				$metadata['industryRating']	= $this->getIndustryRating();
			if (!isset($metadata['ageGate']))
				$metadata['ageGate']		= $this->isAgeGate();
			if (!isset($metadata['language']))
				$metadata['language']		= $this->getLanguage();
		}
		
		if ( $cacheMe ) {
			$result = F::app()->wg->memc->set( $memcKey, $metadata, static::$CACHE_EXPIRY );
		}
		
		$this->metadata = $metadata;
	}
	
	protected function getMetadataCacheKey() {
		$key = static::$CACHE_KEY . '_metadata' . '_' . static::$CACHE_KEY_VERSION . '_' . $this->videoId;
		return $key;
	}

	protected function getVideoPublished(){
		return '';
	}

	protected function getVideoCategory(){
		return '';
	}

	protected function canEmbed(){
		return true;
	}

	protected function getOriginalDescription(){
		return '';
	}

	protected function isHdAvailable(){
		return false;
	}

	/**
	 * List of keywords, separated by comma
	 * @return string
	 */
	protected function getVideoKeywords(){
		return '';
	}

	protected function getVideoDuration(){
		return '';
	}

	public function getAspectRatio(){
		return static::$aspectRatio;
	}

	protected function getAltVideoId() {
		return '';
	}
	
	/**
	 * MPAA trailer rating (e.g. "greenband", "redband") 
	 * @return string 
	 */
	protected function getTrailerRating() {
		return '';
	}
	
	/**
	 * Rating from industry board.
	 * Examples from MPAA: R, PG-13
	 * Examples from ESRB: E, T, AO
	 * @return string 
	 */
	protected function getIndustryRating() {
		return '';
	}
	
	/**
	 * Is clip age-gated?
	 * @return boolean
	 */
	protected function isAgeGate() {
		return false;
	}
	
	protected function getLanguage() {
		return '';
	}
}

class EmptyResponseException extends Exception {
	public function __construct( $apiUrl ) {
		$this->apiUrl = $apiUrl;
	}
}
class NegativeResponseException extends Exception {
	public function __construct( $status, $content, $apiUrl ) {
		$this->status = $status;
		$this->content = $content;
		$this->apiUrl = $apiUrl;
	}
}
class VideoIsPrivateException extends NegativeResponseException {}
class VideoNotFoundException extends NegativeResponseException {}
class VideoQuotaExceededException extends NegativeResponseException {}

class UnsuportedTypeSpecifiedException extends Exception {}
class VideoNotFound extends Exception {}

/**
 * A class that doesn't connect to a video provider's API, but implements
 * ApiWrapper's abstract functions nonetheless. Child classes of PseudoApiWrapper
 * might connect to a database instead of an API.
 */
abstract class PseudoApiWrapper extends ApiWrapper {
	
	protected function getInterfaceObjectFromType( $type ) {
		// override me!
	}
	
	protected function processResponse( $response, $type ) {
		// override me!
	}
}

/**
 * A class that gets video data for "old" videos from the WikiaVideo extension
 */
abstract class WikiaVideoApiWrapper extends PseudoApiWrapper {
	
	protected $videoName;
	protected $provider;
	
	public function __construct( $videoName, array $overrideMetadata=array() ) {
		$this->videoName = $videoName;
		if (!empty($overrideMetadata['ingestedFromFeed'])
		|| $this->isIngestedFromFeed()) {
			// don't connect to api
		}
		else {
			$this->initializeInterfaceObject();
		}
		if (!is_array($overrideMetadata)) {
			$overrideMetadata = array();
		}
		$this->loadMetadata($overrideMetadata);
		$this->getVideoId();	// lazy-load $this->videoId
	}
	
	protected function getInterfaceObjectFromType( $type ) {
		$title = Title::newFromText($this->videoName, NS_LEGACY_VIDEO);
		$videoPage = new VideoPage($title);
		$videoPage->load();
		$this->videoId = $videoPage->getVideoId();
		$this->provider = $videoPage->getProvider();
		$response = $videoPage->getData();
		$this->response = $response;	// only for migration purposes
		if ( empty( $response ) ) {
			throw new EmptyResponseException( $this->getApiUrl() );
		} else {

		}
		return $response;
	}
	
	protected function getVideoTitle() {
		return $this->videoName;
	}
	
	protected function getMetadataCacheKey() {
		$key = static::$CACHE_KEY . '_metadata' . '_' . static::$CACHE_KEY_VERSION . '_' . $this->videoName;
		return $key;
	}
}

/**
 * ApiWrapper for a video provider that has no API. This class does not attempt
 * to connect to any API.
 */
abstract class NullApiWrapper extends PseudoApiWrapper {
	//@todo change this url
	static $THUMBNAIL_URL = 'http://community.wikia.com/extensions/wikia/VideoHandlers/images/NoThumbnailBg.png';

	public function __construct($videoId, array $overrideMetadata=array()) {
		$this->videoId = $this->sanitizeVideoId( $videoId );
		if (!empty($overrideMetadata['ingestedFromFeed'])
		|| $this->isIngestedFromFeed()) {
			// don't connect to api
		}
		if (!is_array($overrideMetadata)) {
			$overrideMetadata = array();
		}
		$this->loadMetadata($overrideMetadata);
	}
	
	protected function getVideoTitle() {
		$classname = get_class($this);
		$provider = substr($classname, 0, strlen($classname)-strlen('ApiWrapper'));
		return wfMsg('videohandler-unknown-title') . " ($provider $this->videoId)";
	}
	
	public function getDescription() {
		return '';
	}
	
	public function getThumbnailUrl() {
		return self::$THUMBNAIL_URL;
	}
	
	protected function getInterfaceObjectFromType( $type ) {
		return null;
	}
	
	protected function processResponse( $response, $type ) {
		return null;
	}
	
}