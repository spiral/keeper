<?php

/**
 * @var Spiral\Keeper\Module\Sitemap\Node $_node_
 */
$_node_ = inject('node');
?>

@if($_node_->getOption('icon') !== null)
    <i class="fa fa-{!! $_node_->getOption('icon') !!}"></i>
@endif

