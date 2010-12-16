<?php
/**
 * @author Tomasz Klim <tomek@wikia.com>
 * @author Maciej Brencz
 * */
if(!defined('MEDIAWIKI')) {
	die(1);
}

global $wgWidgets;
$wgWidgets['WidgetTopUsers'] = array(
	'callback' => 'WidgetTopUsers',
	'title' => 'widget-title-topusers',
	'desc' => 'widget-desc-topusers',
    'closeable' => true,
    'editable' => false,
    'listable' => true
);

function WidgetTopUsers($id, $params) {

    wfProfileIn(__METHOD__);

    $links = array();    

    if ( class_exists( 'DataProvider' ) ) {
        $articles =& DataProvider::singleton()->GetTopFiveUsers();

        if ( is_array( $articles ) && count( $articles ) > 0 ) {
	    foreach ( $articles as $article ) {
	        $links[] = array( 'href' => $article['url'], 'name' => $article['text'] );
	    }
	}
    }

    wfProfileOut( __METHOD__ );
    
    return WidgetFrameworkWrapLinks($links);
}
