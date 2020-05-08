<?php ob_start(); ?>${template}<?php

$_template_ = \Spiral\Toolkit\Helper\Handlebars::convert(ob_get_clean());

ob_start(); ?><block:success-message><i class="fa fa-check-circle"></i>&nbsp; {message}</block:success-message><?php
$_successMessage_ = \Spiral\Toolkit\Helper\Handlebars::convert(ob_get_clean());

ob_start(); ?><block:error-message><i class="fa fa-exclamation"></i>&nbsp; {error}</block:error-message><?php
$_errorMessage_ = \Spiral\Toolkit\Helper\Handlebars::convert(ob_get_clean());

$_confirm_ = [];
if (injected('confirm')) {
ob_start(); ?>${confirm}<?php $_confirm_['body'] = \Spiral\Toolkit\Helper\Handlebars::convert(ob_get_clean());
ob_start(); ?>${confirm-title|Confirmation Required}<?php $_confirm_['title'] =\Spiral\Toolkit\Helper\Handlebars::convert(ob_get_clean());
ob_start(); ?>${confirm-ok}<?php $_confirm_['confirm'] = \Spiral\Toolkit\Helper\Handlebars::convert(ob_get_clean());
ob_start(); ?>${confirm-cancel}<?php $_confirm_['cancel'] = \Spiral\Toolkit\Helper\Handlebars::convert(ob_get_clean());
}

?>
<button
  data-url="${url}${href}"
  @if(injected('method')) data-method="${method}" @endif
  @if(injected('refresh')) data-refresh="${refresh}" @endif
  @if(injected('data')) data-data='${data}' @endif
  @if(injected('template')) data-template="{!! htmlentities($_template_) !!}" @endif
  @if(injected('template-name')) data-template-name="${template-name}" @endif
  @if(injected('lock-type')) data-lock-type="${lock-type|default}" @endif
  data-before-submit="${before-submit}"
  data-after-submit="${after-submit}"
  data-toast-success="{!! htmlentities($_successMessage_) !!}"
  data-toast-error="{!! htmlentities($_errorMessage_) !!}"
  @if(injected('confirm')) data-confirm='@json($_confirm_)' @endif
  class="js-sf-action btn btn-${kind|primary} ${class}"
  attr:aggregate
>
  @if(injected('icon-before') || injected('icon'))<i class="fa fa-${icon-before}${icon}"></i>@endif
  <span>${context}${label}</span>
  @if(injected('icon-after'))<i class="fa fa-${icon-after}"></i>@endif
</button>
<?php
unset($_template_, $_successMessage_, $_errorMessage_, $_confirm_);
?>
