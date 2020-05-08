<dt @if(isset($_dl_horizontal_)) class="col col-4 ${dt-class}" @endif attr:aggregate="prefix:dt-">
  ${name}${title}${label}
</dt>
<dd @if(isset($_dl_horizontal_)) class="col col-8 ${class}" @endif attr:aggregate>
  ${context}
</dd>
