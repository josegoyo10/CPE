<!--
Copyright 2008 Cencosud S.A.
Original Author Frank D. Martinez M.
-->
<script type="text/javascript">QuotationList("<?= $view->customer->id ?>");</script>
<fieldset class="formSection">
    <legend><?= L_CUSTOMER_DATA ?></legend>
    <table class="formLayout" width="100%">
        <tbody>
            <tr>
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
                        <?= $view->location['neighbourhood'] ?>
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
                    <label><?= L_FAX ?>:</label>
                </td>
                <td>
                    <span class="output-label">
                        <?= $view->customer->fax ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td>
                    <label><?= L_MOBILE ?>:</label>
                </td>
                <td>
                    <span class="output-label">
                        <?= $view->customer->mobile ?>
                    </span>
                </td>
                <td>
                    <div class="sep20x10"></div>
                </td>
                <td>
                    <label><?= L_COMMENT ?>:</label>
                </td>
                <td>
                    <span class="output-label">
                        <?= $view->customer->addressObservations ?>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
</fieldset>
<div style="padding: 5px;">
    <form id="filterForm">
        <input type="hidden" name="customerId" value="<?= $view->customer->id ?>" />
        <table class="formLayout" cellpadding="0" cellspacing="0">
            <tbody>
                <tr>
                    <td>
                        <? UHtml::SimpleButtonLink(L_NEW_QUOTATION, "?q=/customer/os/new&customerId={$view->customer->id}", 'new-button-icon') ?>
                    </td>
                    <td width="100%"></td>
                    <td>
                        <select id="projectFilter" name="projectFilter">
                        <?
                            UHtml::ListItems(
                                $view->projects,
                                0,
                                'id', 'description',
                                array(0, L_ALL_PROJECTS));
                        ?>
                        </select>
                    </td>
                    <td>
                        <select id="statusFilter" name="statusFilter">
                        <?
                            UHtml::ListItems(
                                $view->states,
                                '',
                                'id', 'description',
                                array('', L_ALL_STATES));
                        ?>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
<div style="padding: 3px 0px 3px 0px;">
    <table id="grid" width="100%">
        <col width="40"/>
        <col width="120"/>
        <col width="180"/>
        <col width="100"/>
        <col width="100"/>
        <col width="40"/>
        <thead>
            <tr>
                <th><?=L_OS_NUMBER?></th>
                <th><?=L_PROJECT?></th>
                <th><?=L_OS_DETAIL?></th>
                <th><?=L_OS_DATE?></th>
                <th><?=L_OS_STATUS?></th>
                <th><?=L_ACTIONS?></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<div class="buttons-bar">
    <?
        UHtml::SimpleButtonLink(L_PREV, "?q=/customer/view&id={$view->customer->id}", 'prev-button-icon');
        UHtml::SimpleButtonLink(L_NEXT, "?q=/customer/os/new&customerId={$view->customer->id}", 'next-button-icon');
    ?>
</div>
