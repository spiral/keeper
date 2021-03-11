<?php

/** @var \Spiral\Keeper\Helper\GridBuilder $_gb_ */
$_gb_ = $this->container->get(\Spiral\Keeper\Helper\GridBuilder::class);

if (injected('id')) {
    $_gb_->setID(inject('id'));
}

// various grid options
$_gb_->setOption('namespace', inject('namespace', ''));
$_gb_->setOption('method', inject('method', 'GET'));

$_gb_->setOption('captureForms', inject('capture-forms', []));
$_gb_->setOption('captureFilters', inject('capture-filters', []));

$_gb_->setOption('ui.headerCellClassName.actions', inject('actions-class', 'text-right'));
$_gb_->setOption('ui.cellClassName.actions', inject('actions-cell-class', 'text-right py-2'));

$_gb_->setOption('paginator.limitOptions', inject('paginate-options', [10, 20, 50, 100]));

// actions configuration
$_gb_->setOption('actions.title', inject('actions-title', ' '));
$_gb_->setOption('actions.label', inject('actions-label', 'Actions'));
$_gb_->setOption('actions.kind', inject('actions-kind', ''));
$_gb_->setOption('actions.size', inject('actions-size', 'sm'));
$_gb_->setOption('actions.icon', inject('actions-icon', 'cog'));
$_gb_->setOption('actions.class', inject('actions-class', ''));
//$_gb_->setOption('errorMessageTarget', '#' . $_gb_->getID());
$_gb_->setOption('responsive.listSummaryColumn', inject('list-summary', ''));
$_gb_->setOption('responsive.listExcludeColumns', inject('list-exclude', []));
$_gb_->setOption('responsive.tableExcludeColumns', inject('table-exclude', []));
$_gb_->setOption('responsive.listClass', inject('list-class', 'd-md-none'));
$_gb_->setOption('responsive.tableClass', inject('table-class', 'table d-none d-md-table'));

?>
<div class="sf-table ${class}" attr:aggregate>
  <div class="js-sf-datagrid" id="{{ $_gb_->getID() }}" @if(injected('url')) data-url="${url}" @endif >
    ${context}
    <script type="text/javascript" role="sf-options">
        (function () {
            return {!! $_gb_->render() !!};
        });
    </script>
  </div>
</div><?php unset($_gb_); ?>
