<?php

class UserProfilePage {
	/**
	 * @var User
	 */
	private $user;
	private $hiddenPages = null;
	private $templateEngine = null;

	public function __construct( User $user ) {
		global $wgUser;

		$this->user = $user;
		$this->templateEngine = new EasyTemplate( dirname(__FILE__) . "/templates/" );

		// set "global" template variables
		$this->templateEngine->set( 'isOwner', ( $this->user->getId() == $wgUser->getId() ) ? true : false );
	}

	public function get( $pageBody ) {
		global $wgOut, $wgSitename, $wgJsMimeType, $wgExtensionsPath, $wgStyleVersion;

		//$wgOut->addScript( "<script type=\"{$wgJsMimeType}\" src=\"{$wgExtensionsPath}/wikia/UserProfilePage/js/UserProfilePage.js?{$wgStyleVersion}\" ></script>\n" );

		$userContribsProvider = new UserContribsProviderService;

		$this->templateEngine->set_vars(
			array(
				'wikiName'         => $wgSitename,
				'userName'         => $this->user->getName(),
				'userPageUrl'      => $this->user->getUserPage()->getLocalUrl(),
				'activityFeedBody' => $this->renderUserActivityFeed( $userContribsProvider->get( 6, $this->user ) ),
				'wikiSwitch'       => $this->populateWikiSwitchVars(),
				'topPagesBody'     => $this->renderTopPages( $this->getTopPages() ),
				'aboutSection'     => $this->populateAboutSectionVars(),
				'pageBody'         => $pageBody,
			));
		return $this->templateEngine->render( 'user-profile-page' );
	}

	/**
	 * render user's activity feed section
	 * @param array $data
	 * @return string
	 */
	private function renderUserActivityFeed( Array $data ) {
		global $wgBlankImgUrl;
		wfProfileIn(__METHOD__);

		$this->templateEngine->set_vars(
			array(
				'activityFeed' => $data,
				'assets' => array( 'blank' => $wgBlankImgUrl )
			)
		);

		wfProfileOut(__METHOD__);
		return $this->templateEngine->render( 'user-contributions' );
	}

	/**
	 * render user's top pages section
	 * @param array $data
	 * @return string
	 */
	private function renderTopPages( Array $data ) {
		wfProfileIn(__METHOD__);

		$this->templateEngine->set_vars(
			array(
				'topPages' => $data,
			)
		);

		wfProfileOut(__METHOD__);
		return $this->templateEngine->render( 'user-top-pages' );
	}

	private function populateAboutSectionVars() {
		global $wgOut;
		$sTitle = $this->user->getUserPage()->getText() . '/' . wfMsg( 'userprofilepage-about-article-title' );
		$oTitle = Title::newFromText( $sTitle, NS_USER );
		$oArticle = new Article($oTitle, 0);

		$oSpecialPageTitle = Title::newFromText( 'CreateFromTemplate', NS_SPECIAL );

		if( $oTitle->exists() ) {
			$sArticleBody = $wgOut->parse( $oArticle->getContent() );
			$sArticleEditUrl = $oTitle->getLocalURL( 'action=edit' );
		}
		else {
			$sArticleBody = wfMsg( 'userprofilepage-about-empty-section' );
			$sArticleEditUrl = $oSpecialPageTitle->getLocalURL( 'type=aboutuser&wpTitle=' . $oTitle->getPrefixedURL() . '&returnto=' . $this->user->getUserPage()->getFullUrl( 'action=purge' ) );
		}

		return array( 'body' => $sArticleBody, 'articleEditUrl' => $sArticleEditUrl );
	}

	private function populateWikiSwitchVars() {
		return array( 'topWikis' => $this->getTopWikis() );
	}

