<?php
namespace Spqr\Glossary\Controller;

use Pagekit\Application as App;
use Spqr\Glossary\Model\Item;


/**
 * @Access(admin=true)
 * @return string
 */
class GlossaryController
{
	/**
	 * @Access("glossary: manage glossaries")
	 * @Request({"filter": "array", "page":"int"})
	 * @param null $filter
	 * @param int  $page
	 *
	 * @return array
	 */
	public function itemAction( $filter = null, $page = 0 )
	{
		return [
			'$view' => [ 'title' => 'Items', 'name' => 'spqr/glossary:views/admin/item-index.php' ],
			'$data' => [
				'statuses' => Item::getStatuses(),
				'config'   => [
					'filter' => (object) $filter,
					'page'   => $page
				]
			]
		];
	}
	
	/**
	 * @Route("/item/edit", name="item/edit")
	 * @Access("glossary: manage glossaries")
	 * @Request({"id": "int"})
	 * @param int $id
	 *
	 * @return array
	 */
	public function editAction( $id = 0 )
	{
		try {
			$module = App::module( 'spqr/glossary' );
			
			if ( !$item = Item::where( compact( 'id' ) )->first() ) {
				if ( $id ) {
					App::abort( 404, __( 'Invalid glossary id.' ) );
				}
				$item = Item::create(
					[
						'status' => Item::STATUS_DRAFT,
						'date'   => new \DateTime()
					]
				);
				
				$item->set('markdown', $module->config('items.markdown_enabled'));
			}
			
			return [
				'$view' => [
					'title' => $id ? __( 'Edit Item' ) : __( 'Add Item' ),
					'name'  => 'spqr/glossary:views/admin/item-edit.php'
				],
				'$data' => [
					'item' => $item,
					'statuses' => Item::getStatuses()
				]
			];
		} catch ( \Exception $e ) {
			App::message()->error( $e->getMessage() );
			
			return App::redirect( '@glossary/glossary' );
		}
	}
	
	/**
	 * @Access("glossary: manage settings")
	 */
	public function settingsAction()
	{
		$module = App::module( 'spqr/glossary' );
		$config = $module->config;
		
		return [
			'$view' => [
				'title' => __( 'Glossary Settings' ),
				'name'  => 'spqr/glossary:views/admin/settings.php'
			],
			'$data' => [
				'config' => App::module( 'spqr/glossary' )->config()
			]
		];
	}
	
	/**
	 * @Request({"config": "array"}, csrf=true)
	 * @param array $config
	 *
	 * @return array
	 */
	public function saveAction( $config = [] )
	{
		App::config()->set( 'spqr/glossary', $config );
		
		return [ 'message' => 'success' ];
	}
	
}