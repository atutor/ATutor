<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

if (!defined('AT_INCLUDE_PATH')) { exit; }
global $_base_path, $_base_href;

$count_modules=1; 			// starts at 1 because the first position indicates. do not ever give problems, because if no modules are present in the home will not be executed cycles of control.
$num_modules=0;				// number of modules present and visible in the home for a certain course
$column = "left";			// the count of columns from the left

if ($this->banner): ?><?php echo $this->banner; ?><?php endif;

// positioning switch of home ONLY FOR INSTRUCTORS. two icons will be used for identification to distinguish the two different views of the home.
if(authenticate(AT_PRIV_ADMIN,AT_PRIV_RETURN) && count($this->home_links) > 0){
	if($this->view_mode==0)
		echo '<a href ="'.AT_BASE_HREF.'switch_view.php?swid='.$this->view_mode.'" style="background-color:#FFFFFF;"><img src="'.AT_BASE_HREF.'images/detail_view.png"  alt ="'._AT('detail_view').'" border="0"/></a>';
	else
		echo '<a href ="'.AT_BASE_HREF.'switch_view.php?swid='.$this->view_mode.'" style="background-color:#FFFFFF;"><img src="'.AT_BASE_HREF.'images/icon_view.png"  alt ="'._AT('icon_view').'" border="0"/></a>';
}	

