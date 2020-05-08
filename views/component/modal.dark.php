<div class="modal sf-modal" data-sf="modal" id="${id}" tabindex="-1" role="dialog" aria-labelledby="${id}-label"
     aria-hidden="true">
  <div data-sf="modal-content" class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-${kind|white}">
        <h4 class="modal-title" id="${id}-label">${title}</h4>
        <button data-sf="modal-close" type="button" class="close" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">${context}</div>
      <div class="modal-footer">
        @if(injected('cancel'))
          <button data-sf="modal-cancel" type="button" class="btn btn-${cancel-kind|secondary}" data-dismiss="modal">
            ${cancel-label|Cancel}
          </button>
        @endif
        @if(injected('confirm'))
          <button data-sf="modal-confirm" type="button" class="btn btn-${confirm-kind|primary}">
            ${confirm-label|Confirm}
          </button>
        @endif
      </div>
    </div>
  </div>
</div>
