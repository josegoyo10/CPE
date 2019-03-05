/*
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
function SearchProductPopup(options) {
    jQuery(document).ready(function() {

        // Popup panel
        $('#advancedSearchDiv').jqm({
            onShow: function(hash) {
                $("#advSearchResultPanel").hide();
                $("#advSearchCriteriaPanel").show();
                hash.w.show();
            }
        });

        // Search button
        $('#advancedSearch').click(function() {
            $('#advancedSearchDiv').jqmShow({modal:true});
        });

        // Search form
        $("#advancedSearchForm").submit(function() {
            return false;
        });

        // Search form submit handler
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

        // Change from result view to criteria form
        $('#advSearchChangeFilter').click(function(e) {
            e.preventDefault();
            $("#advSearchResultPanel").hide();
            $("#advSearchCriteriaPanel").show();
        });

        // Results grid
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
                options.onSelect(row[1]);
                $('#advancedSearchDiv').jqmHide();
            }
        });


    });
}