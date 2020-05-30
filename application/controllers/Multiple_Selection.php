<?php

	class Multiple_Selection extends CI_Controller
	{
		
        function __construct() { 
            parent::__construct(); 
            $this->load->helper('url'); 
            $this->load->database(); 
            $this->load->model('Expert_Profile_Model');
            $this->load->library('javascript');
            $this->load->library('session');
        }

        public function index()
        {
            $input_query = $this->input->get('q');
            if ($input_query == "*:*"){
                $input_query = "";
            }

            $multiple_selection['search_query'] = $input_query;
            $this->db->query("DELETE FROM `TEMP_INTERSECT`");
            $this->db->query("alter table TEMP_INTERSECT AUTO_INCREMENT = 1");
            $this->db->query("DELETE FROM `TEMP_INFO_INTERSECT`");
            $this->db->query("alter table TEMP_INFO_INTERSECT AUTO_INCREMENT = 1");
            $array = array();
            $ext_keyword = array();
            $array = $this->input->get('word');
            $word = explode(",", $array);
            $multiple_selection['keywords'] = $word;
            $stringWord = implode(",", $word);
            $multiple_selection['keywordsString'] = $stringWord;
            if(count($word) == 2){
                [$keyword1,$keyword2] = $word;

                $query1 = $this->db->query("SELECT publication_id FROM publication WHERE index_keywords LIKE '%$keyword1%' AND index_keywords LIKE '%$keyword2%' OR author_keywords LIKE '%$keyword1%' AND author_keywords LIKE '%$keyword2%' OR title LIKE '%$keyword1%' AND title LIKE '%$keyword2%' OR abstract LIKE '%$keyword1%' AND abstract LIKE '%$keyword2%'");

                if ($query1->num_rows() > 0 || $query1->num_rows() != 0)  //Ensure that there is at least one result 
                {
                    array_push($ext_keyword,$word);
                    $pub_id = array();
                    foreach ($query1->result_array() as $row)  //Iterate through results
                    {
                       array_push($pub_id, $row['publication_id']);
                    }

                    $multiple_selection['pubId'] = implode(',', $pub_id);
                    //print_r($pub_id);
                    $in = '(' . implode(',', $pub_id) .')';
                    $multiple_selection['pub_ids'] = $in;
                    $array_rank = $this->queryMatching($in,$stringWord);
                    $multiple_selection['rank_list'] = $array_rank;
                    //print_r($multiple_selection['rank_list']);
                }
                else{
                    echo("no result");
                    $empty = array();
                    $multiple_selection['rank_list'] = $empty;

                }

            }else if(count($word) == 3){
                [$keyword1,$keyword2,$keyword3] = $word;

                $query2 = $this->db->query("SELECT publication_id FROM publication WHERE index_keywords LIKE '%$keyword1%' AND index_keywords LIKE '%$keyword2%' AND index_keywords LIKE '%$keyword3%' OR author_keywords LIKE '%$keyword1%' AND author_keywords LIKE '%$keyword2%' AND author_keywords LIKE '%$keyword3%' OR title LIKE '%$keyword1%' AND title LIKE '%$keyword2%' AND title LIKE '%$keyword3%' OR abstract LIKE '%$keyword1%' AND abstract LIKE '%$keyword2%' AND abstract LIKE '%$keyword3%'");

                if ($query2->num_rows() > 0 || $query2->num_rows() != 0)  //Ensure that there is at least one result 
                {
                    array_push($ext_keyword,$word);
                    $pub_id = array();
                    foreach ($query2->result_array() as $row)  //Iterate through results
                    {
                       array_push($pub_id, $row['publication_id']);
                    }

                    $in = '(' . implode(',', $pub_id) .')';
                    $multiple_selection['pub_ids'] = $in;
                    $array_rank = $this->queryMatching($in,$stringWord);
                    $multiple_selection['rank_list'] = $array_rank;
                }
                else{
                    $empty = array();
                    $multiple_selection['rank_list'] = $empty;

                }

            }else if(count($word) == 4){
                [$keyword1,$keyword2,$keyword3,$keyword4] = $word;

                $query3 = $this->db->query("SELECT publication_id FROM publication WHERE index_keywords LIKE '%$keyword1%' AND index_keywords LIKE '%$keyword2%' AND index_keywords LIKE '%$keyword3%' AND index_keywords LIKE '%$keyword4%' OR author_keywords LIKE '%$keyword1%' AND author_keywords LIKE '%$keyword2%' AND author_keywords LIKE '%$keyword3%' AND author_keywords LIKE '%$keyword4%' OR title LIKE '%$keyword1%' AND title LIKE '%$keyword2%' AND title LIKE '%$keyword3%' AND title LIKE '%$keyword4%' OR abstract LIKE '%$keyword1%' AND abstract LIKE '%$keyword2%' AND abstract LIKE '%$keyword3%' AND abstract LIKE '%$keyword4%'");

                if ($query3->num_rows() > 0 || $query3->num_rows() != 0)  //Ensure that there is at least one result 
                {
                    array_push($ext_keyword,$word);
                    $pub_id = array();
                    foreach ($query3->result_array() as $row)  //Iterate through results
                    {
                       array_push($pub_id, $row['publication_id']);
                    }

                    $in = '(' . implode(',', $pub_id) .')';
                    $multiple_selection['pub_ids'] = $in;
                    $array_rank = $this->queryMatching($in,$stringWord);
                    $multiple_selection['rank_list'] = $array_rank;
                }
                else{
                    $empty = array();
                    $multiple_selection['rank_list'] = $empty;

                }

            }else if(count($word) == 5){
                [$keyword1,$keyword2,$keyword3,$keyword4,$keyword5] = $word;

                $query4 = $this->db->query("SELECT publication_id FROM publication WHERE index_keywords LIKE '%$keyword1%' AND index_keywords LIKE '%$keyword2%' AND index_keywords LIKE '%$keyword3%' AND index_keywords LIKE '%$keyword4%' AND index_keywords LIKE '%$keyword5%' OR author_keywords LIKE '%$keyword1%' AND author_keywords LIKE '%$keyword2%' AND author_keywords LIKE '%$keyword3%' AND author_keywords LIKE '%$keyword4%' AND author_keywords LIKE '%$keyword5%' OR title LIKE '%$keyword1%' AND title LIKE '%$keyword2%' AND title LIKE '%$keyword3%' AND title LIKE '%$keyword4%' AND title LIKE '%$keyword5%' OR abstract LIKE '%$keyword1%' AND abstract LIKE '%$keyword2%' AND abstract LIKE '%$keyword3%' AND abstract LIKE '%$keyword4%' AND abstract LIKE '%$keyword5%'");

                if ($query4->num_rows() > 0 || $query4->num_rows() != 0)  //Ensure that there is at least one result 
                {
                    array_push($ext_keyword,$word);
                    $pub_id = array();
                    foreach ($query4->result_array() as $row)  //Iterate through results
                    {
                       array_push($pub_id, $row['publication_id']);
                    }

                    $in = '(' . implode(',', $pub_id) .')';
                    $multiple_selection['pub_ids'] = $in;
                    $array_rank = $this->queryMatching($in,$stringWord);
                    $multiple_selection['rank_list'] = $array_rank;
                }
                else{
                    $empty = array();
                    $multiple_selection['rank_list'] = $empty;

                }
            }
            //$multiple_selection['keywords'] = $array;
        	$this->load->view('templates/header');
           	$this->load->view('multiple_selection', $multiple_selection); 
			$this->load->view('templates/footer');
        } 


        public function queryMatching($pub_id,$word){
            $temp_rank = array();
            $query2 = $this->db->query("SELECT alias_id FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias WHERE publication_id IN ".$pub_id);

                        if ($query2->num_rows() > 0)  //Ensure that there is at least one result 
                        {
                            //$pub_id2 = array();
                            $alias_id = array();
                           foreach ($query2->result_array() as $row)  //Iterate through results
                           {
                                
                              array_push($alias_id, $row['alias_id']);
                              
                           }
                        }
                    
                        $unique_alias = array_unique($alias_id); 
                        $unique_alias = array_values($unique_alias);

                        $in2 = '(' . implode(',', $unique_alias) .')';
                        
                        //echo "ALIASID" .$in2;
                        $test = $this->db->query("SELECT expert_id FROM expert_alias NATURAL JOIN expert_match_alias WHERE `match_success`= 'true' AND alias_id IN $in2 ORDER BY expert_id");
                        if ($test->num_rows() > 0)  //Ensure that there is at least one result 
                            {
                                //$pub_id2 = array();"
                               $expert_id = array();
                               foreach ($test->result_array() as $row)  //Iterate through results
                               {
                                    
                                  array_push($expert_id, $row['expert_id']);
                                  
                               }
                            }

                        $unique_expert = array_unique($expert_id); 
                        $unique_expert = array_values($unique_expert);

                        $in3 = '(' . implode(',', $unique_expert) .')';

                        $test2 = $this->db->query("SELECT expert_name FROM expert WHERE expert_id IN" .$in3);
                        if ($test2->num_rows() > 0)  //Ensure that there is at least one result 
                        {
                            //$pub_id2 = array();"
                            $expert_name = array();
                            foreach ($test2->result_array() as $row)  //Iterate through results
                            {
                               array_push($expert_name, $row['expert_name']);
                            }
                        }


                        // Get pub ids of related publications
                        $pubs =  array();
                        foreach ($unique_expert as $row)  //Iterate through results
                        {
                           $pub = $this->db->query("SELECT publication_id AS 'PUB_ID' FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias WHERE `match_success`= 'true' AND expert_id = '$row' AND publication_id IN" .$pub_id);
                           if ($pub->num_rows() > 0)  //Ensure that there is at least one result 
                           {
                                foreach ($pub->result_array() as $row)  //Iterate through results
                                {
                                  array_push($pubs, $row['PUB_ID']);  
                                }
                           }
                        }

                        $multiple_selection['pub_id'] = $pubs;
                        

                        $pub_array = implode(',', $pubs);
                        //echo $pub_array;

                        $no_pubs =  array();
                        foreach ($unique_expert as $row)  //Iterate through results
                        {
                           $query3 = $this->db->query("SELECT COUNT(*) AS 'PUB' FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias WHERE `match_success`= 'true' AND expert_id = '$row' AND publication_id IN" .$pub_id);
                            if ($query3->num_rows() > 0)  //Ensure that there is at least one result 
                            {
                                foreach ($query3->result_array() as $row)  //Iterate through results
                                {
                                      array_push($no_pubs, $row['PUB']);  
                                }
                            }


                       }

                        $no_citation =  array();
                        foreach ($unique_expert as $row)  //Iterate through results
                       {
                           $temp_pub =  array();
                           $query4 = $this->db->query("SELECT publication_id FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias WHERE `match_success`= 'true' AND expert_id = $row AND publication_id IN" .$pub_id);
                            if ($query4->num_rows() > 0)  //Ensure that there is at least one result 
                            {
                               foreach ($query4->result_array() as $row)  //Iterate through results
                               {
                                   array_push($temp_pub, $row['publication_id']);  
                               }
                            }

                           $in2 = '(' . implode(',', $temp_pub) .')';
                           //echo "pub_id" .$in2;
                           //echo "<br/>";
                           $query4 = $this->db->query("SELECT SUM(cited_by) AS 'NoCitation' FROM publication WHERE publication_id IN ".$in2);
                           if ($query4->num_rows() > 0)  //Ensure that there is at least one result 
                           {
                               foreach ($query4->result_array() as $row)  //Iterate through results
                               {
                                    if($row['NoCitation'] == '')
                                    {
                                        array_push($no_citation, 0);  
                                    }
                                    else
                                    {
                                      array_push($no_citation, $row['NoCitation']);  
                                    }
                                }
                            }
                       }

                       //$sum = 0;
                       $record = array();
                       $score = array();
                       $expertID = array();

                       for ($x = 0; $x < count($unique_expert); $x++) {
                            $multiple_selection = array();
                            //$associativeArray ['record_id'] = $x+1;
                            $multiple_selection['Keyword'] = $word;
                            $multiple_selection['ExpertId'] = $unique_expert[$x];
                            $multiple_selection['Expert_name'] = $expert_name[$x];
                            $multiple_selection['No_citation'] = $no_citation[$x];
                            $multiple_selection['No_publication'] = $no_pubs[$x];
                            $multiple_selection['temp_score'] = round(($no_pubs[$x]*0.8 + $no_citation[$x]*0.2), 2);

                            $Keyword = $multiple_selection['Keyword'];
                            $ExpertId = $multiple_selection['ExpertId'];
                            $Expert_name = $multiple_selection['Expert_name'];
                            $No_citation = $multiple_selection['No_citation'];
                            $No_publication = $multiple_selection['No_publication'];
                            $temp_score = $multiple_selection['temp_score'];
                            

                            $this->db->query("INSERT INTO `TEMP_INFO_INTERSECT` (`Keyword`,`expert_id`,`Expert_name`,`No_citation`,`No_publication`,`temp_score`) VALUES ('".$Keyword."','".$ExpertId."','".$Expert_name."','".$No_citation."','".$No_publication."','".$temp_score."')");

                        }

                        $total_pub = $this->db->query("SELECT SUM(No_publication) AS sum_of_pub FROM TEMP_INFO_INTERSECT WHERE Keyword = '$word'");
                        $total_cite = $this->db->query("SELECT SUM(No_citation) AS sum_of_cite FROM TEMP_INFO_INTERSECT WHERE Keyword = '$word'");

                        $max_score = $this->db->query("SELECT MAX(temp_score) AS max_score FROM TEMP_INFO_INTERSECT WHERE Keyword = '$word'");
                        $min_score = $this->db->query("SELECT MIN(temp_score) AS min_score FROM TEMP_INFO_INTERSECT WHERE Keyword = '$word'");

                        $total_pub = json_encode($total_pub->result());
                        $total_cite = json_encode($total_cite->result());

                        $max_score = json_encode($max_score->result());
                        $min_score = json_encode($min_score->result());

                        $total_pub = json_decode($total_pub, true);
                        $total_cite = json_decode($total_cite, true);

                        $max_score = json_decode($max_score, true);
                        $min_score = json_decode($min_score, true);

                        foreach ($total_pub as $key => $value) {
                            foreach ($value as $v) {
                                $total_pub = $v;
                            }
                        }

                        foreach ($total_cite as $key => $value) {
                            foreach ($value as $v) {
                                $total_cite = $v;
                            }
                        }

                        foreach ($max_score as $key => $value) {
                            foreach ($value as $v) {
                                $max_score = $v;
                            }
                        }

                        foreach ($min_score as $key => $value) {
                            foreach ($value as $v) {
                                $min_score = $v;
                            }
                        }
                        //echo("total pub =". $total_pub);
                        //echo("total site =". $total_cite);

                        if ($total_pub == 0) {
                            $total_pub = 1;
                        }

                        if ($total_cite == 0) {
                            $total_cite = 1;
                        }

                        $diff = round($max_score-$min_score,2);

                        if ($diff == 0) {
                            $diff = 1;
                        }
                        
                       for ($x = 0; $x < count($unique_expert); $x++) {
                            $multiple_selection = array();
                            //$associativeArray ['record_id'] = $x+1;
                            $multiple_selection['Keyword'] = $word;
                            $multiple_selection['ExpertId'] = $unique_expert[$x];
                            $multiple_selection['Expert_name'] = $expert_name[$x];
                            $multiple_selection['No_citation'] = $no_citation[$x];
                            $multiple_selection['No_publication'] = $no_pubs[$x];
                            $multiple_selection['temp_score'] = round(($no_pubs[$x]*0.8 + $no_citation[$x]*0.2), 2);
                            $sum_score = round(((round(($no_pubs[$x]*0.8 + $no_citation[$x]*0.2), 2)-(round($min_score,2)))/$diff),2);

                            if ($min_score == $max_score) {
                                $sum_score = 1;
                            }
                            $multiple_selection['Sum_of_score'] = $sum_score;

                            $Keyword = $multiple_selection['Keyword'];
                            $ExpertId = $multiple_selection['ExpertId'];
                            $Expert_name = $multiple_selection['Expert_name'];
                            $No_citation = $multiple_selection['No_citation'];
                            $No_publication = $multiple_selection['No_publication'];
                            $temp_score = $multiple_selection['temp_score'];
                            $Sum_of_score = $multiple_selection['Sum_of_score'];
                            
                            for( $i=0; $i < count($multiple_selection); $i++) {
                                $arrays = implode("," , $multiple_selection);
                            }

                            $this->db->query("INSERT INTO `TEMP_INTERSECT` (`Keyword`,`expert_id`,`Expert_name`,`No_citation`,`No_publication`,`Sum_of_score`,`temp_score`) VALUES ('".$Keyword."','".$ExpertId."','".$Expert_name."','".$No_citation."','".$No_publication."','".$Sum_of_score."','".$temp_score."')");

                            $exp_basic_info = $this->Expert_Profile_Model->get_exp_basic_info($ExpertId);
                            //print_r($exp_basic_info);
                                foreach($exp_basic_info as $row){
                                    $multiple_selection['exp_photo'] =  $row['expert_photo'];
                                    $exp_photo =  $multiple_selection['exp_photo'];
                                    $this->db->query("UPDATE `TEMP_INTERSECT` SET photo = '$exp_photo' WHERE expert_id = '$ExpertId'");
                                }
                                
                            //$get_photo = $this->db->query("SELECT expert_photo FROM `EXPERT` WHERE expert_id = '$ExpertId'");

                            //echo $arrays;
                            //echo "<br/>";
                            array_push($expertID, $ExpertId);
                            array_push($score, $Sum_of_score);
                            array_push($record, $arrays);
                        }

                        $query6 = $this->db->query("SELECT * FROM `TEMP_INTERSECT`");
                        array_push($temp_rank, $query6->result());
                        $json_temp = json_encode($temp_rank);
                        $array_rank = json_decode($json_temp, true);
                        return $array_rank;
                        //return $multiple_selection['rank_list'];
                        /*$this->db->query("SELECT * FROM `temp` WHERE expert_id IN (SELECT * FROM (SELECT expert_id FROM `temp` HAVING COUNT expert_id) > 1) AS a);");*/

                        /*SELECT 
                            Expert_name,count(Expert_name)
                        FROM
                            temp_exp_list
                        GROUP BY Expert_name
                        HAVING COUNT(Expert_name) > 1;*/

                        /*INSERT INTO temp_exp_list (Keyword, Expert_name, expert_id, No_citation, No_publication, temp_score, Sum_of_score) SELECT Keyword, Expert_name, expert_id, No_citation, No_publication, temp_score, Sum_of_score FROM temp WHERE Keyword IN ('keyword1','keyword2');*/


        }

        public function getKeywords()
        {
            $word = $this->input->post('mydata');
            print_r($word);
            //return $word;
        }

    }
?>