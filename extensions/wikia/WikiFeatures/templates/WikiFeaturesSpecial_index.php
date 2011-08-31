<section class="WikiFeatures" id="WikiFeatures">
	<h2>
		<?= wfMsg('wikifeatures-heading') ?>
	</h2>
	<p>
		<?= wfMsg('wikifeatures-creative') ?>
	</p>
	
	<ul class="features">
		<? foreach ($features as $feature) { ?>
			<?= F::app()->getView( 'WikiFeaturesSpecial', 'feature', array('feature' => $feature ) ) ?>
		<? } ?>
	</ul>
	
	<h2>
		<?= wfMsg('wikifeatures-labs-heading') ?>
	</h2>
	<p>
		<?= wfMsg('wikifeatures-labs-creative') ?>
	</p>
	
	<ul class="features">
		<? foreach ($labsFeatures as $feature) { ?>
			<?= F::app()->getView( 'WikiFeaturesSpecial', 'feature', array('feature' => $feature ) ) ?>
		<? } ?>
	</ul>
</section>
<div id="FeedbackDialog" class="FeedbackDialog">
	<h1><?= wfMsg('wikifeatures-feedback-heading') ?></h1>

	<div class="feature-highlight">
		<h2></h2>
		<img src="<?= $wg->BlankImgUrl ?>">
	</div>

	<form>
		<p><?= wfMsg('wikifeatures-feedback-description') ?></p>
		
		<div class="input-group">
			<label><?= wfMsg('wikifeatures-feedback-type-label') ?></label>
			<select name="feedback">
			<?php foreach (WikiFeaturesHelper::$feedbackCategories as $i => $cat) {
				echo "<option value=\"$i\">{$wf->Msg($cat['msg'])}</option>";
			} ?>
			</select>
		</div>
		
		<div class="comment-group">
			<label for="comment"><?= wfMsg('wikifeatures-feedback-comment-label') ?>:</label>
			<textarea name="comment"></textarea>
			<span class="comment-character-count">0</span>/1000
		</div>
		
		<input type="submit" value="Submit">
		<span class="status-msg"></span>
	</form>

	
</div>