var _MODEL_ 		= {};
var _CHECK_ELE_ 	= {};
var _LABEL_ 		= {};
var _ID_		= {};
var _TOPOLOGY_STATUS_	= {};
var _TOPOLOGY_RUNNING_ 	= true;
var _TOPOLOGY_INFO_	= {};

var validate_form = function(form) {
	var retVal = _TOPOLOGY_RUNNING_ ? false : true;
	console.log("_TOPOLOGY_RUNNING_ -- " + _TOPOLOGY_RUNNING_);
	// Validate all the elements in form.
	if(retVal == false) {
		alert("Topology is running = " + _TOPOLOGY_RUNNING_ + ": Kill the topology and re-submit.!!");
	}
	return retVal;
}

var check_topology_running = function(topologyName, agentNode) {

	document.getElementById("mask").style.display="block";

	if(topologyName != "" && agentNode != "") {
		_TOPOLOGY_INFO_.topologyName 	= topologyName;
		_TOPOLOGY_INFO_.agentNode	= agentNode;

                var storm_url = "http://" + _TOPOLOGY_INFO_.agentNode + ":8080/api/v1/topology/summary";
                var url = "http://localhost/storm/storm_glu_api.php?action=get_topology_state&url=" + storm_url + "&topology_name=" + _TOPOLOGY_INFO_.topologyName;
                storm_ajax(url, is_topology_running, _TOPOLOGY_INFO_);
        }
}

var is_topology_running = function(_TOPOLOGY_INFO_, resp) {
	_TOPOLOGY_STATUS_ = JSON.parse(resp);
	for(var topology in _TOPOLOGY_STATUS_.topologies) {
		if(_TOPOLOGY_STATUS_.topologies[topology].name == _TOPOLOGY_INFO_.topologyName) {
			_TOPOLOGY_NAME_	= _TOPOLOGY_INFO_.topologyName;
			_TOPOLOGY_ID_ 	= _TOPOLOGY_STATUS_.topologies[topology].id;
			_TOPOLOGY_RUNNING_ = true;
			break;
		} else {
			_TOPOLOGY_RUNNING_ = false;
		}
	}
	if(_TOPOLOGY_RUNNING_) {
		var kill_topology_flag = confirm("Topology: " + _TOPOLOGY_NAME_ + " is already running !! NEED TO KILL IT ?");
		console.log("Kill the topology - " + _TOPOLOGY_ID_ + " ? " + kill_topology_flag);

		if(kill_topology_flag) {
			var storm_url = "http://" + _TOPOLOGY_INFO_.agentNode + ":8080/api/v1/topology/" + _TOPOLOGY_ID_ + "/kill/10";
                	var url = "http://localhost/storm/storm_glu_api.php?action=kill_topology&url=" + storm_url + "&topology_name=" + _TOPOLOGY_INFO_.topologyName + "&agentNode=" + _TOPOLOGY_INFO_.agentNode;
			console.log("Kill topology API - " + storm_url);
			storm_ajax(url, topology_killed, _TOPOLOGY_INFO_);
			_TOPOLOGY_RUNNING_ = false;
		}
	} else {
		alert("Topology: " + _TOPOLOGY_INFO_.topologyName + " is NOT running");
	}
	document.getElementById("mask").style.display="none";
}

var topology_killed = function(_TOPOLOGY_INFO_, resp) {
	console.log("TOPOLOGY Killed --- " + _TOPOLOGY_INFO_.topologyName);
}

var getGluData = function(val,agentElement) {
	if(val != "") {
		document.getElementById("mask").style.display="block";
		var get_agents_url = "http://localhost/storm/storm_glu_api.php?action=get_agents&url=" + val + "agents";
		storm_ajax(get_agents_url, populateAgents, agentElement);

		var get_model_url = "http://localhost/storm/storm_glu_api.php?action=get_model&url=" + val + "model/static";
		storm_ajax(get_model_url, populateStaticModel, document.getElementById("well"));
	}
	else {
		alert("Invalid DataCenter !!");
	}
}

var populateAgents = function(agentElement, response){
	if(response != "Error(901)") {
		for(var i = 0; i < response.length; i++) {
			if(response[i] != "") {
				agentElement.options[i] = new Option(response[i], response[i]);
			}
		}
		document.getElementById("mask").style.display="none";
	}
}

