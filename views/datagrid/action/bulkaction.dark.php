<?php
/**
 * @var \Spiral\Keeper\Helper\GridBuilder $_gb_
 * @var \Spiral\Security\GuardInterface   $_guard_
 */
if (!injected('permission') || $_guard_->allows(inject('permission'), inject('permission-context', []))) {
ob_start(); ?>${class}<?php $_className_ = ob_get_clean();
ob_start(); ?>${template}${context}<?php $_template_ = ob_get_clean();

$_action_ = [
    'onClick'      => inject('action'),
    'renderAs'  => $_gb_->toHandlebars($_template_),
    'className'  => $_gb_->toHandlebars($_className_),
];

$_gb_->addBulkAction(inject('id'), $_action_);
unset($_action_, $_template_, $_className_, $_onClick_);
}

?>
