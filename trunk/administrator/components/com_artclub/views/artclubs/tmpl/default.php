<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php
    ini_set('display_errors', 1);
    error_reporting(E_ERROR);


require_once(dirname(__FILE__).'/../../../../../../catalogue.php');
$catnums = new CatalogueNumbers;
?>

<script>
function doEdit(content_id) {
    elem = document.getElementById("content_id");
    if (elem) {
        elem.value = content_id;
    }
    elem = document.getElementById("layout");
    if (elem) {
        elem.value = 'form';
    }
    document.forms['adminForm'].submit();
}
</script>

<form action="index.php" method="post" name="adminForm">
<div id="editcell">
    <table class="adminlist">

    <thead>
        <tr>
            <th width="5">
                <?php echo JText::_( 'ID' ); ?>
            </th>
            <th>
                <?php echo JText::_( 'Image' ); ?>
            </th>
            <th>
                <?php echo JText::_( 'Artist' ); ?>
            </th>
            <th>
                <?php echo JText::_( 'Title' ); ?>
            </th>
            <th>
                <?php echo JText::_( 'Exhbition Date' ); ?>
            </th>
            <th>
                <?php echo JText::_( 'Catalogue Number' ); ?>
            </th>
            <th>
                <?php echo JText::_( 'Date' ); ?>
            </th>
            <th>
                <?php echo JText::_( 'Gallery' ); ?>
            </th>
            <th>
                <?php echo JText::_( 'Approved' ); ?>
            </th>
        </tr>
    </thead>
    <?php
    $k = 0;
    $i = 0;
    foreach ($this->items as &$row)
    {
        if ($i && $row->exhibition_date != $last) {
            echo '<tr><td colspan="9">&nbsp;</td></tr>';
        }
        $last = $row->exhibition_date;
        ?>
        <tr class="<?php echo "row" . $k; if ($row->catnum) echo " locked";  ?>"
<?php
if (!$row->catnum) :
?>
   onclick="doEdit('<?php echo $row->cf_id; ?>');"
<?php
endif;
?>
>
            <td>
                <?php
                    echo $row->cf_id;
                ?>
            </td>
            <td align="center">
                <img src="<?php echo '/images/stories/artwork/' . $row->filename . "_med.jpg"; ?>" />
            </td>
            <td>
                <?php echo $row->artist; ?>
            </td>
            <td>
                <?php echo $row->title; ?>
            </td>
            <td>
                <?php echo $row->exhibition_date; ?>
            </td>
            <td>
                <?php echo $catnums->from_id($row->cf_id); ?>
            </td>
            <td>
                <?php echo $row->print_date; ?>
            </td>
            <td>
                <?php echo $row->holding_gallery; ?>
            </td>
            <td>
                <?php echo $row->approved ? "<font color='green'>Approved</font>" : "<font color='red'>Pending Approval</font>"; ?>
            </td>
        </tr>
        <?php
        $k = 1 - $k;
        $i++;
    }
    ?>
  <tfoot>
    <tr>
      <td colspan="6"><?php echo $this->pagination->getListFooter(); ?></td>
    </tr>
  </tfoot>
    </table>
</div>

<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_artclub" />
<input type="hidden" name="model_type" value="<?php echo $this->model_type; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" id="content_id" name="content_id" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="artclubs" />
<input type="hidden" name="view" value="artclubs" />
<input type="hidden" id="layout" name="layout" value="default" />

</form>
