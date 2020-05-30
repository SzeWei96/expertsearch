<?php
	class Expert_Profile extends CI_Controller
	{
        function __construct() { 
            parent::__construct(); 
            $this->load->helper('url'); 
            $this->load->database(); 
            $this->load->model('Expert_Profile_Model'); // load model 
        }

        public function index() { 
            $this->load->view('templates/header');
            
            $expert_id = $_GET['expert_id'];  
            $keyword = $_GET['keyword']; 
            
            //echo($expert_id);
            //echo($keyword);
            $sort = $this->input->get('sort');
			$type_filter = $this->input->get('type');
            $year_filter = $this->input->get('year');

            $expert_profile['Keyword'] = $keyword;
            // Get Expert Basic Information
            $exp_basic_info = $this->Expert_Profile_Model->get_exp_basic_info($expert_id);
            foreach($exp_basic_info as $row){
                $expert_profile['exp_id'] =  $row['expert_id'];
                $expert_profile['exp_name'] =  $row['expert_name'];
				$expert_profile['expert_honorific'] =  $row['expert_honorific'];
                $expert_profile['exp_photo'] =  $row['expert_photo'];
                $expert_profile['exp_faculty'] = $row['school_name'];
                $expert_profile['exp_uni'] = $row['university_name'];
            }


            if(strpos($keyword,',') !== false){
                $pub_ids = $_GET['pub_ids'];
                $expert_profile['pub_ids'] = $pub_ids;
                //print_r($pub_ids);
                // Get total relatd publications
                $get_no_pub = $this->db->query("SELECT No_publication FROM `TEMP_INTERSECT` WHERE Keyword = '$keyword' AND expert_id = '$expert_id'");
                $get_no_pub = json_encode($get_no_pub->result());
                $get_no_pub = json_decode($get_no_pub, true);
                //print_r($get_no_pub);
                foreach ($get_no_pub as $key => $value) {
                                foreach ($value as $v) {
                                    $get_no_pub = $v;
                                    //print_r($get_no_pub);
                                }
                            }
                $expert_profile['exp_tot_related_pub'] = $get_no_pub;
                //print_r($expert_profile['exp_tot_related_pub']);

                // Get total cited related publications
                $get_no_cite = $this->db->query("SELECT No_citation FROM `TEMP_INTERSECT` WHERE Keyword = '$keyword' AND expert_id = '$expert_id'");
                $get_no_cite = json_encode($get_no_cite->result());
                $get_no_cite = json_decode($get_no_cite, true);

                foreach ($get_no_cite as $key => $value) {
                                foreach ($value as $v) {
                                    $get_no_cite = $v;
                                }
                            }
                $expert_profile['exp_tot_related_cite'] = $get_no_cite;

                // Get related co-authors (Select related experts match with this author)
                /*$PUBIDs = $this->db->query("SELECT DISTINCT publication_id FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias WHERE index_keywords LIKE '%$keyword%' OR author_keywords LIKE '%$keyword%' OR title LIKE '%$keyword%' OR abstract LIKE '%$keyword%'");
                if ($PUBIDs->num_rows() > 0 || $PUBIDs->num_rows() != 0)  //Ensure that there is at least one result 
                {
                    $pub_id = array();
                    foreach ($PUBIDs->result_array() as $row)  //Iterate through results
                    {
                       array_push($pub_id, $row['publication_id']);
                          echo $row['publication_id'];
                          echo '/';
                    }

                    $in = '(' . implode(',', $pub_id) .')';
                    echo $in;
                }
                else{
                    echo ("No result found");
                }*/
                $in = explode(",", $pub_ids);
                $in = implode(",", $in);
                $filter_pub = $this->db->query("SELECT DISTINCT publication_id FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias NATURAL JOIN TEMP_INTERSECT WHERE `expert_id` = $expert_id AND `Keyword` = '$keyword' AND `match_success`= 'true' AND publication_id IN ".$in);
                if ($filter_pub->num_rows() > 0 || $filter_pub->num_rows() != 0)  //Ensure that there is at least one result 
                {
                    $pub_id = array();
                    foreach ($filter_pub->result_array() as $row)  //Iterate through results
                    {
                       array_push($pub_id, $row['publication_id']);
                          //echo $row['publication_id'];
                          //echo '/';
                    }

                    $in2 = '(' . implode(',', $pub_id) .')';
                    //echo $in2;
                }
                else{
                    echo ("No result found");
                }

                // Get total related co-authors (Select related publications authors exclude current author)
                $no_co_authors = $this->db->query("SELECT COUNT(DISTINCT expert_id) AS total_co_author FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias NATURAL JOIN TEMP_INTERSECT WHERE `expert_id` != $expert_id AND `Keyword` = '$keyword' AND `match_success`= 'true' AND publication_id IN ".$in2);
                $no_co_authors = json_encode($no_co_authors->result());
                $no_co_authors = json_decode($no_co_authors, true);

                foreach ($no_co_authors as $key => $value) {
                                foreach ($value as $v) {
                                    $no_co_authors = $v;
                                }
                            }
                $expert_profile['no_co_authors'] = $no_co_authors;

                $co_authors = $this->db->query("SELECT DISTINCT expert_name, expert_id, Keyword FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias NATURAL JOIN TEMP_INTERSECT WHERE `expert_id` != $expert_id AND `Keyword` = '$keyword' AND `match_success`= 'true' AND publication_id IN ".$in2);
                if ($co_authors->num_rows() > 0 || $co_authors->num_rows() != 1)  //Ensure that there is at least one result 
                {
                    $co_authors = json_encode($co_authors->result());
                    $co_authors = json_decode($co_authors, true);
                }
                $expert_profile['co_authors'] = $co_authors;

                /*$co_authors = json_encode($co_authors->result());
                $co_authors = json_decode($co_authors, true);

                $co_authors_list = array();
                foreach ($co_authors as $key => $value) {
                                foreach ($value as $v) {
                                    $co_authors = $v;
                                    array_push($co_authors_list, $co_authors);
                                }
                            }
                $expert_profile['co_authors'] = $co_authors_list;*/
              
                // Get related publications
                $pubs = $this->db->query("SELECT DISTINCT title, authors, abstract, document_type, cited_by, year, link FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias NATURAL JOIN TEMP_INTERSECT WHERE `expert_id` = $expert_id AND `Keyword` = '$keyword' AND `match_success`= 'true' AND publication_id IN" .$in2);
                if ($pubs->num_rows() > 0 || $pubs->num_rows() != 1)  //Ensure that there is at least one result 
                {
                    $pubs = json_encode($pubs->result());
                    $pubs = json_decode($pubs, true);
                }
                $expert_profile['pubs'] = $pubs;

                // Get related keywords
                $related_keywords = $this->db->query("SELECT DISTINCT index_keywords FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias NATURAL JOIN TEMP_INTERSECT WHERE `expert_id` = $expert_id AND `Keyword` = '$keyword' AND `match_success`= 'true' AND publication_id IN" .$in2);
                $related_keywords = json_encode($related_keywords->result());
                $related_keywords = json_decode($related_keywords, true);
                //print_r($related_keywords);
                $related_keywords_list = array();
                foreach ($related_keywords as $key => $value) {
                                foreach ($value as $v) {
                                    $related_keywords = $v;
                                    array_push($related_keywords_list, $related_keywords);
                                }
                            }
                $expert_profile['related_keywords'] = $related_keywords_list;

                $break_words = array();
                $expert_profile['merge_author_keywords_array'] = array();
                foreach($related_keywords_list as $item){
                    $item = str_replace("-", " ", $item);
                    $item = ucwords($item);
                    array_push($break_words, explode("; ", $item));
                    $expert_profile['merge_keywords_array'] = call_user_func_array('array_merge', $break_words);
                }

                $expert_profile['frequency_count'] = array_count_values($expert_profile['merge_keywords_array']);
                //print_r($expert_profile['frequency_count']);

                // Get Expert Publication Types 
                $pub_type = $this->db->query("SELECT document_type FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias NATURAL JOIN TEMP_INTERSECT WHERE `expert_id` = $expert_id AND `Keyword` = '$keyword' AND `match_success`= 'true' AND publication_id IN" .$in2);
                if ($pub_type->num_rows() > 0 || $pub_type->num_rows() != 1)  //Ensure that there is at least one result 
                {
                    $pub_type = json_encode($pub_type->result());
                    $pub_type = json_decode($pub_type, true);
                }

                $pubtype_list = array();
                foreach ($pub_type as $value) {
                                
                    array_push($pubtype_list, $value);
                }
                $expert_profile['related_keywords'] = $related_keywords_list;
                $expert_profile['pub_type'] = $pub_type;
                //print_r($pub_type);
                $pub_cat = array();
                foreach ($pub_type as $r) {
                   // print_r($r["document_type"]);
                    array_push($pub_cat,$r["document_type"]);
                }
                $count_cat = array();
                $pub_categories['cat'] = array();
                $pub_categories['no'] = array();
                array_push($count_cat,array_count_values($pub_cat));
                $expert_profile['count_cat'] = $count_cat;
                //print_r($count_cat);
                foreach ($count_cat as $key => $value) {
                    foreach ($value as $key => $value) {
                        //echo $key;
                        //echo $value;
                        array_push($pub_categories['cat'],$key);
                        array_push($pub_categories['no'],$value);
                    }
                }

                $expert_profile['pub_categories'] = $pub_categories;
            }
            else{
                // Get total relatd publications
                $pub_ids = array();
                $expert_profile['pub_ids'] = $pub_ids;
                $get_no_pub = $this->db->query("SELECT No_publication FROM `SHORTLISTED_EXPERTS` WHERE Keyword = '$keyword' AND expert_id = '$expert_id'");
                $get_no_pub = json_encode($get_no_pub->result());
                $get_no_pub = json_decode($get_no_pub, true);
                //print_r($get_no_pub);
                foreach ($get_no_pub as $key => $value) {
                                foreach ($value as $v) {
                                    $get_no_pub = $v;
                                    //print_r($get_no_pub);
                                }
                            }
                $expert_profile['exp_tot_related_pub'] = $get_no_pub;
                //print_r($expert_profile['exp_tot_related_pub']);

                // Get total cited related publications
                $get_no_cite = $this->db->query("SELECT No_citation FROM `SHORTLISTED_EXPERTS` WHERE Keyword = '$keyword' AND expert_id = '$expert_id'");
                $get_no_cite = json_encode($get_no_cite->result());
                $get_no_cite = json_decode($get_no_cite, true);

                foreach ($get_no_cite as $key => $value) {
                                foreach ($value as $v) {
                                    $get_no_cite = $v;
                                }
                            }
                $expert_profile['exp_tot_related_cite'] = $get_no_cite;

                

                // Get related co-authors (Select related experts match with this author)
                $PUBIDs = $this->db->query("SELECT DISTINCT publication_id FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias WHERE index_keywords LIKE '%$keyword%' OR author_keywords LIKE '%$keyword%' OR title LIKE '%$keyword%' OR abstract LIKE '%$keyword%'");
                if ($PUBIDs->num_rows() > 0 || $PUBIDs->num_rows() != 0)  //Ensure that there is at least one result 
                {
                    $pub_id = array();
                    foreach ($PUBIDs->result_array() as $row)  //Iterate through results
                    {
                       array_push($pub_id, $row['publication_id']);
                          //echo $row['publication_id'];
                          //echo '/';
                    }

                    $in = '(' . implode(',', $pub_id) .')';
                    //echo $in;
                }
                else{
                    echo ("No result found");
                }

                $filter_pub = $this->db->query("SELECT DISTINCT publication_id FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias NATURAL JOIN SHORTLISTED_EXPERTS WHERE `expert_id` = $expert_id AND `Keyword` = '$keyword' AND `match_success`= 'true' AND publication_id IN ".$in);
                if ($filter_pub->num_rows() > 0 || $filter_pub->num_rows() != 0)  //Ensure that there is at least one result 
                {
                    $pub_id = array();
                    foreach ($filter_pub->result_array() as $row)  //Iterate through results
                    {
                       array_push($pub_id, $row['publication_id']);
                          //echo $row['publication_id'];
                          //echo '/';
                    }

                    $in2 = '(' . implode(',', $pub_id) .')';
                    //echo $in2;
                }
                else{
                    echo ("No result found");
                }

                // Get total related co-authors (Select related publications authors exclude current author)
                $no_co_authors = $this->db->query("SELECT COUNT(DISTINCT expert_id) AS total_co_author FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias NATURAL JOIN SHORTLISTED_EXPERTS WHERE `expert_id` != $expert_id AND `Keyword` = '$keyword' AND `match_success`= 'true' AND publication_id IN ".$in2);
                $no_co_authors = json_encode($no_co_authors->result());
                $no_co_authors = json_decode($no_co_authors, true);

                foreach ($no_co_authors as $key => $value) {
                                foreach ($value as $v) {
                                    $no_co_authors = $v;
                                }
                            }
                $expert_profile['no_co_authors'] = $no_co_authors;

                $co_authors = $this->db->query("SELECT DISTINCT expert_name, expert_id, Keyword FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias NATURAL JOIN SHORTLISTED_EXPERTS WHERE `expert_id` != $expert_id AND `Keyword` = '$keyword' AND `match_success`= 'true' AND publication_id IN ".$in2);
                if ($co_authors->num_rows() > 0 || $co_authors->num_rows() != 1)  //Ensure that there is at least one result 
                {
                    $co_authors = json_encode($co_authors->result());
                    $co_authors = json_decode($co_authors, true);
                }
                $expert_profile['co_authors'] = $co_authors;

                /*$co_authors = json_encode($co_authors->result());
                $co_authors = json_decode($co_authors, true);

                $co_authors_list = array();
                foreach ($co_authors as $key => $value) {
                                foreach ($value as $v) {
                                    $co_authors = $v;
                                    array_push($co_authors_list, $co_authors);
                                }
                            }
                $expert_profile['co_authors'] = $co_authors_list;*/
              
                // Get related publications
                $pubs = $this->db->query("SELECT DISTINCT title, authors, abstract, document_type, cited_by, year, link FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias NATURAL JOIN SHORTLISTED_EXPERTS WHERE `expert_id` = $expert_id AND `Keyword` = '$keyword' AND `match_success`= 'true' AND publication_id IN" .$in2);
                if ($pubs->num_rows() > 0 || $pubs->num_rows() != 1)  //Ensure that there is at least one result 
                {
                    $pubs = json_encode($pubs->result());
                    $pubs = json_decode($pubs, true);
                }
                $expert_profile['pubs'] = $pubs;

                // Get related keywords
                $related_keywords = $this->db->query("SELECT DISTINCT index_keywords FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias NATURAL JOIN SHORTLISTED_EXPERTS WHERE `expert_id` = $expert_id AND `Keyword` = '$keyword' AND `match_success`= 'true' AND publication_id IN" .$in2);
                $related_keywords = json_encode($related_keywords->result());
                $related_keywords = json_decode($related_keywords, true);
                //print_r($related_keywords);
                $related_keywords_list = array();
                foreach ($related_keywords as $key => $value) {
                                foreach ($value as $v) {
                                    $related_keywords = $v;
                                    array_push($related_keywords_list, $related_keywords);
                                }
                            }
                $expert_profile['related_keywords'] = $related_keywords_list;

                $break_words = array();
                $expert_profile['merge_author_keywords_array'] = array();
                foreach($related_keywords_list as $item){
                    $item = str_replace("-", " ", $item);
                    $item = ucwords($item);
                    array_push($break_words, explode("; ", $item));
                    $expert_profile['merge_keywords_array'] = call_user_func_array('array_merge', $break_words);
                }

                $expert_profile['frequency_count'] = array_count_values($expert_profile['merge_keywords_array']);
                //print_r($expert_profile['frequency_count']);

                // Get Expert Publication Types 
                $pub_type = $this->db->query("SELECT document_type FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias NATURAL JOIN SHORTLISTED_EXPERTS WHERE `expert_id` = $expert_id AND `Keyword` = '$keyword' AND `match_success`= 'true' AND publication_id IN" .$in2);
                if ($pub_type->num_rows() > 0 || $pub_type->num_rows() != 1)  //Ensure that there is at least one result 
                {
                    $pub_type = json_encode($pub_type->result());
                    $pub_type = json_decode($pub_type, true);
                }

                $pubtype_list = array();
                foreach ($pub_type as $value) {
                                
                    array_push($pubtype_list, $value);
                }
                $expert_profile['related_keywords'] = $related_keywords_list;
                $expert_profile['pub_type'] = $pub_type;
                //print_r($pub_type);
                $pub_cat = array();
                foreach ($pub_type as $r) {
                   // print_r($r["document_type"]);
                    array_push($pub_cat,$r["document_type"]);
                }
                $count_cat = array();
                $pub_categories['cat'] = array();
                $pub_categories['no'] = array();
                array_push($count_cat,array_count_values($pub_cat));
                $expert_profile['count_cat'] = $count_cat;
                //print_r($count_cat);
                foreach ($count_cat as $key => $value) {
                    foreach ($value as $key => $value) {
                        //echo $key;
                        //echo $value;
                        array_push($pub_categories['cat'],$key);
                        array_push($pub_categories['no'],$value);
                    }
                }

                $expert_profile['pub_categories'] = $pub_categories;
            }
            


            //echo count(array_unique($expert_profile['merge_author_keywords_array']));
            //$expert_profile['frequency_count'] = array_count_values($expert_profile['merge_author_keywords_array']);
            //print_r($expert_profile['frequency_count']);
            $this->load->view('expert_profile', $expert_profile); 
            $this->load->view('templates/footer');

            
        }
        
    }

?>