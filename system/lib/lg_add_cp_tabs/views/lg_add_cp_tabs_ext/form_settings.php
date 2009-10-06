<!-- EXTENSION ACCESS -->
<div class="tg">
	<h2><?php echo str_replace("{addon_name}", $this->name, $LANG->line("enable_extension_title")); ?></h2>
	<table>
		<tbody>
			<tr class="even">
				<th>
					<?php echo str_replace("{addon_name}",  $this->name, $LANG->line("enable_extension_label")); ?>
				</th>
				<td>
					<?php print $this->select_box(
						$settings["enabled"],
						array("1" => "yes", "0" => "no"),
						"Lg_add_cp_tabs_ext[enabled]"
					); ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<div class="tg">
	<h2><?php echo $LANG->line("member_groups_defaults_title") ?></h2>
	<div class="info"><?php echo $LANG->line("member_groups_defaults_info"); ?></div>
	<table>
		<thead>
			<tr>
				<th>Member group</th>
				<th>Tabs</th>
				<th>Links</th>
			</tr>
		</thead>
		<tbody>
			<? 
				foreach($member_group_query->result as $count => $member_group ) :
				$class = $count % 2 ? "odd" : "even";
				$tabs = (isset($settings["member_group_prefs"][$member_group["group_id"]]["tabs"])) ? $settings["member_group_prefs"][$member_group["group_id"]]['tabs'] : "";
				$links = (isset($settings["member_group_prefs"][$member_group["group_id"]]["links"])) ? $settings["member_group_prefs"][$member_group["group_id"]]['links'] : "";
			?>
			<tr class='<?php echo $class; ?>'>
				<th><?php echo $member_group["group_title"]; ?></th>
				<td><textarea rows="3" name="Lg_add_cp_tabs_ext[member_group_prefs][<?php echo $member_group["group_id"]; ?>][tabs]"><?php print $REGX->form_prep($tabs) ?></textarea></td>
				<td><textarea rows="3" name="Lg_add_cp_tabs_ext[member_group_prefs][<?php echo $member_group["group_id"]; ?>][links]"><?php print $REGX->form_prep($links) ?></textarea></td>
			</tr>
			<? endforeach; ?>
		</tbody>
	</table>
</div>


<!-- CHECK FOR UPDATES -->
<div class="tg">
	<h2><?php echo $LANG->line("check_for_updates_title") ?></h2>
	<div class="info"><?php echo str_replace("{addon_name}", $this->name, $LANG->line("check_for_updates_info")); ?></div>
	<table>
		<tbody>
			<tr class="even">
				<th><?php echo $LANG->line("check_for_updates_label") ?></th>
				<td>
					<select<?php if(!$lgau_enabled) : ?> disabled="disabled"<?php endif; ?> name="Lg_add_cp_tabs_ext[check_for_updates]">
						<option value="1"<?php echo ($settings["check_for_updates"] == TRUE && $lgau_enabled === TRUE) ? 'selected="selected"' : ''; ?>>
							<?php echo $LANG->line("yes") ?>
						</option>
						<option value="0"<?php echo ($settings["check_for_updates"] == FALSE || $lgau_enabled === FALSE) ? 'selected="selected"' : ''; ?>>
							<?php echo $LANG->line("no") ?>
						</option>
					</select>
					<?php if(!$lgau_enabled) : ?>
						&nbsp;
						<span class='highlight'>LG Addon Updater is not installed and activated.</span>
						<input type="hidden" name="Lg_add_cp_tabs_ext[check_for_updates]" value="0" />
					<? endif; ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
