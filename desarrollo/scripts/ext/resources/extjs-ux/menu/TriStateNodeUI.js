Ext.tree.TriStateNodeUI = Ext.extend(Ext.tree.TreeNodeUI, {
	onCheckChange :function(){
		Ext.tree.TriStateNodeUI.superclass.onCheckChange.apply(this, arguments);
		var p;
		if((p = this.node.parentNode) && p.getUI().updateParent) {
			p.getUI().updateParent();
		}
	},
	toggleCheck :function(){
		var checked = Ext.tree.TriStateNodeUI.superclass.toggleCheck.apply(this, arguments);
		this.updateChild(checked);
		return checked;
	},
	renderElements :function(n, a, targetNode, bulkRender){
		Ext.tree.TriStateNodeUI.superclass.renderElements.apply(this, arguments);
		this.updateChild(this.node.attributes.checked);
	},
	updateParent :function(){
		var checked;
		this.node.eachChild(function(n){
			if(checked === undefined){
				checked = n.attributes.checked;
			}else if (checked !== n.attributes.checked) {
				checked = this.grayedValue;
				return false;
			}
		}, this);
		this.toggleCheck(checked);
	},
	updateChild:function(checked){
		if(this.checkbox){
			if(checked === true){
				Ext.fly(this.ctNode).replaceClass('x-tree-branch-unchecked', 'x-tree-branch-checked');
			} else if(checked === false){
				Ext.fly(this.ctNode).replaceClass('x-tree-branch-checked', 'x-tree-branch-unchecked');
			} else {
				Ext.fly(this.ctNode).removeClass(['x-tree-branch-checked', 'x-tree-branch-unchecked']);
			}
		}
	}
});
