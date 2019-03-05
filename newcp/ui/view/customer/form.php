<!--
Copyright 2008 Cencosud S.A.
Original Author Frank D. Martinez M.
-->
<? include(includePath('lib/util/web/UHtml.php')); ?>
<script type="text/javascript">form("<?= $view->customer->id ?>");</script>
<table width="100%">
    <tr>
        <td>
            <h1 style="margin: 0px;"><?=L_EDIT_CUSTOMER?></h1>
        </td>
        <td style="white-space: nowrap; text-align: right;">
            <a href="#" id="postForm" class="button save-button-icon"><?= L_SAVE ?></a>
            <a href="#" id="closeWindow" class="button cancel-button-icon"><?= L_CLOSE ?></a>
        </td>
    </tr>
</table>

<form accept-charset="UTF-8" id="form" name="form">
    <fieldset class="formSection">
        <legend><?= L_GENERAL_INFO ?></legend>
        <table class="formLayout" align="center">
            <tbody>
                <tr>
                    <td>
                        <label>*<?= L_RUT ?>:</label>
                    </td>
                    <td>
                        <input id="cust:id" name="cust:id"
                            type="text" maxlength="15" autocomplete="off"
                            value="<?= $view->customerId ?>"
                            readonly="readonly"
                            style="width: 100px;" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>*<?= L_FIRST_NAME ?>:</label>
                    </td>
                    <td colspan="4">
                        <input id="cust:firstname" name="cust:firstname"
                            type="text" maxlength="100" autocomplete="off"
                            value="<?= $view->customer->firstname ?>"
                            style="width: 300px;" class="uppercase" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>*<?= L_SURNAME1 ?>:</label>
                    </td>
                    <td>
                        <input id="cust:surname1" name="cust:surname1"
                            type="text" maxlength="100" autocomplete="off"
                            value="<?= $view->customer->surname1 ?>"
                            style="width: 200px;" class="uppercase" />
                    </td>
                    <td>
                        <div class="sep10x10"></div>
                    </td>
                    <td>
                        <label>*<?= L_SURNAME2 ?>:</label>
                    </td>
                    <td>
                        <input id="cust:surname2" name="cust:surname2"
                            type="text" maxlength="100" autocomplete="off"
                            value="<?= $view->customer->surname2 ?>"
                            style="width: 200px;" class="uppercase" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>*<?= L_EMAIL ?>:</label>
                    </td>
                    <td>
                        <input id="cust:email" name="cust:email"
                            type="text" maxlength="255" autocomplete="off"
                            value="<?= $view->customer->email ?>"
                            style="width: 200px;" />
                    </td>
                    <td>
                        <div class="sep10x10"></div>
                    </td>
                    <td>
                        <label>*<?= L_ADDRESS ?>:</label>
                    </td>
                    <td>
                        <input id="cust:address" name="cust:address"
                            type="text" maxlength="255" autocomplete="off"
                            value="<?= $view->customer->address ?>"
                            style="width: 250px;" class="uppercase" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>*<?= L_HOME_PHONE ?>:</label>
                    </td>
                    <td>
                        <input id="cust:homePhone" name="cust:homePhone"
                            type="text" maxlength="15" autocomplete="off"
                            value="<?= $view->customer->homePhone ?>"
                            style="width: 150px;" />
                    </td>
                    <td>
                        <div class="sep10x10"></div>
                    </td>
                    <td>
                        <label><?= L_FAX ?>:</label>
                    </td>
                    <td>
                        <input id="cust:fax" name="cust:fax"
                            type="text" maxlength="15" autocomplete="off"
                            value="<?= $view->customer->fax ?>"
                            style="width: 150px;" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label><?= L_MOBILE ?>:</label>
                    </td>
                    <td>
                        <input id="cust:mobile" name="cust:mobile"
                            type="text" maxlength="15" autocomplete="off"
                            value="<?= $view->customer->mobile ?>"
                            style="width: 150px;" />
                    </td>
                    <td>
                        <div class="sep10x10"></div>
                    </td>
                    <td>
                        <label>*<?= L_GENDER ?>:</label>
                    </td>
                    <td>
                        <select id="cust:gender" name="cust:gender">
                        <?
                            UHtml::ListItems(
                                $view->genders,
                                $view->customer->gender,
                                'id', 'description');
                        ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>*<?= L_DEPARTMENT ?>:</label>
                    </td>
                    <td colspan="4">
                        <select id="cust:departmentId" name="cust:departmentId" style="width: 300px;">
                        <?
                            UHtml::ListItems(
                                $view->departments,
                                $view->customer->departmentId,
                                'id', 'description',
                                array(0, L_SELECT_DEPARTMENT))
                        ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>*<?= L_CITY ?>:</label>
                    </td>
                    <td colspan="4">
                        <select id="cust:cityId" name="cust:cityId" style="width: 300px;">
                        <?
                            UHtml::ListItems(
                                $view->cities,
                                $view->customer->cityId,
                                'id', 'description',
                                array(0, L_SELECT_CITY))
                        ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>*<?= L_NEIGHBOURHOOD ?>:</label>
                    </td>
                    <td colspan="4">
                        <select id="cust:locationId" name="cust:locationId" style="width: 300px;">
                        <?
                            UHtml::ListItems(
                                $view->neighbourhoods,
                                $view->customer->locationId,
                                'id', 'description',
                                array(0, L_SELECT_NEIGHBOURHOOD));
                        ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>*<?= L_CUSTOMER_TYPE ?>:</label>
                    </td>
                    <td>
                        <select name="cust:customerTypeId" id="cust:customerTypeId">
                        <?
                            UHtml::ListItems(
                                $view->custTypes,
                                $view->customer->customerTypeId,
                                'id', 'description');
                        ?>
                        </select>
                    </td>
                    <td>
                        <div class="sep10x10"></div>
                    </td>
                    <td>
                        <label><?= L_CUSTOMER_CATEGORY ?>:</label>
                    </td>
                    <td>
                        <select name="cust:categoryId" id="cust:categoryId">
                        <?
                            UHtml::ListItems(
                                $view->custCategories,
                                $view->customer->categoryId,
                                'id', 'description');
                        ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label><?= L_COMMENT ?>:</label>
                    </td>
                    <td colspan="4">
                        <textarea id="cust:addressObservations" name="cust:addressObservations" cols="60" rows="5"
                        style="width: 400px; height: 50px;"><?=
                            $view->customer->addressObservations
                        ?></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
    </fieldset>
    <fieldset class="formSection">
        <legend><?= L_TAX_INFO ?></legend>
        <table class="formLayout" align="center">
            <tbody>
                <tr>
                    <td>
                        <label>*<?= L_TAXCONTRIBUTIONTYPE ?>:</label>
                    </td>
                    <td>
                        <select name="cust:taxContributionTypeId" id="cust:taxContributionTypeId">
                        <?
                            UHtml::ListItems(
                                $view->taxContributionTypes,
                                $view->customer->taxContributionTypeId,
                                'abbr', 'description');
                        ?>
                        </select>
                    </td>
                    <td>
                        <div class="sep10x10"></div>
                    </td>
                    <td>
                        <label for="cust:taxReteica"><?= L_TAX_RETEICA ?></label>
                        <? UHtml::CheckBox("cust:taxReteica", $view->customer->taxReteica, false); ?>
                    </td>
                    <td>
                        <div class="sep10x10"></div>
                    </td>
                    <td>
                        <label for="cust:taxRetefuente"><?= L_TAX_RETEFUENTE ?></label>
                        <? UHtml::CheckBox("cust:taxRetefuente", $view->customer->taxRetefuente, false); ?>
                    </td>
                    <td>
                        <div class="sep10x10"></div>
                    </td>
                    <td>
                        <label for="cust:taxReteiva"><?= L_TAX_RETEIVA ?></label>
                        <? UHtml::CheckBox("cust:taxReteiva", $view->customer->taxReteiva, false); ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </fieldset>
</form>