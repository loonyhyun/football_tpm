
    var urlParams = location.search.split(/[?&]/).slice(1).map(function(paramPair) {
		return paramPair.split(/=(.+)?/).slice(0, 2);
	}).reduce(function(obj, pairArray) {
		obj[pairArray[0]] = pairArray[1];
		return obj;
	}, {});
	
	let today = new Date();
	let year = today.getFullYear(); // 년도
	let month = today.getMonth() + 1;  // 월
	let date = today.getDate();  // 날짜
	let day = today.getDay();  // 요일
	let TODAY_YMD = year + "" + month + "" + date;
	let DEFAULT_START_MATCH_DATE = year + ".01.01";

	let FIRST_LOADING = true;
	function setLayout(){

		if(FIRST_LOADING){
			$("#searchStartDate").val(DEFAULT_START_MATCH_DATE);
			
			$('#input_match_date').datepicker({
				format: "yyyy.mm.dd",
				autoclose: true,
				todayHighlight: true,
				language: "ko"
			});
			$('#searchStartDate').datepicker({
				format: "yyyy.mm.dd",
				autoclose: true,
				todayHighlight: true,
				language: "ko"
			});
			$('#searchEndDate').datepicker({
				format: "yyyy.mm.dd",
				autoclose: true,
				todayHighlight: true,
				language: "ko"
			});
			
			FIRST_LOADING = false;
		}

		$("#team_choice").css("max-height", $(window).height() - 350);
		$("#team_a_tbody").css("max-height", $(window).height() - 350);
		$("#team_b_tbody").css("max-height", $(window).height() - 350);

		$("#match_choice").css("max-height", $(window).height() - 383);
		$("#match_a_tbody").css("max-height", $(window).height() - 383);
		$("#match_b_tbody").css("max-height", $(window).height() - 383);
		
		$(document.body).css("background-image", "url(/football_tpm/img/background.png)");
		$(document.body).css("background-repeat", "repeat-y");
		$(document.body).css("background-position", "center");
		//$(".container").css("background-color", "white");
	}

	var BEFORE_SORT = 0;
	function SortTable( n, order )
	{
		var BF_SORT_TH = $("#PlayerMainTable > thead > tr").children().eq(BEFORE_SORT);
		BF_SORT_TH.css("background-color", "");
		BF_SORT_TH.css("color", "");
		var SORT_TH = $("#PlayerMainTable > thead > tr").children().eq(n);
		SORT_TH.css("background-color", "#343400");
		SORT_TH.css("color", "#ffffff");
		BEFORE_SORT = n;

		var table = document.getElementById("PlayerMainTable");
		var tbody = table.tBodies[0];
		var rows = tbody.getElementsByTagName("tr");

		rows = Array.prototype.slice.call(rows, 0);

		rows.sort(function(row1, row2) {
			var cell1 = row1.getElementsByTagName("td")[n];
			var cell2 = row2.getElementsByTagName("td")[n];
			var value1 = cell1.textContent || cell1.innerText;
			var value2 = cell2.textContent || cell2.innerText;

			value1 = value1.trim();
			value2 = value2.trim();
			if(n < 5){
				if(value1.indexOf("(") > -1){
					value1 = (value1.substring(0, value1.indexOf("(")));
				}
				if(value2.indexOf("(") > -1){
					value2 = (value2.substring(0, value2.indexOf("(")));
				}
			}else{
				if(value1.indexOf("%") > -1){
					value1 = (value1.substring(0, value1.indexOf("%")));
				}
				if(value2.indexOf("%") > -1){
					value2 = (value2.substring(0, value2.indexOf("%")));
				}
			}
			if(!isNaN(value1)){
				value1 = parseFloat(value1);
			}
			if(!isNaN(value2)){
				value2 = parseFloat(value2);
			}

			if(order == "DESC"){
				if (value1 < value2)
					return 1;
				if (value1 > value2)
					return -1;
			}else{
				if (value1 < value2)
					return -1;
				if (value1 > value2)
					return 1;
			}

			return 0;
		});

		for (var i = 0; i < rows.length; ++i) {
			rows[i].firstChild.innerHTML = (i+1)
			tbody.appendChild(rows[i]);
		}
	}

	function getTeam() {

		$.ajax({
			type : "post",
			async: false,
			url : '/football_tpm/get.php',
			data : {
				cmd : "team",
				id : 1
			},
			dataType : "json",
			success : function(data) {
				$("#header_logo").html("<img src='/football_tpm/img/LOGO_1.png' style='width:40px;' />");
				$("#header_1").text(data[0]["team_name"]);
				$("#nav_header").text(data[0]["team_name"]);
				$("#total_match_cnt").text("기록 경기 수: " + data[0]["total_cnt"]);
			},
			error : function(err) {
				alert("오류발생 관리자에게 문의하세요.")
			}
		});
	}
	
	function getPlayerList() {

		$.ajax({
			type : "post",
			url : '/football_tpm/get.php',
			data : {
				cmd : "player",
				//cmd : "tpm_view",
				id : 1
			},
			dataType : "json",
			success : function(data) {
				$("#team_choice").html("");
				setPlayerList(data);
			},
			error : function(err) {
				alert("오류발생 관리자에게 문의하세요.")
			}
		});
	}
	function setPlayerList(data){
		for (var i = 0; i < data.length; i++) {
			var playerId = data[i]["player_id"];
			var playerName = data[i]["player_name"]
			str = "<p class='mgb5'>" + data[i]["player_name"];
			str += " <button type='button' class='btn btn-danger btn-sm' onclick=\"setTeam('A', "
					+ playerId
					+ ", '"
					+ playerName
					+ "')\">A팀</button>";
			str += " <button type='button' class='btn btn-primary btn-sm' onclick=\"setTeam('B', "
					+ playerId
					+ ", '"
					+ playerName
					+ "')\">B팀</button>";
			str += "</p>";
			$("#team_choice").append(str);
		}
	}

	function getMatchCnt(){
		$.ajax({
			type : "post",
			url : '/football_tpm/get.php',
			data : {
				cmd : "play_cnt",
				id : 1,
				st : $("#searchStartDate").val(),
				ed : $("#searchEndDate").val()
			},
			dataType : "json",
			success : function(data) {
				$("#match_cnt").text("조회 경기 수: " + data[0]["total_cnt"]);
			},
			error : function(err) {
				alert("오류발생 관리자에게 문의하세요.")
			}
		});
	}

	function getPlayer() {

		$.ajax({
			type : "post",
			url : '/football_tpm/get.php',
			data : {
				//cmd : "player"
				cmd : "tpm_view_search",
				id : 1,
				st : $("#searchStartDate").val(),
				ed : $("#searchEndDate").val()
			},
			dataType : "json",
			success : function(data) {
				$("#playerTbody").html("");
				$("#team_choice").html("");
				setPlayer(data);
			},
			error : function(err) {
				alert("오류발생 관리자에게 문의하세요.")
			}
		});

		getMatchCnt();
	}
	function setPlayer(data){
		for (var i = 0; i < data.length; i++) {
			var wins = data[i]["win_cnt"];
			var play = data[i]["play_cnt"];
			var goal = data[i]["goal_cnt"];
			var asst = data[i]["asst_cnt"];
			var play_per = parseInt(parseFloat(play)
					/ parseFloat(data[i]["match_total_cnt"])
					* 100);
			var win_per = parseInt(parseFloat(wins)
					/ parseFloat(play) * 100);

			var goal_per = parseInt( parseFloat(goal) / parseFloat(play) * 100 ) / 100.0;
			var asst_per = parseInt( parseFloat(asst) / parseFloat(play) * 100 ) / 100.0;
			if (isNaN(play_per)) { play_per = 0; }
			if (isNaN(win_per)) { win_per = 0; }
			if (isNaN(goal_per)) { goal_per = 0; }
			if (isNaN(asst_per)) { asst_per = 0; }

			var playerId = data[i]["player_id"];
			var playerName = data[i]["player_name"]
			var str = "<tr id='player_"+playerId+"' data-bs-toggle=\"modal\" data-bs-target=\"#updatePlayerDiv\" ";
			str += " onclick=\"updatePlayerInfo('"+playerId+"');\" >";
			str += "<td scope='row'>" + (i + 1) + "</td>";
			str += "<td>" + playerName + "</td>";
			str += "<td>" + play + " ( " + play_per + " % )</td>";
			str += "<td>" + goal + " ( " + goal_per + " )</td>";
			str += "<td>" + asst + " ( "+ asst_per + " )</td>";
			//str += "<td>" + wins + " ( " + win_per + " % )</td>";
			str += "<td>" + win_per + " % ( " + wins + " )</td>";
			str += "<td>" + parseInt(data[i]["pts"]) + "</td>";
			str += "</tr>";
			$("#playerTbody").append(str);
		}
	}
	function getPlayerSearch(){
		//if($("#searchStartDate").val() == "" || $("#searchEndDate").val() == ""){
		//	alert("조회 기간을 선택하세요.");
		//	return;
		//}
		
		var BF_SORT_TH = $("#PlayerMainTable > thead > tr").children().eq(BEFORE_SORT);
		BF_SORT_TH.css("background-color", "");
		BF_SORT_TH.css("color", "");

		$.ajax({
			type : "post",
			url : '/football_tpm/get.php',
			data : {
				cmd : "tpm_view_search",
				id : 1,
				st : $("#searchStartDate").val(),
				ed : $("#searchEndDate").val()
			},
			dataType : "json",
			success : function(data) {
				$("#playerTbody").html("");
				$("#team_choice").html("");
				setPlayer(data);
				searchTablePlayer();
			},
			error : function(err) {
				alert("오류발생 관리자에게 문의하세요.")
			}
		});
		
		getMatchCnt();
	}

	function createPlayer() {
		if ($("#player_name").val() == "") {
			alert("이름을 입력하세요.");
			return;
		}
		if ($("#player_backno").val() == "") {
			$("#player_backno").val(0);
		}
		$.ajax({
			type : "post",
			url : '/football_tpm/save.php',
			data : {
				cmd : "player",
				id : 1,
				player_name : $("#player_name").val(),
				back_no : $("#player_backno").val(),
				position : $("#player_position").val(),
				desc : $("#player_desc").val()
			},
			success : function(data, textStatus, jqXHR) {
				if (data == "ok") {
					location.reload();
				}
			}
		});
	}
	
	function updatePlayerInfo(pid){
        $("#player_update_id").val("");
        $("#player_update_name").val("");
        $("#player_update_backno").val("");
        $("#player_update_position").val("");
        $("#player_update_desc").val("");
        $("#player_total_play_info").html("Loading...");

		$.ajax({
			type : "post",
			url : '/football_tpm/get.php',
			data : {
				cmd : "player_one",
				id : 1,
				playerId: pid
			},
			dataType : "json",
			success : function(data, textStatus, jqXHR) {
				var tmp = data[0];
				var pname = tmp["player_name"];
				var bno = tmp["back_no"];
                var position = tmp["position"];
				var desc = tmp["desc"];

				$("#player_update_id").val(pid);
				$("#player_update_name").val(pname);
				$("#player_update_backno").val(bno);
				$("#player_update_position").val(position);
				$("#player_update_desc").val(desc);

                //total info
                $.ajax({
                    type : "post",
                    url : '/football_tpm/get.php',
                    data : {
                        cmd : "player_sum",
                        id : 1,
                        playerId: pid
                    },
                    dataType : "json",
                    success : function(data, textStatus, jqXHR) {
                        var tmp = data[0];
                        var goal = tmp["goal_cnt"];
                        var asst = tmp["asst_cnt"];
                        var play = tmp["play_cnt"];
                        var wins = tmp["win_cnt"];
                        var ties = tmp["tie_cnt"];
                        var defeat = play - wins - ties;
                        
                        var win_per = parseInt(
                            (parseFloat(wins) + parseFloat(ties)*0.5) / parseFloat(play) * 100
                        );
						var goal_per = parseInt( parseFloat(goal) / parseFloat(play) * 100 ) / 100.0;
						var asst_per = parseInt( parseFloat(asst) / parseFloat(play) * 100 ) / 100.0;

                        //player_total_play_info
                        $("#player_total_play_info").html(
                            play + "전 " + wins + "승 " + ties + "무 " + defeat + "패"
                            + "<br>승률 " + win_per + "%"
                            + "<br>" + goal + "골 " + asst + "어시"
							+ "<br>경기당 득점 " + goal_per + ", 어시 " + asst_per + ""
                        );
                    }
                });
                //match list
                /*
                $.ajax({
                    type : "post",
                    url : '/football_tpm/get.php',
                    data : {
                        cmd : "player_matches",
                        id : 1,
                        playerId: pid
                    },
                    dataType : "json",
                    success : function(data, textStatus, jqXHR) {
                        var tmp = data[0];
                        var pname = tmp["player_name"];
                        var bno = tmp["back_no"];
                        var desc = tmp["desc"];
                    }
                });
                */
			}
		});
	}
	
	function updatePlayer(){
		if(confirm("선수 정보를 수정하시겠습니까?")){
			if ($("#player_update_name").val() == "") {
				alert("이름을 입력하세요.");
				return;
			}
			if ($("#player_update_backno").val() == "") {
				$("#player_update_backno").val(0);
			}
			$.ajax({
				type : "post",
				url : '/football_tpm/save.php',
				data : {
					cmd : "player_update",
					id : 1,
					player_id: $("#player_update_id").val(),
					player_name : $("#player_update_name").val(),
					back_no : $("#player_update_backno").val(),
					position : $("#player_update_position").val(),
					desc : $("#player_update_desc").val()
				},
				success : function(data, textStatus, jqXHR) {
					if (data == "ok") {
						location.reload();
					}
				}
			});
		}
	}

	var array_A = [];
	var array_B = [];
	function setTeam(team, id, name) {
		var aidx = array_A.indexOf(id);
		var bidx = array_B.indexOf(id);
		if (aidx > -1) {
			array_A.splice(aidx, 1);
			$("#teamA_" + id).remove();
		}
		if (bidx > -1) {
			array_B.splice(aidx, 1);
			$("#teamB_" + id).remove();
		}
		if (team == "A") {
			array_A.push(id);

			var str = "<p id='teamA_"+id+"' class='mgb5'>" + name
			str += " <input type='text' name='teamAgoal' id='teamAgoal_"
					+ id
					+ "' value='0' style='width:24px;' onKeyup=\"this.value=this.value.replace(/[^0-9]/g,'');\">"
			str += " <input type='text' name='teamAasst' id='teamAasst_"
					+ id
					+ "' value='0' style='width:24px;' onKeyup=\"this.value=this.value.replace(/[^0-9]/g,'');\"></p>"
			$("#team_a_tbody").append(str);
		} else if (team == "B") {
			array_B.push(id);

			var str = "<p id='teamB_"+id+"' class='mgb5'>" + name
			str += " <input type='text' name='teamBgoal' id='teamBgoal_"
					+ id
					+ "' value='0' style='width:24px;' onKeyup=\"this.value=this.value.replace(/[^0-9]/g,'');\">"
			str += " <input type='text' name='teamBasst' id='teamBasst_"
					+ id
					+ "' value='0' style='width:24px;' onKeyup=\"this.value=this.value.replace(/[^0-9]/g,'');\"></p>"
			$("#team_b_tbody").append(str);
		}
	}

	function saveMatch() {
		var teamA = "$";
		var teamB = "$";
		var goalA = "$";
		var goalB = "$";
		var asstA = "$";
		var asstB = "$";

		if ($("#input_match_date").val() == "") {
			alert("경기일자를 선택하세요.");
			return;
		}

		for (var i = 0; i < array_A.length; i++) {
			teamA += array_A[i] + "$";
		}
		for (var i = 0; i < array_B.length; i++) {
			teamB += array_B[i] + "$";
		}
		for (var i = 0; i < array_A.length; i++) {
			if ($("#teamAgoal_" + array_A[i]).val() != 0) {
				goalA += array_A[i] + "|" + $("#teamAgoal_" + array_A[i]).val()
						+ "$";
			}
		}
		for (var i = 0; i < array_B.length; i++) {
			if ($("#teamBgoal_" + array_B[i]).val() != 0) {
				goalB += array_B[i] + "|" + $("#teamBgoal_" + array_B[i]).val()
						+ "$";
			}
		}
		for (var i = 0; i < array_A.length; i++) {
			if ($("#teamAasst_" + array_A[i]).val() != 0) {
				asstA += array_A[i] + "|" + $("#teamAasst_" + array_A[i]).val()
						+ "$";
			}
		}
		for (var i = 0; i < array_B.length; i++) {
			if ($("#teamBasst_" + array_B[i]).val() != 0) {
				asstB += array_B[i] + "|" + $("#teamBasst_" + array_B[i]).val()
						+ "$";
			}
		}

		if (confirm("경기를 등록하시겠습니까?")) {

			$.ajax({
				type : "post",
				url : '/football_tpm/save.php',
				data : {
					cmd : "match",
					id : 1,
					match_date : $("#input_match_date").val(),
					teamA : teamA,
					teamB : teamB,
					goalA : goalA,
					goalB : goalB,
					asstA : asstA,
					asstB : asstB,
					winAB : $("#win_ab").val()
				},
				success : function(data, textStatus, jqXHR) {
					if (data == "ok") {
						location.reload();
					}
				}
			});
		}
	}
	
	function selectMatchList(){
		$.ajax({
			type : "post",
			url : '/football_tpm/get.php',
			data : {
				cmd : "matchlist",
				id : 1
			},
			dataType:"json",
			success : function(data, textStatus, jqXHR) {
				$("#match_choice").html("");
				$("#match_a_tbody").html("");
				$("#match_b_tbody").html("");
				
				for(var i=0; i<data.length; i++){
					var tmp = data[i];
					var match_id = tmp["match_id"];
					var match_date = tmp["match_date"];
					var str = "<p class='mgt10 mgb5 pointercursor font14px' id='matchlist_"+match_id+"' onclick='selectMatch("+match_id+")'>" +match_date+ "</p>";
					$("#match_choice").append(str);
				}
			}
		});
	}
	
	var selectedMatchId = -1;
	function selectMatch(id){
		$("#matchlist_" + selectedMatchId).css("background-color", "");
		$("#matchlist_" + id).css("background-color", "#ffd756");
		selectedMatchId = id;
		
		$.ajax({
			type : "post",
			url : '/football_tpm/get.php',
			data : {
				cmd : "match",
				id : 1,
				match_id : id
			},
			dataType: "json",
			success : function(data, textStatus, jqXHR) {
				$("#match_a_tbody").html("");
				$("#match_b_tbody").html("");
				var abody = $("#match_a_tbody");
				var bbody = $("#match_b_tbody");
				
				var win_ab = data[0]["win_ab"];
				var winstr = "";
				if(win_ab == "a"){
					winstr = "A팀 (RED) 승";
				}else if(win_ab == "b"){
					winstr = "B팀 (BLUE) 승";
				}else if(win_ab == "-"){
					winstr = "무승부";
				}
				$("#match_result").html(winstr);
				$("#delete_match_id").val(id);
				
				for(var i=1; i<data.length; i++){
					var tmp = data[i];
					var str = "<p class='mbt10 mgb5'>"+tmp["player_name"]+" ( "+tmp["goal_cnt"]+"/"+tmp["asst_cnt"]+" )</p>";
					if(tmp["team_a_yn"] == 1){
						abody.append(str);
					}
					else if(tmp["team_b_yn"] == 1){
						bbody.append(str);
					}
				}
			}
		});
	}
	
	function searchTablePlayer() {
		var tmp = $("#searchPlayerName").val();
		
		$("#playerTbody > tr").each(function(){
			if($(this).children().eq(1).text().indexOf(tmp) > -1){
				$(this).show();
			}else{
				$(this).hide();
			}
		});
	}
	
	function deleteMatch(){
		if(confirm("경기 기록을 삭제하시겠습니까?\n복구할 수 없습니다.")){

			$.ajax({
				type : "post",
				url : '/football_tpm/delete.php',
				data : {
					cmd : "match",
					id : 1,
					match_id : $("#delete_match_id").val(),
					match_secret : $("#delete_secret").val()
				},
				success : function(data, textStatus, jqXHR) {
					if (data == "ok") {
						alert("삭제 되었습니다.");
						location.reload();
					}
					else if(data == "fail secret"){
						alert("SECRET FAIL");
					}
				}
			});
		}
	}