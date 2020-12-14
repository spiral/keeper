<use:bundle path="keeper:bundle"/>

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
         * @var Spiral\Keeper\Module\Sitemap      $_sitemap_
         * @var Spiral\Keeper\Module\Sitemap\Node $_node_
         * @var Spiral\Keeper\Module\Sitemap\Node $_child_
         */
        ?>
        <nav class="sf-nav" data-sf="nav">
            @foreach($_sitemap_ as $_name_ => $_node_)
                @switch($_node_->getOption('type'))
                    @case(\Spiral\Keeper\Module\Sitemap::TYPE_LINK)
                    <div class="sf-navgroup">
                        <ul class="sf-nav__list">
                            <li class="sf-nav__item">
                                <keeper:sidebar:a class="sf-nav__item-heading" node="{{ $_node_ }}">
                                    <keeper:sidebar:icon node="{{ $_node_ }}"/>
                                    <span>{{ $_node_->getOption('title') }}</span>
                                </keeper:sidebar:a>
                            </li>
                        </ul>
                    </div>
                    @break

                    @case(\Spiral\Keeper\Module\Sitemap::TYPE_SEGMENT)
                    <div class="sf-navgroup">
                        <div class="sf-navgroup__title">{{ $_node_->getOption('title') }}</div>
                        @foreach($_node_ as $_childName_ => $_child_)
                            @switch($_child_->getOption('type'))
                                @case(\Spiral\Keeper\Module\Sitemap::TYPE_GROUP)
                                <keeper:sidebar:group name="{!! $_childName_ !!}" node="{{ $_child_ }}"/>
                                @break

                                @case(\Spiral\Keeper\Module\Sitemap::TYPE_LINK)
                                <keeper:sidebar:link node="{{ $_child_ }}"/>
                                @break
                            @endswitch
                        @endforeach
                    </div>
                    @break

                    @case(\Spiral\Keeper\Module\Sitemap::TYPE_GROUP)
                    <div class="sf-navgroup">
                        <keeper:sidebar:group name="{!! $_name_ !!}" node="{{ $_node_ }}"/>
                    </div>
                    @break
                @endswitch
            @endforeach
            @php unset($_name_, $_node_, $_child_, $_childName_); @endphp
        </nav>
    </div>
</aside>
