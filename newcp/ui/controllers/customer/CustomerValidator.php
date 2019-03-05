<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('lib/util/web/UValidator.php');

ULang::Load('Validators');

/**
 * Description of CustomerValidator
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class CustomerValidator extends UValidator {

    public static function GetInstance() {
        return new CustomerValidator;
    }

    public function validate($customer, &$errors) {
        $valid = true;
        $valid &= $this->checkInt($customer, 'id', $errors);
        $valid &= $this->checkRequired($customer, 'id', $errors);
        $valid &= $this->checkRequired($customer, 'firstname', $errors);
        $valid &= $this->checkRequired($customer, 'address', $errors);
        $valid &= $this->checkRequired($customer, 'surname1', $errors);
        $valid &= $this->checkRequired($customer, 'surname2', $errors);
        $valid &= $this->checkRequired($customer, 'departmentId', $errors);
        $valid &= $this->checkRequired($customer, 'cityId', $errors);
        $valid &= $this->checkRequired($customer, 'locationId', $errors);
        $valid &= $this->checkRequired($customer, 'homePhone', $errors);
        $valid &= $this->checkIntRange($customer, 'gender', 1, 2, $errors);
        $valid &= $this->checkRequired($customer, 'gender', $errors);
        $valid &= $this->checkInt($customer, 'customerTypeId', $errors);
        $valid &= $this->checkRequired($customer, 'customerTypeId', $errors);
        $valid &= $this->checkRequired($customer, 'taxContributionTypeId', $errors);
        return $valid;
    }
}
?>
