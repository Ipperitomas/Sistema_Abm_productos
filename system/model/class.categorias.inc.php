<?php
/*
	Manejo de categorias de el sistema.
	Created: 2021-02-03
	Author: Tomas
*/

class cCategorias extends cModels {
	
	
	private $tabla_categorias = TBL_categorias;
	function __construct() {
		parent::__construct();
		$this->actual_file = __FILE__;
		$this->tabla_principal = $this->tabla_categorias;
	}

/*
	Obtiene un mensaje para el usuario actual.
	Establece las propiedades del objeto tomando los campos de la tabla.
*/
	public function Get($id) {
		$result = false;
		$this->Reset();
		try {
			if (SecureInt($id,null) == null) { throw new Exception(__LINE__." ID debe ser un número."); }
			$sql = "SELECT * FROM ".SQLQuote($this->tabla_categorias)." WHERE `id` = ".$id;
			$this->Query($sql);

			if ($response = $this->First()) {
				$result = $response;
			}
		} catch(Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
		return $result;
	}


	/*
	Obtiene un mensaje para el usuario actual.
	Establece las propiedades del objeto tomando los campos de la tabla.
*/
public function GetCategoriesAll() {
	$result = false;
	$this->Reset();
	try {
		$sql = "SELECT * FROM ".SQLQuote($this->tabla_categorias);
		$this->Query($sql);
		if ($response = $this->First()) {
			do {
				$result[] = $response;				
			} while ($response = $this->Next());
		}
	} catch(Exception $e) {
		$this->SetError(__METHOD__,$e->GetMessage());
	}
	return $result;
}


public function Create($data){
	$result = false;
	try {
		if (!CanUseArray($data)) { throw new Exception(__LINE__." Data debe ser un array."); }
		$data['sys_fecha_alta'] = cFechas::Ahora();
		$data['sys_fecha_modif'] = cFechas::Ahora();
		if($this->Insert($this->tabla_categorias,$data)){
			$result = true;
		}
	} catch(Exception $e) {
		$this->SetError(__METHOD__,$e->GetMessage());
	}
	return $result;
}



public function Save($data,$id){
	$result = false;
	try {
		if (!CanUseArray($data)) { throw new Exception(__LINE__." Data debe ser un array."); }
		if (SecureInt($id,null) == null) { throw new Exception(__LINE__." ID debe ser un número."); }
		$data['sys_fecha_modif'] = cFechas::Ahora();
		
		if($this->Update($this->tabla_categorias,$data," `id` = ".$id)){
			$result = true;
		}
	} catch(Exception $e) {
		$this->SetError(__METHOD__,$e->GetMessage());
	}
	return $result;
}

public function Unset($id){
	$result = false;
	try {
		if (SecureInt($id,null) == null) { throw new Exception(__LINE__." ID debe ser un número."); }
		$data['sys_fecha_modif'] = cFechas::Ahora();
		if($this->Delete($this->tabla_categorias," `id` = ".$id)){
			$result = true;
		}
	} catch(Exception $e) {
		$this->SetError(__METHOD__,$e->GetMessage());
	}
	return $result;
}

}

?>