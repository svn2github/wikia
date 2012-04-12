/**
 * JS script performing lint check over JS files. It should be run using nodejs.
 *
 * Usage:
 *   nodejs run-jslint.js --jslint=<absolute path to jslint.js> --file=<file to check>
 *
 * @author Maciej Brencz (Macbre) <macbre at wikia-inc.com>
 */

// require generic functions
var print = require("sys").print,
	parseArgs = require('./utils').parseArgs,
	formatKnownGlobalsComment = require('./utils').formatKnownGlobalsComment;

// options for JSLint
// @see http://www.jslint.com/lint.html
var OPTIONS = {
	// assume JS is to be run in the browser
	browser: true,
	// tolerate type confusion
	confusion: true,
	// tolerate == and != operators
	eqeq: true,
	// tolerate unfiltered for in
	forin: true,
	// perform no white space indentation check
	indent: 0,
	// max. number of errors to report
	maxerr: 750,
	// tolerate uncapitalized constructors
	newcap: true,
	// tolerate dangling _ in identifiers
	nomen: true,
	// tolerate ++ and -- operators
	plusplus: true,
	// tolerate . and [^...]. in /RegExp/
	regexp: true,
	// tolerate missing 'use strict' pragma
	sloppy: true,
	// tolerate many var statements per function
	vars: true,
	// tolerate messy white space
	white: true
};

// parse command line options
var args = parseArgs(process.argv);

// check arguments
if (!args.jslint || !args.file) {
	print("You need to provide a path to jslint and file name to lint\n");
	process.exit(1);
}

// load jslint
var jslint = require(args.jslint).JSLINT;

// check the existance of jslint
if (typeof jslint == 'undefined') {
	print("Unable to import jslint\n");
	process.exit(1);
}

// read the file's content
var fileSrc;

try {
	fileSrc = require("fs").readFileSync(args.file, "utf8");
}
catch(ex) {
	print(ex.message);
	process.exit(1);
}

// check usage of globals
var globalsComment = (args.knownGlobals ? formatKnownGlobalsComment(args.knownGlobals.split(',')) : '');

 // add extra directives for jslint
fileSrc = globalsComment + fileSrc;

// lint it
jslint(fileSrc, OPTIONS);

// prepare output object
var result = {
	errors: jslint.errors,
	tool: "JSLint edition " + jslint.edition  + ' (nodejs ' + process.version + ')'
};

// additional checks
var regExpRules = [
	// detect hardcoded stuff (BugId:12757)
	{
		name: 'Found hardcoded value',
		regexp: /['"]([^"']+blank.gif|\/skins\/|\/extensions\/|\/wiki\/)/,
		dontMatch: /.scss/, // ignore URLs to SASS files as they contain "hardcoded" /extensions and /skins paths
		reason: function(matches) {
			return 'Found hardcoded value: "' + matches[1] + '"';
		}
	},
	// detect $.live (BugId:28034)
	{
		name: 'Found $.live',
		regexp: /.live\(\s?['"]/,
		reason: 'jQuery.live() is deprecated'
	},
	// detect $.css (BugId:28035)
	{
		name: 'Found $.css',
		regexp: /.css\(\s?['"]/,
		reason: 'jQuery.css() should not be used (use CSS classes instead)'
	},
	// detect $.browser (BugId:28056)
	{
		name: 'Found $.browser',
		regexp: /(\$|jQuery).browser./,
		reason: 'jQuery.browser should not be used (use feature detection instead)'
	},
	// detect setTimeout / setInterval with implied eval
	{
		name: 'Implied eval',
		regexp: /set(Timeout|Interval)\(\s?["']/,
		reason: function(matches) {
			return 'Don\'t pass a string to set' + matches[1] + ' (implied eval)';
		}
	}
];

// scan each line
var lines = fileSrc.split("\n"),
	matches;

for(var n=0, len = lines.length; n < len; n++) {

	regExpRules.forEach(function(rule) {
		matches = lines[n].match(rule.regexp);

		if (matches) {
			// omit lines that match 'dontMatch' rule field
			if (rule.dontMatch && rule.dontMatch.test(lines[n])) {
				return;
			}

			// add an issue to the list
			result.errors.push({
				id: '(error)',
				raw: rule.name,
				evidence: lines[n],
				line: n + 1,
				character: lines[n].indexOf(matches[1]) + 1,
				reason: (typeof rule.reason === 'function') ? rule.reason.call(this, matches) : rule.reason
			});
		}
	});
}

// return JSON-encoded result
print(JSON.stringify(result));