var populateStaticModel = function(Ele, response){
	if(response != "Error(901)") {
		document.getElementById("model").value = response;
		_MODEL_ = JSON.parse(response);

		Ele.style.display = (_MODEL_.entries.length > 0) ? "" : "none";
		for(var i in _MODEL_.entries){
			_CHECK_ELE_["ele" + i] = document.createElement("input");
			_CHECK_ELE_["ele" + i].type = "radio";
			_CHECK_ELE_["ele" + i].name = "entry";
			_CHECK_ELE_["ele" + i].id = _MODEL_.entries[i].mountPoint;

			_LABEL_["ele" + i] = document.createElement("label");
			_LABEL_["ele" + i].htmlFor = _MODEL_.entries[i].mountPoint;
			_LABEL_["ele" + i].innerHTML = "&nbsp;" + _MODEL_.entries[i].mountPoint;

			_ID_["ele" + i] = _MODEL_.entries[i].mountPoint;
		}

		Ele.innerHTML = "";
		for(var e in _CHECK_ELE_) {
			Ele.appendChild(_CHECK_ELE_[e]);
			Ele.appendChild(_LABEL_[e]);

			document.getElementById(_ID_[e]).addEventListener("click", function(){
					selectFromModel(this.id);
					});
		}
	}
	document.getElementById("mask").style.display="none";
}

var selectFromModel = function(mountPoint) {
	console.log("selectFromModel === " + mountPoint);

	for(var i in _MODEL_.entries){
		if(_MODEL_.entries[i].mountPoint == mountPoint) {
			console.log("FOUND " + mountPoint + " in model object");
			console.log(_MODEL_.entries[i]);

			//Populate HTML
			document.getElementById("mountPoint").value = _MODEL_.entries[i].mountPoint;
			document.getElementById("jarName").value = _MODEL_.entries[i].initParameters.jarName;
			document.getElementById("version").value = _MODEL_.entries[i].initParameters.version;

			for(var j = 0; j < document.getElementById("className").options.length; j++) {
				if(document.getElementById("className").options[j].value == _MODEL_.entries[i].initParameters.topologyClassName) {
					document.getElementById("className").options[j].selected = true;
				}
			}
			for(var j = 0; j < document.getElementById("topologyDataCenter").options.length; j++) {
				if(document.getElementById("topologyDataCenter").options[j].value == _MODEL_.entries[i].initParameters.topologyDataCenter) {
					document.getElementById("topologyDataCenter").options[j].selected = true;
				}
			}
			document.getElementById("topologyTable").value = _MODEL_.entries[i].initParameters.topologyTable;

			for(var j = 0; j < document.getElementById("topologyEnvironment").options.length; j++) {
				if(document.getElementById("topologyEnvironment").options[j].value == _MODEL_.entries[i].initParameters.topologyEnvironment) {
					document.getElementById("topologyEnvironment").options[j].selected = true;
				}
			}

			for(var j = 0; j < document.getElementById("topologyRegion").options.length; j++) {
				if(document.getElementById("topologyRegion").options[j].value == _MODEL_.entries[i].initParameters.topologyRegion) {
					document.getElementById("topologyRegion").options[j].selected = true;
				}
			}

			for(var j = 0; j < document.getElementById("topologyType").options.length; j++) {
				if(document.getElementById("topologyType").options[j].value == _MODEL_.entries[i].initParameters.topologyType) {
					document.getElementById("topologyType").options[j].selected = true;
				}
			}

			for(var j = 0; j < document.getElementById("topologyMode").options.length; j++) {
				if(document.getElementById("topologyMode").options[j].value == _MODEL_.entries[i].initParameters.topologyMode) {
					document.getElementById("topologyMode").options[j].selected = true;
				}
			}
			document.getElementById("zkPath").value = _MODEL_.entries[i].initParameters.zkPath;
			document.getElementById("zkUrls").value = _MODEL_.entries[i].initParameters.zkUrls;
			document.getElementById("tags").value = _MODEL_.entries[i].tags;
			document.getElementById("topologyName").value = _MODEL_.entries[i].initParameters.topologyName;

		}
	}
}

var isTopologyRunning = function(ele, resp) {
	console.log(resp);
}

var storm_ajax = function(url, callback, callback_param) {
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4) {
			if (xmlhttp.status == 200){
				var resp = xmlhttp.responseText.split(",");
				callback(callback_param, resp);
			}
			else if (xmlhttp.status == 400){
				console.error('There was a 400 error');
			}
			else{
				console.warn('Unknown response');
			}
		}
	}

	xmlhttp.open("GET", url);
	xmlhttp.send();

}
