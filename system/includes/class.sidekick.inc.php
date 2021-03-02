<?php
/*

	- SetError. Controla los erroes. Pasar __METHOD__ y el mensaje siempre.
	- FilterFloat: Convierte la entrada en un tipo flotante PHP, o false en caso de no poder hacerlo.
	- FilterInt: Convierte la entrada en un tipo int PHP, o false en caso de no poder hacerlo.
	- FilterNumber: Verifica si $value está compuesto de solo números.
	- FilterVar: Recorta la entrada a una cantidad arbitraria de caracteres y opcionalmente pasa a minúsculas.
	- BuildEstadoCond: Uso interno, resuelve la lógica para armar la consulta SQL en base al campo `estado`.
	- ParseTel: Divide el teléfono en raw, prefijo y abonado.
	- GetTxtSexo: Devuelve los literales del sexo.
	- GetTxtTipoPersona: Devuelve los literales del tipo de persona.
	- GetTxtTipoDoc: Devuelve los literales del tipo de documento.
	- GetTxtPermanencia: Devuelve los literales del tipo de permanencia.
	- GetTxtNivelUsuario: Devuelve los literales del nivel de los usuarios.
	- GetTxtEstado: Devuelve los literales de los estados de habilitación.
	- GetTxtCalculo
	- GetTxtAplicar
	- GetTxtEstadoPrestamo: Devuelve los literales de los estados de los préstamos.
	- GetEstadosPosibles: Devuelve un array con los estados posibles.
	- CheckField: Agrupa las cinco funciones más comunes de validación de datos de cCheckInputs.
	- GetPerfilesUsuarios: Deluelve la lista de los tipos de perfiles de usuarios.
	- ExistePerfil: Devuelve el registro del perfil de usuario indicado por $alias.
	- FormarIniciales: Dado un nombre y un apellido, devuelve las iniciales.
	- GetNegocios: Devuelve la lista de negocios.
	- GetCargosImpuestos: Devuelve la lista de cargos e impuestos en un array
	- GenerateAlias: Con un string, generar un alias apto para URL.
	- DescomponerRango: Descompone las fechas enviadas por el plugin datetimepicker en las dos fechas límites, devolviéndolas en un array en formato ISO.
	- GetEstadosDeSeguimiento: Devuelve la lista de estados de seguimientos, solamente los alias.
	- GetBanco: Dado un ID de banco, devuelve el nombre o null.
	- GetParam: Traer un valor de la tabla de parámetros. O null en caso de no encontrarlo.
	- SetParam: Poner un valor de la tabla de parámetros. Si ya existe, falla.
	- GetParamGrupos: Traer la lista de grupos de parámetros.
	- GetParamGrupo: Traer un grupo de los grupos de parámetros.
	- GetHollydays: Dado un año, devuelve la lista de feriados establecidos para ese año.
	- EsDiaNoLaborable: Dada una fecha, determina si ese día es laborable o no.
	- FiltroPrestamo: Devuelve un filtro para el listado de préstamos de cobranzas
	- GetSitiosGrupos: Devuelve la lista de grupso de sitios.
	- GetTyposQuery: Devuelve un array con los posibles tipos de parámetros query.
	
	- GetAllGruposCuentas: Devuelve un listado de grupos de cuentas.
*/

include_once(DIR_includes."class.checkinputs.inc.php");
include_once(DIR_includes."class.logging.inc.php");

$Estados_Validos = array(
	'HAB'=>'Habilitado',
	'DES'=>'Deshabilitado',
	'ELI'=>'Eliminado'
);
$Estados_Validos_Colores = array(
	'HAB'=>'<span class="font-weight-bold">%s</span>',
	'DES'=>'<span class="font-weight-bold text-warning">%s</span>',
	'ELI'=>'<span class="font-weight-bold text-danger">%s</span>'
);

class cSidekick{
	
	public static $reg_param = null;
	public static $feriados = null;
	public static $año_feriados = null;

