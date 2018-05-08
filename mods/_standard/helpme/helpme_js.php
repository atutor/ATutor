<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2018                                                   */
/* ATutorSpaces                                                         */
/* https://atutorspaces.com                                             */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

global $_custom_head, $_base_href, $_base_path, $current_help, $savant;

if($_SESSION['member_id'] || $_SESSION['is_admin']){
    $member_id = $_SESSION['member_id'];
}else if($_SESSION['course_id'] == '-1'){
    $member_id = '-1';
} 

if($_SESSION['course_id'] == 0){
    //unset($_COOKIE["nexthelp_cookie"]);
    //setcookie("nexthelp_cookie", "1", "/");
    //setcookie("nexthelp_cookie", "1", "1", "/");
   // $_COOKIE["nexthelp_cookie"] = 1;
    
}

if(!defined($_COOKIE["nexthelp_cookie"]) || $_COOKIE["nexthelp_cookie"] == 0){

    $current_help = queryDB("SELECT help_id FROM %shelpme_user WHERE user_id ='%d'", array(TABLE_PREFIX, $member_id), true);
    if(!empty($current_help)){
        if($current_help['help_id'] == 0){
            $next_help = ((intval($current_help['help_id'])+1));
        }else{
            $next_help = ((intval($current_help['help_id'])));
        }
    }else{
        if($member_id != 0 && $member_id !=''){
            $sql = "REPLACE INTO %shelpme_user (`user_id`, `help_id`) VALUES (%d,%d)";
            queryDB( $sql, array(TABLE_PREFIX, $member_id, 1));
            $current_help = queryDB("SELECT help_id FROM %shelpme_user WHERE user_id ='%d'", array(TABLE_PREFIX, $member_id), true);
            $next_help = (intval($current_help['help_id']));
        } else{
            $next_help = 0;
        }

    }

}else if($_COOKIE["nexthelp_cookie"] == 0){
    $next_help = ($_COOKIE["nexthelp_cookie"]+1);
}else if($_COOKIE["nexthelp_cookie"] > 0){ 
    $next_help = ($_COOKIE["nexthelp_cookie"]);
}

