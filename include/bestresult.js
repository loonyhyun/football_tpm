
var pos_cnt  = [];
var pos_info = [];
pos_info["pos_lw" ] = [];
pos_info["pos_st" ] = [];
pos_info["pos_rw" ] = [];
pos_info["pos_lm" ] = [];
pos_info["pos_am" ] = [];
pos_info["pos_cm" ] = [];
pos_info["pos_dm" ] = [];
pos_info["pos_rm" ] = [];
pos_info["pos_lwb"] = [];
pos_info["pos_rwb"] = [];
pos_info["pos_lb" ] = [];
pos_info["pos_cb" ] = [];
pos_info["pos_rb" ] = [];
pos_info["pos_gk" ] = [];
var pos_ret = [];

function getCount(from, to){
	$.ajax({
		type : "post",
		url : '/football_tpm/get_bestvote.php',
		data : {
			cmd : "count_formation",
			id : TEAM_ID
			, from: from, to: to
		},
		dataType : "json",
		success : function(data) {
			if(data.length == 0){
				$("#INFOMATION").html("데이터 없음.");
				return;
			}
			$("#INFOMATION").html("");
			$("#REG_FORMATION").val(data[0]["formation"]);
			setSelectedPosition();

			getCountPlayer(from, to);
		},
		error : function(err) {
			alert("오류발생 관리자에게 문의하세요.")
		}
	});
}

function getCountPlayer(from, to){
	$.ajax({
		type : "post",
		url : '/football_tpm/get_bestvote.php',
		data : {
			cmd : "count_player",
			id : TEAM_ID
			, from: from, to: to
		},
		dataType : "json",
		success : function(data) {
			var tbody = $("#RESULT_TBODY");
			for(var i=0; i<data.length; i++){
				var str = "<tr>";
					str += "<td>" + (i+1) + "</td>";
					str += "<td>" + data[i]["player_name"] + "</td>";
					str += "<td>" + data[i]["total_cnt"] + "</td>";
					str += "<td>" + getCountPlayerDetailInfo(data[i]) + "</td>";
					str += "</tr>";
				
				tbody.append(str);
			}

			setPositionByCount("pos_lw" );
			setPositionByCount("pos_st" );
			setPositionByCount("pos_rw" );
			setPositionByCount("pos_lm" );
			setPositionByCount("pos_am" );
			setPositionByCount("pos_cm" );
			setPositionByCount("pos_dm" );
			setPositionByCount("pos_rm" );
			setPositionByCount("pos_lwb");
			setPositionByCount("pos_rwb");
			setPositionByCount("pos_lb" );
			setPositionByCount("pos_cb" );
			setPositionByCount("pos_rb" );
			setPositionByCount("pos_gk" );
		},
		error : function(err) {
			alert("오류발생 관리자에게 문의하세요.")
		}
	});
}

function getCountPlayerDetailInfo(data){
	var str = "";
	if(parseInt(data["pos_st"]) > 0){ if(str != "") {str += "<br>";} str += "ST = " + data["pos_st"]; pos_info["pos_st"].push(data); }
	if(parseInt(data["pos_lw"]) > 0){ if(str != "") {str += "<br>";} str += "LW = " + data["pos_lw"]; pos_info["pos_lw"].push(data); }
	if(parseInt(data["pos_rw"]) > 0){ if(str != "") {str += "<br>";} str += "RW = " + data["pos_rw"]; pos_info["pos_rw"].push(data); }
	if(parseInt(data["pos_lm"]) > 0){ if(str != "") {str += "<br>";} str += "LM = " + data["pos_lm"]; pos_info["pos_lm"].push(data); }
	if(parseInt(data["pos_am"]) > 0){ if(str != "") {str += "<br>";} str += "AM = " + data["pos_am"]; pos_info["pos_am"].push(data); }
	if(parseInt(data["pos_cm"]) > 0){ if(str != "") {str += "<br>";} str += "CM = " + data["pos_cm"]; pos_info["pos_cm"].push(data); }
	if(parseInt(data["pos_dm"]) > 0){ if(str != "") {str += "<br>";} str += "DM = " + data["pos_dm"]; pos_info["pos_dm"].push(data); }
	if(parseInt(data["pos_rm"]) > 0){ if(str != "") {str += "<br>";} str += "RM = " + data["pos_rm"]; pos_info["pos_rm"].push(data); }
	if(parseInt(data["pos_lwb"]) > 0){ if(str != "") {str += "<br>";} str += "LWB = " + data["pos_lwb"]; pos_info["pos_lwb"].push(data); }
	if(parseInt(data["pos_rwb"]) > 0){ if(str != "") {str += "<br>";} str += "RWB = " + data["pos_rwb"]; pos_info["pos_rwb"].push(data); }
	if(parseInt(data["pos_lb"]) > 0){ if(str != "") {str += "<br>";} str += "LB = " + data["pos_lb"]; pos_info["pos_lb"].push(data); }
	if(parseInt(data["pos_cb"]) > 0){ if(str != "") {str += "<br>";} str += "CB = " + data["pos_cb"]; pos_info["pos_cb"].push(data); }
	if(parseInt(data["pos_rb"]) > 0){ if(str != "") {str += "<br>";} str += "RB = " + data["pos_rb"]; pos_info["pos_rb"].push(data); }
	if(parseInt(data["pos_gk"]) > 0){ if(str != "") {str += "<br>";} str += "GK = " + data["pos_gk"]; pos_info["pos_gk"].push(data); }
	return str;
}
function setPositionByCount(target){
	var sort = pos_info[target].sort((a,b) => b[target] - a[target]);
	var cnt = pos_cnt[target];
	var chk = 0;
	
	for(var i=0; sort[i] && chk < cnt; i++){
		if(pos_ret.indexOf(sort[i]["player_name"]) > -1){
			continue;
		}
		$("input[name="+target+"]")[chk].value = (sort[i]["player_name"])
		pos_ret.push(sort[i]["player_name"]);
		chk++;
	}
}

