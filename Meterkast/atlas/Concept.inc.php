<?php // generated with ADL vs. 0.8.10-564
  
  /********* on line 279, file "comp/PWO_gmi/463.adl"
    SERVICE Concept : I[Concept]
   = [ description : description;display
     , population : contains;display
     ]
   *********/
  
  class Concept {
    protected $id=false;
    protected $_new=true;
    private $_description;
    private $_population;
    function Concept($id=null, $_description=null, $_population=null){
      $this->id=$id;
      $this->_description=$_description;
      $this->_population=$_population;
      if(!isset($_description) && isset($id)){
        // get a Concept based on its identifier
        // check if it exists:
        $ctx = DB_doquer('SELECT DISTINCT fst.`AttConcept` AS `i`
                           FROM 
                              ( SELECT DISTINCT `i` AS `AttConcept`, `i`
                                  FROM `concept`
                              ) AS fst
                          WHERE fst.`AttConcept` = \''.addSlashes($id).'\'');
        if(count($ctx)==0) $this->_new=true; else
        {
          $this->_new=false;
          // fill the attributes
          $me=firstRow(DB_doquer("SELECT DISTINCT `concept`.`i` AS `id`
                                       , `f1`.`display` AS `description`
                                    FROM `concept`
                                    LEFT JOIN  ( SELECT DISTINCT F0.`i`, F1.`display`
                                                   FROM `concept` AS F0, `explanation` AS F1
                                                  WHERE F0.`description`=F1.`i`
                                               ) AS f1
                                      ON `f1`.`i`='".addslashes($id)."'
                                   WHERE `concept`.`i`='".addslashes($id)."'"));
          $me['population']=firstCol(DB_doquer("SELECT DISTINCT `f1`.`display` AS `population`
                                                  FROM `concept`
                                                  JOIN  ( SELECT DISTINCT F0.`concept`, F1.`display`
                                                                 FROM `containsconcept` AS F0, `atom` AS F1
                                                                WHERE F0.`Atom`=F1.`i`
                                                             ) AS f1
                                                    ON `f1`.`concept`='".addslashes($id)."'
                                                 WHERE `concept`.`i`='".addslashes($id)."'"));
          $this->set_description($me['description']);
          $this->set_population($me['population']);
        }
      }
      else if(isset($id)){ // just check if it exists
        $ctx = DB_doquer('SELECT DISTINCT fst.`AttConcept` AS `i`
                           FROM 
                              ( SELECT DISTINCT `i` AS `AttConcept`, `i`
                                  FROM `concept`
                              ) AS fst
                          WHERE fst.`AttConcept` = \''.addSlashes($id).'\'');
        $this->_new=(count($ctx)==0);
      }
    }

    function save(){
      DB_doquer('START TRANSACTION');
      /**************************\
      * All attributes are saved *
      \**************************/
      $newID = ($this->getId()===false);
      $me=array("id"=>$this->getId(), "description" => $this->_description, "population" => $this->_population);
      DB_doquer("DELETE FROM `string` WHERE `i`='".addslashes($me['description'])."'",5);
      foreach($me['population'] as $i0=>$v0){
        DB_doquer("DELETE FROM `string` WHERE `i`='".addslashes($v0)."'",5);
      }
      $res=DB_doquer("INSERT IGNORE INTO `string` (`i`) VALUES ('".addslashes($me['description'])."')", 5);
      foreach($me['population'] as $i0=>$v0){
        $res=DB_doquer("INSERT IGNORE INTO `string` (`i`) VALUES ('".addslashes($v0)."')", 5);
      }
      if(true){ // all rules are met
        DB_doquer('COMMIT');
        return $this->getId();
      }
      DB_doquer('ROLLBACK');
      return false;
    }
    function del(){
      DB_doquer('START TRANSACTION');
      $me=array("id"=>$this->getId(), "description" => $this->_description, "population" => $this->_population);
      DB_doquer("DELETE FROM `string` WHERE `i`='".addslashes($me['description'])."'",5);
      foreach($me['population'] as $i0=>$v0){
        DB_doquer("DELETE FROM `string` WHERE `i`='".addslashes($v0)."'",5);
      }
      if(true){ // all rules are met
        DB_doquer('COMMIT');
        return true;
      }
      DB_doquer('ROLLBACK');
      return false;
    }
    function set_description($val){
      $this->_description=$val;
    }
    function get_description(){
      return $this->_description;
    }
    function set_population($val){
      $this->_population=$val;
    }
    function get_population(){
      if(!isset($this->_population)) return array();
      return $this->_population;
    }
    function setId($id){
      $this->id=$id;
      return $this->id;
    }
    function getId(){
      if($this->id===null) return false;
      return $this->id;
    }
    function isNew(){
      return $this->_new;
    }
  }

  function getEachConcept(){
    return firstCol(DB_doquer('SELECT DISTINCT `i`
                                 FROM `concept`'));
  }

  function readConcept($id){
      // check existence of $id
      $obj = new Concept($id);
      if($obj->isNew()) return false; else return $obj;
  }

  function delConcept($id){
    $tobeDeleted = new Concept($id);
    if($tobeDeleted->isNew()) return true; // item never existed in the first place
    if($tobeDeleted->del()) return true; else return $tobeDeleted;
  }

?>