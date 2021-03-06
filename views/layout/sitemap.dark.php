<?php
/**
 * @var \Psr\Http\Message\ServerRequestInterface $_serverRequest_
 * @var \Spiral\Keeper\Helper\RouteBuilder       $_router_
 * @var \Spiral\Security\GuardInterface          $_guard_
 * @var \Spiral\Keeper\Module\Sitemap            $_sitemap_
 */

$_router_ = $this->container->get(\Spiral\Keeper\Helper\RouteBuilder::class);
$_sitemap_ = $this->container->get(\Spiral\Keeper\Module\Sitemap::class);

// allow user re-definition
$_activeRoute_ = inject('activeRoute', $_serverRequest_->getAttribute(\Spiral\Router\Router::ROUTE_NAME));

// all visible nodes and active path highlighted
$_sitemap_ = $_sitemap_->withVisibleNodes($_guard_, $_activeRoute_);
?>
