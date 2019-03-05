<!--
Copyright 2008 Cencosud S.A.
Original Author Frank D. Martinez M.
-->
<script type="text/javascript">Form(<?= $view->customerId ?>);</script>
<fieldset class="formSection" style="padding: 5px;">
    <legend><?= L_CUSTOMER_DATA ?></legend>
    <form id="selectCustomerForm" action="?q=/financial/interests/form" method="post">
        <table class="formLayout">
            <tbody>
                <tr>
                    <td>
                        <label><?= L_CC ?>:</label>
                    </td>
                    <td>
                        <input name="customerId" id="customerId" type="text" value="<?= $view->customerId ?>" style="width: 90px;" />
                    </td>
                    <? if ($view->customerFound) : ?>
                    <td>
                        <label><?= L_NAME ?>:</label>
                    </td>
                    <td>
                        <label id="customerName" class="outputText" style="width: 300px; display: block;">
                            <?= $view->customerName ?>
                        </label>
                    </td>
                    <td>
                        <a href="#" id="editCustomerButton" class="button edit-button-icon"><?= L_EDIT ?></a>
                    </td>
                    <? endif ?>
                </tr>
            </tbody>
        </table>
    </form>
</fieldset>
<? if ($view->customerFound) : ?>
    <!-- ======================================================================= -->
    <!-- Product Form -->
    <!-- ======================================================================= -->
    <fieldset class="formSection">
        <legend><?= L_OS_DETAIL ?></legend>
        <form id="gridForm">
            <a href="#" class="button" id="advancedSearch"><?=L_ADV_SEARCH?></a>
            <table class="formLayout2" width="100%" style="background: #e0e0e0">
                <tbody>
                    <tr>
                        <td>
                            <label><?=L_UPC?></label>
                        </td>
                        <td>
                            <label><?=L_DESC?></label>
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
            <col width="40"/>
            <col width="100"/>
            <col width="100"/>
            <thead>
                <tr>
                    <th><?=L_BARCODE?></th>
                    <th><?=L_DESC?></th>
                    <th><?=L_QUANTITY?></th>
                    <th><?=L_PRICE?></th>
                    <th><?=L_SUBTOTAL?></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div align="right" style="padding: 3px; background: #c0c0c0; font-weight: bold;">
            <span><?= L_TOTAL ?>:</span>
            <span id="quotationTotal" style="margin-left: 10px;"></span>
        </div>
    </div>

    <div align="center" style="padding-top: 10px;">
        <a href="#" id="btnEditProduct" class="button"><?= L_EDIT_PRODUCT ?></a>
        <a href="#" id="btnDeleteProduct" class="button"><?= L_DEL_PRODUCT ?></a>
        <a href="#" id="btnDeleteAllProducts" class="button"><?= L_DEL_ALL_PRODUCTS ?></a>
    </div>


    <!-- ======================================================================= -->
    <!-- Financial interests Form -->
    <!-- ======================================================================= -->
    <fieldset class="formSection">
        <legend><?= L_FINANCIATION ?></legend>
        <form id="financialForm">
            <table class="formLayout" width="100%">
                <tbody>
                    <tr>
                        <td>
                            <label><strong><?=L_INITIAL_QUOTA?>*</strong></label>
                        </td>
                        <td>
                            <label><strong><?=L_NUM_CHECKS?>*</strong></label>
                        </td>
                        <td width="100%"></td>
                        <td>
                            <label><?=L_FINANCED_VALUE?></label>
                        </td>
                        <td>
                            <label><?=L_FINANCE_RATE?></label>
                        </td>
                        <td>
                            <label><?=L_CHECK_VALUE?></label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input name="initialQuota" id="initialQuota" type="text" style="width: 120px;" />
                        </td>
                        <td>
                            <input name="numChecks" id="numChecks" type="text" style="width: 120px;"/>
                        </td>
                        <td width="100%"></td>
                        <td>
                            <input name="financedValue" id="financedValue" type="text" readonly="readonly" style="width: 120px; text-align: right;"/>
                        </td>
                        <td>
                            <input name="financeRate" id="financeRate" type="text" readonly="readonly" style="width: 120px; text-align: right;"/>
                        </td>
                        <td>
                            <input name="checkValue" id="checkValue" type="text" readonly="readonly" style="width: 120px; text-align: right;"/>
                        </td>
                        <td>
                            <input type="button" value="<?= L_CALCULATE ?>" id="btnCalculateFinanciation" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </fieldset>


    <!-- ======================================================================= -->
    <!-- Navigation bar -->
    <!-- ======================================================================= -->

    <div class="buttons-bar">
        <a href="../modulos/start/start_01.php" id="btnCancel" class="button"><?= L_CANCEL ?></a>
        <a href="#" id="btnPrint" class="button" style="display: none;"><?= L_PRINT ?></a>
    </div>

    <? include_once includePath('ui/view/product/SearchProductPopup.php'); ?>
    <script type="text/javascript">
        SearchProductPopup({onSelect: function(code) {
            $('#code').val(code);
            $.loadProduct('SAP');
        }});
    </script>

<? else : ?>
<script type="text/javascript">newCustomerForm(<?= $view->customerId ?>);</script>
<? endif ?>