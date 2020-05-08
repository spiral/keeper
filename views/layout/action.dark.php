<stack:push name="actions">
  @if(!injected('permission') || $_guard_->allows(inject('permission'), inject('permission-context', [])))
    ${context}
  @endif
</stack:push>
