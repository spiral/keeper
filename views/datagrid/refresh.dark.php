<a class="js-sf-grid-refresh btn btn-${kind|light} ${class}" data-grid-id="${grid-id}" attr:aggregate>
  @if(injected('icon') || injected('icon-before'))<i class="fa fa-${icon-before}${icon}"></i>@endif<span>${context}${label}</span>
</a>
