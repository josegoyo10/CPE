(function($) {
	$.fn.dgrid = function(settings) {
        defaults = {
            url: false,
            params: {},
            colModel: [],
            onRowClick: false,
            onSubmit: false,
            onLoad: false,
            loadingStatus: false,
            records: false
        };
        settings = $.extend({}, defaults, settings);
		return this.each(function() {
            this.dgrid = {
                opts: settings,
                t: this,
                reload: function(p) {
                    if (p) this.opts = $.extend({}, this.opts, p);
                    cancel = false;
                    if (this.opts.onSubmit) {
                        cancel = !this.opts.onSubmit();
                    }
                    if (!cancel) {
                        qTBODY = $('tbody', this.t);
                        conf = this.opts;
                        if (conf.loadingStatus) {
                            $('#'+conf.loadingStatus).show();
                        }
                        $.post(this.opts.url, this.opts.params, function(data) {
                            qTBODY.html("");
                            var r = data;
                            if (conf.records) {
                                r = data[conf.records];
                            }
                            for (var i=0; i<r.length; i++) {
                                var eTR = document.createElement('tr');
                                var qTR = $(eTR);
                                eTR.data = r[i];
                                eTR.rowKey = i;
                                for (var j=0; j<r[i].length && j<conf.colModel.length; j++) {
                                    if (conf.colModel[j].hide != true) {
                                        var eTD = document.createElement('td');
                                        var qTD = $(eTD);
                                        var content = null;
                                        if (conf.colModel[j].align) qTD.css('text-align', conf.colModel[j].align);
                                        if (conf.colModel[j].render) {
                                            content = conf.colModel[j].render(r[i][j]);
                                        }
                                        else {
                                            content = r[i][j];
                                        }
                                        eTD.innerHTML = content == null ? "" : content;
                                        qTR.append(eTD);
                                    }
                                }
                                if (i%2==0) qTR.addClass('even'); else qTR.addClass('odd');
                                qTBODY.append(eTR);
                                qTR.click(function() {
                                    $('tr', qTBODY).removeClass('selected');
                                    $(this).addClass('selected');
                                    if (conf.onRowClick) {
                                        conf.onRowClick(this.data, this.rowKey);
                                    }
                                });
                            }
                            if (conf.loadingStatus) {
                                $('#'+conf.loadingStatus).hide();
                            }
                            if (conf.onLoad) {
                                conf.onLoad(data);
                            }
                        },
                        'json');
                    }
                }
            };
			$(this).addClass('dgrid');
		});
	};
	$.fn.dgridOptions = function(p) {
		return this.each(function() {
            if (this.dgrid) this.dgrid.opts = $.extend({}, this.dgrid.opts, p);
		});
	};
	$.fn.dgridLoad = function(p) {
		return this.each(function() {
            if (this.dgrid) this.dgrid.reload(p);
		});
	};
})(jQuery);