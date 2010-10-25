<?php
require('fpdf.php');
include( 'class.ezpdf.php' );
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if(isset($_GET['cid'])){
	$sql 	= "SELECT * FROM ".TABLE_PREFIX."content WHERE content_id=$_GET[cid]";
	$result = mysql_query($sql);
	if(mysql_num_rows($result)!=0){
		$content_row = mysql_fetch_assoc($result);
		//$titulo = strip_tags($content_row['title']);
		$titulo = $content_row['title'];
		//$contenido = strip_tags($content_row['text']);
		$contenido = $content_row['text'];
/*
		///JOOMLA
		//$params = new mosParameters( $row->attribs );
		//$params->def( 'author', 	!$mainframe->getCfg( 'hideAuthor' ) );
		//$params->def( 'createdate', !$mainframe->getCfg( 'hideCreateDate' ) );
		//$params->def( 'modifydate', !$mainframe->getCfg( 'hideModifyDate' ) );

		$titulo   	= strip_tags(pdfCleaner( $titulo ));
		$contenido 	= trim(strip_tags(pdfCleaner( $contenido )));


		$pdf = new Cezpdf( 'a4', 'P' );  //A4 Portrait
		$pdf -> ezSetCmMargins( 2, 1.5, 1, 1);
		$pdf->selectFont( './fonts/Helvetica.afm' ); //choose font

		$all = $pdf->openObject();
		$pdf->saveState();
		$pdf->setStrokeColor( 0, 0, 0, 1 );

		// footer
		//$pdf->addText( 250, 822, 6, $mosConfig_sitename );
		$pdf->line( 10, 40, 578, 40 );
		$pdf->line( 10, 818, 578, 818 );
		//$pdf->addText( 30, 34, 6, $mosConfig_live_site );
		//$pdf->addText( 250, 34, 6, _PDF_POWERED );
		//$pdf->addText( 450, 34, 6, _PDF_GENERATED .' '. date( 'j F, Y, H:i', time() + $mosConfig_offset * 60 * 60 ) );

		$pdf->restoreState();
		$pdf->closeObject();
		$pdf->addObject( $all, 'all' );
		$pdf->ezSetDy( 30 );

		$txt1 = $titulo;
		$pdf->ezText( $txt1, 14 );

		//$txt2 = AuthorDateLine( $row, $params );
		$txt2 = $contenido;

		$pdf->ezText( $txt2, 8 );

		//$txt3 = $row->introtext ."\n". $row->fulltext;
		//$pdf->ezText( $txt3, 10 );

		$pdf->ezStream();

		//FIN JOOMLA
*/
		$titulo   	= strip_tags(pdfCleaner( $titulo ));
		$contenido 	= trim(strip_tags(pdfCleaner( $contenido )));
		//$contenido 	= str_word_count($contenido,1);
		//$contenido 	= implode(" ",$contenido);

		//Leemos el fichero
		$muestra_pdf = 'muestra_pdf.txt';
    	$f=fopen($muestra_pdf,'w');
    	fwrite($f,$contenido);
    	fclose($f);
    	$f=fopen($muestra_pdf,'r+');
		$pdf=new FPDF();
		$pdf->AddPage();
		$pdf->SetFont('Arial','',10);
		fclose($f);
		//$pdf->Cell(0,5,$titulo);
		//$pdf->Ln(10);
		$pdf->MultiCell(0,3,$contenido);
		$pdf->Output();
	}
	else{
		echo(_AT('error'));

	}
}

function decodeHTML( $string ) {
	$string = strtr( $string, array_flip(get_html_translation_table( HTML_ENTITIES ) ) );
	$string = preg_replace( "/&#([0-9]+);/me", "chr('\\1')", $string );

	return $string;
}


function pdfCleaner( $text ) {
	// Ugly but needed to get rid of all the stuff the PDF class cant handle

	$text = str_replace( '<p>', 			"\n\n", 	$text );
	$text = str_replace( '<P>', 			"\n\n", 	$text );
	$text = str_replace( '<br />', 			"\n", 		$text );
	$text = str_replace( '<br>', 			"\n", 		$text );
	$text = str_replace( '<BR />', 			"\n", 		$text );
	$text = str_replace( '<BR>', 			"\n", 		$text );
	$text = str_replace( '<li>', 			"\n - ", 	$text );
	$text = str_replace( '<LI>', 			"\n - ", 	$text );
	$text = str_replace( '{mosimage}', 		'', 		$text );
	$text = str_replace( '{mospagebreak}', 	'',			$text );
	$text = str_replace( '<table>','',$text );
	$text = str_replace( '<TABLE>','',$text );
	$text = str_replace( '</table>','',$text );
	$text = str_replace( '</TABLE>','',$text );
	$text = str_replace( '<tr>','',$text );
	$text = str_replace( '</tr>','',$text );
	$text = str_replace( '<TR>','',$text );
	$text = str_replace( '</TR>','',$text );
	$text = str_replace( '&nbsp;','',$text );



	$text = strip_tags( $text );
	$text = decodeHTML( $text );

	return $text;
}
?>