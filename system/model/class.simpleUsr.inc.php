<?php

/*
	Clase cSimpleUsr
	Author: DriverOp
	Created: 2016-08-11
	Desc: Esta clase es para tratar un usuario como si fuera un objeto simple.
	Modif: 2019-04-03
		Agregado método SetError.
	Modif: 2019-05-07
		Agregado GetByUsername y SetData
	Modif: 2019-11-26
		Ahora deriva de cModel para armonizar con el resto de las clases del core.
	Modif: 2019-11-26
		Sorry, mejor derivar de cUsuarios.
	Modif: 2020-03-26
		Agregado método GetByTel()
*/

require_once(DIR_includes."class.fechas.inc.php");
require_once(DIR_includes."class.logging.inc.php");
require_once(DIR_includes."class.sidekick.inc.php");
require_once(DIR_model."class.fundation.inc.php");
require_once(DIR_model."class.usuarios.inc.php");

class cSimpleUsr extends cUsuarios {

	public $tabla_usuarios = TBL_backend_usuarios;
	private $tabla_perfiles = TBL_backend_perfiles;
	private $tabla_perfiles_contenidos = TBL_backend_perfiles_contenidos;
	private $tabla_recovery = TBL_backend_recovery;
	private $tabla_permisos = TBL_backend_permisos;

	function __construct(){
		parent::__construct();
		$this->tabla_principal = $this->tabla_usuarios;
	} // constructor

	public function Get($id) {
		$result = false;
		$this->existe = false;
		$this->raw_record = array();
		if (is_numeric($id)) {
			try {
				$sql = "SELECT ".SQLQuote($this->tabla_usuarios).".*, ".SQLQuote($this->tabla_perfiles).".`nombre` AS `perfil_nombre`, ".SQLQuote($this->tabla_perfiles).".`alias` AS `perfil_alias` FROM ".SQLQuote($this->tabla_usuarios)." LEFT JOIN ".SQLQuote($this->tabla_perfiles)." ON ".SQLQuote($this->tabla_perfiles).".`id` = ".SQLQuote($this->tabla_usuarios).".`perfil_id` WHERE ".SQLQuote($this->tabla_usuarios).".`id` = '".$id."';";
				//EchoLog($sql);
				$this->Query($sql);
				if ($this->error) { throw new Exception(__LINE__." DBErr: ".$this->errmsg); }
				if ($result = $this->first()) {
					$this->raw_record = $result;
					$this->existe = true;
					$this->SetData($result);
					$result = $this;
				}
			} catch(Exception $e) {
				$this->SetError(__METHOD__,$e->GetMessage());
			}
		}
		return $result;
	}

