<!--
Copyright 2008 Cencosud S.A.
Original Author Frank D. Martinez M.
-->
<script type="text/javascript">QuotationForm('<?= $view->customer->id ?>');</script>
<fieldset class="formSection" style="padding: 5px;">
    <legend><?= L_OS_DATA ?></legend>
    <form id="quotHeaderForm">
        <table class="formLayout" width="100%">
            <tbody>
                <tr>
                    <td><label><?= L_CUSTOMER ?>:</label></td>
                    <td colspan="5">
                        [ <?= $view->customer->id ?> ]
                        <?= $view->customer->firstname ?>
                        <?= $view->customer->surname1 ?>
                        <?= $view->customer->surname2 ?>
                        <input type="hidden" name="customerId" value="<?= $view->customer->id ?>" />
                    </td>
                </tr>
                <tr>
                    <td valign="top"><label><?= L_PROJECT ?>:</label></td>
                    <td valign="top">
                        <select name="projectId" id="projectId"
                            style="width: 180px; font-size: 10px;">
                            <? 
                                UHtml::ListItems(
                                    $view->projects,
                                    $view->quotation->projectId,
                                    'id', 'description');
                            ?>
                        </select>
                        <br />
                        <input name="newProjectName" id="newProjectName" type="text"
                            style="width: 150px;"/>
                        <a class="button" id="btnAddProject">+</a>
                    </td>
                    <td>
                        <div class="sep10x10"></div>
                    </td>
                    <td valign="top"><label><?= L_OBSERVATIONS ?>:</label></td>
                    <td>
                        <div class="sep10x10"></div>
                    </td>
                    <td valign="top">
                        <textarea name="description" id="quotDescription"
                            style="width: 250px; height: 40px;"
                            rows="5" cols="50"><?= $view->quotation->comments ?></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</fieldset>

<!-- ======================================================================= -->
<!-- Product Form -->
<!-- ======================================================================= -->
<fieldset class="formSection">
    <legend><?= L_OS_DETAIL ?></legend>
    <table class="formLayout">
        <tbody>
            <tr>
                <td>
                    <a href="#" class="button" id="advancedSearch"><?=L_ADV_SEARCH?></a>
                </td>
                <td>
                    <a href="#" class="button" id="SpecialOrderProduct"><?=L_SPECIAL_ORDER_PRODUCT?></a>
                </td>
                <td>
                    <select id="sharedDispatchTypeId">
                        <? UHtml::ListItems($view->dispatchTypes, -1, 'id', 'description', array(-1, L_CHANGE_DISPATCH_TYPE)); ?>
                    </select>
                </td>
                <td>
                    <div id="productsGridStatus" class="loading"></div>
                </td>
            </tr>
        </tbody>
    </table>
    <form id="gridForm">
        <table class="formLayout2" width="100%" style="background: #e0e0e0">
            <tbody>
                <tr>
                    <td>
                        <input id="upcOption" name="codeType" type="radio" value="UPC" />
                        <label for="upcOption"><?=L_UPC?></label>
                        <input id="sapOption" name="codeType" type="radio" value="SAP" checked="checked" />
                        <label for="sapOption"><?=L_SAP?></label>
                    </td>
                    <td>
                        <label><?=L_DESC?></label>
                    </td>
                    <td>
                        <label><?=L_DISPATCH?></label>
                    </td>
                    <td>
                        <label><?=L_INSTALL?></label>
                    </td>
                    <td style="text-align: right;">
                        <label><?=L_PRICE?></label>
                    </td>
                    <td style="text-align: right;">
                        <label><?=L_QUANTITY?></label>
                    </td>
                    <td style="text-align: right;">
                        <label><?=L_SUBTOTAL?></label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="text" id="code" name="code" style="width: 110px;" />
                        <input type="hidden" id="barcode" name="barcode" />
                        <input type="hidden" id="rowKey" name="rowKey" />
                    </td>
                    <td>
                        <input type="text" id="description" style="width: 200px;" readonly="readonly"/>
                    </td>
                    <td>
                        <select id="dispatchTypeId" name="dispatchTypeId" style="width: 90px;">
                            <? UHtml::ListItems($view->dispatchTypes, 0, 'id', 'description'); ?>
                        </select>
                    </td>
                    <td>
                        <select id="install" name="install" disabled="disabled">
                            <? UHtml::BooleanOptions(false); ?>
                        </select>
                    </td>
                    <td style="text-align: right;">
                        <input type="text" id="price" name="price" style="width: 90px; text-align: right;" readonly="readonly" />
                    </td>
                    <td style="text-align: right;">
                        <input type="text" id="quantity" name="quantity" style="width: 40px;" />
                    </td>
                    <td style="text-align: right;">
                        <input type="text" id="subtotal" style="width: 100px; text-align: right;" readonly="readonly" />
                    </td>
                    <td>
                        <input id="addProduct" type="button" value="<?=L_ADD?>"/>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</fieldset>

