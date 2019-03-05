/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
function QuotationForm(pCustomerId) {
    jQuery(document).ready(function() {

        // =====================================================================
        // Projects
        // =====================================================================
        $('#btnAddProject').click(function() {
            $.post('?q=/customer/projects/create',
            {customerId: pCustomerId,
                projectName: $('#newProjectName').val(), ajax: true}, function(r) {
                if (r.success) {
                    $.getJSON('?q=/customer/projects/json',
                    {val: pCustomerId, ajax: true}, function(j) {
                        var options = '';
                        var selected = '';
                        for (var i = 0; i < j.length; i++) {
                            selected = j[i].id == r.id ? ' selected="selected"' : '';
                            options += '<option value="' + j[i].id + '"'
                                + selected + '>' + j[i].description + '</option>';
                        }
                        $("#projectId").html(options);
                    });
                }
            },
            'json');
            $('#newProjectName').val("");
        });

        // =====================================================================
        // Advanced search popup
        // =====================================================================
        $('#advancedSearchDiv').jqm({
            onShow: function(hash) {
                $("#advSearchResultPanel").hide();
                $("#advSearchCriteriaPanel").show();
                hash.w.show();
            }
        });
        $('#advancedSearch').click(function() {
            $('#advancedSearchDiv').jqmShow({modal:true});
        });
        $("#advancedSearchForm").submit(function() {
            return false;
        });
        $("#advSearchSubmit").click(function(e) {
            e.preventDefault();
            if (document.getElementById('advSearchText').value.length >= 3) {
                $("#advSearchCriteriaPanel").hide();
                $("#advSearchResultPanel").show();
                $('#advSearchResultGrid').dgridLoad();
            }
            else {
                alert('Debe usar un filtro de por lo menos 3 caracteres');
            }
        });
        $('#advSearchChangeFilter').click(function(e) {
            e.preventDefault();
            $("#advSearchResultPanel").hide();
            $("#advSearchCriteriaPanel").show();
        });
        $('#advSearchResultGrid').dgrid({
            url: '?q=/product/list1',
            loadingStatus: 'advSearchResultGridStatus',
            colModel : [
                {name : 'id',          hide: true},
                {name : 'sap',         align: 'center'},
                {name : 'description', align: 'left'},
                {name : 'sellPrice',   align: 'right'},
                {name : 'actions',     align: 'right', render: function(data) {
                    return '<a href="#" name="' + data + '">Agregar</a>';
                }}
                ],
            onSubmit: function() {
                var dt = $('#advancedSearchForm').serializeArray();
                $('#advSearchResultGrid').dgridOptions({params: dt});
                return true;
            },
            onRowClick: function(row) {
                $('#code').val(row[1]);
                $('#sapOption').attr('checked', 'checked');
                $.loadProduct();
                $('#advancedSearchDiv').jqmHide();
            }
        });

        // =====================================================================
        // Products Grid
        // =====================================================================
        jQuery('#productsGrid').dgrid({
            url: '?q=/customer/quotation/detail',
            loadingStatus: 'productsGridStatus',
            records: 'records',
            colModel : [
                {name: 'id', hide: true},
                {name: 'barcode', render: function(barcode) {return new Array(14 - barcode.length).join("0") + barcode;} },
                {name: 'description' },
                {name: 'dispatch' },
                {name: 'install', align: 'center', render: function(v) { return v ? "SI" : "NO"; }},
                {name: 'quantity', align: 'right'},
                {name: 'price', align: 'right'},
                {name: 'discount', align: 'right'},
                {name: 'subtotal', align: 'right'},
                {name: 'specification', align: 'center', render: function(v) {return v ? '*' : '';}},
                {name: 'editablePrice', hide: true}
            ],
            onLoad: function(data) {
                $('#quotationTotal').html(data.total);
                document.selectedProductRow = null;
                document.selectedProductRowKey = null;
                $('#gridForm').each(function() { this.reset(); this.barcode.value = ''; this.rowKey.value = ''; });
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

        // Format the barcodes to show in the select box
        jQuery.formatBarcode = function(barcode, unit) {
            var str = new Array(14 - barcode.length).join("0") + barcode;
            return str + '-' + new Array(4 - unit.length).join(" ") + unit;
        };

        // Request product data using #code, #upcOption, #sapOption
        jQuery.loadProduct = function() {
            var upc = $("#upcOption").attr('checked');
            var sap = $("#sapOption").attr('checked');
            var codeType = upc ? 'UPC' : (sap ? 'SAP' : 'ERR');
            $.getJSON('?q=/product/select', {type: codeType, code: $('#code').val()}, function(r) {
                if (r.success) {
                    var items = r.items;
                    if (items.length > 1) {
                        var select = $("#barcodeSelect");
                        var eSelect = select.get(0);
                        eSelect.products = items;
                        var options = "";
                        for (i=0; i<items.length; i++) {
                            options += '<option value="' + items[i].barcode + '">' 
                                + jQuery.formatBarcode(items[i].barcode, items[i].unit) + '</option>';
                        }
                        select.html(options);
                        var inputCodePos = $("#code").offset();
                        select.css({
                            top: inputCodePos.top + $("#code").outerHeight(),
                            left: inputCodePos.left,
                            width: 220,
                            position: 'absolute'
                        });
                        eSelect.options[0].selected = true;
                        select.show();
                        select.focus();
                    }
                    else if (items.length == 1) {
                        jQuery.fillProductForm(items[0]);
                    }
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

        // onSelect handler for barcodes list
        jQuery('#barcodeSelect').keyup(function(e) {
            switch (e.keyCode) {
                case 13:
                    if (this.selectedIndex > -1) {
                        jQuery.fillProductForm(this.products[this.selectedIndex]);
                        $(this).hide();
                    }
                    break;
                case 27:
                    $(this).hide();
                    break;
            }
        }).blur(function() { $(this).hide(); });

        // Agregar producto
        jQuery('#addProduct').click(function() {
            if ($('#barcode').val()) {
                var data = $("#gridForm").serializeArray();
                $.post("?q=/customer/quotation/detail/addProduct",
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
                    $.post("?q=/customer/quotation/detail/removeProduct",
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
                $.post("?q=/customer/quotation/detail/removeProduct",
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
                jQuery.fillProductForm({
                    barcode: r[1],
                    description: r[2],
                    quantity: r[5],
                    price: r[6],
                    editablePrice: r[10],
                    rowKey: document.selectedProductRowKey});
            }
        });

        // Editar especificaciones del producto
        $('#addCommentDiv').jqm();
        jQuery('#btnEditProductSpecs').click(function(e) {
            e.preventDefault();
            if (document.selectedProductRow) {
                $('#addCommentForm').each(function () {
                    this.reset();
                    this.rowKey.value = document.selectedProductRowKey;
                });
                $('#inputProductSpecs').html(document.selectedProductRow[9] ? document.selectedProductRow[9] : "");
                $('#addCommentDiv').jqmShow({modal: true});
            }
        });

        // Guardar especificaciones del producto
        jQuery('#btnSaveSpecs').click(function(e) {
            e.preventDefault();
            var data = $('#addCommentForm').serializeArray();
            $.post("?q=/customer/quotation/detail/saveProductSpecs",
            data, function(r) {
                if (r.success) {
                    $('#productsGrid').dgridLoad();
                }
                $('#addCommentDiv').jqmHide();
            }, 'json');
        });

        // Cambiar tipo de despacho a todos
        jQuery('#sharedDispatchTypeId').change(function() {
            val = $(this).val();
            $.post("?q=/customer/quotation/detail/changeDispatchType",
                {dispatchTypeId: val}, function(r) {
                if (r.success) {
                    $('#productsGrid').dgridLoad();
                }
            }, 'json');
        });

        // =====================================================================
        // Validation and save
        // =====================================================================
        $('#btnSaveAndNext').click(function() {
            var data = $('#quotHeaderForm').serializeArray();
            $.post('?q=/quotation/save', data, function(r) {
                if (r.success) {
                    document.location = '?q=/quotation/view&id=' + r.quotationId;
                }
                else {
                    for (i=0; i<r.messages.length; i++) {
                        alert(r.messages[i]);
                    }
                }
            }, 'json');
        });

    });
}