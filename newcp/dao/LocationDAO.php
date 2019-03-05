<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');

/**
 * Data Access Object, manages locations like cities, neighbourhoods, regions, etc...
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class LocationDAO {
    
    public static function GetNeighbourhoodsByCity($cityId, $provinceId, $departmentId) {
        $conn = UDataSource::GetConnection();
        $departmentId = intval($departmentId);
        $provinceId = intval($provinceId);
        $cityId = intval($cityId);
        $strCityId = "$cityId,$provinceId,$departmentId";
        $rs = $conn->Execute("SELECT DISTINCT
            N.LOCATION AS id,
            CONCAT(N.DESCRIPTION, ' - ', L.DESCRIPTION) AS description,
            '$strCityId' as cityId
            FROM cu_neighborhood N
            LEFT JOIN cu_locality L 
                ON L.ID = N.ID_LOCALITY AND L.ID_PROVINCE = $provinceId
                AND L.ID_DEPARTMENT = $departmentId
                AND L.ID_CITY = $cityId
            WHERE N.ID_CITY = $cityId AND N.ID_DEPARTMENT = $departmentId
                AND N.ID_PROVINCE = $provinceId
            ORDER BY N.DESCRIPTION");
        return $rs->GetRows();
    }

    public static function GetCitiesByDepartment($departmentId) {
        $conn = UDataSource::GetConnection();
        $departmentId = intval($departmentId);
        $rs = $conn->Execute("SELECT
            CONCAT(C.ID, ',', C.ID_PROVINCE, ',', C.ID_DEPARTMENT) AS id,
            C.ID_DEPARTMENT AS departmentId,
            C.DESCRIPTION AS description
            FROM cu_city C
            WHERE C.ID_DEPARTMENT = $departmentId
            ORDER BY C.DESCRIPTION");
        return $rs->GetRows();
    }

    public static function GetAllDepartments() {
        $conn = UDataSource::GetConnection();
        $rs = $conn->Execute('SELECT
            D.ID AS id,
            D.DESCRIPTION AS description
            FROM cu_department D ORDER BY D.DESCRIPTION');
        return $rs->GetRows();
    }

    public static function GetLocationDescriptions($location) {
        $sql = "SELECT
            n.description as neighbourhood,
            l.description as locality,
            c.description as city,
            p.description as province,
            d.description as department
            FROM cu_neighborhood n
            LEFT JOIN cu_locality l
                ON n.id_department = l.id_department
                    AND n.id_province = l.id_province 
                    AND n.id_city = l.id_city
                    AND n.id_locality = l.id
            LEFT JOIN cu_city c
                ON l.id_department = c.id_department 
                    AND l.id_province = c.id_province
                    AND l.id_city = c.id
            LEFT JOIN cu_province p
                ON c.id_department = p.id_department
                    AND c.id_province = p.id
            LEFT JOIN cu_department d
                ON p.id_department = d.id
            WHERE n.id_department = " . intval($location->departmentId) .
                " AND n.id_province = " . intval($location->provinceId) .
                " AND n.id_city = " . intval($location->cityId) .
                " AND n.id_locality = " . intval($location->localityId) .
                " AND n.id = " . intval($location->neighbourhoodId);
        $conn = UDataSource::GetConnection();
        $rs = $conn->Execute($sql);
        if ($rs->EOF) {
            $rs->Close();
            return null;
        }
        else {
            $data = $rs->fields;
            $rs->Close();
            return $data;
        }
    }

}
?>
