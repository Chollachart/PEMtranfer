<table class="table-main" border="1">
	<thead>
		<tr>
			<td>NO.</td>
			<td>ITEM</td>
			<td>ITEM DESCRIPTION</td>
			<td>WH02(PC)</td>
			<td>WH02(SET)</td>
			<td>WH103(PC)</td>
			<td>WH103(SET)</td>
			<td>USE/SET</td>
		</tr>
	</thead>
	<tbody>
	<?php
		$i=0;
		while($i<sizeof($_POST['itemData'])){
			$row_data = $_POST['itemData'][$i];
			echo '<tr>';
				echo '<td>'.($i+1).'</td>';
				echo '<td>'.$row_data['Itemcode'].'</td>';
				echo '<td>'.$row_data['Description'].'</td>';
				echo '<td>'.number_format($row_data['WH02_PC'],2).'</td>';
				echo '<td>';
					if($row_data['USE_PER_SET']!=NULL&&$row_data['USE_PER_SET']!=0){
						echo number_format(($row_data['WH02_PC']/$row_data['USE_PER_SET']),2);
					}
				echo '</td>';
				echo '<td>'.number_format($row_data['W103_PC'],2).'</td>';
				echo '<td>';
					if($row_data['USE_PER_SET']!=NULL&&$row_data['USE_PER_SET']!=0){
						echo number_format(($row_data['W103_PC']/$row_data['USE_PER_SET']),2);
					}
				echo '</td>';
				echo '<td>';
					echo ($row_data['USE_PER_SET']==NULL)?"":number_format($row_data['USE_PER_SET'],2);
				echo '</td>';
			echo '</tr>';
			$i++;
		}
	?>		
	</tbody>
</table>