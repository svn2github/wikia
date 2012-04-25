(function(exports) {

	var api = require('./api').api;

	var bot = function(options) {
		options = options || {};

		this.server = options.server;

		this.api = new api({
			server: options.server,
			proxy: options.proxy,
			debug: options.debug === true
		});
	};

	// get the object being the first key/value entry of a given object
	var getFirstItem = function(object) {
		for(var key in object);
		return object[key];
	};

	bot.prototype = {
		logIn: function(username, password, callback) {
			var self = this;

			// request a token
			this.api.call({
				action: 'login',
				lgname: username,
				lgpassword: password,
			}, function(data) {
				if (data.result == 'NeedToken') {
					var token = data.token;

					// log in using a token
					self.api.call({
						action: 'login',
						lgname: username,
						lgpassword: password,
						lgtoken: token,
					}, function(data) {
						callback(data);
					}, 'POST');
				}
			}, 'POST');
		},

		getPagesInCategory: function(category, callback) {
			category = 'Category:' + category;

			this.api.call({
				action: 'query',
				list: 'categorymembers',
				cmtitle: category,
				cmlimit: 500
			}, function(data) {
				callback(data && data.categorymembers || []);
			});
		},

		getArticle: function(title, callback) {
			var params = {
				action: 'query',
				prop: 'revisions',
				rvprop: 'content'
			};

			// both page ID or title can be provided
			if (typeof title === 'number') {
				params.pageids = title;
			}
			else {
				params.titles = title;
			}

			this.api.call(params, function(data) {
				var page = getFirstItem(data.pages),
					revision = page.revisions.shift(),
					content = revision['*'];

				callback(content);
			})
		},

		edit: function(title, content, summary, callback) {
			var self = this;

			// get edit token
			this.api.call({
				action: 'query',
				prop: 'info',
				intoken: 'edit',
				titles: title
			}, function(data) {
				var page = getFirstItem(data.pages),
					token = page.edittoken;

				self.api.call({
					action: 'edit',
					title: title,
					text: content,
					summary: summary,
					token: token
				}, function(data) {
					callback(data);
				}, 'POST');
			});
		}
	};

	exports.bot = bot;

})(exports);