	/**
	 * get list of user's top pages (most edited)
	 *
	 * @author ADi
	 * @return array
	 */
	public function getTopPages() {
		global $wgMemc, $wgStatsDB, $wgCityId, $wgContentNamespaces;
		wfProfileIn(__METHOD__);

		//select page_id, count(page_id) from stats.events where wiki_id = N and user_id = N and event_type in (1,2) group by 1 order by 2 desc limit 10;
		$dbs = wfGetDB( DB_SLAVE, array(), $wgStatsDB );
		$res = $dbs->select(
			array( 'stats.events' ),
			array( 'page_id', 'count(page_id) AS count' ),
			array(
				'wiki_id' => $wgCityId,
				'user_id' => $this->user->getId() ),
				'event_type IN (1,2)',
				'page_ns IN (' . join( ',', $wgContentNamespaces ) . ')',
			__METHOD__,
			array(
				'GROUP BY' => 'page_id',
				'ORDER BY' => 'count DESC',
				'LIMIT' => 6
			)
		);

		/* revision
		$dbs = wfGetDB( DB_SLAVE );
		$res = $dbs->select(
			array( 'revision' ),
			array( 'rev_page', 'count(*) AS count' ),
			array( 'rev_user' => $this->user->getId() ),
			__METHOD__,
			array(
				'GROUP BY' => 'rev_page',
				'ORDER BY' => 'count DESC',
				'LIMIT' => 6
			)
		);
		*/

		// TMP: dev-box only
		$pages = array( 4 => 289, 1883 => 164, 1122 => 140, 31374 => 112, 2335 => 83, 78622 => 82 ); // test data
		foreach($pages as $pageId => $editCount) {
			$title = Title::newFromID( $pageId );
			if( ( $title instanceof Title ) && ( $title->getArticleID() != 0 ) && ( !$this->isPageHidden( $title->getText() ) ) ) {
				$pages[ $pageId ] = array( 'id' => $pageId, 'url' => $title->getFullUrl(), 'title' => $title->getText(), 'imgUrl' => null, 'editCount' => $editCount );
			}
			else {
				unset( $pages[ $pageId ] );
			}
		}

		/*
		$pages = array();
		while($row = $dbs->fetchObject($res)) {
			$pageId = $row->page_id;
			$title = Title::newFromID( $pageId );
			if( ( $title instanceof Title ) && ( $title->getArticleID() != 0 ) && ( !$this->isPageHidden( $title->getText() ) ) ) {
				$pages[ $pageId ] = array( 'id' => $pageId, 'url' => $title->getFullUrl(), 'title' => $title->getText(), 'imgUrl' => null, 'editCount' => $row->count );
			}
			else {
				unset( $pages[ $pageId ] );
			}
		}
		*/


		if( class_exists('imageServing') ) {
			// ImageServing extension enabled, get images
			$imageServing = new imageServing( array_keys( $pages ), 100, array( 'w' => 1, 'h' => 1 ) );
			$images = $imageServing->getImages(1); // get just one image per article

			foreach( $pages as $pageId => $data ) {
				if( isset( $images[$pageId] ) ) {
					$image = $images[$pageId][0];
					$data['imgUrl'] = $image['url'];
				}
				$pages[ $pageId ] = $data;
			}
		}

		wfProfileOut(__METHOD__);
		return $pages;
	}

