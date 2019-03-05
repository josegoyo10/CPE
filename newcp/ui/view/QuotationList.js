/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
function QuotationList(pCustomerId) {
    jQuery(document).ready(function() {
        jQuery('#grid').dgrid({
			url: '?q=/customer/oslist/data',
			colModel : [
				{name : 'osId',       align: 'center'},
				{name : 'projectId'                  },
				{name : 'osDetail'                   },
				{name : 'osDate',     align: 'right' },
				{name : 'osStatus',   align: 'center'},
				{name : 'Actions',    align: 'center'}
				],
            params: {customerId: pCustomerId}
        });
        jQuery('#projectFilter').change(function() {
            var dt = $('#filterForm').serializeArray();
            $('#grid').dgridLoad({params:dt});
            return true;
        });
        jQuery('#statusFilter').change(function() {
            var dt = $('#filterForm').serializeArray();
            $('#grid').dgridLoad({params:dt});
            return true;
        });
        $('#grid').dgridLoad();
        $('#editCustomerButton').click(function(e) {
            var w = window.open('?q=/customer/edit&id='+pCustomerId, 'customerFormWindow', 'width=740,height=500');
            w.jowner = {
                onClose: function() {
                    w.close();
                }
            };
            w.focus();
            e.preventDefault();
        });
    });
}