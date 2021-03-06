
function getMatchHtmlList(){
    $.ajax({
        type : "post",
        url : '/football_tpm/get.php',
        data : {
            cmd : "matchlist",
            id : 1
        },
        dataType:"json",
        success : function(data, textStatus, jqXHR) {
            var target = $("#MatchlistTbody");
            target.html("");
            
            for(var i=0; i<data.length; i++){
                var tmp = data[i];
                var match_id = tmp["match_id"];
                var match_date = tmp["match_date"];
                var win_ab = tmp["win_ab"];
                if(win_ab == 'a'){
                    win_ab = "A팀 (RED)";
                }else if(win_ab == 'b'){
                    win_ab = "B팀 (BLUE)";
                }else if(win_ab == "-"){
                    win_ab = "DRAW"
                }
                var str = "<tr id='matchlist_"+match_id+"' onclick='getMatchHtml("+match_id+", \""+match_date+"\", \""+win_ab+"\");'>";
                    str += "<td>"+(i+1)+"</td>";
                    str += "<td>"+match_date+"</td>";
                    str += "<td>"+win_ab+"</td>";
                    str += "</tr>";
                target.append(str);

                if(i==0){
                    getMatchHtml(match_id, match_date, win_ab);
                }
            }
        }
    });
}

var selectedIdByMatchHtml = 0;
function getMatchHtml(mid, mdate, winab){
    $("#matchlist_" + selectedIdByMatchHtml).css("background-color", "");
    $("#matchlist_" + mid).css("background-color", "#ffd756");
    selectedIdByMatchHtml = mid;
    $.ajax({
        type : "post",
        url : '/football_tpm/get.php',
        data : {
            cmd : "match",
            id : 1,
            match_id : mid
        },
        dataType: "json",
        success : function(data, textStatus, jqXHR) {
            if(winab == '-'){
                winab = "무승부";
            }
            var g_name = "-";
            if(data.length > 0){
                if(data[0]["g_name"] != undefined && data[0]["g_name"] != null)
                    g_name = data[0]["g_name"];
            }
            $("#MatchInfoDesc").text("경기일자: " + mdate + ", " + winab + ", " + g_name);
            var targetA = $("#MatchInfoTbodyA");
            var targetB = $("#MatchInfoTbodyB");
            targetA.html("");
            targetB.html("");

            var goalA = 0;
            var goalB = 0;
            for(var i=1; i<data.length; i++){
                var tmp = data[i];
                var str = "<tr>";
                    str += "<td>"+tmp["player_name"]+"</td>";
                    str += "<td>"+tmp["goal_cnt"]+"</td>";
                    str += "<td>"+tmp["asst_cnt"]+"</td>";
                    str += "</tr>";
                if(tmp["team_a_yn"] == 1){
                    targetA.append(str);
                    goalA += parseInt(tmp["goal_cnt"]);
                }
                else if(tmp["team_b_yn"] == 1){
                    targetB.append(str);
                    goalB += parseInt(tmp["goal_cnt"]);
                }
            }

            $("#MatchInfoDesc").append(" (" + goalA + " : " + goalB + ")");

            $("#MatchInfoImgs").html("");
            //getMatchHtmlImgs(mdate);
            //var str = "<img src='"+img1+"' style='width:100%;' id='MatchInfoImg1'>";
            //$("#MatchInfoImgs").append(str);

            getMatchHtmlReply(mid);
        }
    });
}
function getMatchHtmlImgs(mdate){
    var mdateno = mdate.replace(".", "").replace(".", "");
    var exts = ["gif", "jpg", "png"];
    var src = "/football_tpm/img/" + mdateno;
    loadMatchInfoImg(src + "/1." + exts[0]);
    loadMatchInfoImg(src + "/1." + exts[1]);
    loadMatchInfoImg(src + "/1." + exts[2]);
    loadMatchInfoMp4(src + "/1.mp4");
}
function loadMatchInfoImg(src){
    var str = "<img src='"+src+"' style='width:100%;' onError='noMatchInfoImg(this);'>";
    $("#MatchInfoImgs").append(str);
}
function loadMatchInfoMp4(src){
    var str = "<video src='"+src+"' style='width:100%; max-height:360px;' type='video/mp4' controls autoplay loop onError='noMatchInfoImg(this);'>";
        str += "</video>";
    $("#MatchInfoImgs").append(str);
}
function noMatchInfoImg(obj){
    $(obj).hide();
}
function getMatchHtmlReply(mid){
    $.ajax({
        type : "post",
        url : '/football_tpm/get.php',
        data : {
            cmd : "match_reply",
            id : 1,
            match_id : mid
        },
        dataType:"json",
        success : function(data, textStatus, jqXHR) {
            var target = $("#MatchInfoReply");
            target.html("");
            
            for(var i=0; i<data.length; i++){
                var tmp = data[i];
                var reply = tmp["reply"];
                var regdate = tmp["regdate"];
                var str = "<tr>";
                    str += "<td>"+(i+1)+"</td>";
                    str += "<td>"+regdate+"</td>";
                    str += "<td colspan='2'>"+reply+"</td>";
                    str += "</tr>";
                target.append(str);
            }
        }
    });
}
function saveReplyText(){
    if($("#reply_input_text").val() == ""){
        alert("댓글 작성이 필요합니다.");
        $("#reply_input_text").focus();
        return;
    }
    $.ajax({
        type : "post",
        url : '/football_tpm/save.php',
        data : {
            cmd : "match_reply",
            id : 1,
            match_id : selectedIdByMatchHtml,
            reply : $("#reply_input_text").val()
        },
        success : function(data, textStatus, jqXHR) {
            if (data == "ok") {
                alert("댓글이 등록되었습니다.");
                $("#reply_input_text").val("");
                getMatchHtmlReply(selectedIdByMatchHtml);
            }
            else{
                alert("등록 실패");
            }
        }
    });
}
function reMatchHtmlReply(){
    getMatchHtmlReply(selectedIdByMatchHtml);
}

