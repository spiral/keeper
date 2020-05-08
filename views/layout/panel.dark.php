@if(!injected('permission') || $_guard_->allows(inject('permission'), inject('permission-context', [])))
  <div class="card@if(injected('kind')) bg-${kind} text-white@endif ${class}" attr:aggregate="prefix:panel-">
    @if(injected('header'))
      <div class="card-header@if(injected('header-kind')) bg-${header-kind} text-white@endif"
           attr:aggregate="prefix:header-">
        @if(injected('icon'))<i class="fa fa-${icon}"></i>&nbsp; @endif
        ${header}
      </div>
    @endif
    <div class="card-body" attr:aggregate="prefix:body-">
      ${context}
    </div>
  </div>
@endif
