<?php
$local_file = substr(__FILE__,strlen(DIR_BASE))." ";
if (isset($sub_menu_items) and CanUseArray($sub_menu_items)) {

		if (!ExisteArchivo(DIR_plantillas.DEFAULT_SITE_SUB_MENU.'.htm')) {
			cLogging::Write($local_file." Plantilla del menú principal no encontrada: ".DIR_plantillas.DEFAULT_SITE_MENU.'.htm');
			return;
		}
		
		$content = file_get_contents(DIR_plantillas.DEFAULT_SITE_SUB_MENU.'.htm');
		$content_items = '';
		
		foreach($sub_menu_items as $item_menu) {
		$class = '';
		if ($item_menu['alias'] == $objeto_contenido->alias) { $class = ' active'; }
		$content_items .= '
			<li class="nav-item'.$class.'">
				<a class="nav-link" href="'.$item_menu['url'].'">'.$item_menu['nombre'].'</a>
			</li>';
		}
		echo str_replace('{items}',$content_items,$content);
	
}
?>