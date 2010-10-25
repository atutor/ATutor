;(function($){
/**
 * jqGrid Bulgarian Translation 
 * Tony Tomov tony@trirand.com
 * http://trirand.com/blog/ 
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
**/
$.jgrid = {};

$.jgrid.defaults = {
	recordtext: "запис(а)",
	loadtext: "Зареждам...",
	pgtext : "от"
}
$.jgrid.search = {
    caption: "Търсене...",
    Find: "Намери",
    Reset: "Изчисти",
    odata : ['равно', 'различно', 'по-малко', 'по-малко или=','по-голямо','по-голямо или =', 'започва с','завършва с','съдържа' ]
};
$.jgrid.edit = {
    addCaption: "Нов Запис",
    editCaption: "Редакция Запис",
    bSubmit: "Запиши",
    bCancel: "Изход",
	bClose: "Затвори",
    processData: "Обработка...",
    msg: {
        required:"Полето е задължително",
        number:"Въведете валидно число!",
        minValue:"стойността трябва да е по-голяма или равна от",
        maxValue:"стойността трябва да е по-малка или равна от",
        email: "не е валиден ел. адрес",
        integer: "Въведете балидно цяло число"
    }
};
$.jgrid.del = {
    caption: "Изтриване",
    msg: "Да изтрия ли избраният запис?",
    bSubmit: "Изтрий",
    bCancel: "Отказ",
    processData: "Обработка..."
};
$.jgrid.nav = {
	edittext: " ",
    edittitle: "Редакция избран запис",
	addtext:" ",
    addtitle: "Добавяне нов запис",
    deltext: " ",
    deltitle: "Изтриване избран запис",
    searchtext: " ",
    searchtitle: "Търсене запис(и)",
    refreshtext: "",
    refreshtitle: "Обнови таблица",
    alertcap: "Предупреждение",
    alerttext: "Моля, изберете запис"
};
// set column module
$.jgrid.col ={
    caption: "Колони",
    bSubmit: "Запис",
    bCancel: "Изход"	
};
$.jgrid.errors = {
	errcap : "Грешка",
	nourl : "Няма посочен url адрес"
	norecords: "Няма запис за обработка"	
};
})(jQuery);
