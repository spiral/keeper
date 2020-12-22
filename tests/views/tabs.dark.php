<extends:keeper:layout.tabs/>
<use:bundle path="keeper:bundle"/>

<?php
/**
 * @var bool $condition
 */
?>

<ui:tab id="first" title="[[1st]]" active="true">
    First tab
</ui:tab>

<ui:tab id="second" title="[[2nd]]" condition="{{ $condition }}">
    Second tab
</ui:tab>
