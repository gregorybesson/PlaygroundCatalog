jQuery(document).ready(function() {
	var treeLeft = 1;
	var data = {};
	var categoryNestedSetLeftRightParent = function(nodes,level,parent,root) {
		for ( var key in nodes ) {
			var node = nodes[key];
			var id = node.id;
			data['node['+id+'][lft]'] = treeLeft++;
			if ( node.children ) {
				categoryNestedSetLeftRightParent(node.children,level+1,node.id,root?root:id);
			}
			data['node['+id+'][rgt]'] = treeLeft++;
			if ( parent ) {
				data['node['+id+'][parent]'] = parent;
			}
			data['node['+id+'][root]'] = root?root:id;
			data['node['+id+'][level]'] = level;
		}
	};
	var categorySort = jQuery('#category-list').sortable({
		group: 'nested',
		onDrop: function (item, container, _super) {
			_super(item);
			treeLeft = 1;
			data = {};
			var tree = categorySort.sortable('serialize').get();
			categoryNestedSetLeftRightParent(tree,0);
			jQuery.post(categorySortTreeUrl,data, function(result) {
				alert(result.message)
			},'json');
		}
	});
});