	static function SetError($method, $msg) {
		$line = substr(__FILE__,strlen(DIR_BASE))." -> ".$method.". ".$msg;
		if (DEVELOPE) { EchoLogP(htmlentities($line)); }
		cLogging::Write($line);
	}
/*
	Convierte la entrada $value en un tipo flotante PHP, o false en caso de no poder hacerlo.
*/
	static function FilterFloat($value) {
		$value = trim($value);
		$value = substr($value,0,11);
		$value = trim($value);
		if (empty($value)) { return false; }
		$p = strrpos($value,',');
		if (($p !== false) and ($p > 0)) {
			$value = str_replace('.','',$value);
			$value = str_replace(',','.',$value);
		} else {
			$p = strrpos($value,'.');
			if (($p !== false) and ($p > 0)) {
				$value = str_replace(',','',$value);
			}
		}
		$value = trim($value);
		if (empty($value)) { return false; }
		if (!preg_match('/[0-9\.]+/',$value)) { return false; }
		if (!is_numeric($value)) { return false; }
		return (float)$value;
	}
/*
	Convierte la entrada $value en un tipo int PHP, o false en caso de no poder hacerlo.
*/
	static function FilterInt($value) {
		$value = trim($value);
		$value = substr($value,0,11);
		$value = trim($value);
		if (empty($value)) { return false; }
		if (!preg_match('/^[0-9]+$/',$value)) { return false; }
		return (int)$value;
	}
/*
	Verifica si $value está compuesto de solo números.
*/
	static function FilterNumber($value) {
		$value = trim($value);
		if (empty($value)) { return false; }
		if (!preg_match('/[0-9]+/',$value)) { return false; }
		return $value;
	}
/*
	Recorta la entrada a una cantidad arbitraria de caracteres y opcionalmente pasa a minúsculas.
	$var: la variable.
	$length: a qué largo cortarla.
	$lower: convertir a minúsculas.
*/
	static function FilterVar($var, $length = 11, $lower = false) {
		$var = trim($var);
		$var = mb_substr($var,0, $length);
		$var = trim($var);
		if ($lower) {
			$var = mb_strtolower($var);
		}
		return $var;
	}
/*
	Resuelve la lógica para armar la consulta SQL en base al campo `estado`.
*/
	static function BuildEstadoCond($db, $estado, $field = 'estado', $tabla = NULL) {
		$salida = '';
		if (!empty($estado)) {
			if (!empty($tabla)) {
				$campo = SQLQuote($tabla).".".SQLQuote($field);
			} else {
				$campo = SQLQuote($field);
			}
			if (is_array($estado) and (count($estado)>0)) {
				foreach($estado as $k => $e) { $estado[$k] = $db->RealEscape($e); }
				$salida = $campo." IN ('".implode("','",$estado)."')";
			}
			if (is_string($estado)) {
				$salida = "UPPER(".$campo.") = UPPER('".$db->RealEscape($estado)."')";
			}
		}
		if (!empty($salida)) {
			$salida = "AND (".$salida.") ";
		}
		return $salida;
	}

	static function ParseTel($tel, $as_object = false) {
		$result = NULL;
		if (!empty($tel)) {
			$c = mb_stripos($tel,'-');
			if ($c > 0) {
				if ($as_object) {
					$result = new stdClass();
					$result->raw = $tel;
					$result->codigo_area = mb_substr($tel,0,$c);
					$result->numero_abonado = mb_substr($tel,$c+1,mb_strlen($tel));
				} else {
					$result = array(
						'raw'=>$tel,
						'prefijo'=>mb_substr($tel,0,$c),
						'abonado'=>mb_substr($tel,$c+1,mb_strlen($tel))
					);
				}
			} else {
				if ($as_object) {
					$result = new stdClass();
					$result->raw = $tel;
					$result->codigo_area = null;
					$result->numero_abonado = null;
				} else {
					$result = array(
						'raw'=>$tel,
						'prefijo'=>null,
						'abonado'=>null
					);
				}
			}
		}
		return $result;
	} // ParseTel.

	static function GetTxtSexo($sexo) {
		$result = '';
		$sexos = array('M'=>'Masculino','F'=>'Femenino','N'=>'(No aplica)');
		if (isset($sexos[$sexo])) {
			$result = $sexos[$sexo];
		}
		return $result;
	} // GetTxtSexo

	static function GetTxtTipoPersona($tipo) {
		$result = '';
		$tipospersona = array('FIS'=>'Física','JUR'=>'Jurídica');
		if (isset($tipospersona[$tipo])) {
			$result = $tipospersona[$tipo];
		}
		return $result;
	} // GetTxtTipoPersona

	static function GetTxtTipoDoc($tipo) {
		$result = '';
		$tiposdoc = array(
			'CUIT'=>'CUIT',
			'DNI'=>'DNI',
			'LC'=>'LC',
			'CI'=>'CI',
			'LE'=>'LE',
			'PAS'=>'Pasaporte'
		);
		if (isset($tiposdoc[$tipo])) {
	 		$result = $tiposdoc[$tipo];
		 }
		return $result;
	} // GetTxtTipoDoc

	static function GetTxtPermanencia($per) {
		$result = '';
		$permamencias = array('PER'=>'Permanente','TEM'=>'Temporario');
		if (isset($permamencias[$per])) {
			$result = $permamencias[$per];
		}
		return $result;
	} // GetTxtPermanencia
	
	static function GetTxtNivelUsuario($nivel) {
		$result = '(nivel desconocido)';
		switch ($nivel) {
			case 'OWN': $result = 'Administrador general'; break;
			case 'ADM': $result = 'Administrador'; break;
			case 'USR': $result = 'Usuario'; break;
		}
		return $result;
	}
	
	static function GetTxtEstado($estado, $color = false) {
		global $Estados_Validos;
		global $Estados_Validos_Colores;
		$result = '(estado desconocido)';
		if (array_key_exists($estado,$Estados_Validos)) {
			$result = $Estados_Validos[$estado];
			if ($color) {
				$result = sprintf($Estados_Validos_Colores[$estado],$Estados_Validos[$estado]);
			}
		}
		return $result;
	} // GetTxtEstado
	
