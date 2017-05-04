<?php $view->script( 'glossary-index', 'glossary:app/bundle/glossary-index.js', 'vue' ); ?>
<div id="glossary-index">
	<ul class="uk-subnav <?= $config['subnav_style'] ?>">
		<li v-for="letter in alphabet" :class="{ 'uk-active' : letter == selectedLetter}"><a @click="selectedLetter = letter">{{ letter }}</a></li>
		<li :class="{ 'uk-active' : selectedLetter == undefined}"><a @click="selectedLetter = undefined">{{ 'ALL' | trans }}</a></li>
	</ul>
	<dl class="uk-description-list-line" v-for="item in filteredItems">
		<dt><a class="uk-text-large" :href="$url.route(item.url)">{{ item.title }}</a></dt>
		<dd v-if="item.excerpt.length"><div v-html="item.excerpt"></div></dd>
		<dd v-else><div v-html="item.content | truncate 150"></div></dd>
	</dl>
	<h3 class="uk-h1 uk-text-muted uk-text-center"
	    v-show="!filteredItems">{{ 'No Items found.' | trans }}</h3>
</div>