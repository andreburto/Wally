<?php

/**
 * todo.php -- manages records in the todo table
 */

class todo extends WallyQuery2 {
    
    public function __construct($d=null, $u=null, $p=null, $db=null) {
        parent::__construct($d, $u, $p, $db);
    }
    
    public function __destruct() {
        parent::__destruct();
    }
    
    /* READ FUNCTIONS */
    public function GetOne($itemid=null) {
        if ($itemid==null) { return $this->SetError("No itemid passed"); }
        return $this->SelectRecord(array('itemid'=>$itemid), true);
    }
    
    public function GetAll() {
        $res = $this->SelectAll();
        if (count($res == 0)) { return $this->SetError("No records"); }
        
        /**
         * WallyQuery2.php::SelectAll() is primitive because it was rarely used.
         * It needs to be able modified to sort by column at some point. Until
         * then sort with ksort with an ID as an array key. --FIX--
         **/
        $temp = array();
        foreach($res as $item) { $temp[$item['itemid']] = $item; }
        $sorted = krsort($temp, SORT_NUMERIC);
        if ($sorted==false) { return $this->SetError("Could not sort all items"); }
        return $temp;
    }
    
    public function GetByUser($userid=null) {
        if ($userid==null) { return $this->SetError("No userid passed"); }
        $res = $this->SelectRecordRange(array('userid'=>$userid), null,
                                        "created", "DESC");
        if ($res==false) { return "User has no items"; }
        return $res;
    }
    
    /* INSERT FUNCTIONS */
    public function AddItem($todo=null, $userid=null) {
        if ($userid==null) { return $this->SetError("No userid passed"); }
        if ($todo==null) { return $this->SetError("No todo text passed"); }
        $res = $this->AddRecord(array('todo'=>$todo, 'userid'=>$userid,
                                      'created'=>time())); // Not a fan of db time
        if ($res==false) { return $this->SetError("Could not add item"); }
        return $this->InsertID();
    }
    
    /* DELETE FUNCTIONS */
    public function DelItem($itemid=null) {
        if ($itemid==null) { return $this->SetError("No itemid passed"); }
        $res = $this->DelRecord(array('itemid'=>$itemid), true);
    }
    
    // Use this function from the web, so you ensure the logged user is doing
    // the deleting.
    public function DelItemWithUser($itemid=null, $userid=null) {
        if ($itemid==null) { return $this->SetError("No itemid passed"); }
        if ($userid==null) { return $this->SetError("No userid passed"); }
        $res = $this->DelRecord(array('itemid'=>$itemid), true);
    }
    
    /* UPDATE FUNCTIONS */
    public function UpdateItem($todo=null, $itemid=null) {
        if ($itemid==null) { return $this->SetError("No itemid passed"); }
        if ($todo==null) { return $this->SetError("No todo text passed"); }
        $res = $this->UpdateRecord(array('todo'=>$todo), array('itemid'=>$itemid), true);
        if ($res == false) { return $this->SetError("Could not update item"); }
    }
}

?>