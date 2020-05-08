<a href="${href}${url}" class="btn btn-${kind|light} ${class}" attr:aggregate>
  @if(injected('icon') || injected('icon-before'))<i class="fa fa-${icon-before}${icon}"></i>@endif<span>${context}${label}</span>
</a>
