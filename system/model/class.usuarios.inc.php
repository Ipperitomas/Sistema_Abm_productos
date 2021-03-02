<?php
/*
	Author: DriverOp
	Created: 2018-10-18
	Desc: Clase para el manejo de usuarios, no funciona por sí misma, hay que derivar otra clase a partir de ésta.
	Modif: 2018-10-23
	Desc: Cuando $salt está vacío, no salar la contraseña.
	Modif: 2019-10-11
	Desc: Reparado bug. Cuando en el registro de la DB no existía el campo 'tsession' en 'opciones', la sesión nunca se hacía válida.
	Modif: 2019-10-17
	Desc: Derivar de cFundation.
*/
require_once(DIR_includes."class.logging.inc.php");
require_once(DIR_model."class.fundation.inc.php");

$usuarios_nivel_int = array('OWN'=>1,"ADM"=>2,"USR"=>3);

class cUsuarios extends cModels {
	protected $salt = "";
	protected $SecretKey = null;
	protected $session_name = null;
	protected $PasswordMaxLenght = 32;
	public $tsession = 0;
	
	private $session_record = null;
	
	public $DebugOutput = false;
	
	
	
	public function __construct() {
		parent::__construct();
		$this->actual_file = __FILE__;
	}

	
	public function Get($id) {
		$result = false;
		$this->Reset();
		if (SecureInt($id,null) == null) { throw new Exception(__LINE__." ID debe ser un número."); }
		$this->sql = "SELECT * FROM ".SQLQuote($this->tabla_usuarios)." WHERE `id` = ".$id;
		if (parent::Get($id)) {
			$this->ParseRecord();
			$result = true;
			if (!empty($this->opciones)) {
				foreach($this->opciones as $key => $value) {
					$this->$key = $value;
				}
			}
			if (!empty($this->data)) {
				foreach($this->data as $key => $value) {
					$this->$key = $value;
				}
			}
			$this->SetUserData();
		}
		return $result;
	}

/*
	Determina si existe un usuario según su username
*/
	public function GetByUsername($username) {
		$result = false;
		try {
			if (empty($username)) { throw new Exception("No puedo buscar a un usuario anónimo."); }
			$username = substr($username,0,32);
			$username = $this->RealEscape($username);
			$sql = "SELECT * FROM ".SQLQuote($this->tabla_usuarios)." WHERE LOWER(`username`) = LOWER('".$username."') AND `estado` != 'ELI' LIMIT 1;";
			
			$this->Query($sql);
			if ($this->raw_record = $this->First()) {
				$this->SetUserData();
				$result = true;
			}
		} catch (Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
		return $result;
	}
/*
	Determina si existe un usuario según su email
*/
	public function GetByEmail($email) {
		$result = false;
		try {
			if (empty($email)) { throw new Exception("No puedo buscar a un usuario sin email."); }
			$email = mb_substr($email,0,128);
			$email = $this->RealEscape($email);
			$sql = "SELECT * FROM ".SQLQuote($this->tabla_usuarios)." WHERE LOWER(`email`) = LOWER('".$email."') AND `estado` != 'ELI' LIMIT 1;";
			$this->Query($sql);
			if ($this->raw_record = $this->First()) {
				$this->SetUserData();
				$result = true;
			}
		} catch (Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
		return $result;
	}

/*
	Determina si existe un usuario según su jumble
*/
	public function GetByJumble($jumble) {
		$result = false;
		try {
			if (empty($jumble)) { $jumble = NULL; }
			$jumble = mb_substr($jumble,0,32);
			$jumble = $this->RealEscape($jumble);
			$sql = "SELECT * FROM ".SQLQuote($this->tabla_usuarios)." WHERE LOWER(`jumble`) = LOWER('".$jumble."') AND `estado` != 'ELI' LIMIT 1;";
			
			$this->Query($sql);
			if ($this->raw_record = $this->First()) {
				$this->SetUserData();
				$result = true;
			}
		} catch (Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
		return $result;
	}

/*
	Determina si $password es la contraseña correcta para el usuario actual.
*/
	public function ValidPass($password) {
		$result = false;
		try {
			$password = $this->GeneratePassword($password);
			$result = ($this->password == $password);
		} catch (Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
		return $result;
	}
	
/*
	Establece la nueva contraseña a partir de $password
*/
	public function SetNewPassword($password) {
		$result = false;
		try {
			if (empty($password)) { throw new Exception("No se puede establecer una contraseña vacía."); }
			if (strlen($password) > $this->PasswordMaxLenght) { throw new Exception("La nueva contraseña es demasiado larga."); }
			$this->newpassword = $this->GeneratePassword($password);
			
			$reg = array(
				'password'=>$this->newpassword,
				'fecha_modif'=>Date('Y-m-d H:i:s'),
				'usuario_id'=>$this->id
			);
			$this->Update($this->tabla_usuarios,$reg,"`id` = ".$this->id);
			$this->password = $this->newpassword;
			$result = true;
		} catch (Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
		return $result;
	}
/*
	Comprueba si el usuario actual tiene la sesión activa.
*/
	public function CheckLogin() {
		$result = false;
		try {
			if ((isset($_SESSION[$this->session_name])) and ($_SESSION[$this->session_name] > 0)) {

				$this->id = $this->DecodeID();
				
				$sql = "SELECT * FROM ".SQLQuote($this->tabla_sesiones)." WHERE `usuario_id` = '".$this->id."' AND `estado` = 1 ORDER BY `id` DESC";
				$this->Query($sql);
				if ($this->session_record = $this->First()) {

					$this->Get($this->session_record['usuario_id']);
					
					//if ($fila['ip'] == GetIP()) { // Es la misma IP?

						if (($this->session_record['navegador'] == $_SERVER['HTTP_USER_AGENT'])) { // Está en el mismo navegador?
						
							if ((date('U')-$this->session_record['idle']) < ((isset($this->tsession))?$this->tsession:3600)) { // La sesión no se venció?
								
								$result = true; // Entonces la sesión es válida.
								
							} else {
								if ($this->DebugOutput) { $this->SetError(__METHOD__,"La sesión del usuario expiró o tsession no está definido."); }
							}
						} else {
							if ($this->DebugOutput) { $this->SetError(__METHOD__,"El usuario no está en el mismo navegador que en la sesión anterior."); }
						}
					/*} else {
							if ($this->DebugOutput) { $this->SetError(__METHOD__,"El usuario cambió de IP."); }
						} 
					*/
					if ($result) {
						$this->UpdateSession();
					} else {
						$this->Logout();
					}
				} else {
					throw new Exception("No se encontró ninguna sesión para el usuario ".$this->id);
				}
			} else {
				throw new Exception("Cookie de sesión PHP no existe o está vacía.");
			}
		} catch (Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
		return $result;
	}
	
	public function UpdateSession() {
		$idSesion = SecureInt(substr($this->session_record['id'],0,11),null);
		$this->SetError(__METHOD__,"idSesion: ".$idSesion);
		$result = false;
		try {
			if ($idSesion == NULL) { throw new Exception("El id de sesión al que se intenta regenerar es pura fruta."); }
			$this->Update($this->tabla_sesiones,array('idle'=>date("U")),"`id` = ".$idSesion);
			$aux = $_SESSION[$this->session_name];
			unset($_SESSION[$this->session_name]);
			$_SESSION[$this->session_name] = $aux;
			$result = true;
		} catch (Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
		return $result;
	}
/*
	Termina la sesión del usuario. Si $id = null, la del usuario actual, si no, la del usuario apuntado por $id
*/
	public function Logout($id = null) {
		$result = false;
		if ($this->existe) { $id = $this->id; }
		else { $id = $this->DecodeID(); }
		if (empty($id)) { return false; }
		try {
			unset($_SESSION[$this->session_name]);
			$this->Update($this->tabla_sesiones,array('estado'=>0),"`usuario_id` = ".$id);
			$result = true;
		} catch (Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
		return $result;
	}
/*
	Inicia la sesión del usuario actual.
*/
	public function Login() {
		$result = false;
		try {
			$this->Update($this->tabla_sesiones,array('estado'=>0),"`usuario_id` = ".$this->id); // Vencer cualquier otra sesión que el usuario tenga abierta.
			$reg = array(
				'usuario_id'=>$this->id,
				'fecha_hora'=>Date('Y-m-d H:i:s'),
				'estado'=>1,
				'navegador'=>$_SERVER['HTTP_USER_AGENT'],
				'idle'=>Date('U'),
				'ip'=>GetIp()
			);
			$this->Insert($this->tabla_sesiones,$reg);
			$_SESSION[$this->session_name] = $this->EncodeID($this->id);
			$result = true;
		} catch (Exception $e) {
			$this->SetError(__METHOD__,$e->getMessage());
		}
		return $result;
	} // Login
/*
	Devuelve un array con los datos relativos a la última sesión del usuario.
	Si $id es null entonces se refiere al usuario actual.
	Si $prev es true, entonces devuelve la última sesión sin considerar la actual sesión de usuario.
*/
	public function GetSessionData($id = null, $prev = false) {
		$result = null;
		try {
			if ($id == null) {
				$id = $this->id;
			}
			$sql = "SELECT * FROM `".$this->tabla_sesiones."` WHERE `usuario_id` = ".$id." ";
			if ($prev) {
				$sql .= "AND `estado` = 0 ";
			}
			$sql .= "ORDER BY `fecha_hora` DESC LIMIT 1;";
			$this->Query($sql);
			if ($this->numrows > 0) {
				$result = $this->First();
			}
		} catch (Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
		return $result;
	} // GetSessionData

/*
	Establece el jumble para el recupero de contraseña.
*/
	public function SetJumble($jumble) {
		$result = false;
		try {
			
			$reg = array(
				'jumble'=>$this->RealEscape($jumble),
				'fechahora_jumble'=>Date('Y-m-d H:i:s')
			);
			$this->Update($this->tabla_usuarios,$reg,"`id` = ".$this->id);
			$result = true;
		} catch (Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
		return $result;
	}

	public function SetUserData() { // Pone los datos del usuario en el objeto a partir del registro.
		global $usuarios_nivel_int;
		$this->ParseRecord();
		$this->nombre_apellido = @$this->nombre." ".@$this->apellido;
		$this->NomApe = @$this->nombre." ".@$this->apellido;
		$this->nivel_int = @$usuarios_nivel_int[$this->nivel];
	}

	public function EncodeID($id) { // Codifica el id en la sesión de usuario. Para que quien se la robe, no la pueda usar.
		$result = null;
		$id = SecureInt($id,-1);
		if ($id > 0) {
			$result = ($id*$this->SecretKey);
		}
		return $result;
	}

	public function DecodeID($id = null) { // Decodifica el id en la sesión del usuario. La inversa de la anterior función.
		$result = null;
		if (!is_null($id) and is_numeric($id)) {
			$result = ($id/$this->SecretKey);
		} else {
			if (isset($_SESSION[$this->session_name])) {
				$cookie = SecureInt($_SESSION[$this->session_name],-1);
				if ($cookie > 0) {
					$result = ($cookie/$this->SecretKey);
				}
				$_SESSION[$this->session_name] = $cookie;
			}
		}
		return $result;
	}

	public function GeneratePassword($cleartext) {
		if (!empty($this->salt)) {
			$salt = md5($this->salt);
			$jam = array(substr($salt,0,16),substr($salt,16,16));
			$result = md5($jam[0].md5($cleartext).$jam[1]);
		} else {
			$result = md5($cleartext);
		}
		return $result;
	}

	public function Reset() {
		try {
			$fields = $this->GetColumnsNames($this->tabla_usuarios);
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
} // Class cUsuario
?>