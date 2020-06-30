<?php
/**
 * @var \Spiral\Keeper\Helper\GridBuilder $_gb_
 * @var \Spiral\Security\GuardInterface   $_guard_
 */
if (!injected('permission') || $_guard_->allows(inject('permission'), inject('permission-context', []))) {
ob_start(); ?>${href}${url}<?php $_href_ = ob_get_clean();
ob_start(); ?><block:template><span class="text-danger"><i class="fa fw fa-trash"></i>&nbsp;&nbsp; ${label|Delete}</span></block:template><?php $_template_ = ob_get_clean();

$_action_ = [
    'type'      => 'action',
    'url'       => $_gb_->toHandlebars($_href_),
    'method'    => inject('method', 'DELETE'),
    'label'     => inject('label', 'Delete'),
    'icon'      => inject('icon', 'trash'),
    'template'  => $_gb_->toHandlebars($_template_),
    'condition' => $_gb_->toHandlebars(inject('condition')),
    'data'      => inject('data', []),
    'refresh'   => inject('refresh', true)
];

$_action_['confirm'] = [];
ob_start(); ?>${confirm|Are you sure to delete this entry?}<?php $_action_['confirm']['body'] = $_gb_->toHandlebars(ob_get_clean());
ob_start(); ?>${confirm-title|Confirmation Required}<?php $_action_['confirm']['title'] = $_gb_->toHandlebars(ob_get_clean());
ob_start(); ?>${confirm-ok|Delete}<?php $_action_['confirm']['confirm'] = $_gb_->toHandlebars(ob_get_clean());
ob_start(); ?>${confirm-ok-kind|danger}<?php $_action_['confirm']['confirmKind'] = $_gb_->toHandlebars(ob_get_clean());
ob_start(); ?>${confirm-cancel|Cancel}<?php $_action_['confirm']['cancel'] = $_gb_->toHandlebars(ob_get_clean());

ob_start(); ?>
<block:success-message><i class="fa fa-check-circle"></i>&nbsp; {message}
</block:success-message><?php $_action_['toastSuccess'] = $_gb_->toHandlebars(ob_get_clean());
ob_start(); ?>
<block:error-message><i class="fa fa-exclamation"></i>&nbsp; {error}
</block:error-message><?php $_action_['toastError'] = $_gb_->toHandlebars(ob_get_clean());

$_gb_->addAction($_action_);
unset($_action_, $_href_, $_template_);
}

?>