	static function GetTxtCalculo($calculo) {
		$result = '(ninguno)';
		switch ($calculo) {
			case 'PORC': $result = 'Porcentaje'; break;
			case 'FIJO': $result = 'Cargo fijo'; break;
			case 'TASA': $result = 'Tasa'; break;
		}
		return $result;
	} // GetTxtCalculo
	
	static function GetTxtAplicar($aplicar) {
		$result = '(ninguno)';
		switch ($aplicar) {
			case 'CAPITALMAS': $result = 'Sobre el Capital'; break;
			case 'CAPITALMENOS': $result = 'Incluido en el Capital'; break;
			case 'INTERES': $result = 'Al interés calculado'; break;
			case 'CAPITALMASINTERES': $result = 'Capital más interés'; break;
		}
		return $result;
	} // GetTxtAplicar
	
	static function GetTxtEstadoPrestamo($estado) {
		$result = '(desconocido)';
		switch ($estado) {
			case 'SOLIC': $result = '<span title="Solicitado, el préstamo no ha sido transferido">Solicitado</span>'; break;
			case 'PEND': $result = '<span title="El préstamo fue transferido y pendiente de cobro">Pendiente</span>'; break;
			case 'CANC': $result = '<span title="El préstamo fue pagado">Pagado</span>'; break;
			case 'MORA': $result = '<span title="El préstamo está en mora">Mora</span>'; break;
			case 'REFIN': $result = '<span title="El préstamo fue refinanciado">Refinanciado</span>'; break;
		}
		return $result;
	}
	
	static function GetEstadosPrestamosPosibles($tipo = 0) {
		$result = array();
		switch($tipo) {
			case 0: $result = array(
								'PEND'=>'Pendiente',
								'SOLIC'=>'Solicitado',
								'CANC'=>'Pagado',
								'MORA'=>'En mora',
								'REFIN'=>'Refinanciado',
								'ANUL'=>'Anulado'
							);
					break;
			case 1: $result = array(
								'PEND'=>'Pendientes',
								'SOLIC'=>'Solicitados',
								'CANC'=>'Pagados',
								'MORA'=>'En mora',
								'REFIN'=>'Refinanciados',
								'ANUL'=>'Anulados'
							);
					break;
		}
		return $result;
	}
	
	static function GetEstadosPosibles() {
		$result = array(
			'HAB'=>'Habilitado',
			'DES'=>'Deshabilitado',
			'ELI'=>'Eliminado'
		);
		return $result;
	}

	static function CheckField($type, $value, $field) {
		cCheckInput::$checkempty = false; // Que esté o no vacío ya se controló antes.
		switch($type) {
			case 'name': cCheckInput::NomApe($value,null,$field); break;
			case 'email': cCheckInput::Email($value,null,$field); break;
			case 'DNI': cCheckInput::DNI($value,null,$field); break;
			case 'CUIT': cCheckInput::CUIT($value,null,$field); break;
			case 'nro_doc': cCheckInput::nro_doc($value,null,$field); break;
		}
		if (CanUseArray(cCheckInput::$msgerr)) {
			throw new Exception(__LINE__." ".array_shift(cCheckInput::$msgerr));
		}
	} // CheckField

		/*
	 Obtener  lista de companias celilares 
	 
	*/	

	static function GetCompCelular($db) {
		$result = new stdClass();		
			try {
				$sql = "SELECT * FROM ".SQLQuote(TBL_config_celcompanys)." WHERE 1=1 ";
				$db->Query($sql);
				if ($db->error) { throw new Exception(__LINE__." DBErr: ".$db->errmsg); }
				if ($fila = $db->First()) {
					$result = array();
					do {
						$fila['nombre'] = $fila['nombre'];
							$result[$fila['id']] = $fila;
					} while($fila = $db->Next());
				}
			} catch(Exception $e) {
				self::SetError(__METHOD__,$e->getMessage());
			}		
		return $result;
	}	

	/* Obtener ciudades*/
	static function GetCiudades($db, $busqueda) {
		$result = new stdClass();		
			try {
				$sql = "SELECT * FROM ".SQLQuote(TBL_ciudades)." WHERE `nombre` LIKE '".$busqueda."%' LIMIT 50" ;
				$db->Query($sql);
				if ($db->error) { throw new Exception(__LINE__." DBErr: ".$db->errmsg); }
				if ($fila = $db->First()) {
					$result = array();
					do {				 						
						array_push($result,array('id'=> $fila['id'], 'text' => $fila['nombre']));
					} while($fila = $db->Next());
				}
			} catch(Exception $e) {
				self::SetError(__METHOD__,$e->getMessage());
			}		
			//$result = json_encode($result);
		return $result;
	}	

