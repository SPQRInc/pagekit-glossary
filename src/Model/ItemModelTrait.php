<?php

namespace Spqr\Glossary\Model;

use Pagekit\Database\ORM\ModelTrait;

/**
 * Class ItemModelTrait
 * @package Spqr\Glossary\Model
 */
trait ItemModelTrait
{
	use ModelTrait;
	
	
	/**
	 * @Saving
	 */
	public static function saving( $event, Item $item )
	{
		$item->modified = new \DateTime();
		$i              = 2;
		$id             = $item->id;
		while ( self::where( 'slug = ?', [ $item->slug ] )->where(
			function( $query ) use ( $id ) {
				if ( $id ) {
					$query->where( 'id <> ?', [ $id ] );
				}
			}
		)->first() ) {
			$item->slug = preg_replace( '/-\d+$/', '', $item->slug ) . '-' . $i++;
		}
	}
	
	
	/**
	 * @param                                 $event
	 * @param \Spqr\Glossary\Model\Item       $item
	 */
	public static function deleting( $event, Item $item )
	{
	}
	
	/**
	 * @return array
	 */
	public function getMarker()
	{
		if ( $this->marker ) {
			return array_values(
				array_map(
					function( $marker ) {
						return $marker;
					},
					$this->marker
				)
			);
		}
		
		return [];
	}
	
	
	/**
	 * Gets model data as array.
	 *
	 * @param  array $data
	 * @param  array $ignore
	 *
	 * @return array
	 */
	public function toArray( array $data = [], array $ignore = [] )
	{
		$metadata = static::getMetadata();
		$mappings = $metadata->getRelationMappings();
		
		foreach ( static::getProperties( $this ) as $name => $value ) {
			
			if ( isset( $data[ $name ] ) || isset( $mappings[ $name ] ) ) {
				continue;
			}
			
			switch ( $metadata->getField( $name, 'type' ) ) {
				case 'json_array':
					$value = $value ? : new \stdClass();
					break;
				case 'datetime':
					$value = $value ? $value->format( \DateTime::ATOM ) : null;
					break;
			}
			
			$data[ $name ] = $value;
		}
		
		return array_diff_key( $data, array_flip( $ignore ) );
	}
}