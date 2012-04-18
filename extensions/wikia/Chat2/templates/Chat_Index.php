<!doctype html>
<html lang="en">
<head>
	<title><?= $roomName ?>: <?= $roomTopic ?></title>
	<link rel="shortcut icon" href="<?= $wgFavicon ?>">

	<!-- Make IE recognize HTML5 tags. -->
	<!--[if IE]>
		<script>/*@cc_on'abbr article aside audio canvas details figcaption figure footer header hgroup mark menu meter nav output progress section summary time video'.replace(/\w+/g,function(n){document.createElement(n)})@*/</script>
	<![endif]-->

	<!-- CSS -->
	<link rel="stylesheet" href="<?= AssetsManager::getInstance()->getSassCommonURL('/extensions/wikia/Chat2/css/Chat.scss')?>">

	<!-- JS -->
	<?= $globalVariablesScript ?>
	<?php //TODO: use js var?>

</head>
<body class="<?= $bodyClasses ?>">

	<header id="ChatHeader" class="ChatHeader">
		<h1 class="public wordmark">
			<a href="<?= $mainPageURL ?>">
			<? if ($themeSettings['wordmark-type'] == 'graphic') { ?>
			<img width="<?= ChatModule::CHAT_WORDMARK_WIDTH ?>" height="<?= ChatModule::CHAT_WORDMARK_HEIGHT ?>" src="<?= $wordmarkThumbnailUrl ?>">
			<? } else { ?>
			<span class="font-<?= $themeSettings['wordmark-font']?>"><?= $themeSettings['wordmark-text'] ?></span>
			<? } ?>
			</a>
		</h1>
		<h1 class="private"></h1>
		<div class="User"></div>
	</header>

	<section id="WikiaPage" class="WikiaPage">

		<div id="Rail" class="Rail">
			<h1 class="public wordmark selected">
				<img src="<?= $wgBlankImgUrl ?>" class="chevron">
				<? if ($themeSettings['wordmark-type'] == 'graphic') { ?>
				<img width="<?= ChatModule::CHAT_WORDMARK_WIDTH ?>" height="<?= ChatModule::CHAT_WORDMARK_HEIGHT ?>" src="<?= $wordmarkThumbnailUrl ?>" class="wordmark">
				<? } else { ?>
				<span class="font-<?= $themeSettings['wordmark-font']?>"><?= $themeSettings['wordmark-text'] ?></span>
				<? } ?>
				<span id="MsgCount_<?php echo $roomId ?>" class="splotch">0</span>
			</h1>
			<ul id="WikiChatList" class="WikiChatList"></ul>
			<h1 class="private"><?= wfMsg('chat-private-messages') ?></h1>
			<ul id="PrivateChatList" class="PrivateChatList"></ul>
		</div>

		<form id="Write" class="Write" onsubmit="return false">
			<img width="<?= ChatModule::CHAT_AVATAR_DIMENSION ?>" height="<?= ChatModule::CHAT_AVATAR_DIMENSION ?>" src="<?= $avatarUrl ?>">
			<textarea name="message"></textarea>
			<input type="submit">
		</form>

	</section>

	<div id="UserStatsMenu" class="UserStatsMenu"></div>

	<!-- HTML Templates -->
	<script type='text/template' id='message-template'>
		<img width="<?= ChatAjax::CHAT_AVATAR_DIMENSION ?>" height="<?= ChatAjax::CHAT_AVATAR_DIMENSION ?>" class="avatar" src="<%= avatarSrc %>"/>
		<span class="time"><%= timeStamp %></span>
		<span class="username"><%= name %></span>
		<span class="message"><%= text %></span>
	</script>
	<script type='text/template' id='inline-alert-template'>
		<%= text %>
	</script>
	<script type='text/template' id='user-template'>
		<img src="<%= avatarSrc %>"/>
		<span class="username"><%= name %></span>
		<div class="details">
			<span class="status">Away</span>
		</div>
		<% if(isPrivate) { %>
			<span id="MsgCount_<%= roomId %>" class="splotch">0</span>
		<% } %>
		<div class="UserStatsMenu">
			<div class="info">
				<img src="<%= avatarSrc %>"/>
				<ul>
					<li class="username"><%= name %></li>
					<li class="edits"><?= wfMsg('chat-edit-count', "<%= editCount %>") ?></li>
					<% if (since) { %>
						<li class="since"><?= wfMsg('chat-member-since', "<%= since %>") ?></li>
					<% } %>
				</ul>
			</div>
			<div class="actions"></div>
		</div>
	</script>
	<script type='text/template' id='user-action-template'>
		<li class="<%= actionName %>">
			<a href="<%= actionUrl %>">
				<span class="icon">&nbsp;</span>
				<span class="label"><%= actionDesc %></span>
			</a>
		</li>
	</script>
	<script type='text/template' id='user-action-template-no-url'>
		<li class="<%= actionName %>">
			<span class="icon">&nbsp;</span>
			<span class="label"><%= actionDesc %></span>
		</li>
	</script>
	<!-- Load these after the DOM is built -->
	<?php
		$srcs = F::build('AssetsManager',array(),'getInstance')->getGroupCommonURL('chat_js2', array());
	?>
	<?php foreach($srcs as $src): ?>
		<script src="<?php echo $src ?>"></script>
	<?php endforeach;?>
	<script src="<?php echo $jsMessagePackagesUrl ?>"></script>
</body>
</html>