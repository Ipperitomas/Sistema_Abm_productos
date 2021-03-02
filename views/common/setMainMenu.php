<?php
/*
	Esto verifica que la caché del menú esté actualizada.
	Lee el archivo $mainmenu y compara su fecha con la fecha del último registro de la tabla TBL_contenido.
*/
$local_file = substr(__FILE__,strlen(DIR_BASE))." ";

	if (!ExisteArchivo(DIR_plantillas.DEFAULT_SITE_MENU.'.htm')) {
		cLogging::Write($local_file." Plantilla del menú principal no encontrada: ".DIR_plantillas.DEFAULT_SITE_MENU.'.htm');
		return;
	}
	
	$content = file_get_contents(DIR_plantillas.DEFAULT_SITE_MENU.'.htm');
	
	$menu_items = $objeto_contenido->GetMenuItems(0, true, true);
	$sub_menu_items = $objeto_contenido->GetMenuItems($objeto_contenido->id);
	if (empty($sub_menu_items) and ($objeto_contenido->parent_id > 0)) {
			$sub_menu_items = $objeto_contenido->GetMenuItems($objeto_contenido->parent_id);
	}
	
	$content_items = '';
	if (CanUseArray($menu_items)) {
		foreach($menu_items as $item_menu) {
		$class = '';
		if ($item_menu['alias'] == $objeto_contenido->alias) { $class = ' active'; }
		$content_items .= '
			<li class="nav-item">
				<a href="'.BASE_URL.$item_menu['alias'].'" class="nav-link '.$class.'">
				<i class="nav-icon fa fa-circle-o text-danger"></i>
				<p class="text">'.$item_menu['nombre'].'</p>
				</a>
			</li>';
	
		}
	}
	
	$content = str_replace('{logo}',URL_imgs."logo.png",$content);
	echo str_replace('{items}',$content_items,$content);
	
?>