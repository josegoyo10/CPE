<!--
Copyright 2008 Cencosud S.A.
Original Author Frank D. Martinez M.
-->
<div class="scrolled">
<div style="border: 1px solid #000000; padding: 5px;
    background: url(ui/view/theme/images/logo3.gif) no-repeat 5px 5px;"
    class="letterPrintedForm">
    <div style="padding-left: 80px; text-align: center;">
        <span style="font-weight: bold;">
            COMPROBANTE DE FINANCIAMIENTO
        </span>
        <dir style="text-align: left;">
            <table>
                <body>
                    <tr>
                        <td>
                            Fecha:
                        </td>
                        <td>
                            <?= ULang::FormatDate($view->datetime) ?>
                        </td>
                        <td align="right">
                            Tienda: <?= $view->storeName ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            No Identifiaci&oacute;n:
                        </td>
                        <td>
                            <?= $view->customerId ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Nombre:
                        </td>
                        <td>
                            <?= $view->customerName ?>
                        </td>
                    </tr>
                </body>
            </table>
        </dir>
    </div>
    <div>
        <table width="100%" cellspacing="5" class="report-table-simple">
            <thead>
                <tr>
                    <th align="left">
                        C&oacute;digo de barras
                    </th>
                    <th align="left">
                        Descripci&oacute;n
                    </th>
                    <th align="right">
                        Precio
                    </th>
                    <th align="right">
                        Cantidad
                    </th>
                    <th align="right">
                        Subtotal
                    </th>
                </tr>
            </thead>
            <tbody>
            <?
            foreach ($view->rows as $row) {
            ?>
                <tr>
                    <td><?= $row->productBarcode ?></td>
                    <td><?= $row->productDescription ?></td>
                    <td align="right"><?= ULang::FormatCurrency($row->price) ?></td>
                    <td align="right"><?= ULang::FormatNumber($row->quantity) ?></td>
                    <td align="right"><?= ULang::FormatCurrency($row->price * $row->quantity) ?></td>
                </tr>
            <?
            }
            ?>
            </tbody>
        </table>
    </div>
    <div align="right" style="border-top: 1px solid #000000;">
        <table cellspacing="5">
            <tbody>
                <tr>
                    <td>
                        Total:
                    </td>
                    <td align="right">
                        <?= ULang::FormatCurrency($view->credit->total + $view->credit->interests) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        Cuota inicial:
                    </td>
                    <td align="right">
                        <?= ULang::FormatCurrency($view->credit->initialQuota) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        No. Cheques:
                    </td>
                    <td align="right">
                        <?= $view->credit->numQuotas ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        Valor a financiar:
                    </td>
                    <td align="right">
                        <?= ULang::FormatCurrency($view->credit->financedValue) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        Int. de Financiaci&oacute;n:
                    </td>
                    <td align="right">
                        <?= ($view->credit->rate * 100) . '% <sup style="font-size: 7px">M.V.</sup>' ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">
                        Valor x Cheque:
                    </td>
                    <td align="right" style="font-weight: bold;">
                        <?= ULang::FormatCurrency($view->credit->quota) ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><hr /></td>
                </tr>
                <tr>
                    <td style="font-size: 14px; font-weight: bold;">Valor Intereses:</td>
                    <td style="font-size: 14px; font-weight: bold;" align="right"><?= ULang::FormatCurrency($view->credit->interests) ?></td>
                </tr>
                <tr>
                    <td>Iva Intereses:</td>
                    <td align="right"><?= ULang::FormatCurrency($view->credit->interestsTax) ?></td>
                </tr>
                <tr>
                    <td>Base Intereses:</td>
                    <td align="right"><?= ULang::FormatCurrency($view->credit->interests - $view->credit->interestsTax) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="text-align: center; padding: 5px; font-size: 14px; border-top: 1px solid #000000">
        <?= $view->text ?>
    </div>
</div>
</div>
<a href="javascript:window.print()" class="button">Imprimir</a>
<a href="javascript:window.close()" class="button">Cerrar</a>
