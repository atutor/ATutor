jQuery(document).ready(function(){
        if($("div[role='tabpanel'], panel1").attr("aria-hidden") == "false"){
            $("#tab1").addClass("tabactive");
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
        $("#tab1").removeClass("tabactive");
        $("#tab3").removeClass("tabactive");
        $("#tab2").addClass("tabactive");
        $("#tab4").removeClass("tabactive");
        $("#tab5").removeClass("tabactive");
        $("#panel1").attr("aria-hidden","true");
        $("#panel3").attr("aria-hidden","true");
        $("#panel2").attr("aria-hidden","false");
        $("#panel4").attr("aria-hidden","true");
        $("#panel5").attr("aria-hidden","true");
    } else if(Cookies.get('activetab') == "tab3"){
        $("#tab1").removeClass("tabactive");
        $("#tab2").removeClass("tabactive");
        $("#tab3").addClass("tabactive");
        $("#tab4").removeClass("tabactive");
        $("#tab5").removeClass("tabactive");
        $("#panel3").attr("aria-hidden","false");
        $("#panel1").attr("aria-hidden","true");
        $("#panel2").attr("aria-hidden","true");
        $("#panel4").attr("aria-hidden","true");
        $("#panel5").attr("aria-hidden","true");
    } else if(Cookies.get('activetab') == "tab4"){
        if(!$("#tab4").length){
            $("#tab1").focus();
            $("#tab2").removeClass("tabactive");
            $("#tab3").removeClass("tabactive");
            $("#tab1").addClass("tabactive");
            $("#tab4").removeClass("tabactive");
            $("#tab5").removeClass("tabactive");
            $("#panel2").attr("aria-hidden","true");
            $("#panel3").attr("aria-hidden","true");
            $("#panel1").attr("aria-hidden","false");
            $("#panel4").attr("aria-hidden","true");
            $("#panel5").attr("aria-hidden","true");
        } else{
            $("#tab2").removeClass("tabactive");
            $("#tab3").removeClass("tabactive");
            $("#tab1").removeClass("tabactive");
            $("#tab4").addClass("tabactive");
            $("#tab5").removeClass("tabactive");
            $("#panel2").attr("aria-hidden","true");
            $("#panel3").attr("aria-hidden","true");
            $("#panel1").attr("aria-hidden","true");
            $("#panel4").attr("aria-hidden","false");
            $("#panel5").attr("aria-hidden","true");
        }
    } else if (Cookies.get('activetab') == "tab5"){
        $("#tab1").removeClass("tabactive");
        $("#tab2").removeClass("tabactive");
        $("#tab3").removeClass("tabactive");
        $("#tab4").removeClass("tabactive");
        $("#tab5").addClass("tabactive");
        $("#panel3").attr("aria-hidden","true");
        $("#panel1").attr("aria-hidden","true");
        $("#panel2").attr("aria-hidden","true");
        $("#panel4").attr("aria-hidden","true");
        $("#panel5").attr("aria-hidden","false");
    }else {
        $("#tab2").removeClass("tabactive");
        $("#tab3").removeClass("tabactive");
        $("#tab1").addClass("tabactive");
        $("#tab4").removeClass("tabactive");
        $("#tab5").removeClass("tabactive");
        $("#panel2").attr("aria-hidden","true");
        $("#panel3").attr("aria-hidden","true");
        $("#panel1").attr("aria-hidden","false");
        $("#panel4").attr("aria-hidden","true");
        
    }
     $("#tab1").click(function(){
            $("#tab2").removeClass("tabactive");
            $("#tab3").removeClass("tabactive");
            $("#tab1").addClass("tabactive");
            $("#tab4").removeClass("tabactive");
            $("#tab5").removeClass("tabactive");
            $("#panel2").attr("aria-hidden","true");
            $("#panel3").attr("aria-hidden","true");
            $("#panel1").attr("aria-hidden","false");
            $("#panel4").attr("aria-hidden","true");
            $("#panel5").attr("aria-hidden","true");
        });
       $("#tab2").click(function(){
            $("#tab1").removeClass("tabactive");
            $("#tab3").removeClass("tabactive");
            $("#tab2").addClass("tabactive");
            $("#tab4").removeClass("tabactive");
            $("#tab5").removeClass("tabactive");
            $("#panel1").attr("aria-hidden","true");
            $("#panel3").attr("aria-hidden","true");
            $("#panel2").attr("aria-hidden","false");
            $("#panel4").attr("aria-hidden","true");
            $("#panel5").attr("aria-hidden","true");
        });
        $("#tab3").click(function(){
            $("#tab1").removeClass("tabactive");
            $("#tab2").removeClass("tabactive");
            $("#tab3").addClass("tabactive");
            $("#tab4").removeClass("tabactive");
            $("#tab5").removeClass("tabactive");
            $("#panel1").attr("aria-hidden","true");
            $("#panel2").attr("aria-hidden","true");
            $("#panel3").attr("aria-hidden","false");
            $("#panel4").attr("aria-hidden","true");
            $("#panel5").attr("aria-hidden","true");
        });
        $("#tab4").click(function(){
            $("#tab1").removeClass("tabactive");
            $("#tab2").removeClass("tabactive");
            $("#tab3").removeClass("tabactive");
            $("#tab4").addClass("tabactive");
            $("#tab5").removeClass("tabactive");
            $("#panel1").attr("aria-hidden","true");
            $("#panel2").attr("aria-hidden","true");
            $("#panel3").attr("aria-hidden","true");
            $("#panel4").attr("aria-hidden","false");
            $("#panel5").attr("aria-hidden","true");
        });
        $("#tab5").click(function(){
            $("#tab1").removeClass("tabactive");
            $("#tab2").removeClass("tabactive");
            $("#tab3").removeClass("tabactive");
            $("#tab4").removeClass("tabactive");
            $("#tab5").addClass("tabactive");
            $("#panel1").attr("aria-hidden","true");
            $("#panel2").attr("aria-hidden","true");
            $("#panel3").attr("aria-hidden","true");
            $("#panel4").attr("aria-hidden","true");
            $("#panel5").attr("aria-hidden","false");
        });
        $("#tab1").keyup(function(){
            $("#tab2").removeClass("tabactive");
            $("#tab3").removeClass("tabactive");
            $("#tab1").addClass("tabactive");
            $("#tab4").removeClass("tabactive");
            $("#panel2").attr("aria-hidden","true");
            $("#panel3").attr("aria-hidden","true");
            $("#panel1").attr("aria-hidden","false");
            $("#panel4").attr("aria-hidden","true");
            $("#panel5").attr("aria-hidden","true");
        });
       $("#tab2").keyup(function(){
            $("#tab1").removeClass("tabactive");
            $("#tab3").removeClass("tabactive");
            $("#tab2").addClass("tabactive");
            $("#tab4").removeClass("tabactive");
            $("#panel1").attr("aria-hidden","true");
            $("#panel3").attr("aria-hidden","true");
            $("#panel2").attr("aria-hidden","false");
            $("#panel4").attr("aria-hidden","true");
            $("#panel5").attr("aria-hidden","true");
        });
        $("#tab3").keyup(function(){
            $("#tab1").removeClass("tabactive");
            $("#tab2").removeClass("tabactive");
            $("#tab3").addClass("tabactive");
            $("#tab4").removeClass("tabactive");
            $("#panel1").attr("aria-hidden","true");
            $("#panel2").attr("aria-hidden","true");
            $("#panel3").attr("aria-hidden","false");
            $("#panel4").attr("aria-hidden","true");
            $("#panel5").attr("aria-hidden","true");
        });
        $("#tab4").keyup(function(){
            $("#tab1").removeClass("tabactive");
            $("#tab2").removeClass("tabactive");
            $("#tab3").removeClass("tabactive");
            $("#tab4").addClass("tabactive");
            $("#tab5").removeClass("tabactive");
            $("#panel1").attr("aria-hidden","true");
            $("#panel2").attr("aria-hidden","true");
            $("#panel3").attr("aria-hidden","true");
            $("#panel4").attr("aria-hidden","false");
            $("#panel5").attr("aria-hidden","true");
        });  
        $("#tab5").keyup(function(){
            $("#tab1").removeClass("tabactive");
            $("#tab2").removeClass("tabactive");
            $("#tab3").removeClass("tabactive");
            $("#tab4").removeClass("tabactive");
            $("#tab5").addClass("tabactive");
            $("#panel1").attr("aria-hidden","true");
            $("#panel2").attr("aria-hidden","true");
            $("#panel3").attr("aria-hidden","true");
            $("#panel4").attr("aria-hidden","true");
            $("#panel5").attr("aria-hidden","false");
        });     
    if(Cookies.get('activetab') == ''){ 
        $("#tab1").addClass("tabactive");
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