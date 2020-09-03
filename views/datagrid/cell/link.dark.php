<?php
/** @var \Spiral\Keeper\Helper\GridBuilder $_gb_ */
$_name_ = inject('name');

ob_start(); ?>${context}${value}${template}${body}<?php $_context_ = ob_get_clean();
ob_start(); ?>${title}<?php $_title_ = ob_get_clean();
ob_start(); ?>${href}${url}<?php $_href_ = ob_get_clean();

$_gb_->addColumn(
    $_name_,
    [
        'title'   => inject('label'),
        'sortDir' => injected('sort') ? inject('sort-dir', 'asc') : null
    ],
    [
        'name'      => 'link',
        'arguments' => [
            'title' => $_gb_->toHandlebars(trim($_title_)),
            'body'  => $_gb_->toHandlebars(trim($_context_)),
            'href'  => $_gb_->toHandlebars($_href_),
        ]
    ],
    inject('class')
);

if (injected('sort-default')) {
    $_gb_->setOption('sort', $_name_);
}

unset($_name_, $_title_, $_context_, $_href_);

?>
