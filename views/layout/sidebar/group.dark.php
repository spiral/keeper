<use:bundle path="keeper:bundle"/>

<?php

/**
 * @var string                            $_name_
 * @var Spiral\Keeper\Module\Sitemap      $_sitemap_
 * @var Spiral\Keeper\Module\Sitemap\Node $_node_
 * @var Spiral\Keeper\Module\Sitemap\Node $_child_
 */
$_name_ = inject('name');
$_node_ = inject('node');
?>
<ul class="sf-nav__list">
    <li class="sf-nav__item">
        <div class="sf-nav__item-heading {!! $_node_->getOption('active') ? 'active' : '' !!}"
             data-sf="nav-item-toggle"
             aria-expanded="{!! $_node_->getOption('active') ? 'true' : 'false' !!}"
             aria-controls="nav-item-{!! $_name_ !!}">
            <keeper:sidebar:icon node="{!! $_node_ !!}"/>
            <span>{{ $_node_->getOption('title') }}</span>
        </div>
        <div class="sf-nav__item-content" data-sf-nav="item-content" id="nav-item-{!! $_name_ !!}">
            <div class="sf-subnav">
                @foreach($_node_ as $_child_)
                    <keeper:sidebar:link node="{{ $_child_ }}"/>
                @endforeach
            </div>
        </div>
    </li>
</ul>
