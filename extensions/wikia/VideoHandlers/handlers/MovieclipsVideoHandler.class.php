<?php

class MovieclipsVideoHandler extends VideoHandler {
	
	protected $apiName = 'MovieclipsApiWrapper';
	protected static $aspectRatio = 1.84210526;	// 560 x 304
	protected static $urlTemplate = 'http://movieclips.com/e/';
	
	public function getEmbed($articleId, $width, $autoplay=false, $isAjax=false) {
		$height =  $this->getHeight( $width );
		
		$url = self::$urlTemplate . $this->videoId . '/';
		
		$embedCode = <<<EOT
<object width="$width" height="$height" type="application/x-shockwave-flash" data="$url/" style="background: #000000; display:block; overflow: hidden;">
	<param name="movie" value="$url" />
	<param name="allowfullscreen" value="true" />
	<param name="bgcolor" value="#000000" />
	<param name="wmode" value="transparent" />
	<param name="allowscriptaccess" value="always" />
EOT;
		$embedCode .= '<param name="FlashVars" value="autoPlay='.($autoplay ? 'true' : 'false').'" />';
		$embedCode .= <<<EOT
	<embed src="$url" type="application/x-shockwave-flash" allowfullscreen="true" movie="$url" wmode="transparent" allowscriptaccess="always" ></embed>
</object>
EOT;
	
		return $embedCode;
	}
		
}