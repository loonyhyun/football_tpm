var urlParams = location.search.split(/[?&]/).slice(1).map(function(paramPair) {
    return paramPair.split(/=(.+)?/).slice(0, 2);
}).reduce(function(obj, pairArray) {
    obj[pairArray[0]] = pairArray[1];
    return obj;
}, {});

const today = new Date();
const year = today.getFullYear(); // 년도
const month = today.getMonth() + 1;  // 월
const date = today.getDate();  // 날짜
const day = today.getDay();  // 요일
const TODAY_YMD = year + "" + month + "" + date;
const DEFAULT_START_MATCH_DATE = year + ".01.01";

let TEAM_ID = 1;
let TEAM_NAME = '';
let PLAY_CNT = 0;
let SEARCH_YEAR = year;
let PLAYER_LIST = [];
let SELECTED_PLAYER = null;
let SELECTED_OBJECT = null;
let SELECTED_PLAYER_INFO = null;

function setLayout(){

}

function getTeam() {
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
            TEAM_NAME = data[0]["team_name"];
            PLAY_CNT = data[0]["total_cnt"];
        },
        error : function(err) {
            alert("오류발생 관리자에게 문의하세요.")
        }
    });
}
function getPlayerList() {
    $.ajax({
        type : "post",
        async: false,
        url : '/football_tpm/get.php',
        data : {
            cmd : "player",
            id : TEAM_ID
        },
        dataType : "json",
        success : function(data) {
            PLAYER_LIST = data;
        },
        error : function(err) {
            alert("오류발생 관리자에게 문의하세요.")
        }
    });
}
function getPlayer(pid, st, ed) {
    var ret = [0, 1, 2];
    $.ajax({
        type : "post",
        async: false,
        url : '/football_tpm/get.php',
        data : {
            cmd : "player_one",
            id : TEAM_ID,
            playerId : pid
        },
        dataType : "json",
        success : function(data) {
            ret[0] = data[0];
        },
        error : function(err) {
            alert("오류발생 관리자에게 문의하세요.")
        }
    });
    $.ajax({
        type : "post",
        async: false,
        url : '/football_tpm/get.php',
        data : {
            cmd : "player_sum",
            id : TEAM_ID,
            playerId : pid,
            st : st,
            ed : ed
        },
        dataType : "json",
        success : function(data) {
            ret[1] = data[0];
        },
        error : function(err) {
            alert("오류발생 관리자에게 문의하세요.")
        }
    });
    $.ajax({
        type : "post",
        async: false,
        url : '/football_tpm/get_defence.php',
        data : {
            cmd : "player_one_scoreless",
            id : TEAM_ID,
            playerId : pid,
            st : st,
            ed : ed
        },
        dataType : "json",
        success : function(data) {
            ret[2] = data[0];
        },
        error : function(err) {
            alert("오류발생 관리자에게 문의하세요.")
        }
    });
    return ret;
}
function getGrounds(){
    $.ajax({
        type : "post",
        url : '/football_tpm/get.php',
        data : {
            cmd : "ground",
            id : TEAM_ID
        },
        dataType:"json",
        success : function(data, textStatus, jqXHR) {
            if(data == null){ return; }
            for(var i=0; i<data.length; i++){
                $("#ground_id").append("<option value='"+data[i]["ground_id"]+"'>"+data[i]["name"]+"</option>");
            }
        }
    });
}

function getPercent(value, sosujum){
    var ret = value;
    var dif = 1;
    for(var i = 0; i<sosujum; i++){
        dif *= 10;
    }
    ret = parseInt(ret * dif) / dif;
    return ret;
}

function viewDateInfo(from, to){
    var ret = [0, 1];
    $.ajax({
        type : "post",
        async: false,
        url : '/football_tpm/get.php',
        data : {
            cmd : "player_sum",
            id : TEAM_ID,
            playerId : SELECTED_PLAYER.player_id,
            st : from,
            ed : to
        },
        dataType : "json",
        success : function(data) {
            ret[0] = data[0];
        },
        error : function(err) {
            alert("오류발생 관리자에게 문의하세요.")
        }
    });
    $.ajax({
        type : "post",
        async: false,
        url : '/football_tpm/get_defence.php',
        data : {
            cmd : "player_one_scoreless",
            id : TEAM_ID,
            playerId : SELECTED_PLAYER.player_id,
            st : from,
            ed : to
        },
        dataType : "json",
        success : function(data) {
            ret[1] = data[0];
        },
        error : function(err) {
            alert("오류발생 관리자에게 문의하세요.")
        }
    });
    return ret;
}