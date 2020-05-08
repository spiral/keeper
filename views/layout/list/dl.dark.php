<dl class="@if(injected('horizontal'))row@endif ${class}" attr:aggregate>
  <?php if(injected('horizontal')) { $_dl_horizontal_ = true; } ?>
  ${context}
  <?php unset($_dl_horizontal_); ?>
</dl>
