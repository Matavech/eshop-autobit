<?php
/**
 * @var array $columns
 * @var $content
 */
if(is_array($columns))
{
?>
<table>
	<caption> Редактирование продукции</caption>
	<tr>
		<?php foreach ($columns as $column) {?>
		<th><?= $column?></th>
		<?php } ?>
		<th class="td__change"></th>

	</tr>
		<?php } echo $content; ?>
</table>
<?= $pagination ?>