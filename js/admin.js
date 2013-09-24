function post_with(to, p) {
	var myForm = document.createElement("form");
	myForm.method = "post";
	myForm.action = to;
	for (var k in p) {
	  var myInput = document.createElement("input") ;
	  myInput.setAttribute("type", "hidden") ;
	  myInput.setAttribute("name", k) ;
	  myInput.setAttribute("value", p[k]);
	  myForm.appendChild(myInput) ;
	}
	document.body.appendChild(myForm) ;
	myForm.submit() ;
	document.body.removeChild(myForm) ;
}

function queryUserForActionWithParam(query_html, action, name, value) {
	var data = {};
	data[name] = value;
	var ret = confirm(query_html);
	if (ret) {
		// Pressed OK. Go ahead.
		post_with(action, data);
	}
}

function queryUserForActionWithData(query_html, action, data_str) {
	data_str=data_str.replace(/@@apos@@/g,"'");
	data_str=data_str.replace(/@@quot@@/g, "\"");
	var data = JSON.parse(data_str);
	var ret = confirm(query_html);
	if (ret) {
		// Pressed OK. Go ahead.
		post_with(action, data);
	}
}
