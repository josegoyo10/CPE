<!-- ======================================================================= -->
<!-- [START INCLUDE] Advanced Search Popup                                   -->
<!-- ======================================================================= -->
<div id="advancedSearchDiv" style="display: none;" class="jqmWindow">

	<div class='modalHeader'>
        <a href='#' title='<?=L_CLOSE?>' class='jqmClose' style="float: right; margin-right: 5px;">x</a>
        <?=L_ADV_SEARCH?>
    </div>
    <div class='modalBody'>
        <form id="advancedSearchForm">
            <table class="formLayout" id="advSearchCriteriaPanel" width="480">
                <tr>
                    <td valign="top">
                        <fieldset class="formSection" style="height: 100px;">
                            <legend><?=L_BY_PROVIDER?></legend>
                            <input id="prov_name" type="radio" name="searchKey" value="provider,name" />
                            <label for="prov_name"><?=L_NAME?></label>
                            <br />
                            <input id="prov_id" type="radio" name="searchKey" value="provider,id" />
                            <label for="prov_id"><?=L_CC?></label>
                            <br />
                        </fieldset>
                    </td>
                    <td valign="top">
                        <fieldset class="formSection" style="height: 100px;">
                            <legend><?=L_BY_PRODUCT?></legend>
                            <input id="prod_desc" type="radio" name="searchKey" value="product,description" checked="checked" />
                            <label for="prod_desc"><?=L_DESC?></label>
                            <br />
                            <input id="prod_sap" type="radio" name="searchKey" value="product,sap" />
                            <label for="prod_sap"><?=L_SAP?></label>
                            <br />
                            <input id="prod_upc" type="radio" name="searchKey" value="product,upc" />
                            <label for="prod_upc"><?=L_UPC?></label>
                        </fieldset>
                    </td>
                    <td valign="top">
                        <fieldset class="formSection" style="height: 100px;">
                            <legend><?=L_BY_SERVICE?></legend>
                            <input id="serv_desc" type="radio" name="searchKey" value="service,description" />
                            <label for="serv_desc"><?=L_DESC?></label>
                            <br />
                            <input id="serv_sap" type="radio" name="searchKey" value="service,sap" />
                            <label for="serv_sap"><?=L_SAP?></label>
                            <br />
                            <input id="serv_upc" type="radio" name="searchKey" value="service,upc" />
                            <label for="serv_upc"><?=L_UPC?></label>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div class="simpleSection">
                            <label><?=L_SEARCH?>:</label>
                            <input name="searchText" type="text" id="advSearchText" style="width: 300px;" />
                            <input type="submit" value="<?=L_SEARCH?>" id="advSearchSubmit" />
                        </div>
                    </td>
                </tr>
            </table>
            <div class="simpleSection" id="advSearchResultPanel" style="display: none; padding: 3px;">
                <div style=" width: 475px; height: 260px; overflow: auto; border-bottom: 1px solid #c0c0c0">
                    <table id="advSearchResultGrid" width="100%">
                        <col width="40" />
                        <col width="120" />
                        <col width="80" />
                        <col width="100" />
                        <thead>
                            <tr>
                                <th><?=L_SAP?></th>
                                <th><?=L_DESC?></th>
                                <th><?=L_SELL_PRICE?></th>
                                <th><?=L_ACTIONS?></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div id="advSearchResultGridStatus" class="loading" style="float: right;"></div>
                <a href="#" class="button" id="advSearchChangeFilter" style="margin-top: 5px;"><?=L_SEARCH_PARAMETERS?></a>
                <a href='#' class='button jqmClose'><?=L_CLOSE?></a>
            </div>
        </form>
    </div>
</div>
<!-- ======================================================================= -->
<!-- [END INCLUDE] Advanced Search Popup                                     -->
<!-- ======================================================================= -->
