<!--
Copyright 2008 Cencosud S.A.
Original Author Frank D. Martinez M.
-->
<script type="text/javascript">QuotationSummary('<?= $view->customer->id ?>','<?= $view->quotation->id ?>');</script>

<fieldset class="formSection" style="padding: 5px;">
    <legend><?=L_OS_DATA?></legend>
    <table class="formLayout" width="100%">
        <tbody>
            <tr>
                <td>
                    <label><?=L_QUOT_NUMBER?>:</label>
                </td>
                <td>
                    <span class="output-label" style="color: #ff0000">
                        <?= $view->quotation->id . ' - ' . L_PLAN_SEPARE ?>
                    </span>
                </td>
                <td>
                    <div class="sep20x10"></div>
                </td>
                <td>
                    <label><?=L_QUOT_DATE?>:</label>
                </td>
                <td>
                    <span class="output-label">
                        <?= ULang::FormatDate($view->quotation->quotationDate) ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td>
                    <label><?=L_QUOT_STATUS?>:</label>
                </td>
                <td>
                    <span class="output-label">
                        <?= $view->quotation->statusName ?>
                    </span>
                </td>
                <td>
                    <div class="sep20x10"></div>
                </td>
                <td>
                    <label><?=L_QUOT_ATTENDER?>:</label>
                </td>
                <td>
                    <span class="output-label">
                        <?= $view->quotation->userName ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td>
                    <label><?=L_STORE?>:</label>
                </td>
                <td>
                    <span class="output-label">
                        <?= $view->quotation->storeName ?>
                    </span>
                </td>
                <td>
                    <div class="sep20x10"></div>
                </td>
                <td>
                    <label><?=L_QUOT_DUEDATE?>:</label>
                </td>
                <td>
                    <span class="output-label">
                        <?= ULang::FormatDate($view->quotation->overdueDate) ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td>
                    <label><?=L_OBSERVATIONS?>:</label>
                </td>
                <td>
                    <span class="output-label">
                        <?= $view->quotation->comments ?>
                    </span>
                </td>
                <td>
                    <div class="sep20x10"></div>
                </td>
                <td>
                    <label><?=L_PROJECT?>:</label>
                </td>
                <td>
                    <span class="output-label">
                        <?= $view->quotation->projectName ?>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
</fieldset>

<fieldset class="formSection" style="padding: 5px;">
    <legend><?=L_CUSTOMER_DATA?></legend>
    <table class="formLayout" width="100%">
        <tbody>
            <tr>
                <td>
                    <label><?= L_RUT ?>:</label>
                </td>
                <td>
                    <span class="output-label">
                        <?= $view->customer->id ?>
                    </span>
                </td>
                <td>
                    <div class="sep20x10"></div>
                </td>
                <td>
                    <label><?= L_NAME ?>:</label>
                </td>
                <td>
                    <span class="output-label">
                        <?= $view->customer->firstname .' '. $view->customer->surname1 .' '. $view->customer->surname2 ?>
                    </span>
                </td>
                <td>
                    <div class="sep20x10"></div>
                </td>
                <td rowspan="4" style="text-align: right; vertical-align: top;">
                    <a id="editCustomerButton" href="#" class="button edit-button-icon"><?=L_EDIT?></a>
                </td>
            </tr>
            <tr>
                <td>
                    <label><?= L_ADDRESS ?>:</label>
                </td>
                <td>
                    <span class="output-label">
                        <?= $view->customer->address ?>
                    </span>
                </td>
                <td>
                    <div class="sep20x10"></div>
                </td>
                <td>
                    <label><?= L_NEIGHBOURHOOD ?>:</label>
                </td>
                <td>
                    <span class="output-label">
                        <?= $view->customer->locationDescriptions['neighbourhood'] ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td>
                    <label><?= L_HOME_PHONE ?>:</label>
                </td>
                <td>
                    <span class="output-label">
                        <?= $view->customer->homePhone ?>
                    </span>
                </td>
                <td>
                    <div class="sep20x10"></div>
                </td>
                <td>
                    <label><?= L_MOBILE ?>:</label>
                </td>
                <td>
                    <span class="output-label">
                        <?= $view->customer->mobile ?>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
</fieldset>

