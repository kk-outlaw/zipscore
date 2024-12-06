<?php
//����̎����ꗗ
class Scoreboard {
    private $_year;
    private $_month;
    private $_day;
    
    private $_json;
    private $_scores;
    
    private $_message;
    
    //�X�R�A�ꗗ
    public function getScores() {
        return $this->_scores;
    }
    
    public function isEmpty() {
        return $this->_json == null;
    }
    
    //�G���[���b�Z�[�W
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
    
    //�X�R�A�ꗗ��ǂݍ���
    public function load() {
        //API�T�[�o����ǂݍ���
        $url = sprintf('http://statsapi.mlb.com/api/v1/schedule/games/?sportId=1&date=%s/%s/%s', $this->_month, $this->_day, $this->_year);
        $this->_json = json_decode(file_get_contents($url, FALSE));
    }
    
    //�ǂݍ���JSON����͂��ăX�R�A�ꗗ�̔z����\������
    public function parse() {
        $this->_scores = array();
        
        //�������Ȃ���
        if(empty($this->_json) || $this->_json->totalGames == 0) {
            $this->_message = 'No game is scheduled.';
            $this->_json = null;
            
            return;
        }
        
        foreach($this->_json->dates as $date) {
            foreach($date->games as $game) {
                //�����Ă���ׂ���񂪓����Ă��Ȃ������̓C���M�����[�Ƃ��Ė���
                if(empty($game->seriesDescription)) {
                    continue;
                }
                if(empty($game->status->abstractGameState)) {
                    continue;
                }
                
                $row = [];
                
                //���M�����[�V�[�Y���E���[���h�V���[�Y���̋��
                $row{'series'} = $game->seriesDescription;
                //�����̏��(�������E�����I���Ȃ�)
                $row{'status'} = $game->status->abstractGameState;
                //�r�W�^�[�̃`�[����
                $row{'away'} = $game->teams->away->team->name;
                //�z�[���̃`�[����
                $row{'home'} = $game->teams->home->team->name;
                //�r�W�^�[�̓��_
                $row{'away_runs'} = !isset($game->teams->away->score) ? "" : $game->teams->away->score;
                //�z�[���̓��_
                $row{'home_runs'} = !isset($game->teams->home->score) ? "" : $game->teams->home->score;
                
                //����
                $row{'note'} = "";
                //����(�������~)�F�X�R�A�͋�ɂȂ�A�����̎|�𒍎߂ɓ����
                if(!empty($game->rescheduleGameDate)) {
                    $row{'note'} .= '[Rescheduled to ' . $game->rescheduleGameDate . ']';
                }
                //���f�����F�ĊJ���𒍎߂ɓ����
                if(!empty($game->resumeDate)) {
                    $row{'note'} .= '[Resume to ' . $game->resumeGameDate . ']';
                }
                
                //*** �ȉ��A���󖢎g�p�̍��� ***
                //����ID
                $row{'guid'} = $game->gameGuid;
                $row{'game_pk'} = $game->gamePk;
                //�����̃��C�u�X�R�A��URL
                $row{'game_link'} = $game->link;
                //�`�[��ID
                $row{'away_team_id'} = $game->teams->away->team->id;
                $row{'home_team_id'} = $game->teams->home->team->id;
                //�`�[������URL
                $row{'away_team_link'} = $game->teams->away->team->link;
                $row{'home_team_link'} = $game->teams->home->team->link;
                
                $this->_scores[] = $row;
            }
        }
    }
}

?>
