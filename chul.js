
var bpid = "";
var t_n = $("#pteam_no");
var t_a = $("#pteam_a");
var t_b = $("#pteam_b");

var t_aq1 = $("#pteam_aq1");
var t_aq2 = $("#pteam_aq2");
var t_aq3 = $("#pteam_aq3");
var t_aq4 = $("#pteam_aq4");
var t_aq5 = $("#pteam_aq5");

var t_bq1 = $("#pteam_bq1");
var t_bq2 = $("#pteam_bq2");
var t_bq3 = $("#pteam_bq3");
var t_bq4 = $("#pteam_bq4");
var t_bq5 = $("#pteam_bq5");

var plist = $("#plist");
var finbtn = $("#finbtn");

var redcolor = "#ffa2a2";
var bluecolor = "#9d9dff";

var selectedPlayers = [];
var attendPlayers = [];
var yongPlayers = [];

function getPlayerListByAttend() {
    $.ajax({
        async: false, 
        type : "post",
        url : '/football_tpm/get.php',
        data : {
            cmd : "player_chul",
            id : 1
        },
        dataType : "json",
        success : function(data) {
            var tbody = $("#plist");
            var onclickfunc = 'beforeSelectTeam';
            if(chultype == 'A'){
                onclickfunc += 'A'
            }
            for(var i=0; i<data.length; i++){
                var tmp = data[i];
                var pname = tmp["player_name"];
                var pid = tmp["player_id"];
                if(pname.indexOf("_용병") > -1){
                    yongPlayers[pid] = tmp;
                    continue;
                }
                attendPlayers[pid] = tmp;
                tbody.append("<div id='p_"+pid+"' class='plist' onclick='"+onclickfunc+"(\""+pid+"\");'>"+pname+"</div>");
            }
            //console.log(yongPlayers);
            for(var i=0; i<yongPlayers.length; i++){
                var tmp = yongPlayers[i];
                if(tmp == undefined || tmp == null){
                    continue;
                }
                var pname = tmp["player_name"].replace("_", "");
                var pid = tmp["player_id"];
                attendPlayers[pid] = tmp;
                tbody.append("<div id='p_"+pid+"' class='plist' onclick='"+onclickfunc+"(\""+pid+"\");'>"+pname+"</div>");
            }
        },
        error : function(err) {
            alert("오류발생 관리자에게 문의하세요.")
        }
    });
}

function setAttendInit(){
    if(confirm("초기화 시 등록된 인원이 없어집니다. 되돌릴 수 없습니다.\n초기화 하시겠습니까?")){
        $.ajax({
        type : "post",
        url : '/football_tpm/save.php',
        data : {
            cmd : "chul_init",
            id : 1,
            checker : "baron",
            md : getmd()
        },
        success : function(data) {
            if(data == "ok"){
                alert("초기화 완료");
                location.reload();
            }else{
                alert("초기화 실패");
            }
        },
        error : function(err) {
            alert("오류발생 관리자에게 문의하세요.")
        }
    });
    }
}

function hideSelectTeam(){
    finbtn.hide();
    plist.hide();
}

function showSelectTeam(){
    plist.show();
}

function beforeSelectTeam(pid){
    bpid = pid;
    $("#fintxt").text($("#p_" + pid).text());
    var cnt_a = t_a.children().length;
    var cnt_b = t_b.children().length;
    console.log(cnt_a + "/" + cnt_b)
    var color = '';
    if(cnt_a <= cnt_b){
        color = redcolor;
    }else{
        color = bluecolor;
    }
        finbtn.css("background-color", color);
    finbtn.show();
    $(document).scrollTop(0);
}

