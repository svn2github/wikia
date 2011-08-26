<li class="feature">
	<img class="representation" height="100" width="150" src="<?= $wg->ExtensionsPath ?>/wikia/WikiFeatures/images/<?= $feature['name'] ?>.png" >
	<div class="actions">
		<span class="slider<?= $feature['enabled'] ? ' on' : '' ?>">
			<span class="button"></span>
			<span class="textoff">Off</span>
			<span class="texton">On</span>
			<span class="loading"></span>
		</span>
		<? if (isset($feature['rating'])) { ?>
			<button class="secondary feedback">
				<img height="10" width="10" src="<?= $wg->ExtensionsPath ?>/wikia/WikiFeatures/images/star-inactive.png">
				<?php echo wfMsg('wikifeatures-feedback'); ?>
			</button>
			<div class="rating" >
				<? $rating = $feature['rating']; ?>
				<? for ($i = 0; $i < 5; $i++, $rating--) { ?>
					<img <?= $rating > 0 ? 'class="active"' : '' ?> src="<?= $wg->BlankImgUrl ?>"/>
				<? } ?>
			</div>
			<div class="active-on">
				<?= wfMsg('wikifeatures-active-on', $feature['active']) ?>
			</div>
		<? } ?>
	</div>
	<div class="details">
		<h3>
			<?= wfMsg('wikifeatures-feature-heading-'.$feature['name']) ?>
		</h3>
		<p>
			<?= wfMsg('wikifeatures-feature-description-'.$feature['name']) ?>
		</p>
	</div>
</li>