<?php

/** @var \Spiral\Keeper\Helper\GridBuilder $_gb_ */
$_name_ = inject('name');

ob_start(); ?>${context}${value}${template}${body}<?php $_context_ = ob_get_clean();

$_gb_->addColumn(
    $_name_,
    [
        'title'   => inject('label'),
        'sortDir' => injected('sort') ? inject('sortDir', 'asc') : null
    ],
    [
        'name'      => 'template',
        'arguments' => [$_gb_->toHandlebars(trim($_context_))]
    ],
    inject('class')
);

if (injected('sort-default')) {
    $_gb_->setOption('sort', $_name_);
}

unset($_name_, $_context_);

?>
