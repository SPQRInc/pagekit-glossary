<?php $view->script( 'settings', 'glossary:app/bundle/settings.js', [ 'vue' ] ); ?>

<div id="settings" class="uk-form uk-form-horizontal" v-cloak>
	<div class="uk-grid pk-grid-large" data-uk-grid-margin>
		<div class="pk-width-sidebar">
			<div class="uk-panel">
				<ul class="uk-nav uk-nav-side pk-nav-large" data-uk-tab="{ connect: '#tab-content' }">
					<li><a><i class="pk-icon-large-settings uk-margin-right"></i> {{ 'General' | trans }}</a></li>
				</ul>
			</div>
		</div>
		<div class="pk-width-content">
			<ul id="tab-content" class="uk-switcher uk-margin">
				<li>
					<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
						<div data-uk-margin>
							<h2 class="uk-margin-remove">{{ 'General' | trans }}</h2>
						</div>
						<div data-uk-margin>
							<button class="uk-button uk-button-primary" @click.prevent="save">{{ 'Save' | trans }}
							</button>
						</div>
					</div>
					<div class="uk-form-row">
						<label for="form-tooltip" class="uk-form-label">{{ 'Show Tooltip' | trans }}</label>
						<div class="uk-form-controls uk-form-controls-text">
							<input id="form-tooltip" type="checkbox" v-model="config.show_tooltip">
						</div>
					</div>
					<div class="uk-form-row">
						<label for="form-truncated_content"
						       class="uk-form-label">{{ 'Show truncated content if no excerpt available' | trans }}</label>
						<div class="uk-form-controls uk-form-controls-text">
							<input id="form-truncated_content" type="checkbox" v-model="config.show_truncated_content">
						</div>
					</div>
					<div class="uk-form-row">
						<span class="uk-form-label">{{ 'Target' | trans }}</span>
						<div class="uk-form-controls uk-form-controls-text">
							<p class="uk-form-controls-condensed">
								<label>
									<input type="radio" v-model="config.target" value="_blank">
									{{ 'New Tab' | trans }}
								</label>
							</p>
							<p class="uk-form-controls-condensed">
								<label>
									<input type="radio" v-model="config.target" value="_self">
									{{ 'Same Tab' | trans }}
								</label>
							</p>
						</div>
					</div>
					<div class="uk-form-row">
						<label for="form-subnavstyle" class="uk-form-label">{{ 'Navigation Style' | trans }}</label>
						<div class="uk-form-controls">
							<select id="form-subnavstyle" class="uk-form-width-large" v-model="config.subnav_style">
								<option value="">{{ 'None' | trans }}</option>
								<option value="uk-subnav-line"
								">{{ 'Line' | trans }}</option>
								<option value="uk-subnav-pill">{{ 'Pill' | trans }}</option>
							</select>
						</div>
					</div>
					<div class="uk-form-row">
						<label for="form-headingstyle" class="uk-form-label">{{ 'Heading Style' | trans }}</label>
						<div class="uk-form-controls">
							<select id="form-headingstyle" class="uk-form-width-large" v-model="config.heading_style">
								<option value="h1"
								">{{ 'Heading 1' | trans }}</option>
								<option value="h2"
								">{{ 'Heading 2' | trans }}</option>
								<option value="h3"
								">{{ 'Heading 3' | trans }}</option>
								<option value="h4"
								">{{ 'Heading 4' | trans }}</option>
							</select>
						</div>
					</div>
					<div class="uk-form-row">
						<label for="form-hrefclass" class="uk-form-label">{{ 'Href class' | trans }}</label>
						<div class="uk-form-controls uk-form-controls-text">
							<p class="uk-form-controls-condensed">
								<input id="form-hrefclass" type="text" class="uk-form-width-large" v-model="config
								.hrefclass">
							</p>
						</div>
					</div>

				</li>
			</ul>
		</div>
	</div>
</div>