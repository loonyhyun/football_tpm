

	var selectedPlayers = [];
	var attendPlayers = [];
	function setQuarterTbody(team, pid, pname){
		if(team == 'a'){
			$("#TEAM_A_TBODY").append("<tr class='players_quarter' id='player_"+pid+"'>"
				+"<td>"+pname+"</td>"
				+"</tr>"
			);
		}else if(team == 'b'){
			$("#TEAM_B_TBODY").append("<tr class='players_quarter' id='player_"+pid+"'>"
				+"<td>"+pname+"</td>"
				+"</tr>"
			);
		}
		selectedPlayers.push(pid);
	}

	function getInitAttend(){
		$.ajax({
			async: false,
			type : "post",
			url : '/football_tpm/get.php',
			data : {
				cmd : "attend",
				id : 1,
			},
			dataType : "json",
			success : function(data) {
				for(var i=0; i<data.length; i++){
					var tmp = data[i];
					var pid = tmp["player_id"];
					var pname = tmp["player_name"];
					var team = tmp["team_type"];
					setQuarterTbody(team, pid, pname);
					attendPlayers[pid] = tmp;
				}
			},
			error : function(err) {
				alert("오류발생 관리자에게 문의하세요.")
			}
		});
	}

	function setQuarterInit(){
		var lastidx = parseInt($("#QUARTER_NUM").val());
		var astr = "";
			astr += "<td>A팀</td>";
			astr += "<td>GOAL</td>";
			astr += "<td>ASST</td>";
			astr += "<td>조퇴</td>";
		for(var i=0; i<lastidx; i++){
			astr += "<td onclick=\"simulFormation("+(i+1)+", 'a');\">"+(i+1)+"쿼</td>";
		}
		$("#TEAM_A_THEAD").html(astr);
		var bstr = "";
			bstr += "<td>B팀</td>";
			bstr += "<td>GOAL</td>";
			bstr += "<td>ASST</td>";
			bstr += "<td>조퇴</td>";
		for(var i=0; i<lastidx; i++){
			bstr += "<td onclick=\"simulFormation("+(i+1)+", 'b');\">"+(i+1)+"쿼</td>";
		}
		$("#TEAM_B_THEAD").html(bstr);

		$(".players_quarter").each(function(){
			var pid = parseInt($(this).attr("id").replace("player_", ""));
			var tmp = attendPlayers[pid];
			var pname = tmp["player_name"];

			var str = "";
			str += "<td><input type='hidden' name='player' id='player_a_"+pid+"' value='"+pid+"'/>"+pname+"</td>";
			str += "<td><input type='number' name='goal' id='goal_"+pid+"' style='width:30px;' value='0'/></td>";
			str += "<td><input type='number' name='asst' id='asst_"+pid+"' style='width:30px;' value='0'/></td>";
			str += "<td onclick=\"setLeaveEarly(this);\" id='leave_"+pid+"'>-</td>";
			
			for(var i=0; i<lastidx; i++){
				str += "<td onclick='setQuarter(this);' class='q_"+(i+1)+"' id='q_"+(i+1)+"_"+pid+"'></td>";
			}
			$(this).html(str);
		});

		$("#DEF_A").html("<td style=\"background-color:#ffa2a2;\">A팀</td>");
		$("#DEF_B").html("<td style=\"background-color:#9d9dff;\">B팀</td>");
		for(var i=0; i<lastidx; i++){
			$("#DEF_A").append("<td onclick='setDefQuarter(this);' class='def_a' id='def_a_"+(i+1)+"'></td>");
			$("#DEF_B").append("<td onclick='setDefQuarter(this);' class='def_b' id='def_b_"+(i+1)+"'></td>");
		}
	}

	function setLeaveEarly(obj){
		var lastidx = parseInt($("#QUARTER_NUM").val());
		var txt = $(obj).text();
		if(txt == "-"){
			txt = "1쿼후";
		}
		else{
			if((lastidx-1) + "쿼후" == txt){
				txt = "-";
			}
			else{
				txt = (parseInt(txt.substring(0, 1)) + 1) + "쿼후";
			}
		}
		$(obj).text(txt);
	}
	function setQuarter(obj){
		var txt = $(obj).text();
		if(txt == ""){
			txt = "o";
		}else if(txt == "o"){
			txt = "GK";
		}
		else{
			txt = "";
		}
		$(obj).text(txt);
	}
	function setDefQuarter(obj){
		var txt = $(obj).text();
		if(txt == ""){
			txt = "o";
		}else{
			txt = "";
		}
		$(obj).text(txt);
	}

	var getAttendData = null;
	function getAttendQuater(){
		$.ajax({
			type : "post",
			url : '/football_tpm/get.php',
			data : {
				cmd : "attend_quater",
				id : 1
			},
			dataType : "json",
			success : function(data) {
				getAttendData = data;
				if(data != null){
					if(data[0] != null){
						var team_a = data[0]["team_a"];
						var team_b = data[0]["team_b"];
						var quarters = data[0]["quarters"];
						$("#QUARTER_NUM").val(quarters);
						var lastidx = parseInt($("#QUARTER_NUM").val());

						var astr = "";
							astr += "<td>A팀</td>";
							astr += "<td>GOAL</td>";
							astr += "<td>ASST</td>";
							astr += "<td>조퇴</td>";
						for(var i=0; i<lastidx; i++){
							astr += "<td>"+(i+1)+"쿼</td>";
						}
						$("#TEAM_A_THEAD").html(astr);
						var bstr = "";
							bstr += "<td>B팀</td>";
							bstr += "<td>GOAL</td>";
							bstr += "<td>ASST</td>";
							bstr += "<td>조퇴</td>";
						for(var i=0; i<lastidx; i++){
							bstr += "<td>"+(i+1)+"쿼</td>";
						}
						$("#TEAM_B_THEAD").html(bstr);

						setQuarterInit();

						var team = team_a.split("$");
						for(var i=0; i<team.length; i++){
							var tmp = team[i].split("|");
							if(tmp.length < 2){
								continue;
							}
							var pid = 0;
							var goal = 0;
							var asst = 0;
							var leave = "-";
							var qs = [];
							for(var j=0; j<tmp.length; j++){
								if(tmp[j] != undefined && tmp[j] != ""){
									var sets = tmp[j].split("=");
									if(sets[0] == "pid"){
										pid = sets[1];
									}else if(sets[0] == "goal"){
										goal = sets[1];
									}else if(sets[0] == "asst"){
										asst = sets[1];
									}else if(sets[0] == "leave"){
										leave = sets[1];
									}else{
										qs.push(sets[1]);
									}
								}
							}

							var target = $("#player_" + pid);
							$("#goal_" + pid).val(goal);
							$("#asst_" + pid).val(asst);
							$("#leave_" + pid).text(leave);

							if(qs != undefined && qs.length > 1){
								for(var j=0; j<lastidx; j++){
									$("#q_" + (j+1) + "_" + pid).text(qs[j]);
								}
							}
						}

						team = team_b.split("$");
						for(var i=0; i<team.length; i++){
							var tmp = team[i].split("|");
							if(tmp.length < 2){
								continue;
							}
							var pid = 0;
							var goal = 0;
							var asst = 0;
							var leave = "-";
							var qs = [];
							for(var j=0; j<tmp.length; j++){
								if(tmp[j] != undefined && tmp[j] != ""){
									var sets = tmp[j].split("=");
									if(sets[0] == "pid"){
										pid = sets[1];
									}else if(sets[0] == "goal"){
										goal = sets[1];
									}else if(sets[0] == "asst"){
										asst = sets[1];
									}else if(sets[0] == "leave"){
										leave = sets[1];
									}else{
										qs.push(sets[1]);
									}
								}
							}

							var target = $("#player_" + pid);
							$("#goal_" + pid).val(goal);
							$("#asst_" + pid).val(asst);
							$("#leave_" + pid).text(leave);

							if(qs != undefined && qs.length > 1){
								for(var j=0; j<lastidx; j++){
									$("#q_" + (j+1) + "_" + pid).text(qs[j]);
								}
							}
						}

						if(data[0]["def_a"] != null && data[0]["def_a"] != "" && data[0]["def_a"] != undefined){
							var def_a = data[0]["def_a"].split(";");
							for(var di = 0; di<def_a.length; di++){
								$("#def_a_" + def_a[di]).text("o");
							}
						}
						if(data[0]["def_b"] != null && data[0]["def_b"] != "" && data[0]["def_b"] != undefined){
							var def_b = data[0]["def_b"].split(";");
							for(var di = 0; di<def_b.length; di++){
								$("#def_b_" + def_b[di]).text("o");
							}
						}
						
					}//End. if(data[0] != null)
				}//End. if(data != null)
			},
			error : function(err) {
				alert("오류발생 관리자에게 문의하세요.")
			}
		});
	}
	
	function saveAttendQuater(){
		var strA = "$";
		var strB = "$";
		var chkidx = 0;
		var lastidx = parseInt($("#QUARTER_NUM").val());

		$(".players_quarter").each(function(){
			var team = $(this).parent().attr("id");
			var str = "";

			var pid = parseInt($(this).attr("id").replace("player_", ""));
			var tmp = attendPlayers[pid];
			var pname = tmp["player_name"];

			var goal = $("#goal_" + pid).val();
			var asst = $("#asst_" + pid).val();
			var leave = $("#leave_" + pid).text();

			str += "pid=" + pid + "|";
			str += "goal=" + goal + "|";
			str += "asst=" + asst + "|";
			str += "leave=" + leave + "|";

			for(var i=0; i<lastidx; i++){
				str += "q" + (i+1) + "=" + $("#q_" + (i+1) + "_" + pid).text() + "|";
			}

			if(team == "TEAM_A_TBODY"){
				strA += str + "$";
			}else if(team == "TEAM_B_TBODY"){
				strB += str + "$";
			}
		});

		var str_defA = "";
		var str_defB = "";
		$(".def_a").each(function(){
			var did = $(this).attr("id").replace("def_a_", "");
			if($(this).text() != ""){
				str_defA += did + ";"
			}
		});
		$(".def_b").each(function(){
			var did = $(this).attr("id").replace("def_b_", "");
			if($(this).text() != ""){
				str_defB += did + ";"
			}
		});

		$.ajax({
			type : "post",
			url : '/football_tpm/save.php',
			data : {
				cmd : "attend_quater",
				id : 1,
				team_a : strA,
				team_b : strB,
				quarters: $("#QUARTER_NUM").val(),
				def_a : str_defA,
				def_b : str_defB
			},
			success : function(data) {
				if(data == "ok"){
					location.reload();
				}
			},
			error : function(err) {
				alert("오류발생 관리자에게 문의하세요.")
			}
		});
	}

	function setAttendQuater(){
		var data = getAttendData;
		if(data != null){
			if(data[0] != null){
				var team_a = data[0]["team_a"];
				var team_b = data[0]["team_b"];
				var quarters = data[0]["quarters"];
				var lastidx = parseInt($("#QUARTER_NUM").val());

				var astr = "";
					astr += "<td>A팀</td>";
					astr += "<td>GOAL</td>";
					astr += "<td>ASST</td>";
					astr += "<td>조퇴</td>";
				for(var i=0; i<lastidx; i++){
					astr += "<td>"+(i+1)+"쿼</td>";
				}
				$("#TEAM_A_THEAD").html(astr);
				var bstr = "";
					bstr += "<td>B팀</td>";
					bstr += "<td>GOAL</td>";
					bstr += "<td>ASST</td>";
					bstr += "<td>조퇴</td>";
				for(var i=0; i<lastidx; i++){
					bstr += "<td>"+(i+1)+"쿼</td>";
				}
				$("#TEAM_B_THEAD").html(bstr);

				setQuarterInit();

				var team = team_a.split("$");
				for(var i=0; i<team.length; i++){
					var tmp = team[i].split("|");
					if(tmp.length < 2){
						continue;
					}
					var pid = 0;
					var goal = 0;
					var asst = 0;
					var leave = "-";
					var qs = [];
					for(var j=0; j<tmp.length; j++){
						if(tmp[j] != undefined && tmp[j] != ""){
							var sets = tmp[j].split("=");
							if(sets[0] == "pid"){
								pid = sets[1];
							}else if(sets[0] == "goal"){
								goal = sets[1];
							}else if(sets[0] == "asst"){
								asst = sets[1];
							}else if(sets[0] == "leave"){
								leave = sets[1];
							}else{
								qs.push(sets[1]);
							}
						}
					}

					var target = $("#player_" + pid);
					$("#goal_" + pid).val(goal);
					$("#asst_" + pid).val(asst);
					$("#leave_" + pid).text(leave);

					if(qs != undefined && qs.length > 1){
						for(var j=0; j<lastidx; j++){
							$("#q_" + (j+1) + "_" + pid).text(qs[j]);
						}
					}
				}

				team = team_b.split("$");
				for(var i=0; i<team.length; i++){
					var tmp = team[i].split("|");
					if(tmp.length < 2){
						continue;
					}
					var pid = 0;
					var goal = 0;
					var asst = 0;
					var leave = "-";
					var qs = [];
					for(var j=0; j<tmp.length; j++){
						if(tmp[j] != undefined && tmp[j] != ""){
							var sets = tmp[j].split("=");
							if(sets[0] == "pid"){
								pid = sets[1];
							}else if(sets[0] == "goal"){
								goal = sets[1];
							}else if(sets[0] == "asst"){
								asst = sets[1];
							}else if(sets[0] == "leave"){
								leave = sets[1];
							}else{
								qs.push(sets[1]);
							}
						}
					}

					var target = $("#player_" + pid);
					$("#goal_" + pid).val(goal);
					$("#asst_" + pid).val(asst);
					$("#leave_" + pid).text(leave);

					if(qs != undefined && qs.length > 1){
						for(var j=0; j<lastidx; j++){
							$("#q_" + (j+1) + "_" + pid).text(qs[j]);
						}
					}
				}
				
			}
		}
	}
	
	

	var formationPlayers = [];
	var formationPlayerGK = "";
	var viewIdx = 0;
	var viewType = "";
	function simulFormation(idx, type){
		viewIdx = idx;
		viewType = type;
		var selectedId = "q_" + idx + "_";
		var dif = "";
		if(type == "a"){
			dif = "TEAM_A_TBODY";
		}else if(type == "b"){
			dif = "TEAM_B_TBODY";
		}
		formationPlayerGK = "";
		formationPlayers = [];
		$(".q_" + idx).each(function(){
			if(dif == $(this).parent().parent().attr("id")){
				var pid = $(this).attr("id").replace(selectedId, "");
				var tmp = attendPlayers[pid];
				var pname = tmp["player_name"];
				var ppos = tmp["recommand_pos"];
				//console.log(pid + " " + pname + " " + $(this).text());
				
				if($(this).text() == "GK"){
					formationPlayerGK = pname + "$" + ppos;
				}else if($(this).text() != ""){
					formationPlayers.push(pname + "$" + ppos);
				}
			}
		});
		setSelectedPositionByQuater();
		var myModal = new bootstrap.Modal(document.getElementById("formation_modal"), {});
		myModal.show();
	}
	function setSelectedPositionByQuater(){
		$("#POSITION_ATT").html("");
		$("#POSITION_MID").html("");
		$("#POSITION_DEF").html("");
		$("#POSITION_KEEP").html("");

		var val = $("#REG_FORMATION").val();
		if(val == ""){
			return;
		}
		var def = parseInt(val.substring(0, 1));
		var mid = parseInt(val.substring(1, 2));
		var att = parseInt(val.substring(2, 3));

		if(att == 2){
			var str  = "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_st' id='player_st1'/>";
				str += "</div>";
				str += "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_st' id='player_st2'/>";
				str += "</div>";
			$("#POSITION_ATT").append(str);
		}else if(att == 3){
			var str  = "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_lw' id='player_lw'/>";
				str += "</div>";
				str += "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_st' id='player_st1'/>";
				str += "</div>";
				str += "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_rw' id='player_rw'/>";
				str += "</div>";
			$("#POSITION_ATT").append(str);
		}else if(att == 4){
			var str  = "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_lw' id='player_lw'/>";
				str += "</div>";
				str += "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_st' id='player_st1'/>";
				str += "</div>";
				str += "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_st' id='player_st2'/>";
				str += "</div>";
				str += "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_rw' id='player_rw'/>";
				str += "</div>";
			$("#POSITION_ATT").append(str);
		}

		if(mid == 2){
			var str  = "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_cm' id='player_cm1'/>";
				str += "</div>";
				str += "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_cm' id='player_cm2'/>";
				str += "</div>";
			$("#POSITION_MID").append(str);
		}else if(mid == 3){
			var str  = "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_cm' id='player_cm1'/>";
				str += "</div>";
				str += "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_cm' id='player_cm2'/>";
				str += "</div>";
				str += "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_cm' id='player_cm3'/>";
				str += "</div>";
			$("#POSITION_MID").append(str);
		}else if(mid == 4){
			var str  = "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_lm' id='player_lm'/>";
				str += "</div>";
				str += "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_cm' id='player_cm1'/>";
				str += "</div>";
				str += "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_cm' id='player_cm2'/>";
				str += "</div>";
				str += "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_rm' id='player_rm'/>";
				str += "</div>";
			$("#POSITION_MID").append(str);
		}else if(mid == 5){
			var str  = "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_lm' id='player_lm'/>";
				str += "</div>";
				str += "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_cm' id='player_cm1'/>";
				str += "</div>";
				str += "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_cm' id='player_cm2'/>";
				str += "</div>";
				str += "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_cm' id='player_cm3'/>";
				str += "</div>";
				str += "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_rm' id='player_rm'/>";
				str += "</div>";
			$("#POSITION_MID").append(str);
		}
		if(def == 3){
			var str  = "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_cb' id='player_cb1'/>";
				str += "</div>";
				str += "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_cb' id='player_cb2'/>";
				str += "</div>";
				str += "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_cb' id='player_cb3'/>";
				str += "</div>";
			$("#POSITION_DEF").append(str);
		}else if(def == 4){
			var str  = "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_lb' id='player_lb'/>";
				str += "</div>";
				str += "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_cb' id='player_cb1'/>";
				str += "</div>";
				str += "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_cb' id='player_cb2'/>";
				str += "</div>";
				str += "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_rb' id='player_rb'/>";
				str += "</div>";
			$("#POSITION_DEF").append(str);
		}else if(def == 5){
			var str  = "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_lb' id='player_lb'/>";
				str += "</div>";
				str += "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_cb' id='player_cb1'/>";
				str += "</div>";
				str += "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_cb' id='player_cb2'/>";
				str += "</div>";
				str += "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_cb' id='player_cb3'/>";
				str += "</div>";
				str += "<div class='col'>";
				str += "<input type='text' readonly class='bestPlayer' name='player_rb' id='player_rb'/>";
				str += "</div>";
			$("#POSITION_DEF").append(str);
		}
		var str  = "<div class='col'>";
			str += "<input type='text' readonly class='bestPlayer' name='player_gk' id='player_gk'></select>";
			str += "</div>";
		$("#POSITION_KEEP").append(str);
		$(".bestPlayer").addClass("form-control bg-white");
		$(".bestPlayer").addClass("form-control-lg");
		$(".bestPlayer").addClass("auto_font_size");
		$(".bestPlayer").addClass("text-center");
		
		if(viewIdx != 0){
			var falsePlayers = [];
			$("#formation_modal_body").html("");
			$("#formation_modal_body").append("<div class='row'><div class='col'>GK="+formationPlayerGK.split("$")[0]+"</div></div>");
			$("#formation_modal_body").append("<div class='row'><div class='col'>&nbsp;</div></div>");
			
			$("#player_gk").val(formationPlayerGK.split("$")[0]);
			for(var i=0; i<formationPlayers.length; i++){
				var tmp = formationPlayers[i].split("$");
				$("#formation_modal_body").append("<div class='row'><div class='col' id='modal_player_"+i+"'>"+tmp[0]+"="+tmp[1]+"</div></div>");
				var pos = tmp[1].split(",");
				var chk = true;
				for(var p=0;p<pos.length; p++){
					$("input[name=player_" + pos[p].toLowerCase() + "]").each(function(){
						if($(this).val() == "" && chk){
							$(this).val(tmp[0]);
							chk = false;
						}
					});
					if(!chk){
						$("#modal_player_" + i).addClass("text-primary");
						break;
					}
				}
				if(chk){
					falsePlayers.push(tmp);
				}
			}

			var falseIdx = 0;
			$(".bestPlayer").each(function(){
				if($(this).val() == ""){
					if(falseIdx < falsePlayers.length){
						var tmp = falsePlayers[falseIdx];
						$(this).val(tmp[0]);
						$(this).addClass("text-danger");
						falseIdx++;
					}
				}
			});
		}
	}
	
	function saveMatchAttendQuarter(){
		if(!confirm("경기를 기록하시겠습니까?")){
			return;
		}
		var team_a = "$";
		var team_b = "$";
		var goal_a = "$";
		var goal_b = "$";
		var asst_a = "$";
		var asst_b = "$";

		var quarters = $("#QUARTER_NUM").val();
		var def_a = "";
		var def_b = "";

		var goal_int_a = 0;
		var goal_int_b = 0;
		$("#TEAM_A_TBODY").children().each(function(){
			var pid = $(this).attr("id").replace("player_", "");
			var goal = parseInt($("#goal_" + pid).val());
			var asst = parseInt($("#asst_" + pid).val());
			team_a += pid + "$";
			//name = $(this).children().eq(0).text()
			if(goal > 0){
				goal_a += pid + "|" + goal + "$";
			}
			if(asst > 0){
				asst_a += pid + "|" + asst + "$";
			}

			goal_int_a += goal;
		});
		$("#TEAM_B_TBODY").children().each(function(){
			var pid = $(this).attr("id").replace("player_", "");
			var goal = parseInt($("#goal_" + pid).val());
			var asst = parseInt($("#asst_" + pid).val());
			team_b += pid + "$";
			//name = $(this).children().eq(0).text()
			if(goal > 0){
				goal_b += pid + "|" + goal + "$";
			}
			if(asst > 0){
				asst_b += pid + "|" + asst + "$";
			}

			goal_int_b += goal;
		});

		var todayStr = year + ".";
		if(month < 10){
			todayStr += "0" + month + ".";
		}
		if(date < 10){
			todayStr += "0" + date;
		}

		var win_ab = "-";
		if(goal_int_a > goal_int_b){
			win_ab = "a";
		}else if(goal_int_a < goal_int_b){
			win_ab = "b";
		}

		$.ajax({
			async: false,
			type : "post",
			url : '/football_tpm/save.php',
			data : {
				cmd : "match",
				id : 1,
				match_date : todayStr,
				teamA : team_a,
				teamB : team_b,
				goalA : goal_a,
				goalB : goal_b,
				asstA : asst_a,
				asstB : asst_b,
				winAB : win_ab
			},
			success : function(data) {
				if(data == "ok"){
					$.ajax({
						async : false,
						type : "post",
						url : '/football_tpm/get.php',
						data : {
							cmd : "max_match_id",
							id : 1
						},
						dataType: "json",
						success : function(data, textStatus, jqXHR) {
							var mid = data[0]["match_id"];
							$.ajax({
								async: false,
								type : "post",
								url : '/football_tpm/save.php',
								data : {
									cmd : "match_d",
									id : 1,
									match_id : mid,
									cnt : quarters
								},
								success : function(data, textStatus, jqXHR) {
									if(data != "ok"){
										alert("수비 기록 실패.");
									}
								}
							});
							$("#DEF_A").children().each(function(){
								if($(this).text() != ""){
									if($(this).attr("id") != undefined){
										var quarter = $(this).attr("id").replace("def_a_", "");
										saveMatchScoreless(mid, 'a', quarter);
									}
								}
							});
							$("#DEF_B").children().each(function(){
								if($(this).text() != ""){
									if($(this).attr("id") != undefined){
										var quarter = $(this).attr("id").replace("def_b_", "");
										saveMatchScoreless(mid, 'b', quarter);
									}
								}
							});
						}
					});

				}
			},
			error : function(err) {
				alert("오류발생 관리자에게 문의하세요.")
			}
		});
	}

	function saveMatchScoreless(mid, team, quarter){
		$.ajax({
			async : false,
			type : "post",
			url : '/football_tpm/save.php',
			data : {
				cmd : "match_scoreless",
				id : 1,
				match_id : mid,
				q : quarter,
				team : team
			},
			dataType: "json",
			success : function(data, textStatus, jqXHR) {
				if(data != "ok"){
					alert("수비 기록 실패.");
				}
			}
		});
	}