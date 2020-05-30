<?php

	class Keywords_Intersect extends CI_Controller
	{
		
        function __construct() { 
            parent::__construct(); 
            $this->load->helper('url'); 
            $this->load->database(); 
            $this->load->model('Expert_Profile_Model'); // load model 
            $this->load->library('javascript');
            $this->load->library('session');
        }

        public function index()
        {
            $input_query = $this->input->get('q');
            if ($input_query == "*:*"){
                $input_query = "";
            }

            $keywords_intersect['search_query'] = $input_query;
            $this->db->query("DELETE FROM `temp_exp_list`");
            $this->db->query("alter table temp_exp_list AUTO_INCREMENT = 1");
            //$this->db->query("DELETE FROM `intersect_experts`");
            //$this->db->query("alter table intersect_experts AUTO_INCREMENT = 1");
            $this->db->query("DROP TABLE `intersect_experts`");
            
            $array = array();
            $expertName = array();
            $ext_keyword = array();
            $sum_score = array();
            $expid = array();
            $intersect_rank = array();
            $expertScore_k1 = array();
            $expertScore_k2 = array();
            $expertScore_k3 = array();
            $expertScore_k4 = array();
            $expertScore_k5 = array();
            $array = $this->input->get('word');
            $word = explode(",", $array);
            $keywords_intersect['keywords'] = $word;
            $stringWord = implode(",", $word);
            $keywords_intersect['keywordsString'] = $stringWord;
            if(count($word) == 2){
                [$keyword1,$keyword2] = $word;

                $query = $this->db->query("CREATE TABLE `intersect_experts` (record_id int NOT NULL AUTO_INCREMENT,Expert_name varchar(255) NOT NULL, expert_id int,sum_score float,score1 float, score2 float,photo TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,PRIMARY KEY (record_id));");

                $query1 = $this->db->query("INSERT INTO temp_exp_list (Keyword, Expert_name, expert_id, No_citation, No_publication, temp_score, Sum_of_score) SELECT Keyword, Expert_name, expert_id, No_citation, No_publication, temp_score, Sum_of_score FROM SHORTLISTED_EXPERTS WHERE Keyword IN ('".$keyword1."','".$keyword2."')");

                $query2 = $this->db->query("SELECT 
                            Expert_name
                        FROM
                            temp_exp_list
                        GROUP BY Expert_name
                        HAVING COUNT(Expert_name) > 1");

                if ($query2->num_rows() > 0 || $query2->num_rows() != 0)  //Ensure that there is at least one result 
                {
                    foreach ($query2->result_array() as $row)  //Iterate through results
                    {
                       array_push($expertName, $row['Expert_name']);
                       //$this->db->query("INSERT INTO intersect_experts (Expert_name) VALUES ('".$row['Expert_name']."')");
                    }
                    $keywords_intersect['expertName'] = $expertName;
                    foreach($expertName as $name){

                        $expertid = $this->db->query("SELECT DISTINCT(expert_id) FROM temp_exp_list WHERE Expert_name = '$name'");
                        if ($expertid->num_rows() > 0 || $expertid->num_rows() != 0)  //Ensure that there is at least one result 
                        {
                            foreach ($expertid->result_array() as $row)  //Iterate through results
                            {
                               array_push($expid, $row['expert_id']);
                            }
                        }
                        //echo $name;
                        $query3 = $this->db->query("SELECT Sum_of_score FROM temp_exp_list WHERE Keyword = '$keyword1' AND Expert_name = '$name'");
                        if ($query3->num_rows() > 0 || $query3->num_rows() != 0)  //Ensure that there is at least one result 
                        {
                            foreach ($query3->result_array() as $row)  //Iterate through results
                            {
                               array_push($expertScore_k1, $row['Sum_of_score']);
                            }
                        }
                    
                        $query4 = $this->db->query("SELECT Sum_of_score FROM temp_exp_list WHERE Keyword = '$keyword2' AND Expert_name = '$name'");
                        if ($query4->num_rows() > 0 || $query4->num_rows() != 0)  //Ensure that there is at least one result 
                        {
                            foreach ($query4->result_array() as $row)  //Iterate through results
                            {
                               array_push($expertScore_k2, $row['Sum_of_score']);
                            }
                        }

                        for ($i = 0; $i < count($expertScore_k1); $i++) {
                            $expScore1 = $expertScore_k1[$i];
                            for ($j = 0; $j < count($expertScore_k2); $j++) {
                                $expScore2 = $expertScore_k2[$j];
                                $total_score = round(($expertScore_k1[$i]+$expertScore_k2[$j])/2,2);
                            }
                        }

                        for($d = 0; $d < count($expid); $d++){
                            $expID = $expid[$d];
                        }
                        array_push($sum_score, $total_score);
                        //echo($expID);
                        $this->db->query("INSERT INTO intersect_experts (Expert_name,sum_score) VALUES ('".$name."','".$total_score."')");
                        $this->db->query("UPDATE intersect_experts SET expert_id = '$expID' WHERE Expert_name = '$name'");
                        $this->db->query("UPDATE intersect_experts SET score1 = '$expScore1' WHERE Expert_name = '$name'");
                        $this->db->query("UPDATE intersect_experts SET score2 = '$expScore2' WHERE Expert_name = '$name'");
                        
                        foreach($expid as $id){
                            //echo $id;
                            $exp_basic_info = $this->Expert_Profile_Model->get_exp_basic_info($id);
                            foreach($exp_basic_info as $row){
                                $keywords_intersect['exp_id'] =  $row['expert_id'];
                                $keywords_intersect['exp_photo'] =  $row['expert_photo'];
                                $exp_id = $keywords_intersect['exp_id'];
                                $exp_photo =  $keywords_intersect['exp_photo'];
                                $this->db->query("UPDATE intersect_experts SET photo ='$exp_photo' WHERE expert_id = '$exp_id'");
                            }
                        }
                        
                    }
                    $query5 = $this->db->query("SELECT * FROM `intersect_experts` ORDER BY sum_score DESC LIMIT 5");
                    array_push($intersect_rank, $query5->result());
                    $keywords_intersect['expertScore_k1'] = $expertScore_k1;
                    $keywords_intersect['expertScore_k2'] = $expertScore_k2;
                    $keywords_intersect['sum_score'] = $sum_score;

                    $json_temp = json_encode($intersect_rank);
                    $array_rank = json_decode($json_temp, true);
                    $keywords_intersect['rank_list'] = $array_rank;
                }
                else{
                    //echo("no result");
                    $array_rank = array();
                    $keywords_intersect['rank_list'] = $array_rank;

                }

            }else if(count($word) == 3){
                [$keyword1,$keyword2,$keyword3] = $word;

                $query = $this->db->query("CREATE TABLE `intersect_experts` (record_id int NOT NULL AUTO_INCREMENT,Expert_name varchar(255) NOT NULL, expert_id int,sum_score float,score1 float, score2 float, score3 float,photo TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,PRIMARY KEY (record_id));");

                $query1 = $this->db->query("INSERT INTO temp_exp_list (Keyword, Expert_name, expert_id, No_citation, No_publication, temp_score, Sum_of_score) SELECT Keyword, Expert_name, expert_id, No_citation, No_publication, temp_score, Sum_of_score FROM SHORTLISTED_EXPERTS WHERE Keyword IN ('".$keyword1."','".$keyword2."','".$keyword3."')");

                $query2 = $this->db->query("SELECT 
                            Expert_name
                        FROM
                            temp_exp_list
                        GROUP BY Expert_name
                        HAVING COUNT(Expert_name) > 2");

                if ($query2->num_rows() > 0 || $query2->num_rows() != 0)  //Ensure that there is at least one result 
                {
                    foreach ($query2->result_array() as $row)  //Iterate through results
                    {
                       array_push($expertName, $row['Expert_name']);
                       //$this->db->query("INSERT INTO intersect_experts (Expert_name) VALUES ('".$row['Expert_name']."')");
                    }
                    $keywords_intersect['expertName'] = $expertName;
                    foreach($expertName as $name){

                        $expertid = $this->db->query("SELECT DISTINCT(expert_id) FROM temp_exp_list WHERE Expert_name = '$name'");
                        if ($expertid->num_rows() > 0 || $expertid->num_rows() != 0)  //Ensure that there is at least one result 
                        {
                            foreach ($expertid->result_array() as $row)  //Iterate through results
                            {
                               array_push($expid, $row['expert_id']);
                            }
                        }

                        $query3 = $this->db->query("SELECT Sum_of_score FROM temp_exp_list WHERE Keyword = '$keyword1' AND Expert_name = '$name'");
                        if ($query3->num_rows() > 0 || $query3->num_rows() != 0)  //Ensure that there is at least one result 
                        {
                            foreach ($query3->result_array() as $row)  //Iterate through results
                            {
                               array_push($expertScore_k1, $row['Sum_of_score']);
                            }
                        }
                    
                        $query4 = $this->db->query("SELECT Sum_of_score FROM temp_exp_list WHERE Keyword = '$keyword2' AND Expert_name = '$name'");
                        if ($query4->num_rows() > 0 || $query4->num_rows() != 0)  //Ensure that there is at least one result 
                        {
                            foreach ($query4->result_array() as $row)  //Iterate through results
                            {
                               array_push($expertScore_k2, $row['Sum_of_score']);
                            }
                        }

                        $query5 = $this->db->query("SELECT Sum_of_score FROM temp_exp_list WHERE Keyword = '$keyword2' AND Expert_name = '$name'");
                        if ($query5->num_rows() > 0 || $query5->num_rows() != 0)  //Ensure that there is at least one result 
                        {
                            foreach ($query5->result_array() as $row)  //Iterate through results
                            {
                               array_push($expertScore_k3, $row['Sum_of_score']);
                            }
                        }

                        for ($i = 0; $i < count($expertScore_k1); $i++) {
                            $expScore1 = $expertScore_k1[$i];
                            for ($j = 0; $j < count($expertScore_k2); $j++) {
                                $expScore2 = $expertScore_k2[$j];
                                for ($k = 0; $k < count($expertScore_k3); $k++) {
                                    $expScore3 = $expertScore_k3[$k];
                                    $total_score = round(($expertScore_k1[$i]+$expertScore_k2[$j]+$expertScore_k3[$k])/3,2);
                                }
                            }
                        }

                        for($d = 0; $d < count($expid); $d++){
                            $expID = $expid[$d];
                        }

                        array_push($sum_score, $total_score);
                        $this->db->query("INSERT INTO intersect_experts (Expert_name,sum_score) VALUES ('".$name."','".$total_score."')");
                        $this->db->query("UPDATE intersect_experts SET expert_id = '$expID' WHERE Expert_name = '$name'");
                        $this->db->query("UPDATE intersect_experts SET score1 = '$expScore1' WHERE Expert_name = '$name'");
                        $this->db->query("UPDATE intersect_experts SET score2 = '$expScore2' WHERE Expert_name = '$name'");
                        $this->db->query("UPDATE intersect_experts SET score3 = '$expScore3' WHERE Expert_name = '$name'");

                        foreach($expid as $id){
                            //echo $id;
                            $exp_basic_info = $this->Expert_Profile_Model->get_exp_basic_info($id);
                            foreach($exp_basic_info as $row){
                                $keywords_intersect['exp_id'] =  $row['expert_id'];
                                $keywords_intersect['exp_photo'] =  $row['expert_photo'];
                                $exp_id = $keywords_intersect['exp_id'];
                                $exp_photo =  $keywords_intersect['exp_photo'];
                                $this->db->query("UPDATE intersect_experts SET photo ='$exp_photo' WHERE expert_id = '$exp_id'");
                            }
                        }
                        
                    }
                    $query6 = $this->db->query("SELECT * FROM `intersect_experts` ORDER BY sum_score DESC LIMIT 5");
                    array_push($intersect_rank, $query6->result());
                    $keywords_intersect['expertScore_k1'] = $expertScore_k1;
                    $keywords_intersect['expertScore_k2'] = $expertScore_k2;
                    $keywords_intersect['expertScore_k3'] = $expertScore_k3;
                    $keywords_intersect['sum_score'] = $sum_score;

                    $json_temp = json_encode($intersect_rank);
                    $array_rank = json_decode($json_temp, true);
                    $keywords_intersect['rank_list'] = $array_rank;
                }
                else{
                    //echo("no result");
                    $array_rank = array();
                    $keywords_intersect['rank_list'] = $array_rank;

                }
                
            }else if(count($word) == 4){
                [$keyword1,$keyword2,$keyword3,$keyword4] = $word;

                $query = $this->db->query("CREATE TABLE `intersect_experts` (record_id int NOT NULL AUTO_INCREMENT,Expert_name varchar(255) NOT NULL, expert_id int,sum_score float,score1 float, score2 float, score3 float, score4 float,photo TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,PRIMARY KEY (record_id));");

                $query1 = $this->db->query("INSERT INTO temp_exp_list (Keyword, Expert_name, expert_id, No_citation, No_publication, temp_score, Sum_of_score) SELECT Keyword, Expert_name, expert_id, No_citation, No_publication, temp_score, Sum_of_score FROM SHORTLISTED_EXPERTS WHERE Keyword IN ('".$keyword1."','".$keyword2."','".$keyword3."','".$keyword4."')");

                $query2 = $this->db->query("SELECT 
                            Expert_name
                        FROM
                            temp_exp_list
                        GROUP BY Expert_name
                        HAVING COUNT(Expert_name) > 3");

                if ($query2->num_rows() > 0 || $query2->num_rows() != 0)  //Ensure that there is at least one result 
                {
                    foreach ($query2->result_array() as $row)  //Iterate through results
                    {
                       array_push($expertName, $row['Expert_name']);
                       //$this->db->query("INSERT INTO intersect_experts (Expert_name) VALUES ('".$row['Expert_name']."')");
                    }
                    $keywords_intersect['expertName'] = $expertName;

                    foreach($expertName as $name){
                        
                        $expertid = $this->db->query("SELECT DISTINCT(expert_id) FROM temp_exp_list WHERE Expert_name = '$name'");
                        if ($expertid->num_rows() > 0 || $expertid->num_rows() != 0)  //Ensure that there is at least one result 
                        {
                            foreach ($expertid->result_array() as $row)  //Iterate through results
                            {
                               array_push($expid, $row['expert_id']);
                            }
                        }

                        $query3 = $this->db->query("SELECT Sum_of_score FROM temp_exp_list WHERE Keyword = '$keyword1' AND Expert_name = '$name'");
                        if ($query3->num_rows() > 0 || $query3->num_rows() != 0)  //Ensure that there is at least one result 
                        {
                            foreach ($query3->result_array() as $row)  //Iterate through results
                            {
                               array_push($expertScore_k1, $row['Sum_of_score']);
                            }
                        }
                    
                        $query4 = $this->db->query("SELECT Sum_of_score FROM temp_exp_list WHERE Keyword = '$keyword2' AND Expert_name = '$name'");
                        if ($query4->num_rows() > 0 || $query4->num_rows() != 0)  //Ensure that there is at least one result 
                        {
                            foreach ($query4->result_array() as $row)  //Iterate through results
                            {
                               array_push($expertScore_k2, $row['Sum_of_score']);
                            }
                        }

                        $query5 = $this->db->query("SELECT Sum_of_score FROM temp_exp_list WHERE Keyword = '$keyword2' AND Expert_name = '$name'");
                        if ($query5->num_rows() > 0 || $query5->num_rows() != 0)  //Ensure that there is at least one result 
                        {
                            foreach ($query5->result_array() as $row)  //Iterate through results
                            {
                               array_push($expertScore_k3, $row['Sum_of_score']);
                            }
                        }

                        $query6 = $this->db->query("SELECT Sum_of_score FROM temp_exp_list WHERE Keyword = '$keyword2' AND Expert_name = '$name'");
                        if ($query6->num_rows() > 0 || $query6->num_rows() != 0)  //Ensure that there is at least one result 
                        {
                            foreach ($query6->result_array() as $row)  //Iterate through results
                            {
                               array_push($expertScore_k4, $row['Sum_of_score']);
                            }
                        }

                        for ($i = 0; $i < count($expertScore_k1); $i++) {
                            $expScore1 = $expertScore_k1[$i];
                            for ($j = 0; $j < count($expertScore_k2); $j++) {
                                $expScore2 = $expertScore_k2[$j];
                                for ($k = 0; $k < count($expertScore_k3); $k++) {
                                    $expScore3 = $expertScore_k3[$k];
                                    for ($l = 0; $l < count($expertScore_k4); $l++) {
                                        $expScore4 = $expertScore_k4[$l];
                                        $total_score = round(($expertScore_k1[$i]+$expertScore_k2[$j]+$expertScore_k3[$k]+$expertScore_k4[$l])/4,2);
                                    }
                                }
                            }
                        }

                        for($d = 0; $d < count($expid); $d++){
                            $expID = $expid[$d];
                        }

                        array_push($sum_score, $total_score);
                        $this->db->query("INSERT INTO intersect_experts (Expert_name,sum_score) VALUES ('".$name."','".$total_score."')");
                        $this->db->query("UPDATE intersect_experts SET expert_id = '$expID' WHERE Expert_name = '$name'");
                        $this->db->query("UPDATE intersect_experts SET score1 = '$expScore1' WHERE Expert_name = '$name'");
                        $this->db->query("UPDATE intersect_experts SET score2 = '$expScore2' WHERE Expert_name = '$name'");
                        $this->db->query("UPDATE intersect_experts SET score3 = '$expScore3' WHERE Expert_name = '$name'");
                        $this->db->query("UPDATE intersect_experts SET score4 = '$expScore4' WHERE Expert_name = '$name'");

                        foreach($expid as $id){
                            //echo $id;
                            $exp_basic_info = $this->Expert_Profile_Model->get_exp_basic_info($id);
                            foreach($exp_basic_info as $row){
                                $keywords_intersect['exp_id'] =  $row['expert_id'];
                                $keywords_intersect['exp_photo'] =  $row['expert_photo'];
                                $exp_id = $keywords_intersect['exp_id'];
                                $exp_photo =  $keywords_intersect['exp_photo'];
                                $this->db->query("UPDATE intersect_experts SET photo ='$exp_photo' WHERE expert_id = '$exp_id'");
                            }
                        }
                        
                    }
                    $query7 = $this->db->query("SELECT * FROM `intersect_experts` ORDER BY sum_score DESC LIMIT 5");
                    array_push($intersect_rank, $query7->result());
                    $keywords_intersect['expertScore_k1'] = $expertScore_k1;
                    $keywords_intersect['expertScore_k2'] = $expertScore_k2;
                    $keywords_intersect['expertScore_k3'] = $expertScore_k3;
                    $keywords_intersect['expertScore_k4'] = $expertScore_k4;
                    $keywords_intersect['sum_score'] = $sum_score;

                    $json_temp = json_encode($intersect_rank);
                    $array_rank = json_decode($json_temp, true);
                    $keywords_intersect['rank_list'] = $array_rank;
                }
                else{
                    //echo("no result");
                    $array_rank = array();
                    $keywords_intersect['rank_list'] = $array_rank;
                }
                

            }else if(count($word) == 5){
                [$keyword1,$keyword2,$keyword3,$keyword4,$keyword5] = $word;

                $query = $this->db->query("CREATE TABLE `intersect_experts` (record_id int NOT NULL AUTO_INCREMENT,Expert_name varchar(255) NOT NULL, expert_id int,sum_score float,score1 float, score2 float, score3 float, score4 float, score5 float,photo TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,PRIMARY KEY (record_id));");

                $query1 = $this->db->query("INSERT INTO temp_exp_list (Keyword, Expert_name, expert_id, No_citation, No_publication, temp_score, Sum_of_score) SELECT Keyword, Expert_name, expert_id, No_citation, No_publication, temp_score, Sum_of_score FROM SHORTLISTED_EXPERTS WHERE Keyword IN ('".$keyword1."','".$keyword2."','".$keyword3."','".$keyword4."','".$keyword5."')");

                $query2 = $this->db->query("SELECT 
                            Expert_name
                        FROM
                            temp_exp_list
                        GROUP BY Expert_name
                        HAVING COUNT(Expert_name) > 4");

                if ($query2->num_rows() > 0 || $query2->num_rows() != 0)  //Ensure that there is at least one result 
                {
                    foreach ($query2->result_array() as $row)  //Iterate through results
                    {
                       array_push($expertName, $row['Expert_name']);
                       //$this->db->query("INSERT INTO intersect_experts (Expert_name) VALUES ('".$row['Expert_name']."')");
                    }
                    $keywords_intersect['expertName'] = $expertName;
                    foreach($expertName as $name){
                        
                        $expertid = $this->db->query("SELECT DISTINCT(expert_id) FROM temp_exp_list WHERE Expert_name = '$name'");
                        if ($expertid->num_rows() > 0 || $expertid->num_rows() != 0)  //Ensure that there is at least one result 
                        {
                            foreach ($expertid->result_array() as $row)  //Iterate through results
                            {
                               array_push($expid, $row['expert_id']);
                            }
                        }

                        $query3 = $this->db->query("SELECT Sum_of_score FROM temp_exp_list WHERE Keyword = '$keyword1' AND Expert_name = '$name'");
                        if ($query3->num_rows() > 0 || $query3->num_rows() != 0)  //Ensure that there is at least one result 
                        {
                            foreach ($query3->result_array() as $row)  //Iterate through results
                            {
                               array_push($expertScore_k1, $row['Sum_of_score']);
                            }
                        }
                    
                        $query4 = $this->db->query("SELECT Sum_of_score FROM temp_exp_list WHERE Keyword = '$keyword2' AND Expert_name = '$name'");
                        if ($query4->num_rows() > 0 || $query4->num_rows() != 0)  //Ensure that there is at least one result 
                        {
                            foreach ($query4->result_array() as $row)  //Iterate through results
                            {
                               array_push($expertScore_k2, $row['Sum_of_score']);
                            }
                        }

                        $query5 = $this->db->query("SELECT Sum_of_score FROM temp_exp_list WHERE Keyword = '$keyword3' AND Expert_name = '$name'");
                        if ($query5->num_rows() > 0 || $query5->num_rows() != 0)  //Ensure that there is at least one result 
                        {
                            foreach ($query5->result_array() as $row)  //Iterate through results
                            {
                               array_push($expertScore_k3, $row['Sum_of_score']);
                            }
                        }

                        $query6 = $this->db->query("SELECT Sum_of_score FROM temp_exp_list WHERE Keyword = '$keyword4' AND Expert_name = '$name'");
                        if ($query6->num_rows() > 0 || $query6->num_rows() != 0)  //Ensure that there is at least one result 
                        {
                            foreach ($query6->result_array() as $row)  //Iterate through results
                            {
                               array_push($expertScore_k4, $row['Sum_of_score']);
                            }
                        }

                        $query7 = $this->db->query("SELECT Sum_of_score FROM temp_exp_list WHERE Keyword = '$keyword5' AND Expert_name = '$name'");
                        if ($query7->num_rows() > 0 || $query7->num_rows() != 0)  //Ensure that there is at least one result 
                        {
                            foreach ($query7->result_array() as $row)  //Iterate through results
                            {
                               array_push($expertScore_k5, $row['Sum_of_score']);
                            }
                        }

                        for ($i = 0; $i < count($expertScore_k1); $i++) {
                            $expScore1 = $expertScore_k1[$i];
                            for ($j = 0; $j < count($expertScore_k2); $j++) {
                                $expScore2 = $expertScore_k2[$j];
                                for ($k = 0; $k < count($expertScore_k3); $k++) {
                                    $expScore3 = $expertScore_k3[$k];
                                    for ($l = 0; $l < count($expertScore_k4); $l++) {
                                        $expScore4 = $expertScore_k4[$l];
                                        for ($m = 0; $m < count($expertScore_k5); $m++) {
                                            $expScore5 = $expertScore_k5[$m];
                                            $total_score = round(($expertScore_k1[$i]+$expertScore_k2[$j]+$expertScore_k3[$k]+$expertScore_k4[$l]+$expertScore_k5[$m])/5,2);
                                        }
                                    }
                                }
                            }
                        }

                        for($d = 0; $d < count($expid); $d++){
                            $expID = $expid[$d];
                        }

                        array_push($sum_score, $total_score);
                        $this->db->query("INSERT INTO intersect_experts (Expert_name,sum_score) VALUES ('".$name."','".$total_score."')");
                        $this->db->query("UPDATE intersect_experts SET expert_id = '$expID' WHERE Expert_name = '$name'");
                        $this->db->query("UPDATE intersect_experts SET score1 = '$expScore1' WHERE Expert_name = '$name'");
                        $this->db->query("UPDATE intersect_experts SET score2 = '$expScore2' WHERE Expert_name = '$name'");
                        $this->db->query("UPDATE intersect_experts SET score3 = '$expScore3' WHERE Expert_name = '$name'");
                        $this->db->query("UPDATE intersect_experts SET score4 = '$expScore4' WHERE Expert_name = '$name'");
                        $this->db->query("UPDATE intersect_experts SET score5 = '$expScore5' WHERE Expert_name = '$name'");

                        foreach($expid as $id){
                            //echo $id;
                            $exp_basic_info = $this->Expert_Profile_Model->get_exp_basic_info($id);
                            foreach($exp_basic_info as $row){
                                $keywords_intersect['exp_id'] =  $row['expert_id'];
                                $keywords_intersect['exp_photo'] =  $row['expert_photo'];
                                $exp_id = $keywords_intersect['exp_id'];
                                $exp_photo =  $keywords_intersect['exp_photo'];
                                $this->db->query("UPDATE intersect_experts SET photo ='$exp_photo' WHERE expert_id = '$exp_id'");
                            }
                        }
                    }
                    $query8 = $this->db->query("SELECT * FROM `intersect_experts` ORDER BY sum_score DESC LIMIT 5");
                    array_push($intersect_rank, $query8->result());
                    $keywords_intersect['expertScore_k1'] = $expertScore_k1;
                    $keywords_intersect['expertScore_k2'] = $expertScore_k2;
                    $keywords_intersect['expertScore_k3'] = $expertScore_k3;
                    $keywords_intersect['expertScore_k4'] = $expertScore_k4;
                    $keywords_intersect['expertScore_k5'] = $expertScore_k5;
                    $keywords_intersect['sum_score'] = $sum_score;

                    $json_temp = json_encode($intersect_rank);
                    $array_rank = json_decode($json_temp, true);
                    $keywords_intersect['rank_list'] = $array_rank;
                }
                else{
                    //echo("no result");
                    $array_rank = array();
                    $keywords_intersect['rank_list'] = $array_rank;
                }
                
            }
            
        	$this->load->view('templates/header');
           	$this->load->view('keywords_intersect', $keywords_intersect); 
			$this->load->view('templates/footer');
        } 


        public function getKeywords()
        {
            $word = $this->input->post('mydata');
            print_r($word);
            //return $word;
        }

    }
?>