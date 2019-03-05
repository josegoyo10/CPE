<? if (isset($view->flowStep)) : ?>
<div class="flow-state-div">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td width="100%" class="flowStepTitle">
                    <? print ($view->flowStep+1) . '/' . $view->flowTotalSteps .
                        ' - ' . $view->flowStepTitle;
                    ?>
                </td>
                <?for ($i=0; $i<$view->flowTotalSteps; $i++) {
                    $class = $i == $view->flowStep ? 'flowStep flowStepActive' : 'flowStep';
                    $step = $i+1;
                    if ($i > 0) {
                        print "<td><div class='flowStepArrow'></div></td>";
                    }
                    print "<td><div class='$class'>$step</div></td>";
                }?>
            </tr>
        </tbody>
    </table>
</div>
<? endif ?>