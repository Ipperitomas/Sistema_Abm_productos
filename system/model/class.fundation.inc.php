<?php
/*
		Foundation class.
*/
require_once(DIR_includes."class.logging.inc.php");
require_once(DIR_model."class.dbutili.2.inc.php");
if (defined('DBNAME') == false) {
	require_once(DIR_config."database.config.inc.php");
}
require_once(DIR_includes . "class.sidekick.inc.php");
if (!isset($db_link)) {
	$db_link = NULL;
}

class cModels extends cDb {

	public $existe = false;
	public $encontrado = false;
	public $error = false;
	public $msgerr = null;
	public $raw_record = array();
	public $DebugOutput = true;
	public $sql = '';
	public $tabla_principal = null;
	public $usuario = null;
	public $aliasConvertir = null;
	public $decAsCurrency = false;
	private $res = null;
	public $actual_file = __FILE__;

	public function __construct() {
		global $db_link;
		parent::__construct();
		try {
			$this->Connect(DBHOST, DBNAME, DBUSER, DBPASS, DBPORT);
			if ($this->error) {
				throw new Exception(__LINE__." DBErr: ".$this->errmsg);
			}
			if ($db_link == NULL) {
				$db_link = $this->link;
			} else {
				$this->link = $db_link;
				$this->opened = true;
			}
			if (defined("DEC_AS_CURRENCY")) {
				$this->decAsCurrency = DEC_AS_CURRENCY;
			}
			
		} catch(Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
	} // __construct

	public function ParseJson($data) {
		if (is_object($data)) return $data;
		$result = new StdClass();
		if (!empty(trim($data))) {
			try {
				$result = json_decode($data);
				if (json_last_error() != JSON_ERROR_NONE) {
					throw new Exception(__LINE__." ".ShowLastJSONError(json_last_error(), true));
				}
			} catch(Exception $e) {
				$this->SetError(__METHOD__,$e->GetMessage());
				return false;
			}
		}
		return $result;
	} // ParseJson

	function SetError($method, $msg) {
		$this->error = true;
		$this->msgerr = $msg;
		if (in_array($this->errno,[1054,1064,1146])) {
			$msg .= " SQL: ".$this->lastsql;
		}
		$line = basename($this->actual_file)." -> ".$method.". ".$msg;
		if (DEVELOPE and $this->DebugOutput) { EchoLogP(htmlentities($line)); }
		cLogging::Write($line);
	} // SetError
	
	function Query($sql, $contar = false, $tipos = false) {
		if ($this->decAsCurrency) { $tipos = ['type','length','decimals']; }
		$this->res = parent::Query($sql, $contar, $tipos);
		if ($this->error) { throw new Exception(__LINE__." DBErr: ".$this->errmsg); }
		return $this->res;
	}
	
	function RawQuery($sql) {
		$this->res = parent::RawQuery($sql);
		if ($this->error) { throw new Exception(__LINE__." DBErr: ".$this->errmsg); }
		return $this->res;
	}

	function FirstQuery($sql, $contar = false, $tipos = false) {
		if ($this->decAsCurrency) { $tipos = ['type','length','decimals']; }
		$this->res = parent::Query($sql, $contar, $tipos);
		if ($this->error) { throw new Exception(__LINE__." DBErr: ".$this->errmsg); }
		return parent::First($this->res);

	}

	function First($res = NULL) {
		if ($res == NULL) { $res = $this->res; }
		$result = parent::First($res);
		if ($this->error) { throw new Exception(__LINE__." DBErr: ".$this->errmsg); }
		if ($this->decAsCurrency) { $result = $this->transformDecAsCurrency($result); }
		return $result;
	}

	function Next($res = NULL) {
		if ($res == NULL) { $res = $this->res; }
		$result = parent::Next($res);
		if ($this->error) { throw new Exception(__LINE__." DBErr: ".$this->errmsg); }
		if ($this->decAsCurrency) { $result = $this->transformDecAsCurrency($result); }
		return $result;
	}

	function Last($res = NULL) {
		if ($res == NULL) { $res = $this->res; }
		$result = parent::Last($res);
		if ($this->error) { throw new Exception(__LINE__." DBErr: ".$this->errmsg); }
		if ($this->decAsCurrency) { $result = $this->transformDecAsCurrency($result); }
		return $result;
	}
	
	function Seek($num, $res = NULL) {
		if ($res == NULL) { $res = $this->res; }
		$result = parent::Seek($num, $res);
		if ($this->error) { throw new Exception(__LINE__." DBErr: ".$this->errmsg); }
		if ($this->decAsCurrency) { $result = $this->transformDecAsCurrency($result); }
		return $result;
	}
	
	function Update($tabla, $lista, $where = "") {
		$result = parent::Update($tabla, $lista, $where);
		if ($this->error) { throw new Exception(__LINE__." DBErr: ".$this->errmsg); }
		return $result;
	}
	
	function Insert($tabla, $lista) {
		$result = parent::Insert($tabla, $lista);
		if ($this->error) { throw new Exception(__LINE__." DBErr: ".$this->errmsg); }
		return $result;
	}
	
	function GetNumRows($res = NULL) {
		if ($res == NULL) { $res = $this->res; }
		$result = parent::GetNumRows($res);
		if ($this->error) { throw new Exception(__LINE__." DBErr: ".$this->errmsg); }
		return $result;
	}
	
/**
* Summary. Transforma los campos de tipo 246, tamaño 13 y decimales 4 en moneda. Agrega un nuevo campo 'cur_<nombre del campo>'
* @param array $result Array con el registro leído.
* @return array Los campos del registro leído con los campos correspondientes cambiados.
*/
	private function transformDecAsCurrency($result) {
		if ($result and $this->decAsCurrency and $this->tipoCampos) {
			if (is_array($result) and is_array($this->tipoCampos)) {
				$moneda = $this->aliasConvertir;
				if(!empty(($result["tipo_moneda"]))){
					$moneda = $result["tipo_moneda"];
				}
				foreach($result as $fieldName => $fieldValue) {
					if (array_key_exists($fieldName, $this->tipoCampos)) {
						$workField = $this->tipoCampos[$fieldName];
						if (isset($workField['type']) and ($workField['type'] == 246)) {
							if (isset($workField['length']) and ($workField['length'] >= 13)) {
								if (isset($workField['decimals']) and ($workField['decimals'] == 4)) {
									$result['orig_'.$fieldName] = $fieldValue;
									$result[$fieldName] = cSidekick::ConvertMoneyTo($this->aliasConvertir,$moneda,$fieldValue);
								}
							}
						}
					}
				}
			}
		}
		return $result;
	}

	function GetColumnsNames($table = null) {
		$result = array();
		try {
			$table = substr(trim($table),0,64);
			if (empty($table)) { throw new Exception(__LINE__." Table no puede ser nulo o vacío."); }
			$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE `table_schema` = '".DBNAME."' AND `table_name` = '".$this->RealEscape($table)."'";
			$this->Query($sql);
			while($row = self::Next()){
				$result[] = $row['COLUMN_NAME'];                
			}
			return $result;
		}
		catch(Exception $e) {
			trigger_error('DBErr: '.$e->getMessage(), E_USER_ERROR);
		}
		return $result;
	}
	
	function BeginTransaction() {
		$this->Query("START TRANSACTION;");
	}

	function Commit() {
		$this->Query("COMMIT;");
	}

	function Rollback() {
		$this->Query("ROLLBACK;");
	}
	
	function Get($id) {
		$result = false;
		$this->encontrado = false;
		$this->existe = false;
		try {
			if ($this->decAsCurrency and empty($this->aliasConvertir)) {
				$this->aliasConvertir = cSideKick::GetParam(null,'tipo_moneda');
			}
			if (empty($this->sql)) {throw new Exception(__LINE__." No query provided."); }
			$this->Query($this->sql);
			if ($fila = $this->First()) {
				$result = true;
				$this->raw_record = $fila;
				$this->encontrado = true;
				$this->existe = true;
			}
		} catch(Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
		return $result;
	}
/*
	Determina si un registro de la tabla principal existe.
	$campo es el campo a buscar, $valor el valor en ese campo. $tabla la tabla donde buscar. Por omisión es $tabla_principal.
*/
	public function Existe($campo, $valor, $tabla = null) {
		$result = false;
		try {
			if (empty($tabla)) { $tabla = $this->tabla_principal; }
			else { $tabla = $this->RealEscape($tabla); }
			$campo = $this->RealEscape($campo);
			$valor = $this->RealEscape($valor);
			$sql = "SELECT * FROM ".SQLQuote($tabla)." WHERE ".SQLQuote($campo)." = '".$valor."' LIMIT 1;";
			$this->Query($sql);
			$result = $this->First();
		} catch(Exception $e) {
			$this->SetError(__METHOD__,$e->getMessage());
		}
		return $result;
	}
	public function ParseRecord() {
		if ($this->raw_record) {
			foreach($this->raw_record as $key => $value) {
				if (!empty($key)) {
					$this->$key = $value;
				}
			}
			if (isset($this->metadata)) {
				$this->metadata = $this->ParseJson($this->metadata);
			}
			if (isset($this->data)) {
				$this->data = $this->ParseJson($this->data);
			}
			if (isset($this->opciones)) {
				$this->opciones = $this->ParseJson($this->opciones);
			}
		}
	} // ParseRecord

	public function ParseFechas() {
		foreach ($this as $key => $value) {
			if ((substr($key,0,5) == 'fecha') or (substr($key,0,9) == 'sys_fecha')) {
				$newkey = 'txt_'.$key;
				$newkey_short = 'txt_'.$key.'_short';
				if (strpos($key,'hora') !== false ) {
					$this->$newkey = cFechas::SQLDate2Str($value);
					$this->$newkey_short = cFechas::SQLDate2Str($value, CDATE_SHORT);
				} else {
					$this->$newkey = cFechas::SQLDate2Str($value, CDATE_IGNORE_TIME);
					$this->$newkey_short = cFechas::SQLDate2Str($value, CDATE_SHORT+CDATE_IGNORE_TIME);
				}
			}
		}
	} // ParseFechas

/**
* Summary. Restablece a null todos las propiedades de la instancia del objeto a partir del nombre de los campos de la tabla de la base de datos.
*/
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
/**
* Summary. Esto recorre el array pasado por parámetro en busca de valores que parecen ser JSON y si es así, los decodifica sobre el propio elemento. Devuelve el mismo array modificado... o no.
*/
	public function ParseTextFields($fila = null, $force = []) {
		try {
			if (is_null($fila)) { $fila = $this->raw_record; }
			if (CanUseArray($fila)) {
				foreach($fila as $key => $value) {
					if (is_string($value)) {
						$value = trim($value);
						if (preg_match('/^\[?\s*{[\b"\[\s]??(.*?)}\s*\]?$/s',$value)) {
							$fila[$key] = json_decode($value);
						}
					}
				}
			}
		} catch(Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
		return $fila;
	} // ParseTextFields

} // Class cModels
?>