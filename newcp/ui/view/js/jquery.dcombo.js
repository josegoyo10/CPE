(function($) {
	$.fn.dcomboCascade = function(p, opt) {
		if (!this.dcomboReload) {		
			var parent = document.getElementById(p);
			if (!parent.cbcascades) parent.cbcascades = new Array();
			parent.cbcascades[parent.cbcascades.length] = this;
			this.opt = $.extend({template: function(str) {return '<option value="'+str+'">'+str+'</option>'}}, opt);
			this.dcomboReload = function(value) {
				var self = this;
				$.getJSON(this.opt.url, {val: value}, function(r) {
					var content = "";
					for (var i=0; i<r.length; i++) {
						content += self.opt.template(r[i]);
					}
					self.html(content);
					var parent = self.get(0);
					if (parent.cbcascades) {
						for(var i=0; i<parent.cbcascades.length; i++) {
							parent.cbcascades[i].dcomboReload(self.val());
						}
					}
				});
			}
			return this.each(function() {
				$(parent).change(function() {
					for(var i=0; i<this.cbcascades.length; i++) {
						this.cbcascades[i].dcomboReload($(this).val());
					}
				});
			});
		}
	};
})(jQuery);