	static function GetPerfilesUsuarios($db, $estado = null, $id_as_index = true) {
		$result = null;
		try {
			$sql = "SELECT * FROM ".SQLQuote(TBL_backend_perfiles)." WHERE 1=1 ";
			if (!is_null($estado)) {
				$sql .= self::BuildEstadoCond($db, $estado);
			}
			$sql .= " ORDER BY `nombre`;";
			$db->Query($sql);
			if ($db->error) { throw new Exception(__LINE__." DBErr: ".$db->errmsg); }
			if ($fila = $db->First()) {
				$result = array();
				do {
					$fila['data'] = json_decode($fila['data']);
					if ($id_as_index) {
						$result[$fila['id']] = $fila;
					} else {
						$result[] = $fila;
					}
					
				} while($fila = $db->Next());
			}
		} catch(Exception $e) {
			self::SetError(__METHOD__,$e->getMessage());
		}
		return $result;
	}
/*
	Devuelve el registro del perfil de usuario indicado por $alias.
*/
	static function ExistePerfil($db, $alias, $byid = false) {
		$result = false;
		try {
			$sql = "SELECT * FROM ".SQLQuote(TBL_backend_perfiles)." WHERE ";
			if ($byid) {
				if (!is_numeric($alias)) { throw new Exception(__LINE__." alias debe ser un número."); }
				$sql .= "(`id` = ".$alias.") ";
			} else {
				$sql .= "UPPER(`alias`) = UPPER('".$db->RealEscape(mb_substr($alias,0,64))."') ";
			}
			$db->Query($sql);
			if ($db->error) { throw new Exception(__LINE__." DBErr: ".$db->errmsg); }
			if ($fila = $db->First()) {
				$result = array();
				if (isset($fila['data']) and !empty($fila['data'])) {
					$fila['data'] = json_decode($fila['data']);
				}
				$result = $fila;
			}
		} catch(Exception $e) {
			self::SetError(__METHOD__,$e->getMessage());
		}
		return $result;
	}
	
	static function FormarIniciales($str1, $str2) {
		$result = null;
		$work = array_merge(explode(' ',$str1),explode(' ',$str2));
		foreach ($work as $key => $value) {
			$s = trim($value);
			$result .= mb_strtoupper(mb_substr($s,0,1));
		}
		return $result;
	}
	
	static function GetNegocios($db, $estado = null, $id_as_index = true) {
		$result = null;
		try {
			$sql = "SELECT * FROM ".SQLQuote(TBL_config_negocios)." WHERE 1=1 ";
			if (!is_null($estado)) {
				$sql .= self::BuildEstadoCond($db, $estado);
			}
			$sql .= " ORDER BY `config_file`;";
			$db->Query($sql);
			if ($db->error) { throw new Exception(__LINE__." DBErr: ".$db->errmsg); }
			if ($fila = $db->First()) {
				$result = array();
				do {
					$fila['data'] = json_decode($fila['data']);
					if ($id_as_index) {
						$result[$fila['id']] = $fila;
					} else {
						$result[] = $fila;
					}
				} while($fila = $db->Next());
			}
		} catch(Exception $e) {
			self::SetError(__METHOD__,$e->getMessage());
		}
		return $result;
	}
/*
	Devuelve el negocio apuntado por $id
*/	
	static function GetNegocio($db, $id) {
		$result = false;
		try {
			if (!is_numeric($id)) { throw new Exception(__LINE__." id debe ser un número."); }
			$sql = "SELECT * FROM ".SQLQuote(TBL_config_negocios)." WHERE `id`= ".$id;
			$db->Query($sql);
			if ($db->error) { throw new Exception(__LINE__." DBErr: ".$db->errmsg); }
			$result = $db->First();
		} catch(Exception $e) {
			self::SetError(__METHOD__,$e->getMessage());
		}
		return $result;
	}
	
