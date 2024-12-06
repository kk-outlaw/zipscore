<?php
//日付を選択するフォーム
class Calendar {
    private $_year = '0000';
    private $_month = '00';
    private $_day = '00';
    
    public function getYear() {
        return $this->_year;
    }
    
    public function getMonth() {
        return $this->_month;
    }
    
    public function getDay() {
        return $this->_day;
    }
    
    public function __construct() {
        $this->_year = empty($_GET['year']) ? date('Y') : $_GET['year'];
        $this->_month = empty($_GET['month']) ? date('m') : $_GET['month'];
        $this->_day = empty($_GET['day']) ? date('d') : $_GET['day'];
        
        //不正パラメータ対策
        ob_start();
        $date = mktime(0, 0, 0, $this->_month, $this->_day, $this->_year);
        ob_end_clean();
        
        if($date == FALSE) {
            $this->_year = date('Y');
            $this->_month = date('m');
            $this->_day = date('d');
        }
    }
    
    private function today() {
        return [date('Y'), date('m'), date('d')];
    }
    
    private function yesterday() {
        $yesterday = strtotime('-1 day', strtotime(sprintf('%s-%s-%s', $this->_year, $this->_month, $this->_day)));
        
        return [date('Y', $yesterday), date('m', $yesterday), date('d', $yesterday)];
    }
    
    private function tommorrow() {
        $tommorrow =  strtotime('+1 day', strtotime(sprintf('%s-%s-%s', $this->_year, $this->_month, $this->_day)));
        return [date('Y', $tommorrow), date('m', $tommorrow), date('d', $tommorrow)];
    }
    
    public function datePickerForm($action) {
        $today = $this->today();
        $yesterday = $this->yesterday();
        $tommorrow = $this->tommorrow();
        
        $datePickerPrev = <<<DATE_PICKER_PREV
<form style="display: inline" id="date_select" method="GET" action="{$action}" target="_self">
    <input type="hidden" name="year" value="{$yesterday[0]}">
    <input type="hidden" name="month" value="{$yesterday[1]}">
    <input type="hidden" name="day" value="{$yesterday[2]}">
    <input type="submit" value="PREV">
</form>
DATE_PICKER_PREV;
        
        $datePickerToday = <<<DATE_PICKER_TODAY
<form style="display: inline" id="date_select" method="GET" action="{$action}" target="_self">
    <input type="hidden" name="year" value="{$today[0]}">
    <input type="hidden" name="month" value="{$today[1]}">
    <input type="hidden" name="day" value="{$today[2]}">
    <input type="submit" value="Today">
</form>
DATE_PICKER_TODAY;
        
        $datePickerNext = <<<DATE_PICKER_NEXT
<form style="display: inline" id="date_select" method="GET" action="{$action}" target="_self">
    <input type="hidden" name="year" value="{$tommorrow[0]}">
    <input type="hidden" name="month" value="{$tommorrow[1]}">
    <input type="hidden" name="day" value="{$tommorrow[2]}">
    <input type="submit" value="NEXT">
</form>
DATE_PICKER_NEXT;
        
        $datePickerSpecify = <<<EOS
<form id="date_select" method="GET" action="{$action}" target="_self">
<select name="year">
EOS;
        for($y = 2005; $y <= date('Y'); $y++) {
            $ys = sprintf('%04d', $y);
            if($ys == $this->_year) {
                $datePickerSpecify .= '<option value="' . $ys . '" selected>' . $ys . '</option>';
            } else {
                $datePickerSpecify .= '<option value="' . $ys . '">' . $ys . '</option>';
            }
        }
        
        $datePickerSpecify .= <<<EOS

</select>
<select name="month">
EOS;
        
        for($m = 1; $m <= 12; $m++) {
            $ms = sprintf('%02d', $m);
            if($ms == $this->_month) {
                $datePickerSpecify .= '<option value="' . $ms . '" selected>' . $ms . '</option>';
            } else {
                $datePickerSpecify .= '<option value="' . $ms . '">' . $ms . '</option>';
            }
        }
        
        $datePickerSpecify .= <<<EOS

    </select>
    <select name="day">
EOS;
        
        for($d = 1; $d <= 31; $d++) {
            $ds = sprintf('%02d', $d);
            if($ds == $this->_day) {
                $datePickerSpecify .= '<option value="' . $ds . '" selected>' . $ds . '</option>';
            } else {
                $datePickerSpecify .= '<option value="' . $ds . '">' . $ds . '</option>';
            }
        }
        
        $datePickerSpecify .= <<<EOS

    </select>
    <input type="submit" value="GO">
</form>
EOS;
        
        return $datePickerPrev . $datePickerToday . $datePickerNext . $datePickerSpecify;
    }
}

?>