function MatchListVisible(){
    if($("#MatchListThead").is(":visible")){
        $("#MatchListThead").hide();
        $("#MatchlistTbody").hide();
        //$("#MatchListCaption").text("경기 리스트 - 숨김");
    }else{
        $("#MatchListThead").show();
        $("#MatchlistTbody").show();
        //$("#MatchListCaption").text("경기 리스트 - 보기");
    }
}


function match_d_save(){
    if(selectedIdByMatchHtml != 0){
        $.ajax({
            type : "post",
            url : '/football_tpm/save.php',
            data : {
                cmd : "match_d",
                id : 1,
                match_id : selectedIdByMatchHtml,
                cnt : $("#p_quarters").val()
            },
            success : function(data, textStatus, jqXHR) {
                if(data == "ok"){
                    $.ajax({
                        async : false,
                        type : "post",
                        url : '/football_tpm/save.php',
                        data : {
                            cmd : "match_scoreless_init",
                            id : 1,
                            match_id : selectedIdByMatchHtml
                        },
                        dataType: "json",
                        success : function(data, textStatus, jqXHR) { }
                    });
                    var tmpA = $("#p_team_a").val().split(";");
                    var tmpB = $("#p_team_b").val().split(";");
                    for(var i = 0; i < tmpA.length; i++){
                        var tmpQuarter = tmpA[i];
                        match_d_scoreless(tmpQuarter, 'a');
                    }
                    for(var i = 0; i < tmpB.length; i++){
                        var tmpQuarter = tmpB[i];
                        match_d_scoreless(tmpQuarter, 'b');
                    }

                    alert("수비 기록 완료.");
                }else{
                    alert("수비 기록 실패.");
                }
            }
        });
    }
}
function match_d_scoreless(tmpQuarter, team){
    if(tmpQuarter.trim() != ""){
        $.ajax({
            async : false,
            type : "post",
            url : '/football_tpm/save.php',
            data : {
                cmd : "match_scoreless",
                id : 1,
                match_id : selectedIdByMatchHtml,
                q : tmpQuarter,
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
}

$(document).ready(function(){
    $('#input_match_date').datepicker({
        format: "yyyy.mm.dd",
        autoclose: true,
        todayHighlight: true,
        language: "ko"
    });
});

function hideCreateMatch(){
    $("#CREATE_MATCH_DIV").hide();
    $("#CREATE_MATCH_BUTTON").show();
}
function showCreateMatch(){
    $("#CREATE_MATCH_DIV").show();
    $("#CREATE_MATCH_BUTTON").hide();
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
        str += " <button type='button' class='btn btn-danger btn-sm' onclick=\"setTeamByMatch('A', "
                + playerId
                + ", '"
                + playerName
                + "')\">A팀</button>";
        str += " <button type='button' class='btn btn-primary btn-sm' onclick=\"setTeamByMatch('B', "
                + playerId
                + ", '"
                + playerName
                + "')\">B팀</button>";
        str += "</p>";
        $("#team_choice").append(str);
    }
}

var array_A = [];
var array_B = [];
function setTeamByMatch(team, id, name) {
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

        var str = "";
        /*str = "<p id='teamA_"+id+"' class='mgb5'>" + name;
        str += " <input type='text' name='teamAgoal' id='teamAgoal_"
                + id
                + "' value='0' style='width:24px;'  onKeyup=\"this.value=this.value.replace(/[^0-9]/g,'');\">";
        str += " <button class='btn btn-primary' onclick=\"\">+</button>";
        str += " <button class='btn btn-danger'>-</button>";
        str += " <input type='text' name='teamAasst' id='teamAasst_"
                + id
                + "' value='0' style='width:24px;' onKeyup=\"this.value=this.value.replace(/[^0-9]/g,'');\">";
        str += " <button class='btn btn-primary' onclick=\"\">+</button>";
        str += " <button class='btn btn-danger'>-</button>";
        str += "</p>";*/

        str = "<tr id='teamA_"+id+"'>";
        str += "  <td>" + name + "</td>";
        str += "  <td>";
        str += "<input type='number' class='VALUE_INPUT' name='teamAgoal' id='teamAgoal_" + id + "' value='0' style='width:30px;'>";
        str += " <button class='PLUS_MINUS btn btn-sm btn-primary' style='padding:0 0.25rem; width:24px; height:24px;' onclick=\"plusValueMatch('teamAgoal_"+id+"');\">+</button>";
        str += " <button class='PLUS_MINUS btn btn-sm btn-danger' style='padding:0 0.25rem; width:24px; height:24px;' onclick=\"minusValueMatch('teamAgoal_"+id+"');\">-</button>";
        str += "  </td>";
        str += "  <td>";
        str += "<input type='number' class='VALUE_INPUT' name='teamAasst' id='teamAasst_" + id + "' value='0' style='width:30px;'>";
        str += " <button class='PLUS_MINUS btn btn-sm btn-primary' style='padding:0 0.25rem; width:24px; height:24px;' onclick=\"plusValueMatch('teamAasst_"+id+"');\">+</button>";
        str += " <button class='PLUS_MINUS btn btn-sm btn-danger' style='padding:0 0.25rem; width:24px; height:24px;' onclick=\"minusValueMatch('teamAasst_"+id+"');\">-</button>";
        str += "  </td>";
        str += "</tr>";
        $("#team_a_tbody").append(str);
    } else if (team == "B") {
        array_B.push(id);

        var str = "";
        /*str = "<p id='teamB_"+id+"' class='mgb5'>" + name;
        str += " <input type='text' name='teamBgoal' id='teamBgoal_"
                + id
                + "' value='0' style='width:24px;' onKeyup=\"this.value=this.value.replace(/[^0-9]/g,'');\">";
        str += " <button class='btn btn-primary'>+</button>";
        str += " <button class='btn btn-danger'>-</button>";
        str += " <input type='text' name='teamBasst' id='teamBasst_"
                + id
                + "' value='0' style='width:24px;' onKeyup=\"this.value=this.value.replace(/[^0-9]/g,'');\">";
        str += " <button class='btn btn-primary'>+</button>";
        str += " <button class='btn btn-danger'>-</button>";
        str += "</p>"*/
        
        str = "<tr id='teamB_"+id+"'>";
        str += "  <td>" + name + "</td>";
        str += "  <td>";
        str += "<input type='number' class='VALUE_INPUT' name='teamBgoal' id='teamBgoal_" + id + "' value='0' style='width:30px;'>";
        str += " <button class='PLUS_MINUS btn btn-sm btn-primary' style='padding:0 0.25rem; width:24px; height:24px;' onclick=\"plusValueMatch('teamBgoal_"+id+"');\">+</button>";
        str += " <button class='PLUS_MINUS btn btn-sm btn-danger' style='padding:0 0.25rem; width:24px; height:24px;' onclick=\"minusValueMatch('teamBgoal_"+id+"');\">-</button>";
        str += "  </td>";
        str += "  <td>";
        str += "<input type='number' class='VALUE_INPUT' name='teamBasst' id='teamBasst_" + id + "' value='0' style='width:30px;'>";
        str += " <button class='PLUS_MINUS btn btn-sm btn-primary' style='padding:0 0.25rem; width:24px; height:24px;' onclick=\"plusValueMatch('teamBasst_"+id+"');\">+</button>";
        str += " <button class='PLUS_MINUS btn btn-sm btn-danger' style='padding:0 0.25rem; width:24px; height:24px;' onclick=\"minusValueMatch('teamBasst_"+id+"');\">-</button>";
        str += "  </td>";
        str += "</tr>";
        $("#team_b_tbody").append(str);
    }
}

function plusValueMatch(id){
    $("#" + id).val(parseInt($("#" + id).val()) + 1);
}
function minusValueMatch(id){
    $("#" + id).val(parseInt($("#" + id).val()) - 1);
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
                winAB : $("#win_ab").val(),
                groundId : $("#ground_id option:selected").val()
            },
            success : function(data, textStatus, jqXHR) {
                if (data == "ok") {
                    alert("등록되었습니다.");
                    location.reload();
                }
            }
        });
    }
}