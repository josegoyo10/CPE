<style>
    .det th, .det td {
        border: 1pt solid #000000;
        font-size: 8pt !important;
    }
    .det, .taxDet {
        border-collapse: collapse;
        margin-top: 0.4cm;
    }
    .taxDet th, .taxDet td {
        border: 1pt solid #000000;
        font-size: 8pt !important;
        padding: 4px;
    }
</style>

<div style="margin-left: auto; margin-right: auto; width: 21cm; border: 1pt solid #c0c0c0; padding: 0.2cm">

    <table width="100%">
        <tr>
            <td valign="top">
                <img src="ui/view/theme/images/logo3.gif" />
            </td>
            <td>
                <strong>COTIZACION / PEDIDO PLAN SEPARALO No. <?= $view->quotation->id ?></strong>
            </td>
            <td>
                <div style="float: right">
                    <?= BARCODE_EAN13($view->quotation->barcode, 150, 60) ?>
                </div>
            </td>
        </tr>
    </table>

    <div style="border-top: 1pt solid #000000">
        <h1>Datos de la cotizaci&oacute;n</h1>
        <div style="float: left; width: 9.5cm">
            <table>
                <tr>
                    <td>No. Cotizaci&oacute;n:</td>
                    <td><?= $view->quotation->id ?></td>
                </tr>
                <tr>
                    <td>Estado:</td>
                    <td><?= $view->quotation->statusName ?></td>
                </tr>
                <tr>
                    <td>Tienda:</td>
                    <td><?= $view->quotation->storeName ?></td>
                </tr>
                <tr>
                    <td>Observaciones:</td>
                    <td>
                        <?= $view->quotation->description ?>
                        <?= $view->quotation->comments ?>
                    </td>
                </tr>
            </table>
        </div>
        <div style="float: left; width: 9.5cm">
            <table>
                <tr>
                    <td>Fecha Cotizaci&oacute;n:</td>
                    <td><?= ULang::FormatDate($view->quotation->date) ?></td>
                </tr>
                <tr>
                    <td>Atendido por:</td>
                    <td><?= $view->quotation->userName ?></td>
                </tr>
                <tr>
                    <td>V&aacute;lido hasta:</td>
                    <td><?= ULang::FormatDate($view->quotation->overdueDate) ?></td>
                </tr>
                <tr>
                    <td>Proyecto:</td>
                    <td><?= $view->quotation->projectName ?></td>
                </tr>
                <tr>
                    <td>Fecha de entrega:</td>
                    <td><?= ULang::FormatDate($view->quotation->estimatedDispatchDate) ?></td>
                </tr>
            </table>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div style="border-top: 1pt solid #000000">
        <h1>Datos del cliente</h1>
        <div style="float: left; width: 9.5cm">
            <table>
                <tr>
                    <td>C&eacute;dula o Nit:</td>
                    <td><?= $view->customer->id ?></td>
                </tr>
                <tr>
                    <td>Direcci&oacute;n:</td>
                    <td><?= $view->customer->address ?></td>
                </tr>
                <tr>
                    <td>Tel&eacute;fono:</td>
                    <td><?= $view->customer->homePhone ?></td>
                </tr>
                <tr>
                    <td>Tel&eacute;fono celular:</td>
                    <td><?= $view->customer->mobile ?></td>
                </tr>
            </table>
        </div>
        <div style="float: left; width: 9.5cm">
            <table>
                <tr>
                    <td>Nombre:</td>
                    <td>
                        <?= $view->customer->firstname ?>
                        <?= $view->customer->surname1 ?>
                        <?= $view->customer->surname2 ?>
                    </td>
                </tr>
                <tr>
                    <td>Barrio:</td>
                    <td><?= $view->customer->locationDescriptions['neighbourhood'] ?></td>
                </tr>
            </table>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div style="border-top: 1pt solid #000000">
        <h1>Direcci&oacute;n de servicio (Despachos e instalaciones)</h1>
        <div style="float: left; width: 9.5cm">
            <table>
                <tr>
                    <td>Direcci&oacute;n:</td>
                    <td><?= $view->address->address ?></td>
                </tr>
                <tr>
                    <td>Tel&eacute;fono:</td>
                    <td><?= $view->address->phone ?></td>
                </tr>
                <tr>
                    <td>Indicaciones:</td>
                    <td><?= $view->address->observations ?></td>
                </tr>
            </table>
        </div>
        <div style="float: left; width: 9.5cm">
            <table>
                <tr>
                    <td>Barrio:</td>
                    <td><?= $view->address->locationDescriptions['neighbourhood'] ?></td>
                </tr>
            </table>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div style="border-top: 1pt solid #000000">

        <table width="100%" class="det">
            <thead>
                <tr>
                    <th style="text-align: center">CODIGO</th>
                    <th style="text-align: center">TIPO</th>
                    <th style="text-align: center">DESCRIPCI&Oacute;N</th>
                    <th style="text-align: center">DESPACHO</th>
                    <th style="text-align: center">ENTREGA</th>
                    <th style="text-align: center">INSTALACI&Oacute;N</th>
                    <th style="text-align: right">PESO</th>
                    <th style="text-align: right">CANTIDAD</th>
                    <th style="text-align: right">PRECIO CON IVA</th>
                    <th style="text-align: right">TOTAL CON IVA</th>
                </tr>
            </thead>
            <tbody>
                <? foreach ($view->quotation->items as $row) { ?>
                    <tr>
                        <td style="text-align: center"><?= $row->productBarcode ?></td>
                        <td style="text-align: center"><?= $row->productType ?></td>
                        <td style="text-align: center"><?= $row->productDescription ?></td>
                        <td style="text-align: center"><?= $row->dispatchTypeDescription ?></td>
                        <td style="text-align: center"><?= ULang::FormatDate($row->deliveryDate) ?></td>
                        <td style="text-align: center"><?= $row->install ? L_YES : L_NO ?></td>
                        <td style="text-align: right"><?= $row->productWeight ?></td>
                        <td style="text-align: right"><?= $row->quantity ?></td>
                        <td style="text-align: right"><?= ULang::FormatCurrency($row->price) ?></td>
                        <td style="text-align: right"><?= ULang::FormatCurrency($row->price * $row->quantity) ?></td>
                    </tr>
                <? } ?>
                <tr>
                    <td colspan="8" style="border: none;">
                    </td>
                    <th style="text-align: left">
                        SubTotal:
                    </th>
                    <td style="text-align: right">
                        <?= ULang::FormatCurrency($view->quotation->getTotal()) ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="8" style="border: none;">
                    </td>
                    <th style="text-align: left">
                        Valor m&iacute;nimo a pagar:
                    </th>
                    <td style="text-align: right">
                        <?= ULang::FormatCurrency($view->quotation->initialQuota) ?>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>

    <p>
        El Valor de fletes cotizados esta sujeto a cambios de tarifas o cambio de cantidades en la compra.
    </p>

    <div style="border-top: 1pt solid #000000; margin-top: 0.5cm; text-align: center" align="center">
        <h1>DETALLE DE IVA INCLUIDO EN LA COTIZACI&Oacute;N</h1>
        <table class="taxDet" align="center">
            <thead>
                <tr>
                    <th>
                        Descripci&oacute;n
                    </th>
                    <th>
                        Base
                    </th>
                    <th>
                        IVA
                    </th>
                </tr>
            </thead>
            <tbody>
                <? foreach ($view->iva as $row) { ?>
                    <tr>
                        <td>
                            <?= $row['tax_rate'] ?> %
                        </td>
                        <td>
                            <?= ULang::FormatCurrency($row['base']) ?>
                        </td>
                        <td>
                            <?= ULang::FormatCurrency($row['tax']) ?>
                        </td>
                    </tr>
                <? } ?>
            </tbody>
        </table>
    </div>

    <div style="border-top: 1pt solid #000000; margin-top: 0.5cm">
        <p>
            Esta cotizaci&oacute;n es vigente en la tienda que se expide por 3 d&iacute;as a
            partir de la fecha de generaci&oacute;n; vencido este tiempo los precios ser&aacute;n
            los vigentes al d&iacute;a de la compra.
        </p>
        <p style="text-align: center">
            <strong>Favor girar cheque a nombre de EASY COLOMBIA S.A Nit: 900.155.107-1</strong>
        </p>
    </div>

    <div style="border-top: 1pt solid #000000; margin-top: 0.5cm; text-align: center">
        <strong>SERVICIO AL CLIENTE</strong>
        <p>
            Si usted tiene alguna sugerencia, queja o reclamo puede comunicarse<br />
            a los telefonos XXXXXXXX o a la direccion de internet XXXXXXXXXX<br />
            <strong>Consulte por nuestros medios de pago en el Stand de Servicios</strong>
        </p>
    </div>

    <div style="border-top: 1pt solid #000000; margin-top: 0.5cm; text-align: center">
        <strong>CONDICIONES DE INSTALACI&Oacute;N</strong>
        <p>
        </p>
    </div>

    <div style="border-top: 1pt solid #000000; margin-top: 0.5cm; text-align: center">
        <strong>CONDICIONES DE ENTREGA DE MERCANC&Iacute;A</strong>
        <div style="text-align: left">
            <ul>
                <li>
                    <strong>Retira Cliente:</strong>
                    La entrega de su mercanc&iacute;a se realizara m&aacute;ximo a los 3 d&iacute;as
                    despu&eacute;s de realizar el pago presentando la tirilla de pago
                    en el area de Despachos.
                </li>
                <li>
                    <strong>Despacho Domiclio Programado:</strong>
                    La entrega de su mercanc&iacute;a se realizara al d&iacute;a siguiente de
                    realizado el pago en lugar designado para la entrega,
                    presentando la tirilla de pago. Si usted tiene inconvenintes
                    en recibir la mercanc&iacute;a este d&iacute;a debera notificar el d&iacute;a del
                    pago en el Stand de Servicio al Cliente.
                </li>
                <li>
                    <strong>Despacho Express:</strong>
                    La entrega de su mercanc&iacute;a se realizar&aacute; el mismo d&iacute;a del
                    Pago en el lugar designado para la entrega.
                </li>
                <li>
                    <strong>Pedido Especial:</strong>
                    La entrega de su mercanc&iacute;a se realizara de acuerdo al tiempo
                    de entrega establecido por el proveedor.
                </li>
            </ul>
        </div>
    </div>

</div>