<fieldset class="formSection" style="padding: 5px;">
    <legend><?=L_ADDRESS_DATA?></legend>
    <form id="addressForm">
        <table class="formLayout" width="100%">
            <tbody>
                <tr>
                    <td>
                        <label><?=L_ADDRESS?>:</label>
                    </td>
                    <td colspan="6">
                        <select id="addresses" name="address" <?= $view->quotation->hasFreight() ? 'disabled="disabled"' : ''?>>
                            <?
                                UHtml::ListItems(
                                    $view->addresses,
                                    $view->quotation->addressId, 'id', 'description');
                            ?>
                        </select>
                        <input name="locationId" id="locationId" type="hidden" value="<?= $view->address->locationId ?>" />
                        <? if (!$view->quotation->hasFreight()) : ?>
                            <a href="#" class="button edit-button-icon"><?=L_EDIT?></a>
                            <a href="#" class="button freight-button-icon" id="freightsButton"><?=L_CALCULATE_FREIGHT?></a>
                        <? endif ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label><?=L_NAME?>:</label>
                    </td>
                    <td>
                        <span class="output-label" id="addressName">
                            <?= $view->address->name ?>
                        </span>
                    </td>
                    <td>
                        <div class="sep20x10"></div>
                    </td>
                    <td>
                        <label><?=L_DEPARTMENT?>:</label>
                    </td>
                    <td>
                        <span class="output-label" id="addressDepartment">
                            <?= $view->address->locationDescriptions['department'] ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label><?=L_ADDRESS?>:</label>
                    </td>
                    <td>
                        <span class="output-label" id="addressAddress">
                            <?= $view->address->address ?>
                        </span>
                    </td>
                    <td>
                        <div class="sep20x10"></div>
                    </td>
                    <td>
                        <label><?=L_CITY?>:</label>
                    </td>
                    <td>
                        <span class="output-label" id="addressCity">
                            <?= $view->address->locationDescriptions['city'] ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label><?=L_OBSERVATIONS?>:</label>
                    </td>
                    <td>
                        <span class="output-label" id="addressObservation">
                            <?= $view->address->observation ?>
                        </span>
                    </td>
                    <td>
                        <div class="sep20x10"></div>
                    </td>
                    <td>
                        <label><?=L_NEIGHBOURHOOD?>:</label>
                    </td>
                    <td>
                        <span class="output-label" id="addressNeighbourhood">
                            <?= $view->address->locationDescriptions['neighbourhood'] ?> -
                            <?= $view->address->locationDescriptions['locality'] ?>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</fieldset>

<fieldset class="formSection" style="padding: 5px;">
    <legend><?=L_OS_DETAIL?></legend>
    <table width="100%" class="dgrid">
        <thead>
            <tr>
                <th><?=L_CODES?></th>
                <th><?=L_TYPE?></th>
                <th><?=L_DESC?></th>
                <th><?=L_DISPATCH?></th>
                <th><?=L_DELIVERY?></th>
                <th><?=L_INSTALL?></th>
                <th><?=L_WEIGHT?></th>
                <th><?=L_QUANTITY?></th>
                <th><?=L_PRICE?></th>
                <th><?=L_SUBTOTAL?></th>
            </tr>
        </thead>
        <tbody>
        <?
            foreach($view->quotation->items as $item) {
                ?>
                <tr>
                    <td style="text-align: center;"><?=$item->productBarcode . '<br/>(' . $item->productSAPId . ')' ?></td>
                    <td><?=$item->productType?></td>
                    <td><?=$item->productDescription?></td>
                    <td><?=$item->dispatchTypeDescription?></td>
                    <td style="text-align: right;"><?=ULang::FormatDate($item->deliveryDate)?></td>
                    <td style="text-align: center;"><?=$item->install ? L_YES : L_NO?></td>
                    <td style="text-align: right;"><?=$item->productWeight?></td>
                    <td style="text-align: right;"><?=$item->quantity?></td>
                    <td style="text-align: right;"><?= ULang::FormatCurrency($item->price) ?></td>
                    <td style="text-align: right;"><?= ULang::FormatCurrency($item->getSubTotal()) ?></td>
                </tr>
                <?
            }
        ?>
            <tr style="background: #c0c0c0">
                <td colspan="9" align="right" style="font-size: 14px; font-weight: bold;">
                    <?=L_TOTAL?>:
                </td>
                <td colspan="10" align="right" style="font-size: 14px; font-weight: bold;">
                    <?= ULang::FormatCurrency($view->quotation->getTotal()) ?>
                </td>
            </tr>
        </tbody>
    </table>
</fieldset>

<div style="padding: 5px; text-align: center;">
    <a href="#" id="printQuotationButton" class="button print-button-icon">
        <?= L_PRINT_QUOTATION ?>
    </a>
    <a href="#" id="printContractButton" class="button print-button-icon">
        <?= L_PRINT_CONTRACT ?>
    </a>
    <a href="#" id="printFiniquitoButton" class="button print-button-icon">
        <?= L_PRINT_FINIQUITO ?>
    </a>
</div>

