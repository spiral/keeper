@if(injected('href'))
  <a href="${href}" class="btn btn-${kind|primary}" role="button" tabindex="0">
    @if(injected('icon-before'))<i class="fa fa-${icon-before}"></i>@endif
    <span>${label}</span>
    @if(injected('icon-after'))<i class="fa fa-${icon-after}"></i>@endif
  </a>
@else
  <button type="${type|button}" class="btn btn-${kind|primary}" data-sf="${data-sf}" data-target="${data-target}">
    @if(injected('icon-before'))<i class="fa fa-${icon-before}"></i>@endif
    <span>${label}</span>
    @if(injected('icon-after'))<i class="fa fa-${icon-after}"></i>@endif
  </button>
@endif
