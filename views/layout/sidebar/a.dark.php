<?php

/**
 * @var Spiral\Keeper\Module\Sitemap      $_sitemap_
 * @var Spiral\Keeper\Module\Sitemap\Node $_node_
 * @var Spiral\Keeper\Helper\RouteBuilder $_router_
 */
$_node_ = inject('node');
?>

<a class="${class} {!! $_node_->getOption('active') ? 'active' : '' !!}"
   href="{!! $_router_->uri($_sitemap_->getNamespace(), $_node_->getOption('route') ?? $_node_->getName()) !!}">
    ${context}
</a>

