<extends:keeper:layout.base lang="@{locale}" baseTitle="${title|Control Panel} - Keeper"/>
<use:bundle path="keeper:bundle"/>

<stack:push name="init">
  <use:element path="keeper:layout/sitemap" as="sitemap:init"/>
  <sitemap:init/>
</stack:push>

<stack:push name="scripts">
  @if(env('LIFERELOAD'))
    <script src="http://localhost:35731/livereload.js"></script>
  @endif
</stack:push>

<define:body>
  <keeper:sidebar activeRoute="${activeRoute}"/>

  <main class="sf-main">
    <keeper:header/>

    <define:heading>
      <div class="sf-heading">
        <keeper:breadcrumbs activeRoute="${activeRoute}"/>
        @if(!empty($_ln_))
          <h1>
            <block:title>{{$_ln_->getOption('title')}}</block:title>
          </h1>
        @endif
        <div>
          <block:actions/>
          <stack:collect name="actions" level="20"/>
        </div>
      </div>
    </define:heading>

    <block:main/>
  </main>
</define:body>
