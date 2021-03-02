<?php
/*
	Clase cUsrBackend.
	Esto maneja los usuarios del backend. Está basado en la clase cUsuarios.
	Created: 2018-10-22
	Author: DriverOp.
	Modif: 2018-11-02
	Desc: Agregado método para recuperar datos de un usuario arbitrario.
	Modif: 2019-11-16
	Desc: Agregado métodos Everything, Menu y ParseItemMenu relacionado con el menú del usuario.

*/

require_once(DIR_model."class.usuarios.inc.php");
require_once(DIR_includes."class.fechas.inc.php");
require_once(DIR_model."class.usrmsgs.inc.php");

class cUsrBackend extends cUsuarios {
	protected $tabla_usuarios = TBL_backend_usuarios;
	protected $tabla_sesiones = TBL_backend_sesiones;
	protected $tabla_perfiles = TBL_backend_perfiles;
	protected $salt = '';
	protected $SecretKey = 6005;
	protected $session_name = 'USR_BACKEND';
	protected $PasswordMaxLenght = 32;
	private $tabla_msgasuntosusr = "config_motivos_usuarios";
	private $tabla_permitidos = TBL_backend_permisos;
	private $tabla_reloads = 'backend_reloads';
	private $tabla_asuntos_mensajes = 'config_motivos';
	private $tabla_contenidos = TBL_contenido;
	public $active_content = null; // El ID del contenido que está activo en este momento.

	public function __construct() {
		parent::__construct();
		$this->tabla_principal = $this->tabla_usuarios;
		$this->actual_file = __FILE__;
	}
	
