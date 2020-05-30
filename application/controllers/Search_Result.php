<?php
use Phpml\Tokenization\NGramTokenizer;
use Phpml\Tokenization\WordTokenizer;
use Phpml\Tokenization\WhitespaceTokenizer;
use Nadar\Stemming\Stemm;
use Nadar\Stemming\StemmingInterface;

require 'vendor\rake-php-plus-master\src\AbstractStopwordProvider.php';
require 'vendor\rake-php-plus-master\src\StopwordArray.php';
require 'vendor\rake-php-plus-master\src\StopwordsPatternFile.php';
require 'vendor\rake-php-plus-master\src\StopwordsPHP.php';
require 'vendor\rake-php-plus-master\src\RakePlus.php';
require 'vendor\text-miner-master\TextMiner.php';
require 'vendor\stemming-master\src\Stemm.php';
require 'vendor\stemming-master\src\StemmerInterface.php';
require 'vendor\stemming-master\src\Stemms\EnglishStemmer.php';

use DonatelloZa\RakePlus\RakePlus;
	class Search_Result extends CI_Controller
	{
		
        function __construct() { 
            parent::__construct(); 
            $this->load->helper('url'); 
            $this->load->database(); 
            $this->load->model('Expert_Profile_Model'); // load model 
            $this->load->library('javascript');
            //$this->load->library('session');
        }


        public function  index()
        {
        	$input_query = $this->input->get('q');
        	if ($input_query == "*:*"){
                $input_query = "";
            }
            if(isset($input_query)){
            	$this->db->query("DELETE FROM `SHORTLISTED_EXPERTS`");
            	$this->db->query("alter table TEMP AUTO_INCREMENT = 1");
            	$this->db->query("DELETE FROM `TEMP_EXP`");
            	$this->db->query("alter table TEMP_EXP AUTO_INCREMENT = 1");
            	$this->db->query("DELETE FROM `TEMP_INFO`");
            	$this->db->query("alter table TEMP_INFO AUTO_INCREMENT = 1");
            	$this->db->query("DELETE FROM `INTERSECT_EXPERTS`");
            	$this->db->query("alter table INTERSECT_EXPERTS AUTO_INCREMENT = 1");

            	//$search_result['input_query'] = new TextMiner();
            	$newPhrases = array();
            	$search_result['input_query'] = $input_query;
            	$search_result['input_query'] = explode(" ", $search_result['input_query']);
            	//$search_result['input_query'] = str_replace("-"," ",$search_result['input_query']);
            	$search_result['input_query'] = str_replace("&"," ",$search_result['input_query']);
            	$search_result['input_query'] = str_replace("("," ",$search_result['input_query']);
            	$search_result['input_query'] = str_replace(")"," ",$search_result['input_query']);
            	$search_result['input_query'] = str_replace("/"," ",$search_result['input_query']);
            	$search_result['input_query'] = str_replace('"'," ",$search_result['input_query']);
            	$search_result['input_query'] = str_replace('{'," ",$search_result['input_query']);
            	$search_result['input_query'] = str_replace('}'," ",$search_result['input_query']);
            	$search_result['input_query'] = str_replace('#'," ",$search_result['input_query']);
            	$search_result['input_query'] = str_replace(array("\n", "\t", ' '), '', $search_result['input_query']);
            	$search_result['input_query'] = array_unique($search_result['input_query']);
            	if(count($search_result['input_query'])>=80){
            		//$search_result['exceedNumberOfKeywords'] = "Search input exceeds maximum number of words.";
            		//$numkeywords = array();
            		$numkeywords = count($search_result['input_query']);
            		$search_result['num_input'] = $numkeywords;
            		$search_result['rank_list'] = array();
            		$search_result['search_query'] = $input_query;
            	}
            	else if(count($search_result['input_query'])<80){
            		$numkeywords = count($search_result['input_query']);
            		foreach($search_result['input_query'] as $phrase){
	            		$phrase = Stemm::stem($phrase,'en');
	            		//echo("Stemming keywords : ");
	            		//echo($phrase);
	            		//echo "</br>";
	            		array_push($newPhrases,$phrase);
	            	}

	            	$newPhrases = implode(" ",$newPhrases);
	            	$search_result['phrases'] = $newPhrases;
	            	$search_result['phrases'] = RakePlus::create($search_result['phrases'],'vendor\rake-php-plus-master\lang\en_US.php',1)->get();
	            	//$search_result['phrases'] = $newPhrases;
	            	//$keywords = explode(',', $search_result['phrases']);
	            	//$search_result['phrases'] = tokenize($search_result['input_query']);
	            	$keywords = $search_result['phrases'];
	            	$array_rank = array();
	            	$temp_rank = array();
	            	$temp_id = array();
	            	$array_id = array();
	            	$temp_info = array();
	            	$ext_keyword = array();
	            	$keywords = array_unique($keywords);
	            	foreach ($keywords as $word){ 
	            		echo($word);
	            		//echo("--");
	            		echo("<br/>");
	            		$query1 = $this->db->query("SELECT publication_id FROM publication WHERE index_keywords LIKE '%$word%' OR author_keywords LIKE '%$word%' OR title LIKE '%$word%' OR abstract LIKE '%$word%'");
	            		if ($query1->num_rows() > 0 || $query1->num_rows() != 0)  //Ensure that there is at least one result 
						{
							array_push($ext_keyword,$word);
							$pub_id = array();
							foreach ($query1->result_array() as $row)  //Iterate through results
							{
							   array_push($pub_id, $row['publication_id']);
							}

							$in = '(' . implode(',', $pub_id) .')';
							//echo($in);
							$query2 = $this->db->query("SELECT alias_id FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias WHERE publication_id IN ".$in);

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
						   	   $pub = $this->db->query("SELECT publication_id AS 'PUB_ID' FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias WHERE `match_success`= 'true' AND expert_id = '$row' AND publication_id IN" .$in);
						   	   foreach ($pub->result_array() as $row)  //Iterate through results
							   {
								   	  array_push($pubs, $row['PUB_ID']);  
							   }
						    }

						    $search_result['pub_id'] = $pubs;
							$pub_array = implode(',', $pubs);
							//echo $pub_array;

						    $no_pubs =  array();
						    foreach ($unique_expert as $row)  //Iterate through results
						    {
						   	   $query3 = $this->db->query("SELECT COUNT(*) AS 'PUB' FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias WHERE `match_success`= 'true' AND expert_id = '$row' AND publication_id IN" .$in);
						   	   foreach ($query3->result_array() as $row)  //Iterate through results
								{
								   	  array_push($no_pubs, $row['PUB']);  
								}
						   }

							$no_citation =  array();
							foreach ($unique_expert as $row)  //Iterate through results
						    {
						   	   $temp_pub =  array();
						   	   $query4 = $this->db->query("SELECT publication_id FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias WHERE `match_success`= 'true' AND expert_id = $row AND publication_id IN" .$in);
						   	   foreach ($query4->result_array() as $row)  //Iterate through results
							   {
								   array_push($temp_pub, $row['publication_id']);  
							   }

							   $in2 = '(' . implode(',', $temp_pub) .')';
							   //echo "pub_id" .$in2;
							   //echo "<br/>";
						   	   $query4 = $this->db->query("SELECT SUM(cited_by) AS 'NoCitation' FROM publication WHERE publication_id IN ".$in2);

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

						   //$sum = 0;
						   $record = array();
						   $score = array();
						   $expertID = array();

						   for ($x = 0; $x < count($unique_expert); $x++) {
						   		$search_result = array();
						   		//$associativeArray ['record_id'] = $x+1;
						   		$search_result['Keyword'] = $word;
						   		$search_result['ExpertId'] = $unique_expert[$x];
						   		$search_result['Expert_name'] = $expert_name[$x];
								$search_result['No_citation'] = $no_citation[$x];
								$search_result['No_publication'] = $no_pubs[$x];
								$search_result['temp_score'] = round(($no_pubs[$x]*0.8 + $no_citation[$x]*0.2), 2);

								$Keyword = $search_result['Keyword'];
								$ExpertId = $search_result['ExpertId'];
								$Expert_name = $search_result['Expert_name'];
								$No_citation = $search_result['No_citation'];
								$No_publication = $search_result['No_publication'];
								$temp_score = $search_result['temp_score'];
								

								$this->db->query("INSERT INTO `TEMP_INFO` (`Keyword`,`expert_id`,`Expert_name`,`No_citation`,`No_publication`,`temp_score`) VALUES ('".$Keyword."','".$ExpertId."','".$Expert_name."','".$No_citation."','".$No_publication."','".$temp_score."')");

							}

							$total_pub = $this->db->query("SELECT SUM(No_publication) AS sum_of_pub FROM TEMP_INFO WHERE Keyword = '$word'");
							$total_cite = $this->db->query("SELECT SUM(No_citation) AS sum_of_cite FROM TEMP_INFO WHERE Keyword = '$word'");

							$max_score = $this->db->query("SELECT MAX(temp_score) AS max_score FROM TEMP_INFO WHERE Keyword = '$word'");
							$min_score = $this->db->query("SELECT MIN(temp_score) AS min_score FROM TEMP_INFO WHERE Keyword = '$word'");

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

							//echo($diff);
							//echo("/");
						   for ($x = 0; $x < count($unique_expert); $x++) {
						   		$search_result = array();
						   		//$associativeArray ['record_id'] = $x+1;
						   		$search_result['Keyword'] = $word;
						   		$search_result['ExpertId'] = $unique_expert[$x];
						   		$search_result['Expert_name'] = $expert_name[$x];
								$search_result['No_citation'] = $no_citation[$x];
								$search_result['No_publication'] = $no_pubs[$x];
								//$search_result['Score_citation'] = round(($no_citation[$x]*0.2)/$total_cite,2);
								//$search_result['Score_publication'] = round(($no_pubs[$x]*0.8)/$total_pub,2);
								$search_result['temp_score'] = round(($no_pubs[$x]*0.8 + $no_citation[$x]*0.2), 2);
								$sum_score = round(((round(($no_pubs[$x]*0.8 + $no_citation[$x]*0.2), 2)-(round($min_score,2)))/$diff),2);

								if ($min_score == $max_score) {
									$sum_score = 1;
								}
								$search_result['Sum_of_score'] = $sum_score;

								$Keyword = $search_result['Keyword'];
								$ExpertId = $search_result['ExpertId'];
								$Expert_name = $search_result['Expert_name'];
								$No_citation = $search_result['No_citation'];
								$No_publication = $search_result['No_publication'];
								$temp_score = $search_result['temp_score'];
								//$Score_citation = $search_result['Score_citation'];
								//$Score_publication = $search_result['Score_publication'];
								$Sum_of_score = $search_result['Sum_of_score'];
								
								for( $i=0; $i < count($search_result); $i++) {
									$arrays = implode("," , $search_result);
								}

								$this->db->query("INSERT INTO `SHORTLISTED_EXPERTS` (`Keyword`,`expert_id`,`Expert_name`,`No_citation`,`No_publication`,`Sum_of_score`,`temp_score`) VALUES ('".$Keyword."','".$ExpertId."','".$Expert_name."','".$No_citation."','".$No_publication."','".$Sum_of_score."','".$temp_score."')");

								//echo $arrays;
								//echo "<br/>";
								array_push($expertID, $ExpertId);
								array_push($score, $Sum_of_score);
								array_push($record, $arrays);
							}
							$search_result['phrases'] = $keywords;
							$search_result['record'] = $record;

							$associativeArray3 = array();
							$associativeArray3['Keyword'] = $word;
							$associativeArray3['Sum_of_score'] = $score;
							$associativeArray3['ExpertId'] = $expertID;

							

							$query7 = $this->db->query("SELECT expert_id FROM `SHORTLISTED_EXPERTS` WHERE Keyword = '$word' ORDER BY Sum_of_score DESC LIMIT 5");
							array_push($temp_id, $query7->result());
							//print_r($temp_id);
							$json_id = json_encode($temp_id);
							$array_id = json_decode($json_id, true);
							
							
							$author_keywords = array();
							foreach ($query7->result_array() as $expert_id){
								foreach ($expert_id as $id) {
									$temp_aut_keyword = array();

							   		//$id = $id['expert_id'];
							   		$get_keyword = $this->db->query("SELECT author_keywords, Expert_name FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias NATURAL JOIN SHORTLISTED_EXPERTS WHERE Keyword = '$word' AND expert_id = $id AND `match_success`= 'true' AND publication_id IN" .$in);

							   		array_push($temp_aut_keyword, $get_keyword->result());
									$json_keywords = json_encode($temp_aut_keyword);

									$array_keywords = json_decode($json_keywords, true);
									foreach ($array_keywords as $value) {
											array_push($author_keywords , $value);
									}
									//array_push($author_keyword_list, $author_keywords);
									//echo($id);
									//echo("/");
								}
							}	
							//print_r($author_keywords);
							$expert_profile = array();
							$array_info = array();
							$expPhoto = array();
							foreach($array_id as $expert_id){
								foreach($expert_id as $id){
									$exp_basic_info = $this->Expert_Profile_Model->get_exp_basic_info($id['expert_id']);
						        	foreach($exp_basic_info as $row){
							            $expert_profile['exp_id'] =  $row['expert_id'];
							            $expert_profile['exp_name'] =  $row['expert_name'];
										$expert_profile['expert_honorific'] =  $row['expert_honorific'];
							            $expert_profile['exp_photo'] =  $row['expert_photo'];
							            $expert_profile['exp_faculty'] = $row['school_name'];
							            $expert_profile['exp_uni'] = $row['university_name'];

							            $exp_id = $expert_profile['exp_id'];
							            $exp_name = $expert_profile['exp_name'];
							            $expert_honorific = $expert_profile['expert_honorific'];
							            $exp_photo =  $expert_profile['exp_photo'];
							            $exp_faculty = $expert_profile['exp_faculty'];
							            $exp_uni = $expert_profile['exp_uni'];

							            for( $i=0; $i < count($expert_profile); $i++) {
											$expert_info = implode("," , $expert_profile);
										}

										$this->db->query("INSERT INTO `TEMP_EXP` (`exp_id`,`exp_name`,`expert_honorific`,`exp_photo`,`exp_faculty`,`exp_uni`) VALUES ('".$exp_id."','".$exp_name."','".$expert_honorific."','".$exp_photo."','".$exp_faculty."','".$exp_uni."')");
							       		}

							       		$this->db->query("UPDATE `SHORTLISTED_EXPERTS` SET photo ='$exp_photo' WHERE expert_id = $exp_id");

						       		array_push($array_info, $expert_info);
						       		array_push($expPhoto, $exp_photo);
								}
							}
							$query6 = $this->db->query("SELECT * FROM `SHORTLISTED_EXPERTS` WHERE Keyword = '$word' ORDER BY Sum_of_score DESC LIMIT 5");
							array_push($temp_rank, $query6->result());
							$temp_info = json_encode($array_info);
							$temp_info = json_decode($temp_info, true);
							$search_result['expert_information'] = $temp_info;
							$search_result['expPhoto'] = $expPhoto;
						}
						else{
							$tokenizeWord = array();
							$search_result['keywords'] = RakePlus::create($word,'vendor\rake-php-plus-master\lang\en_US.php',1)->keywords();
							$search_result['keywords'] = str_replace("("," ",$search_result['keywords']);
            				$search_result['keywords'] = str_replace(")"," ",$search_result['keywords']);
            				$search_result['keywords'] = str_replace("/"," ",$search_result['keywords']);
            				$search_result['keywords'] = str_replace('"'," ",$search_result['keywords']);
            				$search_result['keywords'] = str_replace('{'," ",$search_result['keywords']);
            				$search_result['keywords'] = str_replace('}'," ",$search_result['keywords']);
            				$search_result['keywords'] = str_replace('#'," ",$search_result['keywords']);
            				$search_result['keywords'] = str_replace(array("\n", "\t", ' '), '', $search_result['keywords']);
							$words = $search_result['keywords'];
							$words = array_unique($words);
							foreach ($words as $word) {
								array_push($tokenizeWord,$word);
							}
							$tokenizeWord = array_unique($tokenizeWord);
							foreach ($tokenizeWord as $word){ 
								echo("Tokenized words : ");
			            		echo($word);
			            		//echo "--";
			            		echo "</br>";
			            		array_push($ext_keyword,$word);
			            		$search_result['Keyword'] = $word;
			            		$query1 = $this->db->query("SELECT publication_id FROM publication WHERE index_keywords LIKE '%$word%' OR author_keywords LIKE '%$word%' OR title LIKE '%$word%' OR abstract LIKE '%$word%'");
			            		if ($query1->num_rows() > 0 || $query1->num_rows() != 0)  //Ensure that there is at least one result 
								{
									$pub_id = array();
									foreach ($query1->result_array() as $row)  //Iterate through results
									{
									   array_push($pub_id, $row['publication_id']);
									      //echo $row['publication_id'];
									      //echo '/';
									}

									$in = '(' . implode(',', $pub_id) .')';
									$query2 = $this->db->query("SELECT alias_id FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias WHERE publication_id IN ".$in);

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
									/*foreach($unique_alias as $alias){
										echo $alias;
								   	    echo "/";
									}*/

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

									/*echo "<br/>";
									foreach($unique_expert as $experts){
										echo $experts;
										echo "/";
									}*/

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

								   //echo json_encode($expert_name);

								    $no_pubs =  array();
								    foreach ($unique_expert as $row)  //Iterate through results
								    {
								   	   $query3 = $this->db->query("SELECT COUNT(*) AS 'PUB' FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias WHERE `match_success`= 'true' AND expert_id = '$row' AND publication_id IN" .$in);
								   	   foreach ($query3->result_array() as $row)  //Iterate through results
										   {
										   	  array_push($no_pubs, $row['PUB']);  
										   }
								   }

								   /*echo "<br/>";
								   foreach($no_pubs as $pubs){
										echo $pubs;
										echo "/";
									}*/

									$no_citation =  array();
									foreach ($unique_expert as $row)  //Iterate through results
								   {
								   	   $temp_pub =  array();
								   	   $query4 = $this->db->query("SELECT publication_id FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias WHERE `match_success`= 'true' AND expert_id = $row AND publication_id IN" .$in);
								   	   foreach ($query4->result_array() as $row)  //Iterate through results
									   {
										   	array_push($temp_pub, $row['publication_id']);  
									   }
									   $in2 = '(' . implode(',', $temp_pub) .')';
									   //echo "<br/>";
									   //echo "pub_id" .$in2;
								   	   $query4 = $this->db->query("SELECT SUM(cited_by) AS 'NoCitation' FROM publication WHERE publication_id IN ".$in2);

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
								   
								   $record = array();
								   $score = array();
								   $expertID = array();

								   for ($x = 0; $x < count($unique_expert); $x++) {
							   		$search_result = array();
							   		//$associativeArray ['record_id'] = $x+1;
							   		$search_result['Keyword'] = $word;
							   		$search_result['ExpertId'] = $unique_expert[$x];
							   		$search_result['Expert_name'] = $expert_name[$x];
									$search_result['No_citation'] = $no_citation[$x];
									$search_result['No_publication'] = $no_pubs[$x];
									$search_result['temp_score'] = round(($no_pubs[$x]*0.8 + $no_citation[$x]*0.2), 2);

									$Keyword = $search_result['Keyword'];
									$ExpertId = $search_result['ExpertId'];
									$Expert_name = $search_result['Expert_name'];
									$No_citation = $search_result['No_citation'];
									$No_publication = $search_result['No_publication'];
									$temp_score = $search_result['temp_score'];
									

									$this->db->query("INSERT INTO `TEMP_INFO` (`Keyword`,`expert_id`,`Expert_name`,`No_citation`,`No_publication`,`temp_score`) VALUES ('".$Keyword."','".$ExpertId."','".$Expert_name."','".$No_citation."','".$No_publication."','".$temp_score."')");

									}

									$total_pub = $this->db->query("SELECT SUM(No_publication) AS sum_of_pub FROM TEMP_INFO WHERE Keyword = '$word'");
									$total_cite = $this->db->query("SELECT SUM(No_citation) AS sum_of_cite FROM TEMP_INFO WHERE Keyword = '$word'");

									$max_score = $this->db->query("SELECT MAX(temp_score) AS max_score FROM TEMP_INFO WHERE Keyword = '$word'");
									$min_score = $this->db->query("SELECT MIN(temp_score) AS min_score FROM TEMP_INFO WHERE Keyword = '$word'");

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

									//echo($diff);
									//echo("/");
								   for ($x = 0; $x < count($unique_expert); $x++) {
								   		$search_result = array();
								   		//$associativeArray ['record_id'] = $x+1;
								   		$search_result['Keyword'] = $word;
								   		$search_result['ExpertId'] = $unique_expert[$x];
								   		$search_result['Expert_name'] = $expert_name[$x];
										$search_result['No_citation'] = $no_citation[$x];
										$search_result['No_publication'] = $no_pubs[$x];
										//$search_result['Score_citation'] = round(($no_citation[$x]*0.2)/$total_cite,2);
										//$search_result['Score_publication'] = round(($no_pubs[$x]*0.8)/$total_pub,2);
										$search_result['temp_score'] = round(($no_pubs[$x]*0.8 + $no_citation[$x]*0.2), 2);
										$sum_score = round(((round(($no_pubs[$x]*0.8 + $no_citation[$x]*0.2), 2)-(round($min_score,2)))/$diff),2);

										if ($min_score == $max_score) {
											$sum_score = 1;
										}
										$search_result['Sum_of_score'] = $sum_score;

										$Keyword = $search_result['Keyword'];
										$ExpertId = $search_result['ExpertId'];
										$Expert_name = $search_result['Expert_name'];
										$No_citation = $search_result['No_citation'];
										$No_publication = $search_result['No_publication'];
										$temp_score = $search_result['temp_score'];
										//$Score_citation = $search_result['Score_citation'];
										//$Score_publication = $search_result['Score_publication'];
										$Sum_of_score = $search_result['Sum_of_score'];
										
										for( $i=0; $i < count($search_result); $i++) {
											$arrays = implode("," , $search_result);
										}

										$this->db->query("INSERT INTO `SHORTLISTED_EXPERTS` (`Keyword`,`expert_id`,`Expert_name`,`No_citation`,`No_publication`,`Sum_of_score`,`temp_score`) VALUES ('".$Keyword."','".$ExpertId."','".$Expert_name."','".$No_citation."','".$No_publication."','".$Sum_of_score."','".$temp_score."')");

										//echo $arrays;
										//echo "<br/>";
										array_push($expertID, $ExpertId);
										array_push($score, $Sum_of_score);
										array_push($record, $arrays);
									}
									$search_result['phrases'] = $words;
									$search_result['record'] = $record;

									$associativeArray3 = array();
									$associativeArray3['Keyword'] = $word;
									$associativeArray3['Sum_of_score'] = $score;
									$associativeArray3['ExpertId'] = $expertID;

									$query7 = $this->db->query("SELECT expert_id FROM `SHORTLISTED_EXPERTS` WHERE Keyword = '$word' ORDER BY Sum_of_score DESC LIMIT 5");
									array_push($temp_id, $query7->result());
									$json_id = json_encode($temp_id);
									$array_id = json_decode($json_id, true);
									//print_r($array_id);
									$author_keywords = array();
									foreach ($query7->result_array() as $expert_id){
										foreach ($expert_id as $id) {
											$temp_aut_keyword = array();

									   		//$id = $id['expert_id'];
									   		$get_keyword = $this->db->query("SELECT author_keywords, Expert_name FROM publication NATURAL JOIN alias_match_publication NATURAL JOIN expert_alias NATURAL JOIN expert_match_alias NATURAL JOIN SHORTLISTED_EXPERTS WHERE Keyword = '$word' AND expert_id = $id AND `match_success`= 'true' AND publication_id IN" .$in);

									   		array_push($temp_aut_keyword, $get_keyword->result());
											$json_keywords = json_encode($temp_aut_keyword);

											$array_keywords = json_decode($json_keywords, true);
											foreach ($array_keywords as $value) {
													array_push($author_keywords , $value);
											}
											//array_push($author_keyword_list, $author_keywords);
											//echo($id);
											//echo("/");
										}
									}	
										
											
									//print_r($author_keywords);
									//echo("<br/");
									//$json_temp1 = json_encode($temp_rank);
									//$array_rank1 = json_decode($json_temp, true);
									$expert_profile = array();
									$array_info = array();
									$expPhoto = array();
									foreach($array_id as $expert_id){
										foreach($expert_id as $id){
											$exp_basic_info = $this->Expert_Profile_Model->get_exp_basic_info($id['expert_id']);
											//echo($id['expert_id']."/");
								        	foreach($exp_basic_info as $row){
								        		//$expert_profile['keyword'] = $word;
									            $expert_profile['exp_id'] =  $row['expert_id'];
									            $expert_profile['exp_name'] =  $row['expert_name'];
												$expert_profile['expert_honorific'] =  $row['expert_honorific'];
									            $expert_profile['exp_photo'] =  $row['expert_photo'];
									            $expert_profile['exp_faculty'] = $row['school_name'];
									            $expert_profile['exp_uni'] = $row['university_name'];

									            $keyword = $word;
									            $exp_id = $expert_profile['exp_id'];
									            $exp_name = $expert_profile['exp_name'];
									            $expert_honorific = $expert_profile['expert_honorific'];
									            $exp_photo =  $expert_profile['exp_photo'];
									            $exp_faculty = $expert_profile['exp_faculty'];
									            $exp_uni = $expert_profile['exp_uni'];
									            //echo($exp_photo);
									            for( $i=0; $i < count($expert_profile); $i++) {
													$expert_info = implode("," , $expert_profile);
												}

												$this->db->query("INSERT INTO `TEMP_EXP` (`exp_id`,`exp_name`,`expert_honorific`,`exp_photo`,`exp_faculty`,`exp_uni`) VALUES ('".$exp_id."','".$exp_name."','".$expert_honorific."','".$exp_photo."','".$exp_faculty."','".$exp_uni."')");
									       		}

												$this->db->query("UPDATE `SHORTLISTED_EXPERTS` SET photo ='$exp_photo' WHERE expert_id = $exp_id");

								       		array_push($array_info, $expert_info);
								       		array_push($expPhoto, $exp_photo);
										}
									}
									$query6 = $this->db->query("SELECT * FROM `SHORTLISTED_EXPERTS` WHERE Keyword = '$word' ORDER BY Sum_of_score DESC LIMIT 5");
									array_push($temp_rank, $query6->result());
									$temp_info = json_encode($array_info);
									$temp_info = json_decode($temp_info, true);
									$search_result['expert_information'] = $temp_info;
									$search_result['expPhoto'] = $expPhoto;
								}
								else{
									$pub_id = '';
									$search_result['rank_list'] = array();
									//$search_result['no_result'] = "No result found";
								}	
			            	}
							//echo("no result");
							//$search_result['no_result'] = "No result found";
						}	
	            	}
            	
	        		$json_temp = json_encode($temp_rank);
					$array_rank = json_decode($json_temp, true);
					$search_result['rank_list'] = $array_rank;
					$search_result['search_query'] = $input_query;
					$search_result['num_input'] = $numkeywords;
					$search_result['ext_keyword'] = $ext_keyword;
					$search_result['ext_keyword2'] = $ext_keyword;
				}
            }
            
        	
        	$this->load->view('templates/header');
           	$this->load->view('search_result',$search_result); 
			$this->load->view('templates/footer');
        } 
    }
?>