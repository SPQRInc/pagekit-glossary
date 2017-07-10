<?php

namespace Spqr\Glossary\Controller;

use Pagekit\Application as App;
use Spqr\Glossary\Model\Item;

/**
 * @Access("glossary: manage items")
 * @Route("item", name="item")
 */
class ItemApiController
{
	/**
	 * @param array $filter
	 * @param int   $page
	 * @param int   $limit
	 * @Route("/", methods="GET")
	 * @Request({"filter": "array", "page":"int", "limit":"int"})
	 *
	 * @return mixed
	 */
	public function indexAction( $filter = [], $page = 0, $limit = 0 )
	{
		$query  = Item::query();
		$filter = array_merge( array_fill_keys( [ 'status', 'search', 'limit', 'order' ], '' ), $filter );
		extract( $filter, EXTR_SKIP );
		if ( is_numeric( $status ) ) {
			$query->where( [ 'status' => (int) $status ] );
		}
		if ( $search ) {
			$query->where(
				function( $query ) use ( $search ) {
					$query->orWhere(
						[
							'title LIKE :search'
						],
						[ 'search' => "%{$search}%" ]
					);
				}
			);
		}
		if ( preg_match( '/^(title)\s(asc|desc)$/i', $order, $match ) ) {
			$order = $match;
		} else {
			$order = [ 1 => 'title', 2 => 'asc' ];
		}
		$default = App::module( 'spqr/glossary' )->config( 'items_per_page' );
		$limit   = min( max( 0, $limit ), $default ) ? : $default;
		$count   = $query->count();
		$pages   = ceil( $count / $limit );
		$page    = max( 0, min( $pages - 1, $page ) );
		$items   = array_values(
			$query->offset( $page * $limit )->limit( $limit )->orderBy( $order[ 1 ], $order[ 2 ] )->get()
		);
		
		return compact( 'items', 'pages', 'count' );
	}
	
	/**
	 * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
	 * @param $id
	 *
	 * @return static
	 */
	public function getAction( $id )
	{
		if ( !$item = Item::where( compact( 'id' ) )->first() ) {
			App::abort( 404, 'Item not found.' );
		}
		
		return $item;
	}
	
	/**
	 * @Route(methods="POST")
	 * @Request({"ids": "int[]"}, csrf=true)
	 * @param array $ids
	 *
	 * @return array
	 */
	public function copyAction( $ids = [] )
	{
		foreach ( $ids as $id ) {
			if ( $item = Item::find( (int) $id ) ) {
				if ( !App::user()->hasAccess( 'glossary: manage items' ) ) {
					continue;
				}
				$item         = clone $item;
				$item->id     = null;
				$item->status = $item::STATUS_UNPUBLISHED;
				$item->title  = $item->title . ' - ' . __( 'Copy' );
				$item->date   = new \DateTime();
				$item->save();
			}
		}
		
		return [ 'message' => 'success' ];
	}
	
	/**
	 * @Route("/bulk", methods="POST")
	 * @Request({"items": "array"}, csrf=true)
	 * @param array $items
	 *
	 * @return array
	 */
	public function bulkSaveAction( $items = [] )
	{
		foreach ( $items as $data ) {
			$this->saveAction( $data, isset( $data[ 'id' ] ) ? $data[ 'id' ] : 0 );
		}
		
		return [ 'message' => 'success' ];
	}
	
	/**
	 * @Route("/", methods="POST")
	 * @Route("/{id}", methods="POST", requirements={"id"="\d+"})
	 * @Request({"item": "array", "id": "int"}, csrf=true)
	 * @param     $data
	 * @param int $id
	 *
	 * @return array
	 */
	public function saveAction( $data, $id = 0 )
	{
		if ( !$id || !$item = Item::find( $id ) ) {
			if ( $id ) {
				App::abort( 404, __( 'Item not found.' ) );
			}
			$item = Item::create();
		}
		if ( !$data[ 'slug' ] = App::filter( $data[ 'slug' ] ? : $data[ 'title' ], 'slugify' ) ) {
			App::abort( 400, __( 'Invalid slug.' ) );
		}
		
		$item->save( $data );
		
		return [ 'message' => 'success', 'item' => $item ];
	}
	
	/**
	 * @Route("/bulk", methods="DELETE")
	 * @Request({"ids": "array"}, csrf=true)
	 * @param array $ids
	 *
	 * @return array
	 */
	public function bulkDeleteAction( $ids = [] )
	{
		foreach ( array_filter( $ids ) as $id ) {
			$this->deleteAction( $id );
		}
		
		return [ 'message' => 'success' ];
	}
	
	/**
	 * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
	 * @Request({"id": "int"}, csrf=true)
	 * @param $id
	 *
	 * @return array
	 */
	public function deleteAction( $id )
	{
		if ( $item = Item::find( $id ) ) {
			if ( !App::user()->hasAccess( 'glossary: manage items' ) ) {
				App::abort( 400, __( 'Access denied.' ) );
			}
			$item->delete();
		}
		
		return [ 'message' => 'success' ];
	}
	
}