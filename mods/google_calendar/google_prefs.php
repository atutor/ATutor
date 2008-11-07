<?php


?>

<div class="input-form">
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<label for="gcal_xml" class="input"><?php echo _AT('google_private_xml'); ?> <input type="text" name="gcal_xml" id="gcal_url" size="65"/><br />
<label for="gcal_html"><?php echo _AT('google_private_html'); ?> <input type="text" name="gcal_html" id="gcal_html" size="65" /><br />
<?php

$time_zone = date_default_timezone_get();

if($time_zone == ''){
?>

<label for="timezone"><?php echo  _AT('select_timezone') ?>&nbsp;
<select id="timezone" name="timezone">

<option>Africa/Accra</option>
<option>Africa/Cairo</option>
<option>Africa/Ceuta</option>
<option>Africa/Johannesburg</option>
<option>Africa/Kampala</option>
<option>Africa/Khartoum</option>
<option>Africa/Nairobi</option>
<option>Africa/Ouagadougou</option>
<option>Africa/Tunis</option>
<option>Africa/Windhoek</option>
<option>America/Anchorage</option>
<option>America/Araguaina</option>
<option>America/Argentina/Buenos_Aires</option>
<option>America/Bahia</option>
<option>America/Belem</option>
<option>America/Boa_Vista</option>
<option>America/Campo_Grande</option>
<option>America/Chicago</option>
<option>America/Cuiaba</option>
<option>America/Dawson_Creek</option>
<option>America/Denver</option>
<option>America/Edmonton</option>
<option>America/Fortaleza</option>
<option>America/Halifax</option>
<option>America/Havana</option>
<option>America/Iqaluit</option>
<option>America/Lima</option>
<option>America/Los_Angeles</option>
<option>America/Maceio</option>
<option>America/Manaus</option>
<option>America/Montreal</option>
<option>America/New_York</option>
<option>America/Noronha</option>
<option>America/Phoenix</option>
<option>America/Porto_Velho</option>
<option>America/Recife</option>
<option>America/Regina</option>
<option>America/Rio_Branco</option>
<option>America/Sao_Paulo</option>
<option>America/St_Johns</option>
<option>America/Toronto</option>
<option>America/Vancouver</option>
<option>America/Whitehorse</option>
<option>America/Winnipeg</option>
<option>America/Yellowknife</option>
<option>Asia/Amman</option>
<option>Asia/Baghdad</option>
<option>Asia/Beirut</option>
<option>Asia/Calcutta</option>
<option>Asia/Damascus</option>
<option>Asia/Dili</option>
<option>Asia/Dubai</option>
<option>Asia/Hong_Kong</option>
<option>Asia/Irkutsk</option>
<option>Asia/Jerusalem</option>
<option>Asia/Kamchatka</option>
<option>Asia/Krasnoyarsk</option>
<option>Asia/Kuwait</option>
<option>Asia/Magadan</option>
<option>Asia/Muscat</option>
<option>Asia/Nicosia</option>
<option>Asia/Omsk</option>
<option>Asia/Pyongyang</option>
<option>Asia/Qatar</option>
<option>Asia/Rangoon</option>
<option>Asia/Riyadh</option>
<option>Asia/Seoul</option>
<option>Asia/Shanghai</option>
<option>Asia/Singapore</option>
<option>Asia/Taipei</option>
<option>Asia/Tehran</option>
<option>Asia/Tokyo</option>
<option>Asia/Vladivostok</option>
<option>Asia/Yakutsk</option>
<option>Asia/Yekaterinburg</option>
<option>Asia/Yerevan</option>
<option>Atlantic/Azores</option>
<option>Atlantic/Canary</option>
<option>Atlantic/Reykjavik</option>
<option>Australia/Adelaide</option>
<option>Australia/Brisbane</option>
<option>Australia/Darwin</option>
<option>Australia/Hobart</option>
<option>Australia/Perth</option>
<option>Australia/Sydney</option>
<option>Europe/Amsterdam</option>
<option>Europe/Andorra</option>
<option>Europe/Athens</option>
<option>Europe/Belgrade</option>
<option>Europe/Berlin</option>
<option>Europe/Brussels</option>
<option>Europe/Bucharest</option>
<option>Europe/Budapest</option>
<option>Europe/Chisinau</option>
<option>Europe/Copenhagen</option>
<option>Europe/Dublin</option>
<option>Europe/Gibraltar</option>
<option>Europe/Helsinki</option>
<option>Europe/Istanbul</option>
<option>Europe/Kaliningrad</option>
<option>Europe/Kiev</option>
<option>Europe/Lisbon</option>
<option>Europe/London</option>
<option>Europe/Luxembourg</option>
<option>Europe/Madrid</option>
<option>Europe/Malta</option>
<option>Europe/Minsk</option>
<option>Europe/Monaco</option>
<option>Europe/Moscow</option>
<option>Europe/Oslo</option>
<option>Europe/Paris</option>
<option>Europe/Prague</option>
<option>Europe/Riga</option>
<option>Europe/Rome</option>
<option>Europe/Samara</option>
<option>Europe/Sofia</option>
<option>Europe/Stockholm</option>
<option>Europe/Tallinn</option>
<option>Europe/Tirane</option>
<option>Europe/Vaduz</option>
<option>Europe/Vienna</option>
<option>Europe/Vilnius</option>
<option>Europe/Warsaw</option>
<option>Europe/Zurich</option>
<option>Indian/Mahe</option>
<option>Indian/Mauritius</option>
<option>Pacific/Gambier</option>
<option>Pacific/Honolulu</option>
<option>Pacific/Marquesas</option>
<option>Pacific/Tahiti
</select><br />

<?php 
}else{ ?>
<input type="hidden" name="timezone" value="<?php echo $time_zone; ?>" class="input"/>
<?php } 


?>
<input type="submit" name="save_prefs" value="<?php echo _AT('save'); ?>" class="button" />
</form>
</div>
