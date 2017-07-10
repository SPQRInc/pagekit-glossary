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
	 * @var
	 */
	protected $target;
	/**
	 * @var
	 */
	protected $tooltip;
	/**
	 * @var
	 */
	protected $class;
	/**
	 * @var
	 */
	protected $hrefclass;
	/**
	 * @var
	 */
	protected $truncate;
	/**
	 * @var
	 */
	protected $exclusions;
	
	/**
	 * @var
	 */
	protected $detection;
	
	/**
	 * GlossaryPlugin constructor.
	 */
	public function __construct()
	{
		$config           = App::module( 'spqr/glossary' )->config();
		$this->target     = $config[ 'target' ];
		$this->tooltip    = $config[ 'show_tooltip' ];
		$class            = $config[ 'href_class' ];
		$this->hrefclass  = ( $class ? "class='$class'" : "" );
		$this->truncate   = $config[ 'truncate_tooltip' ];
		$this->exclusions = ( $config[ 'exclusions' ] ? $config[ 'exclusions' ] : [ 'a' ] );
		$this->detection  = $config[ 'detection' ];
	}
	
	/**
	 * Content plugins callback.
	 *
	 * @param ContentEvent $event
	 */
	public function onContentPlugins( ContentEvent $event )
	{
		$node    = App::node();
		$content = $event->getContent();
		
		if ( $content ) {
			if ( $node->link != "@glossary" ) {
				if ( $this->detection == 'auto' ) {
					foreach ( $markers = $this->getMarkers() as $marker ) {
						
						$text    = $marker[ 'text' ];
						$url     = $marker[ 'url' ];
						$excerpt = $marker[ 'excerpt' ];
						
						$tooltip = ( $this->tooltip ? "data-uk-tooltip='' title='" . $excerpt . "'" : "" );
						$replace = "<a href='$url' $this->hrefclass target='$this->target' $tooltip>\$0</a>";
						$content = $this->searchDOM(
							$content,
							$text,
							$replace,
							$this->exclusions
						);
					}
				}
				
				$event->setContent( $content );
			}
		}
	}
	
	/**
	 * @param \Pagekit\Content\Event\ContentEvent $event
	 */
	public function onApplyPlugins( ContentEvent $event )
	{
		
		$event->addPlugin(
			'glossary',
			[
				$this,
				'applyPlugin'
			]
		);
		
	}
	
	/**
	 * @param array $options
	 *
	 * @return string
	 */
	public function applyPlugin( array $options )
	{
		if ( isset( $options[ 'id' ] ) ) {
			$query = Item::where( [ 'id = ?', 'status = ?' ], [ $options[ 'id' ], Item::STATUS_PUBLISHED ] );
			$item  = $query->first();
			
			if ( isset( $options[ 'title' ] ) ) {
				$text = $options[ 'title' ];
			} else {
				$text = strip_tags( $item->title );
			}
			
			$item    = $this->prepareItem( $item );
			$tooltip = ( $this->tooltip ? "data-uk-tooltip='' title='" . $item->excerpt . "'" : "" );
			$link    = "<a href='$item->url' $this->hrefclass target='$this->target' $tooltip>$text</a>";
			
			return $link;
			
		} else {
			return '';
		}
	}
	
	/**
	 * @return array
	 */
	private function getMarkers()
	{
		$query   = Item::where( [ 'status = ?' ], [ Item::STATUS_PUBLISHED ] );
		$markers = [];
		
		foreach ( $items = $query->get() as $key => $item ) {
			
			$item = $this->prepareItem( $item );
			
			$markers[ strtolower( $item->title ) ] =
				[ 'text' => $item->title, 'url' => $item->url, 'excerpt' => $item->excerpt ];
			
			if ( is_array( $item->marker ) && !empty ( $item->marker ) ) {
				foreach ( $item->marker as $marker ) {
					$markers[ strtolower( $marker ) ] =
						[ 'text' => $marker, 'url' => $item->url, 'excerpt' => $item->excerpt ];
				}
			}
		}
		
		return $markers;
	}
	
	/**
	 * @param \Spqr\Glossary\Model\Item $item
	 *
	 * @return \Spqr\Glossary\Model\Item
	 */
	private function prepareItem( Item $item )
	{
		if ( $item->get( 'markdown' ) ) {
			$item->content = App::markdown()->parse( $item->content );
			$item->excerpt = App::markdown()->parse( $item->excerpt );
		}
		
		if ( empty( $item->excerpt ) ) {
			$item->excerpt = $item->content;
		}
		
		$item->excerpt = strip_tags( $item->excerpt );
		
		if ( $this->truncate > 0 ) {
			$item->excerpt =
				strlen( $item->excerpt ) > $this->truncate ? substr( $item->excerpt, 0, $this->truncate ) . "..."
					: $item->excerpt;
		}
		
		$item->url = App::url( '@glossary/id', [ 'id' => $item->id ], 'base' );
		
		return $item;
	}
	
	/**
	 * @param       $content
	 * @param       $search
	 * @param       $replace
	 * @param array $excludedParents
	 *
	 * @return mixed
	 */
	private function searchDOM( $content, $search, $replace, $excludedParents = [] )
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
			if ( !in_array( $element->parent()->tag, $excludedParents ) ) {
				$element->innertext = preg_replace(
					'/(?<!\w)' . preg_quote( $search, "/" ) . '(?!\w)/i',
					$replace,
					$element->innertext
				);
			}
		}
		
		return $dom->save();
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function subscribe()
	{
		return [
			'content.plugins' => [
				[ 'onApplyPlugins', 15 ],
				[ 'onContentPlugins', 5 ]
			],
		];
	}
}