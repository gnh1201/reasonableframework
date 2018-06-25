function get_routes() {
	return routes.split(',');
}

function inArray(needle, haystack) {
	var len = haystack.length;
	for(var i=0; i<len; i++) if(haystack[i] == needle) return true;
	return false;
}

function addScript(url) {
	var s = document.createElement('script');
	s.src = url;
	s.type = 'text/javascript';
	document.body.appendChild(s);
}

$(document).ready(function() {
	var route_names = routes.split(',');
	$(route_names).each(function(i, v) {
		if($.trim(v).length > 0) {
			addScript(base_url + "?route=assetproxy&path=view/public/js/route/" + v + ".js");
		}
	});
}); 
