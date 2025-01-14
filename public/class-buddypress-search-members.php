<?php
/**
 * BuddyPress search members.
 *
 * @todo    add description
 *
 * @package BuddyPress_Search
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'BuddyPress_Search_Members' ) ) :

	/**
	 * BuddyPress Global Search  - search members class
	 */
	class BuddyPress_Search_Members extends BuddyPress_Search_Type {

		/**
		 * Insures that only one instance of Class exists in memory at any
		 * one time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 *
		 * @var $type Members
		 */
		private $type = 'members';

		/**
		 * Insures that only one instance of Class exists in memory at any
		 * one time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 *
		 * @return object BuddyPress_Search_Members
		 */
		public static function instance() {
			// Store the instance locally to avoid private static replication.
			static $instance = null;
			global $sql_xprofile_result_temp;
			$sql_xprofile_result_temp = '';
			// Only run these methods if they haven't been run previously.
			if ( null === $instance ) {
				$instance = new BuddyPress_Search_Members();

				add_action( 'bp_search_settings_item_members', array( $instance, 'print_search_options' ) );
			}

			// Always return the instance.
			return $instance;
		}

		/**
		 * A dummy constructor to prevent this class from being loaded more than once.
		 *
		 * @since 1.0.0
		 */
		private function __construct() {
			/* Do nothing here */
		}

		/**
		 * Generates sql for members search.
		 *
		 * @todo  : if Mr.X has set privacy of xprofile field 'location' data as 'private'
		 * then, location of Mr.X shouldn't be checked in searched.
		 *
		 * @since 1.0.0
		 *
		 * @param string  $search_term seach text.
		 * @param boolean $only_totalrow_count Total Row Count.
		 *
		 * @return string sql query
		 */
		public function sql( $search_term, $only_totalrow_count = false ) {
			global $wpdb, $bp, $sql_xprofile_result_temp;

			$bpsearch_bp_search = get_option( 'bpsearch_bp_search' );

			$query_placeholder = array();

			$COLUMNS = ' SELECT ';

			if ( $only_totalrow_count ) {
				$COLUMNS .= ' COUNT( DISTINCT u.id ) ';
			} else {
				$COLUMNS            .= " DISTINCT u.id, 'members' as type, u.display_name LIKE %s AS relevance, a.date_recorded as entry_date ";
				$query_placeholder[] = '%' . $search_term . '%';
			}

			$FROM = " {$wpdb->users} u LEFT JOIN {$bp->members->table_name_last_activity} a ON a.user_id=u.id AND a.component = 'members' AND a.type = 'last_activity'";

			/**
			 * Filter the MySQL JOIN clause for the Member Search query.
			 *
			 * @since 1.0.0
			 *
			 * @param string $join_sql JOIN clause.
			 * @param string $uid_name User ID field name.
			 */
			$FROM = apply_filters( 'bp_user_search_join_sql', $FROM, 'id' );

			$WHERE        = array();
			$WHERE[]      = '1=1';
			$WHERE[]      = 'u.user_status = 0';
			$where_fields = array();

			/*
			 * wp_users table fields
			 **/
			$user_fields = buddypress_search_get_user_fields();
			if ( ! empty( $user_fields ) ) {
				$conditions_wp_user_table = array();
				foreach ( $user_fields as $user_field => $field_label ) {

					if ( ! isset( $bpsearch_bp_search['bp_search_member'][ $user_field ] ) ) {
						continue;
					}

					if ( 'user_meta' === $user_field ) {
						// Search in user meta table for terms.
						$conditions_wp_user_table[] = " ID IN ( SELECT user_id FROM {$wpdb->usermeta} WHERE ExtractValue(meta_value, '//text()') LIKE %s AND meta_key NOT IN( 'first_name', 'last_name', 'nickname' ) ) ";
						$query_placeholder[]        = '%' . $search_term . '%';
					} else {
						$conditions_wp_user_table[] = $user_field . ' LIKE %s ';
						$query_placeholder[]        = '%' . $search_term . '%';
					}
				}

				if ( ! empty( $conditions_wp_user_table ) ) {

					$clause_wp_user_table  = "u.id IN ( SELECT ID FROM {$wpdb->users}  WHERE ( ";
					$clause_wp_user_table .= implode( ' OR ', $conditions_wp_user_table );
					$clause_wp_user_table .= ' ) ) ';

					$where_fields[] = $clause_wp_user_table;
				}
			}
			/* _____________________________ */

			// get all selected xprofile fields.
			if ( function_exists( 'bp_is_active' ) && bp_is_active( 'xprofile' ) ) {
				$groups = bp_xprofile_get_groups(
					array(
						'fetch_fields'                   => true,
						'repeater_show_main_fields_only' => true,
					)
				);

				if ( ! empty( $groups ) ) {
					$selected_xprofile_fields = array(
						'word_search' => array( 0 ), // Search for whole word in field of type checkbox and radio.
						'char_search' => array( 0 ), // Search for character in field of type textbox, textarea and etc.
					);

					$selected_xprofile_repeater_fields = array();

					$word_search_field_type = array( 'radio', 'checkbox' );

					foreach ( $groups as $group ) {
						if ( ! empty( $group->fields ) ) {
							foreach ( $group->fields as $field ) {
								if ( isset( $bpsearch_bp_search['bp_search_member'][ $field->id ] ) ) {
									$repeater_enabled = bp_xprofile_get_meta( $field->group_id, 'group', 'is_repeater_enabled', true );

									if ( ! empty( $repeater_enabled ) && 'on' === $repeater_enabled ) {
										$selected_xprofile_repeater_fields = array_unique(
											array_merge(
												$selected_xprofile_repeater_fields,
												bp_get_repeater_clone_field_ids_all( $field->group_id )
											)
										);
									} else {
										if ( in_array( $field->type, $word_search_field_type ) ) {
											$selected_xprofile_fields['word_search'][] = $field->id;
										} else {
											$selected_xprofile_fields['char_search'][] = $field->id;
										}
									}
								}
							}
						}
					}

					// added repeater support based on privacy.
					if ( ! empty( $selected_xprofile_repeater_fields ) ) {
						$selected_xprofile_repeater_fields = array_unique( $selected_xprofile_repeater_fields );
						foreach ( $selected_xprofile_repeater_fields as $field_id ) {
							$field_object = new BP_XProfile_Field( $field_id );
							if ( in_array( $field_object->type, $word_search_field_type ) ) {
								$selected_xprofile_fields['word_search'][] = $field_object->id;
							} else {
								$selected_xprofile_fields['char_search'][] = $field_object->id;
							}
						}
					}

					if ( ! empty( $selected_xprofile_fields ) ) {

						$data_clause_xprofile_table  = "( SELECT field_id, user_id FROM {$bp->profile->table_name_data} WHERE field_id IN ( ";
						$data_clause_xprofile_table .= implode( ',', $selected_xprofile_fields['char_search'] );
						$data_clause_xprofile_table .= ') OR ( value LIKE "%%%s%" AND field_id IN ( ';
						$data_clause_xprofile_table .= implode( ',', $selected_xprofile_fields['word_search'] );
						$data_clause_xprofile_table .= ') ) )';
						$sql_xprofile                = $wpdb->prepare( $data_clause_xprofile_table, '%' . $wpdb->esc_like( $search_term ) . '%' );

						if ( $sql_xprofile_result_temp == '' ) {
							$sql_xprofile_result_temp = $sql_xprofile_result = $wpdb->get_results( $sql_xprofile );
						} else {
							$sql_xprofile_result = $sql_xprofile_result_temp;
						}

						$user_ids = array();

						// check visiblity for field id with current user.
						if ( ! empty( $sql_xprofile_result ) ) {
							foreach ( $sql_xprofile_result as $field_data ) {
								$hidden_fields = bp_xprofile_get_hidden_fields_for_user( $field_data->user_id, bp_loggedin_user_id() );

								if (
									( ! empty( $hidden_fields )
									&& ! in_array( $field_data->field_id, $hidden_fields )
									)
									|| empty( $hidden_fields )
								) {
									$user_ids[] = $field_data->user_id;
								}
							}
						}

						// Added user when visbility matched.
						if ( ! empty( $user_ids ) ) {
							$user_ids       = array_unique( $user_ids );
							$where_fields[] = 'u.id IN ( ' . implode( ',', $user_ids ) . ' )';
						} else {
							$where_fields[] = 'u.id = 0';
						}
					}
				}
			}
			/* _____________________________ */

			// Search from search string.
			$split_search_term = explode( ' ', $search_term );

			if ( count( $split_search_term ) > 1 ) {

				$clause_search_string_table = "u.id IN ( SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = 'bbgs_search_string' AND (";

				foreach ( $split_search_term as $k => $sterm ) {

					if ( $k == 0 ) {
						$clause_search_string_table .= ' meta_value LIKE %s ';
						$query_placeholder[]         = '%' . $sterm . '%';
					} else {
						$clause_search_string_table .= ' OR meta_value LIKE %s ';
						$query_placeholder[]         = '%' . $sterm . '%';
					}
				}

				$clause_search_string_table .= ') ) ';

				$where_fields[] = $clause_search_string_table;

			}

			/* _____________________________ */

			if ( ! empty( $where_fields ) ) {
				$WHERE[] = '(' . implode( ' OR ', $where_fields ) . ')';
			}

			// other conditions
			// $WHERE[] = " a.component = 'members' ";
			// $WHERE[] = " a.type = 'last_activity' ";

			/**
			 * Filters the MySQL WHERE conditions for the member Search query.
			 *
			 * @since 1.0.0
			 *
			 * @param array  $where_conditions Current conditions for MySQL WHERE statement.
			 * @param string $uid_name         User ID field name.
			 */
			$WHERE = apply_filters( 'bp_user_search_where_sql', $WHERE, 'id' );

			$sql = $COLUMNS . ' FROM ' . $FROM . ' WHERE ' . implode( ' AND ', $WHERE );
			if ( ! $only_totalrow_count ) {
				$sql .= ' GROUP BY u.id ';
			}

			if ( ! empty( $query_placeholder ) ) {
				$sql = $wpdb->prepare( $sql, $query_placeholder );
			}

			return apply_filters(
				'buddypress_search_members_sql',
				$sql,
				array(
					'search_term'         => $search_term,
					'only_totalrow_count' => $only_totalrow_count,
				)
			);
		}

		/**
		 * Generate BuddyPress members template HtML
		 *
		 * @param  mixed $template_type template Type.
		 * @return void
		 */
		protected function generate_html( $template_type = '' ) {
			$group_ids = array();

			foreach ( $this->search_results['items'] as $item_id => $item ) {
				$group_ids[] = $item_id;
			}

			do_action( 'bp_before_search_members_html' );

			// now we have all the posts
			// lets do a groups loop.
			if ( bp_has_members(
				array(
					'search_terms' => '',
					'include'      => $group_ids,
					'per_page'     => count( $group_ids ),
				)
			) ) {
				while ( bp_members() ) {
					bp_the_member();

					$result_item = array(
						'id'    => bp_get_member_user_id(),
						'type'  => $this->type,
						'title' => bp_get_member_name(),
						'html'  => buddypress_search_template_part( 'loop/member', $template_type, false ),
					);

					$this->search_results['items'][ bp_get_member_user_id() ] = $result_item;
				}
			}

			do_action( 'bp_after_search_members_html' );
		}

		/**
		 * What fields members should be searched on?
		 * Prints options to search through username, email, nicename/displayname.
		 * Prints xprofile fields, if xprofile component is active.
		 *
		 * @since 1.0.0
		 *
		 * @param string $items_to_search Item search.
		 */
		public function print_search_options( $items_to_search ) {
			echo "<div class='wp-user-fields' style='margin: 10px 0 0 30px'>";
			echo "<p class='xprofile-group-name' style='margin: 5px 0'><strong>" . esc_html_e( 'Account', 'buddypress-search' ) . '</strong></p>';

			$fields = array(
				'user_login'   => __( 'Username/Login', 'buddypress-search' ),
				'display_name' => __( 'Display Name', 'buddypress-search' ),
				'user_email'   => __( 'Email', 'buddypress-search' ),
				'user_meta'    => __( 'User Meta', 'buddypress-search' ),
			);
			foreach ( $fields as $field => $label ) {
				$item            = 'member_field_' . $field;
				$checked         = ! empty( $items_to_search ) && in_array( $item, $items_to_search ) ? ' checked' : '';
				$xprofile_custom = "<label><input type='checkbox' value='{$item}' name='bp_search_plugin_options[items-to-search][]' {$checked}>{$label}</label><br>";
				echo wp_kses_post( $xprofile_custom );
			}

			echo '</div><!-- .wp-user-fields -->';

			if ( ! function_exists( 'bp_is_active' ) || ! bp_is_active( 'xprofile' ) ) {
				return;
			}

			$groups = bp_xprofile_get_groups(
				array(
					'fetch_fields' => true,
				)
			);

			if ( ! empty( $groups ) ) {
				echo "<div class='xprofile-fields' style='margin: 0 0 10px 30px'>";
				foreach ( $groups as $group ) {
					echo "<p class='xprofile-group-name' style='margin: 5px 0'><strong>" . esc_html( $group->name ) . '</strong></p>';

					if ( ! empty( $group->fields ) ) {
						foreach ( $group->fields as $field ) {
							// lets save these as xprofile_field_{field_id}.
							$item                 = 'xprofile_field_' . $field->id;
							$checked              = ! empty( $items_to_search ) && in_array( $item, $items_to_search ) ? ' checked' : '';
							$xprofile_field_by_id = "<label><input type='checkbox' value='{$item}' name='bp_search_plugin_options[items-to-search][]' {$checked}>{$field->name}</label><br>";
							echo wp_kses_post( $xprofile_field_by_id );
						}
					}
				}
				echo '</div><!-- .xprofile-fields -->';
			}
		}
	}

	// End class BuddyPress_Search_Members.

endif;
