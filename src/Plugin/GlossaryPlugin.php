<?php

namespace Spqr\Glossary\Plugin;

use Pagekit\Application as App;
use Pagekit\Content\Event\ContentEvent;
use Pagekit\Event\EventSubscriberInterface;
use Spqr\Glossary\Model\Item;


class GlossaryPlugin implements EventSubscriberInterface
{
	/**
	 * Content plugins callback.
	 *
	 * @param ContentEvent $event
	 */
	public function onContentPlugins( ContentEvent $event )
	{
		$content = $event->getContent();
		$query   = Item::where( [ 'status = ?' ], [ Item::STATUS_PUBLISHED ] );
		
		$node    = App::node();
		$config  = App::module( 'glossary' )->config();
		$target  = $config[ 'target' ];
		$tooltip = $config[ 'show_tooltip' ];
		
		if ( $node->link != "@glossary" ) {
			
			$markers = [];
			
			foreach ( $items = $query->get() as $key => $item ) {
				
				if ( $item->get( 'markdown' ) ) {
					$item->content = App::markdown()->parse( $item->content );
					$item->excerpt = App::markdown()->parse( $item->excerpt );
				}
				
				if ( empty( $item->excerpt ) ) {
					$item->excerpt = $item->content;
				}
				
				$url                                   = App::url( '@glossary/id', [ 'id' => $item->id ], 'base' );
				$markers[ strtolower( $item->title ) ] = [ 'text' => $item->title, 'url' => $url ];
				
				if ( is_array( $item->marker ) && !empty ( $item->marker ) ) {
					foreach ( $item->marker as $marker ) {
						$markers[ strtolower( $marker ) ] = [ 'text' => $marker, 'url' => $url ];
					}
				}
				
			}
			
			foreach ( $markers as $marker ) {
				$text    = $marker[ 'text' ];
				$url     = $marker[ 'url' ];
				$content =
					preg_replace(
						'/\b' . preg_quote( $text, "/" ) . '\b/i',
						"<a href='$url' target='$target' " . ( $tooltip ? "data-uk-tooltip title='$item->excerpt'"
							: "" ) . ">\$0</a>",
						$content
					);
			}
			
			$event->setContent( $content );
			
		}
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function subscribe()
	{
		return [
			'content.plugins' => [ 'onContentPlugins', 10 ]
		];
	}
}