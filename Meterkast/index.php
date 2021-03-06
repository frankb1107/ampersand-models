<?php
  if (!isset($_SERVER['AUTH_USER'])|| $_SERVER['AUTH_USER']=='') {
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
      header('WWW-Authenticate: Basic realm="Ampersand - Bedrijfsregels"');
      echo 'Just enter a name without password. Refresh the page to retry...';
      exit;
    } else {
      DEFINE("USER","PHP_".str_replace(" ","",$_SERVER['PHP_AUTH_USER']));
    }
  } else {
    DEFINE("USER", str_replace("\\", "_", $_SERVER['AUTH_USER']));
  }

  DEFINE("IMGPATH","");
  DEFINE("FILEPATH","comp/".USER."/");
  DEFINE("COMPILATIONS_PATH","comp/".USER."/");
  @mkdir(FILEPATH);
  session_start();
  if (isset($_POST['adlsessie'])) //user reloads session
     $_SESSION["adlsessie"]=$_POST['sessie'];
  elseif (isset($_POST['adlbestand']) || isset($_POST['adltekst'])) //user sends new script
  { 
	  session_regenerate_id();
	  $_SESSION["adlsessie"]=session_id(); 
  }
  elseif (!isset($_SESSION["adlsessie"])) //if comes for the first time
  { 
	  $_SESSION["adlsessie"]=session_id();
	  $_SESSION["home"]=$_SERVER['PHP_SELF'];
  } //else -> leave session as is (user is coming back from Atlas or something)
  require "inc/Session.inc.php";
  require "inc/Gebruiker.inc.php";
  require "inc/Bestand.inc.php";
  require "inc/Actie.inc.php";
  require "inc/Operatie.inc.php";
  require "inc/connectToDataBase.inc.php";
  //$DB_debug=6;
  $ses = new Session($_SESSION["adlsessie"]);
  if($ses->isNew()){
    $ses->set_ip($_SERVER['REMOTE_ADDR']);
    $ses->set_gebruiker(USER);
    $ses->save();
//  }else if($ses->get_ip()!=$_SERVER['REMOTE_ADDR']){ //GMI-> Do I need this check/regenerate action???
//      $n=0;
//      while (!$ses->isNew() && $n<50) {
//        $n++;
//        session_regenerate_id();
//        $ses = new Session(session_id(),$_SERVER['REMOTE_ADDR'],null);
//      }
//      if($n>=50) die('Error creating new session, please reload this page or contact the system administrator.');
//      $ses->save();
  }

  if(isset($_POST['adlbestand'])){
    delBestand($ses->get_file());
    $file = new Bestand(null,$_FILES['file']['name'],$ses->getId(),array());
    if($file->save()!==false){
      move_uploaded_file($_FILES['file']['tmp_name'],FILEPATH.$file->getId().'.adl');
    }else{
      echo $file->getId();
      $file = false;
    }
  }
  else if(isset($_POST['adltekst'])){
    if ($_POST['scriptname']=='') {$sn =  'phptekstinvoer';} else {$sn =  $_POST['scriptname'];}	  
    delBestand($ses->get_file());
    $file = new Bestand(null,$sn,$ses->getId(),array());
    if($file->save()!==false){
      file_put_contents(FILEPATH.$file->getId().'.adl',$_POST['adltext']);
    }else{
      echo $file->getId();
      $file = false;
    }
  } else {$file=readBestand($ses->get_file());}
  
	  DEFINE("HANDLEIDING","Om uw bedrijfsregels te onderzoeken, kunt u een Ampersandscript aanbieden en laten analyseren. U kunt uw regels op fouten onderzoeken, conceptuele modellen bekijken, populaties bekijken en overtredingen van regels vaststellen. Ook kunt u een functionele specificatie genereren voor een informatiesysteem wat uw bedrijfsregels naleeft.<br/><br/>U kunt uw Ampersandscript op drie manieren aanbieden:<br/> * Door een .adl bestand van uw computer up te loaden<br/> * Door een eerdere versie van een script te laden<br/> * Door de Ampersandcode in het tekstveld te laden<br/><br/>E�n of meerdere bewerkingen kunnen uitgevoerd worden door op <i>Uitvoeren</i> achter de gewenste bewerking te klikken. Als een bewerking output genereert, dan verschijnt er na succesvolle uitvoering een <b>vinkje</b> dat tevens de link is naar de output. Als een bewerking geen output genereert, dan wordt een succesvolle bewerking aangegeven met een toepasselijke melding.<br/><br/>Iedere aanpassing aan het script, ook in het tekstveld, vereist dat het script opnieuw naar de server wordt gestuurd. Iedere keer als u een script naar de server stuurt, dan krijgt dit script een nieuw versienummer. Per scriptversie kunt u de verschillende soorten bewerkingen eenmaal uitvoeren. Het is niet nodig om een bewerking opnieuw uit te kunnen voeren, omdat het resultaat van een bewerking op een bepaalde versie altijd hetzelfde zal zijn. Als u eerdere resultaten van oudere versies wilt raadplegen dan kunt u de betreffende versie laden.<br/></br>Voor verdere informatie bekijk de meldingen onder de andere informatie symbolen.");
	  DEFINE("SCRIPTNAAM","Als u het script herlaadt, dan krijgt het script een nieuw versienummer. U kunt deze versie een voor u herkenbare scriptnaam geven.");
	  DEFINE("ADLTEKST","U kunt het Ampersandscript in het tekstveld wijzigen. Wijzigingen moeten via de knop <b>Script wijzigingen laden</b> geladen worden. Een gewijzigd script krijgt een nieuw versienummer.");
	  DEFINE("SESSIELADEN","Een eerdere versie van een script kunt u opnieuw laden. De links naar de resultaten van de bewerkingen, die u destijds heeft uitgevoerd op dit script, worden getoond. Ook kunt u de nog niet op dit script uitgevoerde bewerkingen uitvoeren.");
	  DEFINE("ADLBEWERKING","Klik op <b>Uitvoeren</b> achter de bewerking die u wil uitvoeren. U mag meerdere bewerkingen tegelijk uitvoeren. Als een bewerking een gegenereerd resultaat oplevert, dan is dat resultaat te benaderen via het vinkje.");
	  DEFINE("BESTANDLADEN","Selecteer het bestand op uw filesysteem met daarin het Ampersandscript dat u wilt laden. Klik op de knop <b>Bestand uploaden</b>. Het script verschijnt in het tekstveld onderaan de pagina, voorzien van regelnummers in Ampersandcommentaar. Wijzigingen in het tekstveld worden <i>niet</i> gesynchroniseerd met het bestand op uw filesysteem");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/strict.dtd">
