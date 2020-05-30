<?php
    class Publication_Info_Model extends CI_Model {
        
        // Get Publication Info
        function get_pub_info($pub_id, $extra_info){
            $data = array();

            $query = $this->db->query("SELECT * FROM publication WHERE `publication_id` = $pub_id");
            $result = $query->result_array();
            // return $result;
            if($query->num_rows() > 0){
                if ($extra_info == true){
                    foreach($result as $row){
                        array_push($data, array( 
                        "Year"=>$row["year"], "Cited by"=>$row["cited_by"], "Source"=>$row["source_title"], 
                        "Volume"=>$row["volume"], "Issue"=>$row["issue"], "page_start"=>$row["page_start"], 
                        "page_end"=>$row["page_end"], "Publisher"=>$row["publisher"], 
                        "Record Type"=>$row["document_type"], "link"=>$row["link"]));
                    }
                    $data = array_filter($data);
                    return $data;
                }else{
                    foreach($result as $row){
                        array_push($data, array("title"=>$row["title"], "authors"=>$row["authors"], 
                        "abstract"=>$row["abstract"], "author_keywords"=>$row["author_keywords"]));
                    }
                    return $data;
                }
            }
            return false;
        }

        // Get any expert (author/co-authors) links
        function get_exp_link($pub_id){
            $data = array();
            $query = $this->db->query("SELECT alias_name, expert_id, expert_name FROM (SELECT DISTINCT publication_id, authors FROM publication WHERE publication_id = $pub_id) AS aut_table NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias NATURAL JOIN expert WHERE `match_success`= 'true' AND `confirm_match` = 'true' GROUP BY expert_id");
            $result = $query->result_array();
            if($query->num_rows() > 0){
                foreach($result as $row){
                    array_push($data, array("alias_name"=>$row["alias_name"],"expert_id"=>$row["expert_id"]));
                }
                return $data;
            }
            return false;
        }
    }

?> 