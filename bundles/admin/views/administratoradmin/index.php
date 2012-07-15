			<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2>Administrateurs</h2>
					<ul>
						<li><a href="administrators/new">Ajouter</a></li>
					</ul>
					<?php
					//~ $form = Form::create()->open('', 'get');
					//~ $form->input('search', '', array('placeholder' => 'Search'));
					//~ $form->close();
					?>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<div class="block small left" style="width:79%">
			
						<div class="block_head">
							<div class="bheadl"></div>
							<div class="bheadr"></div>
							
							<h2><a href="administrators">Liste</a></h2>	
						</div>		<!-- .block_head ends -->
						
						
						
						<div class="block_content">
					<?php Messenger::getInstance()->showAll() ?>
				
					<form action="" method="post">
						<table cellpadding="0" cellspacing="0" width="100%" class="sortable">
							<thead>
								<tr>
									<th width="10"><input type="checkbox" class="check_all" /></th>
									<th>Utilisateur</th>
									<td>&nbsp;</td>
								</tr>
							</thead>
							
							<tbody>
								<?php
								foreach($administrators as $administrator) { ?>								
									<tr>
										<td><input type="checkbox" name="id[]" value="<?php echo $administrator->id ?>" /></td>
										<td><a href="administrators/<?php echo $administrator->id ?>/edit"><?php echo $administrator ?></a></td>
											<td class="actions">
													<?php Event::trigger_show('coxis_administrator_actions', $administrator) ?>
													<a class="delete" href="administrators/<?php echo $administrator->id ?>/delete">Supprimer</a>
												</td>
									</tr>
								<?php } ?>
							</tbody>
							
						</table>
						
						<div class="tableactions">
							<select name="action">
								<option>Actions</option>
								<option value="delete">Supprimer</option>
							</select>
							
							<input type="submit" class="submit tiny" value="Appliquer" />
						</div>		<!-- .tableactions ends -->
						
						<?php
						if(isset($paginator))
						if($paginator->getPages()>1) {
						?>
						<div class="pagination right">
							<?php $paginator->show() ?>
						</div>
						<?php
						}
						?>
						
					</form>
				
						</div>		<!-- .block_content ends -->
						
						<div class="bendl"></div>
						<div class="bendr"></div>
						
					</div>
					
					<div class="block small right" style="width:19%">
			
						<div class="block_head">
							<div class="bheadl"></div>
							<div class="bheadr"></div>
							
							<h2>Filters</h2>
						</div>		<!-- .block_head ends -->
						
						
						
						<div class="block_content">
						
							<?php
							//~ $form = FilterForm::create()->open('', 'get');
							//~ $form->input('username', 'Utilisateur');
							//~ $form->close();
							?>
							
						</div>		<!-- .block_content ends -->
						
						<div class="bendl"></div>
						<div class="bendr"></div>
						
					</div>
				
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->