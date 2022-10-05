<use:bundle path="toolkit:bundle"/>

{{--layout elements--}}
<use:element path="keeper:layout/sidebar" as="keeper:sidebar"/>
<use:element path="keeper:layout/sidebar/group" as="keeper:sidebar:group"/>
<use:element path="keeper:layout/sidebar/link" as="keeper:sidebar:link"/>
<use:element path="keeper:layout/sidebar/icon" as="keeper:sidebar:icon"/>
<use:element path="keeper:layout/sidebar/a" as="keeper:sidebar:a"/>
<use:element path="keeper:layout/header" as="keeper:header"/>
<use:element path="keeper:layout/breadcrumbs" as="keeper:breadcrumbs"/>

{{--layout partials--}}
<use:element path="keeper:layout/panel" as="ui:panel"/>
<use:element path="keeper:layout/tabs/tab" as="ui:tab"/>
<use:element path="keeper:layout/action" as="ui:action"/>

{{--lists--}}
<use:element path="keeper:layout/list/dl" as="ui:dl"/>
<use:element path="keeper:layout/list/dt" as="ui:dt"/>
<use:element path="keeper:layout/list/dd" as="ui:dd"/>
<use:element path="keeper:layout/list/dl-item" as="dl:item"/>

{{--Grids and columns--}}
<use:element path="keeper:layout/grid/row" as="ui:row"/>
<use:element path="keeper:layout/grid/col1" as="ui:col.1"/>
<use:element path="keeper:layout/grid/col2" as="ui:col.2"/>
<use:element path="keeper:layout/grid/col3" as="ui:col.3"/>
<use:element path="keeper:layout/grid/col4" as="ui:col.4"/>
<use:element path="keeper:layout/grid/col5" as="ui:col.5"/>
<use:element path="keeper:layout/grid/col6" as="ui:col.6"/>
<use:element path="keeper:layout/grid/col7" as="ui:col.7"/>
<use:element path="keeper:layout/grid/col8" as="ui:col.8"/>
<use:element path="keeper:layout/grid/col9" as="ui:col.9"/>
<use:element path="keeper:layout/grid/col10" as="ui:col.10"/>
<use:element path="keeper:layout/grid/col11" as="ui:col.11"/>
<use:element path="keeper:layout/grid/col12" as="ui:col.12"/>

{{--UI components--}}
<use:element path="keeper:component/qrcode" as="ui:qrcode"/>
<use:element path="keeper:component/modal" as="ui:modal"/>
<use:element path="keeper:component/notification" as="ui:notification"/>
<use:element path="keeper:component/date" as="ui:date"/>
<use:element path="keeper:component/button" as="ui:button"/>

{{--dashboard elements--}}
<use:element path="keeper:dashboard/value-card" as="ui:value-card"/>

{{--data grids--}}
<use:element path="keeper:datagrid/wrapper" as="ui:grid"/>
<use:element path="keeper:datagrid/filter" as="grid:filter"/>
<use:element path="keeper:datagrid/refresh" as="grid:refresh"/>
<use:element path="keeper:datagrid/refresh" as="grid:refreshbutton"/>

{{--grid cells--}}
<use:element path="keeper:datagrid/cell/text" as="grid:cell"/>
<use:element path="keeper:datagrid/cell/text" as="grid:cell.text"/>
<use:element path="keeper:datagrid/cell/link" as="grid:cell.link"/>
<use:element path="keeper:datagrid/cell/date" as="grid:cell.date"/>
<use:element path="keeper:datagrid/cell/template" as="grid:cell.template"/>
<use:element path="keeper:datagrid/cell/render" as="grid:cell.render"/>

{{--grid actions--}}
<use:element path="keeper:datagrid/action/action" as="grid:action"/>
<use:element path="keeper:datagrid/action/bulkaction" as="grid:bulkaction"/>
<use:element path="keeper:datagrid/action/link" as="grid:action.link"/>
<use:element path="keeper:datagrid/action/delete" as="grid:action.delete"/>

{{--action buttons--}}
<use:element path="keeper:action/button" as="action:button"/>
<use:element path="keeper:action/invoke" as="action:invoke"/>
<use:element path="keeper:action/delete" as="action:delete"/>

{{--notifications--}}
<use:element path="keeper:notifications/drawer" as="notifications:drawer"/>
<use:element path="keeper:notifications/toggle" as="notifications:toggle"/>
