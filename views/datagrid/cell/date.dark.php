<?php

/** @var \Spiral\Keeper\Helper\GridBuilder $_gb_ */
$_name_ = inject('name');

$_gb_->addColumn(
    $_name_,
    [
        'title'   => inject('label'),
        'sortDir' => injected('sort') ? inject('sort-dir', 'asc') : null
    ],
    [
        'name'      => 'dateFormat',
        'arguments' => [inject('format', 'LLL dd, yyyy hh:mm')]
    ],
    inject('class', 'text-nowrap')
);

if (injected('sort-default')) {
    $_gb_->setOption('sort', $_name_);
}

unset($_name_);

?>