function afterSelectTeam(){
    $("#infbtn").hide();
    var cnt_a = t_a.children().length;
    var cnt_b = t_b.children().length;
    var addclass = "";
    var pname = $("#p_"+bpid).text();
    if(cnt_a <= cnt_b){
        if(cnt_a%2 == 1){
            addclass = "bgc";
        }
        t_n.append("<div class='t_row "+addclass+"'>"+(cnt_a+1)+"</div>")
        var str = ("<div class='t_row "+addclass+"' id='ta_"+bpid+"'>"+pname+"")
        if(pname.indexOf("용병") == 0){
            str += (" <input type='text' id='txt_"+bpid+"' value='' class='yongs' onchange='saveAttendInfo()'>")
        }
        str += ("</div>")
        t_a.append(str);
    }else{
        if(cnt_b%2 == 1){
            addclass = "bgc";
        }
        var str = ("<div class='t_row "+addclass+"' id='tb_"+bpid+"'>"+pname+"")
        if(pname.indexOf("용병") == 0){
            str += (" <input type='text' id='txt_"+bpid+"' value='' class='yongs' onchange='saveAttendInfo()'>")
        }
        str += ("</div>")
        t_b.append(str);
    }
    hideSelectPlayer(bpid);
    bpid = "";
    hideSelectTeam();

    saveAttendInfo();
}

function beforeSelectTeamA(pid){
    bpid = pid;
    $("#fintxt").text($("#p_" + pid).text());
    finbtn.css("background-color", redcolor);
    finbtn.show();
    $(document).scrollTop(0);
}
function afterSelectTeamA(){
    $("#infbtn").hide();
    var cnt_a = t_a.children().length;
    var cnt_b = t_b.children().length;
    var addclass = "";
    var pname = $("#p_"+bpid).text();

    if(cnt_a%2 == 1){
        addclass = "bgc";
    }
    t_n.append("<div class='t_row "+addclass+"'>"+(cnt_a+1)+"</div>")
    var str = ("<div class='t_row "+addclass+"' id='ta_"+bpid+"'>"+pname+"")
    if(pname.indexOf("용병") == 0){
        str += (" <input type='text' id='txt_"+bpid+"' value='' class='yongs' onchange='saveAttendInfo()'>")
    }
    str += ("</div>")
    t_a.append(str);

    hideSelectPlayer(bpid);
    bpid = "";
    hideSelectTeam();

    saveAttendInfo();
}

function hideSelectPlayer(pid){
    $("#p_" + pid).hide();
}

function getmd(){
    if(month < 10){
        month = "0" + month;
    }
    if(date < 10){
        date = "0" + date;
    }
    return year + "." + month + "." + date;
}
function saveAttendInfo(){
    var team_a = "$";
    var team_b = "$";
    var yong_a = "$";
    var yong_b = "$";

    t_a.children().each(function(){
        var tid = $(this).attr("id").replace("ta_", "");
        team_a += tid.replace("ta_", "") + "$";
        if(attendPlayers[tid]["player_name"].indexOf("_용병") > -1){
            yong_a += $("#txt_"+tid).val();
        }
        yong_a += "$";
    })
    t_b.children().each(function(){
        var tid = $(this).attr("id").replace("tb_", "");
        team_b += tid + "$";
        if(attendPlayers[tid]["player_name"].indexOf("_용병") > -1){
            yong_b += $("#txt_"+tid).val();
        }
        yong_b += "$";
    })

    //console.log(team_a);
    //console.log(team_b);

    $.ajax({
        async: false,
        type : "post",
        url : '/football_tpm/save.php',
        data : {
            cmd : "chul",
            id : 1,
            md: getmd(),
            team_a : team_a,
            team_b : team_b,
            yong_a : yong_a,
            yong_b : yong_b
        },
        success : function(data) {
            if(data == "ok"){
                finalert("저장 성공");
            }else{
                finalert("저장 실패");
            }
        },
        error : function(err) {
            alert("오류발생 관리자에게 문의하세요.")
        }
    });
}

var falt = $("#finalert")
function finalert(txt){
    var d = new Date();
    falt.text(txt + " " + d.getHours() + ":" + d.getMinutes() + ":" + d.getSeconds());
    falt.show();
    setTimeout(function(){
        falt.hide();
    }, 2000)
}