function setSelectedPosition(from, to){
	$("#POSITION_ATT").html("");
	$("#POSITION_MID_1").html("");
	$("#POSITION_MID_2").html("");
	$("#POSITION_MID_3").html("");
	$("#POSITION_DEF").html("");
	$("#POSITION_KEEP").html("");
	var val = $("#REG_FORMATION").val();
	if(val == ""){
		return;
	}

	var def = parseInt(val.substring(0, 1));
	var mid = parseInt(val.substring(1, 2));
	var att = parseInt(val.substring(2, 3));
	
	$.ajax({
		async: false,
		type : "post",
		url : './get_bestvote.php',
		data : {
			cmd : "info_formation",
			id : TEAM_ID,
			formation : val
			, from: from, to: to
		},
		dataType : "json",
		success : function(data) {
			setFormationInput($("#POSITION_ATT"), data[0], "pos_lw");
			setFormationInput($("#POSITION_ATT"), data[0], "pos_st");
			setFormationInput($("#POSITION_ATT"), data[0], "pos_rw");
			
			setFormationInput($("#POSITION_MID_1"), data[0], "pos_am");
			
			setFormationInput($("#POSITION_MID_2"), data[0], "pos_lm");
			setFormationInput($("#POSITION_MID_2"), data[0], "pos_cm");
			setFormationInput($("#POSITION_MID_2"), data[0], "pos_rm");

			setFormationInput($("#POSITION_MID_3"), data[0], "pos_lwb");
			setFormationInput($("#POSITION_MID_3"), data[0], "pos_dm");
			setFormationInput($("#POSITION_MID_3"), data[0], "pos_rwb");

			setFormationInput($("#POSITION_DEF"), data[0], "pos_lb");
			setFormationInput($("#POSITION_DEF"), data[0], "pos_cb");
			setFormationInput($("#POSITION_DEF"), data[0], "pos_rb");
		},
		error : function(err) {
			alert("오류발생 관리자에게 문의하세요.")
		}
	});

	var str  = "<input type='text' class='bestPlayer form-control' style='' name='pos_gk'/>";
	$("#POSITION_KEEP").html(str);
	pos_cnt["pos_gk"] = 1;

	$(".bestPlayer").addClass("form-control-lg");
	$(".bestPlayer").addClass("auto_font_size");
	$(".bestPlayer").addClass("text-center");

	$(".bestPlayer").each(function(){
		$(this).html($("#playersList").html());
	})
}

function setFormationInput(htmlTarget, datas, target){
	var value = parseInt(datas[target]);
	var stylevalue = "";
	if(target == 'pos_lwb'){
		stylevalue = "float:left;";
	}
	if(target == 'pos_rwb'){
		stylevalue = "float:right;";
	}
	for(var i=0; i<value; i++){
		htmlTarget.append("<input class='bestPlayer form-control' style='"+stylevalue+"' name='"+target+"'/>");
	}

	pos_cnt[target] = value;
}

var MatchCnt = 0;
var MIN_PER = 60;
function getTopScore(from, to, cnt){
	MatchCnt = 0;
	$.ajax({
		async : false,
		type : "post",
		url : '/football_tpm/get.php',
		data : {
			cmd : "play_cnt",
			id : TEAM_ID,
			st : from,
			ed : to
		},
		dataType : "json",
		success : function(data) {
			MatchCnt = data[0]["total_cnt"];
			$("#match_cnt").html(from + " ~ " + to + " 결산 <br/> 경기 수 : " + MatchCnt);
		},
		error : function(err) {
			alert("오류발생 관리자에게 문의하세요.")
		}
	});

	getTopList(from, to, cnt);
}

