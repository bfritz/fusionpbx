<script type="text/javascript">
function submitform()
{
    if(document.flashform.onsubmit &&
    !document.flashform.onsubmit())
    {
        return;
    }
 document.flashform.submit();
}
</script>

<div>
<form action="/flashfoner/phone.php" name="flashform">
<select id="extension_id">
<?php 
foreach($extension_array as $row)
printf('<option value="%s">%s</option>'."\n", $row['extension_id'], $row['extension']);
?>
</select>
</form>
<a href='javascript:var testvar = document.getElementById("extension_id").value; window.open("/flashfoner/phone.php?extension_id="+testvar, "FlashPhoner", "height=350, width=150");'>
<img src="phone.jpg" /><br /> Click here to Open Your Phone</a>
<div>
