<?php
/**
 * Internationalisation file for Special:PhotoPop extension / game.
 *
 * @addtogroup Extensions
 */

$messages = array();

$messages['en'] = array(
	'photopop' => 'Foggy Foto game',
	'photopop-desc' => 'Creates a page where the Foggy Foto game can be played in HTML5 + Canvas. It will be accessible via Nirvana\'s APIs',
	'photopop-score' => 'Score: <span>$1</span>',
	'photopop-progress' => 'Photos: <span>$1</span>',
	'photopop-progress-numbers' => '$1/$2',
	'photopop-continue-correct' => 'CORRECT!',
	'photopop-continue-timeup' => 'TIME IS UP!',
);

/** Message documentation (Message documentation) */
$messages['qqq'] = array(
	'photopop' => 'Special page name for "Foggy Foto" game.',
	'photopop-desc' => '{{desc}}',
	'photopop-progress' => 'Parameters:
* $1 is replaced with {{msg-wikia|photopop-progress-numbers}}.',
	'photopop-progress-numbers' => 'This is the format of the numbers that will be substituted into the "$1" portion of {{msg-wikia|photopop-progress}}. Parameters:
* $1 is what number photo the player is on (starting with 1)
* $2 is the total number of photos in a round of the game.',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 */
$messages['mk'] = array(
	'photopop' => 'Матна слика',
	'photopop-desc' => 'Создава страница кајшто се игра играта „Матна слика“ (Foggy Foto) во HTML5 + Canvas. Ќе биде достапна преку прилозите (API) на Nirvana',
	'photopop-score' => 'Бодови: <span>$1</span>',
	'photopop-progress' => 'Слики: <span>$1</span>',
	'photopop-progress-numbers' => '$1/$2',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'photopop' => 'Foggy Fotospel',
	'photopop-desc' => "Maakt een pagina aan het Foggy Fotospel gespeeld kan worden in HTML5 met Canvas. Dit is beschikbaar via Nirvana's API's",
	'photopop-score' => 'Score: <span>$1</span>',
	'photopop-progress' => "Foto's: <span>$1</span>",
	'photopop-progress-numbers' => '$1/$2',
);

