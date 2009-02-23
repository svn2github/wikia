// "add image" toolbar button
var FCKAddVideoCommand = function() {
	this.Name = 'AddVideo' ;

	this.IsEnabled = (typeof window.parent.vet_enabled != 'undefined');
}
FCKAddVideoCommand.prototype = {
	Execute : function() {
		FCKUndo.SaveUndoStep() ;
		FCK.log('opening "add video" dialog');
		window.parent.VET_show(-1);
	},
	GetState : function() {
		if ( (FCK.EditMode != FCK_EDITMODE_WYSIWYG) || (this.IsEnabled == false) )
			return FCK_TRISTATE_DISABLED ;
		return FCK_TRISTATE_OFF;
	}
} ;

// register FCK command
FCKCommands.RegisterCommand('AddVideo', new FCKAddVideoCommand());

// toolbar item icon
var oTildesItem = new FCKToolbarButton( 'AddVideo', 'Add video' ) ;
oTildesItem.IconPath = FCKConfig.PluginsPath + 'video/addVideo.png';

// register toolbar item
FCKToolbarItems.RegisterItem( 'AddVideo', oTildesItem );

FCK.VideoAdd = function(wikitext, options) {
	var refid = FCK.GetFreeRefId();

	FCK.log('adding new video #' + refid + ' using >>' + wikitext + '<<');

	FCK.Track('/video/add');

	// add meta data entry
	FCK.wysiwygData[refid] = {
		'type': 'video',
		'original': wikitext
	};

	// create new placeholder and add it to the article
	placeholder = FCK.EditorDocument.createElement('INPUT');
	placeholder.className = 'wysiwygDisabled';
	placeholder.type = 'button';
	placeholder.value = wikitext;
	placeholder.setAttribute('refid', refid);
	placeholder.setAttribute('_fck_type', 'video');

	FCK.InsertElement(placeholder);
}

// setup <videogallery> hook placeholder
FCK.VideoSetupGalleryPlaceholder = function(placeholder) {
	FCK.log(placeholder);

	// change HTML
	placeholder.value = '<videogallery>';
	placeholder.className = 'wysiwygDisabled wysiwygVideoGallery';
	placeholder.setAttribute('_fck_type', 'videogallery');

	// add onclick handler
	FCKTools.AddEventListener(placeholder, 'click', FCK.VideoGalleryClick);
}

FCK.VideoGalleryClick = function(e) {
	var e = FCK.YE.getEvent(e);
	var target = FCK.YE.getTarget(e);

	FCK.YE.stopEvent(e);

	var refid = parseInt(target.getAttribute('refid'));

	FCK.log('<videogallery> #' + refid + ' click');

	if (typeof window.parent.VET_show != 'undefined') {
		window.parent.VET_show(refid, 1);
	}
}