	public function GetIdsAreasUsuario() {
		$result = array();
		if ($this->existe) {
			try {
				if ($this->nivel == 'USR') {
					$sql = "SELECT ".SQLQuote($this->tabla_msgasuntosusr).".`motivo_id` FROM ".SQLQuote($this->tabla_msgasuntosusr).", ".SQLQuote($this->tabla_asuntos_mensajes)." WHERE ".SQLQuote($this->tabla_asuntos_mensajes).".`id` = ".SQLQuote($this->tabla_msgasuntosusr).".`motivo_id` AND ".SQLQuote($this->tabla_msgasuntosusr).".`usuario_id` = ".$this->id." AND ".SQLQuote($this->tabla_msgasuntosusr).".`estado` = 'HAB' AND ".SQLQuote($this->tabla_asuntos_mensajes).".`habilitado` = 'HAB' ORDER BY ".SQLQuote($this->tabla_asuntos_mensajes).".`id`";
				} else {
					$sql = "SELECT ".SQLQuote($this->tabla_asuntos_mensajes).".`id` AS `motivo_id` FROM ".SQLQuote($this->tabla_asuntos_mensajes)." WHERE ".SQLQuote($this->tabla_asuntos_mensajes).".`habilitado`= 'HAB' ORDER BY ".SQLQuote($this->tabla_asuntos_mensajes).".`id`";
				}
				$this->Query($sql);
				if ($this->numrows > 0) {
					while ($fila = $this->Next()) {
						$result[] = $fila['motivo_id'];
					} // while
				}
			} catch(Exception $e) {
				$this->SetError(__METHOD__,$e->GetMessage());
			}
		}
		return $result;
	}
/*
	Devuelve en un array la lista de contenidos permitidos para el usuario actual para armarle el menú.
*/
	public function Menu($parent_id = 0, $url = BASE_URL) {
		$result = array();
		if ($this->existe) {
			if ($this->nivel == 'OWN') { return $this->Everything(); }
			try {
				$sql = "SELECT ".SQLQuote($this->tabla_contenidos).".* FROM ".SQLQuote($this->tabla_contenidos).", ".SQLQuote($this->tabla_permitidos)." WHERE 1=1 AND ".SQLQuote($this->tabla_contenidos).".`parent_id` = ".$parent_id." AND ".SQLQuote($this->tabla_contenidos).".`esta_protegido` = 1 AND ".SQLQuote($this->tabla_contenidos).".`estado` = 'HAB' AND ".SQLQuote($this->tabla_contenidos).".`id` = ".SQLQuote($this->tabla_permitidos).".`contenido_id` AND ".SQLQuote($this->tabla_contenidos).".`en_menu` = 1 AND ".SQLQuote($this->tabla_permitidos).".`usuario_id` = ".$this->id." ORDER BY ".SQLQuote($this->tabla_contenidos).".`id` ASC, ".SQLQuote($this->tabla_contenidos).".`orden` ASC;";
				//EchoLog($sql);
				$res = $this->Query($sql, true);
				if ($this->cantidad > 0) {
					while($fila = $this->Next($res)) {
						$result[$fila['id']] = $this->ParseItemMenu($fila);
						$result[$fila['id']]['url'] = EnsureTrailingURISlash($url.$fila['alias']);
					}
				}
				if (CanUseArray($result)) {
					foreach($result as $key => $value) {
						$result[$key]['childs'] = $this->Menu($value['id'], $result[$key]['url']);
					}
				}
			} catch(Exception $e) {
				$this->SetError(__METHOD__,$e->GetMessage());
			}
		}
		$this->FindActive($result);
		$this->Menu_Usuario = $result;
		return $result;
	}
	
/*
	Marca el tiempo (momento) en que se visualizó un listado.
*/
	public function SetReload($id_listado) {
		$sql = "SELECT * FROM ".SQLQuote($this->tabla_reloads)." WHERE `usuario_id` = ".$this->id." AND `listado_id` = ".$id_listado;
		try {
			$this->Query($sql);
			$reg = array('momento'=>Date('Y-m-d H:i:s'));
			if ($this->numrows > 0) {
				$this->Update($this->tabla_reloads, $reg, "`usuario_id` = ".$this->id." AND `listado_id` = ".$id_listado);
			} else {
				$reg['usuario_id'] = $this->id;
				$reg['listado_id'] = $id_listado;
				$this->Insert($this->tabla_reloads, $reg);
			}
		} catch(Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
	} // SetReload
/*
	Lee el momento en que se visualizó un listado.
*/
	public function GetReload($id_listado) {
		$result = null;
		$sql = "SELECT * FROM ".SQLQuote($this->tabla_reloads)." WHERE `usuario_id` = ".$this->id." AND `listado_id` = ".$id_listado;
		try {
			$this->Query($sql);
			$result = $this->First();
		} catch(Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
		return $result;
	}
/*
	Devuelve TODOS los contenidos para menú.
*/
	private function Everything($parent_id = 0, $url = BASE_URL) {
		$result = array();
		try {
			$sql = "SELECT * FROM ".SQLQuote($this->tabla_contenidos)." WHERE 1=1 AND `parent_id` = ".$parent_id." AND `esta_protegido` = 1 AND `estado` = 'HAB'  AND `en_menu` = 1";			
			$res = $this->Query($sql, true);
			if ($this->cantidad > 0) {
				while($fila = $this->Next($res)) {
					$result[$fila['id']] = $this->ParseItemMenu($fila);
					$result[$fila['id']]['url'] = EnsureTrailingURISlash($url.$fila['alias']);
				}
			}
			if (CanUseArray($result)) {
				foreach($result as $key => $value) {
					$result[$key]['childs'] = $this->Everything($value['id'], $result[$key]['url']);
				}
			}
		} catch(Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
		$this->FindActive($result);
		return $result;
		
	}
/*
	Esto uniformiza los items de menú para que todos queden con las mismas propiedades.
*/
	private function ParseItemMenu($item) {
		$metadata = json_decode($item['metadata']);
		$item['active'] = ($this->active_content == $item['id']);
		$item['menutag'] = (isset($metadata->menutag))?$metadata->menutag:$item['nombre'];
		$item['icon_class'] = 'fa-dashboard';
		$item['icon_class'] = (isset($metadata->icon_class))?$metadata->icon_class:$item['icon_class'];
		$item['icon_collection'] = 'fa';
		$item['icon_collection'] = (isset($metadata->icon_collection))?$metadata->icon_collection:$item['icon_collection'];
		$item['tooltip'] = (isset($metadata->tooltip))?$metadata->tooltip:$item['nombre'];
		return $item;
	}
/*
	Pone la rama activa del menú del usuario según el contenido actual.
*/
	private function FindActive(&$menu) {
		$active = false;
		foreach($menu as $id => &$item) { // Ese ampersand es el que hace todo el truco.
			if ($item['active']) {
				$active = true;
				break;
			} else {
				if (count($item['childs']) > 0) {
					if ($this->FindActive($item['childs'])) {
						$active = true;
						$item['active'] = true;
					}
				}
			}
		}
		return $active;
	}
/*
	Devuelve los datos del perfil del usuario si lo tiene.
*/
	public function GetPerfil() {
		$result = false;
		$this->perfil = new stdClass();
		try {
			if (!empty($this->perfil_id)) {
				$sql = "SELECT * FROM ".SQLQuote($this->tabla_perfiles)." WHERE `id` = ".$this->perfil_id;
				$this->Query($sql);
				if ($fila = $this->First()) {
					foreach ($fila as $key => $value) {
						$this->perfil->$key = $value;
					}
				}
			}
		} catch(Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
		return $result;
	}
}
?>