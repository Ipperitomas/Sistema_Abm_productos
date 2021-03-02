
$(document).on('ready',function () {
	$("div#navbar ul li").on("click",function () {
		$("div#navbar ul li").removeClass('active');
		$(this).addClass('active');
	});
<?php
	if (DEVELOPE) {
?>
	if (document.getElementById('windims')) {
		$("#windims").on('dblclick', function () { $(this).hide();});
		document.getElementById('windims').innerHTML = 'Ancho: <b>'+window.innerWidth+'</b> Alto: <b>'+window.innerHeight+'</b>';
		$(window).resize(function () {
			document.getElementById('windims').innerHTML = 'Ancho: <b>'+window.innerWidth+'</b> Alto: <b>'+window.innerHeight+'</b>';
		});
	}
<?php
	}
?>
});
<?php
	if ($objeto_contenido->alias != 'login') {
?>

var userTL = null;
userTimeLeft();

function userTimeLeft() {
	if (userTL != null) {
		clearTimeout(userTL); userTL = null;
	}
	userTL = setTimeout(function () {
		getAjax({
			archivo: 'userTimeleft',
			extraparams: 'user_id=<?php echo @$objeto_usuario->id; ?>'
		},function (a,b,c,d) {
			if (a == 200) {
				var result = parseJson(c);
				if (result.quit) {
					if (result.quit == 'true') {
						window.location.reload();
						return;
					}
				}
			}
			userTimeLeft();
		});
	},(1000*<?php
	if (isset($objeto_usuario->tsession)) { echo ($objeto_usuario->tsession+5); } else { echo '60*2'; }
	?>));
}

<?php
	}
?>