// Icon View, $this->view_mode = 0. course will be made changes to the icons to restore the classic icons.
if($this->view_mode==0){ ?>
	<div style="width: 100%; margin-top: -5px; float:left;">
		<ul id="home-links">
		<br>
		<?php foreach ($this->home_links as $link): ?>
			<li><a href="<?php echo $link['url']; ?>"><img src="<?php echo $link['img']; ?>" alt="" class="img-size-home" border="0" /><?php echo $link['title']; ?></a></li>
		<?php endforeach; ?>
		</ul>
	</div> <?php
} else {		// Detail View, $this->view_mode=1.?>
	
	<div style="width: 100%; margin-top: -5px; float: left; ">
		<ul id="home-links">
		<?php 				// create table container divided into two columns for the placement of modules
		foreach ($this->home_links as $link)				// counting the number of modules present in the home for the student. need for controls on the positioning of the arrows of the various modules.
			$num_modules++;
			
		if(authenticate(AT_PRIV_ADMIN,AT_PRIV_RETURN)){		// display enabled course tool
			foreach ($this->home_links as $link){ ?>
			<div id="home_box"> 
				<div id="home_toolbar"><?php
				if($num_modules!=0 && $this->enable_move_tool){													//si controlla se sono presenti moduli visibili nella home.
					if($num_modules != 1 && $link['check'] == 'visible'){ 				//se check � impostato 'visible' significa che il modulo sar� presente nella home e potrebbe necessitare delle frecce direzionali
						if($count_modules <= 2 ){ 
							if($num_modules >2 && ($num_modules-$count_modules)>1) 								//significa che ci sono possibili moduli sottostanti quindi sar� da stampare la freccia "down"
								echo '<a href ="'.AT_BASE_HREF.'move_module.php?mid='.$count_modules.'&move=down"><img src="'.AT_BASE_HREF.'images/arrow-mini-down.png" alt="move down" border="0"/></a>&nbsp';
							if($column=="left")
								echo '<a href ="'.AT_BASE_HREF.'move_module.php?mid='.$count_modules.'&move=right"><img src="'.AT_BASE_HREF.'images/arrow-mini-right.png" alt="move right" border="0"/></a>&nbsp';
							else 
								echo '<a href ="'.AT_BASE_HREF.'move_module.php?mid='.$count_modules.'&move=left"><img src="'.AT_BASE_HREF.'images/arrow-mini-left.png" alt="move left" border="0"/></a>&nbsp';
						} else if($count_modules>2 && ($num_modules-$count_modules)>1){	//moduli intermedi, dovranno essere stampate obbligatoriamente le frecce 'up', 'down' e a seconda della colonna anche 'sx' o 'dx'
							echo '<a href ="'.AT_BASE_HREF.'move_module.php?mid='.$count_modules.'&move=down"><img src="'.AT_BASE_HREF.'images/arrow-mini-down.png" alt="move down" border="0"/></a>&nbsp';
							echo '<a href ="'.AT_BASE_HREF.'move_module.php?mid='.$count_modules.'&move=up"><img src="'.AT_BASE_HREF.'images/arrow-mini-up.png" alt="move up" border="0"/></a>&nbsp';
							if($column=="left")
								echo '<a href ="'.AT_BASE_HREF.'move_module.php?mid='.$count_modules.'&move=right"><img src="'.AT_BASE_HREF.'images/arrow-mini-right.png" alt="move right" border="0"/></a>&nbsp';
							else
								echo '<a href ="'.AT_BASE_HREF.'move_module.php?mid='.$count_modules.'&move=left"><img src="'.AT_BASE_HREF.'images/arrow-mini-left.png" alt="move left" border="0"/></a>&nbsp';	
						}else if($count_modules>2 && ($num_modules-$count_modules)==1){
							echo '<a href ="'.AT_BASE_HREF.'move_module.php?mid='.$count_modules.'&move=up"><img src="'.AT_BASE_HREF.'images/arrow-mini-up.png" alt="move up" border="0"/></a>&nbsp';
							if($column=="left")
								echo '<a href ="'.AT_BASE_HREF.'move_module.php?mid='.$count_modules.'&move=right"><img src="'.AT_BASE_HREF.'images/arrow-mini-right.png" alt="move right" border="0"/></a>&nbsp';
							else 
								echo '<a href ="'.AT_BASE_HREF.'move_module.php?mid='.$count_modules.'&move=left"><img src="'.AT_BASE_HREF.'images/arrow-mini-left.png" alt="move left" border="0"/></a>&nbsp';
						} else {														//caso in cui la differenza sia pari a zero. se l 'ultimo modulo � nella posizione di sx sar� stampata solo la freccia 'up' altrim se nella posizione di destra: freccia 'up' e 'sx'
							echo '<a href ="'.AT_BASE_HREF.'move_module.php?mid='.$count_modules.'&move=up"><img src="'.AT_BASE_HREF.'images/arrow-mini-up.png" alt="move up" border="0"/></a>&nbsp';
							if($column=="right")
								echo '<a href ="'.AT_BASE_HREF.'move_module.php?mid='.$count_modules.'&move=left"><img src="'.AT_BASE_HREF.'images/arrow-mini-left.png" alt="move left" border="0"/></a>&nbsp';
						}
						echo '<a href ="'.AT_BASE_HREF.'move_module.php?mid='.$count_modules.'"><img src="'.AT_BASE_HREF.'images/eye-mini-close.png" alt="set invisible" border="0"/></a>&nbsp';
					} else if($num_modules == 1 && $link['check'] == 'visible'){ //
						echo '<a href ="'.AT_BASE_HREF.'move_module.php?mid='.$count_modules.'"><img src="'.AT_BASE_HREF.'images/eye-mini-close.png" alt="set invisible" border="0"/></a>&nbsp';
					}
				} ?>
				</div> <?php
				print_sublinks($link); 						//chiamata alla funzione di stampa dei moduli?>
			</div> <?php
				if($column=='left'){									//caso in cui sia appena stata definita la prima cella della riga (posizione left)
					$column='right';									//$column impostato a right per definire l'eventuale seconda cella
				} 
				else if($column=='right'){ 								//caso in cui sia stata definita la seconda cella all'interno della riga corrente.				
					$column='left';
				}
				$count_modules++;										//aggiornamento del numero dei moduli sinora posizionati nella home
			}
		} else {														//caso in cui debbano essere visualizzati i moduli per lo studente nella detail view
			foreach ($this->home_links as $link){?>
				<div id="home_box">
					<div id="home_toolbar">
						<br>
					</div><?php
					print_sublinks($link); 							//chiamata alla funzione di stampa dei moduli (si ricorda che nel ciclo sono trattati solo quelli visibili nella home)?>
					
				</div>
				<?php
				if($column=='left'){									//caso in cui sia appena stata definita la prima cella della riga (posizione left)
					$column='right';									//$column impostato a right per definire l'eventuale seconda cella
				} 
				else if($column=='right' ){ 							//caso in cui sia stata definita la seconda cella all'interno della riga.
					$column="left";										
				}
			}
		} ?>														<!-- chiusura tabella contenitore esterno -->
		</ul>
	</div> 
	<?php
}  