	static function GetMarcas($db, $negocio_id, $estado = null, $id_as_index = true) {
		$result = null;
		if (!empty($negocio_id) and is_numeric($negocio_id)) {
			try {
				$sql = "SELECT * FROM ".SQLQuote(TBL_config_marcas)." WHERE `negocio_id`= ".$negocio_id." ";
				if (!is_null($estado)) {
					$sql .= self::BuildEstadoCond($db, $estado);
				}
				$sql .= " ORDER BY `nombre`;";
				//EchoLog($sql);
				$db->Query($sql);
				if ($db->error) { throw new Exception(__LINE__." DBErr: ".$db->errmsg); }
				if ($fila = $db->First()) {
					$result = array();
					do {
						$fila['data'] = json_decode($fila['data']);
						if ($id_as_index) {
							$result[$fila['id']] = $fila;
						} else {
							$result[] = $fila;
						}
					} while($fila = $db->Next());
				}
			} catch(Exception $e) {
				self::SetError(__METHOD__,$e->getMessage());
			}
		}
		return $result;
	}
/**
* Devuelve la lista de cargos e impuestos en un array
*/
	static function GetCargosImpuestos($db, $tipo = null, $estado = null, $id_as_index = true) {
		$result = null;
		try {
			$sql = "SELECT * FROM ".SQLQuote(TBL_cargos)." WHERE 1=1 ";
			if (!is_null($estado)) {
				$sql .= self::BuildEstadoCond($db, $estado);
			}
			if (!is_null($tipo) and is_string($tipo)) {
				$tipo = $db->RealEscape(strtoupper(substr($tipo,0,7)));
				$sql .= "AND `tipo` = '".$tipo."' ";
			}
			$sql .= "ORDER BY `nombre`;";
			$db->Query($sql);
			if ($db->error) { throw new Exception(__LINE__." DBErr: ".$db->errmsg); }
			if ($fila = $db->First()) {
				$result = array();
				do {
					if (!empty($fila['data'])) {
						$fila['data'] = json_decode($fila['data']);
					}
						if ($id_as_index) {
							$result[$fila['id']] = $fila;
						} else {
							$result[] = $fila;
						}
				} while($fila = $db->Next());
			}
		} catch(Exception $e) {
			self::SetError(__METHOD__,$e->getMessage());
		}
		return $result;
	}
/*
	Con un string, generar un alias apto para URL.
*/
	static function GenerateAlias($titulo) {
$lista = array(
'/\ba\b/i',
'/\bal\b/i',
'/\baquel\b/i',
'/\baquella\b/i',
'/\bante\b/i',
'/\bbajo\b/i',
'/\bcabe\b/i',
'/\bcon\b/i',
'/\bcontra\b/i',
'/\bde\b/i',
'/\bdel\b/i',
'/\bdesde\b/i',
'/\bdurante\b/i',
'/\bel\b/i',
'/\ben\b/i',
'/\bentre\b/i',
'/\besa\b/i',
'/\bese\b/i',
'/\besta\b/i',
'/\beste\b/i',
'/\bhacia\b/i',
'/\bhasta\b/i',
'/\bla\b/i',
'/\blas\b/i',
'/\blo\b/i',
'/\blos\b/i',
'/\bmediante\b/i',
'/\bnos\b/i',
'/\bpara\b/i',
'/\bpor\b/i',
'/\bse\b/i',
'/\bsegun\b/i',
'/\bsin\b/i',
'/\bsu\b/i',
'/\bsus\b/i',
'/\bso\b/i',
'/\bsobre\b/i',
'/\btras\b/i',
'/\btu\b/i',
'/\btus\b/i',
'/\bun\b/i',
'/\buna\b/i',
'/\bunas\b/i',
'/\buno\b/i',
'/\bunos\b/i',
'/\bversus\b/i',
'/\bvia\b/i'
);
		$titulo = self::StripTildes($titulo);
		$titulo = mb_convert_encoding($titulo, 'ASCII'); 
		$titulo = self::StripPunctuation($titulo);
		$titulo = preg_replace($lista, "", $titulo);
		$titulo = preg_replace(array("/(-)\\1{1,}/is","/(\s)\\1{1,}/is","/(_)\\1{1,}/is"),array("-"," ","-"), $titulo); // Elimina las repeticiones de guiones y espacios del preg_replace anterior.
		$titulo = preg_replace("/\s+/", "-", $titulo);
		$titulo = str_replace(" ","", $titulo);
		$titulo = substr($titulo,0,255);
		while ($titulo[0] == '-') {
			$titulo = substr($titulo,1,strlen($titulo)-1);
		}
		while ($titulo[strlen($titulo)-1] == '-') {
			$titulo = substr($titulo,0,strlen($titulo)-1);
		}
		return $titulo;
	} // GenerateAlias

	static function StripTildes($str) {
		$reptildes=array('á','é','í','ó','ú','à','è','ì','ò','ù','ñ','ë','ü','_');
		$repplanas=array('a','e','i','o','u','a','e','i','o','u','n','e','u','-');
		$str = mb_strtolower($str);
		$str = str_replace($reptildes,$repplanas,FormatStrUTF8($str));
		return $str;
	}

