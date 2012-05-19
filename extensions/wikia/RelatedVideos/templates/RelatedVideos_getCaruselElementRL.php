<?	$elementHeight = 90;
	$maxDescriptionLength = 80;
?>
<div class="item">
	<a class="video-thumbnail <?= $videoPlay; ?>" style="height:<?=$elementHeight; ?>px" href="<?=$video['fullUrl'];?>" data-ref="<?=$video['prefixedUrl'];?>" data-external="<?=$video['external'];?>" data-video-name="<?=$video['title'];?>" >
		<?
		if ( !empty( $video['duration'] ) ) {
			$mins = floor ($video['duration'] / 60);
			$secs = $video['duration'] % 60;
			$duration = $mins.':'.( ( $secs < 10 ) ? '0'.$secs : $secs );
		} else {
			$duration = 0;
		}
		if( !empty( $duration ) ){  ?><div class="timer"><?=$duration;?></div><? }
		if( !empty( $video['isNew'] ) ){  ?><div class="new"><?=wfMsg('related-videos-video-is-new');?><div  class="newRibbon" ></div></div><? } ?>
		<div class="playButton"></div>
		<img class="Wikia-video-thumb" data-src="<?=$video['thumbnailData']['thumb'];?>" src="<?=( $preloaded ) ? $video['thumbnailData']['thumb'] : wfBlankImgUrl();?>" style="margin-top:<?= floor( ( $elementHeight - $video['thumbnailData']['height'] ) / 2 ); ?>px; height:<?=$video['thumbnailData']['height'];?>px; width:<?=$video['thumbnailData']['width'];?>px;" />
	</a>
	<div class="description">
		<a class="<?= $videoPlay; ?>" href="<?=$video['fullUrl'];?>" data-ref="<?=$video['prefixedUrl'];?>" data-external="<?=$video['external'];?>" data-video-name="<?=$video['title'];?>" >
		<?=( strlen( $video['title'] ) > $maxDescriptionLength )
			? substr( $video['title'], 0, $maxDescriptionLength).'&#8230;'
			: $video['title'];
		?>
		</a>
	</div>
	<div class="options">
		<a class="remove" href="#"><img src="<?=wfBlankImgUrl();?>" /></a>
	</div>
</div>