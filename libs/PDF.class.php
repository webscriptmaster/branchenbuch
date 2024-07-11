<?php
//=========================================
// Script	: ProConcept
// File		: PDF.class.php
// Version	: 0.1
// Author	: Matthias Franke
// Email	: info@matthiasfranke.com
// Website	: http://www.matthiasfranke.com
//=========================================
// Copyright (c) 2007 Matthias Franke
//=========================================
class PDF extends FPDF {

	function footer_pfv(){
		$this->SetAutoPageBreak(false);
		$this->SetY(-15);$this->SetFont('Arial','',8);
		$this->Cell(4,4,'pro','',0,'L');$this->SetFont('Arial','I',8);$this->Cell(12,4,'Concept','',0,'L');$this->SetFont('Arial','',8);$this->Cell(5,4,'AG','',0,'L');$this->Cell(0,4,' - Stand 10.03.2008','',0,'L');
		$this->SetAutoPageBreak(true);$this->SetFont('Arial','',10);
	}
	
	function get_easy($data){
		
		//$pdf = new PDF();
		$this->SetCreator('lv-doktor.com');
		$this->SetAuthor('lv-doktor.com');
		$this->SetLeftMargin(20);
		$this->SetRightMargin(20);
		$this->AliasNbPages();
		$this->SetDisplayMode('default','continuous');
		$this->SetCompression(true) ;
		############################################################# Einleitungsseite #############################################################
		$this->AddPage();
		$this->SetY(15);$this->SetDrawColor(0,0,0);$this->SetFillColor(102,153,204);
		$this->SetFont('Arial','B',12);$this->Cell(0,5,'Ökopaket','',0,'C');
		$this->Ln(10);$this->SetFont('Arial','BIU',10);$this->Cell(0,4,'Merkmale:','',0,'L');$this->SetFont('Arial','',10);
		$this->Ln(8);$this->Cell(0,4,'1. nur noch 3 Seiten','',0,'L');
		$this->Ln(5);$this->Cell(0,4,'2. nach erfolgreichem Verfahrensabschluss garantieren wir die Erstattung mindestens in Höhe','',0,'L');
		$this->Ln(4);$this->Cell(0,4,'    der doppelten Bearbeitungsgebühr','',0,'L');
		$this->Ln(5);$this->Cell(0,4,'3. für jeden Vertrag ein Antrag','',0,'L');
		$this->Ln(5);$this->Cell(0,4,'4. kein Vorprüfergebnis','',0,'L');
		$this->Ln(5);$this->Cell(0,4,'5. alle Daten müssen durch Vermittler online erfasst sein','',0,'L');
		$this->Ln(10);$this->SetFont('Arial','BIU',10);$this->Cell(0,4,'Benötigte Unterlagen:','',0,'L');$this->SetFont('Arial','',10);
		$this->Ln(8);$this->Cell(0,4,'- den korrekt ausgefüllten Prüfauftrag – bestehend aus 2 Seiten','',0,'L');
		$this->Ln(8);$this->Cell(0,4,'- Rechtsanwaltsvollmacht – blanko unterschrieben und pro Vertrag eine','',0,'L');
		$this->Ln(8);$this->Cell(0,4,'- der Originalversicherungsschein des zu kündigenden Vertrages','',0,'L');
		$this->Ln(4);$this->SetFont('Arial','BI',8);$this->Cell(0,4,'  (wenn der originale Versicherungsschein nicht mehr vorliegt eine Verlusterklärung beifügen)','',0,'L');$this->SetFont('Arial','',10);
		$this->Ln(8);$this->Cell(0,4,'- oder das Abrechnungsschreiben des gekündigten Vertrages','',0,'L');
		$this->Ln(10);$this->SetFont('Arial','BIU',10);$this->Cell(0,4,'optional:','',0,'L');$this->SetFont('Arial','',10);
		$this->Ln(8);$this->Cell(0,4,'- Auszahlungsauftrag','',0,'L');
		$this->Ln(8);$this->Cell(0,4,'- Freistellungsauftrag','',0,'L');
		$this->Ln(4);$this->SetFont('Arial','BI',8);$this->Cell(0,4,'  (nachträglich eingereichte können nicht berücksichtigt werden)','',0,'L');$this->SetFont('Arial','',10);
		$this->Ln(8);$this->Cell(0,4,'- Factoringauftrag','',0,'L');
		$this->Ln(4);$this->SetFont('Arial','BI',8);$this->Cell(0,4,'  (nur für ungekündigte Verträge- nachträglich eingehende können nicht berücksichtigt werden)','',0,'L');$this->SetFont('Arial','',10);
		$this->Ln(10);$this->SetFont('Arial','BIU',10);$this->Cell(0,4,'Postversand bitte an:','',0,'L');$this->SetFont('Arial','',10);
		$this->Ln(8);$this->Cell(5,4,'pro','',0,'L');$this->SetFont('Arial','I',10);$this->Cell(15,4,'Concept','',0,'L');$this->SetFont('Arial','',10);$this->Cell(0,4,'AG','',0,'L');
		$this->Ln(4);$this->Cell(0,4,'C/o „LV-Doktor-Team“','',0,'L');
		$this->Ln(4);$this->Cell(0,4,'Ankerstrasse 3a','',0,'L');
		$this->Ln(4);$this->Cell(0,4,'06108 Halle','',0,'L');
		$this->footer_pfv();
		############################################################# Leerseite #############################################################
		$this->AddPage();
		############################################################# PFV Seite 1/5 #############################################################
		$this->AddPage();
		$this->SetY(10);
		$this->SetFont('Arial','B',12);$this->Cell(0,5,'Prüfauftrag Ökopaket','',0,'C');
		if($user[26]!=''){
			
			//Vertriebspartner mit Orga_Sub
			$this->Ln(8);$this->SetFont('Arial','B',8);$this->Cell(0,4,'Vertriebspartner','LTR',0,'L',1);$this->SetFont('Arial','',8);
			$this->Ln(4);$this->Cell(25,5,'Name:','L',0,'L');$this->Cell(45,5,$name[0],'B',0,'L');$this->Cell(10);$this->Cell(25,5,'Vorname:',0,0,'L');$this->Cell(45,5,$name[1],'B',0,'L');$this->Cell(0,5,'','R',0,'L');
			$this->Ln(5);$this->Cell(25,5,'VP-Nummer:','L',0,'L');$this->Cell(45,5,$partner[0].' - '.$user[26],'B',0,'L');$this->Cell(10);$this->Cell(25,5,'E-Mail:',0,0,'L');$this->Cell(45,5,$email,'B',0,'L');$this->Cell(0,5,'','R',0,'L');
			$this->Ln(5);$this->Cell(25,5,'Telefon:','L',0,'L');$this->Cell(45,5,$telefon,'B',0,'L');$this->Cell(10);$this->Cell(25,5,'Handy:',0,0,'L');$this->Cell(45,5,$handy,'B',0,'L');$this->Cell(0,5,'','R',0,'L');
		
			$this->Ln(4);$this->Cell(0,4,'','LBR',0,'L');
		}
		else {
			//Vertriebspartner
			$this->Ln(8);$this->SetFont('Arial','B',8);$this->Cell(0,4,'Vertriebspartner','LTR',0,'L',1);$this->SetFont('Arial','',8);
			$this->Ln(4);$this->Cell(25,5,'Name:','L',0,'L');$this->Cell(45,5,$name[0],'B',0,'L');$this->Cell(10);$this->Cell(25,5,'Vorname:',0,0,'L');$this->Cell(45,5,$name[1],'B',0,'L');$this->Cell(0,5,'','R',0,'L');
			$this->Ln(5);$this->Cell(25,5,'VP-Nummer:','L',0,'L');$this->Cell(45,5,$partner[0].' /','B',0,'L');$this->Cell(10);$this->Cell(25,5,'E-Mail:',0,0,'L');$this->Cell(45,5,$partner[9],'B',0,'L');$this->Cell(0,5,'','R',0,'L');
			$this->Ln(5);$this->Cell(0,4,'','LBR',0,'L');
			if($partner[0]=='12913') $vollmacht=$partner[5];
			else $vollmacht=$partner[6].', '.$partner[8];
		}
		//Persönliche Daten Kunde (Auftraggeber)
		$this->Ln(7);$this->SetFont('Arial','B',8);$this->Cell(0,4,'Persönliche Daten Kunde (Auftraggeber)','LTR',0,'L',1);$this->SetFont('Arial','',8);
		$this->Ln(4);$this->Cell(25,5,'Name:','L',0,'L');$this->Cell(45,5,$user[11],'B',0,'L');$this->Cell(10);$this->Cell(25,5,'Vorname:',0,0,'L');$this->Cell(45,5,$user[10],'B',0,'L');$this->Cell(0,5,'','R',0,'L');
		$this->Ln(5);$this->Cell(25,5,'Straße, Nr.:','L',0,'L');$this->Cell(45,5,$user[12],'B',0,'L');$this->Cell(10);$this->Cell(25,5,'PLZ, Ort:',0,0,'L');$this->Cell(45,5,$user[13]." ".$user[14],'B',0,'L');$this->Cell(0,5,'','R',0,'L');
		$this->Ln(5);$this->Cell(25,5,'Telefon:','L',0,'L');$this->Cell(45,5,$user[15],'B',0,'L');$this->Cell(10);$this->Cell(25,5,'Telefax:',0,0,'L');$this->Cell(45,5,$user[16],'B',0,'L');$this->Cell(0,5,'','R',0,'L');
		$this->Ln(5);$this->Cell(25,5,'Mobil:','L',0,'L');$this->Cell(45,5,'','B',0,'L');$this->Cell(10);$this->Cell(25,5,'E-Mail:',0,0,'L');$this->Cell(45,5,$user[17],'B',0,'L');$this->Cell(0,5,'','R',0,'L');
		$this->Ln(4);$this->Cell(0,4,'','LBR',0,'L');
		//Adresse
		$this->SetFont('Arial','',10);
		$this->Ln(5);$this->Cell(5,4,'pro','',0,'L');$this->SetFont('Arial','I',10);$this->Cell(15,4,'Concept','',0,'L');$this->SetFont('Arial','',10);$this->Cell(0,4,'AG','',0,'L');
		$this->Ln(5);$this->Cell(25,4,'C/o „LV-Doktor-Team“',0,0,'L');
		$this->Ln(5);$this->Cell(25,4,'Ankerstrasse 3a',0,0,'L');
		$this->Ln(5);$this->Cell(25,4,'06108 Halle/Saale',0,0,'L');
		//Betreff
		$this->Ln(8);$this->SetFont('Arial','B',10);$this->Cell(55,4,'Angebot eines Prozessfinanzierungs- und Prozessbetreuungsvertrag',0,0,'L');
		$this->Ln(5);$this->SetFont('Arial','',10);$this->Cell(55,4,'Auftrag zur Prüfung von Versicherungsverträgen',0,0,'L');
		$this->Ln(5);$this->SetFont('Arial','B',10);$this->Cell(20,4,'KD-Nr.:',0,0,'L');$this->Cell(35,4,$pf,'B',0,'L');
		//Text
		$this->Ln(8);$this->Cell(45,4,'Sehr geehrte Damen und Herren,',0,0,'L');
		$this->Ln(8);$this->SetFont('Arial','',10);$this->MultiCell(0,5,'ich bin davon überzeugt, dass nach Kündigung meines Versicherungsvertrages ein erheblich höherer Rückkaufswert erzielt werden kann. Um von möglichen zusätzlichen Rückerstattungen profitieren zu können, bitte ich um Ihre Unterstützung. Ich biete Ihnen dazu den Abschluss eines Prozessfinanzierungs- und Prozessbetreuungsvertrages nach den mir bekannten und ausgehändigten PFV-Bedingungen 
		(',0,'J',0);$this->Ln(-5);$this->SetX(22);$this->SetFont('Arial','B',10);$this->Cell(25,5,'Stand: 06-2007',0,0,'L');$this->SetFont('Arial','',10);$this->Cell(0,5,') zur Anfechtung des nachstehend bezeichneten Vertrags an. Gem. § 151 S. 1BGB',0,0,'L');
		$this->Ln(5);$this->MultiCell(0,5,'verzichte ich auf den Zugang Ihrer Annahmeerklärung. Jegliche Korrespondenz führen Sie bitte über die o.a. Email-Adresse.',0,'J',0);
		
		//Block 1
		$this->Ln(5);$this->Cell(0,1,'','LTR',0,'L');
		$this->Ln(1);$this->SetFont('Arial','',10);$this->Cell(55,5,'Vertragsart:','L',0,'L');
		if($show != "blanko")$this->Cell(80,4,$showit['VART'],'B',0,'L');
		else {
			$this->Cell(75,4,'Kap.LV / Kap.RV / FondsLV / FondsRV / UPR /','',0,'L');$this->Cell(25,4,'','B',0,'L');
		}
		$this->Cell(0,5,'','R',0,'L');
		$this->Ln(5);$this->Cell(55,5,'Versicherungsgesellschaft:','L',0,'L');$this->Cell(80,4,$showit['VGES'],'B',0,'L');$this->Cell(0,5,'','R',0,'L');
		$this->Ln(5);$this->Cell(55,5,'Vertragsnummer:','L',0,'L');$this->Cell(80,4,$showit['VNUM'],'B',0,'L');$this->Cell(0,5,'','R',0,'L');
		$this->Ln(5);$this->Cell(55,5,'Versicherungsbeginn (Datum):','L',0,'L');
		if($showit['vbeginn_eingabe']!='0000-00-00')$this->Cell(35,4,$showit['vbeginn_eingabe'],'B',0,'L');else $this->Cell(35,4,'','B',0,'L');
		$this->Cell(0,5,'','R',0,'L');
		$this->Ln(5);$this->Cell(55,5,'Erstbeitrag gezahlt am:','L',0,'L');
		if($showit['verst_eingabe']!='0000-00-00')$this->Cell(35,4,$showit['verst_eingabe'],'B',0,'L');else $this->Cell(35,4,'','B',0,'L');
		$this->Cell(22,4,' in Höhe von ','',0,'L');
		if($showit['verst_betrag_eingabe']!='')$this->Cell(35,4,$showit['verst_betrag_eingabe'],'B',0,'L');else $this->Cell(35,4,'','B',0,'L');
		$this->Cell(5,4,' EUR','',0,'L');$this->Cell(0,5,'','R',0,'L');
		$this->Ln(5);$this->Cell(55,5,'Letzter Beitrag gezahlt am:','L',0,'L');
		if($showit['vletzter_eingabe']!='0000-00-00')$this->Cell(35,4,$showit['vletzter_eingabe'],'B',0,'L');else $this->Cell(35,4,'','B',0,'L');
		$this->Cell(22,4,' in Höhe von ','',0,'L');
		if($showit['vletzter_betrag_eingabe']!='')$this->Cell(35,4,$showit['vletzter_betrag_eingabe'],'B',0,'L');else $this->Cell(35,4,'','B',0,'L');
		$this->Cell(5,4,' EUR','',0,'L');$this->Cell(0,5,'','R',0,'L');
		$this->Ln(5);$this->Cell(55,5,'Erfolgte Teilzahlungen: am','L',0,'L');
		if($showit['vteil_eingabe']!='0000-00-00')$this->Cell(35,4,$showit['vteil_eingabe'],'B',0,'L');else $this->Cell(35,4,'','B',0,'L');
		$this->Cell(22,4,' in Höhe von ','',0,'L');
		if($showit['vteil_betrag_eingabe']!='')$this->Cell(35,4,$showit['vteil_betrag_eingabe'],'B',0,'L');else $this->Cell(35,4,'','B',0,'L');
		$this->Cell(5,4,' EUR','',0,'L');$this->Cell(0,5,'','R',0,'L');
		$this->Ln(5);$this->Cell(35,5,'Policedarlehen am','L',0,'L');
		if($showit['vdarlehen_eingabe']!='0000-00-00')$this->Cell(35,4,$showit['vdarlehen_eingabe'],'B',0,'L');else $this->Cell(35,4,'','B',0,'L');
		$this->Cell(22,4,' in Höhe von ','',0,'L');
		if($showit['vdarlehen_betrag_eingabe']!='')$this->Cell(35,4,$showit['vdarlehen_betrag_eingabe'],'B',0,'L');else $this->Cell(35,4,'','B',0,'L');
		$this->Cell(30,4,' in Anspruch genommen','',0,'L');$this->Cell(0,5,'','R',0,'L');
		$this->Ln(5);$this->Cell(55,5,'Policedarlehen getilgt am:','L',0,'L');
		if($showit['vdarlehen_getilgt_eingabe']!='0000-00-00')$this->Cell(35,4,$showit['vdarlehen_getilgt_eingabe'],'B',0,'L');else $this->Cell(35,4,'','B',0,'L');
		$this->Cell(0,5,'','R',0,'L');
		$this->Ln(5);$this->Cell(1,5,'','L',0,'L');
		if($showit['kuen_eingabe']=='Y'&&$show!='blanko'&&$vt!='')$this->Cell(3,3,'X',1,0,'C');
		else $this->Cell(3,3,'',1,0,'C');
		$this->Cell(2);$this->Cell(45,4,'Vertrag gekündigt am ','',0,'L');
		if($showit['kuen_datum_eingabe']!='0000-00-00')$this->Cell(25,4,$showit['kuen_datum_eingabe'],'B',0,'L');else $this->Cell(30,4,'','B',0,'L');
		$this->Cell(24,4,' akzeptiert am','',0,'L');$this->Cell(20,4,'','B',0,'L');$this->Cell(10,4,' zum','',0,'L');$this->Cell(20,4,'','B',0,'L');$this->Cell(0,5,'','R',0,'L');
		$this->Ln(5);$this->Cell(1,5,'','L',0,'L');$this->Cell(3,3,'',1,0,'C');$this->Cell(2);$this->Cell(45,4,'Vertrag abgelaufen am ','',0,'L');$this->Cell(30,4,'','B',0,'L');$this->Cell(5);if($showit['kuen_eingabe']!='Y'&&$show!='blanko'&&$vt!='')$this->Cell(3,3,'X',1,0,'C');else $this->Cell(3,3,'',1,0,'C');$this->Cell(2);$this->Cell(22,4,'Vertrag noch zu kündigen','',0,'L');$this->Cell(0,5,'','R',0,'L');
		$this->Ln(5);$this->Cell(0,1,'','LBR',0,'L');
		$this->Ln(5);if($showit['abgetreten_eingabe']!='Y'&&$show!='blanko')$this->Cell(3,3,'X',1,0,'C');else $this->Cell(3,3,'',1,0,'C');$this->Cell(34,5,'Ich versichere, dass','',0,'L');$this->SetFont('Arial','B',10);$this->Cell(87,5,'der vorstehende Vertrag weder an Dritte verpfändet','',0,'L');$this->SetFont('Arial','',10);$this->Cell(0,5,', noch durch diese','',0,'L');
		$this->Ln(5);$this->Cell(3);$this->Cell(37,5,'gepfändet ist und dass','',0,'L');$this->SetFont('Arial','B',10);$this->Cell(10,5,'keine','',0,'L');$this->SetFont('Arial','',10);$this->Cell(0,5,'Absprachen/ Vereinbarungen getroffen wurden, die sich nicht aus den ','',0,'L');
		$this->Ln(5);$this->Cell(3);$this->Cell(0,5,'beigefügten Unterlagen ergeben.','',0,'L');
		$this->Ln(5);$this->Cell(3);$this->SetFont('Arial','B',10);$this->Cell(0,5,'Oder:','',0,'L');$this->SetFont('Arial','',10);
		$this->Ln(5);if($showit['abgetreten_eingabe']=='Y'&&$show!='blanko')$this->Cell(3,3,'X',1,0,'C');else $this->Cell(3,3,'',1,0,'C');$this->Cell(50,5,'Versicherung ist abgetreten an ','',0,'L');$this->Cell(70,5,'','B',0,'L');$this->Cell(7,5,'und','',0,'L');$this->SetFont('Arial','B',10);$this->Cell(0,5,'Freigabeerklärung','',0,'L');$this->SetFont('Arial','',10);
		$this->Ln(5);$this->Cell(3);$this->Cell(55,5,'ist beigefügt','',0,'L');
		$this->SetFont('Arial','',8);$this->Cell(22,5,'(sonst Annahme','',0,'L');$this->SetFont('Arial','B',8);$this->Cell(8,5,'nicht','',0,'L');$this->SetFont('Arial','',8);$this->Cell(15,5,'möglich)','',0,'L');$this->SetFont('Arial','',10);
		$this->Ln(8);$this->MultiCell(0,5,'Ich versichere, alle vorstehenden Angaben wahrheitsgemäß und nach bestem Wissen und Gewissen gemacht zu haben. Mir ist bewusst, dass Sie mit diesen Angaben nur eine grobe Einschätzung der Erfolgsaussichten meiner Beitragserstattung vornehmen können und sich die vollständigen Informationen bei der Versicherung beschaffen.',0,'J',0);
		$this->footer_pfv();
		
		############################################################# PFV Seite 2/5 #############################################################
		$this->AddPage();
		$this->SetY(10);$this->SetAutoPageBreak(false);
		$this->MultiCell(0,5,'Ich nehme Ihre Garantie in Anspruch, dass mein Erstattungsbetrag nach erfolgreicher Auseinandersetzung mit der Gesellschaft mindestens das Doppelte der von mir gezahlten Prüfgebühr betragen wird. Deshalb verzichte ich auf ein Vorprüfergebnis',0,'J',0);
		$this->Ln(0);$this->Cell(22,5,'und bitte Sie,',0,0,'L');$this->SetFont('Arial','B',10);$this->Cell(79,5,'eine unterschriebene Rechtsanwaltsvollmacht',0,0,'L');$this->SetFont('Arial','',10);$this->Cell(0,5,'beizufügen sowie:',0,0,'L');
		$this->Ln(5);if($showit['abrechschr_eingabe']=='Y'&&$show!='blanko')$this->Cell(3,3,'X',1,0,'C');else $this->Cell(3,3,'',1,0,'C');$this->Cell(0,5,'das Abrechnungsschreiben meines gekündigten Vertrages*',0,0,'L');
		$this->Ln(5);if($showit['origvs_eingabe']=='Y'&&$show!='blanko')$this->Cell(3,3,'X',1,0,'C');else $this->Cell(3,3,'',1,0,'C');$this->Cell(0,5,'folgende zur Kündigung erforderliche Unterlagen:',0,0,'L');
		$this->Ln(5);$this->Cell(3);$this->Cell(0,5,'Originalversicherungsschein / oder Verlusterklärung*',0,0,'L');
		$this->Ln(5);if($showit['fstellda_eingabe']=='Y'&&$show!='blanko')$this->Cell(3,3,'X',1,0,'C');else $this->Cell(3,3,'',1,0,'C');$this->Cell(61,5,'Freistellungsauftrag für Kapitalerträge*',0,0,'L');$this->SetFont('Arial','',8);$this->Cell(0,5,'(nachträglich eingehende werden nicht berücksichtigt)','',0,'L');$this->SetFont('Arial','',10);
		$this->Ln(4);$this->Cell(0,5,'* nicht Zutreffendes bitte streichen.',0,0,'L');
		$this->Ln(5);$this->MultiCell(0,5,'Alle Unterlagen, die ich zu diesem Versicherungsvertrag noch vorliegen habe, stelle ich Ihnen auf Anfrage zur Verfügung. Ich werde diese aufbewahren, bis das Verfahren beendet wurde und Sie mich über die Vernichtung der Unterlagen informieren.',0,'J',0);
		
		//Zustimmung zur Abbuchung
		$this->Ln(5);$this->SetFont('Arial','B',10);$this->Cell(0,5,'Zustimmung zur Abbuchung','LTR',0,'L',1);
		$this->Ln(5);$this->SetFont('Arial','',10);$this->Cell(82,8,'Abbuchung der Bearbeitungsgebühr von 150,- EUR ','L',0,'L');$this->SetFont('Arial','B',10);$this->Cell(20,8,'je Vertrag',0,0,'L');$this->SetFont('Arial','',8);$this->Cell(45,8,'(bei bereits gekündigten Verträgen)','',0,'L');$this->SetFont('Arial','',10);$this->Cell(0,8,'von meinem','R',0,'L');
		$this->Ln(6);$this->Cell(13,8,'Konto: ','L',0,'L');$this->Cell(37,5,$user[29],'B',0,'L');$this->Cell(10,8,'BLZ: ',0,0,'L');$this->Cell(40,5,$user[30],'B',0,'L');$this->Cell(23,8,'Kreditinstitut:',0,0,'L');$this->Cell(38,5,$user[28],'B',0,'L');$this->Cell(0,8,'','R',0,'L');
		$this->Ln(6);$this->Cell(60,8,'Gewünschter Abbuchungszeitpunkt:','L',0,'L');$this->Cell(35,5,'','B',0,'L');$this->Cell(0,8,' des Monats','R',0,'L');
		$this->Ln(6);$this->Cell(40,8,'Kontoinhaber – Name:','L',0,'L');$this->Cell(82,5,$user[77],'B',0,'L');$this->Cell(0,8,'','R',0,'L');
		$this->Ln(3);$this->Cell(0,5,'','LBR',0,'L');
		$this->Ln(8);$this->SetFont('Arial','B',10);$this->MultiCell(0,5,'Soweit ich keine anderweitigen Verfügungen treffe, bitte ich Sie, die künftige Erstattung auf oben genanntes Konto vorzunehmen.',0,'J',0);
		//Auskunftsvollmacht
		if($partner[2]=='Y'){
			$this->Ln(2);$this->SetFont('Arial','B',10);$this->Cell(52,5,'Vollmacht:',0,0,'L');
			$this->Ln(5);$this->SetFont('Arial','',10);$this->MultiCell(0,5,'Ich ermächtige Sie, Herr/Frau/Firma '.$vollmacht.' auf Nachfrage Auskunft über den Verfahrensstand zu geben; zuviel erhaltene Unterlagen senden Sie bitte über diesen an mich zurück.',0,'J',0);
		}
		else $this->Ln(0);
		$this->Ln(2);$this->SetFont('Arial','B',10);$this->MultiCell(0,5,'Zudem beauftrage ich Sie, umgehend einen Rechtsanwalt mit der Durchsetzung meiner Interessen sowie der sofortigen Kündigung des vorstehenden Vertrages zu beauftragen.',0,'J',0);
		$this->Ln(2);$this->SetFont('Arial','B',10);$this->MultiCell(0,5,'Leiten Sie alle erforderlichen Maßnahmen zur Durchsetzung meines Anspruchs ein. Sollte der Versicherungsvertrag noch nicht gekündigt sein, bitte ich Sie, die Kündigung umgehend zu veranlassen, die dafür entstehende Gebühr i.H.v. 87,50 € sowie die Bearbeitungsgebühr i.H.v. 150,00 € vom Rückkaufswert einzubehalten.',0,'J',0);
		$this->Ln(2);$this->SetFont('Arial','B',10);$this->MultiCell(0,5,'Anbei gebe ich Ihnen eine Blankovollmacht, die Sie an den Rechtsanwalt Ihrer Wahl aushändigen können. Mir ist bewusst, dass kein Vertrags- bzw. Mandatsverhältnis zwischen mir und dem von Ihnen beauftragten Rechtsanwalt zustande kommt.',0,'J',0);
		//Unterschrift 1
		$this->Ln(5);$this->SetFont('Arial','',10);$this->Cell(52,5,'Mit freundlichen Grüßen',0,0,'L');
		$this->Ln(8);$this->Cell(50,5,'','B',0,'L');$this->Cell(50);$this->Cell(50,5,'','B',0,'L');
		$this->Ln(5);$this->Cell(50,5,'Ort / Datum',0,0,'L');$this->Cell(50);$this->Cell(50,5,'Unterschrift',0,0,'L');
		//Unterschrift 2
		$this->Ln(7);$this->SetFont('Arial','B',8);$this->MultiCell(0,5,'Rechtsfolgen: Ich bin mir darüber im Klaren, dass durch die proConcept AG und deren Rechtsanwälte lediglich eine Prüfung der Anfechtbarkeit meiner Verträge vorgenommen wird. Es erfolgt keinerlei Überprüfung der damit verbundenen Absicherungen und durch die Kündigung ggf. entstehenden Versorgungslücken. Die entstehenden Versorgungslücken sind gewollt, bzw. werden anderweitig wieder geschlossen, was aber nicht von der proConcept AG oder deren Rechtsanwälte überwacht bzw. veranlasst wird. Insofern stelle ich die proConcept AG und deren Rechtsanwälte von sämtlichen diesbezüglich evtl. entstehenden Verpflichtungen und Ansprüchen frei. Ich gebe proConcept AG ausdrücklich meine Einwilligung in die Erhebung, Verarbeitung und Nutzung meiner personenbezogenen Daten zum Zweck der Geltendmachung, Ausübung oder Verteidigung rechtlicher Ansprüche vor Gericht sowie in Einzel-, Sammel-, Klage- und Vergleichsverfahren über von proConcept AG beauftragte Rechtsanwälte. Weiterhin bin ich explizit damit einverstanden, dass meine für den Vertragszweck objektiv erforderlichen personenbezogenen Daten über Partner der proConcept AG  und deren beauftragte Abwicklungsgesellschaft proConcept GmbH erhoben bzw. übermittelt werden.','LTR','J',0);
		$this->Ln(0);$this->Cell(1,6,'','L',0,'L');$this->Cell(50,6,'','B',0,'L');$this->Cell(50);$this->Cell(50,6,'','B',0,'L');$this->Cell(19,6,'','R',0,'L');
		$this->Ln(6);$this->SetFont('Arial','',8);$this->Cell(50,5,'Ort / Datum','LB',0,'L');$this->Cell(50,5,'','B',0,'L');$this->Cell(0,5,'Unterschrift','BR',0,'L');
		$this->footer_pfv();
		############################################################# PFV Seite 3/5 VOLLMACHT #############################################################
		$this->AddPage();$this->SetAutoPageBreak(true);
		$this->SetY(15);$this->SetFont('Arial','B',10);$this->Cell(0,5,'VOLLMACHT','',0,'L');
		$this->Ln(10);$this->SetFont('Arial','',10);$this->Cell(0,5,'Der Unterzeichner/die Unterzeichnerin erteilt hiermit den Rechtsanwälten','',0,'L');
		$this->Ln(65);$this->SetFont('Arial','B',10);$this->Cell(25,5,'VOLLMACHT','',0,'L');$this->SetFont('Arial','',10);$this->Cell(0,5,'in der Sache:','',0,'L');
		$this->Ln(10);$this->Cell(100,5,$user[10].' '.$user[11].' / '.$showit['VGES'],'B',0,'L');
		$this->Ln(10);$this->Cell(0,5,'Gegenstand des Mandats: Prüfung/Kündigung von Verträgen','',0,'L');
		$this->Ln(10);$this->Cell(0,5,'Die Vollmacht umfasst die Befugnis','',0,'L');
		$this->Ln(5);$this->Cell(6,5,'zur','',0,'L');$this->SetFont('Arial','B',10);$this->Cell(28,5,'Prozessführung','',0,'L');$this->SetFont('Arial','',10);$this->Cell(25,5,'(u.a. nach §§ 81 ff. ZPO) einschließlich der Befugnis der Erhebung und Zurücknahme','',0,'L');
		$this->Ln(5);$this->Cell(0,5,'von Widerklagen;','',0,'L');
		$this->Ln(5);$this->Cell(28,5,'zur Vertretung in','',0,'L');$this->SetFont('Arial','B',10);$this->Cell(110,5,'sonstigen Verfahren und bei außergerichtlichen Verhandlungen','',0,'L');$this->SetFont('Arial','',10);$this->Cell(0,5,'aller Art','',0,'L');
		$this->Ln(5);$this->Cell(58,5,'zur Begründung und Aufhebung von','',0,'L');$this->SetFont('Arial','B',10);$this->Cell(38,5,'Vertragsverhältnissen','',0,'L');$this->SetFont('Arial','',10);$this->Cell(0,5,'und zur Abgabe und Entgegennahme von','',0,'L');
		$this->Ln(5);$this->Cell(0,5,'einseitigen Willenserklärungen (z.B. Kündigungen).','',0,'L');
		$this->Ln(10);$this->Cell(100,5,'Die Vollmacht gilt für alle Instanzen und erstreckt sich auch auf','',0,'L');$this->SetFont('Arial','B',10);$this->Cell(48,5,'Neben- und Folgeverfahren','',0,'L');$this->SetFont('Arial','',10);$this->Cell(0,5,'aller Art (z.B.','',0,'L');
		$this->Ln(5);$this->SetFont('Arial','B',10);$this->Cell(12,5,'Arrest','',0,'L');$this->SetFont('Arial','',10);$this->Cell(8,5,'und','',0,'L');$this->SetFont('Arial','B',10);$this->Cell(0,5,'einstweilige Verfügung, Kostenfestsetzungs-, Zwangsvollstreckungs-, Interventions-,','',0,'L');
		$this->Ln(5);$this->Cell(0,5,'Zwangsversteigerungs-, Zwangsverwaltungs- und Hinterlegungsverfahren sowie Insolvenz- und','',0,'L');
		$this->Ln(5);$this->Cell(35,5,'Vergleichsverfahren','',0,'L');$this->SetFont('Arial','',10);$this->Cell(0,5,'über das Vermögen des Gegners). Sie umfasst insbesondere die Befugnis,','',0,'L');
		$this->Ln(5);$this->MultiCell(0,5,'Zustellungen zu bewirken und entgegenzunehmen, die Vollmacht ganz oder teilweise auf andere zu übertragen (Untervollmacht), Rechtsmittel einzulegen, zurückzunehmen oder auf sie zu verzichten, den Rechtsstreit oder außergerichtliche Verhandlungen durch Vergleich, Verzicht oder Anerkenntnis zu erledigen, Geld, Wertsachen und Urkunden, insbesondere auch den Streitgegenstand und die von dem Gegner, von der Justizkasse oder von sonstigen Stellen zu erstattenden Beträge entgegenzunehmen sowie Akteneinsicht zu nehmen.',0,'J',0);
		//Unterschrift
		$this->Ln(15);$this->Cell(50,10,'','B',0,'L');$this->Cell(50);$this->Cell(50,10,'','B',0,'L');$this->Cell(19,10,'',0,0,'L');
		$this->Ln(12);$this->SetFont('Arial','',10);$this->Cell(50,5,'Ort / Datum',0,0,'L');$this->Cell(50,5,'',0,0,'L');$this->Cell(0,5,'Unterschrift',0,0,'L');
		############################################################# Leerseite #############################################################
		$this->AddPage();	
		############################################################# PFV Seite 4/5 AUSZAHLUNGSAUFTRAG #############################################################
		if($user[29]!=$user[103]){
			$this->AddPage();
			$this->SetY(5);
			//Persönliche Daten Kunde (Auftraggeber)
			$this->Ln(7);$this->SetFont('Arial','B',8);$this->Cell(0,4,'Persönliche Daten Kunde (Auftraggeber)','LTR',0,'L',1);$this->SetFont('Arial','',8);
			$this->Ln(4);$this->Cell(25,5,'Name:','L',0,'L');$this->Cell(45,5,$user[11],'B',0,'L');$this->Cell(10);$this->Cell(25,5,'Vorname:',0,0,'L');$this->Cell(45,5,$user[10],'B',0,'L');$this->Cell(0,5,'','R',0,'L');
			$this->Ln(5);$this->Cell(25,5,'Straße, Nr.:','L',0,'L');$this->Cell(45,5,$user[12],'B',0,'L');$this->Cell(10);$this->Cell(25,5,'PLZ, Ort:',0,0,'L');$this->Cell(45,5,$user[13]." ".$user[14],'B',0,'L');$this->Cell(0,5,'','R',0,'L');
			$this->Ln(5);$this->Cell(25,5,'Telefon:','L',0,'L');$this->Cell(45,5,$user[15],'B',0,'L');$this->Cell(10);$this->Cell(25,5,'Telefax:',0,0,'L');$this->Cell(45,5,$user[16],'B',0,'L');$this->Cell(0,5,'','R',0,'L');
			$this->Ln(5);$this->Cell(25,5,'Mobil:','L',0,'L');$this->Cell(45,5,'','B',0,'L');$this->Cell(10);$this->Cell(25,5,'E-Mail:',0,0,'L');$this->Cell(45,5,$user[17],'B',0,'L');$this->Cell(0,5,'','R',0,'L');
			$this->Ln(4);$this->Cell(0,4,'','LBR',0,'L');
			$this->Ln(8);$this->SetFont('Arial','B',10);$this->Cell(0,5,'AUSZAHLUNGSAUFTRAG','',0,'L');
			//Adresse
			$this->SetFont('Arial','',10);
			$this->Ln(20);$this->Cell(5,4,'pro','',0,'L');$this->SetFont('Arial','I',10);$this->Cell(15,4,'Concept','',0,'L');$this->SetFont('Arial','',10);$this->Cell(0,4,'AG','',0,'L');
			$this->Ln(5);$this->Cell(25,4,'C/o „LV-Doktor-Team“',0,0,'L');
			$this->Ln(5);$this->Cell(25,4,'Ankerstrasse 3a',0,0,'L');
			$this->Ln(5);$this->Cell(25,4,'06108 Halle/Saale',0,0,'L');
			$this->Ln(25);$this->Cell(45,4,'Sehr geehrte Damen und Herren,',0,0,'L');
			$this->Ln(10);$this->SetFont('Arial','',10);$this->MultiCell(0,5,'abweichend zu der in der Beauftragung angegebenen Bankverbindung möchte ich Sie bitten, die mir zustehenden Rückkaufswerte vom Anderkonto Ihres beauftragten Rechtsanwaltes',0,'J',0);
			$this->Ln(10);$this->Cell(10);$this->Cell(3,3,'',1,0,'C');$this->Cell(10);$this->Cell(30,5,'einen Betrag von',0,0,'L');$this->Cell(30,5,'','B',0,'L');$this->Cell(0,5,'Euro',0,0,'L');
			$this->Ln(10);$this->Cell(10);$this->Cell(3,3,'',1,0,'C');$this->Cell(10);$this->Cell(30,5,'den gesamten Rückkaufswert abzüglich der entstandenen Kosten',0,0,'L');
			$this->Ln(10);$this->Cell(15,5,'an',0,0,'L');$this->Cell(80,4,$user[101],'B',0,'L');
			$this->Ln(8);$this->Cell(15,5,'bei der',0,0,'L');$this->Cell(40,4,$user[102],'B',0,'L');$this->Cell(8,5,'Blz',0,0,'L');$this->Cell(40,4,$user[104],'B',0,'L');$this->Cell(18,5,'Konto Nr.',0,0,'L');$this->Cell(40,4,$user[103],'B',0,'L');
			$this->Ln(8);$this->Cell(35,5,'Verwendungszweck',0,0,'L');$this->Cell(80,4,'','B',0,'L');
			$this->Ln(8);$this->Cell(0,5,'zu meiner',0,0,'L');
			$this->Ln(5);$this->Cell(28,5,'Zeichnung vom',0,0,'L');$this->Cell(40,4,'','B',0,'L');$this->Cell(8,5,'Nr.',0,0,'L');$this->Cell(40,4,'','B',0,'L');
			$this->Ln(8);$this->Cell(0,5,'zu überweisen.',0,0,'L');
			$this->Ln(10);$this->MultiCell(0,5,'Den darüber hinausgehenden Betrag bitte ich auf mein Ihnen bereits benanntes Konto anzuweisen.',0,'J',0);
			$this->Ln(10);$this->Cell(0,5,'Mit freundlichen Grüßen',0,0,'L');
			//Unterschrift
			$this->Ln(15);$this->Cell(50,10,'','B',0,'L');$this->Cell(50);$this->Cell(50,10,'','B',0,'L');$this->Cell(19,10,'',0,0,'L');
			$this->Ln(12);$this->SetFont('Arial','',10);$this->Cell(50,5,'Ort / Datum',0,0,'L');$this->Cell(50,5,'',0,0,'L');$this->Cell(0,5,'Unterschrift',0,0,'L');
			$this->footer_pfv();
		}
		$this->AddPage();
		$this->SetY(5);
		//Persönliche Daten Kunde (Auftraggeber)
		$this->Ln(7);$this->SetFont('Arial','B',8);$this->Cell(0,4,'Persönliche Daten Kunde (Auftraggeber)','LTR',0,'L',1);$this->SetFont('Arial','',8);
		$this->Ln(4);$this->Cell(25,5,'Name:','L',0,'L');$this->Cell(45,5,$user[11],'B',0,'L');$this->Cell(10);$this->Cell(25,5,'Vorname:',0,0,'L');$this->Cell(45,5,$user[10],'B',0,'L');$this->Cell(0,5,'','R',0,'L');
		$this->Ln(5);$this->Cell(25,5,'Straße, Nr.:','L',0,'L');$this->Cell(45,5,$user[12],'B',0,'L');$this->Cell(10);$this->Cell(25,5,'PLZ, Ort:',0,0,'L');$this->Cell(45,5,$user[13]." ".$user[14],'B',0,'L');$this->Cell(0,5,'','R',0,'L');
		$this->Ln(5);$this->Cell(25,5,'Telefon:','L',0,'L');$this->Cell(45,5,$user[15],'B',0,'L');$this->Cell(10);$this->Cell(25,5,'Telefax:',0,0,'L');$this->Cell(45,5,$user[16],'B',0,'L');$this->Cell(0,5,'','R',0,'L');
		$this->Ln(5);$this->Cell(25,5,'Mobil:','L',0,'L');$this->Cell(45,5,'','B',0,'L');$this->Cell(10);$this->Cell(25,5,'E-Mail:',0,0,'L');$this->Cell(45,5,$user[17],'B',0,'L');$this->Cell(0,5,'','R',0,'L');
		$this->Ln(4);$this->Cell(0,4,'','LBR',0,'L');
		$this->Ln(8);$this->SetFont('Arial','B',10);$this->Cell(0,5,'AUSZAHLUNGSAUFTRAG','',0,'L');
		//Adresse
		$this->SetFont('Arial','',10);
		$this->Ln(20);$this->Cell(5,4,'pro','',0,'L');$this->SetFont('Arial','I',10);$this->Cell(15,4,'Concept','',0,'L');$this->SetFont('Arial','',10);$this->Cell(0,4,'AG','',0,'L');
		$this->Ln(5);$this->Cell(25,4,'C/o „LV-Doktor-Team“',0,0,'L');
		$this->Ln(5);$this->Cell(25,4,'Ankerstrasse 3a',0,0,'L');
		$this->Ln(5);$this->Cell(25,4,'06108 Halle/Saale',0,0,'L');
		//Text
		if($partner[86]=='11403'){
			$empfaeger=$empfaenger_11403;
			$bank=$bank_11403;
			$blz=$blz_11403;
			$kto=$kto_11403;
			$zweck=$zweck_11403;
		}
		else {
			$empfaeger='';
			$bank='';
			$blz='';
			$kto='';
			$zweck='';
		}
		$this->Ln(25);$this->Cell(45,4,'Sehr geehrte Damen und Herren,',0,0,'L');
		$this->Ln(10);$this->SetFont('Arial','',10);$this->MultiCell(0,5,'abweichend zu der in der Beauftragung angegebenen Bankverbindung möchte ich Sie bitten, die mir zustehenden Rückkaufswerte vom Anderkonto Ihres beauftragten Rechtsanwaltes',0,'J',0);
		$this->Ln(10);$this->Cell(10);$this->Cell(3,3,'',1,0,'C');$this->Cell(10);$this->Cell(30,5,'einen Betrag von',0,0,'L');$this->Cell(30,5,'','B',0,'L');$this->Cell(0,5,'Euro',0,0,'L');
		$this->Ln(10);$this->Cell(10);$this->Cell(3,3,'',1,0,'C');$this->Cell(10);$this->Cell(30,5,'den gesamten Rückkaufswert abzüglich der entstandenen Kosten',0,0,'L');
		$this->Ln(10);$this->Cell(15,5,'an die',0,0,'L');$this->Cell(80,4,$empfaeger,'B',0,'L');
		$this->Ln(8);$this->Cell(15,5,'bei der',0,0,'L');$this->Cell(40,4,$bank,'B',0,'L');$this->Cell(8,5,'Blz',0,0,'L');$this->Cell(40,4,$blz,'B',0,'L');$this->Cell(18,5,'Konto Nr.',0,0,'L');$this->Cell(40,4,$kto,'B',0,'L');
		$this->Ln(8);$this->Cell(35,5,'Verwendungszweck',0,0,'L');$this->Cell(80,4,$zweck,'B',0,'L');
		$this->Ln(8);$this->Cell(0,5,'zu meiner',0,0,'L');
		$this->Ln(5);$this->Cell(28,5,'Zeichnung vom',0,0,'L');$this->Cell(40,4,'','B',0,'L');$this->Cell(8,5,'Nr.',0,0,'L');$this->Cell(40,4,'','B',0,'L');
		$this->Ln(8);$this->Cell(0,5,'zu überweisen.',0,0,'L');
		$this->Ln(10);$this->MultiCell(0,5,'Den darüber hinausgehenden Betrag bitte ich auf mein Ihnen bereits benanntes Konto anzuweisen.',0,'J',0);
		$this->Ln(10);$this->Cell(0,5,'Mit freundlichen Grüßen',0,0,'L');
		//Unterschrift
		$this->Ln(15);$this->Cell(50,10,'','B',0,'L');$this->Cell(50);$this->Cell(50,10,'','B',0,'L');$this->Cell(19,10,'',0,0,'L');
		$this->Ln(12);$this->SetFont('Arial','',10);$this->Cell(50,5,'Ort / Datum',0,0,'L');$this->Cell(50,5,'',0,0,'L');$this->Cell(0,5,'Unterschrift',0,0,'L');
		$this->footer_pfv();
		############################################################# Leerseite #############################################################
		$this->AddPage();
		############################################################# PFV Seite 6/7 VERLUSTERKLÄRUNG #############################################################
		$this->AddPage();
		$this->SetY(15);$this->SetFont('Arial','B',10);$this->Cell(0,5,'VERLUSTERKLÄRUNG','',0,'L');
		$this->Ln(65);$this->SetFont('Arial','',10);$this->Cell(25,5,'Lebensversicherung Nr.: '.$showit['VNUM'],'',0,'L');
		$this->Ln(10);$this->Cell(100,5,"Versicherte Person: ".$user[10].' '.$user[11],'',0,'L');
		$this->Ln(10);$this->MultiCell(0,5,'Nach eingehender Nachforschung erkläre ich, dass ich den Versicherungsschein nicht erhalten habe/nicht mehr besitze. (Zutreffendes Unterstreichen)',0,'J',0); 
		$this->Ln(5);$this->Ln(5);$this->MultiCell(0,5,'Ich versichere, dass kein unwiderrufliches Bezugsrecht vorliegt, die Rechte und Ansprüche aus diesem Versicherungsvertrag weder verpfändet, abgetreten noch gepfändet sind; auch habe ich den Versicherungsschein nicht in Verbindung mit einem sonstigen Leistungsversprechen weitergegeben. ',0,'J',0);
		$this->Ln(5);$this->Ln(5);$this->MultiCell(0,5,'Für den Fall, dass Sie aus dem genannten Versicherungsvertrag von einem berechtigten Dritten mit Erfolg in Anspruch genommen werden sollten, verpflichte ich mich Ihnen gegenüber zur Zahlung. Finde ich den Versicherungsschein wieder oder sollte er noch nachträglich in meinen Besitz gelangen, so verpflichte ich mich, diesen unverzüglich an Sie weiterzuleiten. ',0,'J',0);
		$this->Ln(5);$this->Ln(5);$this->MultiCell(0,5,'Bitte lassen Sie durch Ihren beauftragten Rechtsanwalt eine entsprechende Verlusterklärung abgeben.',0,'J',0);
		//Unterschrift
		$this->Ln(15);$this->Cell(50,10,'','B',0,'L');$this->Cell(50);$this->Cell(50,10,'','B',0,'L');$this->Cell(19,10,'',0,0,'L');
		$this->Ln(12);$this->SetFont('Arial','',10);$this->Cell(50,5,'Ort / Datum',0,0,'L');$this->Cell(50,5,'',0,0,'L');$this->Cell(0,5,'Unterschrift des Versicherungsnehmers',0,0,'L');
		

		
	}
	
	function get_page_1($data){
		
		$this->AddPage();
		$font_size=9;
		$cell_height=4;
		$font_face='Arial';
		$this->SetFont($font_face,'',$font_size);
		
		$this->SetFont($font_face,'B',15);
		$this->Cell(30,$cell_height,'Prüfauftrag  „LV-Doktor“');
		
		
		$this->Ln();
		$this->Ln();
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(20,$cell_height,'Name:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(60,$cell_height,$data['lvcheck_name']);
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(20,$cell_height,'Telefon:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(40,$cell_height,$data['lvcheck_tel']);
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(15,$cell_height,'ID:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(30,$cell_height,$data['partner_id']);
		
		$this->Ln();
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(20,$cell_height,'Vorname:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(60,$cell_height,$data['lvcheck_firstname']);
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(20,$cell_height,'Telefax:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(40,$cell_height,$data['lvcheck_fax']);
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(15,$cell_height,'VT-Nr.:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(30,$cell_height,$data['lvcheck_vt']);
		
		$this->Ln();
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(20,$cell_height,'Strasse Nr.:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(60,$cell_height,$data['lvcheck_adress']);
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(20,$cell_height,'Funk:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(40,$cell_height,$data['lvcheck_handy']);
		
		$this->Ln();
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(20,$cell_height,'PLZ / Ort:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(60,$cell_height,$data['lvcheck_zip'].' '.$data['lvcheck_city']);
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(20,$cell_height,'Email:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(40,$cell_height,$data['lvcheck_email']);
		
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		
		$this->Cell(30,$cell_height,'Pro Concept AG');
		$this->Ln();
		$this->Cell(30,$cell_height,'C/o „LV-Doktor-Team“');
		$this->Ln();
		$this->Cell(30,$cell_height,'Ankerstrasse 3a');
		$this->Ln();
		$this->Cell(30,$cell_height,'06108 Halle');
		
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(0,$cell_height,'Sehr geehrte Damen und Herren');
		$this->SetFont($font_face,'',$font_size);
		$this->Ln();
		$this->MultiCell(0,$cell_height,'Ich glaube, bei meinem Versicherungsvertrag ist ein erheblich höherer Rückkaufswert möglich. Um von möglichen zusätzlichen Rückerstattungen profitieren zu können, bitte ich um Ihre Unterstützung. Ich biete Ihnen dazu den Abschluss eines Prozessfinanzierungs- und Prozessbetreuungsvertrages nach den mir bekannten und ausgehändigten PFV- Bedingungen ( Stand: 06-2007) zur Anfechtung des nachstehend bezeichneten Vertrags an. Gem. § 151 S. 1 BGB verzichte ich auf den Zugang Ihrer Annahmeerklärung. Jegliche Korrespondenz führen Sie bitte über die o.a. Kontaktdaten.');
		
		$this->Ln();
		$this->Ln();
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(50,$cell_height,'Versicherungsart:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(60,$cell_height,$GLOBALS['dbapi']->get_lvcheck_type_name($data['lvcheck_contract_type']).' / abgelaufener / gekündigter Vertrag');
		$this->Ln();
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(50,$cell_height,'Versicherungsgesellschaft:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(60,$cell_height,$GLOBALS['dbapi']->get_lvcheck_company_name($data['lvcheck_contract_company']));
		$this->Ln();
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(50,$cell_height,'Vertragsnummer:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(60,$cell_height,$data['lvcheck_contract_nr']);
		
		$this->Ln();
		$this->Ln();
		
		/*
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(50,$cell_height,'Versicherungsbeginn:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(50,$cell_height,$data['lvcheck_contract_start']);
		$this->Ln();
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(50,$cell_height,'Erstbeitrag gezahlt am:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(50,$cell_height,$data['lvcheck_contract_option_1']);
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(40,$cell_height,'in Höhe von:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(50,$cell_height,$data['lvcheck_contract_option_2'].' EUR');
		$this->Ln();
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(50,$cell_height,'Letzter Beitrag gezahlt am:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(50,$cell_height,$data['lvcheck_contract_option_3']);
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(40,$cell_height,'in Höhe von:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(50,$cell_height,$data['lvcheck_contract_option_4'].' EUR');
		$this->Ln();
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(50,$cell_height,'Erfolgte Teilzahlungen am:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(50,$cell_height,$data['lvcheck_contract_option_5']);
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(40,$cell_height,'in Höhe von:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(50,$cell_height,$data['lvcheck_contract_option_6'].' EUR');
		$this->Ln();
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(50,$cell_height,'Policedarlehen am:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(50,$cell_height,$data['lvcheck_contract_option_7']);
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(40,$cell_height,'in Höhe von:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(50,$cell_height,$data['lvcheck_contract_option_8'].' erhalten');
		$this->Ln();
		$this->Ln();
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(50,$cell_height,'Policedarlehen getilgt am:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(50,$cell_height,$data['lvcheck_contract_option_9'].' EUR');
		$this->Ln();
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(50,$cell_height,'Vertrag gekündigt am:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(50,$cell_height,$data['lvcheck_contract_option_10']);
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(40,$cell_height,'akzeptiert am:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(50,$cell_height,$data['lvcheck_contract_option_11'].' zum: '.$data['lvcheck_contract_option_12']);
		$this->Ln();
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(50,$cell_height,'Vertrag abgelaufen am:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(50,$cell_height,$data['lvcheck_contract_end']);
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(40,$cell_height,'Auszahlungssumme:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(50,$cell_height,$data['lvcheck_contract_option_14'].' EUR');
		
		$this->Ln();
		$this->Ln();
		
		*/
		
		$this->MultiCell(0,$cell_height,'Ihre Garantie, dass mein Erstattungsbetrag nach erfolgreicher Auseinandersetzung mit der Gesellschaft mindestens das Doppelte der von mir gezahlten Bearbeitungsgebühr betragen wird, nehme ich in Anspruch und erteile Ihnen hiermit die Zustimmung zur Abbuchung der Bearbeitungsgebühr.');
		
		$this->Ln();
		$this->Ln();
		
		$this->SetFont($font_face,'B',$font_size);
		$this->MultiCell(0,$cell_height,'Zustimmung zur Abbuchung');
		$this->Ln();
		$this->SetFont($font_face,'',$font_size);
		$this->MultiCell(0,$cell_height,'Abbuchung der Bearbeitungsgebühr von 150 € pro Vertrag von meinem:');
		$this->Ln();
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(30,$cell_height,'Konto:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(60,$cell_height,$data['lvcheck_knr']);
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(30,$cell_height,'BLZ:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(60,$cell_height,$data['lvcheck_blz']);
		$this->Ln();
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(30,$cell_height,'Bank:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(60,$cell_height,$data['lvcheck_bank']);
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(30,$cell_height,'Inhaber:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(60,$cell_height,$data['lvcheck_inh']);
		$this->Ln();
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(60,$cell_height,'Gewünschter Abbuchungszeitpunkt:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(60,$cell_height,$data['lvcheck_bank_date'].' des Monats');
		$this->Ln();
		$this->Ln();
		
		$this->MultiCell(0,$cell_height,'Soweit ich keine anderweitigen Verfügungen treffe, bitte ich Sie die künftige Erstattung auf oben genanntes Konto vorzunehmen.');
		
		$this->Ln();
		$this->Ln();
		
		$this->MultiCell(0,$cell_height,'Hiermit beauftrage ich Sie alle erforderlichen Maßnahmen zur Durchsetzung meines Anspruchs einzuleiten. Ich beauftrage Sie, einen Rechtsanwalt Ihrer Wahl mit der Durchsetzung meiner Interessen zu beauftragen und füge dazu eine Vollmacht bei. Mir ist bewusst, dass dadurch zwischen mir und dem Rechtsanwalt kein Vertrags- bzw. Mandantenverhältnis zustande kommt.');
		
		$this->Ln();
		$this->Ln();
		
		$this->SetFont($font_face,'B',$font_size);
		$this->MultiCell(0,$cell_height,'Zur Bearbeitung stelle ich Ihnen folgendes Dokument zur Verfügung');
		$this->SetFont($font_face,'',$font_size);
		
		$this->Ln();
		
		if($data['lvcheck_contract_att_1']==true){
			$this->MultiCell(0,$cell_height,'Abrechnungsschreiben');
		}
		
		if($data['lvcheck_contract_att_2']==true){
			$this->MultiCell(0,$cell_height,'Kontoauszug mit Auszahlungsbetrag');
		}
		
		if($data['lvcheck_contract_att_3']==true){
			$this->MultiCell(0,$cell_height,'Versicherungsdokument');
		}
		
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(60,$cell_height,'Datum / Ort');
		$this->Cell(60,$cell_height,'Unterschrift');
		
		// Vollmacht
		
		$this->AddPage();
		$font_size=9;
		$cell_height=4;
		$font_face='Arial';
		$this->SetFont($font_face,'',$font_size);
		
		$this->SetFont($font_face,'B',15);
		$this->Cell(30,$cell_height,'Vollmacht');
		$this->SetFont($font_face,'',$font_size);
		
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		
		$this->MultiCell(0,$cell_height,'Der Unterzeichner/die Unterzeichnerin erteilt hiermit den Rechtsanwälten');
		
		
		
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		
		$this->MultiCell(0,$cell_height,'Vollmacht in der Sache:');
		
		$this->Ln();
		$this->Ln();
		
		$this->MultiCell(0,$cell_height,$data['lvcheck_firstname'].' '.$data['lvcheck_name'].' ./. '.$GLOBALS['dbapi']->get_lvcheck_company_name($data['lvcheck_contract_company']));
		
		$this->Ln();
		$this->Ln();
		
		$this->MultiCell(0,$cell_height,'Gegenstand des Mandats:  Prüfung/Kündigung von Verträgen');
		
		$this->Ln();
		$this->Ln();
		
		$this->MultiCell(0,$cell_height,'Die Vollmacht umfasst die Befugnis zur Prozessführung (u.a. nach §§ 81 ff. ZPO) einschließlich der Befugnis der Erhebung und Zurücknahme von Widerklagen; zur Vertretung in sonstigen Verfahren und bei außergerichtlichen Verhandlungen aller Art zur Begründung und Aufhebung von Vertragsverhältnissen und zur Abgabe und Entgegennahme von einseitigen Willenserklärungen (z.B. Kündigungen). Die Vollmacht gilt für alle Instanzen und erstreckt sich auch auf Neben- und Folgeverfahren aller Art (z.B. Arrest und einstweilige Verfügung, Kostenfestsetzungs-, Zwangsvollstreckungs-, Interventions-, Zwangsversteigerungs-, Zwangsverwaltungs- und Hinterlegungsverfahren sowie Insolvenz- und Vergleichsverfahren über das Vermögen des Gegners). Sie umfasst insbesondere die Befugnis, Zustellungen zu bewirken und entgegenzunehmen, die Vollmacht ganz oder teilweise auf andere zu übertragen (Untervollmacht), Rechtsmittel einzulegen, zurückzunehmen oder auf sie zu verzichten, den Rechtsstreit oder außergerichtliche Verhandlungen durch Vergleich, Verzicht oder Anerkenntnis zu erledigen, Geld, Wertsachen und Urkunden, insbesondere auch den Streitgegenstand und die von dem Gegner, von der Justizkasse oder von sonstigen Stellen zu erstattenden Beträge entgegenzunehmen sowie Akteneinsicht zu nehmen.');
		
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		
		$this->Cell(60,$cell_height,'Datum / Ort');
		$this->Cell(60,$cell_height,'Unterschrift');
	}
	
	function get_page_2($data){
		
		$this->AddPage();
		$font_size=9;
		$cell_height=4;
		$font_face='Arial';
		$this->SetFont($font_face,'',$font_size);
		
		$this->SetFont($font_face,'B',15);
		$this->Cell(30,$cell_height,'Prüfauftrag  „LV-Doktor“');
		
		
		$this->Ln();
		$this->Ln();
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(20,$cell_height,'Name:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(40,$cell_height,$data['lvcheck_name']);
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(20,$cell_height,'Telefon:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(40,$cell_height,$data['lvcheck_tel']);
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(15,$cell_height,'ID:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(30,$cell_height,$data['partner_id']);
		
		$this->Ln();
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(20,$cell_height,'Vorname:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(40,$cell_height,$data['lvcheck_firstname']);
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(20,$cell_height,'Telefax:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(40,$cell_height,$data['lvcheck_fax']);
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(15,$cell_height,'VT-Nr.:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(30,$cell_height,$data['lvcheck_vt']);
		
		$this->Ln();
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(20,$cell_height,'Strasse Nr.:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(40,$cell_height,$data['lvcheck_adress']);
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(20,$cell_height,'Funk:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(40,$cell_height,$data['lvcheck_handy']);
		
		$this->Ln();
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(20,$cell_height,'PLZ / Ort:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(40,$cell_height,$data['lvcheck_zip'].' '.$data['lvcheck_city']);
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(20,$cell_height,'Email:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(40,$cell_height,$data['lvcheck_email']);
		
		
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		
		$this->Cell(30,$cell_height,'Pro Concept AG');
		$this->Ln();
		$this->Cell(30,$cell_height,'C/o „LV-Doktor-Team“');
		$this->Ln();
		$this->Cell(30,$cell_height,'Ankerstrasse 3a');
		$this->Ln();
		$this->Cell(30,$cell_height,'06108 Halle');
				
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(0,$cell_height,'Sehr geehrte Damen und Herren');
		$this->SetFont($font_face,'',$font_size);
		$this->Ln();
		$this->MultiCell(0,$cell_height,'Ich glaube, bei meinem Versicherungsvertrag ist ein erheblich höherer Rückkaufswert möglich. Um von möglichen zusätzlichen Rückerstattungen profitieren zu können, bitte ich um Ihre Unterstützung. Ich biete Ihnen dazu den Abschluss eines Prozessfinanzierungs- und Prozessbetreuungsvertrages nach den mir bekannten und ausgehändigten PFV- Bedingungen ( Stand: 06-2007) zur Anfechtung des nachstehend bezeichneten Vertrags an. Gem. § 151 S. 1 BGB verzichte ich auf den Zugang Ihrer Annahmeerklärung. Jegliche Korrespondenz führen Sie bitte über die o.a. Kontaktdaten.');
		
		$this->Ln();
		$this->Ln();
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(50,$cell_height,'Versicherungsart:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(60,$cell_height,$GLOBALS['dbapi']->get_lvcheck_type_name($data['lvcheck_contract_type']).' / laufender Vertrag');
		$this->Ln();
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(50,$cell_height,'Versicherungsgesellschaft:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(60,$cell_height,$GLOBALS['dbapi']->get_lvcheck_company_name($data['lvcheck_contract_company']));
		$this->Ln();
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(50,$cell_height,'Vertragsnummer:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(60,$cell_height,$data['lvcheck_contract_nr']);
			
		$this->Ln();
		$this->Ln();
		
		/*
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(50,$cell_height,'Versicherungsbeginn:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(50,$cell_height,$data['lvcheck_contract_start']);
		$this->Ln();
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(50,$cell_height,'Erstbeitrag gezahlt am:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(50,$cell_height,$data['lvcheck_contract_option_1']);
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(40,$cell_height,'in Höhe von:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(50,$cell_height,$data['lvcheck_contract_option_2'].' EUR');
		$this->Ln();
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(50,$cell_height,'Letzter Beitrag gezahlt am:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(50,$cell_height,$data['lvcheck_contract_option_3']);
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(40,$cell_height,'in Höhe von:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(50,$cell_height,$data['lvcheck_contract_option_4'].' EUR');
		$this->Ln();
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(50,$cell_height,'Erfolgte Teilzahlungen am:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(50,$cell_height,$data['lvcheck_contract_option_5']);
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(40,$cell_height,'in Höhe von:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(50,$cell_height,$data['lvcheck_contract_option_6'].' EUR');
		$this->Ln();
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(50,$cell_height,'Policedarlehen am:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(50,$cell_height,$data['lvcheck_contract_option_7']);
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(40,$cell_height,'in Höhe von:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(50,$cell_height,$data['lvcheck_contract_option_8'].' erhalten');
		$this->Ln();
		$this->Ln();
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(50,$cell_height,'Policedarlehen getilgt am:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(50,$cell_height,$data['lvcheck_contract_option_9'].' EUR');

		*/
		
		$this->Ln();
		$this->Ln();
		
		$this->MultiCell(0,$cell_height,'Hiermit beauftrage ich Sie alle erforderlichen Maßnahmen zur Durchsetzung meines Anspruchs einzuleiten. Ich beauftrage Sie, einen Rechtsanwalt Ihrer Wahl mit der Durchsetzung meiner Interessen zu beauftragen und füge dazu eine Vollmacht bei. Mir ist bewusst, dass dadurch zwischen mir und dem Rechtsanwalt kein Vertrags- bzw. Mandantenverhältnis zustande kommt. Da der Versicherungsvertrag noch nicht gekündigt ist, bitte ich Sie dessen Kündigung umgehend zu veranlassen und die dafür  entstehende Gebühr i.H.v. 87,50 € sowie die Prüfgebühr i.H.v. 150,00 € vom Rückkaufswert einzubehalten.');
		
		$this->Ln();
		$this->Ln();
		
		// Bankdaten
		
		$this->SetFont($font_face,'B',$font_size);
		$this->MultiCell(0,$cell_height,'Bankdaten');
		$this->SetFont($font_face,'',$font_size);
		
		$this->Ln();
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(50,$cell_height,'Bank:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(60,$cell_height,$data['lvcheck_bank']);
		$this->Ln();
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(50,$cell_height,'Kontonummer:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(60,$cell_height,$data['lvcheck_knr']);
		$this->Ln();
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(50,$cell_height,'BLZ:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(60,$cell_height,$data['lvcheck_blz']);
		$this->Ln();
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(50,$cell_height,'Inhaber:');
		$this->SetFont($font_face,'',$font_size);
		$this->Cell(60,$cell_height,$data['lvcheck_inh']);
		
		$this->Ln();
		$this->Ln();
		$this->Ln();
		
		$this->SetFont($font_face,'B',$font_size);
		$this->MultiCell(0,$cell_height,'Zur Bearbeitung stelle ich Ihnen folgendes Dokument zur  Verfügung');
		$this->SetFont($font_face,'',$font_size);
		
		$this->Ln();
		$this->Ln();
		
		if($data['lvcheck_contract_org']){
			$this->MultiCell(0,$cell_height,'Originalpolice');
		} else {
			$this->MultiCell(0,$cell_height,'Verlusterklärung');
		}
		// Freistellungsauftrag
		if($data['lvcheck_contract_free']){
			$this->MultiCell(0,$cell_height,'Freistellungsauftrag');	
		}
		
		
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		
		$this->SetFont($font_face,'B',$font_size);
		$this->Cell(60,$cell_height,'Datum / Ort');
		$this->Cell(60,$cell_height,'Unterschrift');
		
		// Vollmacht
		
		$this->AddPage();
		$font_size=9;
		$cell_height=4;
		$font_face='Arial';
		$this->SetFont($font_face,'',$font_size);
		
		$this->SetFont($font_face,'B',15);
		$this->Cell(30,$cell_height,'Vollmacht');
		$this->SetFont($font_face,'',$font_size);
		
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		
		$this->MultiCell(0,$cell_height,'Der Unterzeichner/die Unterzeichnerin erteilt hiermit den Rechtsanwälten');
		
		
		
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		
		$this->MultiCell(0,$cell_height,'Vollmacht in der Sache:');
		
		$this->Ln();
		$this->Ln();
		
		$this->MultiCell(0,$cell_height,$data['lvcheck_firstname'].' '.$data['lvcheck_name'].' ./. '.$GLOBALS['dbapi']->get_lvcheck_company_name($data['lvcheck_contract_company']));
		
		$this->Ln();
		$this->Ln();
		
		$this->MultiCell(0,$cell_height,'Gegenstand des Mandats:  Prüfung/Kündigung von Verträgen');
		
		$this->Ln();
		$this->Ln();
		
		$this->MultiCell(0,$cell_height,'Die Vollmacht umfasst die Befugnis zur Prozessführung (u.a. nach §§ 81 ff. ZPO) einschließlich der Befugnis der Erhebung und Zurücknahme von Widerklagen; zur Vertretung in sonstigen Verfahren und bei außergerichtlichen Verhandlungen aller Art zur Begründung und Aufhebung von Vertragsverhältnissen und zur Abgabe und Entgegennahme von einseitigen Willenserklärungen (z.B. Kündigungen). Die Vollmacht gilt für alle Instanzen und erstreckt sich auch auf Neben- und Folgeverfahren aller Art (z.B. Arrest und einstweilige Verfügung, Kostenfestsetzungs-, Zwangsvollstreckungs-, Interventions-, Zwangsversteigerungs-, Zwangsverwaltungs- und Hinterlegungsverfahren sowie Insolvenz- und Vergleichsverfahren über das Vermögen des Gegners). Sie umfasst insbesondere die Befugnis, Zustellungen zu bewirken und entgegenzunehmen, die Vollmacht ganz oder teilweise auf andere zu übertragen (Untervollmacht), Rechtsmittel einzulegen, zurückzunehmen oder auf sie zu verzichten, den Rechtsstreit oder außergerichtliche Verhandlungen durch Vergleich, Verzicht oder Anerkenntnis zu erledigen, Geld, Wertsachen und Urkunden, insbesondere auch den Streitgegenstand und die von dem Gegner, von der Justizkasse oder von sonstigen Stellen zu erstattenden Beträge entgegenzunehmen sowie Akteneinsicht zu nehmen.');
		
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		
		$this->Cell(60,$cell_height,'Datum / Ort');
		$this->Cell(60,$cell_height,'Unterschrift');
		
		
		// Verlusterklärung
		if(!$data['lvcheck_contract_org']){
			$this->AddPage();
			$font_size=9;
			$cell_height=4;
			$font_face='Arial';
			$this->SetFont($font_face,'',$font_size);
			
			$this->SetFont($font_face,'B',15);
			$this->Cell(30,$cell_height,'Verlusterklärung');
			$this->SetFont($font_face,'',$font_size);
			
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->Ln();
			
			$this->MultiCell(0,$cell_height,'Lebensversicherung Nr.: '.$data['lvcheck_contract_nr']);
			
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->Ln();
			
			$this->MultiCell(0,$cell_height,'Versicherte Person: '.$data['lvcheck_firstname'].' '.$data['lvcheck_name']);
			
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->Ln();
			
			$this->MultiCell(0,$cell_height,'Ich erkläre nach eingehender Nachforschung dass ich den Versicherungsschein nicht erhalten habe/nicht mehr besitze.');
			
			$this->Ln();
			$this->Ln();
			
			$this->MultiCell(0,$cell_height,'Ich versichere, dass kein unwiderrufliches Bezugsrecht vorliegt, die Rechte und Ansprüche aus diesem Versicherungsvertrag weder verpfändet, abgetreten noch gepfändet sind. Auch habe ich den Versicherungsschein nicht in Verbindung mit einem sonstigen Leistungsversprechen weitergegeben. Für den Fall, dass Sie aus dem genannten Versicherungsvertrag von einem berechtigten Dritten mit Erfolg in Anspruch genommen werden sollten, verpflichte ich mich Ihnen gegenüber insoweit zur Zahlung. Finde ich den Versicherungsschein wieder oder sollte er noch nachträglich in meinen Besitz gelangen, so verpflichte ich mich, diesen unverzüglich an Sie weiterzuleiten.');
			
			$this->Ln();
			$this->Ln();
			
			$this->MultiCell(0,$cell_height,'Bitte lassen Sie durch den Rechtsanwalt eine entsprechende Verlusterklärung abgeben.');
			
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->Ln();
			
			$this->MultiCell(0,$cell_height,'Ort / Datum Unterschrift des Versicherungsnehmers');
		}
		
		// Freistellungsauftrag
		if($data['lvcheck_contract_free']){
			$this->AddPage();
			$font_size=9;
			$cell_height=4;
			$font_face='Arial';
			$this->SetFont($font_face,'',$font_size);
			
			$this->SetFont($font_face,'B',15);
			$this->Cell(30,$cell_height,'Freistellungsauftrag für Kapitalerträge');
			$this->SetFont($font_face,'',$font_size);
			
			$this->Ln();
			
			$this->MultiCell(0,$cell_height,'(Gilt nicht für Betriebseinnahmen und Einnahmen aus Vermietung und Verpachtung)');
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->SetFont($font_face,'B',$font_size);
			$this->MultiCell(0,$cell_height,'Antragsteller');
			$this->SetFont($font_face,'',$font_size);
			
			$this->Ln();
			
			$this->Cell(30,$cell_height,'Name:');
			$this->Cell(70,$cell_height,$data['lvcheck_name']);
			$this->Cell(30,$cell_height,'Name (Ehegatten):');
			$this->Ln();
			$this->Cell(30,$cell_height,'Vorname:');
			$this->Cell(70,$cell_height,$data['lvcheck_firstname']);
			$this->Cell(30,$cell_height,'Vorname (Ehegatten):');
			$this->Ln();
			$this->Cell(30,$cell_height,'Strasse Nr.:');
			$this->Cell(70,$cell_height,$data['lvcheck_adress']);
			$this->Cell(30,$cell_height,'Geburtsdatum (Ehegatten):');
			$this->Ln();
			$this->Cell(30,$cell_height,'PLZ Ort:');
			$this->Cell(70,$cell_height,$data['lvcheck_zip'].' '.$data['lvcheck_city']);
			$this->Ln();
			$this->Cell(30,$cell_height,'Geburtsdatum:');
			
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->Ln();
			
			$this->MultiCell(0,$cell_height,'An Versicherungsgesellschaft');
			
			$this->Cell(100,$cell_height,'');
			$this->Cell(40,$cell_height,'Bitte Original zurücksenden bzw. einreichen bei dem');
			$this->Ln();
			$this->Cell(100,$cell_height,'');
			$this->Cell(40,$cell_height,'beauftragten Kreditinstitut / Bausparkasse /');
			$this->Ln();
			$this->Cell(100,$cell_height,'');
			$this->Cell(40,$cell_height,'Lebensversicherungsunternehmen/ Bundes-');
			$this->Ln();
			$this->Cell(100,$cell_height,'');
			$this->Cell(40,$cell_height,'/Landesschuldverwaltung');
			
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->SetFont($font_face,'B',$font_size);
			$this->MultiCell(0,$cell_height,'Für Versicherungsschein ________________________');
			$this->SetFont($font_face,'',$font_size);
			
			$this->Ln();
			
			$this->MultiCell(0,$cell_height,'Hiermit erteile ich/erteilen wir*) Ihnen den Auftrag, meine/unsere*) bei Ihrem Institut anfallenden Zinseinnahmen vom Steuerabzug freizustellen und/oder bei Dividenden und ähnlichen Kapitalerträgen die Erstattung von Kapitalertragsteuer und die Vergütung von Körperschaftsteuer beim Bundesamt für Finanzen zu beantragen, und zwar');
			
			$this->Ln();
			
			$this->Rect($this->GetX(), $this->GetY(), 3, 3); 
			$this->MultiCell(0,$cell_height,'    bis zu einem Betrag von ......................... € (bei Verteilung des Freibetrages auf mehrere Kreditinstitute).');
			$this->Ln();
			$this->Rect($this->GetX(), $this->GetY(), 3, 3); 
			$this->MultiCell(0,$cell_height,'    bis zur Höhe des für mich/uns*) geltenden Sparer-Freibetrags und');
			$this->MultiCell(0,$cell_height,'    Werbungskosten-Pauschbetrags von insgesamt 801 € / 1.602 €*).');
			$this->MultiCell(0,$cell_height,'    Dieser Auftrag gilt ab dem .........................');
			$this->Ln();
			$this->Rect($this->GetX(), $this->GetY(), 3, 3); 
			$this->MultiCell(0,$cell_height,'    so lange, bis Sie einen anderen Auftrag von mir/uns*) erhalten.');
			$this->MultiCell(0,$cell_height,'    Er endet am 31.12. des Jahres in dem ich/wir*) die Auszahlung erhalten habe(n)');
			$this->Ln();
			$this->Rect($this->GetX(), $this->GetY(), 3, 3); 
			$this->MultiCell(0,$cell_height,'    bis zum .........................');
			
			$this->Ln();
			
			$this->MultiCell(0,$cell_height,'Die in dem Auftrag enthaltenen Daten werden dem Bundesamt für Finanzen (BfF) übermittelt. Sie dürfen zur Durchführung eines Verwaltungsverfahrens oder eines gerichtlichen Verfahrens in Steuersachen oder eines Strafverfahrens wegen einer Steuerstraftat oder eines Bußgeldverfahrens wegen einer Steuerordnungswidrigkeit verwendet sowie vom BfF den Sozialleistungsträgern übermittelt werden, soweit dies zur Überprüfung des bei der Sozialleistung zu berücksichtigenden Einkommens oder Vermögens erforderlich ist (§ 45 d EStG). Ich versichere/wir versichern*) dass mein/unser*) Freistellungsauftrag zusammen mit Freistellungs-aufträgen an andere Kreditinstitute, Bausparkassen, das Bundesamt für Finanzen usw. den für mich/uns*) geltenden Höchstbetrag von insgesamt 801 € / 1.602 €*) nicht übersteigt.');
			
			$this->MultiCell(0,$cell_height,'Ich versichere/wir versichern*) außerdem, dass ich/wir*) mit allen für das Kalenderjahr erteilten Freistellungsaufträgen für keine höheren Kapitalerträge als insgesamt 801 € / 1.602 €*) im Kalenderjahr die Freistellung oder Erstattung von Kapitalertragsteuer in Anspruch nehme(n)*).');
			
			$this->MultiCell(0,$cell_height,'Die mit dem Freistellungsauftrag angeforderten Daten werden auf Grund von § 36 b Abs. 1, § 44 a Abs. 2, § 44 b Abs. 1 und § 45 d Abs. 1 EStG erhoben.');
			
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->Ln();
			
			$this->Cell(80,$cell_height,'_____________________________');
			$this->Cell(60,$cell_height,'_____________________________________________');
			$this->Ln();
			$this->Cell(80,$cell_height,'Ort, Datum, Unterschrift Antragsteller');
			$this->Cell(60,$cell_height,'gg f . Unterschrift des Ehegatten; gesetzlicher Vertreter');
			
			$this->Ln();
			$this->Ln();
			
			$this->MultiCell(0,$cell_height,'*) Nicht zutreffendes bitte streichen');
			
			$this->Ln();
			$this->Ln();
			
			$this->MultiCell(0,$cell_height,'Der Höchstbetrag von 1.602 € gilt nur bei Ehegatten, bei denen die Voraussetzungen einer Zusammenveranlagung im Sinne des § 26 Abs. 1 Satz 1 EStG vorliegen. Der Freistellungsauftrag ist z. B. nach Auflösung der Ehe oder bei dauerndem Getrenntleben zu ändern.');

			
		}
	}

}

?>