	static function StripPunctuation($str) {
		$aux = null;
		if (mb_strlen($str) > 0) {
			for ($x=0; $x<mb_strlen($str); $x++) {
				if (ord($str[$x]) == 32) { // Space
					$aux .= $str[$x];
				} else {
					if (ord($str[$x]) < 124) { // No Ansi
						if ((ord($str[$x]) == 45) or (ord($str[$x]) >= 48) and (ord($str[$x]) <= 57)) { // Is a number
							$aux .= $str[$x];
						} else {
							if ((ord($str[$x]) >= 65) and (ord($str[$x]) <= 90)) { // Uppercases
								$aux .= $str[$x];
							} else {
								if ((ord($str[$x]) == 95) or (ord($str[$x]) >= 97)) { // Lowercases
									$aux .= $str[$x];
								}
							}
						}
					}
				}
			} // for
		}
		return $aux;
	}

/*
	Descompone las fechas enviadas por el plugin datetimepicker en las dos fechas límites, devolviéndolas en un array en formato ISO.
*/
	static function DescomponerRango($rango) {
		$result = null;
		$work = explode(' - ',$rango);
		if (count($work)>1) {
			$result = array('desde'=>null, 'hasta'=> null);
			$result['desde'] = substr(trim($work[0]),0,10);
			$result['hasta'] = substr(trim($work[1]),0,10);
			foreach ($result as $key => $value) {
				if (cFechas::LooksLikeDate($value)) {
					$result[$key] = cFechas::FechaToISO($value);
				}
			}
			if ($result['desde'] > $result['hasta']) {
				$aux = $result['desde'];
				$result['desde'] = $result['hasta'];
				$result['hasta'] = $aux;
			}
		}
		return $result;
	}
/*
	Devuelve la lista de estados de seguimientos, solamente los alias. filtrado por estado y devolviendo el ID en el índice del array resultante.
*/
	static function GetEstadosDeSeguimiento($db, $all = false, $estado = null, $id_as_index = true) {
		$result = null;
		try {
			$sql = "SELECT * FROM ".SQLQuote(TBL_estados_seg)." WHERE 1=1 ";
			if (!is_null($estado)) {
				$sql .= self::BuildEstadoCond($db, $estado);
			}
			$sql .= "ORDER BY `nombre`;";
			$db->Query($sql);
			if ($db->error) { throw new Exception(__LINE__." DBErr: ".$db->errmsg); }
			if ($fila = $db->First()) {
				$result = array();
				do {
					if ($id_as_index) {
						$result[$fila['id']] = ($all)?$fila:$fila['alias'];
					} else {
						$result[] = ($all)?$fila:$fila['alias'];
					}
				} while($fila = $db->Next());
			}
		} catch(Exception $e) {
			self::SetError(__METHOD__,$e->getMessage());
		}
		return $result;
	}
	
