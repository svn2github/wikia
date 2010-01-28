<?php

/**
 * @package MediaWiki
 * @ingroup WikiFactory
 * @author Krzysztof Krzyżaniak (eloy) <eloy@wikia.com> for Wikia Inc.
 * @copyright (C) 2010, Wikia Inc.
 * @licence GNU General Public Licence 2.0 or later
 */

# use tables
#
#CREATE TABLE `city_tag` (
#  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
#  `name` varchar(255) DEFAULT NULL,
#  PRIMARY KEY (`id`),
#  UNIQUE KEY `city_tag_name_uniq` (`name`)
#) ENGINE=InnoDB DEFAULT;
#
#
#CREATE TABLE `city_tag_map` (
#  `city_id` int(9) NOT NULL,
#  `tag_id` int(8) unsigned NOT NULL,
#  PRIMARY KEY (`city_id`,`tag_id`),
#  KEY `tag_id` (`tag_id`),
#  CONSTRAINT FOREIGN KEY (`city_id`) REFERENCES `city_list` (`city_id`) ON DELETE CASCADE ON UPDATE CASCADE,
#  CONSTRAINT FOREIGN KEY (`tag_id`) REFERENCES `city_tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
#) ENGINE=InnoDB
#
# http://www.pui.ch/phred/archives/2005/04/tags-database-schemas.html

class WikiFactoryTags {

	private $mCityId;

	/**
	 * public constructor
	 *
	 * @access public
	 * @param integer $city_id	city_id value from city_list table
	 */
	public function __construct( $city_id ) {
		$this->mCityId = $city_id;
	}

	/**
	 *
	 * @access private
	 *
	 * @return string  key used for caching
	 */
	private function cacheKey() {
		return sprintf( "wikifactory:tags:v1:%d", $this->mCityId );
	}


	/**
	 * getTags -- get all tags defined from database
	 *
	 * @access public
	 */
	public function getTags( $skipcache = false ) {

		global $wgMemc;

		wfProfileIn( __METHOD__ );

		$result = array();

		/**
		 * try cache first
		 */
		if( !$skipcache ) {
			/**
			 * check if cache has any values stored
			 */
			$result = $wgMemc->get( $this->cacheKey() );
			$usedb  = is_array( $result ) ? false : true;
		}
		else {
			$usedb = true;
		}

		if( $usedb ) {
			$dbr = WikiFactory::db( DB_SLAVE );
			$sth = $dbr->select(
				array( "city_tag", "city_tag_map" ),
				array( "tag_id", "name" ),
				array( "tag_id = id", "city_id" => $this->mCityId ),
				__METHOD__
			);
			while( $row = $dbr->fetchObject( $sth ) ) {
				$result[ $row->tag_id ] = $row->name;
			}

			/**
			 * set in cache for future use
			 */
			$wgMemc->set( $this->cacheKey(), $result, 60*60*24 );
		}

		wfProfileOut( __METHOD__ );

		return $result;
	}

	/**
	 * use provided string to add new tags into database. Tags will be:
	 *
	 * 1) splitted by space
	 * 2) lowercased
	 * 3) added to city_tag table if they are not exist already
	 * 4) added to city_tag_map
	 *
	 * @access public
	 *
	 * @param string $stag string with tag definition
	 *
	 * @return Array current tags for wiki
	 */
	public function setTagsByName( $stag ) {

		wfProfileIn( __METHOD__ );

		$tags = explode( " ", trim( str_lowercase( $stag ) ) );
		$dbw  = WikiFactory::db( DB_MASTER );

		/**
		 * check if all tags are already defined in database,
		 *
		 * if not - add them and get id value
		 * if yes - just get id value
		 *
		 */
		$ids = array();
		foreach( $tags as $tag ) {
			$row = $dbw->selectRow(
				array( "city_tag" ),
				array( "*" ),
				array( "name" => $tag ),
				__METHOD__
			);

			if( !empty( $row->id ) ) {
				$ids[] = $row->id;
			}
			else {
				/**
				 * add new tag to database
				 */
				$dbw->insert(
					"city_tag",
					array( "name"=> $tag ),
					__METHOD__
				);
				$ids[] = $dbw->insertId();
			}
		}

		wfProfileOut( __METHOD__ );

		/**
		 * add tags by id, refresh cache, return defined tags
		 */
		return $this->setTagsById( $ids );
	}

	/**
	 * use provided array to add new tags into database.
	 *
	 * @param Array $ids
	 *
	 * @return Array current tags for wiki
	 */
	public function setTagsById( $ids ) {

		/**
		 * and now map tags in city_tag_map
		 */
		wfProfileIn( __METHOD__ );
		if( is_array( $ids ) ) {
			foreach( $ids as $id ) {
				$dbw->replace(
					"city_tag",
					array( "city_id", "tag_id" ),
					array( "city_id" => $this->mCityId, "tag_id" => $id ),
					__METHOD__
				);
			}
		}
		wfProfileOut( __METHOD__ );

		/**
		 * refresh cache, return defined tags
		 */
		return $this->getTags( true );
	}

	/**
	 * use provided string to remove tags from database. Tags will be:
	 *
	 * 1) splitted by space
	 * 2) lowercased
	 * 3) removed from city_tag_map
	 * 4) leaved in city_tag
	 *
	 * @access public
	 *
	 * @param string $stag string with tag definition
	 *
	 * @return Array current tags for wiki
	 */
	public function removeTagsByName( $stag ) {

		wfProfileIn( __METHOD__ );

		$tags = explode( " ", trim( str_lowercase( $stag ) ) );
		$dbw  = WikiFactory::db( DB_MASTER );

		$ids = array();
		foreach( $tags as $tag ) {
			$row = $dbw->selectRow(
				array( "city_tag" ),
				array( "*" ),
				array( "name" => $tag ),
				__METHOD__
			);

			if( !empty( $row->id ) ) {
				$ids[] = $row->id;
			}
		}

		wfProfileOut( __METHOD__ );
		return $this->removeTagsById( $ids );
	}

	/**
	 * use provided array to remove tags from database.
	 *
	 * @access public
	 * @param Array $ids
	 *
	 * @return Array current tags for wiki
	 */
	public function removeTagsById( $ids ) {

		wfProfileIn( __METHOD__ );

		if( is_array( $ids ) ) {
			foreach( $ids as $id ) {
				$dbw->delete(
					"city_tag",
					array( "city_id" => $this->mCityId, "tag_id" => $id ),
					__METHOD__
				);
			}
		}

		wfProfileOut( __METHOD__ );

		/**
		 * refresh cache, return defined tags
		 */
		return $this->getTags( true );
	}
}
