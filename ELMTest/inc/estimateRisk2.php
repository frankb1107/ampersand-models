<?php // generated with Prototype vs. 1.1.0.874(core vs. 2.0.0.13)
/**********************\
*                      *
*   Interface V1.3.1   *
*                      *
*                      *
*   Using interfaceDef *
*                      *
\**********************/
  error_reporting(E_ALL); 
  ini_set("display_errors", 1);
  require "interfaceDef.inc.php";
  require "estimateRisk2.inc.php";
  require "connectToDataBase.inc.php";
  if(isset($_REQUEST['save'])) { // handle ajax save request (do not show the interface)
    // we posted . characters, but something converts them to _ (HTTP 1.1 standard)
    $r=array();
    foreach($_REQUEST as $i=>$v){
      $r[join('.',explode('_',$i))]=$v; //convert _ back to .
    }
    if(@$r['0']!=''){
      $risk = @$r['0'];
    }else $risk=null;
    if(@$r['1']!=''){
      $accepted = @$r['1'];
    }else $accepted=null;
    $estimateRisk2=new estimateRisk2(@$_REQUEST['ID'],$risk, $accepted);
    if($estimateRisk2->save()!==false) die('ok:'.serviceref($_REQUEST['content']).'&estimateRisk2='.urlencode($estimateRisk2->getId()));
    else die('Please fix errors!');
    exit(); // do not show the interface
  }
  $buttons="";
  if(isset($_REQUEST['new'])) $new=true; else $new=false;
  if(isset($_REQUEST['edit'])||$new) $edit=true; else $edit=false;
  $del=isset($_REQUEST['del']);
  if(isset($_REQUEST['estimateRisk2'])){
    if(!$del || !delestimateRisk2($_REQUEST['estimateRisk2']))
      $estimateRisk2 = readestimateRisk2($_REQUEST['estimateRisk2']);
    else $estimateRisk2 = false; // delete was a succes!
  } else if($new) $estimateRisk2 = new estimateRisk2();
  else $estimateRisk2 = false;
  if($estimateRisk2){
    writeHead("<TITLE>estimateRisk2 - ctxELMTest - Ampersand Prototype</TITLE>"
              .($edit?'<SCRIPT type="text/javascript" src="js/edit.js"></SCRIPT>':'').'<SCRIPT type="text/javascript" src="js/navigate.js"></SCRIPT>'."\n" );
    if($edit)
        echo '<FORM name="editForm" action="'.$_SERVER['PHP_SELF'].'" method="POST" class="Edit">';
    if($edit && $estimateRisk2->isNew())
         echo '<P><INPUT TYPE="TEXT" NAME="ID" VALUE="'.addslashes($estimateRisk2->getId()).'" /></P>';
    else echo '<H1>'.$estimateRisk2->getId().'</H1>';
    ?>
    <DIV class="Floater risk">
      <DIV class="FloaterHeader">risk</DIV>
      <DIV class="FloaterContent"><?php
          $risk = $estimateRisk2->get_risk();
          //PICK an existing item0. Creating instances should at most be possible for simple Concepts.
          if(isset($risk)){
            echo '<DIV CLASS="item UI_risk" ID="0">';
          }else{
            echo '<DIV CLASS="new UI_risk" ID="0">';
          }
              if(isset($risk) && $risk!=''){
                if(!$edit) echo '
                <A HREF="'.serviceref('LMH',false,$edit, array('LMH'=>urlencode($risk))).'">'.htmlspecialchars($risk).'</A>';
                else echo htmlspecialchars($risk);
              } else {echo '<I>Nothing</I>';}
          echo '</DIV>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater accepted">
      <DIV class="FloaterHeader">accepted</DIV>
      <DIV class="FloaterContent"><?php
          $accepted = $estimateRisk2->get_accepted();
          //PICK an existing item1. Creating instances should at most be possible for simple Concepts.
          if(isset($accepted)){
            echo '<DIV CLASS="item UI_accepted" ID="1">';
          }else{
            echo '<DIV CLASS="new UI_accepted" ID="1">';
          }
              if(isset($accepted) && $accepted!=''){
                if(!$edit){
                  echo '
                <A class="GotoLink" id="To1">';
                  echo htmlspecialchars($accepted).'</A>';
                  echo '<DIV class="Goto" id="GoTo1"><UL>';
                  echo '<LI><A HREF="'.serviceref('decideObligationRisks',false,$edit, array('decideObligationRisks'=>urlencode($accepted))).'">decideObligationRisks</A></LI>';
                  echo '<LI><A HREF="'.serviceref('estimateRisk2',false,$edit, array('estimateRisk2'=>urlencode($accepted))).'">estimateRisk2</A></LI>';
                  echo '</UL></DIV>';
                } else echo htmlspecialchars($accepted);
              } else {echo '<I>Nothing</I>';}
          echo '</DIV>';
        ?> 
      </DIV>
    </DIV>
    <?php
    if($edit) echo '</FORM>';
   if($del) echo "<P><I>Delete failed</I></P>";
   if($edit){
     if($new) 
       $buttons.=ifaceButton("JavaScript:save('".serviceref($_REQUEST['content'])."&save=1', document.forms[0].ID.value);","Save");
     else { 
       $buttons.=ifaceButton("JavaScript:save('".serviceref($_REQUEST['content'])."&save=1','".urlencode($estimateRisk2->getId())."');","Save");
       $buttons.=ifaceButton(serviceref($_REQUEST['content'],false,false, array('estimateRisk2'=>urlencode($estimateRisk2->getId()) )),"Cancel");
     } 
  } else {
          $buttons.=ifaceButton(serviceref($_REQUEST['content'],false,false, array('estimateRisk2'=>urlencode($estimateRisk2->getId()),'edit'=>1)),"Edit");
          $buttons.=ifaceButton(serviceref($_REQUEST['content'],false,false, array('estimateRisk2'=>urlencode($estimateRisk2->getId()),'del'=>1)),"Delete");
         }
  }else{
    if($del){
      writeHead("<TITLE>Delete geslaagd</TITLE>");
      echo 'The estimateRisk2 is deleted';
    }else{  // deze pagina zou onbereikbaar moeten zijn
      writeHead("<TITLE>No estimateRisk2 object selected - ctxELMTest - Ampersand Prototype</TITLE>");
      ?><i>No estimateRisk2 object selected</i><?php 
    }
  }
  writeTail($buttons);
?>