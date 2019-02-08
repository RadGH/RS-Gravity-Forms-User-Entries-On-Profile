<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display the table on a user's profile
 *
 * @param $profileuser
 */
function gfue_display_entries_table( $profileuser ) {
	if ( !current_user_can('administrator') && !current_user_can('gravityforms_view_entries') ) return;
	
	$entries = gfue_get_entries_by_user( $profileuser->ID );
	?>
	<table class="form-table gfue-table">
		<tbody>
		<tr>
			<th>Gravity Form Entries</th>
			<td>
				<?php
				
				
if ( empty($entries) ) {
	echo '<p><em>No entries have been submitted by this user.</em></p>';
}else{
	?>
	<table class="gfue-user-entries">
		<thead>
		<tr>
			<th class="gfue-col-entry_id">Entry ID</th>
			<th class="gfue-col-form">Form</th>
			<th class="gfue-col-date">Date</th>
			<th class="gfue-col-fields">Field Preview</th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach( $entries as $entry_id ) {
			$entry = GFAPI::get_entry($entry_id);
			if ( !$entry || is_wp_error($entry) ) {
				echo '<td>', $entry_id, '</td><td colspan="2">This entry could not be retrieved by the Gravity Forms API.</td>';
				continue;
			}
			
			$form = GFAPI::get_form( $entry['form_id'] );
			$edit_url = add_query_arg( array('page' => 'gf_entries', 'view' => 'entry', 'id' => $entry['form_id'], 'lid' => $entry_id), admin_url('admin.php') );
			
			$date = get_date_from_gmt( $entry['date_created'], get_option('date_format') );
			
			// Preview some fields
			$fields = array();
			$field_count = 3;
			if ( $form['fields'] ) foreach( $form['fields'] as $field ) {
				if ( $field->type == 'hidden' ) continue;
				if ( $field->visibility != 'visible' ) continue;
				if ( empty($entry[$field->id]) ) continue;
				if ( $field_count < 1 ) break;
				
				$label = (string) $field['label'];
				$label = wp_strip_all_tags( $label );
				if ( strlen($label) > 28 ) $label = substr($label, 0, 23) . '';
				
				$value = (string) $entry[$field->id];
				$value = wp_strip_all_tags( $value );
				if ( strlen($value) > 28 ) $value = substr($value, 0, 23) . '';
				
				$fields[] = '<tr><td class="gfue-field__label">'. esc_html($label) . ':</td><td class="gfue-field__value">'. esc_attr($value) . '</td></tr>';
				$field_count--;
			}
			?>
			<tr>
				<td class="gfue-col-entry_id"><a href="<?php echo esc_attr($edit_url); ?>"><?php echo esc_html($entry_id); ?></a></td>
				<td class="gfue-col-form"><?php echo esc_html($form['title']); ?></td>
				<td class="gfue-col-date"><?php echo esc_html($date); ?></td>
				<td class="gfue-col-fields">
					<table class="gfue-fields-table"><?php echo implode( '', $fields ); ?></table>
				</td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
	<?php
}


				?>
			</td>
		</tr>
		</tbody>
	</table>
	<?php
}
add_action( 'show_user_profile', 'gfue_display_entries_table', 5 );
add_action( 'edit_user_profile', 'gfue_display_entries_table', 5 );

/**
 * Gets gravity form entries for the given user. This includes entries where the user was logged in when they submitted the entry, and entries
 * that were used to create their account with the GF User Registration Addon.
 *
 * @param $user_id
 *
 * @return array
 */
function gfue_get_entries_by_user( $user_id ) {
	global $wpdb;
	
	$entries = array();
	
	$where_extra = "and entry.status = 'active'";
	$where_extra = apply_filters( 'gfue/get_entries_by_user/where', $where_extra, $user_id );
	
	// 1. Get entries submitted when the user was logged in, by looking at the "created_by" column. Similar to post_author in normal post types.
	$sql = $wpdb->prepare("
select entry.id
from {$wpdb->prefix}gf_entry entry
where entry.created_by = %d
$where_extra",
		$user_id
	);
	
	$sql = apply_filters( 'gfue/get_entries_by_user/sql_author', $sql );
	
	if ( $sql ) {
		$e = $wpdb->get_col( $sql );
		if ( $e ) $entries = array_merge( $entries, $e );
	}
	
	// 2. Get entries that created the user's account via User Registration Addon.
	$sql = $wpdb->prepare("
select entry.id

from {$wpdb->prefix}gf_entry entry
left join {$wpdb->prefix}usermeta registration_entry
on registration_entry.meta_key = '_gform-entry-id' and registration_entry.meta_value = entry.id

where registration_entry.user_id = %d
$where_extra",
		$user_id
	);
	
	$sql = apply_filters( 'gfue/get_entries_by_user/sql_user_registration', $sql );
	
	if ( $sql ) {
		$e = $wpdb->get_col( $sql );
		if ( $e ) $entries = array_merge( $entries, $e );
	}
	
	$entries = apply_filters( 'gfue_get_entries_by_user/result', $entries, $user_id );
	
	return $entries;
}