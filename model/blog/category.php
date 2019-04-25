<?php

class ModelBlogCategory extends Model {

	public function getCategory( $category_id ) {
		global $wpdb;

		$sql = "SELECT 
            t.`term_id` AS 'ID',
            t.`name`,
            tt.`parent`,
            tt.`description`
        FROM
            `wp_terms` t 
            LEFT JOIN `wp_term_taxonomy` tt 
            ON tt.`term_id` = t.`term_id` 
        WHERE tt.`taxonomy` = 'category' and t.`term_id` = '" . (int) $category_id . "'";

		$sql .= " GROUP BY t.term_id";

		$result = $wpdb->get_row( $sql );

		return $result;
	}

	public function getCategories( $data = array() ) {
		global $wpdb;

		$sql = "SELECT 
            t.`term_id` AS 'ID',
            t.`name`,
            tt.`parent`,
            tt.`description`
        FROM
            `wp_terms` t 
            LEFT JOIN `wp_term_taxonomy` tt 
            ON tt.`term_id` = t.`term_id` 
        WHERE tt.`taxonomy` = 'category'";

		$implode = array();

		if ( isset( $data['filter_parent_id'] ) ) {
			$implode[] = "tt.parent = '" . (int) $data['filter_parent_id'] . "'";
		}

		if ( count( $implode ) > 0 ) {
			$sql .= ' AND ' . implode( ' AND ', $implode );
		}

		$sql .= " GROUP BY t.term_id";

		$sort_data = array(
			'ID'
		);

		if ( isset( $data['sort'] ) && in_array( $data['sort'], $sort_data ) ) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY ID";
		}

		if ( isset( $data['order'] ) && ( $data['order'] == 'DESC' ) ) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if ( isset( $data['start'] ) || isset( $data['limit'] ) ) {
			if ( $data['start'] < 0 ) {
				$data['start'] = 0;
			}

			if ( $data['limit'] < 1 ) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
		}

		$results = $wpdb->get_results( $sql );

		return $results;
	}

	public function getTotalCategories( $data = array() ) {
		global $wpdb;

		$sql = "SELECT count(*) as total 
        FROM
            `wp_terms` t 
            LEFT JOIN `wp_term_taxonomy` tt 
            ON tt.`term_id` = t.`term_id` 
        WHERE tt.`taxonomy` = 'category'";

		$implode = array();

		if ( isset( $data['filter_parent_id'] ) ) {
			$implode[] = "tt.parent = '" . (int) $data['filter_parent_id'] . "'";
		}

		if ( count( $implode ) > 0 ) {
			$sql .= ' AND ' . implode( ' AND ', $implode );
		}

		$result = $wpdb->get_row( $sql );

		return $result->total;
	}
}