function getInitAttend(){
    $.ajax({
        type : "post",
        url : '/football_tpm/get.php',
        data : {
            cmd : "chul",
            id : 1,
            md : getmd()
        },
        dataType : "json",
        success : function(data) {
            console.log(data);
            for(var i=0; i<data.length; i++){
                var ta = data[i]["team_a"];
                var tb = data[i]["team_b"];
                var ya = data[i]["yong_a"];
                var yb = data[i]["yong_b"];
                var aa = ta.split("$");
                var ab = tb.split("$");
                var aya = ya.split("$");
                var ayb = yb.split("$");
                var aq1 = data[i]["aq1"];
                var aq2 = data[i]["aq2"];
                var aq3 = data[i]["aq3"];
                var aq4 = data[i]["aq4"];
                var aq5 = data[i]["aq5"];
                var bq1 = data[i]["aq1"];
                var bq2 = data[i]["aq2"];
                var bq3 = data[i]["aq3"];
                var bq4 = data[i]["aq4"];
                var bq5 = data[i]["aq5"];
                aq1 = aq1 != null ? aq1.split("$") : "";
                aq2 = aq2 != null ? aq2.split("$") : "";
                aq3 = aq3 != null ? aq3.split("$") : "";
                aq4 = aq4 != null ? aq4.split("$") : "";
                aq5 = aq5 != null ? aq5.split("$") : "";
                bq1 = bq1 != null ? bq1.split("$") : "";
                bq2 = bq2 != null ? bq2.split("$") : "";
                bq3 = bq3 != null ? bq3.split("$") : "";
                bq4 = bq4 != null ? bq4.split("$") : "";
                bq5 = bq5 != null ? bq5.split("$") : "";

                for(var ai = 0; ai < aa.length; ai++){
                    var pid = aa[ai];
                    if(pid == ""){
                        continue;
                    }
                    $("#p_" + pid).hide();
                    
                    var cnt_a = t_a.children().length;
                    var addclass = "";
                    var pname = $("#p_"+pid).text();
                    if(cnt_a%2 == 1){
                        addclass = "bgc";
                    }
                    t_n.append("<div class='t_row "+addclass+"'>"+(cnt_a+1)+"</div>")
                    var str = ("<div class='t_row "+addclass+"' id='ta_"+pid+"'>"+pname+"")
                    if(pname.indexOf("용병") == 0){
                        str += (" <input type='text' id='txt_"+pid+"' value='"+aya[ai]+"' class='yongs' onchange='saveAttendInfo()'>")
                    }
                    str += "<div></div>";
                    t_a.append(str);
                }
                for(var ai = 0; ai < ab.length; ai++){
                    var pid = ab[ai];
                    if(pid == ""){
                        continue;
                    }
                    $("#p_" + pid).hide();
                    
                    var cnt_b = t_b.children().length;
                    var addclass = "";
                    var pname = $("#p_"+pid).text();
                    if(cnt_b%2 == 1){
                        addclass = "bgc";
                    }
                    var str = ("<div class='t_row "+addclass+"' id='tb_"+pid+"'>"+pname+"")
                    if(pname.indexOf("용병") == 0){
                        str += (" <input type='text' id='txt_"+pid+"' value='"+ayb[ai]+"' class='yongs' onchange='saveAttendInfo()'>")
                    }
                    str += ("</div>")
                    t_b.append(str);
                }

                if(chultype == 'AQ'){
                    for(var ai = 0; ai < aa.length; ai++){
                        var pid = aa[ai];
                        if(pid == ""){ continue; }
                        addclass = "";
                        if(ai%2 == 0){ addclass= "bgc"; }
                        t_aq1.append("<div class='t_row "+addclass+"' id='aq1_"+pid+"' onclick='changeChulQ(\"aq1_"+pid+"\");'>-</div>");
                        t_aq2.append("<div class='t_row "+addclass+"' id='aq2_"+pid+"' onclick='changeChulQ(\"aq2_"+pid+"\");'>-</div>");
                        t_aq3.append("<div class='t_row "+addclass+"' id='aq3_"+pid+"' onclick='changeChulQ(\"aq3_"+pid+"\");'>-</div>");
                        t_aq4.append("<div class='t_row "+addclass+"' id='aq4_"+pid+"' onclick='changeChulQ(\"aq4_"+pid+"\");'>-</div>");
                        t_aq5.append("<div class='t_row "+addclass+"' id='aq5_"+pid+"' onclick='changeChulQ(\"aq5_"+pid+"\");'>-</div>");
                    }
                    if(data[i]["aq1"] != null){
                        for(var ai = 0; ai < aq1.length; ai++){
                            var tq1 = aq1[ai]; if(tq1 == ""){ continue; }
                            var tq2 = aq2[ai]; 
                            var tq3 = aq3[ai]; 
                            var tq4 = aq4[ai]; 
                            var tq5 = aq5[ai]; 

                            var pid = aa[ai];
                            
                            var cnt_b = t_a.children().length;
                            addclass = "";
                            if(ai%2 == 0){ addclass = "bgc"; }
                            $("#aq1_"+pid).text(tq1);
                            $("#aq2_"+pid).text(tq2);
                            $("#aq3_"+pid).text(tq3);
                            $("#aq4_"+pid).text(tq4);
                            $("#aq5_"+pid).text(tq5);
                        }
                    }
                }
                
                if(chultype == 'Q'){
                    for(var ai = 0; ai < ab.length; ai++){
                        var pid = ab[ai]; if(pid == ""){ continue; }
                        var addclass = ""; if(ai%2 == 0){ addclass = "bgc"; }
                        $("#pteam_bno").append("<div class='t_row "+addclass+"'>"+(ai)+"</div>")
                    }
                    for(var ai = 0; ai < aa.length; ai++){
                        var pid = aa[ai];
                        if(pid == ""){ continue; }
                        addclass = "";
                        if(ai%2 == 0){ addclass= "bgc"; }
                        t_aq1.append("<div class='t_row "+addclass+"' id='aq1_"+pid+"' onclick='changeChulQ(\"aq1_"+pid+"\");'>-</div>");
                        t_aq2.append("<div class='t_row "+addclass+"' id='aq2_"+pid+"' onclick='changeChulQ(\"aq2_"+pid+"\");'>-</div>");
                        t_aq3.append("<div class='t_row "+addclass+"' id='aq3_"+pid+"' onclick='changeChulQ(\"aq3_"+pid+"\");'>-</div>");
                        t_aq4.append("<div class='t_row "+addclass+"' id='aq4_"+pid+"' onclick='changeChulQ(\"aq4_"+pid+"\");'>-</div>");
                        t_aq5.append("<div class='t_row "+addclass+"' id='aq5_"+pid+"' onclick='changeChulQ(\"aq5_"+pid+"\");'>-</div>");
                    }
                    for(var ai = 0; ai < ab.length; ai++){
                        var pid = ab[ai];
                        if(pid == ""){ continue; }
                        addclass = "";
                        if(ai%2 == 0){ addclass= "bgc"; }
                        t_bq1.append("<div class='t_row "+addclass+"' id='bq1_"+pid+"' onclick='changeChulQ(\"bq1_"+pid+"\");'>-</div>");
                        t_bq2.append("<div class='t_row "+addclass+"' id='bq2_"+pid+"' onclick='changeChulQ(\"bq2_"+pid+"\");'>-</div>");
                        t_bq3.append("<div class='t_row "+addclass+"' id='bq3_"+pid+"' onclick='changeChulQ(\"bq3_"+pid+"\");'>-</div>");
                        t_bq4.append("<div class='t_row "+addclass+"' id='bq4_"+pid+"' onclick='changeChulQ(\"bq4_"+pid+"\");'>-</div>");
                        t_bq5.append("<div class='t_row "+addclass+"' id='bq5_"+pid+"' onclick='changeChulQ(\"bq5_"+pid+"\");'>-</div>");
                    }
                    
                    if(data[i]["aq1"] != null){
                        for(var ai = 0; ai < aq1.length; ai++){
                            var tq1 = aq1[ai]; if(tq1 == ""){ continue; }
                            var tq2 = aq2[ai]; 
                            var tq3 = aq3[ai]; 
                            var tq4 = aq4[ai]; 
                            var tq5 = aq5[ai]; 

                            var pid = aa[ai];
                            addclass = "";
                            if(ai%2 == 0){ addclass = "bgc"; }
                            $("#aq1_"+pid).text(tq1);
                            $("#aq2_"+pid).text(tq2);
                            $("#aq3_"+pid).text(tq3);
                            $("#aq4_"+pid).text(tq4);
                            $("#aq5_"+pid).text(tq5);
                        }
                    }
                    if(data[i]["bq1"] != null){
                        for(var ai = 0; ai < bq1.length; ai++){
                            var tq1 = bq1[ai]; if(tq1 == ""){ continue; }
                            var tq2 = bq2[ai]; 
                            var tq3 = bq3[ai]; 
                            var tq4 = bq4[ai]; 
                            var tq5 = bq5[ai]; 

                            var pid = ab[ai];
                            addclass = "";
                            if(ai%2 == 0){ addclass = "bgc"; }
                            $("#bq1_"+pid).text(tq1);
                            $("#bq2_"+pid).text(tq2);
                            $("#bq3_"+pid).text(tq3);
                            $("#bq4_"+pid).text(tq4);
                            $("#bq5_"+pid).text(tq5);
                        }
                    }
                }
            }
        },
        error : function(err) {
            alert("오류발생 관리자에게 문의하세요.")
        }
    });
}

