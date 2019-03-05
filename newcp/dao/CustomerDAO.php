<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');

/**
 * Description of CustomerDAO
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class CustomerDAO {

    public static function FindPersonById($id) {
        $conn = UDataSource::GetConnection();
        $id = intval($id);
        $sql = "SELECT 
            c.clie_rut as id,
            c.clie_tipo as type,
            c.clie_nombre as firstname,
            c.clie_paterno as surname1,
            c.clie_materno as surname2,
            c.clie_telefonocasa as homePhone,
            c.clie_telcontacto1 as fax,
            c.clie_telcontacto2 as mobile,
            c.clie_observacion as observation,
            c.clie_activo as isActive,
            c.clie_nombrecomercial as commercialName,
            c.clie_rutcontacto as contactRut,
            c.clie_razonsocial as legalName,
            c.clie_giro as payment,
            c.clie_email as email,
            c.clie_localizacion as locationId,
            c.clie_departamento as departmentId,
            c.clie_provincia as provinceId,
            CONCAT(c.clie_ciudad,',',c.clie_provincia,',',c.clie_departamento) as cityId,
            c.clie_localidad as localityId,
            c.clie_barrio as neighbourhoodId,
            c.clie_sexo as gender,
            c.clie_tipo_cliente as customerTypeId,
            c.clie_categoria_cliente as categoryId,
            c.clie_tipo_contribuyente as taxContributionTypeId,
            c.clie_reteica as taxReteica,
            c.clie_fuente as taxRetefuente,
            c.clie_reteiva as taxReteiva,
            c.id_tipo_doc as doctype,
            d.id_direccion as addressId,
            d.dire_direccion as address,
            d.dire_observacion as addressObservations
            FROM clientes c LEFT JOIN direcciones d
            ON d.clie_rut = c.clie_rut
            WHERE c.clie_rut = $id AND c.clie_tipo = 'p'
            AND d.dire_activo = 1 AND (d.dire_defecto = 'p' OR d.dire_defecto IS NULL)";
        $rs = $conn->Execute($sql);
        $customer = $rs->FetchNextObj();
        $rs->Close();
        return $customer;
    }

    public static function SavePerson($customer) {

        require_once includePath('business/Location.php');

        // Test for customer existence
        $isNew = !self::Exists($customer->id);

        // Insert customer SQL
        if ($isNew) {
            $SQL = "INSERT INTO clientes(
                clie_tipo,
                clie_nombre,
                clie_paterno,
                clie_materno,
                clie_telefonocasa,
                clie_telcontacto1,
                clie_telcontacto2,
                clie_observacion,
                clie_activo,
                clie_nombrecomercial,
                clie_rutcontacto,
                clie_razonsocial,
                clie_giro,
                clie_email,
                clie_localizacion,
                clie_departamento,
                clie_provincia,
                clie_ciudad,
                clie_localidad,
                clie_barrio,
                clie_sexo,
                clie_tipo_cliente,
                clie_categoria_cliente,
                clie_tipo_contribuyente,
                clie_reteica,
                clie_fuente,
                clie_reteiva,
                id_tipo_doc,
                clie_rut)
                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        }
        // Update customer sql
        else {
            $SQL = "UPDATE clientes SET
                clie_tipo=?,
                clie_nombre=?,
                clie_paterno=?,
                clie_materno=?,
                clie_telefonocasa=?,
                clie_telcontacto1=?,
                clie_telcontacto2=?,
                clie_observacion=?,
                clie_activo=?,
                clie_nombrecomercial=?,
                clie_rutcontacto=?,
                clie_razonsocial=?,
                clie_giro=?,
                clie_email=?,
                clie_localizacion=?,
                clie_departamento=?,
                clie_provincia=?,
                clie_ciudad=?,
                clie_localidad=?,
                clie_barrio=?,
                clie_sexo=?,
                clie_tipo_cliente=?,
                clie_categoria_cliente=?,
                clie_tipo_contribuyente=?,
                clie_reteica=?,
                clie_fuente=?,
                clie_reteiva=?,
                id_tipo_doc=?
                WHERE clie_rut = ?";
        }

        // Build location
        $location = new Location($customer->locationId);

        // Execute insert/update customer
        $conn = UDataSource::GetConnection();
        $conn->Execute($SQL, array(
            'p',
            $customer->firstname,
            $customer->surname1,
            $customer->surname2,
            $customer->homePhone,
            $customer->fax,
            $customer->mobile,
            null,
            1,
            null,
            null,
            null,
            null,
            $customer->email,
            $location->id,
            $location->departmentId,
            $location->provinceId,
            $location->cityId,
            $location->localityId,
            $location->neighbourhoodId,
            $customer->gender,
            $customer->customerTypeId,
            $customer->categoryId,
            $customer->taxContributionTypeId,
            $customer->taxReteica,
            $customer->taxRetefuente,
            $customer->taxReteiva,
            13,
            $customer->id
        ));

        // Test for default address
        $addressId = self::GetCustomerDefaultAddress($customer->id);

        if (!$addressId) {
            $SQL = "INSERT INTO direcciones (
                dire_localizacion,
                id_departamento,
                id_provincia,
                id_ciudad,
                id_localidad,
                id_comuna,
                clie_rut,
                dire_nombre,
                dire_direccion,
                dire_telefono,
                dire_observacion,
                dire_activo,
                dire_defecto,
                id_direccion)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        }
        else {
            $SQL = "UPDATE direcciones SET
                dire_localizacion=?,
                id_departamento=?,
                id_provincia=?,
                id_ciudad=?,
                id_localidad=?,
                id_comuna=?,
                clie_rut=?,
                dire_nombre=?,
                dire_direccion=?,
                dire_telefono=?,
                dire_observacion=?,
                dire_activo=?,
                dire_defecto=?
                WHERE id_direccion=?";
        }

        // Execute insert/update default address
        $conn->Execute($SQL, array(
            $location->id,
            $location->departmentId,
            $location->provinceId,
            $location->cityId,
            $location->localityId,
            $location->neighbourhoodId,
            $customer->id,
            'Principal',
            $customer->address,
            $customer->homePhone,
            $customer->addressObservations,
            1,
            'p',
            $addressId
        ));

        // Instanciar web service client
        require_once includePath('sdo/CustomerService.php');
        $ws = CustomerService::GetInstance();

        $wsCustomer = $ws->findById($customer->id);        
        $isNew = $wsCustomer ? false : true;

        // Obtener el cliente actualizado de la base de datos local
        $customer = self::FindPersonById($customer->id);

        // Guardar en Cliente Unico
        if ($isNew) {
            $response = $ws->create($customer);
        }
        else {
            $response = $ws->update($customer);
        }

        return $customer;
    }

    public static function GetAddresses($customerId) {
        $customerId = intval($customerId);
        $conn = UDataSource::GetConnection();
        $rs = $conn->Execute("SELECT
            d.id_direccion as id,
            concat(d.dire_nombre, ' (',
                d.dire_direccion, ') ',
                COALESCE(n.description, '')) as description
            FROM direcciones d
                LEFT JOIN cu_neighborhood n
                    ON d.id_departamento = n.id_department
                        AND d.id_provincia = n.id_province
                        AND d.id_ciudad = n.id_city
                        AND d.id_localidad = n.id_locality
                        AND d.id_comuna = n.id
            WHERE d.clie_rut = $customerId
            ORDER BY IF(d.dire_defecto='p', 0, 1)");
        return $rs->GetRows();
    }

    public static function GetAddress($addressId) {
        require_once includePath('business/Location.php');
        $addressId = intval($addressId);
        $conn = UDataSource::GetConnection();
        $rs = $conn->Execute("SELECT
            id_direccion as id,
            dire_localizacion as locationId,
            clie_rut as customerId,
            dire_nombre as name,
            dire_direccion as address,
            dire_telefono as phone,
            dire_observacion as observation,
            dire_defecto as isDefault
            FROM direcciones
            WHERE id_direccion = $addressId");
        if ($rs->EOF) return null;
        $address = $rs->FetchObj();
        $location = new Location($address->locationId);
        $address->locationDescriptions = $location->getDescriptions();
        return $address;
    }

    public static function GetCustomerDefaultAddress($customerId) {
        $customerId = intval($customerId);
        $conn = UDataSource::GetConnection();
        $rs = $conn->Execute("SELECT id_direccion as id
            FROM direcciones
            WHERE clie_rut = $customerId
            AND dire_activo = 1 AND (dire_defecto = 'p' OR dire_defecto IS NULL)");
        if ($rs->EOF) {
            return null;
        }
        else {
            $row = $rs->FetchRow();
            return $row['id'];
        }
    }

    public static function GetName($customerId) {
        $conn = UDataSource::GetConnection();
        $customerId = intval($customerId);
        $r = $conn->Execute("SELECT
            c.clie_rut as id,
            concat(c.clie_nombre, ' ', c.clie_paterno, ' ', c.clie_materno) as name
            FROM clientes c
            WHERE c.clie_rut = $customerId");
        $cust = $r->FetchObj();
        if ($cust) {
            return $cust->name;
        }
        return null;
    }

    public static function GetCustomerType($typeId) {
        $conn = UDataSource::GetConnection();
        $typeId = intval($typeId);
        $r = $conn->Execute("SELECT nombre_tipo_cliente as description
                FROM tipo_cliente WHERE id_tipo_cliente = $typeId");
        $row = $r->FetchRow();
        if ($row) {
            return $row['description'];
        }
        return null;
    }

    public static function GetCustomerCat($catId) {
        $conn = UDataSource::GetConnection();
        $catId = intval($catId);
        $r = $conn->Execute("SELECT nombre_categoria as description
                FROM tipo_categoria_cliente WHERE id_categoria = $catId");
        $row = $r->FetchRow();
        if ($row) {
            return $row['description'];
        }
        return null;
    }

    public static function Exists($customerId) {
        $conn = UDataSource::GetConnection();
        $customerId = intval($customerId);
        $r = $conn->Execute("SELECT 1 FROM clientes WHERE clie_rut = $customerId");
        return !$r->EOF;
    }
}
?>
