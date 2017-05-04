<?php
namespace Spqr\Glossary\Model;

use Pagekit\Application as App;
use Pagekit\Application\Exception;
use Pagekit\System\Model\DataModelTrait;

/**
 * @Entity(tableClass="@glossary_item")
 */
class Item implements \JsonSerializable
{
	use ItemModelTrait, DataModelTrait;
	
	/* Product draft. */
	const STATUS_DRAFT = 0;
	
	/* Product published. */
	const STATUS_PUBLISHED = 1;
	
	/* Product unpublished. */
	const STATUS_UNPUBLISHED = 2;
	
	/** @Column(type="integer") @Id */
	public $id;
	
	/** @Column(type="integer") */
	public $status;
	
	/** @Column(type="string") */
	public $slug;
	
	/** @Column(type="string") */
	public $title;
	
	/** @Column(type="json_array") */
	public $marker;
	
	/** @Column(type="string") */
	public $excerpt = '';
	
	/** @Column(type="string") */
	public $content = '';
	
	/** @Column(type="datetime") */
	public $date;
	
	/** @Column(type="datetime") */
	public $modified;
	
	
	/**
	 * @param $item
	 *
	 * @return mixed
	 */
	public static function getPrevious( $item )
	{
		return self::where(
			[ 'date > ?', 'date < ?', 'status = 1' ],
			[
				$item->date,
				new \DateTime
			]
		)->orderBy( 'date', 'ASC' )->first();
	}
	
	/**
	 * @param $item
	 *
	 * @return mixed
	 */
	public static function getNext( $item )
	{
		return self::where( [ 'date < ?', 'status = 1' ], [ $item->date ] )->orderBy( 'date', 'DESC' )->first();
	}
	
	/**
	 * @return mixed
	 */
	public function getStatusText()
	{
		$statuses = self::getStatuses();
		
		return isset( $statuses[ $this->status ] ) ? $statuses[ $this->status ] : __( 'Unknown' );
	}
	
	/**
	 * @return array
	 */
	public static function getStatuses()
	{
		return [
			self::STATUS_PUBLISHED   => __( 'Published' ),
			self::STATUS_UNPUBLISHED => __( 'Unpublished' ),
			self::STATUS_DRAFT       => __( 'Draft' )
		];
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function jsonSerialize()
	{
		$data = [
			'url' => App::url('@glossary/id', ['id' => $this->id], 'base'),
			'marker'      => $this->getMarker()
		];
		
		return $this->toArray( $data );
	}
	
}