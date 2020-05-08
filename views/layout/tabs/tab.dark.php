<stack:push name="tab-headers">
  @if(!injected('permission') || $_guard_->allows(inject('permission'), inject('permission-context', [])))
    <li class="nav-item">
      <a data-sf="tabnav-tab"
         class="nav-link@if(inject('active') === true) active@endif"
         id="${id}-tab"
         href="#${id}"
         role="tab"
         aria-controls="${id}"
         aria-selected="${active}">
        @if(injected('icon'))<span class="fa fa-${icon}"></span> @endif${title}
      </a>
    </li>
  @endif
</stack:push>

<stack:push name="tab-body">
  @if(!injected('permission') || $_guard_->allows(inject('permission'), inject('permission-context', [])))
    <div class="sf-tabnav__content tab-pane@if(inject('active') === true) active@endif"
         id="${id}"
         role="tabpanel"
         aria-labelledby="${id}-tab">
      <div class="sf-main__wrapper">
        ${context}
      </div>
    </div>
  @endif
</stack:push>
