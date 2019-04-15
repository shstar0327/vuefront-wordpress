<?php

class ModelCommonPage
{
    public function getPage($page_id)
    {
        global $wpdb;

        $sql = "SELECT p.ID, p.post_title AS title, p.`post_content` AS description, p.`menu_order` AS sort_order FROM wp_posts p WHERE p.`post_type` = 'page' AND p.`ID` = '".(int)$page_id."'";

        $result = $wpdb->get_row($sql);

        return $result;
    }

    public function getPages($data = array())
    {
        global $wpdb;

        $sql = "SELECT p.ID, p.post_title AS title, p.`post_content` AS description, p.`menu_order` AS sort_order FROM wp_posts p WHERE p.`post_type` = 'page' AND p.post_status = 'publish'";
        
        $implode = array();

        if (!empty($data['filter_search'])) {
            $implode[] = "(p.post_title LIKE '%".$data['filter_search']."%' 
            OR p.post_content LIKE '%".$data['filter_search']."%')";
        }

        if (count($implode) > 0) {
            $sql .= ' AND ' . implode(' AND ', $implode);
        }

        $sql .= " GROUP BY p.ID";

        $sort_data = array(
            'p.ID',
            'title',
            'sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY p.ID";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $results = $wpdb->get_results($sql);

        return $results;
    }

    public function getTotalPages($data = array())
    {
        global $wpdb;

        $sql = "SELECT count(*) as total FROM wp_posts p WHERE p.`post_type` = 'page' AND p.post_status = 'publish'";
        
        $implode = array();

        if (!empty($data['filter_search'])) {
            $implode[] = "(p.post_title LIKE '%".$data['filter_search']."%' 
            OR p.post_content LIKE '%".$data['filter_search']."%')";
        }

        if (count($implode) > 0) {
            $sql .= ' AND ' . implode(' AND ', $implode);
        }

        $result = $wpdb->get_row($sql);

        return $result->total;
    }
}
