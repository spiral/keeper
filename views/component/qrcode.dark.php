<stack:push name="scripts" unique-id="qr-code">
  <script type="text/javascript" src="/toolkit/qrcode/qrcode.js"></script>
</stack:push>
<div class="js-sf-qrcode" data-value="${value}"
     @if(injected('type')) data-type="${type}" @endif
     @if(injected('size')) data-size="${size}" @endif
     @if(injected('ecLevel')) data-ec-level="${ecLevel}" @endif
     @if(injected('bgColor')) data-bg-color="${bgColor}" @endif
     @if(injected('fgColor')) data-fg-color="${fgColor}" @endif
     @if(injected('logoUrl')) data-logo-url="${logoUrl}" @endif
     @if(injected('logoHeight')) data-logo-height="${logoHeight}" @endif
     @if(injected('logoWidth')) data-logo-width="${logoWidth}" @endif
     @if(injected('logoX')) data-logo-x="${logoX}" @endif
     @if(injected('logoY')) data-logo-y="${logoY}" @endif
     @if(injected('logoMargin')) data-logo-margin="${logoMargin}" @endif
></div>
