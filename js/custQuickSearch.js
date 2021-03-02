var listCustQuickSearch;
var ruloCustQuickSearch;

function showCustQuickSearch(fncCallback) {
	getAjax({
		archivo: 'custQuickSearch',
		content: 'clientes'
	}, function (a,b,c,d) {
		if (a == 200) {
			if (evalresult.Eval(c)) {
				if (document.getElementById('modalCustQuickSearch')) {
					document.getElementsByTagName('BODY')[0].removeChild(document.getElementById('modalCustQuickSearch'));
				}
				document.getElementsByTagName('BODY')[0].insertAdjacentHTML('beforeend', c);
				$('#modalCustQuickSearch').modal('show');
				$('#modalCustQuickSearch').on('hidden.bs.modal', function (e) {
					document.getElementsByTagName('BODY')[0].removeChild(document.getElementById('modalCustQuickSearch'));
				});
				listCustQuickSearch.setFinish(fncCallback);
				listCustQuickSearch.Get();
			}
		}
	});
	return false;
}

function hideCustQuickSearch() {
	$('#modalCustQuickSearch').modal('hide');
}

if(typeof fmdRulo != 'function') {
	LoadJS('fmdRulo', false, InitruloCustQuickSearch);
} else {
	InitruloCustQuickSearch();
}
if(typeof mgrListadoCreator != 'function') {
	LoadJS('objListados', false, InitcustQuickSearchList);
} else {
	InitcustQuickSearchList();
}

function InitruloCustQuickSearch () {
	ruloCustQuickSearch = new fmdRulo;
}

function InitcustQuickSearchList() {
	listCustQuickSearch = new mgrListadoCreator({
		archivo: 'custQuickSearchList',
		content: 'clientes',
		divListIdName: 'listCustQuickSearch'
	});
}

/*
function AgregarPersona() {
	if (!window.showCustQuickSearch) {
		LoadJS('custQuickSearch', true, AgregarPersona);
	} else {
		showCustQuickSearch(addSelectCustomer);
	}
}

function addSelectCustomer(laTabla) {
	var trs = laTabla.querySelectorAll('tbody tr');
	if (trs.length > 0) {
		trs.forEach(function (tr) {
			tr.addEventListener('dblclick', function () { 
				AgregarRegistro(tr.dataset.custid);
			});
		});
	}
	var btns = laTabla.querySelectorAll('button.btn-sel');
	if (btns.length > 0) {
		btns.forEach(function (btn) {
			btn.addEventListener('click', function () { 
				AgregarRegistro(btn.dataset.custid);
			});
		});
	}
}
*/