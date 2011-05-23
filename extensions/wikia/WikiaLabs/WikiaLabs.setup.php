<?php
# Alert the user that this is not a valid entry point to MediaWiki if they try to access the special pages file directly.
if (!defined('MEDIAWIKI')) {
	echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/MyExtension/MyExtension.php" );
EOT;
	exit( 1 );
}

$app = F::app();
$dir = dirname(__FILE__) . '/';

/**
 * classes
 */
$app->registerClass('WikiAPIClient', $dir . 'WikiAPIClient.class.php');
$app->registerClass('WikiaLabsWikisListPager', $dir . 'WikiaLabsWikisListPager.class.php');
$app->registerClass('WikiaLabsModule', $dir . 'WikiaLabsModule.class.php');
$app->registerClass('WikiaLabs', $dir . 'WikiaLabs.class.php');
$app->registerClass('WikiaLabsProject', $dir . 'WikiaLabsProject.class.php');
$app->registerClass('WikiaLabsHelper', $dir . 'WikiaLabsHelper.class.php');

/**
 * hooks
 */
$app->registerHook('GetRailModuleSpecialPageList', 'WikiaLabs', 'onGetRailModuleSpecialPageList' );
$app->registerHook('MyTools::getDefaultTools', 'WikiaLabs', 'onGetDefaultTools' );

/**
 * controllers
 */
$app->registerClass('WikiaLabsSpecialController', $dir . 'WikiaLabsSpecialController.class.php');

/**
 * special pages
 */
$app->registerSpecialPage('WikiaLabs', 'WikiaLabsSpecialController');

/**
* message files
*/
$app->registerExtensionMessageFile('WikiaLabs', $dir . 'WikiaLabs.i18n.php' );

/**
 * alias files
 */
$app->registerExtensionAliasFile('WikiaLabs', $dir . 'WikiaLabs.alias.php');

/**
 * Factory config
 */
F::addClassConstructor( 'WikiaLabs', array( 'app' => $app ) );
F::addClassConstructor( 'WikiaLabsProject', array( 'app' => $app, 'id' => 0 ) );

/*
 * set global
*/
$logTypes = $app->getGlobal('wgLogTypes');
$logTypes[] = 'wikialabs';
$app->setGlobal('wgLogTypes', $logTypes);

$logHeaders = $app->getGlobal('wgLogHeaders');
$logHeaders['wikialabs'] = 'wikialabs';
$app->setGlobal('wgLogHeaders', $logHeaders);

/*
 * ajax function
 */
$wgAjaxExportList[] = 'WikiaLabsHelper::getProjectModal';
$wgAjaxExportList[] = 'WikiaLabsHelper::saveProject';
$wgAjaxExportList[] = 'WikiaLabsHelper::getImageUrlForEdit';
$wgAjaxExportList[] = 'WikiaLabsHelper::switchProject';
$wgAjaxExportList[] = 'WikiaLabsHelper::saveFeedback';