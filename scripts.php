<?php

return [
	
	/*
	 * Installation hook
	 *
	 */
	'install'   => function( $app ) {
		$util = $app[ 'db' ]->getUtility();
		if ( $util->tableExists( '@glossary_item' ) === false ) {
			$util->createTable(
				'@glossary_item',
				function( $table ) {
					$table->addColumn(
						'id',
						'integer',
						[
							'unsigned'      => true,
							'length'        => 10,
							'autoincrement' => true
						]
					);
					$table->addColumn( 'slug', 'string', [ 'length' => 255 ] );
					$table->addColumn( 'status', 'smallint' );
					$table->addColumn( 'title', 'string', [ 'length' => 255 ] );
					$table->addColumn( 'marker', 'json_array', [ 'notnull' => false ] );
					$table->addColumn( 'content', 'text' );
					$table->addColumn( 'excerpt', 'text' );
					$table->addColumn( 'data', 'json_array', [ 'notnull' => false ] );
					$table->addColumn( 'date', 'datetime', [ 'notnull' => false ] );
					$table->addColumn( 'modified', 'datetime' );
					$table->setPrimaryKey( [ 'id' ] );
					$table->addUniqueIndex( [ 'slug' ], '@GLOSSARY_SLUG' );
				}
			);
		}
		
	},
	
	/*
	 * Enable hook
	 *
	 */
	'enable'    => function( $app ) {
	},
	
	/*
	 * Uninstall hook
	 *
	 */
	'uninstall' => function( $app ) {
		// remove the tables
		$util = $app[ 'db' ]->getUtility();
		if ( $util->tableExists( '@glossary_item' ) ) {
			$util->dropTable( '@glossary_item' );
		}
		
		// remove the config
		$app[ 'config' ]->remove( 'spqr/glossary' );
	},
	
	/*
	 * Runs all updates that are newer than the current version.
	 *
	 */
	'updates'   => [
		'1.0.7' => function ($app) {
			$app['config']->set('spqr/glossary', $app->config('glossary')->toArray());
			$app['config']->remove('glossary');
		}
	],

];