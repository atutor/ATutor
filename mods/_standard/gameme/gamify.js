jQuery(document).ready(function(){
        if($("div[role='tabpanel'], panel1").attr("aria-hidden") == "false"){
            $("#tab1").addClass("active");
            $("#panel1").attr("aria-hidden", "false");
        } 
        //} 
/*        if($("div[role='tabpanel'], panel2").attr("aria-hidden") == "false"){
            $("#tab2").addClass("active");
        } 
        if($("div[role='tabpanel'], panel3").attr("aria-hidden") == "false"){
            $("#tab3").addClass("active");
        } 
*/
    if(Cookies.get('activetab') == "tab2"){
        $("#tab1").removeClass("active");
        $("#tab3").removeClass("active");
        $("#tab2").addClass("active");
        $("#tab4").removeClass("active");
        $("#tab5").removeClass("active");
        $("#panel1").attr("aria-hidden","true");
        $("#panel3").attr("aria-hidden","true");
        $("#panel2").attr("aria-hidden","false");
        $("#panel4").attr("aria-hidden","true");
        $("#panel5").attr("aria-hidden","true");
    } else if(Cookies.get('activetab') == "tab3"){
        $("#tab1").removeClass("active");
        $("#tab2").removeClass("active");
        $("#tab3").addClass("active");
        $("#tab4").removeClass("active");
        $("#tab5").removeClass("active");
        $("#panel3").attr("aria-hidden","false");
        $("#panel1").attr("aria-hidden","true");
        $("#panel2").attr("aria-hidden","true");
        $("#panel4").attr("aria-hidden","true");
        $("#panel5").attr("aria-hidden","true");
    } else if(Cookies.get('activetab') == "tab4"){
        if(!$("#tab4").length){
            $("#tab1").focus();
            $("#tab2").removeClass("active");
            $("#tab3").removeClass("active");
            $("#tab1").addClass("active");
            $("#tab4").removeClass("active");
            $("#tab5").removeClass("active");
            $("#panel2").attr("aria-hidden","true");
            $("#panel3").attr("aria-hidden","true");
            $("#panel1").attr("aria-hidden","false");
            $("#panel4").attr("aria-hidden","true");
            $("#panel5").attr("aria-hidden","true");
        } else{
            $("#tab2").removeClass("active");
            $("#tab3").removeClass("active");
            $("#tab1").removeClass("active");
            $("#tab4").addClass("active");
            $("#tab5").removeClass("active");
            $("#panel2").attr("aria-hidden","true");
            $("#panel3").attr("aria-hidden","true");
            $("#panel1").attr("aria-hidden","true");
            $("#panel4").attr("aria-hidden","false");
            $("#panel5").attr("aria-hidden","true");
        }
    } else if (Cookies.get('activetab') == "tab5"){
        $("#tab1").removeClass("active");
        $("#tab2").removeClass("active");
        $("#tab3").removeClass("active");
        $("#tab4").removeClass("active");
        $("#tab5").addClass("active");
        $("#panel3").attr("aria-hidden","true");
        $("#panel1").attr("aria-hidden","true");
        $("#panel2").attr("aria-hidden","true");
        $("#panel4").attr("aria-hidden","true");
        $("#panel5").attr("aria-hidden","false");
    }else {
        $("#tab2").removeClass("active");
        $("#tab3").removeClass("active");
        $("#tab1").addClass("active");
        $("#tab4").removeClass("active");
        $("#panel2").attr("aria-hidden","true");
        $("#panel3").attr("aria-hidden","true");
        $("#panel1").attr("aria-hidden","false");
        $("#panel4").attr("aria-hidden","true");
    }
     $("#tab1").click(function(){
            $("#tab2").removeClass("active");
            $("#tab3").removeClass("active");
            $("#tab1").addClass("active");
            $("#tab4").removeClass("active");
            $("#tab5").removeClass("active");
            $("#panel2").attr("aria-hidden","true");
            $("#panel3").attr("aria-hidden","true");
            $("#panel1").attr("aria-hidden","false");
            $("#panel4").attr("aria-hidden","true");
            $("#panel5").attr("aria-hidden","true");
        });
       $("#tab2").click(function(){
            $("#tab1").removeClass("active");
            $("#tab3").removeClass("active");
            $("#tab2").addClass("active");
            $("#tab4").removeClass("active");
            $("#tab5").removeClass("active");
            $("#panel1").attr("aria-hidden","true");
            $("#panel3").attr("aria-hidden","true");
            $("#panel2").attr("aria-hidden","false");
            $("#panel4").attr("aria-hidden","true");
            $("#panel5").attr("aria-hidden","true");
        });
        $("#tab3").click(function(){
            $("#tab1").removeClass("active");
            $("#tab2").removeClass("active");
            $("#tab3").addClass("active");
            $("#tab4").removeClass("active");
            $("#tab5").removeClass("active");
            $("#panel1").attr("aria-hidden","true");
            $("#panel2").attr("aria-hidden","true");
            $("#panel3").attr("aria-hidden","false");
            $("#panel4").attr("aria-hidden","true");
            $("#panel5").attr("aria-hidden","true");
        });
        $("#tab4").click(function(){
            $("#tab1").removeClass("active");
            $("#tab2").removeClass("active");
            $("#tab3").removeClass("active");
            $("#tab4").addClass("active");
            $("#tab5").removeClass("active");
            $("#panel1").attr("aria-hidden","true");
            $("#panel2").attr("aria-hidden","true");
            $("#panel3").attr("aria-hidden","true");
            $("#panel4").attr("aria-hidden","false");
            $("#panel5").attr("aria-hidden","true");
        });
        $("#tab5").click(function(){
            $("#tab1").removeClass("active");
            $("#tab2").removeClass("active");
            $("#tab3").removeClass("active");
            $("#tab4").removeClass("active");
            $("#tab5").addClass("active");
            $("#panel1").attr("aria-hidden","true");
            $("#panel2").attr("aria-hidden","true");
            $("#panel3").attr("aria-hidden","true");
            $("#panel4").attr("aria-hidden","true");
            $("#panel5").attr("aria-hidden","false");
        });
        $("#tab1").keydown(function(){
            $("#tab2").removeClass("active");
            $("#tab3").removeClass("active");
            $("#tab1").addClass("active");
            $("#tab4").removeClass("active");
            $("#panel2").attr("aria-hidden","true");
            $("#panel3").attr("aria-hidden","true");
            $("#panel1").attr("aria-hidden","false");
            $("#panel4").attr("aria-hidden","true");
            $("#panel5").attr("aria-hidden","true");
        });
       $("#tab2").keydown(function(){
            $("#tab1").removeClass("active");
            $("#tab3").removeClass("active");
            $("#tab2").addClass("active");
            $("#tab4").removeClass("active");
            $("#panel1").attr("aria-hidden","true");
            $("#panel3").attr("aria-hidden","true");
            $("#panel2").attr("aria-hidden","false");
            $("#panel4").attr("aria-hidden","true");
            $("#panel5").attr("aria-hidden","true");
        });
        $("#tab3").keydown(function(){
            $("#tab1").removeClass("active");
            $("#tab2").removeClass("active");
            $("#tab3").addClass("active");
            $("#tab4").removeClass("active");
            $("#panel1").attr("aria-hidden","true");
            $("#panel2").attr("aria-hidden","true");
            $("#panel3").attr("aria-hidden","false");
            $("#panel4").attr("aria-hidden","true");
            $("#panel5").attr("aria-hidden","true");
        });
        $("#tab4").keydown(function(){
            $("#tab1").removeClass("active");
            $("#tab2").removeClass("active");
            $("#tab3").removeClass("active");
            $("#tab4").addClass("active");
            $("#panel1").attr("aria-hidden","true");
            $("#panel2").attr("aria-hidden","true");
            $("#panel3").attr("aria-hidden","true");
            $("#panel4").attr("aria-hidden","false");
            $("#panel5").attr("aria-hidden","true");
        });  
        $("#tab5").keydown(function(){
            $("#tab1").removeClass("active");
            $("#tab2").removeClass("active");
            $("#tab3").removeClass("active");
            $("#tab4").removeClass("active");
            $("#tab4").addClass("active");
            $("#panel1").attr("aria-hidden","true");
            $("#panel2").attr("aria-hidden","true");
            $("#panel3").attr("aria-hidden","true");
            $("#panel4").attr("aria-hidden","true");
            $("#panel5").attr("aria-hidden","false");
        });     
    if(Cookies.get('activetab') == ''){ 
        $("#tab1").addClass("active");
        $("#panel1").attr("aria-hidden","false");
        Cookies.set('activetab', 'tab1');
     } 
     


    // go back to previously select tab panel
    
    var tab = getUrlVars()["tab"];
    if(tab >1){
        $("#tab"+tab).attr("aria-selected","true").focus();
        $("#panel"+tab).attr("aria-hidden","false");
        $("#tab1").attr("aria-selected","false");
        $("#panel1").attr("aria-hidden","true");
    }
    function getUrlVars(){
        var vars = [], hash;
        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        for(var i = 0; i < hashes.length; i++)
        {
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        return vars;
    }
    
    function saveEvent(editableObj,column,id) {
	//$(editableObj).css("background","#FFF url(loaderIcon.gif) no-repeat right");
        $.ajax({
            url: "save_event.php",
            type: "POST",
            data:'column='+column+'&editval='+editableObj.innerHTML+'&id='+id,
            success: function(data){
                $(editableObj).css("background","#FDFDFD");
            }        
        });
    }
    });