const loadAlpine = () => {
	if (typeof Alpine !== "undefined") {
		console.log("Alpine is loaded");
	}
};

const loadjQuery = () => {
	if (typeof jQuery !== "undefined") {
		jQuery(document).ready(function () {
			console.log("jQuery is loaded");
		});
	}
};

const onContentLoad = () => {
	loadAlpine();
	loadjQuery();
};

document.addEventListener("DOMContentLoaded", onContentLoad);