	public function getTopWikis() {
		global $wgExternalDatawareDB;

		// alternate query:
		// SELECT city_id, sum(edit_count) from user_edits_summary where user_id='259228' group by 1 order by 2 desc limit 10;

		// SELECT lu_wikia_id, lu_rev_cnt FROM city_local_users WHERE lu_user_id=$userId ORDER BY lu_rev_cnt DESC LIMIT $limit;
		$dbs = wfGetDB(DB_SLAVE, array(), $wgExternalDatawareDB);
		$res = $dbs->select(
			array( 'city_local_users' ),
			array( 'lu_wikia_id', 'lu_rev_cnt' ),
			array( 'lu_user_id' => $this->user->getId() ),
			__METHOD__,
			array(
				'ORDER BY' => 'lu_rev_cnt DESC',
				'LIMIT' => 4
			)
		);

		$wikis = array();
		while($row = $dbs->fetchObject($res)) {
			$wikiId = $row->lu_wikia_id;
			$editCount = $row->lu_rev_cnt;
			$wikiName = WikiFactory::getVarValueByName( 'wgSitename', $wikiId );
			$wikiUrl = WikiFactory::getVarValueByName( 'wgServer', $wikiId );
			$wikiLogo = WikiFactory::getVarValueByName( "wgLogo", $wikiId );
			$wikis[$wikiId] = array( 'wikiName' => $wikiName, 'wikiUrl' => $wikiUrl, 'wikiLogo' => $wikiLogo, 'editCount' => $editCount );
		}

		// TMP: local only
		$wikis = array( 4832 => 72, 3613 => 60, 4036 => 35, 177 => 72 ); // test data
		foreach($wikis as $wikiId => $editCount) {
			$wikiName = WikiFactory::getVarValueByName( 'wgSitename', $wikiId );
			$wikiUrl = WikiFactory::getVarValueByName( 'wgServer', $wikiId );
			$wikiLogo = WikiFactory::getVarValueByName( "wgLogo", $wikiId );
			$wikis[$wikiId] = array( 'wikiName' => $wikiName, 'wikiUrl' => $wikiUrl, 'wikiLogo' => $wikiLogo, 'editCount' => $editCount );
		}
		//

		return $wikis;
	}

	/**
	 * perform action (hide/unhide page or wiki)
	 *
	 * @author ADi
	 * @param string $actionName
	 * @param string $type
	 * @param string $value
	 */
	public function doAction( $actionName, $type, $value) {
		wfProfileIn( __METHOD__ );
		$methodName = strtolower( $actionName ) . ucfirst( $type );

		if( method_exists( $this, $methodName ) ) {
			return call_user_func_array( array( $this, $methodName ), array( $value ) );
		}
		wfProfileOut( __METHOD__ );
	}

	private function hidePage( $pageTitleText ) {
		wfProfileIn( __METHOD__ );
		if( !$this->isPageHidden( $pageTitleText ) ) {
			$this->hiddenPages[] = $pageTitleText;
			$this->updateHiddenPagesInDb();
		}
		return $this->renderTopPages( $this->getTopPages() );
		wfProfileOut( __METHOD__ );
	}

	private function unhidePage( $pageTitleText ) {
		wfProfileIn( __METHOD__ );
		if( $this->isPageHidden( $pageTitleText ) ) {
			for( $i = 0; $i < count( $this->hiddenPages ); $i++ ) {
				if( $this->hiddenPages[ $i ] == $pageTitleText ) {
					unset( $this->hiddenPages[ $i ] );
					$this->hiddenPages = array_values( $this->hiddenPages );
				}
			}
			//unset( $this->hiddenPages[ $pageTitleText ] );
			$this->updateHiddenPagesInDb();
		}
		return $this->renderTopPages( $this->getTopPages() );
		wfProfileOut( __METHOD__ );
	}

	/**
	 * auxiliary function for updating hidden pages in db
	 */
	private function updateHiddenPagesInDb() {
		wfProfileIn( __METHOD__ );

		$dbw = wfGetDB( DB_MASTER );
		$dbw->replace(
				'page_wikia_props',
				null,
				array( 'page_id' => $this->user->getId(), 'propname' => 10, 'props' => serialize( $this->hiddenPages ) ),
				__METHOD__
			);
		$dbw->commit();

		wfProfileOut( __METHOD__ );
	}

	public function isPageHidden( $pageTitleText ) {
		return ( in_array( $pageTitleText, $this->getHiddenPages() ) ? true : false );
	}

	private function hideWiki( $wikiName ) {
		return true;
	}

	private function unhideWiki( $wikiName ) {
		return true;
	}

	private function getHiddenPages() {
		if( $this->hiddenPages == null ) {
			$dbs = wfGetDB( DB_MASTER );

			$row = $dbs->selectRow(
				array( 'page_wikia_props' ),
				array( 'props' ),
				array( 'page_id' => $this->user->getId(), 'propname' => 10 ),
				__METHOD__,
				array()
			);

			$this->hiddenPages = ( empty($row) ? array() : unserialize( $row->props ) );
		}
		return $this->hiddenPages;
	}

}
