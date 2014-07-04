/* Set Timer go to top */
var goto_top_type = -1;
var goto_top_itv = 0;

function studio_timer() {
	var studioy = goto_top_type == 1 ? document.documentElement.scrollTop : document.body.scrollTop;
	var studiomoveby = 15;
	studioy -= Math.ceil(studioy * studiomoveby / 100);
	if (studioy < 0) { studioy = 0; }
	if (goto_top_type == 1) {
		document.documentElement.scrollTop = studioy;
	} else {
		document.body.scrollTop = studioy;
	}
	if (studioy == 0) {
		clearInterval(goto_top_itv);
		goto_top_itv = 0;
	}
}
/* Go to top function */
function gotop() {
	if (goto_top_itv == 0) {
		if (document.documentElement && document.documentElement.scrollTop) {
			goto_top_type = 1;
		} else if (document.body && document.body.scrollTop) {
			goto_top_type = 2;
		} else {
			goto_top_type = 0;
		}
		if (goto_top_type > 0) { goto_top_itv = setInterval('studio_timer()', 25); }
	}
}
/* Go to top Image hover */
function tophover(status) {
    if (status == 0) {
        document.getElementById('topgohover').src = document.getElementById('hiddenurl').innerHTML+'/images/tophover.png';
    } else {
        document.getElementById('topgohover').src = document.getElementById('hiddenurl').innerHTML+'/images/top.png';
    }
}
