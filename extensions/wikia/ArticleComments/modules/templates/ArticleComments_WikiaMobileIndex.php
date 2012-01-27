<section id=wkArtCom data-pages="<?= $pagesCount ;?>">
	<h1 class=collSec><?= $wf->MsgExt( 'wikiamobile-article-comments-header', array('parsemag'), $wg->Lang->formatNum( $countCommentsNested ) ) ;?><span class=chev></span></h1>
	<div id=article-comments>
		<? if (!$isReadOnly) { ?>
		<form method=post class=article-comm-form id=article-comm-form>
			<input type=hidden name=wpArticleId value="<?= $title->getArticleId() ;?>" />
			<textarea type=text placeholder="<?= $wf->Msg('wikiamobile-article-comments-placeholder') ;?>" name=wpArticleComment id=article-comm></textarea>
			<input type=submit name=wpArticleSubmit id=article-comm-submit class=wikia-button value="<?= $wf->Msg('wikiamobile-article-comments-post') ;?>" />
		</form>
		<? } ?>
		<? if ( $countCommentsNested > 0 ) :?>
			<? if ( $pagesCount > 1 ) :?>
				<a id=commPrev class="lbl<?= ( !empty( $prevPage ) ) ? ' pag" href="?page=' . $prevPage . '#article-comments"' : '' ?>"><?= $wf->Msg( 'wikiamobile-article-comments-prev' ) ;?></a>
			<? endif ;?>
			<?= wfRenderPartial( 'ArticleComments', 'CommentList', array( 'commentListRaw' => $commentListRaw, 'page' => $page, 'useMaster' => false ) );?>
			<? if ( $pagesCount > 1 ) :?>
				<a id=commMore class="lbl<?= ( !empty( $nextPage ) ) ? ' pag" href="?page=' . $nextPage . '#article-comments"' : '' ?>"><?= $wf->Msg( 'wikiamobile-article-comments-more' ) ;?></a>
			<? endif ;?>
		<? else :?>
			<?= $wf->Msg( 'wikiamobile-article-comments-none' ) ;?>
		<? endif ;?>
	</div>
</section>
