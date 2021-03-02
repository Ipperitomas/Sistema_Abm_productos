<?php
/*
	Clase para el manejo de las cuentas del libro diario
	Created: 2020-04-03
	Author: DriverOp
*/

require_once(DIR_model."class.fundation.inc.php");

$this_file = substr(__FILE__,strlen(DIR_BASE))." ";

class cCuenta extends cModels {
	
	private $tabla_cuentas = TBL_cuentas;
	private $tabla_cuentas_grupos = TBL_cuentas_grupos;

	public function __construct() {
		parent::__construct();
		$this->tabla_principal = $this->tabla_cuentas;
		$this->actual_file = __FILE__;
	}

	public function Get($id) {
		$result = false;
		$this->Reset();
		if (SecureInt($id,null) == null) { throw new Exception(__LINE__." ID debe ser un número."); }
		$this->sql = "SELECT `cuenta`.*, `grupos`.`nombre` AS `nombre_grupo` FROM ".SQLQuote($this->tabla_cuentas)." AS `cuenta` LEFT JOIN ".SQLQuote($this->tabla_cuentas_grupos)." AS `grupos` ON `grupos`.`id` = `cuenta`.`grupo_id` WHERE `cuenta`.`id` = ".$id;
		if (parent::Get($id)) {
			$this->ParseRecord();
			$this->ParseFechas();
			$result = true;
		}
		return $result;
	}

	public function Reset() {
		$this->existe = false;
		$this->encontrado = false;
		try {
			$fields = $this->GetColumnsNames($this->tabla_principal);
			if (CanUseArray($fields)) {
				foreach ($fields as $field) {
					$this->$field = null;
				}
			}
			$this->raw_record = null;
		} catch(Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
	}

}
?>