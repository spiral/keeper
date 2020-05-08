<extends path="keeper:layout/common"/>
<use:bundle path="keeper:bundle"/>

<define:main>
  <ul data-sf="tabnav" class="sf-tabnav nav nav-tabs" role="tablist">
    <stack:collect name="tab-headers" level="20"/>
  </ul>

  <stack:collect name="tab-body" level="20"/>
</define:main>
