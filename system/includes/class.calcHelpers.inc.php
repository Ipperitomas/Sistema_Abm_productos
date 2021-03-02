<?php
/*
	Funciones de ayuda para los cálculos financieros.
*/

class calcHelpers {

/*
	Calcula un pago con intereses.
	$tasa es el coeficiente de interés por cada cuota.
	$cuotas es la cantidad de cuotas a pagar.
	$valorafinanciar es el capital total prestado.
*/
	static function Pago($tasa, $cuotas, $valorafinanciar) {
		$capital = $valorafinanciar;
		$prorat = ((1 - (1 / (pow(1 + $tasa, $cuotas)))) / $tasa);
		return $capital / $prorat;
	}
/*
	Calcula el monto del Impuesto al Valor Agregado sobre el monto total.
*/
	static function IVA($porc_iva, $monto) {
		return ($monto/100*$porc_iva);
	}
/*
	TIR: Tasa de Retorno.
*/
	static function TIR($lista, $guess = 0.01) {
		$used_guess = $guess;
		$x = $used_guess;
		$next_x = null;
		if ($used_guess == -1.0) {
			$x = 0.1;
		}
		$max_iterations = 20;
		$iterations_done = 0;
		$wanted_precision = 0.00000001;
		$current_diff = PHP_INT_MAX;
		$current = null;
		$above = null;
		$below = null;
		$index = null;
		
		while (($current_diff > $wanted_precision) and ($iterations_done < $max_iterations)) {
			$index = 0;
			$above = 0.0;
			$below = 0.0;
			reset($lista);

			foreach($lista as $key => $current) {
				$a = pow(1.0 + $x, $index);
				$above += $current / $a;

				$b = pow(1.0 + $x, $index + 1.0);
				
				$below += -$index * $current / $b;
				
				$index++;
				
			}

			$next_x = $x - $above / $below;
			$iterations_done++;
			$current_diff = abs($next_x - $x);
			$x = $next_x;
		}

		if (($used_guess == 0.0) and (abs($x) < $wanted_precision)) {
			$x = 0.0;
		}
		if ($current_diff < $wanted_precision) {
			return $x;
		}
		else {
			return NULL;
		}
	}

}

?>