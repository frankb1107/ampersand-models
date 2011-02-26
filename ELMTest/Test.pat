PATTERN "RisicoManagementProcesje" -- WIJZIGER: rieks.joosten@tno.nl
-- Dit pattern is een eenvoudig procesje voor risico management en is bedoeld om te demonstreren hoe aan de hand hiervan (en aan de hand van de invarianten), services worden gedefinieerd. Bij deze demo hoort uiteraard ook de eruit gegenereerde service-laag met website.

CONCEPT Asset "one (of possibly many) implementation of a (business) function."
CONCEPT Obligation "a rule that (the accountable person of) an asset is committed to comply with."
CONCEPT LMH "the set {'L', 'M', 'H'}, of which each element represents a level of risk (Low, Medium or High respectively)."

assetManager :: Asset -> Person PRAGMA "Accountability for " " has been assigend to ".

obligationOf :: Obligation -> Asset PRAGMA "Compliance to " " is committed to by (the owner of) ".
oblRisk :: Obligation * LMH [UNI] PRAGMA "The expected amount of (yearly) damage for the holon for " " is estimated to be ".

statAssetRA :: Asset * Asset [SYM,ASY] PRAGMA "Alle risico's voor " " zijn - in hun gezamenlijkheid - geaccepteerd".

dAssetRA :: Asset * Asset [SYM,ASY] PRAGMA "De eigenaar van " " heeft expliciet besloten dat alle risico's met betrekking tot " " in hun gezamenlijkheid acceptabel zijn".

RULE "acceptatie van asset risicos": statAssetRA = dAssetRA \/ (I /\ obligationOf~;obligationOf) /\ (-obligationOf~ !(statOblRA; obligationOf))
PURPOSE RULE "acceptatie van asset risicos" IN DUTCH
{+Alle risico's van het niet nakomen van verplichtingen van een asset zijn ofwel expliciet geaccepteerd middels een besluit, ofwel impliciet geaccepteerd doordat van elke individuele verplichting het risico is geaccepteerd.-}

statOblRA :: Obligation * Obligation [SYM,ASY] PRAGMA "Het risico dat " " niet wordt nagekomen is geaccepteerd".

dOblRA :: Obligation * Obligation [SYM,ASY] PRAGMA "De eigenaar van de asset die verplicht is " " na te leven, heeft expliciet besloten het risico dat voortvloeit uit het niet naleven van " " acceptabel is".

RULE "acceptatie van individuele risicos": statOblRA = dOblRA \/ (I /\ oblRisk; (I \/ lth); 'L'; V)
PURPOSE RULE "acceptatie van individuele risicos" IN DUTCH
{+Een risico van het niet nakomen van een verplichting is geaccepteerd als dit ofwel expliciet is besloten, ofwel impliciet geldt omdat het risico als voldoende laag (of lager) is gekarakteriseerd.-}

RULE "lmh_atoms": I[LMH] = 'L' \/ 'M' \/ 'H' PHRASE "Naast L(aag), M(idden), en H(oog) zijn geen andere LMH-scores mogelijk."

-- Om te voorkomen dat we bij niet bestaande populaties overtredingen krijgen op bovenstaande regel, introduceren we hier een hulprelatie met populatie
lth :: LMH * LMH [TRN,ASY] PRAGMA "" " is kleiner/lager dan " = [ ("L", "M"); ("L", "H"); ("M", "H") ].
PURPOSE RELATION lth[LMH*LMH] IN DUTCH
{+ Om te voorkomen dat we bij niet bestaande populaties overtredingen krijgen op bovenstaande regel, introduceren we hier een hulprelatie met populatie.-}

ENDPATTERN
------------------------
SERVICE Asset1(assetManager,dAssetRA) : I[Asset]
 = [ manager     : assetManager
   , "accepted status"   : statAssetRA
   , "manually accepted"  : dAssetRA
   , obligations : obligationOf~
   ]

SERVICE Assets1(assetManager,dAssetRA) : I[ONE]
   = [ Assets : V[ONE*Asset]
     = [ manager     : assetManager
     , accepted    : dAssetRA
     , obligations : obligationOf~
   ] ]

SERVICE Assets2 (dAssetRA): I[Asset];-statAssetRA
 = [ accepted    : dAssetRA
   , obligations : obligationOf~
   ]

------------------------
--!GMI: simple3.adl is waar ik het meest mee getest heb. cmd: adl --theme=student -p simple3
PROCESS "ELMTest"
ROLE AssetOwner EDITS dAssetRA, dOblRA
ROLE AssetOwner USES decideAssetRisks, decideObligationRisks, decideAllRisks
ROLE SecurityOfficer EDITS oblRisk
ROLE SecurityOfficer USES decideAllRisks,estimateRisk1,estimateRisk2,estimateRisk3
ENDPROCESS

SERVICE decideAssetRisks (dAssetRA): I[Asset];-statAssetRA
 = [ accepted    : dAssetRA
   , obligations : obligationOf~
   ]

SERVICE decideObligationRisks (dOblRA): I[Obligation]
 = [ risk       : oblRisk
   , accepted   : dOblRA
   ]

SERVICE decideAllRisks (dAssetRA, dOblRA): I[Asset];-statAssetRA
 = [ accepted  : dAssetRA
   , obligations : obligationOf~
   = [ risk       : oblRisk
     , accepted   : dOblRA
   ] ]

SERVICE estimateRisk1 (oblRisk): I[Asset]
 = [  accepted  : dAssetRA
   , obligations : obligationOf~
   ]

SERVICE estimateRisk2 (oblRisk): I[Obligation]
 = [ risk       : oblRisk
   , accepted   : dOblRA
   ]

SERVICE estimateRisk3 (oblRisk): I[Asset]
 = [ accepted  : dAssetRA
   , obligations : obligationOf~
   = [ risk       : oblRisk
     , accepted   : dOblRA
   ] ]

------------------------