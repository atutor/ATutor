function insertNewline(obj)
{
  if(obj.document.selection.type=='Control') return;
  var Range = obj.document.selection.createRange();
  if(!Range.duplicate) return;
  Range.pasteHTML('<br>.');
  Range.findText('.',10,5)
  Range.select();
  obj.curword=Range.duplicate();
  obj.curword.text = ''; 
  Range.select();
}

function insertNewParagraph(obj)
{
  if(obj.document.selection.type=='Control') return;
  var Range = obj.document.selection.createRange();
  if(!Range.duplicate) return;

  var parent=Range.parentElement()
  var tagLI= 0
  while(parent && parent.tagName!='BODY')
  {
	if(parent.tagName=='LI'){ tagLI=1; break }
	parent= parent.parentElement
  }

  if(tagLI) Range.pasteHTML('<LI>.');
  else Range.pasteHTML('<P>.');

  Range.findText('.',10,5)
  Range.select();
  obj.curword=Range.duplicate();
  obj.curword.text = '' ;
  Range.select();

}