<!-- ======================================================================= -->
<!-- Products Grid -->
<!-- ======================================================================= -->
<div style="padding: 3px 0px 3px 0px;">
    <table id="productsGrid" width="100%">
        <col width="100"/>
        <col width="120"/>
        <col width="90"/>
        <col width="30"/>
        <col width="40"/>
        <col width="100"/>
        <col width="100"/>
        <col width="100"/>
        <col width="1"/>
        <thead>
            <tr>
                <th><?=L_BARCODE?></th>
                <th><?=L_DESC?></th>
                <th><?=L_DISPATCH?></th>
                <th><?=L_INSTALL?></th>
                <th><?=L_QUANTITY?></th>
                <th><?=L_PRICE?></th>
                <th><?=L_DISCOUNT?></th>
                <th><?=L_SUBTOTAL?></th>
                <th><?=L_SPEC?></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <div align="right" style="padding: 3px; background: #c0c0c0; font-weight: bold;">
        <span><?= L_TOTAL ?>:</span>
        <span id="quotationTotal" style="margin-left: 10px;"></span>
    </div>
</div>
<select id="barcodeSelect" style="display: none;" size="5">
</select>

<div align="center" style="padding-top: 10px;">
    <a href="#" id="btnEditProduct" class="button"><?= L_EDIT_PRODUCT ?></a>
    <a href="#" id="btnDeleteProduct" class="button"><?= L_DEL_PRODUCT ?></a>
    <a href="#" id="btnDeleteAllProducts" class="button"><?= L_DEL_ALL_PRODUCTS ?></a>
    <a href="#" id="btnEditProductSpecs" class="button"><?= L_PRODUCT_COMMENT ?></a>
</div>

<!-- ======================================================================= -->
<!-- Navigation bar -->
<!-- ======================================================================= -->

<div class="buttons-bar">
    <a href="?q=/customer/oslist&customerId=<?= $view->customer->id ?>" class="button prev-button-icon"><?= L_PREV ?></a>
    <a href="#" id="btnSaveAndNext" class="button next-button-icon"><?= L_NEXT ?></a>
</div>

<!-- ======================================================================= -->
<!-- Advanced Search Popup -->
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
<!-- Add comment to product -->
<!-- ======================================================================= -->

<div id="addCommentDiv" style="display: none;" class="jqmWindow">
	<div class='modalHeader'>
        <a href='#' title='<?=L_CLOSE?>' class='jqmClose' style="float: right; margin-right: 5px;">x</a>
        <?=L_PRODUCT_COMMENT?>
    </div>
    <div class='modalBody'>
        <form id="addCommentForm">
            <fieldset class="formSection">
                <legend><?=L_EDIT_SPECS?></legend>
                <table class="formLayout">
                    <tr>
                        <td>
                            <textarea name="specs" id="inputProductSpecs" rows="5" cols="50"></textarea>
                            <input type="hidden" name="rowKey" id="inputProductSpecsRowKey" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="#" class="button" id="btnSaveSpecs"><?=L_SAVE?></a>
                            <a href="#" class="button jqmClose"><?=L_CANCEL?></a>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </div>
</div>
