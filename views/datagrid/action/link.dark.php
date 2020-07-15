<?php
/**
 * @var \Spiral\Keeper\Helper\GridBuilder $_gb_
 * @var \Spiral\Security\GuardInterface   $_guard_
 */
if (!injected('permission') || $_guard_->allows(inject('permission'), inject('permission-context', []))) {
ob_start(); ?>${href}${url}<?php $_href_ = ob_get_clean();
ob_start(); ?>${template}<?php $_template_ = ob_get_clean();

$_gb_->addAction([
    'type'     => 'href',
    'url'      => $_gb_->toHandlebars($_href_),
    'label'    => inject('label'),
    'target'   => inject('target'),
    'icon'     => inject('icon'),
    'template' => $_gb_->toHandlebars($_template_)
]);
unset($_href_, $_template_);
}

?>
