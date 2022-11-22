
	let today = new Date();
	let year = today.getFullYear(); // 년도
	let month = today.getMonth() + 1;  // 월
	let date = today.getDate();  // 날짜
	let day = today.getDay();  // 요일
	let TODAY_YMD = year + "" + month + "" + date;
	let DEFAULT_START_MATCH_DATE = year + ".01.01";

	function setLayout(){
		$("#searchStartDate").val(DEFAULT_START_MATCH_DATE);
		$("#footers").load("/football_tpm/html/footer.html", function(){
			
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
		});

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
		if(TEAM_ID != 0 && TEAM_NAME != ''){
			$("#header_1").text(TEAM_NAME);
			$("#nav_header").text(TEAM_NAME);
			getPlayCnt();
			return;
		}

		$.ajax({
			type : "post",
			async: false,
			url : '/football_tpm/get.php',
			data : {
				cmd : "team",
				id : TEAM_ID
			},
			dataType : "json",
			success : function(data) {
				$("#header_logo").html("<img src='/football_tpm/img/LOGO_1.png' style='width:40px;' />");
				$("#header_1").text(data[0]["team_name"]);
				$("#nav_header").text(data[0]["team_name"]);
			},
			error : function(err) {
				alert("오류발생 관리자에게 문의하세요.")
			}
		});
	}

	function getPlayCnt(){
		$.ajax({
			type : "post",
			async: false,
			url : '/football_tpm/get.php',
			data : {
				cmd : "play_cnt",
				id : TEAM_ID
			},
			dataType : "json",
			success : function(data) {
				PLAY_CNT = data[0]["total_cnt"];
				$("#total_match_cnt").text("기록 경기 수: " + PLAY_CNT);
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
				id : TEAM_ID,
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
			str += "<td>" + play + " ( "
					+ play_per + " % )</td>";
			str += "<td>" + goal + " ( "
					+ goal_per + " )</td>";
			str += "<td>" + asst + " ( "
					+ asst_per + " )</td>";
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
				id : TEAM_ID,
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
				id : TEAM_ID,
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
					id : TEAM_ID,
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
	
	function selectMatchList(){
		$.ajax({
			type : "post",
			url : '/football_tpm/get.php',
			data : {
				cmd : "matchlist",
				id : TEAM_ID
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
				id : TEAM_ID,
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
					id : TEAM_ID,
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