<HTML>
<HEAD>
<script type="text/javascript" src="jquery-1.3.2.min.js"></script>
<TITLE>Ampersand</TITLE>
<SCRIPT type="text/javascript" src="compile.js"></SCRIPT>
<SCRIPT type="text/javascript" src="overlib421/overlib.js"><!-- overLIB (c) Erik Bosrup --></SCRIPT>
<script type="text/javascript">
var funcspecinfo="<a href=\"javascript:void(0);\" onmouseover=\"return overlib('Met deze bewerking kunt u een functionele specificatie (pdf) genereren die overeenkomt met wat u in het Ampersandscript gespecificeerd heeft.',WIDTH, 350);\" onmouseout=\"return nd();\"><IMG SRC=\"info.png\" /></a>";
var funcspecwarning="<a href=\"javascript:void(0);\" onmouseover=\"return overlib('Eventuele errors worden momenteel om technische redenen onderdrukt. Als het gegenereerde pdf bestand niet bestaat geef dit dan door aan gerard.michels@ou.nl. N.B. Om zonder poespas (geen refresh+resend requests) terug naar deze pagina te kunnen keren is het aan te raden om het pdf bestand te openen in een nieuw window of tabblad (rechtermuisknop op het vinkje).',WIDTH, 350);\" onmouseout=\"return nd();\"><IMG SRC=\"warning.png\" /></a>";
var checkscriptinfo="<a href=\"javascript:void(0);\" onmouseover=\"return overlib('Met deze bewerking kunt u testen of de syntax klopt en of relatie algebra regels typecorrect zijn opgesteld. Als dit het geval is, dan wordt de melding <i>De bewerking is succesvol uitgevoerd</i> weergegeven.',WIDTH, 350);\" onmouseout=\"return nd();\"><IMG SRC=\"info.png\" /></a>";
var atlasinfo="<a href=\"javascript:void(0);\" onmouseover=\"return overlib('Met deze bewerking kunt u de <b>Atlas</b> van uw script genereren. Het is mogelijk om data en plaatjes in afzonderlijke bewerkingen te genereren. Dit is nuttig als u een script heeft waarvan het genereren van plaatjes te lang duurt naar de zin van de web server. Dit is mogelijk het geval als u de melding <i>error retrieving compile.php</i> terugkrijgt.',WIDTH, 350);\" onmouseout=\"return nd();\"><IMG SRC=\"info.png\" /></a>";
</script>
<link rel="stylesheet" type="text/css" href="style.css" />
</HEAD>
<BODY>
  <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div><!--needed for overLIB-->

