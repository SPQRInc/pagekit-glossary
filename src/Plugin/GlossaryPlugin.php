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
		
		if ( $content ) {
			$query = Item::where( [ 'status = ?' ], [ Item::STATUS_PUBLISHED ] );
			
			$target    = $config[ 'target' ];
			$tooltip   = $config[ 'show_tooltip' ];
			$class     = $config[ 'hrefclass' ];
			$hrefclass = ( $class ? "class='$class'" : "" );
			$truncate  = $config[ 'truncate_tooltip' ];
			
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
				
				if ( $config[ 'detection' ] == 'auto' ) {
					foreach ( $markers as $marker ) {
						$text = $marker[ 'text' ];
						$url  = $marker[ 'url' ];
						$excerpt  = strip_tags( $marker[ 'excerpt' ] );
						
						if ( $truncate > 0 ) {
							$excerpt = strlen( $excerpt ) > $truncate ? substr( $excerpt, 0, $truncate ) . "..." : $excerpt;
						}
						
						$tooltip = ( $tooltip ? "data-uk-tooltip='' title='" . $excerpt . "'" : "" );
						$replace = "<a href='$url' $hrefclass target='$target' $tooltip>\$0</a>";
						$content = $this->searchDOM(
							$content,
							$text,
							$replace,
							[ 'a', 'img', 'script', 'style', 'code', 'pre' ]
						);
					}
				}
				
				
				$event->setContent( $content );
			}
		}
	}
	
	/**
	 * @param       $content
	 * @param       $search
	 * @param       $replace
	 * @param array $excludedParents
	 *
	 * @return mixed
	 */
	public function searchDOM( $content, $search, $replace, $excludedParents = [] )
	{
		
		$dom = HtmlDomParser::str_get_html(
			$content,
			true,
			true,
			DEFAULT_TARGET_CHARSET,
			false,
			DEFAULT_BR_TEXT,
			DEFAULT_SPAN_TEXT
		);
		
		
		foreach ( $dom->find( 'text' ) as $element ) {
			
			if ( !in_array( $element->parent()->tag, $excludedParents ) )
				$element->innertext = preg_replace(
					'/\b' . preg_quote( $search, "/" ) . '\b/i',
					$replace,
					$element->innertext
				);
			
		}
		
		return $dom->save();
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