<?php

class ViddlerVideoHandler extends VideoHandler {
	protected $apiName = 'ViddlerApiWrapper';
	protected static $aspectRatio = 1.56160458;
	protected static $urlTemplate = 'http://www.viddler.com/player/$1/';
	
	public function getEmbed($articleId, $width, $autoplay = false, $isAjax = false) {
		$height = $this->getHeight($width);
		$url = str_replace('$1', $this->getEmbedVideoId(), static::$urlTemplate);
		$embedVideoId = $this->getEmbedVideoId();
		$flashVars = '';

		$html = <<<EOT
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="$width" height="$height" id="viddler_$embedVideoId">
EOT;
		if ($autoplay) {
			$flashVars = ' flashvars="autoplay=t"';
			$html .= <<<EOT
	<param name="flashvars" value="autoplay=t" />
EOT;
		}
		$html .= <<<EOT
	<param name="movie" value="$url" />
	<param name="allowScriptAccess" value="always" />
	<param name="allowFullScreen" value="true" />
	<embed src="$url" width="$width" height="$height" type="application/x-shockwave-flash" allowScriptAccess="always"{$flashVars} allowFullScreen="true" name="viddler_$embedVideoId"></embed>
</object>
EOT;
		return $html;
	}

}