<?php

namespace Spqr\Glossary\Controller;

use Pagekit\Application as App;
use Spqr\Glossary\Model\Item;

/**
 * Class SiteController
 * @package Spqr\Glossary\Controller
 */
class SiteController
{
	
	/**
	 * @Route("/")
	 */
	public function indexAction( $value = '' )
	{
		
		$query = Item::where( [ 'status = ?' ], [ Item::STATUS_PUBLISHED ] );
		
		foreach ( $items = $query->get() as $item ) {
			
			$item->excerpt = App::content()->applyPlugins(
				$item->excerpt,
				[
					'item'     => $item,
					'markdown' => $item->get( 'markdown' )
				]
			);
			
			$item->content = App::content()->applyPlugins(
				$item->content,
				[
					'item'     => $item,
					'markdown' => $item->get( 'markdown' )
				]
			);
		}
		
		return [
			'$view'  => [
				'title' => App::node()->title ? : __( 'Glossary' ),
				'name'  => 'spqr/glossary:views/glossary-index.php'
			],
			'$data'  => [
				'config'   => App::module( 'spqr/glossary' )->config(),
				'items'    => $items,
				'alphabet' => range( 'A', 'Z' )
			],
			'config' => App::module( 'spqr/glossary' )->config()
		];
	}
	
	/**
	 * @Route("/{id}", name="id")
	 */
	public function detailsAction( $id = '' )
	{
		
		if ( !$item =
			Item::where( [ 'id = ?', 'status = ?', 'date < ?' ], [ $id, Item::STATUS_PUBLISHED, new \DateTime ] )
			    ->first()
		) {
			App::abort( 404, __( 'Item not found!' ) );
		}
		
		if ( $breadcrumbs = App::module( 'bixie/breadcrumbs' ) ) {
			$breadcrumbs->addUrl(
				[
					'title' => $item->title,
					'url'   => '',
				]
			);
		}
		
		$item->excerpt =
			App::content()->applyPlugins( $item->excerpt, [ 'item' => $item, 'markdown' => $item->get( 'markdown' ) ] );
		$item->content =
			App::content()->applyPlugins( $item->content, [ 'item' => $item, 'markdown' => $item->get( 'markdown' ) ] );
		
		
		$description = $item->get( 'meta.og:description' );
		if ( !$description ) {
			$description = strip_tags( $item->excerpt ? : $item->content );
			$description = rtrim( mb_substr( $description, 0, 150 ), " \t\n\r\0\x0B.," ) . '...';
		}
		
		return [
			'$view'         => [
				'title'          => $item->title ? : __( 'Glossary' ),
				'name'           => 'spqr/glossary:views/glossary-details.php',
				'og:title'       => $item->get( 'meta.og:title' ) ? : $item->title,
				'og:description' => $description
			],
			'$data'         => [
				'config' => App::module( 'spqr/glossary' )->config()
			],
			'item'          => $item,
			'heading_size'  => App::module( 'spqr/glossary' )->config( 'heading_size' ) ? : 'h1',
			'heading_class' => ( App::module( 'spqr/glossary' )->config( 'heading_class' ) ? "class='" . App::module(
					'glossary'
				)->config( 'heading_class' ) . "'" : "" )
		];
	}
	
}