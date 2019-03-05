<style>
    .contract ol li {
        text-align: justify;
    }
    .contract {
        width: 22cm;
        margin-right: auto;
        margin-left: auto;
        border: 1px solid #c0c0c0;
        background: #ffffff;
        padding: 0.5cm;
        page-break-inside: avoid;
        page-break-after: always;
    }
    .contract .logo {
        padding: 5px;
        border-bottom: 1px solid #000000;
        text-align: center;
    }
</style>


<div class="contract">

    <div class="logo">
        <img src="ui/view/theme/images/logo3.gif" />
    </div>
    <p style="text-align: center">
        <strong>CONTRATO PLAN SEPARALO No. <?= $view->quotation->barcode ?></strong>
    </p>
    <p>
        <strong>
            Fecha de Generaci&oacute;n: <?= ULang::FormatDate($view->quotation->quotationDate) ?>,
            Fecha Vencimiento: <?= ULang::FormatDate($view->quotation->overdueDate) ?>
        </strong>
    </p>
    <ol>
        <li>
            <strong>EL CLIENTE</strong> separa la mercanc&iacute;a se&ntilde;alada en la cotizaci&oacute;n <strong>No <?= $view->quotationId ?></strong> con la promesa de adquirirla dentro de los tres meses siguientes contados a partir de la fecha all&iacute; indicada, en las cantidades, precios y condiciones se&ntilde;alados.
        </li>
        <li>
            <strong>EL ALMACEN</strong>, se compromete a mantener el precio de la mercanc&iacute;a y a garantizar la disponibilidad de la misma, hasta cumplidos tres meses contados a partir de la fecha de la separaci&oacute;n de la mercanc&iacute;a y del pago inicial de la misma (Cuota Inicial).
        </li>
        <li>
            <strong>EL CLIENTE</strong> efect&uacute;a un pago inicial en dinero, por un valor equivalente <strong>m&iacute;nimo al 20%</strong> del valor total de la mercanc&iacute;a, suma que se abonar&aacute; al valor total al momento en que realice la compra prometida, el 80% restante se cancelar&aacute; m&aacute;ximo en 3 meses, contados a partir de la fecha del primer abono.
        </li>
        <li>
            <strong>EL CLIENTE</strong>, durante el transcurso de los tres meses podr&aacute; efectuar abonos adicionales al monto de la compra y solamente podr&aacute; retirar la mercanc&iacute;a una vez cancel&eacute; la totalidad del valor de la misma, en la tienda donde se realizo la separaci&oacute;n inicial de la mercanc&iacute;a.
        </li>
        <li>
            El 10% del valor total de la mercanc&iacute;a separada, es a t&iacute;tulo de <strong>"ARRAS"</strong>. En el evento en que <strong>EL CLIENTE</strong> se retracte o incumpla con este contrato, la tienda asumir&aacute; el 10% del valor total de la compra, para cubrir costos administrativos y de bodegaje de la mercanc&iacute;a separada mediante este contrato, teniendo derecho  <strong>EL CLIENTE</strong> a la devoluci&oacute;n del resto de dinero. De igual manera, cuando <strong>EL ALMACEN</strong> incumpla, <strong>EL CLIENTE</strong> tendr&aacute; derecho a que <strong>EL ALMACEN</strong> le devuelva la totalidad de lo abonado y la suma adicional equivalente a las <strong>"ARRAS"</strong> aqu&iacute; pactadas (10% del valor total de la mercanc&iacute;a Separada). En ning&uacute;n evento de devoluci&oacute;n de dinero o pago de <strong>"ARRAS"</strong>, habr&aacute; lugar al pago de intereses por ninguna de las partes.
        </li>
        <li>
            Vencido el plazo del contrato, sin que <strong>EL CLIENTE</strong>, hubiere efectuado la compra prometida, <strong>EL ALMACEN</strong> quedar&aacute; liberado de sus obligaciones y <strong>EL CLIENTE</strong> &uacute;nicamente tendr&aacute; derecho a la devoluci&oacute;n del dinero abonado, menos las <strong>"ARRAS"</strong>. En el evento en que <strong>EL CLIENTE</strong> no se presente <strong>AL ALMACEN</strong> para dicha devoluci&oacute;n, se les enviar&aacute; un telegrama inform&aacute;ndole tal situaci&oacute;n y dicho dinero se consignar&aacute; en la cuenta estipulada por el cliente en este contrato.
        </li>
        <li>
            En caso de que el titular de &eacute;ste no pueda acercarse a finalizar el contrato y retirar la mercanc&iacute;a, autoriza a __________________________________________________   identificado con cedula de ciudadan&iacute;a No.__________________ de _____________________, para que cancele el saldo final de este contrato y pueda retirar la Mercanc&iacute;a correspondiente.
        </li>
    </ol>

    <table width="100%">
        <col width="60%"/>
        <col width="40%"/>
        <tr>
            <td style="padding-bottom: 20px;" valign="top">
                <strong>DATOS DEL CLIENTE (TITULAR DEL CONTRATO):</strong>
            </td>
            <td style="padding-bottom: 20px;" valign="top">
                <strong>DATOS  DE LA TIENDA:</strong>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <p><span style="width: 120px; display: block; float: left;">Nombre:</span>____________________________</p>
                <p><span style="width: 120px; display: block; float: left;">C&eacute;dula:</span>____________________________</p>
                <p><span style="width: 120px; display: block; float: left;">Direcci&oacute;n:</span>____________________________</p>
                <p><span style="width: 120px; display: block; float: left;">Tel&eacute;fono:</span>____________________________</p>
                <p><span style="width: 120px; display: block; float: left;">No. Cuenta:</span>____________________________</p>
                <p><span style="width: 120px; display: block; float: left;">Banco:</span>____________________________</p>
            </td>
            <td valign="top">
                <p>EASY COLOMBIA S.A.</p>
                <p>NIT: 900.155.107-1</p>
                <p>DIRECCI&Oacute;N: <?= $view->quotation->storeAddress . ', ' . $view->quotation->storeCity ?></p>
            </td>
        </tr>
    </table>

    <p style="text-align: justify">
        <strong>
            EL CLIENTE DECLARA QUE HA DADO LECTURA A ESTE CONTRATO, QUE HA PLANTEADO PREGUNTAS E INQUIETUDES Y ESTAS LE HAN SIDO RESUELTAS; QUE LO HA ENTENDIDO, QUE SE ENCUENTRA DE ACUERDO CON SU CONTENIDO Y QUE POR ELLO LO FIRMA.
        </strong>
    </p>

    <table width="100%" style="margin-top: 1cm">
        <col width="50%"/>
        <col width="50%"/>
        <tr>
            <td>
                __________________________________<br />
                C.C.
            </td>
            <td>
                __________________________________<br />
                C.C.
            </td>
        </tr>
        <tr>
            <td>
                FIRMA DEL CLIENTE
            </td>
            <td>
                FIRMA DEL JEFE DE TIENDA
            </td>
        </tr>
    </table>

    <p style="text-align: center">
        <?= $view->copy ?>
    </p>

</div>