if ($this->announcements): ?>
<h2 class="page-title"><?php echo _AT('announcements'); ?></h2>
	<?php foreach ($this->announcements as $item): ?>
		<div class="news">
			<h3><?php echo $item['title']; ?></h3>
			<p><span class="date"><?php echo $item['date'] .' '. _AT('by').' ' . $item['author']; ?></span></p> <?php echo $item['body']; ?>
		</div>
	<?php endforeach; ?>

	<?php if ($this->num_pages > 1): ?>
		<?php echo _AT('page'); ?>: | 
		<?php for ($i=1; $i<=$this->num_pages; $i++): ?>
			<?php if ($i == $this->current_page): ?>
				<strong><?php echo $i; ?></strong>
			<?php else: ?>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?p=<?php echo $i; ?>"><?php echo $i; ?></a>
			<?php endif; ?>
			 | 
		<?php endfor; ?>
	<?php endif; ?>
<?php endif;


/*la funzione viene utilizzata per la stampa dei moduli e degli eventuali sottocontenuti per ogni modulo. ad ogni chiamata sar� passato il modulo interessato dal quale saranno estrapolati
* i dati necessari (preventivamente caricati) per la visualizzazione. in questo modo possono essere gestite le due distinte visualizzazioni: istruttore e studente
*/
function print_sublinks($link){ ?>
	<div id="home_icon_title">
		<div id="home_icon">
				<img src="<?php echo $link['img']; ?>" alt="" border="0"/>					
		</div>
		<div id="home_title">
				<font size="+1"> 
					<a href="<?php echo $link['url']; ?>"><?php echo $link['title']; ?></a>	<!-- inserimento link associato -->
				</font>
		</div>
	</div><?php
	if($link['icon']!=""){						//nel caso in cui sia settata una sottoicona per il modulo in esame allora saranno stampati gli eventuali sottocontenuti 
		$array = require(AT_INCLUDE_PATH.'../'.$link['sub_file']);	//viene richiamato il file di controllo specifico per i sottocontenuti contenuto in include/html/sibmodules
		if(($array)==0){ 						//"0" è il valore di ritorno del file nel caso in cui non siano stati trovati dei sottocontenuti*/?>
			<div id="home_text">
				<i><?php echo _AT('none_found'); ?></i>
			</div><?php
		} else { ?>								<!-- stampa dei sottocontenuti, per ognuno verr� stampata la sub-icon relativa e il collegamento al sottocontenuto stesso -->
			<div id="home_content"><?php
				for($i=0; $array[$i]['sub_url']!=""; $i++){ 			//si esegue il ciclo di stampa fin quando saranno presenti sottocontenuti per il modulo corrente. il limite � impostato per default a 3?>
					<img src="<?php echo $link['icon']; ?>" border="0"/> <?php
					$text = validate_length($array[$i]['sub_text'], 38, VALIDATE_LENGTH_FOR_DISPLAY); //controllo della lunghezza del testo dei sub content
					if($text != $array[$i]['sub_text'])					//nel caso in cui la lunghezza sia superiore a quella prefissata viene visualizzato l' "alt" in modo da rapprsentare l'intera stringa
						$title = $array[$i]['sub_text'];
					else
						$title=''; 										//$title impostato '' nel caso in cui non sia necessario visualizzare l' "atl" ?>
					<a href="<?php echo $array[$i]['sub_url']; ?>" title="<?php echo $title; ?>"> <?php echo $text; ?> </a> 
					<br> <?php
				} ?>
			</div> <?php
		}						
	} else { 									//se non sono presenti sottocontenuti viene adattata la tabella di conseguenza e stampata una breve descrizione?>
		<div id="home_text">
		<?php echo $link['text']; ?> 
		</div><?php
	}
} ?>