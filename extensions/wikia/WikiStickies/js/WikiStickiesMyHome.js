/**
 * @ingroup Wikia
 * @file WikiStickiesMyHome.js
 * @package WikiStickies
 * @see WikiStickies.js
 *
 * Extra JavaScript for WikiStickies on the Special:MyHome pages.
 *
 * JavaScript specific to the WikiStickies functionality on the Special:MyHome
 * page goes here. This file assumes the presence of the WikiStickies.js file.
 *
 * @TODO: Look into how we can use more functionality from the base JS file for
 *        any of the features here.
 */

// TODO: fetch from api a data set, yeah
WIKIA.WikiStickies.stickies = [
	'Can you add to the page about<br /><a href="#">Axl Rose</a>?',
	'Can you add to the page about<br /><a href="#">Some really freaking long, crazy article name</a>?',
	'Can you upload a photo to the page about<br /><a href="#">Kermit the Frog</a>?',
	'Can you start an article about<br /><a href="#">The Chaosmaker</a>?'
];
WIKIA.WikiStickies.count = 0;

WIKIA.WikiStickies.flipWikisticky = function (e) {
	e.preventDefault();
	$(".wikisticky_content p").fadeOut("fast", WIKIA.WikiStickies.updateSticky);
	$(".wikisticky_content h2").fadeOut("fast");
	$(".wikisticky_next").hide();
	$(".wikisticky_curl").animate({
		width: "900px"
	}, function() {
		$(".wikisticky_content p, .wikisticky_next, .wikisticky_content h2").fadeIn("slow");
		$(this).css({
			bottom: "-80px",
			right: "-80px",
			width: "80px"
		}).animate({
			bottom: "-8px",
			right: "-8px"
		});
	});
}
WIKIA.WikiStickies.updateSticky = function () {
	//set content
	$(".wikisticky_content p").html(WIKIA.WikiStickies.stickies[WIKIA.WikiStickies.count]);
	WIKIA.WikiStickies.count++;
	if (WIKIA.WikiStickies.count == WIKIA.WikiStickies.stickies.length) {
		WIKIA.WikiStickies.count = 0;
	}
	//set font size and position
	var paragraph = $(".wikisticky_content p");
	paragraph.css("fontSize", "14pt");
	var verticalDifference = stickyContentHeight - paragraph.height();	
	while (verticalDifference < 0) {
		paragraph.css("fontSize", parseInt( paragraph.css("fontSize") ) - 1);
		verticalDifference = stickyContentHeight - paragraph.height();
	}
	paragraph.css("top", verticalDifference / 2);
}	

$(document).ready(function() {
	$(".wikisticky_curl, .wikisticky_next").bind("click", WIKIA.WikiStickies.flipWikisticky);

	stickyContentHeight = $(".wikisticky_content").height() - $(".wikisticky_content h2").height() - $(".wikisticky_next").outerHeight();
	WIKIA.WikiStickies.updateSticky();
});
