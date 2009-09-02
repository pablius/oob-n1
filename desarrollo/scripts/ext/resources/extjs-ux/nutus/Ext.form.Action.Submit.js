Ext.form.Action.Submit.prototype.success = function(response){
		
	    var result = this.processResponse(response);
        if(result === true || result.success){			
			this.form.afterAction(this, true);
					
								
				if( this.options.grid_reload_id &&  this.options.grid_reload_id != '' ){
				
					var grid =  Ext.getCmp(this.options.grid_reload_id);
					if(grid){
						var tb = grid.getBottomToolbar();					
						tb.doLoad(tb.cursor);					
					}								
				
				}//end if	
				
				if( this.options.success_msg != '' ){					
				
					var title = this.options.success_msg_title;					
					if( title == '' || !title ){
						title = 'Emporika';
					}
					
				}

				var objthis = this;
				
				var onsuccessfunction = function(){
				
						Ext.callback(objthis.options.success_after_click, objthis.options.scope, [objthis, objthis]);						
												
						if( objthis.options.new_tab_dir && objthis.options.new_tab_dir != ''  ){
						
						var tab_title = objthis.options.new_tab_title;
						if( !tab_title || tab_title == '' ){
							tab_title = 'no title';
						}
						
						var load_here = false;
						if( objthis.options.load_tab_here ){
							load_here = objthis.options.load_tab_here;
						}
						
						var params = false;
						if( objthis.options.new_tab_pass_response_params ){							
							 var obj_params = objthis.options.new_tab_pass_response_params;
							 
							 var resultado = Ext.decode(response.responseText);
							 var newvalue = '';		
							 var evaluar = '';		
							 for( var k in obj_params ){
								newvalue = '';
								evaluar = 'newvalue = resultado.' + obj_params[k] + ';';								 								
								eval(evaluar);												
								obj_params[k] = newvalue;
							 }
						
							params = Ext.urlEncode(obj_params);
							
						}
						
						if( objthis.options.new_tab_pass_params && objthis.options.new_tab_pass_params != '' ){						
							params+= Ext.urlEncode(objthis.options.new_tab_pass_params);
						}
						
						Ext.addTab( tab_title, objthis.options.new_tab_dir, !load_here,params );
						
						}				
				}
				
				if( this.options.success_msg && this.options.success_msg != '' ){				
					Ext.MessageBox.alert( title, this.options.success_msg, onsuccessfunction, this );				
				}else{
					onsuccessfunction();
				}
				
				
			           
            return;
        }
		
        if(result.errors){
            this.form.markInvalid(result.errors);
            this.failureType = Ext.form.Action.SERVER_INVALID;
        }
		
        this.form.afterAction(this, false);
			
} 