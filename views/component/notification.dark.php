<div data-sf="notification" role="alert" data-position="${position|inline}"
     class="sf-notification alert alert-${kind|info}@if(!injected('static')) alert-dismissible@endif @if(!injected('position') || inject('position') === 'inline') active@endif">
  ${context}
  @if(!injected('static'))
    <button type="button" class="close" data-sf="notification-close" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  @endif
</div>
