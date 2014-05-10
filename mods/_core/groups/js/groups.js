function popup_open(group_id)
{
    $("#group_"+group_id).dialog("open");
}
    
$(document).ready(function()
{
    $('.group_dialog').each(function(){
        $(this).dialog({
            autoOpen: false,
            width: 400,
            modal: true
        })
    })
});
