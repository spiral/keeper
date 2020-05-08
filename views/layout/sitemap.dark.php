<?php
/**
 * @var \Psr\Http\Message\ServerRequestInterface $_serverRequest_
 * @var \Spiral\Keeper\Module\RouteRegistry      $_router_
 * @var \Spiral\Security\GuardInterface          $_guard_
 * @var \Spiral\Keeper\Module\Sitemap            $_sitemap_
 */

$_router_ = $this->container->get(\Spiral\Keeper\Module\RouteRegistry::class);
$_sitemap_ = $this->container->get(\Spiral\Keeper\Module\Sitemap::class);

// allow user re-definition
$_activeRoute_ = inject('activeRoute', $_serverRequest_->getAttribute('routeName'));

// all visible nodes and active path highlighted
$_sitemap_ = $_sitemap_->withVisibleNodes($_guard_, $_activeRoute_);
?>