	static function GetBanco($db, $id) {
		$result = null;
		try {
			$sql = "SELECT * FROM ".SQLQuote(TBL_bancos)." WHERE ";
			if (!is_numeric($id)) { throw new Exception(__LINE__." ID debe ser un número."); }
			$sql .= "`id` = '".$id."'";
			$db->Query($sql);
			if ($db->error) { throw new Exception(__LINE__." DBErr: ".$db->errmsg); }
			$result = $db->First();
		} catch(Exception $e) {
			self::SetError(__METHOD__,$e->getMessage());
		}
		return $result;
	}
/*
	Traer un valor de la tabla de parámetros. O null en caso de no encontrarlo.
	$byid indica que en vez de buscar $nombre en el campo `nombre`, lo haga en el campo `id`.
*/
	static function GetParam($db, $nombre, $byid = false) {
		$result = null;
			try {
				$sql = "SELECT * FROM ".SQLQuote(TBL_parametros)." WHERE ";
				if ($byid) {
					if (!is_numeric($nombre)) { throw new Exception(__LINE__." Nombre debe ser un número."); }
					$sql .= "(`id` = ".$nombre.") ";
				} else {
					$sql .= "LOWER(`nombre`) = LOWER('".$db->RealEscape(mb_substr($nombre,0,64))."') ";
				}
				
				$sql .= "AND `estado` = 'HAB' LIMIT 1;";
				$db->Query($sql);
				if ($db->error) { throw new Exception(__LINE__." DBErr: ".$db->errmsg); }
				if ($fila = $db->First()) {
					$result = $fila['valor'];
					self::$reg_param = $fila;
				}
			} catch(Exception $e) {
				self::SetError(__METHOD__,$e->getMessage());
			}
		return $result;
	}
/*
	Poner un valor de la tabla de parámetros. Si ya existe, actualiza el valor.
	No olvides pasar el id del usuario actual.
*/
	static function SetParam($db, $nombre, $valor = null, $tipo='STRING', $usuario_id = null) {
		$result = false;
			try {
				$reg = array(
					'valor'=>$db->RealEscape(mb_substr($valor,0,64)),
					'usuario_id'=>$db->RealEscape(mb_substr($usuario_id,0,11)),
					'fechahora_modif'=>cFechas::Ahora()
				);
				if (!is_null(self::GetParam($db, $nombre))) {
					switch (self::$reg_param['tipo']) {
						case 'STRING': if (!is_string($valor)) { throw new Exception(__LINE__." Valor: '".$valor."' no es STRING."); } break;
						case 'INT': if (self::FilterInt($valor) === false) { throw new Exception(__LINE__." Valor: '".$valor."' no es INT."); } break;
						case 'FLOAT': if (self::FilterFloat($valor) === false) { throw new Exception(__LINE__." Valor: '".$valor."' no es FLOAT."); } break;
						case 'BOOL': if (!is_bool($valor)) { throw new Exception(__LINE__." Valor: '".$valor."' no es BOOL."); } else { $reg['valor'] = ($valor)?1:0; } break;
					}
					$db->Update(TBL_parametros, $reg, "LOWER(`nombre`) = LOWER('".$db->RealEscape(mb_substr($nombre,0,64))."')");
				} else {
					$reg['fechahora_alta']=cFechas::Ahora();
					$reg['nombre'] = $db->RealEscape(mb_substr($nombre,0,64));
					$reg['tipo'] = $tipo;
					$db->Insert(TBL_parametros, $reg);
				}
				$result = true;
			} catch(Exception $e) {
				self::SetError(__METHOD__,$e->getMessage());
			}
		return $result;
	}
/*
	Trae la lista de grupos de parámetros.
*/
	static function GetParamGrupos($db, $estado = null, $id_as_index = false) {
		$result = false;
		try {
			$sql = "SELECT * FROM ".SQLQuote(TBL_parametros_grupos)." WHERE 1 = 1 ";
			if (!is_null($estado)) {
				$sql .= self::BuildEstadoCond($db, $estado);
			}
			$sql .= "ORDER BY `nombre`;";
			$db->Query($sql);
			if ($db->error) { throw new Exception(__LINE__." DBErr: ".$db->errmsg); }
			if ($fila = $db->First()) {
				$result = array();
				do {
					if ($id_as_index) {
						$result[$fila['id']] = $fila;
					} else {
						$result[] = $fila;
					}
				} while($fila = $db->Next());
			}
		} catch(Exception $e) {
			self::SetError(__METHOD__,$e->getMessage());
		}
		return $result;
	}
/*
	Traer un grupo de los grupos de parámetros.
*/
	static function GetParamGrupo($db, $id) {
		$result = false;
			try {
				if (!is_numeric($id)) { throw new Exception("ID debe ser un número."); }
				$sql = "SELECT * FROM ".SQLQuote(TBL_parametros_grupos)." WHERE `id` = ".$id;
				$db->Query($sql);
				if ($db->error) { throw new Exception(__LINE__." DBErr: ".$db->errmsg); }
				if ($fila = $db->First()) {
					if (!empty($fila['data'])) {
						$fila['data'] = json_decode($fila['data']);
					}
					$result = $fila;
				}
			} catch(Exception $e) {
				self::SetError(__METHOD__,$e->getMessage());
			}
		return $result;
	}
/*
	Dado un año, devuelve la lista de feriados establecidos para ese año.
	$año es el año en cuestión.
	$indexed: usar la fecha como índice del array.
	Devuelve un array donde cada elemento es un array con la fecha y el tíulo (motivo) del feriado
*/
	static function GetHollydays($db, $año, $indexed = false) {
		$result = [];
			try {
				if (!is_numeric($año)) { throw new Exception("Año debe ser un número."); }
				self::$año_feriados = $año;
				$sql = "SELECT * FROM ".SQLQuote(TBL_feriados)." WHERE YEAR(`fecha`) = '".$año."' AND `estado` = 'HAB'";
				$db->Query($sql);
				if ($db->error) { throw new Exception(__LINE__." DBErr: ".$db->errmsg); }
				if ($fila = $db->First()) {
					do {
						if ($indexed) {
							$result[$fila['fecha']] = $fila['leyenda'];
						} else {
							$result[] = array('fecha'=>$fila['fecha'], 'titulo'=>$fila['leyenda']);
						}
					} while($fila = $db->Next());
				}
			} catch(Exception $e) {
				self::SetError(__METHOD__,$e->getMessage());
			}
		return $result;
	}
/*	
	Dada una fecha (ISO:YYYY-MM-DD), determina si ese día es laborable o no.
*/
	static function EsDiaNoLaborable($db, $fecha) { // Devuelve TRUE si es NO laborable, ojo!.
		$result = false;
		$dia_sem = Date('N',strtotime($fecha))*1;
		if ($dia_sem == 6) { $result = true; }
		if ($dia_sem == 7) { $result = true; }
		if (!$result) {
			$año = explode('-',$fecha);
			$año = $año[0];
			if (empty(self::$feriados) or ($año != self::$año_feriados)) {
				self::$feriados = self::GetHollydays($db, $año, true);
			}
			if (array_key_exists($fecha,self::$feriados)) { $result = true; }
		}
		return $result;	
	}
/*
	FiltroPrestamo
*/
	static function FiltroPrestamo($db, $filtro_id = null) {
		$result = null;
		try {
			$sql = "SELECT * FROM ".SQLQuote(TBL_config_filtros)." WHERE 1=1 ";
			if (!empty($filtro_id) and is_numeric($filtro_id)) {
				$sql .= "AND `id` = ".$filtro_id." ";
			} else {
				$sql .= "AND `omision` = 1 ";
			}
			$sql .= "AND `estado` = 'HAB'";
			$db->Query($sql, true);
			if ($db->error) { throw new Exception(__LINE__." DBErr: ".$db->errmsg); }
			if (($db->cantidad == 0) and empty($filtro_id)) {
				$sql = "SELECT * FROM ".SQLQuote(TBL_config_filtros)." WHERE 1=1 AND `estado` = 'HAB'";
				$db->Query($sql, true);
				if ($db->error) { throw new Exception(__LINE__." DBErr: ".$db->errmsg); }
			}
			if ($db->cantidad > 0) {
				$result = $db->First();
				$result['data'] = json_decode($result['data']);
			}
		} catch(Exception $e) {
			self::SetError(__METHOD__,$e->getMessage());
		}
		return $result;
	}
	