<div style="width:100%;background-color:#ffffff;margin:0px;padding:0px;top:0px"> <img style="margin:0px;padding:0px;float:right" src="ou.jpg">

<H5 style="text-align:left">Huidige geladen script: 
      <?php if ($file) echo htmlspecialchars($file->get_path()).' ('.$file->getId().'). '; 
            else echo 'Geen. ';?>
      U bent ingelogd als: <?php echo USER; ?>
  <a href="javascript:void(0);" 
  onmouseover="return overlib('<?php echo (HANDLEIDING); ?>',STICKY, CAPTION, 'Handleiding',CLOSECLICK,WIDTH, 500);"
     onmouseout="return nd();">
     <?php echo '<IMG SRC="'.IMGPATH.'info.png" />'; ?></a>
</H5>

</div>

  <hr style="height:15px"></hr>

  <FORM name="myForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
  <H3 style="text-align:left">Voer een bewerking uit op het geladen Ampersandscript
  <a href="javascript:void(0);" 
     onmouseover="return overlib('<?php echo (ADLBEWERKING); ?>',WIDTH, 350);"
     onmouseout="return nd();">
     <?php echo '<IMG SRC="'.IMGPATH.'info.png" />'; ?></a>
  </H3>


<?php if ($file && !isset($_REQUEST['newFile'])) { ?>
  <UL>
  <?php
 // $havops=array();

  function trygetActie($file,$op){
    foreach($file->get_compilations() as $c){
     if ($c['operatie']==$op) return $c['id'];
    } 
    return false;
  }
  foreach(getEachOperatie() as $opid){
    if ($op=readOperatie($opid)){
       echo '<LI id="op'.$opid.'">'.$op->get_naam().': <SPAN>'; // reason that getName() is not shown as htmlspecialchars: only the Admin can change this, and maybe he'd like to put in an image, such as the LaTeX logo
       if ($actid=trygetActie($file,$opid)){
          if ($act=readActie($actid)){
             $target = escapeshellcmd(COMPILATIONS_PATH.$file->getId().'_'.$opid.'/');
             $source = escapeshellcmd(FILEPATH.$file->getId().'.adl');
             $compileurl = ''.sprintf($op->get_outputURL(),$target,$source,$file->getId(),USER); 
	     if ($act->get_error()){ echo $act->get_error();
	     } else { 
	          if ($act->get_compiled())
	               echo '<A HREF="'.$compileurl.'" target="_blank"/><IMG SRC="'.IMGPATH.'ok.png" /></A>';
                  else echo '<small><A href="JavaScript:compile(\''.$opid.'\');">Uitvoeren</A></small>';
	     }   
           } else echo '<small><A href="JavaScript:compile(\''.$opid.'\');">Uitvoeren</A></small>';
       }else echo '<small><A href="JavaScript:compile(\''.$opid.'\');">Uitvoeren</A></small>';
       echo '</SPAN></LI>';
    }
  }

  ?>
  </UL><br>
<?php } 
else {
  foreach(getEachOperatie() as $opid){
	  if ($op=readOperatie($opid)){
		  echo '<LI id="op'.$opid.'">'.$op->get_naam().': <i>geen script geladen</i> </LI>';
	  }
  }
}?>

  <P><textarea name="adltext" cols=100 rows=30><?php if ($file && !isset($_REQUEST['newFile'])) {
	        //if () {  //user toevoegen aan meterkast.adl en juiste FILEPATH nemen.
	     //} else {
	     $i=0;
	     foreach( file ( escapeshellcmd(FILEPATH.$file->getId().'.adl')) as $line) //}
	     {
		     $i++;
		     $line = (preg_replace('/{-(\d+)-}/','',$line));
		     echo '{-'.$i.'-}'.$line;
	     }
  } else { echo 'Gebruik ��n van onderstaande opties om een script te laden:
 + Laad een .adl bestand van uw computer (zie onder).
 + Laad een eerdere scriptversie.
 + Type een Ampersandscript in dit tekstveld en klik op de knop "Script wijzigingen laden".

Het geladen script zal worden weergegeven in dit tekstveld.
 
U kunt een geladen script in dit tekstveld wijzigen en deze als een nieuwe versie laden via de knop "Script wijzigingen laden".'; } 
     ?>
     </textarea></P>
<br>
  <P>Scriptnaam <input type="text" name="scriptname" value="<?php if ($file) echo htmlspecialchars($file->get_path()); ?>"/>
  <a href="javascript:void(0);" 
     onmouseover="return overlib('<?php echo (SCRIPTNAAM); ?>',WIDTH, 350,ABOVE);"
     onmouseout="return nd();">
     <?php echo '<IMG SRC="'.IMGPATH.'info.png" />'; ?></a>
  </P>
<br>
  <P><input type="submit" name="adltekst" value="Script wijzigingen laden" />
  <a href="javascript:void(0);" 
     onmouseover="return overlib('<?php echo (ADLTEKST); ?>',WIDTH, 350);"
     onmouseout="return nd();">
     <?php echo '<IMG SRC="'.IMGPATH.'info.png" />'; ?></a></P>
<br>  
<hr/>
  <table><tr>
  <td>
  <H3>Laad een .adl bestand op uw computer
  <a href="javascript:void(0);" 
     onmouseover="return overlib('<?php echo (BESTANDLADEN); ?>',WIDTH, 350,ABOVE);"
     onmouseout="return nd();">
     <?php echo '<IMG SRC="'.IMGPATH.'info.png" />'; ?></a>
  </H3>
  <P><input type="file" name="file" /></P><br>
  <P><input type="submit" name="adlbestand" value="Bestand uploaden" /></P>
  </td><vr/><td>
  <H3>Laad een eerdere versie van een Ampersandscript
  <a href="javascript:void(0);" 
     onmouseover="return overlib('<?php echo (SESSIELADEN); ?>',WIDTH, 350,ABOVE);"
     onmouseout="return nd();">
     <?php echo '<IMG SRC="'.IMGPATH.'info.png" />'; ?></a>
  </H3>
  <p>Scriptversie <select name="sessie"><?php
  $myscripts = array();
  if ($usr = readGebruiker($ses->get_gebruiker()))
  {
    //ignore sessions/files which cannot be found
    foreach ($usr->get_sessies() as $sesid)
    {
      if($sesi = readSession($sesid)){
        if($sesf = readBestand($sesi->get_file())){
	  $myscripts += 
		array($sesf->getId() => '<option value="'.$sesid.'">'.$sesf->get_path().' ('.$sesf->getId().')</option>');
	    }
	  }
    } if (ksort ($myscripts)) echo print_r(array_reverse($myscripts,true));
  } ?></select></p><br>
  <P><input type="submit" name="adlsessie" value="Script laden" /></P>
  </td>
  </tr></table>
  </FORM>
</BODY>
</HTML>

