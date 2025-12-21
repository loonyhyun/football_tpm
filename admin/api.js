/**
 * 출석 자석판 API 정보 관련 START
 * */

function getApiGames(){
    let games = null;
    $.ajax({
        type : "post",
        async: true,
        url : '/football_tpm/chul/api_games.php',
        dataType : "json",
        success : function(data) {
            console.log(data);
            if(data.games){
                games = data.games;
                for(const game of data.games){
                    setApiGames(game.id, JSON.stringify(game));
                    setApiGamesView(game);
                    setTimeout(function(){
                        getApiAttendances(game.id);
                    }, 500);
                    //alert("sync fin.");
                    return;
                }
            }
        },
        error : function(err) {
            alert("오류발생 관리자에게 문의하세요.")
        }
    });
}

function setApiGames(game_id, game_value){
    $.ajax({
        type : "post",
        async: true,
        url : '/football_tpm/admin/save_api.php',
        data: {
            id: '1'
            , cmd: 'games'
            , game_id: game_id
            , game_value: game_value
        },
        dataType : "text",
        success : function(data) {
            console.log("games sync: " + data);
        },
        error : function(err) {
            alert("오류발생 관리자에게 문의하세요.")
        }
    });
}

function getApiAttendances(game_id){
    $.ajax({
        type : "post",
        async: true,
        url : '/football_tpm/chul/api_attend.php',
        data: {
            game_id: game_id
        },
        dataType : "json",
        success : function(data) {
            if(data.attendances){
                setApiAttendances(game_id, JSON.stringify(data));
                setApiAttendancesView(data);
                setTimeout(function(){
                    getApiQuarterEvents(game_id);
                }, 500);
            }
        },
        error : function(err) {
            alert("오류발생 관리자에게 문의하세요.")
        }
    });
}

function setApiAttendances(game_id, attend_value){
    $.ajax({
        type : "post",
        async: true,
        url : '/football_tpm/admin/save_api.php',
        data: {
            id: '1'
            , cmd: 'attends'
            , game_id: game_id
            , attend_value: attend_value
        },
        dataType : "text",
        success : function(data) {
            console.log("attend sync: " + data);
        },
        error : function(err) {
            alert("오류발생 관리자에게 문의하세요.")
        }
    });
}

function getApiQuarterEvents(game_id){
    $.ajax({
        type : "post",
        async: true,
        url : '/football_tpm/chul/api_quarter_events.php',
        data: {
            game_id: game_id
        },
        dataType : "json",
        success : function(data) {
            if(data){
                setApiQuarterEvents(game_id, JSON.stringify(data));
                setApiQuarterEventsView(data);
            }
        },
        error : function(err) {
            alert("오류발생 관리자에게 문의하세요.")
        }
    });
}

function setApiQuarterEvents(game_id, value){
    $.ajax({
        type : "post",
        async: true,
        url : '/football_tpm/admin/save_api.php',
        data: {
            id: '1'
            , cmd: 'quarter_events'
            , game_id: game_id
            , quarter_events_value: value
        },
        dataType : "text",
        success : function(data) {
            console.log("attend sync: " + data);
        },
        error : function(err) {
            alert("오류발생 관리자에게 문의하세요.")
        }
    });
}


//화면에 데이터 세팅
function setApiGamesView(data){
    console.log(data);
    
    const matchDate = data.game_date.replaceAll('-', '.');
    $('#input_match_date').val(matchDate);

    const ground = data.venue;
    const groundArr = $('#ground_id').find('option').filter((idx, opt) => {
        return $(opt).text().includes(ground);
    });
    if(groundArr){
        $(groundArr[0]).prop('selected', true);
    }

    const matchType = data.match_type;
    if(matchType == 'external'){
        $('#match_yn').val('Y');
    }else{
        $('#match_yn').val('N');
    }

}

let merList = [];

function setApiAttendancesView(data){
    merList = [];
    const team_choice = $('#team_choice');
    const players = team_choice.find('p');
    const assignments = data.assignments;
    const attendances = data.attendances;
    let merCnt = 1;
    for(const attend of attendances){
        const users = attend.users;
        const name = users.name;
        const player = players.filter((idx, p) => {
            return $(p).text().includes(name);
        });
        if(player && player.length > 0) {
            if(attend.team == 'red'){
                $(player[0]).find('button')[0].click();
            }
            else{
                $(player[0]).find('button')[1].click();
            }
        }else{
            const merStr = merCnt + '';
            const merName = '_용병' + merStr.padStart(2, '0');
            const merPlayer = players.filter((idx, p) => {
                return $(p).text().includes(merName);
            });
            if(merPlayer && merPlayer.length > 0){
                merList.push({name: name, merName: merName});
                if(attend.team == 'red'){
                    $(merPlayer[0]).find('button')[0].click();
                }else{
                    $(merPlayer[0]).find('button')[1].click();
                }
            }
            merCnt++;
        }
    }
}
function setApiQuarterEventsView(data){
    for(const goal of data.goals){
        console.log(goal);
        if(goal.user == null){
            continue;
        }
        const name = goal.user.name;
        const asst = goal.assist;
        const asstName = asst ? asst.name : null;
        const team = goal.team;
        const className = 'team' + (team == 'red' ? 'A' : 'B') + 'name';
        const goalClassName = 'team' + (team == 'red' ? 'A' : 'B') + 'goal';
        const gt = $('.' + className).filter((idx, n) => {
            return $(n).text() == name;
        })
        if(gt && gt.length > 0){
            const pid = $(gt).attr('id').replaceAll(className + '_', '');
            plusValueMatch(goalClassName + '_' + pid);
        }else{
            const mer = merList.filter((idx, m) => {
                return m.name == name;
            })
            const mt = $('.' + className).filter((idx, n) => {
                return $(n).text() == name;
            })
            if(mt && mt.length > 0){
                const pid = $(mt).attr('id').replaceAll(className + '_', '');
                plusValueMatch(goalClassName + '_' + pid);
            }
        }
        if(asstName != null){
            const className = 'team' + (team == 'red' ? 'A' : 'B') + 'name';
            const asstClassName = 'team' + (team == 'red' ? 'A' : 'B') + 'asst';
            const t = $('.' + className).filter((idx, n) => {
                return $(n).text() == asstName;
            })
            if(t && t.length > 0){
                const pid = $(t).attr('id').replaceAll(className + '_', '');
                plusValueMatch(asstClassName + '_' + pid);
            }else{
                const mer = merList.filter((idx, m) => {
                    return m.name == asstName;
                })
                const mt = $('.' + className).filter((idx, n) => {
                    return $(n).text() == asstName;
                })
                if(mt && mt.length > 0){
                    const pid = $(mt).attr('id').replaceAll(className + '_', '');
                    plusValueMatch(asstClassName + '_' + pid);
                }
            }
        }
    }

    for(const own of data.ownGoals){
        const team = own.team;
        funcSet('own_goal_' + (team == 'red' ? 'b' : 'a'), '+');
    }
}