	static function FiltrosPrestamos($db, $estado = 'HAB') {
		$result = null;
		try {
			$sql = "SELECT * FROM ".SQLQuote(TBL_config_filtros)." WHERE 1=1 ";
			if (!is_null($estado)) {
				$sql .= self::BuildEstadoCond($db, $estado);
			}
			$sql .= "ORDER BY `nombre`";
			$db->Query($sql);
			if ($db->error) { throw new Exception(__LINE__." DBErr: ".$db->errmsg); }
			if ($fila = $db->First()) {
				$result = array();
				do {
					if (!empty($fila['data'])) {
						$fila['data'] = json_decode($fila['data']);
					}
					$result[] = $fila;
				} while($fila = $db->Next());
			}
		} catch(Exception $e) {
			self::SetError(__METHOD__,$e->getMessage());
		}
		return $result;
	}

	static function GetSitiosGrupos($db, $estado = null, $id_as_index = true) {
		$result = null;
		try {
			$sql = "SELECT * FROM ".SQLQuote(TBL_config_sitios_grupos)." WHERE 1=1 ";
			if (!is_null($estado)) {
				$sql .= self::BuildEstadoCond($db, $estado);
			}
			$sql .= " ORDER BY `nombre`;";
			$db->Query($sql);
			if ($db->error) { throw new Exception(__LINE__." DBErr: ".$db->errmsg); }
			if ($fila = $db->First()) {
				$result = array();
				do {
					$fila['data'] = json_decode(@$fila['data']);
					if ($id_as_index) {
						$result[$fila['id']] = $fila;
					} else {
						$result[] = $fila;
					}
				} while($fila = $db->Next());
			}
		} catch(Exception $e) {
			self::SetError(__METHOD__,$e->getMessage());
		}
		return $result;
	}

	static function GetBuros($db, $estado = null, $id_as_index = true) {
		$result = null;
		try {
			$sql = "SELECT * FROM ".SQLQuote(TBL_buros)." WHERE 1=1 ";
			if (!is_null($estado)) {
				$sql .= self::BuildEstadoCond($db, $estado);
			}
			$sql .= " ORDER BY `nombre`;";
			$db->Query($sql);
			if ($db->error) { throw new Exception(__LINE__." DBErr: ".$db->errmsg); }
			if ($fila = $db->First()) {
				$result = array();
				do {
					$fila['data'] = json_decode(@$fila['data']);
					if ($id_as_index) {
						$result[$fila['id']] = $fila;
					} else {
						$result[] = $fila;
					}
				} while($fila = $db->Next());
			}
		} catch(Exception $e) {
			self::SetError(__METHOD__,$e->getMessage());
		}
		return $result;
	}
/*
	Devuelve un listao de grupos de cuentas.

*/
	static function GetAllGruposCuentas($db, $estado = null, $id_as_index = true) {
		$result = null;
		try {
			$sql = "SELECT * FROM ".SQLQuote(TBL_cuentas_grupos)." WHERE 1=1 ";
			if (!is_null($estado)) {
				$sql .= self::BuildEstadoCond($db, $estado);
			}
			$sql .= " ORDER BY `nombre`;";
			$db->Query($sql);
			if ($db->error) { throw new Exception(__LINE__." DBErr: ".$db->errmsg); }
			if ($fila = $db->First()) {
				$result = array();
				do {
					$fila['data'] = json_decode(@$fila['data']);
					if ($id_as_index) {
						$result[$fila['id']] = $fila;
					} else {
						$result[] = $fila;
					}
				} while($fila = $db->Next());
			}
		} catch(Exception $e) {
			self::SetError(__METHOD__,$e->getMessage());
		}
		return $result;
	}
/*
	Devuelve un array con los posibles tipos de parámetros query.
*/
	static function GetTyposQuery() {
		$typos = array(
			'dni'=>'DNI',
			'nomape'=>'Nombre/Apellido',
			'cuit'=>'CUIT/CUIL',
			'dirpost'=>'Dirección Postal',
			'fecha'=>'Fecha ISO',
			'fechahora'=>'Fecha y hora ISO'
		);
		return $typos;
	}


} // class cSideKick
/*
		$result = null;
		try {
			$sql = "SELECT * FROM ".SQLQuote(TBL_)." WHERE 1=1 ";
			$db->Query($sql);
			if ($db->error) { throw new Exception(__LINE__." DBErr: ".$db->errmsg); }
			if ($fila = $db->First()) {
				$result = array();
				do {
					
				} while($fila = $db->Next());
			}
		} catch(Exception $e) {
			self::SetError(__METHOD__,$e->getMessage());
		}
		return $result;
*/



?>