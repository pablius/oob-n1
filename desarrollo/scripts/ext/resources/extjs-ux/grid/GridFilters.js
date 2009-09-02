/**
 * Ext.ux.grid.GridFilters v0.2.8
 **/

Ext.namespace("Ext.ux.grid");
Ext.ux.grid.GridFilters = function(config){		
	this.filters = new Ext.util.MixedCollection();
	this.filters.getKey = function(o){return o ? o.dataIndex : null};
	for(var i=0, len=config.filters.length; i<len; i++)	
		this.addFilter(config.filters[i]);
	
	this.deferredUpdate = new Ext.util.DelayedTask(this.reload, this);
	
	delete config.filters;
	Ext.apply(this, config);
};
		   	
Ext.extend(Ext.ux.grid.GridFilters, Ext.util.Observable, {
	/**
	 * @cfg {Integer} updateBuffer
	 * Number of milisecond to defer store updates since the last filter change.
	 */
	updateBuffer: 500,
	/**
	 * @cfg {String} paramPrefix
	 * The url parameter prefix for the filters.
	 */
	paramPrefix: 'filter',
	/**
	 * @cfg {String} fitlerCls
	 * The css class to be applied to column headers that active filters. Defaults to 'ux-filterd-column'
	 */
	filterCls: 'ux-filtered-column',
	/**
	 * @cfg {Boolean} local
	 * True to use Ext.data.Store filter functions instead of server side filtering.
	 */
	local: false,
	/**
	 * @cfg {Boolean} autoReload
	 * True to automagicly reload the datasource when a filter change happens.
	 */
	autoReload: true,
	/**
	 * @cfg {String} stateId
	 * Name of the Ext.data.Store value to be used to store state information.
	 */
	stateId: undefined,
	/**
	 * @cfg {Boolean} showMenu
	 * True to show the filter menus
	 */
	showMenu: true,

	menuFilterText: 'Filtros',
	
	tabId : 0 ,

	init: function(grid){					
		if(grid instanceof Ext.grid.GridPanel){
			this.grid  = grid;		  
			
			this.store = this.grid.getStore();
			if(this.local){
				this.store.on('load', function(store){				
					store.filterBy(this.getRecordFilter());
				}, this);
			} else {
			  this.store.on('beforeload', this.onBeforeLoad, this);
			}
			  
			this.grid.filters = this;
			 
			this.grid.addEvents({"filterupdate": true});	
						
			//applyfilters event
			grid.addListener( 'applyfilters' , function( tab_id ){				
				this.tabId = tab_id;					
			
   			 var cnx = new Ext.data.Connection();
			 cnx.request( {url: '/admin/getfilters',
						method: 'POST',
						 scope: this ,
						params: 'tabid=' + tab_id ,
					   success: function(responseObject){								
								json = Ext.decode(responseObject.responseText);																											
								Ext.each( json, function( filter ){		
							
							if(filter.type == 'list'){										
										this.getFilter(filter.field).setValue( filter.value.split(',') );																	
							}else{	
								switch(filter.comparison){
								case 'eq':									
									if(filter.type == 'date'){																			
										this.getFilter(filter.field).setValue({ 'on': new Date(filter.value) });										
									}else{
										this.getFilter(filter.field).setValue({ 'eq': filter.value });
									}
								break;
								case 'gt':
									if(filter.type == 'date'){
										this.getFilter(filter.field).setValue({ 'after':  new Date(filter.value) });										
									}else{
										this.getFilter(filter.field).setValue({ 'gt': filter.value });
									}	
								break;
								case 'lt':
									if(filter.type == 'date'){										
										this.getFilter(filter.field).setValue({ 'before':  new Date(filter.value) });
									}else{
										this.getFilter(filter.field).setValue({ 'lt': filter.value });
									}	
								break;
									
								}
							}	
									this.getFilter(filter.field).setActive(true);
									this.getFilter(filter.field).fireUpdate();									
								},this);								
								 							   
					     }} );		
					
					
					
			}, this );
			//end event
			
			grid.on("render", this.onRender, this);				  
			grid.on("beforestaterestore", this.applyState, this);
			grid.on("beforestatesave", this.saveState, this);
			
											  
		} else if(grid instanceof Ext.PagingToolbar){
		  this.toolbar = grid;
		}
	},
	
	
		
	/** private **/
	applyState: function(grid, state){
		this.suspendStateStore = true;
		this.clearFilters();
		if(state.filters)
			for(var key in state.filters){
				var filter = this.filters.get(key);
				if(filter){
					filter.setValue(state.filters[key]);
					filter.setActive(true);
				}
			}
			
		this.deferredUpdate.cancel();
		if(this.local)			
			this.reload();
			
		this.suspendStateStore = false;
	},
	
	/** private **/
	saveState: function(grid, state){	
		var filters = {};
		this.filters.each(function(filter){
			if(filter.active)				
				filters[filter.dataIndex] = filter.getValue();
		});
		return state.filters = filters;
	},
	
	/** private **/
	onRender: function(){			
		var hmenu;
		
		if(this.showMenu){
			hmenu = this.grid.getView().hmenu;
			
			this.sep  = hmenu.addSeparator();
			this.menu = hmenu.add(new Ext.menu.CheckItem({
					text: this.menuFilterText,
					menu: new Ext.menu.Menu()
				}));
			this.menu.on('checkchange', this.onCheckChange, this);
			this.menu.on('beforecheckchange', this.onBeforeCheck, this);
				
			hmenu.on('beforeshow', this.onMenu, this);
		}
		
		this.grid.getView().on("refresh", this.onRefresh, this);
		this.updateColumnHeadings(this.grid.getView());
	},
	
	/** private **/
	onMenu: function(filterMenu){		
		var filter = this.getMenuFilter();
		if(filter){
			this.menu.menu = filter.menu;
			this.menu.setChecked(filter.active, false);
		}
		
		this.menu.setVisible(filter !== undefined);
		this.sep.setVisible(filter !== undefined);
	},
	
	/** private **/
	onCheckChange: function(item, value){		
		this.getMenuFilter().setActive(value);
	},
	
	/** private **/
	onBeforeCheck: function(check, value){
		return !value || this.getMenuFilter().isActivatable();
	},
	
	/** private **/
	onStateChange: function(event, filter){
    if(event == "serialize") return;
    
		if(filter == this.getMenuFilter())
			this.menu.setChecked(filter.active, false);
			
		if(this.autoReload || this.local)
			this.deferredUpdate.delay(this.updateBuffer);
		
		var view = this.grid.getView();
		this.updateColumnHeadings(view);
			
		this.grid.saveState();
			
		this.grid.fireEvent('filterupdate', this, filter);
	},
	
	/** private **/
	onBeforeLoad: function(store, options){			
		options.params = options.params || {};
		this.cleanParams(options.params);		
		var params = this.buildQuery(this.getFilterData());
		Ext.apply(options.params, {data:params});	 	
	},
	
	/** private **/
	onRefresh: function(view){
		this.updateColumnHeadings(view);
	},
	
	/** private **/
	getMenuFilter: function(){
		var view = this.grid.getView();
		if(!view || view.hdCtxIndex === undefined)
			return null;
		
		return this.filters.get(
			view.cm.config[view.hdCtxIndex].dataIndex);
	},
	
	/** private **/
	updateColumnHeadings: function(view){
		if(!view || !view.mainHd) return;
		
		var hds = view.mainHd.select('td').removeClass(this.filterCls);
		for(var i=0, len=view.cm.config.length; i<len; i++){
			var filter = this.getFilter(view.cm.config[i].dataIndex);
			if(filter && filter.active)
				hds.item(i).addClass(this.filterCls);
		}
	},
	
	/** private **/
	reload: function(){			
		//si el filtrado es local, filtra el store con javascript directamente
		if(this.local){
			this.grid.store.clearFilter(true);
			this.grid.store.filterBy(this.getRecordFilter());
		} 
		else //si no se manda los datos al server para traerlos filtrados
		{
			
			this.deferredUpdate.cancel();
			var store = this.grid.store;
			if(this.toolbar) {
				var start = this.toolbar.paramNames.start;
				if(store.lastOptions && store.lastOptions.params && store.lastOptions.params[start]) {
					store.lastOptions.params[start] = 0;
        
			}
			}
		
			var params = this.buildQuery(this.getFilterData());	
			var pag = 0;				
			var limit = store.lastOptions.params['limit'];			
			store.load({params:{start:pag, limit:limit,data:params}});
		
		
		}
	},
	
	/**
	 * Method factory that generates a record validator for the filters active at the time
	 * of invokation.
	 * 
	 * @private
	 */
	getRecordFilter: function(){
		var f = [];
		this.filters.each(function(filter){
			if(filter.active) f.push(filter);
		});
		
		var len = f.length;
		return function(record){
			for(var i=0; i<len; i++)
				if(!f[i].validateRecord(record))
					return false;
				
			return true;
		};
	},
	
	/**
	 * Metodo para agregar filtros a una columna
	 * 
	 * @param {Object/Ext.ux.grid.filter.Filter} config A filter configuration or a filter object.
	 * 
	 * @return {Ext.ux.grid.filter.Filter} The existing or newly created filter object.
	 */
	addFilter: function(config){
		//si sale en el menu lo agrega
		var filter = config.menu ? config : 
				new (this.getFilterClass(config.type))(config);
		this.filters.add(filter);
		
		Ext.util.Observable.capture(filter, this.onStateChange, this);
		return filter;
	},
	
	/**
	 * Retorn un filtro,hay que pasarle por parametro el dataIndex ej 'id'.
	 * 
	 * @param {String} dataIndex The dataIndex of the desired filter object.
	 * 
	 * @return {Ext.ux.grid.filter.Filter}
	 */
	getFilter: function(dataIndex){
		return this.filters.get(dataIndex);
	},

	/**
	 * Turns all filters off. This does not clear the configuration information.
	 */
	clearFilters: function(){
		this.filters.each(function(filter){
			filter.setActive(false);
		});
	},

	/** private **/
	getFilterData: function(){					
		var filters = [];		
		this.filters.each(function(f){			
			if(f.active){
				var d = [].concat(f.serialize());
				for(var i=0, len=d.length; i<len; i++){				
					filters.push({
						field: f.dataIndex,
						data: d[i]
					});
				}	
			}
		});
		
		return filters;
	},
	
	/**
	devuelve un json con todos los datos de los filtros
	 */
	buildQuery: function(filters){		
		var p = new Array(),i,Key;		
		for(var i=0, len=filters.length; i<len; i++){
			var f = filters[i];
			var valores = new Array();													
			valores.push('"field":"'+f.field+'"');									
			for( var key in f.data){								
				valores.push('"'+key+'":"'+f.data[key]+'"');							
			}				
			p.push("{"+valores.toString()+"}");	
		}
		
		return '{ "filters" : [' +p.toString() +'] , "tabid" : "' + this.tabId + '"}';		
	},	
	
	/**
	 * Removes filter related query parameters from the provided object.
	 * 
	 * @param {Object} p Query parameters that may contain filter related fields.
	 */
	cleanParams: function(p){
		var regex = new RegExp("^" + this.paramPrefix + "\[[0-9]+\]");
		for(var key in p)
			if(regex.test(key))
				delete p[key];
	},
	
	/**
	 * Function for locating filter classes, overwrite this with your favorite
	 * loader to provide dynamic filter loading.
	 * 
	 * @param {String} type The type of filter to load.
	 * 
	 * @return {Class}
	 */
	getFilterClass: function(type){
		return Ext.ux.grid.filter[type.substr(0, 1).toUpperCase() + type.substr(1) + 'Filter'];
	}
});