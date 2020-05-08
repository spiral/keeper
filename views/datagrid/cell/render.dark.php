<?php

/** @var \Spiral\Keeper\Helper\GridBuilder $_gb_ */
$_name_ = inject('name');

$_gb_->addColumn(
    $_name_,
    [
        'title'   => inject('label'),
        'sortDir' => injected('sort') ? inject('sortDir', 'asc') : null
    ],
    [
        'name'      => inject('renderer'),
        'arguments' => inject('arguments', [])
    ],
    inject('class')
);

if (injected('sort-default')) {
    $_gb_->setOption('sort', $_name_);
}

unset($_name_);

?>
