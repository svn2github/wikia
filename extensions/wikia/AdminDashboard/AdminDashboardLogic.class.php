<?php

/**
 * Helper functions for Admin Dashboard
 */

class AdminDashboardLogic {
	
	public static function isGeneralApp($appName) {
		$generalApps = array(
			'Categories' => true,
			'CreateBlogPage' => true,
			'CreatePage' => true,
			'Listusers' => true,
			'ListUsers' => true,
			'MultipleUpload' => true,
			'PageLayoutBuilder' => true,
			'Recentchanges' => true,
			'RecentChanges' => true,
			'ThemeDesigner' => true,
			'Upload' => true,
			'UserRights' => true,
			'Userrights' => true,
			'WikiaLabs' => true,
		);
		return !empty($generalApps[$appName]);
	}

	/**
	 * @brief Helper function which determines whether to display the Admin Dashboard Chrome in the Oasis Skin
	 * @param type $title Title of page we are on
	 * @return type boolean 
	 */
	public static function displayAdminDashboard($app, $title) {
		// Admin Dashboard is only for logged in plus a list of groups
		if (!$app->wg->User->isLoggedIn()) return false;
		if (!$app->wg->User->isAllowed( 'admindashboard' )) return false;
		if ($title && $title->isSpecialPage()) {
			$exclusionList = array(
				"Connect", 
				"Contact",
				"Contributions",
				"CreateBlogPage",
				"CreatePage",
				"CreateNewWiki",
				"CreateTopList",
				"CloseWiki",
				"Following",
				"EditAccount",
				"EditTopList",
				"HuluVideoPanel",
				"LandingPageSmurfs",
				"LayoutBuilder",
				"LayoutBuilderForm",
				"Leaderboard",
				"LookupContribs",
				"LookupUser",
				"MovePage",
				"MultiLookup",
				"NewFiles",
				"Our404Handler",
				"PageLayoutBuilder",
				"Phalanx",
				"PhalanxStats",
				"Preferences",
				"ScavengerHunt",
				"Search",
				"Signup",
				"SiteWideMessages",
				"TaskManager",
				"ThemeDesigner",
				"ThemeDesignerPreview",
				"UserLogin",
				"UserPathPrediction",
				"Version",
				"WhereIsExtension",
				"WikiActivity",
				"WikiFactory",
				"WikiFactoryReporter",
				"WikiaLabs",
				"WikiStats",
			);
			return (!in_array($title->getDBKey(), $exclusionList));
		}
		return false;
	}
	
}
