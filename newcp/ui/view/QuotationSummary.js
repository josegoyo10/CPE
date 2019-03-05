/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
function QuotationSummary(pCustomerId, pQuotationId) {
    jQuery(document).ready(function() {
        jQuery('#addresses').change(function() {
            var id = $(this).val();
            $.getJSON('?q=/customer/address', {addressId: id}, function(r) {
                $('#addressName').html(r.name);
                $('#addressAddress').html(r.address);
                $('#addressObservation').html(r.observation);
                $('#locationId').val(r.locationId);
                if (r.locationDescriptions) {
                    $('#addressDepartment').html(r.locationDescriptions.department);
                    $('#addressCity').html(r.locationDescriptions.city);
                    $('#addressNeighbourhood').html(
                        r.locationDescriptions.neighbourhood + ' - ' +
                        r.locationDescriptions.locality);
                }
                else {
                    $('#addressDepartment').html("");
                    $('#addressCity').html("");
                    $('#addressNeighbourhood').html("");
                }
            });
        });
        jQuery('#freightsButton').click(function(e) {
            var data = {quotationId: pQuotationId};
            data.locationId = $('#locationId').val();
            data.addressId = $('#addresses').val();
            e.preventDefault();
            $.post('?q=/quotation/freight/gen', data, function(r) {
                if (r.success) {
                    var loc = window.location.href.substr(0, window.location.href.length);
                    window.location.replace(loc + "&rand=" + Math.random());
                }
                else {
                    alert('Error al intentar calcular los fletes');
                }
            }, 'json');
        });
        jQuery('#printQuotationButton').click(function(e) {
            var w = window.open('?q=/quotation/print&id='+pQuotationId, 'printWindow1', 
                'width=900,height=600,scrollbars=yes');
            w.focus();
            e.preventDefault();
        });
        jQuery('#printContractButton').click(function(e) {
            var w = window.open('?q=/quotation/contract/print&id='+pQuotationId, 'printWindow2',
                'width=900,height=600,scrollbars=yes');
            w.focus();
            e.preventDefault();
        });
        jQuery('#printFiniquitoButton').click(function(e) {
            var w = window.open('?q=/quotation/finiquito/print&id='+pQuotationId, 'printWindow2',
                'width=900,height=600,scrollbars=yes');
            w.focus();
            e.preventDefault();
        });
        jQuery('#editCustomerButton').click(function(e) {
            var w = window.open('?q=/customer/edit&id='+pCustomerId, 'customerFormWindow', 'width=740,height=500');
            w.jowner = {
                onClose: function() {
                    w.close();
                }
            };
            w.focus();
            e.preventDefault();
        });
    })
}
