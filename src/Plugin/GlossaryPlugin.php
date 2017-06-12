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
		libxml_use_internal_errors( true );
		
		$node   = App::node();
		$config = App::module( 'glossary' )->config();
		
		$content = $event->getContent();
		
		if ( $content ) {
			$query = Item::where( [ 'status = ?' ], [ Item::STATUS_PUBLISHED ] );
			
			$dom = new \DOMDocument();
			$dom->loadHtml( utf8_decode($content) );
			$xpath = new \DOMXPath( $dom );
			
			$target    = $config[ 'target' ];
			$tooltip   = $config[ 'show_tooltip' ];
			$class     = $config[ 'hrefclass' ];
			$hrefclass = ( $class ? "class='$class'" : "" );
			
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
					$markers[ strtolower( $item->title ) ] =
						[ 'text' => $item->title, 'url' => $url, 'excerpt' => $item->excerpt ];
					
					if ( is_array( $item->marker ) && !empty ( $item->marker ) ) {
						foreach ( $item->marker as $marker ) {
							$markers[ strtolower( $marker ) ] =
								[ 'text' => $marker, 'url' => $url, 'excerpt' => $item->excerpt ];
						}
					}
				}
				
				foreach ( $markers as $marker ) {
					foreach ( $xpath->query( '//text()[not(ancestor::a)]' ) as $node ) {
						$text     = $marker[ 'text' ];
						$url      = $marker[ 'url' ];
						$tip      = strip_tags( $marker[ 'excerpt' ] );
						$tooltip  = ( $tooltip ? "data-uk-tooltip='' title='$tip'" : "" );
						$replaced = preg_replace(
							'/\b' . preg_quote( $text, "/" ) . '\b/i',
							"<a href='$url' $hrefclass target='$target' $tooltip>\$0</a>",
							$node->wholeText
						);
						$newNode  = $dom->createDocumentFragment();
						$newNode->appendXML( $replaced );
						$node->parentNode->replaceChild( $newNode, $node );
					}
				}
				
				$event->setContent( utf8_encode( $dom->saveHTML()) );
			}
		}
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function subscribe()
	{
		return [
			'content.plugins' => [ 'onContentPlugins', 5 ]
		];
	}
}