if($next_help > $helpme_total){
    $_custom_head .="
    <script src=\"".$_base_href."mods/_standard/helpme/js.cookie-2.2.0.min.js\"></script>
    <link rel=\"stylesheet\" href=\"".$_base_href."mods/_standard/helpme/module.css\" type=\"text/css\" />
    <script type=\"text/javascript\">
        jQuery(document).ready(function(){ 
            $(\"#help\").css(\"display\",\"none\", \"!important\");
        });
    </script>
    ";
}
if($_SESSION['valid_user'] && $next_help <= $helpme_total){
        $_custom_head .="
<link rel=\"stylesheet\" href=\"".$_base_href."mods/_standard/helpme/module.css\" type=\"text/css\" />
<script src=\"".$_base_href."mods/_standard/helpme/js.cookie-2.2.0.min.js\"></script>
<script type=\"text/javascript\">

jQuery(document).ready(function(){ 
        var oldSrc = '".$_base_path."themes/default/images/close_icon.png';
        var newSrc = '".$_base_path."themes/default/images/next.png';
        $('#delete img[src=\"' + oldSrc + '\"]').attr('src', newSrc);
        
        // Define the nexthelp variable to hold help_id of the next help message to be displayed
        var nexthelp;
        Number(nexthelp);
      
       // Set the next help id on the fresh loaded page, giving the cookie priority
         if(Cookies.get('nexthelp_cookie') > 1){
                nexthelp = Cookies.get('nexthelp_cookie');
         } else if (".$next_help." > 0){
                nexthelp = \"".$next_help."\";
         } else{
                nexthelp = 0;
        }
        // Hide the message box if number viewed is greater than the total
        if(".$next_help.">".$helpme_total."){
            $(\"#message\").css(\"display\",\"none\", \"!important\");
        }
        
        // Hide the next icon for the last message
        if((+nexthelp+1) >=  ".$helpme_total."){
            $(\"#delete img\").animate({ opacity: 'hide' }, \"slow\");
            $(\"#delete img\").css(\"display\", \"none\");
        }
        
        // Hide all messages except the current one
        $(\"#help li\" ).each(function( index ) {
            index++;
            if(index != ".($next_help)."){
                $( this ).css('display','none');
            } else if(index == 1){
                $( this ).css('display','inline');
            }else{
                $( this ).css('display','inline');
            }
        });
        
        // Hide the previous icon if its the first message
        if(".$next_help." <= 1 || Cookies.get('nexthelp_cookie') <= 1){
            $(\"#revisit img\").animate({ opacity: 'hide' }, \"slow\");
            $(\"#revisit img\").css(\"display\", \"none\");
        } else{
           // $(\"#revisit img\").animate({ opacity: 'show' }, \"slow\");
        }
       
       ///////
       // The delete() i.e. Next() function, updates the db and cookie, to cycle/hide/show messages
        var delete_callback = function() {          
            // Set the cookie if there isnt one, and assign the nexthelp value
            if(Cookies.get('nexthelp_cookie') === 'undefined' || Cookies.get('nexthelp_cookie')  === null){ 
                nexthelp = ".$next_help.";
                Cookies.set('nexthelp_cookie', ( nexthelp ), { path: '/'});
            }else  if(Cookies.get('nexthelp_cookie') >= 0){
                nexthelp = Cookies.get('nexthelp_cookie');
            }

            var thishelp = '';

            // Hide the Next/Previous button if its the first message
            if((+nexthelp) <= 1){
                $(\"#revisit img\").animate({ opacity: 'hide' }, \"slow\");
            } else{
                $(\"#revisit img\").animate({ opacity: 'show' }, \"slow\");
            }
            
            // Create the message list, and show only the current one
            $(\"#help li\" ).each(function( index ) {
                // Increment the index (0-9) so it matches nexthelp (1-10)
                index++;                
                
                if(index <= (+nexthelp)){
                    // Hide any message not the current message, hide all if the total is reached
                     if((+nexthelp) >= ".$helpme_total."){
                        $(\"#message\").animate({ opacity: 'hide' }, \"slow\");
                    }else{
                        $(\"#help li:nth-child(\"+(+nexthelp)+\")\").animate({ opacity: 'hide' }, \"slow\");
                    }
                
                }else if(index == (+nexthelp+1)){
                    
                    Cookies.set('nexthelp_cookie', ( index ), {path: '/'});
                    var thishelp = (+nexthelp+1);
                    if(+nexthelp > 0 && (+nexthelp+1) <".$helpme_total."){
                        $(\"#delete img\").animate({ opacity: 'show' }, \"slow\");
                    } else if((+nexthelp+1) >=  ".$helpme_total."){
                        $(\"#delete img\").animate({ opacity: 'hide' }, \"slow\");
                    }else if(+nexthelp){
                        $(\"#delete img\").animate({ opacity: 'show' }, \"slow\");
                    }
                    
                    if((+nexthelp+1) > ".$helpme_total." || ".($next_help+1)." > ".$helpme_total."){
                        saveData((+thishelp));
                        $(\"#message\").animate({ opacity: 'hide' }, \"slow\");
                    
                    } else{
                        $(\"#help li:nth-child(\"+(+nexthelp+1)+\")\").delay( \"slow\" ).animate({ opacity: 'show' }, \"slow\"); 
                        saveData(+thishelp);
                        if((+nexthelp) == ".$helpme_total."){
                            $(\"#help a#delete\").animate({ opacity: 'hide' }, \"slow\");
                        } 
                    }
                    
                } else {
                    $(\"#help li:nth-child(\"+(+nexthelp+1)+\")\").css( \"display\",\"none\"); 
                }
             });
        }

        $(\"#delete img\").keydown(function(event) {
            if (event.which == 13) delete_callback();
        });
        
        $('#delete img').click(delete_callback);

        ////////
        // Click or key press to dismiss all messages
       var dismiss_callback = function(){
           if (confirm('Are you sure you want to dismiss all HelpMe messages permanently?')) {  
           Cookies.set('nexthelp_cookie', (".$helpme_total."+1), { path: '/' });
                $(this).parents(\".divClass\").animate({ opacity: 'hide' }, \"slow\");
                saveData((".$helpme_total."+1));    
            }
        }

         $(\"#dismiss_all\").keypress(function(event) {
            if (event.which == 13) dismiss_callback();
        });
        $('#dismiss_all').click(dismiss_callback);
        
        ////////
        // Click or keypress to reset messages back to the start
         var reset_callback = function(){
            saveData(+0);    
            nexthelp = Cookies.get('nexthelp_cookie');

            $(\"#help li\" ).each(function( index ) {
                $( this ).css('display','none');
            });

            Cookies.set('nexthelp_cookie', '1', { path: '/' });
            $(this).parents(\".divClass\").animate({ opacity: 'hide' }, \"slow\");  
            $(this).parents(\".divClass\").animate({ opacity: 'show' }, \"slow\");
            $(\"#help li:nth-child(1)\").animate({ opacity: 'show' }, \"slow\");  
            $(\"#revisit img\").animate({ opacity: 'hide' }, \"slow\");
            $(\"#help a#delete\").animate({ opacity: 'show' }, \"slow\");
            $(\"#help\").focus();
            //$(\"#delete img\").animate({ opacity: 'show' }, \"slow\");
            //url = \"https://www.rapidtables.com/web/dev/jquery-redirect.htm\";
            // window.location.reload();
          }  
            
        $(\"#helpme_reset\").keypress(function(event) {
            if (event.which == 13) reset_callback();
        });
        $('#helpme_reset').click(reset_callback);
        
        /////////
        // Click or keypress to go back through a message sequence
        var revisit_callback = function(){
                nexthelp = Cookies.get('nexthelp_cookie');
                nexthelp = (+nexthelp-1);

                $(\"#help li:nth-child(\"+(+nexthelp+1)+\")\").animate({ opacity: 'hide' }, \"slow\"); 
                $(\"#help li:nth-child(\"+(+nexthelp)+\")\").animate({ opacity: 'show' }, \"slow\"); 
                Cookies.set('nexthelp_cookie', ( nexthelp ), { path: '/'});

                if(nexthelp <=1){
                    $(\"#revisit img\").animate({ opacity: 'hide' }, \"slow\");
                } else{
                    $(\"#revisit img\").animate({ opacity: 'show' }, \"slow\");
                }
                $(\"#delete img\").animate({ opacity: 'show' }, \"slow\");
                saveData(nexthelp);
            }
        
            $(\"#revisit img\").keypress(function(event) {
                if (event.which == 13) revisit_callback();
            });
        $('#revisit img').click(revisit_callback);
        
    });
    
    /////////
    // Saves the current help_id to the database, and updates the counter
    // next_help = integer 
    function saveData(next_help){  
        $.ajax({
            type: \"GET\",
            url: \"".$_base_href."mods/_standard/helpme/update_helpme.php\",
            data: { user_id: \"".$member_id."\", help_id: next_help },
            success: function (data) {
                //console.log(\"data success\");
            },
            error: function (data) {
                //console.log(\"data failed\");
            }
            }).done(function( msg ) {
                var otherdata =$('.helpme_count').text();
                var arr = otherdata.split('/');
                var set;
                var thishelp = (parseInt(arr[0])+1);
                if(typeof set === 'undefined' || set === null){
                    
                     if(+next_help == +0){
                        $(\".helpme_count\").html(1+\"/\"+arr[1] );
                    } else if(arr[1] >= +next_help){
                        $(\".helpme_count\").html(next_help+\"/\"+arr[1] );
                    }
                    var set=1;
                }
        });
    }
    if(".$next_help.">".$helpme_total."){
            $(\"#delete img\").css(\"display\",\"none\");
    }
</script>";
}
?>