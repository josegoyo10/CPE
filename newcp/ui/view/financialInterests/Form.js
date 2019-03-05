/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
function Form(pCustomerId) {
    jQuery(document).ready(function() {

        // =====================================================================
        // Products Grid
        // =====================================================================
        jQuery('#productsGrid').dgrid({
            url: '?q=/financial/interests/products',
            records: 'records',
            colModel : [
                {name: 'id', hide: true},
                {name: 'barcode', render: function(barcode) {return new Array(14 - barcode.length).join("0") + barcode;} },
                {name: 'description' },
                {name: 'quantity', align: 'right'},
                {name: 'price', align: 'right'},
                {name: 'subtotal', align: 'right'},
                {name: 'editablePrice', hide: true}
            ],
            onLoad: function(data) {
                $('#quotationTotal').html(data.total);
                document.selectedProductRow = null;
                document.selectedProductRowKey = null;
                document.total = data.rawTotal;
                $('#gridForm').each(function() { this.reset(); this.barcode.value = ''; this.rowKey.value = ''; });
                calculateAll(false);
            },
            onRowClick: function(row, key) {
                document.selectedProductRow = row;
                document.selectedProductRowKey = key;
            }
        }).dgridLoad();

        // =====================================================================
        // Product Form
        // =====================================================================
        jQuery("#code").keyup(function(e) {
            if (e.keyCode == 13) {
                jQuery.loadProduct();
            }
        });
        jQuery("#quantity").keyup(function(e) {
            if (e.keyCode == 13) {
                jQuery("#addProduct").click();
            }
        });

        // Request product data using #code
        jQuery.loadProduct = function(codeType) {
            if (!codeType) codeType = 'UPC';
            $.getJSON('?q=/product/select', {type: codeType, code: $('#code').val()}, function(r) {
                if (r.success) {
                    jQuery.fillProductForm(r.items[0]);
                }
                else {
                    alert('Producto no existe.')
                }
            });
        };

        // Fill form fields
        jQuery.fillProductForm = function(item) {
            $('#barcode').val(item.barcode);
            $('#description').val(item.description);
            $('#price').val(item.price);
            $('#price').attr('readonly', item.editablePrice == 0);
            if (item.quantity) {
                $('#quantity').val(item.quantity);
            }
            else {
                $('#quantity').val(1);
            }
            $('#subtotal').val(item.price);
            $('#quantity').focus().select();
            $('#quantity,#price').change(function() {
                $('#quantity').val(parseFloat($('#quantity').val()));
                $('#subtotal').val(parseFloat($('#quantity').val()) * parseFloat($('#price').val()));
            });
            if ((typeof item.rowKey) == 'undefined') {
                $('#rowKey').val("");
            }
            else {
                $('#rowKey').val(item.rowKey);
            }
        };

        // Agregar producto
        jQuery('#addProduct').click(function() {
            if ($('#barcode').val()) {
                var data = $("#gridForm").serializeArray();
                $.post("?q=/financial/interests/addProduct",
                data, function(r) {
                    if (r.success) {
                        $('#productsGrid').dgridLoad();
                    }
                    else if (r.message) {
                        alert(r.message);
                    }
                }, 'json');
            }
        });

        // Eliminar producto
        jQuery('#btnDeleteProduct').click(function(e) {
            if ((typeof document.selectedProductRowKey) != 'undefined') {
                if (confirm("Desea eliminar el producto seleccionado?")) {
                    var data = { rowKey: document.selectedProductRowKey };
                    $.post("?q=/financial/interests/removeProduct",
                    data, function(r) {
                        if (r.success) {
                            $('#productsGrid').dgridLoad();
                        }
                    }, 'json');
                }
            }
            e.preventDefault();
        });

        // Eliminar todos los productos
        jQuery('#btnDeleteAllProducts').click(function(e) {
            if (confirm("Desea eliminar todos los productos?")) {
                var data = { all: true };
                $.post("?q=/financial/interests/removeProduct",
                data, function(r) {
                    if (r.success) {
                        $('#productsGrid').dgridLoad();
                    }
                }, 'json');
            }
            e.preventDefault();
        });

        // Editar producto
        jQuery('#btnEditProduct').click(function(e) {
            e.preventDefault();
            if (document.selectedProductRow) {
                var r = document.selectedProductRow;
                $('#code').val('');
                jQuery.fillProductForm({
                    barcode: r[1],
                    description: r[2],
                    quantity: r[3],
                    price: r[4],
                    editablePrice: r[6],
                    rowKey: document.selectedProductRowKey});
            }
        });

        // Calcular intereses
        jQuery('#btnCalculateFinanciation').click(function() {
            calculateAll(true);
        });

        // Imprimir comprobante
        jQuery('#btnPrint').click(function(e) {
            var params = "&total=" + parseFloat(document.total) +
                "&initialQuota=" + parseFloat($('#initialQuota').val()) +
                "&numQuotas=" + parseInt($('#numChecks').val());
            window.open('?q=/financial/interests/printedForm' + params, 'printedForm', 'width=800,height=600');
        });


        jQuery('#initialQuota,#numChecks').keydown(function(e) {
            if (e.keyCode == 13) {
                calculateAll(true);
            }
            else {
                $('#btnPrint').hide();
                $('#financedValue').val('');
                $('#financeRate').val('');
                $('#checkValue').val('');
            }
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

    });
}

function newCustomerForm(pCustomerId) {
    if (pCustomerId) {
        var w = window.open('?q=/customer/edit&id='+pCustomerId, 'customerFormWindow', 'width=740,height=500');
        w.jowner = {
            onClose: function(newId) {
                w.close();
                if (newId) {
                    $('#selectCustomerForm').submit();
                }
            }
        };
        w.focus();
    }
}

function calculateAll(interactive) {
    var data = {
        initialQuota: parseFloat($('#initialQuota').val()),
        numQuotas: parseInt($('#numChecks').val()),
        total: parseFloat(document.total)
    };
    if (data.initialQuota < 0.0 || data.numQuotas <= 0 || isNaN(data.initialQuota) || isNaN(data.numQuotas)) {
        $('#financedValue').val('');
        $('#financeRate').val('');
        $('#checkValue').val('');
        if (interactive) {
            alert('El valor de la cuota inicial y el numero de cheques son requeridos.');
        }
    }
    else {
        $.getJSON('?q=/financial/basicFinanciation', data, function(r) {
            if (r.success && r.quota > 0) {
                $('#financedValue').val(r.financedValue);
                $('#financeRate').val((r.rate*100) + '%');
                $('#checkValue').val(r.quota);
                $('#btnPrint').show();
            }
            else {
                $('#btnPrint').hide();
                $('#financedValue').val('');
                $('#financeRate').val('');
                $('#checkValue').val('');
                if (r.messages) {
                    var message = "";
                    for (var i=0; i<r.messages.length; i++) {
                        message = message + r.messages[i] + "\n";
                    }
                    alert(message);
                }
            }
        });
    }
}