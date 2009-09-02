Ext.namespace('Ext.ux.plugins');
Ext.ux.plugins.FitToParent = function() {
 return {
  init: function(control) {    
	
	  control.on('render',function(){		 
		  this.var_parent = Ext.get(control.getEl().dom.parentNode);	   
		  control.monitorResize = true;
		  control.doLayout = control.doLayout.createInterceptor(function(){
			size = this.var_parent.getViewSize();
			this.setSize(size.width,size.height);		  		
		  });   	  	  
	  });  
	  
  }
  
 }
}




