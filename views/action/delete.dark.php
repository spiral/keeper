<?php ob_start(); ?>${template}<?php

$_template_ = \Spiral\Toolkit\Helper\Handlebars::convert(ob_get_clean());

ob_start(); ?><block:success-message><i class="fa fa-check-circle"></i>&nbsp; {message}</block:success-message><?php
$_successMessage_ = \Spiral\Toolkit\Helper\Handlebars::convert(ob_get_clean());

ob_start(); ?><block:error-message><i class="fa fa-exclamation"></i>&nbsp; {error}</block:error-message><?php
$_errorMessage_ = \Spiral\Toolkit\Helper\Handlebars::convert(ob_get_clean());

$_confirm_ = [];
ob_start(); ?>${confirm|Are you sure to delete this entry?}<?php $_confirm_['body'] = \Spiral\Toolkit\Helper\Handlebars::convert(ob_get_clean());
ob_start(); ?>${confirm-title|Confirmation Required}<?php $_confirm_['title'] =\Spiral\Toolkit\Helper\Handlebars::convert(ob_get_clean());
ob_start(); ?>${confirm-ok|Delete}<?php $_confirm_['confirm'] = \Spiral\Toolkit\Helper\Handlebars::convert(ob_get_clean());
ob_start(); ?>${confirm-cancel|Cancel}<?php $_confirm_['cancel'] = \Spiral\Toolkit\Helper\Handlebars::convert(ob_get_clean());

$_afterSubmitFunction_ = inject('after-submit');
if(injected('redirect')){
    $_afterSubmitFunction_ = 'rf' . md5(__FILE__ . '_' . __LINE__);
    ob_start(); ?>${redirect}<?php $_redirectURL_ = ob_get_clean();
}
?>
<button
  data-url="${url}${href}"
  data-method="${method|DELETE}"
  @if(injected('refresh')) data-refresh="${refresh}" @endif
  @if(injected('data')) data-data='${data}' @endif
  @if(injected('template')) data-template="{!! htmlentities($_template_) !!}" @endif
  @if(injected('template-name')) data-template-name="${template-name}" @endif
  @if(injected('lock-type')) data-lock-type="${lock-type|default}" @endif
  data-before-submit="${before-submit}"
  data-after-submit="{!! $_afterSubmitFunction_!!}"
  data-toast-success="{!! htmlentities($_successMessage_) !!}"
  data-toast-error="{!! htmlentities($_errorMessage_) !!}"
  data-confirm='@json($_confirm_)'
  class="js-sf-action btn btn-${kind|danger} ${class}"
  attr:aggregate
>
  <i class="fa fa-${icon-before}${icon|trash}"></i>
  <span>${context}${label}</span>
  @if(injected('icon-after'))<i class="fa fa-${icon-after}"></i>@endif
</button>
@if(injected('redirect'))
<script type="text/javascript">
    function {!! $_afterSubmitFunction_ !!}(_, err){
        if (!err) {
            setTimeout(function(){
                window.location = {{ $_redirectURL_ }};
            }, ${redirect-timeout|1000})
        }
    }
</script>
@endif
<?php
unset($_template_, $_successMessage_, $_errorMessage_, $_afterSubmitFunction_, $_redirectURL_);
?>
