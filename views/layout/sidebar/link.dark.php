<use:bundle path="keeper:bundle"/>

<?php

/**
 * @var Spiral\Keeper\Module\Sitemap      $_sitemap_
 * @var Spiral\Keeper\Module\Sitemap\Node $_node_
 * @var Spiral\Keeper\Helper\RouteBuilder $_router_
 */
$_node_ = inject('node');
?>
<keeper:sidebar:a class="sf-subnav__item" node="{{ $_node_ }}">
    <keeper:sidebar:link node="{{ $_node_ }}"/>
    <span>{{ $_node_->getOption('title') }}</span>
</keeper:sidebar:a>