function delChul(){
    var cnt_a = t_a.children().length;
    var cnt_b = t_b.children().length;
    if(cnt_a == cnt_b){
        //b remove
        t_b.children().last().remove();
    }
    else{
        //a remove
        t_a.children().last().remove();
        t_n.children().last().remove();
    }
    saveAttendInfo();
}

function saveChulQ(){
    var team_aq1 = "$";
    var team_aq2 = "$";
    var team_aq3 = "$";
    var team_aq4 = "$";
    var team_aq5 = "$";
    var team_bq1 = "$";
    var team_bq2 = "$";
    var team_bq3 = "$";
    var team_bq4 = "$";
    var team_bq5 = "$";

    t_aq1.children().each(function(){ var tid = $(this).text(); team_aq1 += tid + "$"; })
    t_aq2.children().each(function(){ var tid = $(this).text(); team_aq2 += tid + "$"; })
    t_aq3.children().each(function(){ var tid = $(this).text(); team_aq3 += tid + "$"; })
    t_aq4.children().each(function(){ var tid = $(this).text(); team_aq4 += tid + "$"; })
    t_aq5.children().each(function(){ var tid = $(this).text(); team_aq5 += tid + "$"; })
    t_bq1.children().each(function(){ var tid = $(this).text(); team_bq1 += tid + "$"; })
    t_bq2.children().each(function(){ var tid = $(this).text(); team_bq2 += tid + "$"; })
    t_bq3.children().each(function(){ var tid = $(this).text(); team_bq3 += tid + "$"; })
    t_bq4.children().each(function(){ var tid = $(this).text(); team_bq4 += tid + "$"; })
    t_bq5.children().each(function(){ var tid = $(this).text(); team_bq5 += tid + "$"; })

    //console.log(team_a);
    //console.log(team_b);

    $.ajax({
        async: false,
        type : "post",
        url : '/football_tpm/save.php',
        data : {
            cmd : "chulq",
            id : 1,
            md: getmd(),
            aq1 : team_aq1,
            aq2 : team_aq2,
            aq3 : team_aq3,
            aq4 : team_aq4,
            aq5 : team_aq5,
            bq1 : team_bq1,
            bq2 : team_bq2,
            bq3 : team_bq3,
            bq4 : team_bq4,
            bq5 : team_bq5
        },
        success : function(data) {
            if(data == "ok"){
                finalert("저장 성공");
            }else{
                finalert("저장 실패");
            }
        },
        error : function(err) {
            alert("오류발생 관리자에게 문의하세요.")
        }
    });
}

function changeChulQ(pid){
    var txt = $("#" + pid).text();
    if(txt == '-'){
        txt = "O";
    }else if(txt == 'O'){
        txt = "GK";
    }
    $("#" + pid).text(txt);
}