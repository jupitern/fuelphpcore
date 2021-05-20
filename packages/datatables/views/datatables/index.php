
<div class="table-responsive">
<table id="<?= $instanceName ?>" class="table table-striped table-bordered gridTable" cellpadding="0" cellspacing="0" border="0" >
    <thead>
        <tr>
        <?php
            foreach ((array)$columns as $column) {
                $headerOptions = "";
                if( isset($column['headerOptions']) ){
                    foreach( $column['headerOptions'] as $option => $value ) $headerOptions .= $option.'="'.$value.'" ';
                }
				echo '<th '.$headerOptions.'>'. $column['header'] .'</th>';
            }
            
            if(count((array)$hasActions))
                echo '<th class="header_actions"></th>';
        ?>
        </tr>
        <tr>
        <?php
			// filters
			$i = 0;
			foreach ($columns as $column){
				
				echo '<td align="center">';
				
				if( isset($column['filter']) ){
					if( is_array($column['filter']) ){
						echo '<select class="form-control input-sm" data-index="'.$i.'">
								<option value=""></option>';
						foreach( $column['filter'] as $key => $value ){
							echo '<option value="'.$key.'" style="width: 90%;">'.$value.'</option>';
						}
						echo '</select>';
					}
					elseif( $column['filter'] !== null ){
						echo '<input class="form-control input-sm" type="text" data-index="'.$i.'" />';
					}
				}
				else{
					echo '<input class="form-control input-sm" type="text" data-index="'.$i.'" />';
				}
				
				echo '</td>';
				++$i;
			}
            
            if(count((array)$hasActions))
                echo '<td align="center">&nbsp;</td>';
        ?>
        </tr>
    </thead>
    <tbody></tbody>
</table>
</div>

<script type="text/javascript">

    $(document).ready(function() {
        var <?= $instanceName ?> = datatable_init( "<?= $instanceName ?>", "<?= $dataSourceUrl ?>" );
    });

</script>
