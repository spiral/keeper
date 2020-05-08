<div
  class="js-sf-localdate"
  attr:aggregate
  data-value="${value}"
  data-format="${format|LLL dd, yyyy hh:mm}"
  @if(injected('source-format')) data-source-format="${source-format}" @endif
  @if(injected('error-value')) data-error-value="${error-value}" @endif
  @if(injected('title-format')) data-title-format="${title-format}" @endif
></div>
