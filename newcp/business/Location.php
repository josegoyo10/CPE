<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */

/**
 * Description of Location
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class Location {

    public $id;
    public $departmentId;
    public $cityId;
    public $provinceId;
    public $localityId;
    public $neighbourhoodId;

    public function __construct($id = 0) {
        if ($id) {
            $this->id = str_pad($id, 14, '0', STR_PAD_LEFT);
            $this->departmentId    = substr($this->id,  0, 2);
            $this->provinceId      = substr($this->id,  2, 3);
            $this->cityId          = substr($this->id,  5, 3);
            $this->localityId      = substr($this->id,  8, 3);
            $this->neighbourhoodId = substr($this->id, 11, 3);
        }
    }

    public function getDescriptions() {
        require_once(includePath('dao/LocationDAO.php'));
        return LocationDAO::GetLocationDescriptions($this);
    }

}
?>
