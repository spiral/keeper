<aside class="sf-sidebar active" data-sf="sidebar" id="sidebar">
  <div class="sf-sidebar__container">
    <block:header>
      <div class="sf-sidebar__logo">
        <span class="dummy"></span>
        <span>Spiral Framework</span>
      </div>
    </block:header>
      <?php
      /**
       * Defined in keeper:layout/sitemap
       *
       * @var \Psr\Http\Message\ServerRequestInterface $_serverRequest_
       * @var \Spiral\Keeper\Helper\RouteBuilder       $_router_
       * @var \Spiral\Security\GuardInterface          $_guard_
       * @var \Spiral\Keeper\Module\Sitemap            $_sitemap_
       * @var \Spiral\Keeper\Module\Sitemap\Node       $_s_
       * @var \Spiral\Keeper\Module\Sitemap\Node       $_i_
       */
      ?>
    <nav class="sf-nav" data-sf="nav">
      @foreach($_sitemap_ as $_n_ => $_s_)
        @switch($_s_->getOption('type'))
          @case(\Spiral\Keeper\Module\Sitemap::TYPE_LINK)
          <div class="sf-navgroup">
            <ul class="sf-nav__list">
              <li class="sf-nav__item">
                <a class="sf-nav__item-heading {!! $_s_->getOption('active') ? 'active' : '' !!}"
                   href="{!! $_router_->uri($_sitemap_->getNamespace(), $_s_->getOption('route') ?? $_s_->getName()) !!}">
                  @if($_s_->getOption('icon') !== null)
                    <i class="fa fa-{!! $_s_->getOption('icon') !!}"></i>
                  @endif
                  <span>{{ $_s_->getOption('title') }}</span>
                </a>
              </li>
            </ul>
          </div>
          @break
          @case(\Spiral\Keeper\Module\Sitemap::TYPE_SEGMENT)
          <div class="sf-navgroup">
            <div class="sf-navgroup__title">{{ $_s_->getOption('title') }}</div>
            @foreach($_s_ as $_in_ => $_is_)
              <ul class="sf-nav__list">
                <li class="sf-nav__item">
                  <div class="sf-nav__item-heading {!! $_is_->getOption('active') ? 'active' : '' !!}"
                       data-sf="nav-item-toggle"
                       aria-expanded="{!! $_is_->getOption('active') ? 'true' : 'false' !!}"
                       aria-controls="nav-item-{!! $_in_ !!}">
                    @if($_is_->getOption('icon') !== null )
                      <i class="fa fa-{!! $_is_->getOption('icon') !!}"></i>
                    @endif
                    <span>{{ $_is_->getOption('title') }}</span>
                  </div>
                  <div class="sf-nav__item-content" data-sf-nav="item-content"
                       id="nav-item-{!! $_in_ !!}">
                    <div class="sf-subnav">
                      @foreach($_is_ as $_i_)
                        <a class="sf-subnav__item {!! $_i_->getOption('active') ? 'active' : '' !!}"
                           href="{!! $_router_->uri($_sitemap_->getNamespace(), $_i_->getOption('route') ?? $_i_->getName()) !!}">
                          @if($_i_->getOption('icon') !== null )
                            <i class="fa fa-{!! $_i_->getOption('icon') !!}"></i>
                          @endif
                          <span>{{ $_i_->getOption('title') }}</span>
                        </a>
                      @endforeach
                    </div>
                  </div>
                </li>
              </ul>
            @endforeach
          </div>
          @break
          @default
          @if($_s_->hasOption('type'))
            <div class="sf-navgroup">
              <ul class="sf-nav__list">
                <li class="sf-nav__item">
                  <div class="sf-nav__item-heading {!! $_s_->getOption('active') ? 'active' : '' !!}"
                       data-sf="nav-item-toggle"
                       aria-expanded="{!! $_s_->getOption('active') ? 'true' : 'false' !!}"
                       aria-controls="nav-item-{!! $_n_ !!}">
                    @if($_s_->getOption('icon') !== null )
                      <i class="fa fa-{!! $_s_->getOption('icon') !!}"></i>
                    @endif
                    <span>{{ $_s_->getOption('title') }}</span>
                  </div>
                  <div class="sf-nav__item-content" data-sf-nav="item-content" id="nav-item-{!! $_n_ !!}">
                    <div class="sf-subnav">
                      @foreach($_s_ as $_i_)
                        <a class="sf-subnav__item {!! $_i_->getOption('active') ? 'active' : '' !!}"
                           href="{!! $_router_->uri($_sitemap_->getNamespace(), $_i_->getOption('route') ?? $_i_->getName()) !!}">
                          @if($_i_->getOption('icon') !== null )
                            <i class="fa fa-{!! $_i_->getOption('icon') !!}"></i>
                          @endif
                          <span>{{ $_i_->getOption('title') }}</span>
                        </a>
                      @endforeach
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          @endisset
          @break
        @endswitch
      @endforeach
      @php unset($_n_, $_s_, $_i_, $_is_, $_in_); @endphp
    </nav>
  </div>
</aside>
