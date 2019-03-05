<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');

/**
 * Description of UHtml
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class UHtml {

    public static function BooleanOptions($value = false, $threeValued = false) {
        if ($threeValued) {
            print '<option value="-1"></option>';
        }
        if ($value) {
            print '<option value="1" selected="selected">Si</option><option value="0">No</option>';
        }
        else {
            print '<option value="1">Si</option><option value="0" selected="selected">No</option>';
        }
    }

    public static function FormSubmitButton($label, $form, $icon = '') {
        print "<a class='button $icon' onclick='$(\"#$form\").submit()'>$label</a>";
    }

    public static function ListItems(&$list, $selected, $valueAttr, $labelAttr, $first = null) {
        if (is_array($first)) {
            print "<option value=\"{$first[0]}\">{$first[1]}</option>\n";
        }
        foreach ($list as $item) {
            $sel = '';
            $val = $item[$valueAttr];
            $label = $item[$labelAttr];
            if ($val == $selected) {
                $sel = 'selected="selected"';
            }
            print "<option value=\"$val\" $sel>$label</option>\n";
        }
    }

    public static function CheckBox($name, $value, $enabled = true) {
        print "<input type='checkbox' name='$name' id='$name' value='1' ";
        print !$enabled ? 'disabled="disabled"' : '';
        print $value ? 'checked="checked"' : '';
        print " />";
    }

    public static function SimpleButtonLink($label, $url, $icon = '') {
        print "<a class='button $icon' href='$url'>$label</a>";
    }

    public static function LIList($list) {
        foreach ($list as $item) {
            print '<li>'.$item.'</li>';
        }
    }

    public static function JQueryValidMsgs($messages, $prefix = '') {
        if ($prefix != '') { $prefix = $prefix . '\\\\:'; }
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function() {
                <?
                foreach ($messages as $name => $msg) {
                    $msg = str_replace("'", "\\'", $msg);
                    if ($name != 'global') {
                        print "jQuery('#$prefix$name').addClass('input-error').attr('title', '$msg');\n";
                    }
                }
                ?>
            });
        </script>
        <?
    }
}
?>
