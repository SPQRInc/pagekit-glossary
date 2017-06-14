<?php $view->script( 'glossary-details', 'glossary:app/bundle/glossary-details.js', 'vue' ); ?>
<div id="glossary-details">
	<div class="uk-grid" data-uk-grid-margin>
		<div class="uk-width-1-1">
			<<?=$heading_size ?> <?= $heading_class ?>><?= $item->title ?></<?=$heading_size?> >
		<div class="uk-flex uk-flex-wrap uk-margin" data-uk-margin="">
			<?php foreach($item->marker as $tag) : ?>
				<div class="uk-badge uk-margin-small-right"><?= $tag ?></div>
			<?php endforeach; ?>
		</div>
		<p><?= $item->content ?></p>
	</div>
</div>
</div>