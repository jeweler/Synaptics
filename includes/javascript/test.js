$(document).ready(function() {
	if (localStorage.filter) {
		var tmp = localStorage.filter.split(',');
		for (var k in tmp) {
			$('#' + tmp[k]).click();

		};
		putFilters();
	};
	$("ul.filters input").click(function() {

		putFilters();
	});

});
function putFilters() {

	$(".filter-area input").attr("name");
	filtersId = new Array();
	matrix = new Array();
	filtersTypes = new Array();
	$(".filters-area input:checked").each(function() {

		if (matrix[$(this).data("filter-type")] == undefined)
			matrix[$(this).data("filter-type")] = new Array();
		matrix[$(this).data("filter-type")].push($(this).attr("name"));
		filtersId.push($(this).attr("name"));
		if (filtersTypes.indexOf($(this).data("filter-type")) == -1)
			filtersTypes.push($(this).data("filter-type"));

	});

	filtersId = filtersId.join(",");

	if (filtersId.length == 0) {
		$('.forFilter tbody tr').show();
		localStorage.filter = undefined;
	} else {

		compare = new Array();
		result = new Array();
		allTr = $(".forFilter tbody tr");
		allTr.hide();
		for (var i in filtersTypes) {
			allTr.each(function() {
				if (compare[i] == undefined)
					compare[i] = new Array();
				if (isInType($(this), matrix[filtersTypes[i]]))
					compare[i].push($(this));
			})
		}
		if (filtersTypes.length == 1) {
			showTrs(compare[0]);
		} else if (filtersTypes.length == 2) {
			showTrs(intersect_safe(compare[0], compare[1]));
		} else {
			for (var i = 0; i < compare.length - 2; i++) {
				if (i == 0) {
					result = intersect_safe(compare[i], compare[i + 1]);
				} else {
					result = intersect_safe(result, compare[i + 1]);
				}
			}
			showTrs(result);
		}
	}
	localStorage.filter = filtersId;
};
function showTrs(trs) {
	$(trs).each(function() {
		$(this).show();
	});
}

function intersect_safe(a, b) {
	_a = new Array();
	_b = new Array();
	var result = new Array();
	for (var aa in a) {
		console.log(a[aa]);
		_a.push(a[aa]);
	}
	for (var aa in b) {
		_b.push(b[aa].get(0));
	}
	for (var _ba in _b) {
		if (_a.indexOf(_b[_ba]) != -1) {
			result.push(_b[_ba]);
		}
	}

	return result;
}

function isInType(dom, ids) {
	var ret = false;
	for (var id in ids) {
		if (dom.data("filter").indexOf(Math.floor(ids[id])) != -1)
			ret = true
	}
	return ret;
}