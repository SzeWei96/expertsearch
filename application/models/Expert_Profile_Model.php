<?php
    class Expert_Profile_Model extends CI_Model {

        // Get Expert Basic Info
        function get_exp_basic_info($exp_id){
            //$query = $this->db->get_where('expert', array('expert_id' => $exp_id));
            $query = $this->db->query("SELECT expert_id, expert_name, expert_honorific, expert_photo, school_name, university_name FROM `expert_affiliation` NATURAL JOIN expert NATURAL JOIN `school` NATURAL JOIN university NATURAL JOIN uni_match_school WHERE expert_id=$exp_id");
            $result = $query->result_array();
            return $result;
        }

        //Get related total publication
        function get_exp_tot_related_pub($exp_id, $word){
            $query = $this->db->query("SELECT COUNT(DISTINCT publication_id) AS total_publication FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias WHERE  AND index_keywords LIKE '%$word%' OR author_keywords LIKE '%$word%' OR title LIKE '%$word%' OR abstract LIKE '%$word%' AND `expert_id` = $exp_id AND `match_success`= 'true' AND `confirm_match` = 'true'");
            //$query = $this->db->query("SELECT COUNT(DISTINCT publication_id) AS total_publication FROM publication WHERE publication_id IN ( SELECT DISTINCT publication_id FROM publication NATURAL JOIN expert_match_alias NATURAL JOIN expert_alias WHERE `expert_id` = $exp_id AND `authors` LIKE CONCAT('%',`alias_name`, '%'))");
            $row = $query->row_array();
            return $row['total_publication'];
        } 

        // Get Expert Total Publication
        function get_exp_tot_pub($exp_id){
            $query = $this->db->query("SELECT COUNT(DISTINCT publication_id) AS total_publication FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias WHERE `expert_id` = $exp_id AND `match_success`= 'true' AND `confirm_match` = 'true'");
            //$query = $this->db->query("SELECT COUNT(DISTINCT publication_id) AS total_publication FROM publication WHERE publication_id IN ( SELECT DISTINCT publication_id FROM publication NATURAL JOIN expert_match_alias NATURAL JOIN expert_alias WHERE `expert_id` = $exp_id AND `authors` LIKE CONCAT('%',`alias_name`, '%'))");
            $row = $query->row_array();
            return $row['total_publication'];
        } 
        
        // Get Expert Total Times Cited 
        function get_exp_tot_cite($exp_id){
            $query = $this->db->query("SELECT sum(cited_by) AS total_times_cited FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias WHERE `expert_id` = $exp_id AND `match_success`= 'true' AND `confirm_match` = 'true'");
            $row = $query->row_array();
            return $row['total_times_cited'];
        } 

        // Get Expert Total Co-authoring Expert
        function get_exp_tot_co_auth($exp_id){
            $query = $this->db->query("SELECT COUNT(DISTINCT expert_id) AS total_co_author FROM (SELECT DISTINCT publication_id, authors FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias WHERE `expert_id` = $exp_id AND `match_success`= 'true' AND `confirm_match` = 'true') AS `expert_pub_table` NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias WHERE `expert_id` != $exp_id AND `match_success`= 'true' AND `confirm_match` = 'true'");
            $row = $query->row_array();
            return $row['total_co_author'];
        }

        // Get Expert Total Publication Category
        function get_exp_tot_cat($exp_id){
            return; 
        }
        
        // Get Expert Publication Types (Summary Horizontal Bar)
        /*function get_exp_pub_type_summary($exp_id){
            $query = $this->db->query("SELECT document_type, COUNT(publication_id) AS total FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias WHERE `expert_id` = $exp_id AND `match_success`= 'true' AND `confirm_match` = 'true' GROUP BY document_type");
            $result = $query->result_array();
            return $result;
        }

        // Get Expert Author Keyword (Summary Word Cloud)
        function get_exp_auth_word($exp_id){
            $query = $this->db->query("SELECT DISTINCT publication_id, author_keywords FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias WHERE `expert_id` = $exp_id AND `match_success`= 'true'");
            $result = $query->result_array();
            return $result;
        }

        // Get Expert's Publication List (Summary)
        function get_exp_pub_5_list($exp_id){
            $query = $this->db->query("SELECT DISTINCT publication_id, title, authors, source_title, page_start, page_end, cited_by, year FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias WHERE `expert_id` = $exp_id AND `match_success`= 'true' ORDER BY cited_by DESC LIMIT 5");
            $result = $query->result_array();
            return $result;
        }*/

        
	
   } 
?>