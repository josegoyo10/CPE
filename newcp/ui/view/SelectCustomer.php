<!--
Copyright 2008 Cencosud S.A.
Original Author Frank D. Martinez M.
-->
<?
include(includePath('lib/util/web/UHtml.php'));
include(includePath('ui/view/FlowState.php'));
?>
<form action="?q=/customer/select" accept-charset="utf-8" method="POST" id="form" name="form">
    <fieldset class="formSection">
        <legend><?= L_SEL_CUST_PAGE_TITLE ?></legend>
            <table class="formLayout" align="center">
                <tbody>
                    <tr>
                        <td>
                            <label>*<?= L_RUT ?>:</label>
                        </td>
                        <td>
                            <input id="cust:id" name="cust:id" type="text" maxlength="15" autocomplete="off" />
                        </td>
                        <td>
                            <label><?= L_RUT_EXAMPLE ?></label>
                        </td>
                    </tr>
                </tbody>
            </table>
    </fieldset>
    <div class="buttons-bar">
        <? 
            UHtml::FormSubmitButton(L_NEXT, 'form', 'next-button-icon');
        ?>
    </div>
</form>