<?php
//一日の試合一覧
class Scoreboard {
    private $_year;
    private $_month;
    private $_day;
    
    private $_json;
    private $_scores;
    
    private $_message;
    
    //スコア一覧
    public function getScores() {
        return $this->_scores;
    }
    
    public function isEmpty() {
        return $this->_json == null;
    }
    
    //エラーメッセージ
    public function getMessage() {
        return $this->_message;
    }
    
    public function getTitle() {
        return sprintf("Scoreboard %04d-%02d-%02d", $this->_year, $this->_month, $this->_day);
    }
    
    public function __construct($year, $month, $day) {
        $this->_year = $year;
        $this->_month = $month;
        $this->_day = $day;
    }
    
    //スコア一覧を読み込む
    public function load() {
        //APIサーバから読み込む
        $url = sprintf('http://statsapi.mlb.com/api/v1/schedule/games/?sportId=1&date=%s/%s/%s', $this->_month, $this->_day, $this->_year);
        $this->_json = json_decode(file_get_contents($url, FALSE));
    }
    
    //読み込んだJSONを解析してスコア一覧の配列を構成する
    public function parse() {
        $this->_scores = array();
        
        //試合がない日
        if(empty($this->_json) || $this->_json->totalGames == 0) {
            $this->_message = 'No game is scheduled.';
            $this->_json = null;
            
            return;
        }
        
        foreach($this->_json->dates as $date) {
            foreach($date->games as $game) {
                //入っているべき情報が入っていない試合はイレギュラーとして無視
                if(empty($game->seriesDescription)) {
                    continue;
                }
                if(empty($game->status->abstractGameState)) {
                    continue;
                }
                
                $row = [];
                
                //レギュラーシーズン・ワールドシリーズ等の区別
                $row{'series'} = $game->seriesDescription;
                //試合の状態(試合中・試合終了など)
                $row{'status'} = $game->status->abstractGameState;
                //ビジターのチーム名
                $row{'away'} = $game->teams->away->team->name;
                //ホームのチーム名
                $row{'home'} = $game->teams->home->team->name;
                //ビジターの得点
                $row{'away_runs'} = !isset($game->teams->away->score) ? "" : $game->teams->away->score;
                //ホームの得点
                $row{'home_runs'} = !isset($game->teams->home->score) ? "" : $game->teams->home->score;
                
                //注釈
                $row{'note'} = "";
                //延期(試合中止)：スコアは空になり、延期の旨を注釈に入れる
                if(!empty($game->rescheduleGameDate)) {
                    $row{'note'} .= '[Rescheduled to ' . $game->rescheduleGameDate . ']';
                }
                //中断試合：再開日を注釈に入れる
                if(!empty($game->resumeDate)) {
                    $row{'note'} .= '[Resume to ' . $game->resumeGameDate . ']';
                }
                
                //*** 以下、現状未使用の項目 ***
                //試合ID
                $row{'guid'} = $game->gameGuid;
                $row{'game_pk'} = $game->gamePk;
                //試合のライブスコアのURL
                $row{'game_link'} = $game->link;
                //チームID
                $row{'away_team_id'} = $game->teams->away->team->id;
                $row{'home_team_id'} = $game->teams->home->team->id;
                //チーム情報のURL
                $row{'away_team_link'} = $game->teams->away->team->link;
                $row{'home_team_link'} = $game->teams->home->team->link;
                
                $this->_scores[] = $row;
            }
        }
    }
}

?>
