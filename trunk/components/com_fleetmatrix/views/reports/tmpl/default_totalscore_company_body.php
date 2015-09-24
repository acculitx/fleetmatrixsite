<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>

<?php if (count($this->items) > 0) : ?>
    <tr class="row<?php echo 0; ?>">
        <td>
            <?php echo $this->items[0]->company_group_name; ?>
        </td>

        <td>
            <?php echo round($this->items[0]->company_group_total_score, 2); ?>
        </td>

        <td>
            <?php echo round($this->items[0]->company_group_aggressive_score, 2); ?>
        </td>

        <td>
            <?php echo round($this->items[0]->company_group_distraction_score, 2); ?>
        </td>
    </tr>
<?php endif; ?>