	public function GetByUsername($username) {
		$result = false;
		$this->existe = false;
		$this->raw = array();
		try {
			$username = $this->RealEscape(mb_substr($username,0,32));
			$sql = "SELECT * FROM `".$this->tabla_usuarios."` WHERE LOWER(`username`) = LOWER('".$username."') AND `estado` != 'ELI';";
			$this->Query($sql);
			if ($this->error) { throw new Exception(__LINE__." DBErr: ".$this->errmsg); }
			if ($result = $this->first()) {
				$this->raw = $result;
				$this->existe = true;
				$this->SetData($result);
				$result = $this;
			}
		} catch(Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
		return $result;
	}

	public function GetByEmail($email) { // Util para recuperar la contraseña de un usuario
		$result = false;
		$this->existe = false;
		$this->raw = array();
		try {
			$email = $this->RealEscape(mb_substr($email,0,75));
			$sql = "SELECT * FROM `".$this->tabla_usuarios."` WHERE LOWER(`email`) = LOWER('".$email."');";
			$this->Query($sql);
			if ($result = $this->First()) {
				$this->raw = $result;
				$this->existe = true;
				$this->SetData($result);
				$result = $this;
			}
		} catch(Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
		return $result;
	} // GetByEmail

	public function GetByJumble($jumble) { // Qué usuario tiene este jumble?, el jumble es válido?.
		$result = false;
		$this->existe = false;
		$this->raw = array();
		try {
			$this->Delete($this->tabla_recovery,"`estado` = 'ELI'");
			if ($this->error) { throw new Exception(__LINE__.' DBErr: '.$this->errmsg); }
			$email = $this->RealEscape(mb_substr($jumble,0,32));
			$sql = "SELECT `usuario_id` FROM `".$this->tabla_recovery."` WHERE LOWER(`code`) = LOWER('".$jumble."');";
			$this->Query($sql);
			if ($this->error) { throw new Exception(__LINE__." DBErr: ".$this->errmsg); }
			if ($result = $this->First()) {
				$this->Get($result['usuario_id']);
				$result = true;
			}
		} catch(Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
		return $result;
	} // GetByJumble
	
	public function GetByTel($tel) { // Qué usuario tiene el número de telefono indicado?
		$result = false;
		$this->existe = false;
		$this->raw = array();
		try {
			$tel = $this->RealEscape(mb_substr($tel,0,25));
			$sql = "SELECT * FROM `".$this->tabla_usuarios."` WHERE LOWER(`tel`) = LOWER('".$tel."');";
			$this->Query($sql);
			if ($this->error) { throw new Exception(__LINE__." DBErr: ".$this->errmsg); }
			if ($result = $this->first()) {
				$this->raw = $result;
				$this->existe = true;
				$this->SetData($result);
				$result = $this;
			}
		} catch(Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
		return $result;
	}
	
/* 
	Guarda en la tabla los datos de $data.
	$data debe ser un array donde el índice es el nombre del campo y el valor el valor a almacenar en ese campo.
	    ************** NO VERIFICA LA VALIDEZ DE LOS DATOS ************
*/
	public function Save($data, $usuario_id = NULL) {
		$result = false;
		if ($this->existe) {
			if (CanUseArray($data)) {
				$reg = array();
				try {
					foreach($data as $key => $value) {
						$key = $this->RealEscape($key);
						$value = $this->RealEscape($value);
						if ($key != 'id') { // Just a precaution.
							$reg[$key] = $value;
						}
					}
					$reg['fecha_modif'] = Date('Y-m-d H:i:s');
					$reg['usuario_id'] = $usuario_id;
					$this->Update($this->tabla_usuarios, $reg, "`id` = ".$this->id);
					$result = true;
				} catch(Exception $e) {
					$this->SetError(__METHOD__,$e->getMessage());
				}
			}
		}
		return $result;
	}
/* 
	Crea un usuario nuevo usando los datos de $data
	$data debe ser un array donde el índice es el nombre del campo y el valor el valor a almacenar en ese campo.
	    ************** NO VERIFICA LA VALIDEZ DE LOS DATOS ************
*/
	public function Create($data, $usuario_id = NULL) {
		$result = false;
		if (CanUseArray($data)) {
			$reg = array();
			try {
				foreach($data as $key => $value) {
					if ($key == 'password') {
						$value = $this->GeneratePassword($value);
					} else {
						$key = $this->RealEscape($key);
						$value = $this->RealEscape($value);
					}
					if ($key != 'id') { // Just a precaution.
						$reg[$key] = $value;
					}
				}
				$reg['fecha_alta'] = Date('Y-m-d H:i:s');
				$reg['fecha_modif'] = Date('Y-m-d H:i:s');
				$reg['usuario_id'] = $usuario_id;
				$this->Insert($this->tabla_usuarios, $reg);
				$result = $this->last_id;
				$this->Get($result);
			} catch(Exception $e) {
				$this->SetError(__METHOD__,$e->getMessage());
			}
		}
		return $result;
	}
	
	public function SetData($data) {
		global $usuarios_nivel_int;
		foreach($data as $key => $value) {
			$this->$key = $value;
		}
		if (!empty($data['data'])) {
			$this->data = json_decode($data['data']);
		}
		if (!empty($data['opciones'])) {
			$this->opciones = json_decode($data['opciones']);
		}
		$this->nombre_apellido = $this->nombre." ".$this->apellido;
		$this->NomApe = $this->nombre_apellido;
		$this->nivel_int = $usuarios_nivel_int[$this->nivel];
		$this->perfil = $this->GetPerfil($data['perfil_id']);
		$this->tel = cSideKick::ParseTel($this->tel, true);
	}
	
	public function GetLastLogin() {
		$result = null;
		if ($this->existe) {
			try {
				$sql = "SELECT * FROM ".SQLQuote(TBL_backend_sesiones)." WHERE `usuario_id` = ".$this->id." ORDER BY `fecha_hora` DESC LIMIT 1;";
				$this->Query($sql);
				if ($fila = $this->First()) {
					$result = new StdClass();
					foreach($fila as $key => $value) {
						$result->$key = $value;
					}
					if (!empty($result->fecha_hora)) {
						$result->fecha_hora_txt = cFechas::SQLDate2Str($result->fecha_hora);
						$result->fecha_hora_txt_short = cFechas::SQLDate2Str($result->fecha_hora, CDATE_SHORT);
						$result->hace = cFechas::TiempoTranscurrido($result->fecha_hora, Date('Y-m-d H:i:s'));
					}
				}
			} catch(Exception $e) {
				$this->SetError(__METHOD__,$e->getMessage());
			}
		}
		return $result;
	}
	
	public function SetJumble($len = 32) {
		$result = false;
		try {
			if (!$this->existe) { throw new Exception(__LINE__.' Tiene que haber un usuario activo para continuar esta operación.'); }
			$jumble = md5($this->username.$this->email.date('YmdHis'));
			$jumble = substr($jumble,0,$len);
			$this->Delete($this->tabla_recovery,"`estado` = 'ELI' OR `usuario_id` = ".$this->id);
			if ($this->error) { throw new Exception(__LINE__.' DBErr: '.$this->errmsg); }
			$this->Insert($this->tabla_recovery,
				array(
					'usuario_id'=>$this->id,
					'email'=>$this->email,
					'code'=>$jumble,
					'fecha'=>cFechas::Ahora()
				)
			);
			if ($this->error) { throw new Exception(__LINE__.' DBErr: '.$this->errmsg); }
			$result = $jumble;
			//throw new Exception('');
		} catch (Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
		return $result;
	}
/*
	Devuelve el jumble del usuario actual, si es que existe.
*/	
	public function GetJumble() {
		$result = false;
		try {
			if (!$this->existe) { throw new Exception(__LINE__.' Tiene que haber un usuario activo para continuar esta operación.'); }
			$aux = strtotime('-1 hour', strtotime(cFechas::Ahora()));
			$this->Delete($this->tabla_recovery,"`fecha` < '".date('Y-m-d H:i:s', $aux)."'");
			if ($this->error) { throw new Exception(__LINE__.' DBErr: '.$this->errmsg); }
			$sql = "SELECT * FROM ".SQLQuote($this->tabla_recovery)." WHERE `usuario_id` = ".$this->id." AND `estado` = 'HAB'";
			$this->Query($sql);
			if ($this->error) { throw new Exception(__LINE__.' DBErr: '.$this->errmsg); }
			if ($fila = $this->First()) {
				$result = $fila;
			}
		} catch (Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
		return $result;
	}
/*
	Esto crea o recrea los permisos de acceso a los contenidos basados en el perfil del usuario.
*/
	public function CreateUserPermissions($id_usuario_actual = null) {
		$result = false;
		try {
			if (!$this->existe) { throw new Exception(__LINE__.' Tiene que haber un usuario activo para continuar esta operación.'); }
			if (!empty($this->perfil_id)) {
				$this->Delete($this->tabla_permisos, "`usuario_id` = ".$this->id);
				$sql = "SELECT * FROM ".SQLQuote($this->tabla_perfiles_contenidos)." WHERE `perfil_id` = ".$this->perfil_id." AND `estado` = 'HAB';";
				$res = $this->Query($sql);
				if ($fila = $this->First($res)) {
					do {
						$reg = array(
							'usuario_id'=>$this->id,
							'contenido_id'=>$fila['contenido_id'],
							'fechahora'=>Date('Y-m-d H:i:s'),
							'usralta_id'=>$id_usuario_actual
						);
						$this->Insert($this->tabla_permisos, $reg);
					}while($fila = $this->Next($res));
				}
			} else {
				cLogging::Write(__METHOD__.' El usuario '.$this->id.' no tiene ningún perfil asignado.');
			}
		} catch (Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
		return $result;
	}
	
	private function GetPerfil($perfil_id) {
		$result = new stdClass();
			if (!is_null($perfil_id) and !empty($perfil_id) and is_numeric($perfil_id)) {
			try {
				$sql = "SELECT * FROM ".SQLQuote($this->tabla_perfiles)." WHERE `id` = ".$perfil_id;
				$this->Query($sql);
				if ($fila = $this->First()) {
					$fila['data'] = json_decode($fila['data']);
					foreach ($fila as $key => $value) {
						$result->$key = $value;
					}
				}
			} catch(Exception $e) {
				$this->SetError(__METHOD__,$e->getMessage());
			}
		} else {
			$result->nombre = 'Todos';
		}
		return $result;
	}
	
	private function GetSucursal() {
		$result = false;
		$this->sucursal = new StdClass();
		if ($this->existe) {
			if (!empty($this->sucursal_id) and is_numeric($this->sucursal_id) and ($this->sucursal_id > 0)) {
				try {
					$sql = "SELECT * FROM `".$this->tabla_sucursales."` WHERE `id` = ".$this->sucursal_id;
					$this->Query($sql);
					if ($this->error) { throw new Exception(__LINE__." DBErr: ".$this->errmsg); }
					if ($fila = $this->first()) {
						$result = $fila;
						foreach ($fila as $key => $value) {
							$this->sucursal->$key = $value;
						}
						if (!empty($this->sucursal->data)) {
							$this->sucursal->data = json_decode($this->sucursal->data);
						}
					}
				} catch(Exception $e) {
					$this->SetError(__METHOD__,$e->GetMessage());
				}
			}
		}
		return $result;
	}

} // class
?>