<?php

namespace Spqr\Glossary\Event;

use Pagekit\Application as App;
use Spqr\Glossary\UrlResolver;
use Pagekit\Event\EventSubscriberInterface;

class RouteListener implements EventSubscriberInterface
{
	
	/**
	 * Registers permalink route alias.
	 */
	public function onConfigureRoute($event, $route)
	{
		if ($route->getName() == '@glossary/id') {
			App::routes()->alias(dirname($route->getPath()).'/{slug}', '@glossary/id', ['_resolver' => 'Spqr\Glossary\UrlResolver']);
		}
	}
	
	/**
	 * Clears resolver cache.
	 */
	public function clearCache()
	{
		App::cache()->delete(UrlResolver::CACHE_KEY);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function subscribe()
	{
		return [
			'route.configure' => 'onConfigureRoute',
			'model.item.saved' => 'clearCache',
			'model.item.deleted' => 'clearCache'
		];
	}
}