<?php
global $ari;
$ari->popup = 1; // no mostrar el main_frame 
?>

<html>
<head>
	<script type="text/javascript" src="ext/adapter/ext/ext-base.js"></script>
	<script type="text/javascript" src="ext/ext-all.js"></script>
	
	<script type="text/javascript" src="ux/menu/EditableItem.js"></script>
	<script type="text/javascript" src="ux/menu/RangeMenu.js"></script>
	
	<script type="text/javascript" src="ux/grid/GridFilters.js"></script>
	<script type="text/javascript" src="ux/grid/filter/Filter.js"></script>
	<script type="text/javascript" src="ux/grid/filter/StringFilter.js"></script>
	<script type="text/javascript" src="ux/grid/filter/DateFilter.js"></script>
	<script type="text/javascript" src="ux/grid/filter/ListFilter.js"></script>
	<script type="text/javascript" src="ux/grid/filter/NumericFilter.js"></script>
	<script type="text/javascript" src="ux/grid/filter/BooleanFilter.js"></script>

  <script type="text/javascript" src="JsonResponseReader.js"></script>
	
	<script type="text/javascript">
	Ext.onReady(function(){
		Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
			
		var ds = new Ext.data.GroupingStore({
			proxy: new Ext.data.HttpProxy({
				url:'action.php'
			}),
			
			reader: new Ext.data.JsonReader({
				id:   'id',
				totalProperty: 'data.total',
				root: 'data.results'
			}, Ext.data.Record.create([
				{name:'id'}, 
				{name:'name'}, 
				{name:'price'}, 
				{name:'dateAdded'}, 
				{name:'visible'}, 
				{name:'size'}])),
			
			groupField: 'size',
			sortInfo: {field: 'name', direction: 'ASC'},
			remoteSort: true
				});
				
		var filters = new Ext.ux.grid.GridFilters({filters:[
				{type: 'numeric',  dataIndex: 'id'},
				{type: 'string',  dataIndex: 'name'},
				{type: 'numeric', dataIndex: 'price'},
				{type: 'date',  dataIndex: 'dateAdded'},
				{
					type: 'list',  
					dataIndex: 'size', 
					options: ['extra small', 'small', 'medium', 'large', 'extra large'],
					phpMode: true},
				{type: 'boolean', dataIndex: 'visible'}
		]});
				
		var cm = new Ext.grid.ColumnModel([
				{
                  dataIndex: 'id',
                  header: 'Id'
                }, {
                  dataIndex: 'name',
                  header: 'Name'
                }, {
                  dataIndex: 'price',
                  header: 'Price'
                }, {
                  dataIndex: 'dateAdded',
                  header: 'Date Added'
                }, {
                  dataIndex: 'size',
                  header: 'Size'
                }, {
                  dataIndex: 'visible',
                  header: 'Visible'
                }]);
				cm.defaultSortable = true;
				
		var grid = new Ext.grid.GridPanel({
			id: 'example',
			ds: ds,
			cm: cm,
			enableColLock: false,
			loadMask: true,
			view: new Ext.grid.GroupingView(),
			plugins: filters,
			height:400,
			width:700,
					
			el: 'grid-example',
					
			bbar: new Ext.PagingToolbar({
            store: ds,
            pageSize: 15,
            plugins: filters
		})
	});
	grid.render();
				
	ds.load({params:{start: 0, limit: 15}});
});
	</script>
	
	<style type="text/css" title="currentStyle" media="screen">
		@import "ext/resources/css/ext-all.css";
		@import "resources/style.css";
	</style>
</head>

<body>
	<h1>ExtJS Filter Grid Example</h1>
	<div id="grid-example" style="margin: 10px;"></div>
	<a href="source.zip">Source</a>
</body>
</html>