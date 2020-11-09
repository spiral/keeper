<a class="card@if(injected('kind')) bg-${kind} text-white@endif" href="${href}">
  <div class="card-body">
    <div class="sf-statpanel">
      <div class="sf-statpanel__text">
        <span class="sf-statpanel__value">${value}</span>
        <span class="sf-statpanel__label">&nbsp;${label}&nbsp;</span>
      </div>
      <span class="sf-statpanel__icon">
        <i class="fa fa-${icon}"></i>
      </span>
    </div>
  </div>
</a>
