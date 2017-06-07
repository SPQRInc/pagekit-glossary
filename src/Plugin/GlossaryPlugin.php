<?php

namespace Spqr\Glossary\Plugin;

use Pagekit\Application as App;
use Pagekit\Content\Event\ContentEvent;
use Pagekit\Event\EventSubscriberInterface;
use Spqr\Glossary\Model\Item;
use Sunra\PhpSimple\HtmlDomParser;


class GlossaryPlugin implements EventSubscriberInterface
{
	/**
	 * Content plugins callback.
	 *
	 * @param ContentEvent $event
	 */
	public function onContentPlugins( ContentEvent $event )
	{
		$node   = App::node();
		$config = App::module( 'glossary' )->config();
		
		$content = $event->getContent();
		$query   = Item::where( [ 'status = ?' ], [ Item::STATUS_PUBLISHED ] );
		
		$dom     = HtmlDomParser::str_get_html(
			$content,
			true,
			true,
			DEFAULT_TARGET_CHARSET,
			$config[ 'stip_nl' ],
			DEFAULT_BR_TEXT,
			DEFAULT_SPAN_TEXT
		);
		
		$target     = $config[ 'target' ];
		$tooltip    = $config[ 'show_tooltip' ];
		$class      = $config[ 'hrefclass' ];
		$hrefclass  = ( $class ? "class='$class'" : "" );
		
		$i = 0;
		
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
			
			foreach ( $dom->find( 'text' ) as $element ) {
				if ( !in_array( $element->parent()->tag, $config[ 'exclusions' ] ) ) {
					foreach ( $markers as $marker ) {
						$text               = $marker[ 'text' ];
						$url                = $marker[ 'url' ];
						$tip                = strip_tags( $marker[ 'excerpt' ] );
						$tooltip            = ( $tooltip ? "data-uk-tooltip title='$tip'" : "" );
						$tmpval             = "tmpval-$i";
						$element->innertext = preg_replace(
							'/\b' . preg_quote( $text, "/" ) . '\b/i',
							"<a href='$url' $hrefclass target='$target' $tmpval>\$0</a>",
							$element->innertext,
							1
						);
						
						$element->innertext = str_replace( $tmpval, $tooltip, $element->innertext );
						$i++;
					}
				}
			}
			
			$event->setContent( $dom );
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