function getTopList(from, to, max){
	$.ajax({
		type : "post",
		url : '/football_tpm/get.php',
		data : {
			cmd : "tpm_view_search",
			id : TEAM_ID,
			st : from,
			ed : to
		},
		dataType : "json",
		success : function(data) {
			$(".VIEW_BEST_TBODY").each(function(){
				$(this).html("");
			})
			var i = 0;
			if(data.length < max){
				max = data.length;
			}
			//출석율
			data.sort(function(a,b){
				return sortDesc(a["play_cnt"], b["play_cnt"])
			});
			for(i=0; i<max; i++){
				var tmpName = data[i]["player_name"];
				var tmpValue = data[i]["play_cnt"];
				setHtml(tmpName, tmpValue, (i+1), "VIEW_BEST_PLAY_TBODY");
			}
			//골
			data.sort(function(a,b){
				return sortDesc(a["goal_cnt"], b["goal_cnt"])
			});
			for(i=0; i<max; i++){
				var goal = data[i]["goal_cnt"];
				var play = data[i]["play_cnt"];
				var goal_per = parseInt( parseFloat(goal) / parseFloat(play) * 100 ) / 100.0;
				var tmpName = data[i]["player_name"];
				var tmpValue = goal + " ("+goal_per+")";
				setHtml(tmpName, tmpValue, (i+1), "VIEW_BEST_GOAL_TBODY");
			}
			//어시
			data.sort(function(a,b){
				return sortDesc(a["asst_cnt"], b["asst_cnt"])
			});
			for(i=0; i<max; i++){
				var asst = data[i]["asst_cnt"];
				var play = data[i]["play_cnt"];
				var asst_per = parseInt( parseFloat(asst) / parseFloat(play) * 100 ) / 100.0;
				var tmpName = data[i]["player_name"];
				var tmpValue = asst + " ("+asst_per+")";
				setHtml(tmpName, tmpValue, (i+1), "VIEW_BEST_ASST_TBODY");
			}
			//승점
			data.sort(function(a,b){
				return sortDesc(a["pts"], b["pts"])
			});
			for(i=0; i<max; i++){
				var tmpName = data[i]["player_name"];
				var tmpValue = data[i]["pts"];
				setHtml(tmpName, tmpValue, (i+1), "VIEW_BEST_PTS_TBODY");
			}
			//승률
			data.sort(function(a,b){
				var win_per_a = parseInt(parseFloat(a["win_cnt"])
						/ parseFloat(a["play_cnt"]) * 100);
				var win_per_b = parseInt(parseFloat(b["win_cnt"])
						/ parseFloat(b["play_cnt"]) * 100);
				return sortDesc(win_per_a, win_per_b)
			});
			var winperCnt = 0;
			for(i=0; i<data.length && winperCnt < max; i++){
				var tmpPlay = data[i]["play_cnt"];
				var win_per = parseInt(parseFloat(data[i]["win_cnt"])
						/ parseFloat(tmpPlay) * 100);
				var play_per = parseFloat(tmpPlay) / MatchCnt * 100;
				if(play_per >= MIN_PER){
					var tmpName = data[i]["player_name"];
					var tmpValue = win_per;
					setHtml(tmpName, tmpValue + " ("+data[i]["win_cnt"]+")", (winperCnt+1), "VIEW_BEST_WINPER_TBODY");
					winperCnt++;
				}
			}
			//수비
			$.ajax({
				type : "post",
				url : '/football_tpm/get_defence.php',
				data : {
					cmd : "player_scoreless",
					id : TEAM_ID,
					st : from,
					ed : to
				},
				dataType: "json",
				success : function(ddata, textStatus, jqXHR) {
					for(i=0; i<max; i++){
						var tmpName = ddata[i]["player_name"];
						var tmpValue = ddata[i]["ls_cnt"];
						setHtml(tmpName, tmpValue, (i+1), "VIEW_BEST_DEF_TBODY");
					}
				}
			});
		},
		error : function(err) {
			alert("오류발생 관리자에게 문의하세요.")
		}
	});
}

function sortDesc(a, b){
	a = parseFloat(a);
	b = parseFloat(b);
	if(a > b) return -1;
	if(a === b) return 0;
	if(a < b) return 1;			
}

function setHtml(name, value, idx, target){
	$("#"+target).append("<tr>"
	+"<td>"+idx+"</td>"
	+"<td>"+name+"</td>"
	+"<td>"+value+"</td>"
	+"</tr>");
}