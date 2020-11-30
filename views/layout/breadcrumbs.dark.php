<?php
/**
 * Defined in keeper:layout/sitemap
 *
 * @var \Psr\Http\Message\ServerRequestInterface $_serverRequest_
 * @var \Spiral\Keeper\Helper\RouteBuilder       $_router_
 * @var \Spiral\Security\GuardInterface          $_guard_
 * @var \Spiral\Keeper\Module\Sitemap            $_sitemap_
 */

/** @var \Spiral\Keeper\Module\Sitemap $_sitemap_ */
$_bc_ = $_sitemap_->getActivePath();

// current params?
$_args_ = $_serverRequest_->getAttribute(\Spiral\Router\Router::ROUTE_MATCHES);
$_ln_ = array_pop($_bc_);
?>
<nav class="sf-breadcrumb" aria-label="breadcrumb">
    @if(count($_bc_) !== 0)
        <ul class="sf-breadcrumb__list">
            @foreach($_bc_ as $_n_)
                @if($_n_->getName() === $_activeRoute_)
                    <li class="sf-breadcrumb__item active" aria-current="page">{{ $_n_->getOption('title') }}</li>
                @elseif(in_array($_n_->getOption('type'), ['segment','group'], true))
                    <li class="sf-breadcrumb__item">{{ $_n_->getOption('title') }}</li>
                @else
                    <li class="sf-breadcrumb__item"><a
                                href="{!! $_router_->uri($_sitemap_->getNamespace(), $_n_->getOption('route') ?? $_n_->getName(), $_args_) !!}">{{ $_n_->getOption('title') }}</a>
                    </li>
                @endif
            @endforeach
            @php unset($_bc_, $_n_, $_args_); @endphp
        </ul>
    @endif
</nav>
