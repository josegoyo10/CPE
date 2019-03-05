<style>
    .contract ol li {
        text-align: justify;
    }
    .contract {
        width: 21cm;
        margin-right: auto;
        margin-left: auto;
        border: 1px solid #c0c0c0;
        background: #ffffff;
        padding: 1cm;
    }
    .contract .logo {
        padding: 5px;
        border-bottom: 1px solid #000000;
        text-align: center;
    }
    .contract .sim-ckeckbox {
        width: 16px;
        height: 16px;
        border: 1px solid #000000;
    }
</style>


<div class="contract">

    <table width="100%">
        <tr>
            <td>
                <img src="ui/view/theme/images/logo3.gif" />
            </td>
            <td>
                <h1>FINIQUITO CONTRATO PLAN SEPARALO</h1>
            </td>
        </tr>
    </table>

    <? if ($view->reprint) : ?>
        <table width="100%">
            <tr>
                <td style="border: 1pt solid">
                    Reimpresi&oacute;n No. <?= $view->reprint ?>
                </td>
                <td style="text-align: right; border: 1pt solid">
                    Autorizado Por: <?= $view->reprintUser ?>
                </td>
            </tr>
        </table>
    <? endif ?>

    <p style="text-align: justify">
        A las <?= date("H:n") ?> horas del d&iacute;a <?= date("d/m") ?> del A&ntilde;o <?= date("Y") ?> el(la) Se&ntilde;or(a) <?= $view->quotation->customerName ?>
        identificado(a) con c&eacute;dula de ciudadan&iacute;a No. <?= $view->quotation->customerId ?> de __________________ da por terminado el Contrato
        relacionado al Plan Separalo No. <?= $view->quotation->barcode ?> firmado con Easy Colombia S.A por concepto de:
    </p>


    <table width="400">
        <tr>
            <td>
                a. Pago total
            </td>
            <td>
                <div class="sim-ckeckbox"></div>
            </td>
        </tr>
        <tr>
            <td>
                b. Arrepentimiento del cliente
            </td>
            <td>
                <div class="sim-ckeckbox"></div>
            </td>
        </tr>
        <tr>
            <td>
                c. Arrepentimiento de Easy Colombia S.A.
            </td>
            <td>
                <div class="sim-ckeckbox"></div>
            </td>
        </tr>
    </table>

    <p>
        Observaciones:
        <div style="height: 2cm; width: 20cm; border: 1pt solid #000000"></div>
    </p>

    <p style="margin-top: 1cm;">
        Firma Cliente: ____________________________________
    </p>

    <p style="text-align: center">
        <?= $view->copy ?>
    </p>

</div>