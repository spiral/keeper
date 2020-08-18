<use:bundle path="keeper:bundle"/>
<?php
/**
 * @var \Spiral\Keeper\Helper\GridBuilder $_gb_
 */
?>
<div class="border-bottom px-3 pt-3 pb-1">
  <div class="row">
      <?php ob_start();?>${context}<?php $_filters_ = trim(ob_get_clean()); ?>
    @if($_filters_ !== '')
      <div class="col col-12 col-lg-7 col-md-6 col-sm-12 mb-2">
          <?php $_gb_->captureFilterForm($_gb_->getID()); ?>
        <div class="sf-filter-toggle js-sf-filter-toggle" data-id="{!! $_gb_->getID()!!}" data-track-fields="${fields}${track}">
          <div class="">
            @declare(syntax=off)
            <button
              class="btn btn-light sf-filter-toggle-button"
              data-class-emptyfilter="btn-light"
              data-class-hasfilter="btn-primary"
              data-template="<i class='fas fa-filter'></i> {{#if isEmpty}}Filter{{else}}Filter<span class='ml-2 badge badge-light'>{{changes}}</span>{{/if}}"
            ><i class="fas fa-filter"></i> [[Filter]]
            </button>
            @declare(syntax=on)
          </div>
          <div class="sf-filter-toggle-panel" data-class-open="" data-class-closed="d-none">
            <div class="position-relative">
              <div class="border position-absolute shadow-sm p-3 bg-white w-100 mt-2" style="z-index: 1">
                  <?php $_gb_->captureForm('#' . $_gb_->getID() . '-filter'); ?>
                <form:wrapper action="#{!! $_gb_->getID().'-filter' !!}" id="#{!! $_gb_->getID() . '-filter' !!}"
                              lock-type="none" submit-on-reset="true">
                  {!! $_filters_ !!}
                  @if(injected('buttons'))
                    <div class="col-12 text-right">
                      <div class="btn-group">
                        <ui:button type="reset" kind="light" label="Clear"/>
                        <ui:button label="Apply" type="submit"/>
                      </div>
                    </div>
                  @endif
                </form:wrapper>
              </div>
            </div>
          </div>
        </div>
          @if(inject('refresh', false))
            <a class="btn btn-light js-sf-grid-refresh" data-grid-id="{!! $_gb_->getID() !!}"><i class="fas fa-sync"></i> [[Refresh]]</a>
          @endif
      </div>
    @elseif(injected('search'))
      <div class="col col-12 col-lg-7 col-md-6 col-sm-12 mb-2">
          @if(inject('refresh', false))
            <a class="btn btn-light js-sf-grid-refresh" data-grid-id="{!! $_gb_->getID() !!}"><i class="fas fa-sync"></i> [[Refresh]]</a>
          @endif
      </div>
    @endif
      @if(injected('search'))
      <div class="col col-12 col-lg-5 col-md-6 col-sm-12">
          <?php
          $_gb_->captureForm('#' . $_gb_->getID() . '-search'); ?>
          @if(inject('immediate', false))
          <form:wrapper action="#{!! $_gb_->getID() . '-search' !!}" id="{!! $_gb_->getID() . '-search' !!}"
                        lock-type="none" immediate="300">
              <form:input add-icon="search" name="${search-name|search}" type="search" placeholder="Search"/>
          </form:wrapper>
          @else
          <form:wrapper action="#{!! $_gb_->getID() . '-search' !!}" id="{!! $_gb_->getID() . '-search' !!}"
                        lock-type="none" immediate="${immediate}">

              <form:input add-icon="search" size="9" name="${search-name|search}" type="search" placeholder="Search"/>
              <div class="col-md-3 col-sm-12">
                  <div class="btn-group w-100">
                      <ui:button label="Search" type="submit"/>
                  </div>
              </div>
          </form:wrapper>
          @endif
      </div>
      @endif
  </div>
</div>
