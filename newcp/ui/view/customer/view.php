<!--
Copyright 2008 Cencosud S.A.
Original Author Frank D. Martinez M.
-->
<? include(includePath('lib/util/web/UHtml.php')); ?>
<script type="text/javascript">view("<?= $view->customer->id ?>");</script>
<fieldset class="formSection">
    <legend><?= L_GENERAL_INFO ?></legend>
    <table class="formLayout3">
        <tbody>
            <tr>
                <td>
                    <label><?= L_RUT ?>:</label>
                </td>
                <td>
                    <span class="outputText2">
                        <?= $view->customer->id ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td>
                    <label><?= L_FIRST_NAME ?>:</label>
                </td>
                <td colspan="4">
                    <span class="outputText2"><?= $view->customer->firstname ?></span>
                    <span class="outputText2"><?= $view->customer->surname1 ?></span>
                    <span class="outputText2"><?= $view->customer->surname2 ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <label><?= L_EMAIL ?>:</label>
                </td>
                <td>
                    <span class="outputText2"><?= $view->customer->email ?></span>
                </td>
                <td>
                    <div class="sep10x10"></div>
                </td>
                <td>
                    <label><?= L_ADDRESS ?>:</label>
                </td>
                <td>
                    <span class="outputText2"><?= $view->customer->address ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <label><?= L_HOME_PHONE ?>:</label>
                </td>
                <td>
                    <span class="outputText2"><?= $view->customer->homePhone ?></span>
                </td>
                <td>
                    <div class="sep10x10"></div>
                </td>
                <td>
                    <label><?= L_FAX ?>:</label>
                </td>
                <td>
                    <span class="outputText2"><?= $view->customer->fax ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <label><?= L_MOBILE ?>:</label>
                </td>
                <td>
                    <span class="outputText2"><?= $view->customer->mobile ?></span>
                </td>
                <td>
                    <div class="sep10x10"></div>
                </td>
                <td>
                    <label><?= L_GENDER ?>:</label>
                </td>
                <td>
                    <span class="outputText2"><?= $view->customer->gender == 1 ? 'Femenino' : 'Masculino' ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <label><?= L_DEPARTMENT ?>:</label>
                </td>
                <td colspan="4">
                    <span class="outputText2"><?= $view->location['department'] ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <label><?= L_CITY ?>:</label>
                </td>
                <td colspan="4">
                    <span class="outputText2"><?= $view->location['city'] ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <label><?= L_NEIGHBOURHOOD ?>:</label>
                </td>
                <td colspan="4">
                    <span class="outputText2"><?= $view->location['neighbourhood'] ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <label><?= L_CUSTOMER_TYPE ?>:</label>
                </td>
                <td>
                    <span class="outputText2"><?= $view->customerType ?></span>
                </td>
                <td>
                    <div class="sep10x10"></div>
                </td>
                <td>
                    <label><?= L_CUSTOMER_CATEGORY ?>:</label>
                </td>
                <td>
                    <span class="outputText2"><?= $view->customerCategory ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <label><?= L_COMMENT ?>:</label>
                </td>
                <td colspan="4">
                    <span class="outputTextArea2"><?= $view->customer->addressObservations ?></span>
                </td>
            </tr>
        </tbody>
    </table>
</fieldset>
<fieldset class="formSection" style="padding: 10px;">
    <legend><?= L_TAX_INFO ?></legend>
    <table class="formLayout">
        <tbody>
            <tr>
                <td>
                    <label><?= L_TAXCONTRIBUTIONTYPE ?>:</label>
                </td>
                <td>
                    <span class="outputText2"><?= $view->customer->taxContributionTypeId ?></span>
                </td>
                <td>
                    <div class="sep10x10"></div>
                </td>
                <td>
                    <label><?= L_TAX_RETEICA ?>:</label>
                    <span class="outputText2"><?= $view->customer->taxReteica ? L_YES : L_NO ?></span>
                </td>
                <td>
                    <div class="sep10x10"></div>
                </td>
                <td>
                    <label><?= L_TAX_RETEFUENTE ?>:</label>
                    <span class="outputText2"><?= $view->customer->taxRetefuente ? L_YES : L_NO ?></span>
                </td>
                <td>
                    <div class="sep10x10"></div>
                </td>
                <td>
                    <label><?= L_TAX_RETEIVA ?>:</label>
                    <span class="outputText2"><?= $view->customer->taxReteiva ? L_YES : L_NO ?></span>
                </td>
            </tr>
        </tbody>
    </table>
</fieldset>
<div class="buttons-bar">
    <a href="?q=/customer/select" class="button prev-button-icon"><?=L_PREV?></a>
    <a id="editCustomerButton" href="#" class="button edit-button-icon"><?=L_EDIT_CUSTOMER?></a>
    <a href="?q=/customer/oslist&customerId=<?= $view->customer->id ?>" class="button next-button-icon"><?=L_NEXT?></a>
</div>
