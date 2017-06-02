<?php

use Pagekit\Application;
use Spqr\Glossary\Event\RouteListener;
use Spqr\Glossary\Plugin\GlossaryPlugin;


return [
	'name' => 'glossary',
	'type' => 'extension',
	'main' => function( Application $app ) {
	
	},
	
	'autoload' => [
		'Spqr\\Glossary\\' => 'src'
	],
	
	'nodes' => [
		'glossary' => [
			'name'       => '@glossary',
			'label'      => 'Glossary',
			'controller' => 'Spqr\\Glossary\\Controller\\SiteController',
			'protected'  => true,
			'frontpage'  => true
		]
	],
	
	'routes' => [
		'/glossary'     => [
			'name'       => '@glossary',
			'controller' => [ 'Spqr\\Glossary\\Controller\\GlossaryController' ]
		],
		'/api/glossary' => [
			'name'       => '@glossary/api',
			'controller' => [
				'Spqr\\Glossary\\Controller\\ItemApiController'
			]
		]
	],
	
	'widgets' => [],
	
	'menu' => [
		'glossary'           => [
			'label'  => 'Glossary',
			'url'    => '@glossary/item',
			'active' => '@glossary/item*',
			'icon'   => 'glossary:icon.svg'
		],
		'glossary: items'    => [
			'parent' => 'glossary',
			'label'  => 'Items',
			'icon'   => 'glossary:icon.svg',
			'url'    => '@glossary/item',
			'access' => 'glossary: manage items',
			'active' => '@glossary/item*'
		],
		'glossary: settings' => [
			'parent' => 'glossary',
			'label'  => 'Settings',
			'url'    => '@glossary/settings',
			'access' => 'glossary: manage settings'
		]
	],
	
	'permissions' => [
		'glossary: manage settings'   => [
			'title' => 'Manage settings'
		],
		'glossary: manage glossaries' => [
			'title' => 'Manage glossaries'
		]
	],
	
	'settings' => '@glossary/settings',
	
	'resources' => [
		'glossary:' => ''
	],
	
	'config' => [
		'items_per_page'         => 20,
		'show_tooltip'           => true,
		'show_truncated_content' => true,
		'target'                 => '_self',
		'subnav_style'           => '',
		'heading_style'          => 'h1',
		'items'                  => [
			'markdown_enabled' => true
		],
	],
	
	'events' => [
		'boot' => function( $event, $app ) {
			$app->subscribe(
				new RouteListener,
				new GlossaryPlugin
			);
		},
		
		'site'         => function( $event, $app ) {
			
			$app[ 'scripts' ]->add(
				'uikit-tooltip',
				'app/assets/uikit/js/components/tooltip.min.js'
			);
			
		},
		'view.scripts' => function( $event, $scripts ) {
			$scripts->register( 'link', 'glossary:app/bundle/link.js', '~panel-link' );
			$scripts->register( 'item-meta', 'glossary:app/bundle/item-meta.js', '~item-edit' );
			$scripts->register( 'item-marker', 'glossary:app/bundle/item-marker.js', '~item-edit' );
		}
	]
];