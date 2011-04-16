<?php
include ('../define.conf');
?>
<!-- 	INSERT JAVASCRIPT HEAD TAGS HERE -->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo SRCROOT; ?>/js/jquery-ui.js" type="text/javascript"></script>
<script src="<?php echo SRCROOT; ?>/js/tablesort.js" type="text/javascript"></script>
<script src="<?php echo SRCROOT; ?>/js/picnet.table.filter.min.js"  type="text/javascript"></script>
<script type="text/javascript" charset="utf-8">
<!-- Begin
 function putFocus(formInst, elementInst) {
  if (document.forms.length > 0) {
   document.forms[formInst].elements[elementInst].focus();
  }
 }
// The second number in the "onLoad" command in the body
// tag determines the forms focus.
//  End -->
</script>
<script type="text/javascript" charset="utf-8">
$(function() {
    $('.opener').click(function(e) {
        e.preventDefault();
        var $this = $(this);
        var horizontalPadding = 30;
        var verticalPadding = 30;
        $('<div id="outerdiv"><iframe id="externalSite" class="externalSite" src="' + this.href + '" />').dialog({
            title: ($this.attr('title')) ? $this.attr('title') : 'Instant Item Editor',
            autoOpen: true,
            width: 560,
            height: 700,
            modal: true,
            resizable: true,
            autoResize: true,
            overlay: {
                opacity: 0.5,
                background: "black"
            }
        }).width(560 - horizontalPadding).height(700 - verticalPadding);            
    });
});
</script>

<!-- 	INSERT CSS HEAD TAGS HERE -->
<link rel="stylesheet" href="<?php echo SRCROOT; ?>/style.css" type="text/css" />
<!-- <link rel="stylesheet" href="<?php //echo SRCROOT; ?>/tablesort.css" type="text/css" /> -->
<link rel="stylesheet" href="<?php echo SRCROOT; ?>/js/css/fannie/jquery-ui-1.8.6.custom.css" type="text/css" />