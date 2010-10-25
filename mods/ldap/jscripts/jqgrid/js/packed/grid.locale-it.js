/*
 * jqGrid  3.3 - jQuery Grid
 * Copyright (c) 2008, Tony Tomov, tony@trirand.com
 * Dual licensed under the MIT and GPL licenses
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 * Date: 2008-10-14 rev 64
 */

;(function($){$.jgrid={};$.jgrid.defaults={recordtext:"Record",loadtext:"Caricamento...",pgtext:"/"};$.jgrid.search={caption:"Ricerca...",Find:"Cerca",Reset:"Pulisci",odata:['uguale','diverso','minore','minore o uguale','maggiore','maggiore o uguale','inizia con','finisce con','contiene']};$.jgrid.edit={addCaption:"Aggiungi Record",editCaption:"Modifica Record",bSubmit:"Invia",bCancel:"Annulla",bClose:"Chiudi",processData:"In elaborazione...",msg:{required:"Campo richiesto",number:"Per favore, inserisci un valore valido",minValue:"il valore deve essere maggiore o uguale a ",maxValue:"il valore deve essere minore o uguale a",email:"e-mail non corretta"}};$.jgrid.del={caption:"Cancella",msg:"Cancellare record selezionato/i?",bSubmit:"Cancella",bCancel:"Annulla",processData:"In elaborazione..."};$.jgrid.nav={edittext:" ",edittitle:"Modifica record selezionato",addtext:" ",addtitle:"Aggiungi nuovo record",deltext:" ",deltitle:"Cancella record selezionato",searchtext:" ",searchtitle:"Ricerca record",refreshtext:"",refreshtitle:"Aggiorna griglia",alertcap:"Attenzione",alerttext:"Per favore, seleziona un record"};$.jgrid.col={caption:"Mostra/Nascondi Colonne",bSubmit:"Invia",bCancel:"Annulla"};$.jgrid.errors={errcap:"Errore",nourl:"Url non settata",norecords:"Nessun record da elaborare"};})(jQuery);