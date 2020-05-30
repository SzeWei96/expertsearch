<?php
use Phpml\Dataset\CsvDataset;
use Phpml\Dataset\ArrayDataset;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WordTokenizer;
use Phpml\Tokenization\WhitespaceTokenizer;
use Phpml\CrossValidation\StratifiedRandomSplit;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\Metric\Accuracy;
use Phpml\Classification\SVC;
use Phpml\SupportVectorMachine\Kernel;
use Phpml\ModelManager;
use Phpml\Pipeline;
use Phpml\FeatureExtraction\StopWords;
use Phpml\FeatureExtraction\StopWords\English;
use Phpml\Classification\KNearestNeighbors;
use Phpml\Classification\NaiveBayes;
use Phpml\Math\Distance\Euclidean;
use Phpml\Math\Distance\Manhattan;
use Phpml\Math\Distance\Minkowski;
use Phpml\Math\Distance\Chebyshev;

class Classifier extends CI_Controller
{
	function __construct(){
        parent::__construct();
        if(empty($this->session->userdata('admin_id'))) {
            $this->session->set_flashdata('flash_data', 'You don\'t have access!');
            redirect('admin/login');
        }
    }
	
    public function classifier_management($page = 'classifier_management'){
        //session_destroy();
        $this->session->unset_userdata('refresh_bool');
        $this->session->unset_userdata('transformers');
        $this->session->unset_userdata('article_check_ids');
        $this->session->unset_userdata('recent_publication_ids');

        if (!file_exists(APPPATH . 'views/admin/the-classifier/classifier_management/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = 'Classifier Management';
        $data['classifiers'] = $this->Classifier_Model->get_classifiers(FALSE);

        $data['info']['table_name'] = 'classifier';
        $data['info']['data_id_name'] = 'class_id';

        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/sidebar', $data);
        $this->load->view('admin/the-classifier/classifier_management/' . $page, $data);
        $this->load->view('admin/template/footer', $data);
    }

    public function add_classifier($page = 'add_classifier'){
        date_default_timezone_set('Asia/Kuala_Lumpur');
        $data['title'] = 'Add Classifier';

        $class_name = $this->input->post('classifier-name');        
        $datetime_create = date("Y-m-d H:i:s");

        $this->form_validation->set_rules('classifier-name', $class_name, 'required|callback_check_classifier_name_exists');
        
        if($this->form_validation->run() === FALSE){
            $this->load->view('admin/template/header', $data);
            $this->load->view('admin/template/sidebar', $data);
            $this->load->view('admin/the-classifier/classifier_management/' . $page, $data);
            $this->load->view('admin/template/footer', $data);
        }else{            
            $this->Classifier_Model->add_classifier($class_name, $datetime_create, '0');

            $this->session->set_flashdata('success', 'Classifier ('.$class_name.') has been created successfully.');
            redirect('admin/the-classifier/classifier-management');
        }
    }

    public function update_classifier($id = NULL){
        $page = 'update_classifier';
        $data['title'] = 'View / Edit Classifier';
        $data['info']['table_name_publication'] = 'publication_predict';
        $data['info']['data_id_name_publication'] = 'publication_predict_id';
        $data['info']['table_name_expert'] = 'expert_predict';
        $data['info']['data_id_name_expert'] = 'expert_predict_id';

        $data['classifier'] = $this->Classifier_Model->get_classifiers($id);
        $table_name = 'classifier_'.$data['classifier']['class_id'];
        
        if(isset($data['classifier']['nof_category'])){
            $data['categories'] = $this->Classifier_Model->get_category(FALSE, $table_name);
        }

        switch ($data['classifier']['supervised_class_type']) {
            case "svc":
                $data['parameters'] = $this->Classifier_Model->get_svc($data['classifier']['supervised_class_id']);

                if($data['parameters']['svc_prob_est'] == 'true') {
                    $data['svc_prob_bool'] = TRUE;

                } else {
                    $data['svc_prob_bool'] = FALSE;
                }
                break;
            case "knn":
                $data['parameters'] = $this->Classifier_Model->get_knn($data['classifier']['supervised_class_id']);
                $data['svc_prob_bool'] = FALSE;
                break;
            case "nb":
                $data['parameters'] = $this->Classifier_Model->get_nb($data['classifier']['supervised_class_id']);
                $data['svc_prob_bool'] = FALSE;
                break;
        }

        $data['publications'] = $this->Classifier_Model->get_publication_predict_specific('class_id', $id);
        $data['experts'] = $this->Classifier_Model->get_expert_predict_specific('class_id', $id);

        if(!empty($data['publications'])) {
            foreach($data['publications'] as $publication) {
                $publication_categories[] = $this->Classifier_Model->get_publication_predict_cat($publication['publication_predict_id']);
            }

            for ($i = 0; $i < count($publication_categories); $i++) {
                foreach($publication_categories[$i] as $y) { 
                    $distinct_publication_categories[$i][$y['publication_cat']] = $y['publication_cat_prob']; 
                }
                array_shift($publication_categories[$i]);
            }
            
            $data['publications'] = array_map(function ($arr, $distinct_publication_categories) {
                $arr['categories'] = $distinct_publication_categories;
                return $arr;
                
            }, $data['publications'], $distinct_publication_categories);

            foreach ($data['publications'] as &$publication) {
                arsort($publication['categories']);
            }
        }

        if(!empty($data['experts'])) {
            foreach($data['experts'] as $expert) {
                $expert_categories[] = $this->Classifier_Model->get_expert_predict_cat($expert['expert_predict_id']);
            }

            for ($i = 0; $i < count($expert_categories); $i++) {
                foreach($expert_categories[$i] as $y) { 
                    $distinct_expert_categories[$i][$y['expert_cat']] = $y['expert_cat_prob']; 
                }
                array_shift($expert_categories[$i]);
            }

            $data['experts'] = array_map(function ($arr, $distinct_expert_categories) {
                $arr['categories'] = $distinct_expert_categories;
                return $arr;
                
            }, $data['experts'], $distinct_expert_categories);

            foreach ($data['experts'] as &$expert) {
                arsort($expert['categories']);
            }
        }

        if($id === NULL) {
            $class_name = $this->input->post('classifier-name');
            $display_name = $this->input->post('display-name');
            $id = $this->input->post('classifier-id');
            $data['classifier'] = $this->Classifier_Model->get_classifiers($id);

            if($data['classifier']['class_name'] == $class_name) {
                $this->Classifier_Model->update_classifier($id, 'display_name', $display_name);

                $this->session->set_flashdata('success', 'Classifier ('.$class_name.') has been updated successfully.');
                redirect('admin/the-classifier/classifier-management/update-classifier/'.$id);
            } else {
                $this->form_validation->set_rules('classifier-name', $class_name, 'required|callback_check_classifier_name_exists');
            
                if($this->form_validation->run() === TRUE){          
                    $this->Classifier_Model->update_classifier($id, 'class_name', $class_name);
                    $this->Classifier_Model->update_classifier($id, 'display_name', $display_name);

                    $this->session->set_flashdata('success', 'Classifier ('.$class_name.') has been updated successfully.');
                    redirect('admin/the-classifier/classifier-management/update-classifier/'.$id);
                }
            }
        }

        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/sidebar', $data);
        $this->load->view('admin/the-classifier/classifier_management/'. $page, $data);
        $this->load->view('admin/template/footer', $data);
    }

    public function update_tested_publications_cm($id = NULL){
        $data['title'] = 'Update Tested Publications';
        $page = 'update_tested_publications_cm';

        $data['publication_predict'] = $this->Classifier_Model->get_publication_predict($id);
        $data['publication'] = $this->Classifier_Model->get_publications($data['publication_predict']['publication_id']);
        $data['classifiers'] = $this->Classifier_Model->get_classifiers($data['publication_predict']['class_id']);
        
        if ($id != NULL) {
            $publication_predict_all_categories = $this->Classifier_Model->get_publication_predict_cat($id);

            $data['publication_predict']['categories'] = $publication_predict_all_categories;

            $table_name = 'classifier_'.$data['publication_predict']['class_id'];

            $categories = $this->Classifier_Model->get_category(FALSE, $table_name);
            foreach($categories as $category) {
                $data['categories'][] = $category['cat_name'];
            }
        }

        if ($id === NULL) {
            $publication_predict_id = $this->input->post('publication-predict-id');
            $publication_cats = $this->input->post('publication-predict-cat');
            $publication_cat_default = $this->input->post('publication-predict-cat-default');
            $publication_cat_prob = $this->input->post('publication-predict-cat-prob');

            for ($x = 0; $x < count($publication_cats); $x++) {
                $publication_predicts[$x] = array("publication_predict_cat" => $publication_cats[$x], 
                "publication_cat_default" => $publication_cat_default[$x], 
                "publication_cat_prob" => $publication_cat_prob[$x]/100);
            } 

            foreach ($publication_predicts as $publication_predict) {
                $this->Classifier_Model->update_publication_predict_cat($publication_predict_id, 
                $publication_predict['publication_predict_cat'], 
                $publication_predict['publication_cat_default'], 
                $publication_predict['publication_cat_prob']);
            }

            $publication_predict = $this->Classifier_Model->get_publication_predict($publication_predict_id);
            $publication = $this->Classifier_Model->get_publications($publication_predict['publication_id']);
            $id = $publication_predict['class_id'];
            $table_name = 'classifier_'.$id;
            $all_categories = $this->Classifier_Model->get_category(FALSE, $table_name);
            $publication['experts'] = $this->Classifier_Model->get_expert_alias_publication($publication['publication_id'], FALSE);
            $specific_expert_id = 0;

            if (!empty($publication['experts'])) {
                foreach ($publication['experts'] as $experts) {
                    $expert_alias_publication_ids[] = $experts['expert_id'];
                }

                $expert_alias_publication_ids = array_values(array_unique($expert_alias_publication_ids));

                foreach ($expert_alias_publication_ids as $expert_alias_publication_id) {
                    $expert_publications[] = $this->Classifier_Model->get_expert_alias_publication(FALSE, $expert_alias_publication_id);
                }

                foreach ($expert_publications as &$i) { 
                    $num_publications = 0;
                    $sum_categories = array();
                    $num_highest_publications = array();
                    foreach ($i as &$expert_publication) {
                        $expert_publication['publications'] = $this->Classifier_Model->get_publication_predict_multiple($id, $expert_publication['publication_id']);
                        $specific_expert_id = $expert_publication['expert_id'];

                        if ($expert_publication['publications'] != NULL) {
                            $categories = $this->Classifier_Model->get_publication_predict_cat($expert_publication['publications']['publication_predict_id']);

                            $distinct_categories = array();
                            foreach($categories as $y) { 
                                $distinct_categories[$y['publication_cat']] = $y['publication_cat_prob']; 

                                if(!isset($sum_categories[$y['publication_cat']])){
                                    $sum_categories[$y['publication_cat']] = 0;
                                }
                                $sum_categories[$y['publication_cat']] += $y['publication_cat_prob'];
                            }

                            $expert_publication['publications']['categories'] = $distinct_categories;
                            $num_publications++;

                            $highest_publication = array_keys($distinct_categories, max($distinct_categories));
                            $num_highest_publications[] = $highest_publication[0];
                        }
                    }
                    $num_highest_publications = array_count_values($num_highest_publications);

                    foreach ($all_categories as $all_category) {
                        if(!array_key_exists($all_category['cat_name'], $sum_categories)){
                            $sum_categories[$all_category['cat_name']] = 0;
                        }
                    }
                    
                    if($num_publications != 0) {
                        foreach ($sum_categories as $category => $probabilty) {
                            $sum_categories[$category] = $probabilty/$num_publications;
                        }
                    } else {
                        foreach ($sum_categories as $category => $probabilty) {
                            $sum_categories[$category] = 0.00;
                        }
                    }

                    $expert_predict = $this->Classifier_Model->get_expert_predict_multiple($id, $specific_expert_id);
                    $this->Classifier_Model->delete('expert_predict_id', $expert_predict['expert_predict_id'], 'expert_predict');
                    $this->Classifier_Model->delete('expert_predict_id', $expert_predict['expert_predict_id'], 'expert_predict_cat');

                    $recent_expert_id = $this->Classifier_Model->add_expert_predict($id, $specific_expert_id, '0');

                    foreach ($sum_categories as $category => $probabilty) {
                        if(array_key_exists($category, $num_highest_publications)){
                            $this->Classifier_Model->add_expert_predict_cat($recent_expert_id, $category, $probabilty, $num_highest_publications[$category]);
                        } else {
                            $this->Classifier_Model->add_expert_predict_cat($recent_expert_id, $category, $probabilty, '0');
                        }   
                    }
                }
            }

            $this->session->set_flashdata('success', 'Publication information updated successfully.');
            redirect('admin/the-classifier/classifier-management/update-classifier/update-tested-publications/'.$publication_predict_id);
        }

        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/sidebar', $data);
        $this->load->view('admin/the-classifier/classifier_management/' . $page, $data);
        $this->load->view('admin/template/footer', $data);
    }

    public function update_tested_experts_cm($id = NULL){
        $data['title'] = 'Update Tested Experts';
        $page = 'update_tested_experts_cm';

        $data['expert_predict'] = $this->Classifier_Model->get_expert_predict($id);
        $data['experts'] = $this->Classifier_Model->get_experts($data['expert_predict']['class_id']);
        $data['classifiers'] = $this->Classifier_Model->get_classifiers($data['expert_predict']['class_id']);
        
        if ($id != NULL) {
            $expert_predict_all_categories = $this->Classifier_Model->get_expert_predict_cat($id);

            $data['expert_predict']['categories'] = $expert_predict_all_categories;

            $table_name = 'classifier_'.$data['expert_predict']['class_id'];

            $categories = $this->Classifier_Model->get_category(FALSE, $table_name);
            foreach($categories as $category) {
                $data['categories'][] = $category['cat_name'];
            }
        }

        if ($id === NULL) {
            $expert_predict_id = $this->input->post('expert-predict-id');
            $expert_cats = $this->input->post('expert-predict-cat');
            $expert_cat_default = $this->input->post('expert-predict-cat-default');
            $expert_cat_prob = $this->input->post('expert-predict-cat-prob');

            for ($x = 0; $x < count($expert_cats); $x++) {
                $expert_predicts[$x] = array("expert_predict_cat" => $expert_cats[$x], 
                "expert_cat_default" => $expert_cat_default[$x], 
                "expert_cat_prob" => $expert_cat_prob[$x]/100);
            } 

            foreach ($expert_predicts as $expert_predict) {
                $this->Classifier_Model->update_expert_predict_cat($expert_predict_id, 
                $expert_predict['expert_predict_cat'], 
                $expert_predict['expert_cat_default'], 
                $expert_predict['expert_cat_prob']);
            }            

            $this->session->set_flashdata('success', 'Expert information updated successfully.');
            redirect('admin/the-classifier/classifier-management/update-classifier/update-tested-experts/'.$expert_predict_id);
        }

        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/sidebar', $data);
        $this->load->view('admin/the-classifier/classifier_management/' . $page, $data);
        $this->load->view('admin/template/footer', $data);
    }

    public function train_classifier($page = 'train_classifier'){
        //session_destroy();
        $this->session->unset_userdata('refresh_bool');
        $this->session->unset_userdata('transformers');
        $this->session->unset_userdata('article_check_ids');
        $this->session->unset_userdata('recent_publication_ids');

        if (!file_exists(APPPATH . 'views/admin/the-classifier/train_classifier/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = 'Classifier Training';

        $data['classifiers'] = $this->Classifier_Model->get_classifiers(FALSE);
        
        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/sidebar', $data);
        $this->load->view('admin/the-classifier/train_classifier/' . $page, $data);
        $this->load->view('admin/template/footer', $data);
    }

    public function training_data($id = NULL){
        $page = 'training_data';
        $data['title'] = 'Training Data';

        if ($this->session->userdata('refresh_bool') != null) {
            if ($this->session->userdata('refresh_bool')) {
                $this->session->unset_userdata('refresh_bool');
                redirect('admin/the-classifier/classifier-training/training-data/'.$id, 'refresh');
            }
        }        

        $data['train_data_inputs'] = $this->Classifier_Model->get_train_data_inputs();
        $data['classifiers'] = $this->Classifier_Model->get_classifiers($id);

        $table_name = 'classifier_'.$data['classifiers']['class_id'];        
        $data['keywords'] = $this->Classifier_Model->get_keyword(FALSE, $table_name);
        
        $data['info']['table_name'] = $table_name;
        $data['info']['data_id_name_keyword'] = 'key_id';
        $data['info']['data_id_name_category'] = 'cat_id';

        if($id === NULL) {
            $id = $this->input->post('classifier-id');
            $data['classifiers'] = $this->Classifier_Model->get_classifiers($id);
            $table_name = 'classifier_'.$data['classifiers']['class_id'];
            $train_data_select_value = $this->input->post('train-data-input-select');
            
            if($this->input->post('submit') == "folder") {
                $category_texts = $this->train_data_input_folder('train-data-input-file', 'train-data-input-folder');
            } else {
                $category_texts = $this->train_data_input_excel('train-data-input-excel');
            }
            
            if ($category_texts === FALSE) {
                $this->session->set_flashdata('danger', 'No file is read');
            } else {
                $this->Classifier_Model->update_classifier($id, 'input_id', $train_data_select_value);

                $this->Classifier_Model->create_table_category($table_name);
                $this->Classifier_Model->create_table_keyword($table_name);

                $this->Classifier_Model->truncate('category_'.$table_name);
                $this->Classifier_Model->truncate('keyword_'.$table_name);

                foreach ($category_texts as $category_text) {
                    $keywords[] = $category_text["text"];
                    $categories[] = $category_text["category"];
                }
                
                $categories = array_unique($categories);

                $this->Classifier_Model->update_classifier($id, 'nof_category', count($categories));
                $this->Classifier_Model->update_classifier($id, 'nof_keyword', count($keywords));

                foreach ($categories as $category) {
                    $this->Classifier_Model->add_category($category, $table_name);
                }
                
                $categories = $this->Classifier_Model->get_category(FALSE, $table_name);
                
                foreach ($category_texts as $category_text) {
                    $keywords[] = $category_text["text"];

                    for ($x = 0; $x < count($categories); $x++) {
                        if ($categories[$x]['cat_name'] == $category_text["category"]){
                            $this->Classifier_Model->add_keyword($category_text["text"], $table_name, $categories[$x]['cat_id'], $category_text["text"]);
                        }
                    }                    
                }
                $this->session->set_flashdata('success', 'Training data has been uploaded successfully.');
            }
            redirect('admin/the-classifier/classifier-training/training-data/'.$id, 'refresh');
        }

        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/sidebar', $data);
        $this->load->view('admin/the-classifier/train_classifier/' . $page, $data);
        $this->load->view('admin/template/footer', $data);
    }

    public function train_text_preprocess($id = NULL){
        $data['title'] = 'Train Text Preprocessing';
        $page = 'train_text_preprocess';

        $data['classifiers'] = $this->Classifier_Model->get_classifiers($id);

        if (!empty($data['classifiers']['class_id'])) {
            $table_name = 'classifier_'.$data['classifiers']['class_id'];
            $data['tokens'] = $this->Classifier_Model->get_token(FALSE, $table_name);

            $data['info']['table_name'] = $table_name;
            $data['info']['data_id_name_token'] = 'token_id';
            
            $default_stopword = new English();
            $default_stopword = $default_stopword->get_stopwords();
            
            $this->Classifier_Model->create_table_stopword($table_name);

            $stopword = $this->Classifier_Model->get_stopword(FALSE, $table_name);

            if (empty($stopword)) {
                foreach($default_stopword as $key => $value) {
                    $this->Classifier_Model->add_stopword($key, $table_name);
                }
            }

            $default_keywords = $this->Classifier_Model->get_keyword(FALSE, $table_name);
            
            if($data['tokens'] != FALSE) {                
                if ($data['classifiers']['nof_stopword'] != 0) {
                    $stopwords = $this->Classifier_Model->get_stopword(FALSE, $table_name);

                    foreach($stopwords as $stopword) {
                        $classifier_stopword[] = $stopword['stopword_txt'];
                    }

                    $phpml_stopword = new StopWords($classifier_stopword);

                    $transformers = [
                        new TokenCountVectorizer(new WordTokenizer(), $phpml_stopword, $data['classifiers']['token_freq_threshold']),
                        new TfIdfTransformer(),
                    ];
                } else {
                    $transformers = [
                        new TokenCountVectorizer(new WordTokenizer(), null, $data['classifiers']['token_freq_threshold']),
                        new TfIdfTransformer(),
                    ];
                }

                $this->session->set_userdata('transformers', $transformers); 
            }
        }

        if($id === NULL) {
            $id = $this->input->post('classifier-id');
            $data['classifiers'] = $this->Classifier_Model->get_classifiers($id);
            $table_name = 'classifier_'.$data['classifiers']['class_id'];
            $number_removal = $this->input->post('number-removal-checkbox');
            $keywords = $this->Classifier_Model->get_keyword(FALSE, $table_name);

            if(isset($number_removal)) {
                foreach ($keywords as $key => $sample) { 
                    $keywords[$key]['preprocesskey_txt'] = $this->number_removal($sample['key_txt']);
                    $this->Classifier_Model->update_keyword_preprocessed('key_id', $sample['key_id'], 'keyword_'.$table_name, $this->number_removal($sample['key_txt']));
                }
            } else {
                foreach ($keywords as $key => $sample) { 
                    $keywords[$key]['preprocesskey_txt'] = $sample['key_txt'];
                    $this->Classifier_Model->update_keyword_preprocessed('key_id', $sample['key_id'], 'keyword_'.$table_name, $sample['key_txt']);
                }
            }
            
            foreach($keywords as $keyword) {
                $samples[] = $keyword['preprocesskey_txt'];
                $categories[] = $keyword['cat_name'];
            }

            $stopwords = $this->Classifier_Model->get_stopword(FALSE, $table_name);
            foreach($stopwords as $stopword) {
                $classifier_stopword[] = $stopword['stopword_txt'];
            }

            $phpml_stopword = new StopWords($classifier_stopword);
            
            $train_text_preprocess_select_value = $this->input->post('train-text-preprocess-select');
            $threshold_value = (float) $this->input->post('threshold-value'); //not include
            
            $this->Classifier_Model->update_classifier($id, 'token_freq_threshold', $threshold_value);
            
            if ($train_text_preprocess_select_value == "stopword") {
                $transformers = [
                    new TokenCountVectorizer(new WordTokenizer(), $phpml_stopword, $threshold_value),
                    new TfIdfTransformer(),
                ];
                $vectorizer = new TokenCountVectorizer(new WordTokenizer(), $phpml_stopword, $threshold_value);

                $this->Classifier_Model->update_classifier($id, 'nof_stopword', count($classifier_stopword));
            } else {
                $transformers = [
                    new TokenCountVectorizer(new WordTokenizer(), null, $threshold_value),
                    new TfIdfTransformer(),
                ];
                $vectorizer = new TokenCountVectorizer(new WordTokenizer(), null, $threshold_value);
                $this->Classifier_Model->update_classifier($id, 'nof_stopword', '0');
            }

            $this->session->set_userdata('transformers', $transformers); 
            
            $vectorizer->fit($samples, $categories);
            $vectorizer->transform($samples);
            
            $tokens = $vectorizer->getVocabularyFrequency();
            
            $this->Classifier_Model->create_table_token($table_name);
            $this->Classifier_Model->truncate('token_'.$table_name);

            foreach($tokens as $token => $frequency) {
                $this->Classifier_Model->add_token($token, $frequency, $table_name);
            }
            $this->session->set_flashdata('success', 'Train data successfully.');
            redirect('admin/the-classifier/classifier-training/train-text-preprocess/'.$id);
        }

        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/sidebar', $data);
        $this->load->view('admin/the-classifier/train_classifier/' . $page, $data);
        $this->load->view('admin/template/footer', $data);
    }

    public function edit_stopword($id = NULL){
        $data['title'] = 'Stopword List';
        $page = 'edit_stopword';

        if ($this->session->userdata('refresh_bool')) {
            $this->session->unset_userdata('refresh_bool');
            redirect('admin/the-classifier/classifier-training/edit-stopword/'.$id, 'refresh');
        }

        $data['classifiers'] = $this->Classifier_Model->get_classifiers($id);
        
        if (!empty($data['classifiers']['class_id'])) {
            $table_name = 'classifier_'.$data['classifiers']['class_id'];
            $data['info']['table_name'] = $table_name;
            $data['info']['data_id_name_stopword'] = 'stopword_id';
            $data['stopwords'] = $this->Classifier_Model->get_stopword(FALSE, $table_name);
        } 

        if($id === NULL) {
            $id = $this->input->post('classifier-id');
            $data['classifiers'] = $this->Classifier_Model->get_classifiers($id);
            $table_name = 'classifier_'.$data['classifiers']['class_id'];

            if($this->input->post('submit') == "upload") { 
                $stopwords = $this->train_input_stopword('train-input-stopword');
            
                $this->Classifier_Model->truncate('stopword_'.$table_name);
                
                if (!empty($stopwords)) {
                    foreach($stopwords as $stopword) {
                        $this->Classifier_Model->add_stopword($stopword, $table_name);
                    }
                }

                $this->Classifier_Model->update_classifier($id, 'nof_stopword', count($stopwords));
            } else {
                $this->Classifier_Model->truncate('stopword_'.$table_name);

                $default_stopword = new English();
                $default_stopword = $default_stopword->get_stopwords();

                foreach($default_stopword as $key => $value) {
                    $this->Classifier_Model->add_stopword($key, $table_name);
                }

                $this->Classifier_Model->update_classifier($id, 'nof_stopword', count($default_stopword));
            }
            $this->session->set_userdata('refresh_bool', true);
            redirect('admin/the-classifier/classifier-training/edit-stopword/'.$id);
        }

        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/sidebar', $data);
        $this->load->view('admin/the-classifier/train_classifier/' . $page, $data);
        $this->load->view('admin/template/footer', $data);
    }

    public function training_algorithm($id = NULL){
        $data['title'] = 'Training Algortihm';
        $page = 'training_algorithm';

        $data['classifiers'] = $this->Classifier_Model->get_classifiers($id);
        $table_name = 'classifier_'.$data['classifiers']['class_id'];
        $data['info']['table_name'] = $table_name;

        if (isset($data['classifiers']['supervised_class_type'])) {
            switch ($data['classifiers']['supervised_class_type']) {
                case "svc":
                    $data['parameters'] = $this->Classifier_Model->get_svc($data['classifiers']['supervised_class_id']);
                    break;
                case "knn":
                    $data['parameters'] = $this->Classifier_Model->get_knn($data['classifiers']['supervised_class_id']);
                    break;
                case "nb":
                    $data['parameters'] = $this->Classifier_Model->get_nb($data['classifiers']['supervised_class_id']);
                    break;
            }
        }
        
        if($id === NULL) {
            $id = $this->input->post('classifier-id');
            $data['classifiers'] = $this->Classifier_Model->get_classifiers($id);
            $table_name = 'classifier_'.$data['classifiers']['class_id']; 

            $keywords = $this->Classifier_Model->get_keyword(FALSE, $table_name);
            foreach($keywords as $keyword) {
                $samples[] = $keyword['preprocesskey_txt'];
                //$samples[] = preg_replace('/\d+/u', '', $keyword['key_txt']);
                $categories[] = $keyword['cat_name'];
            }

            $train_algo_select_value = $this->input->post('train-algo-select');

            $this->Classifier_Model->delete('class_id', $id, 'supervised_class');
            $this->Classifier_Model->delete('supervised_class_id', $data['classifiers']['supervised_class_id'], 'svc');
            $this->Classifier_Model->delete('supervised_class_id', $data['classifiers']['supervised_class_id'], 'knn');
            $this->Classifier_Model->delete('supervised_class_id', $data['classifiers']['supervised_class_id'], 'nb');

            $this->Classifier_Model->add_supervised_class($train_algo_select_value, $id, '0');
            $supervised_class = $this->Classifier_Model->get_supervised_class($id);

            if ($train_algo_select_value == "svc") {
                $classifier = new SVC(
                    $kernel = (int) $this->input->post('svc-kernal-type'),  // $kernel
                    $cost = (float) $this->input->post('svc-cost-value'),  // $cost
                    $degree = (int) $this->input->post('svc-degree-kernel'),  // $degree
                    $gamma = (float) $this->input->post('svc-kernal-coef'),  // $gamma
                    $coef = (float) $this->input->post('svc-coef'),  // $coef0
                    $tolerance = (float) $this->input->post('svc-tolerance'),  // $tolerance
                    $cachesize = (int) $this->input->post('svc-cache-ms'),  // $cacheSize
                    $shrinking = $this->input->post('svc-shrinking'),  // $shrinking
                    $prob_est = $this->input->post('svc-prob-est')   // $probabilityEstimates, set to true
                );

                $this->Classifier_Model->add_svc($supervised_class['supervised_class_id'], $kernel, $cost, $degree, $gamma, $coef, $tolerance, $cachesize, $shrinking, $prob_est);
            } else if ($train_algo_select_value == "knn") {
                switch ($this->input->post('knn-dist-metric')) {
                    case "euclidean":
                        $classifier = new KNearestNeighbors((int) $this->input->post('knn-k'), new Euclidean());
                        break;
                    case "manhattan":
                        $classifier = new KNearestNeighbors((int) $this->input->post('knn-k'), new Manhattan());
                        break;
                    case "chebyshev":
                        $classifier = new KNearestNeighbors((int) $this->input->post('knn-k'), new Chebyshev());
                        break;
                    case "minkowski":
                        $classifier = new KNearestNeighbors((int) $this->input->post('knn-k'), new Minkowski((int) $this->input->post('knn-lambda')));
                        break;
                    default:
                        $classifier = new KNearestNeighbors((int) $this->input->post('knn-k'), new Euclidean());
                }

                $this->Classifier_Model->add_knn(
                    $supervised_class['supervised_class_id'], 
                    (int) $this->input->post('knn-k'), 
                    $this->input->post('knn-dist-metric'), 
                    (int) $this->input->post('knn-lambda'));
            } else {
                $classifier = new NaiveBayes();
                $this->Classifier_Model->add_nb($supervised_class['supervised_class_id']);
            }

            $transformers = $this->session->userdata('transformers'); 
            $pipeline = new Pipeline($transformers, $classifier);

            $time_start = microtime(true);

            $pipeline->train($samples, $categories);

            $time_end = microtime(true);
            $time = $time_end - $time_start;

            $this->Classifier_Model->update_supervised_class($supervised_class['supervised_class_id'], 'train_duration', $this->format_period($time));

            $modelManager = new ModelManager();
            $serialized_classifier = 'application\classifier_serialized_data\serialized_'.$table_name.'.txt';
            $modelManager->saveToFile($pipeline, $serialized_classifier);
            
            $this->Classifier_Model->delete('class_id', $id, 'serialized_data');
            $this->Classifier_Model->add_serialized_data($serialized_classifier, $id);
            
            $this->session->set_flashdata('success', 'Classifier trained successfully.');
            redirect('admin/the-classifier/classifier-training/trained-report/'.$id);
        }

        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/sidebar', $data);
        $this->load->view('admin/the-classifier/train_classifier/' . $page, $data);
        $this->load->view('admin/template/footer', $data);
    }

    public function train_report($id = NULL){
        $page = 'train_report';
        $data['title'] = 'Trained Report';

        $data['classifiers'] = $this->Classifier_Model->get_classifiers($id);
        $table_name = 'classifier_'.$data['classifiers']['class_id'];

        $data['categories'] = $this->Classifier_Model->get_category(FALSE, $table_name);

        $supervised_class = $this->Classifier_Model->get_supervised_class($id);
        $data['supervised_class'] = $supervised_class;

        switch ($supervised_class['supervised_class_type']) {
            case "svc":
                $data['parameters'] = $this->Classifier_Model->get_svc($supervised_class['supervised_class_id']);
                break;
            case "knn":
                $data['parameters'] = $this->Classifier_Model->get_knn($supervised_class['supervised_class_id']);
                break;
            case "nb":
                $data['parameters'] = $this->Classifier_Model->get_nb($supervised_class['supervised_class_id']);
                break;
        }

        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/sidebar', $data);
        $this->load->view('admin/the-classifier/train_classifier/' . $page, $data);
        $this->load->view('admin/template/footer', $data);
    }

    public function test_classifier($id = NULL){
        $page = 'test_classifier';
        $data['title'] = 'Expert Classification';
        $data['expert_id'] = $id;

        if (!file_exists(APPPATH . 'views/admin/the-classifier/test_classifier/' . $page . '.php')) {
            show_404();
        }

        $data['experts'] = $this->Classifier_Model->get_distinct_expert_match_alias(FALSE);
        $data['publications'] = $this->Classifier_Model->get_expert_alias_publication(FALSE, $id);

        for ($y = 0; $y < count($data['publications']); $y++) {
            $data['publications'][$y]['checked'] = 'false';
        }

        foreach ($data['publications'] as &$publication) {
            $publication['classified'] = $this->Classifier_Model->get_publication_predict_publication_id($publication['publication_id']);

            foreach($publication['classified'] as &$classified) {
                $classified = $this->Classifier_Model->get_classifiers($classified['class_id']);
            }
        }

        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/sidebar', $data);
        $this->load->view('admin/the-classifier/test_classifier/' . $page, $data);
        $this->load->view('admin/template/footer', $data);
    }

    public function classifier_selection(){
        $page = 'classifier_selection';
        $data['title'] = 'Classifier Selection';

        $classifiers = $this->Classifier_Model->get_classifiers(FALSE);
        $serialized_datas = $this->Classifier_Model->get_serialized_data(FALSE);
        
        foreach ($serialized_datas as $serialized_data) {
            $data['classifiers'][] = $this->Classifier_Model->get_classifiers($serialized_data['class_id']);
        }

        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/sidebar', $data);
        $this->load->view('admin/the-classifier/test_classifier/' . $page, $data);
        $this->load->view('admin/template/footer', $data);
    }

    public function test_text_preprocess($id = NULL){
        $data['title'] = 'Test Text Preprocessing';
        $page = 'test_text_preprocess';

        $article_check_ids = $this->session->userdata('article_check_ids');
        $data['feature_selections'] = $this->Classifier_Model->get_test_feature_selection(FALSE);
        $data['classifiers'] = $this->Classifier_Model->get_classifiers($id); 

        if ($article_check_ids === null) {
            $this->session->set_flashdata('danger', 'Something wrong with the chosen publications. Please back to "Expert Classification" main page.');
        }

        if($id === NULL) {
            $id = $this->input->post('classifier-id');
            $feature_selects = $this->input->post('feature-select');
            $data['classifiers'] = $this->Classifier_Model->get_classifiers($id);
            $testing_samples = null;
            $table_name = 'classifier_'.$data['classifiers']['class_id'];

            if ($feature_selects === null) {
                $this->session->set_flashdata('danger', 'No Feature Selection Checked.');
            } else {
                $this->Classifier_Model->delete('class_id', $id, 'sel_test_feature');

                foreach ($feature_selects as $key => $feature_select) {
                    $this->Classifier_Model->add_sel_test_feature($id, $feature_select);

                    switch ($feature_select) {
                        case "1":
                            $feature_selects[$key] = 'title'; 
                            break;
                        case "2":
                            $feature_selects[$key] = 'abstract';
                            break;
                        case "3":
                            $feature_selects[$key] = 'index_keywords';
                            break;
                    }
                }
                
                foreach ($article_check_ids as $article_check_id) {
                    $publications[] = $this->Classifier_Model->get_publications($article_check_id);
                }
                
                foreach ($publications as $key => $publication) {
                    $publications[$key] = array(
                        'publication_id' => $publication['publication_id']
                    );
                    foreach ($feature_selects as $feature_select) {
                        $publications[$key][$feature_select] = $publication[$feature_select];
                        $testing_samples .= $publication[$feature_select]." ";
                    }
                    $publications[$key]['testing_samples'] = $testing_samples;
                    $testing_samples = null;
                }
                
                foreach ($publications as $publication) {
                    $samples[] = $publication['testing_samples'];

                    $publication_predict = $this->Classifier_Model->get_publication_predict_multiple($id, $publication['publication_id']);
                    $this->Classifier_Model->delete('publication_predict_id', $publication_predict['publication_predict_id'], 'publication_predict');
                    $this->Classifier_Model->delete('publication_predict_id', $publication_predict['publication_predict_id'], 'publication_predict_cat');
                }

                $modelManager = new ModelManager();
                $restoredClassifier = $this->Classifier_Model->get_serialized_data($id);      
                $classifier = $modelManager->restoreFromFile($restoredClassifier['serialized_data_string']);

                if ($data['classifiers']['supervised_class_type'] == 'svc') {
                    $svc_data = $this->Classifier_Model->get_svc($data['classifiers']['supervised_class_id']);
                    
                    if ($svc_data['svc_prob_est'] == 'true') {
                        $predictedLabels = $classifier->predictProbability($samples);
                        
                        $publications = array_map(function ($arr, $predictedLabels) {
                            $arr['categories'] = $predictedLabels;
                            return $arr;
                            
                        }, $publications, $predictedLabels);
                    } else {
                        $predictedLabels = $classifier->predict($samples);

                        $publications = array_map(function ($arr, $predictedLabels) {
                            $arr['categories'] = array($predictedLabels => '1');
                            return $arr;
                            
                        }, $publications, $predictedLabels);
                    }
                } else {
                    $predictedLabels = $classifier->predict($samples);

                    $publications = array_map(function ($arr, $predictedLabels) {
                        $arr['categories'] = array($predictedLabels => '1');
                        return $arr;
                        
                    }, $publications, $predictedLabels);
                }
                
                foreach ($publications as &$publication) {
                    $recent_publication_id = $this->Classifier_Model->add_publication_predict($id, $publication['publication_id'], '0');

                    foreach ($publication['categories'] as $key => $value) {
                        $this->Classifier_Model->add_publication_predict_cat($recent_publication_id, $key, $value);
                    }

                    $recent_publication_ids[] = $recent_publication_id;

                    $publication['experts'] = $this->Classifier_Model->get_expert_alias_publication($publication['publication_id'], FALSE);

                    if (!empty($publication['experts'])) {
                        foreach ($publication['experts'] as $experts) {
                            $expert_alias_publication_ids[] = $experts['expert_id'];
                        }
                    }
                }
                
                if(isset($expert_alias_publication_ids)) {
                    $expert_alias_publication_ids = array_values(array_unique($expert_alias_publication_ids));
                    
                    foreach ($expert_alias_publication_ids as $expert_alias_publication_id) {
                        $expert_publications[] = $this->Classifier_Model->get_expert_alias_publication(FALSE, $expert_alias_publication_id);
                    }
                    
                    $all_categories = $this->Classifier_Model->get_category(FALSE, $table_name);

                    $specific_expert_id = 0;
                    foreach ($expert_publications as &$i) {
                        $num_publications = 0;
                        $sum_categories = array();
                        $nof_publications = array();
                        foreach ($i as &$expert_publication) {
                            $expert_publication['publications'] = $this->Classifier_Model->get_publication_predict_multiple($id, $expert_publication['publication_id']);
                            $specific_expert_id = $expert_publication['expert_id'];
                            
                            if ($expert_publication['publications'] != NULL) {
                                $categories = $this->Classifier_Model->get_publication_predict_cat($expert_publication['publications']['publication_predict_id']);
                                
                                $distinct_categories = array();
                                foreach($categories as $y) { 
                                    $distinct_categories[$y['publication_cat']] = $y['publication_cat_prob']; 

                                    if(!isset($sum_categories[$y['publication_cat']])){
                                        $sum_categories[$y['publication_cat']] = 0;
                                    }
                                    $sum_categories[$y['publication_cat']] += $y['publication_cat_prob'];
                                }

                                $expert_publication['publications']['categories'] = $distinct_categories;
                                $num_publications++;
                                
                                $highest_publication = array_keys($distinct_categories, max($distinct_categories));
                                $nof_publications[] = $highest_publication[0];
                            }
                        }
                        
                        $nof_publications = array_count_values($nof_publications);
                        
                        foreach ($all_categories as $all_category) {
                            if(!array_key_exists($all_category['cat_name'], $sum_categories)){
                                $sum_categories[$all_category['cat_name']] = 0;
                            }
                        }

                        if($num_publications != 0) {
                            foreach ($sum_categories as $category => $probabilty) {
                                $sum_categories[$category] = $probabilty/$num_publications;
                            }
                        } else {
                            foreach ($sum_categories as $category => $probabilty) {
                                $sum_categories[$category] = 0;
                            }
                        }    

                        $expert_predict = $this->Classifier_Model->get_expert_predict_multiple($id, $specific_expert_id);
                        $this->Classifier_Model->delete('expert_predict_id', $expert_predict['expert_predict_id'], 'expert_predict');
                        $this->Classifier_Model->delete('expert_predict_id', $expert_predict['expert_predict_id'], 'expert_predict_cat');

                        $recent_expert_id = $this->Classifier_Model->add_expert_predict($id, $specific_expert_id, '0');

                        foreach ($sum_categories as $category => $probabilty) {
                            if(array_key_exists($category, $nof_publications)){
                                $this->Classifier_Model->add_expert_predict_cat($recent_expert_id, $category, $probabilty, $nof_publications[$category]);
                            } else {
                                $this->Classifier_Model->add_expert_predict_cat($recent_expert_id, $category, $probabilty, '0');
                            }                        
                        }                    
                    }
                }
                
                $this->session->set_userdata('recent_publication_ids', $recent_publication_ids);

                $this->session->set_flashdata('success', 'Testing data tested successfully.');
                redirect('admin/the-classifier/classifier-testing/test-article-result/'.$id);
            }            
            redirect('admin/the-classifier/classifier-testing/test-text-preprocess/'.$id);
        }

        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/sidebar', $data);
        $this->load->view('admin/the-classifier/test_classifier/' . $page, $data);
        $this->load->view('admin/template/footer', $data);
    }

    public function test_article_result($id = NULL){
        $data['title'] = 'Tested Article';
        $page = 'test_article_result';
        $data['info']['table_name'] = 'publication_predict';
        $data['info']['data_id_name'] = 'publication_predict_id';

        $data['classifiers'] = $this->Classifier_Model->get_classifiers($id); 

        switch ($data['classifiers']['supervised_class_type']) {
            case "svc":
                $data['parameters'] = $this->Classifier_Model->get_svc($data['classifiers']['supervised_class_id']);
                
                if($data['parameters']['svc_prob_est'] == 'true') {
                    $data['svc_prob_bool'] = TRUE;
                } else {
                    $data['svc_prob_bool'] = FALSE;
                }
                break;
            case "knn":
                $data['parameters'] = $this->Classifier_Model->get_knn($data['classifiers']['supervised_class_id']);
                $data['svc_prob_bool'] = FALSE;
                break;
            case "nb":
                $data['parameters'] = $this->Classifier_Model->get_nb($data['classifiers']['supervised_class_id']);
                $data['svc_prob_bool'] = FALSE;
                break;
        }

        $recent_publication_ids = $this->session->userdata('recent_publication_ids');

        $all_publications = $this->Classifier_Model->get_publication_predict();

        foreach ($all_publications as $all_publication) {
            $all_publications_ids[] = $all_publication['publication_predict_id'];
        }
        
        foreach ($recent_publication_ids as $key => $recent_publication_id) {
            if (in_array($recent_publication_id, $all_publications_ids)) {
                $publications[] = $this->Classifier_Model->get_publication_predict($recent_publication_id);
                $categories[] = $this->Classifier_Model->get_publication_predict_cat($recent_publication_id);
            }
        }

        for ($i = 0; $i < count($categories); $i++) {
            foreach($categories[$i] as $y) { 
                $distinct_categories[$i][$y['publication_cat']] = $y['publication_cat_prob']; 
            }
            array_shift($categories[$i]);
        }
        
        $publications = array_map(function ($arr, $distinct_categories) {
            $arr['categories'] = $distinct_categories;
            return $arr;
            
        }, $publications, $distinct_categories);

        foreach ($publications as &$publication) {
            arsort($publication['categories']);
        }

        $data['publications'] = $publications;

        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/sidebar', $data);
        $this->load->view('admin/the-classifier/test_classifier/' . $page, $data);
        $this->load->view('admin/template/footer', $data);
    }

    public function update_tested_publications($id = NULL){
        $data['title'] = 'Update Tested Publications';
        $page = 'update_tested_publications';

        $data['publication_predict'] = $this->Classifier_Model->get_publication_predict($id);
        $data['publication'] = $this->Classifier_Model->get_publications($data['publication_predict']['publication_id']);
        $data['classifiers'] = $this->Classifier_Model->get_classifiers($data['publication_predict']['class_id']);
        
        if ($id != NULL) {
            $publication_predict_all_categories = $this->Classifier_Model->get_publication_predict_cat($id);

            $data['publication_predict']['categories'] = $publication_predict_all_categories;

            $table_name = 'classifier_'.$data['publication_predict']['class_id'];

            $categories = $this->Classifier_Model->get_category(FALSE, $table_name);
            foreach($categories as $category) {
                $data['categories'][] = $category['cat_name'];
            }
            
        }

        if ($id === NULL) {
            $publication_predict_id = $this->input->post('publication-predict-id');
            $publication_cats = $this->input->post('publication-predict-cat');
            $publication_cat_default = $this->input->post('publication-predict-cat-default');
            $publication_cat_prob = $this->input->post('publication-predict-cat-prob');

            //$publication_predicts = array_combine($publication_cat,$publication_cat_prob);
            for ($x = 0; $x < count($publication_cats); $x++) {
                $publication_predicts[$x] = array("publication_predict_cat" => $publication_cats[$x], 
                "publication_cat_default" => $publication_cat_default[$x], 
                "publication_cat_prob" => $publication_cat_prob[$x]/100);
            } 

            foreach ($publication_predicts as $publication_predict) {
                $this->Classifier_Model->update_publication_predict_cat($publication_predict_id, 
                $publication_predict['publication_predict_cat'], 
                $publication_predict['publication_cat_default'], 
                $publication_predict['publication_cat_prob']);
            }            

            $this->session->set_flashdata('success', 'Publication information updated successfully.');
            redirect('admin/the-classifier/classifier-testing/update-tested-publications/'.$publication_predict_id);
        }

        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/sidebar', $data);
        $this->load->view('admin/the-classifier/test_classifier/' . $page, $data);
        $this->load->view('admin/template/footer', $data);
    }

    public function check_classifier_name_exists($class_name){
        $this->form_validation->set_message('check_classifier_name_exists', 'Classifier name: %s is already taken, Please enter a different one.', $class_name);
        
        if ($this->Classifier_Model->check_classifier_name_exists($class_name)) {
            return true;
        }else{
            return false;
        }
    }

    public function update_keyword(){
        $data_id_name = $this->input->post('data_id_name');
        $data_id = $this->input->post('data_id');
        $data_table = 'keyword_'.$this->input->post('data_table');
        $keyword = $this->input->post('keyword');
        
        $this->Classifier_Model->update_keyword($data_id_name, $data_id, $data_table, $keyword);
    }

    public function update_category(){
        $data_id_name = $this->input->post('data_id_name');
        $data_id = $this->input->post('data_id');
        $data_table = 'category_'.$this->input->post('data_table');
        $category = $this->input->post('category');
        
        $this->Classifier_Model->update_category($data_id_name, $data_id, $data_table, $category);
    }

    public function update_token(){
        $data_id_name = $this->input->post('data_id_name');
        $data_id = $this->input->post('data_id');
        $data_table = 'token_'.$this->input->post('data_table');
        $token = $this->input->post('token');
        $ori_token = $this->input->post('ori_token');
        
        $this->Classifier_Model->update_token($data_id_name, $data_id, $data_table, $token);
        
        $id = $this->input->post('class_id');
        $data['classifiers'] = $this->Classifier_Model->get_classifiers($id);
        $table_name = 'classifier_'.$data['classifiers']['class_id'];
        $keywords = $this->Classifier_Model->get_keyword(FALSE, $table_name);

        foreach ($keywords as $key => $sample) { 
            $this->Classifier_Model->update_keyword_preprocessed('key_id', $sample['key_id'], 'keyword_'.$table_name, 
                $this->match_replace_word($sample['preprocesskey_txt'], $ori_token, $token));
        }
    }

    public function update_stopword(){
        $data_id_name = $this->input->post('data_id_name');
        $data_id = $this->input->post('data_id');
        $data_table = 'stopword_'.$this->input->post('data_table');
        $stopword = $this->input->post('stopword');
        
        $this->Classifier_Model->update_stopword($data_id_name, $data_id, $data_table, $stopword);
    }

    public function delete(){
        $data_id_name = $this->input->post('data_id_name');
        $data_id = $this->input->post('data_id');
        $data_table = $this->input->post('data_table');
        
        $this->Classifier_Model->delete($data_id_name, $data_id, $data_table);
    }

    public function delete_all(){
        $data_id_name = $this->input->post('data_id_name'); //class_id
        $data_id = $this->input->post('data_id');
        $data_table = $this->input->post('data_table'); //classifier
        $table_name = 'classifier_'.$data_id;
        $supervised_class = $this->Classifier_Model->get_supervised_class($data_id);
        $restoredClassifier = $this->Classifier_Model->get_serialized_data($data_id);
        $file = dirname(dirname(dirname(__FILE__))).'/'.$restoredClassifier['serialized_data_string'];
        
        $this->Classifier_Model->drop('category_'.$table_name);
        $this->Classifier_Model->drop('keyword_'.$table_name);
        $this->Classifier_Model->drop('stopword_'.$table_name);
        $this->Classifier_Model->drop('token_'.$table_name);
        $this->Classifier_Model->delete($data_id_name, $data_id, $data_table); 
        $this->Classifier_Model->delete($data_id_name, $data_id, 'expert_predict');
        $this->Classifier_Model->delete($data_id_name, $data_id, 'publication_predict');
        $this->Classifier_Model->delete($data_id_name, $data_id, 'school_predict');
        $this->Classifier_Model->delete($data_id_name, $data_id, 'sel_test_feature');
        $this->Classifier_Model->delete($data_id_name, $data_id, 'serialized_data');
        $this->Classifier_Model->delete('supervised_class_id', $supervised_class['supervised_class_id'], 'svc');
        $this->Classifier_Model->delete('supervised_class_id', $supervised_class['supervised_class_id'], 'knn');
        $this->Classifier_Model->delete('supervised_class_id', $supervised_class['supervised_class_id'], 'nb');
        $this->Classifier_Model->delete($data_id_name, $data_id, 'supervised_class');
        unlink($file);
    }

    public function get_article_list_checked(){
        $article_check_ids = $this->input->post('article_check_ids');
        $this->session->set_userdata('article_check_ids', $article_check_ids);
    }

    public function reset_expert(){ //need to know deleted wat publications
        $id = $this->input->post('data_class_id');
        $publication_predict_id = $this->input->post('data_id');
        $publication_id = $this->input->post('data_publication_id');
        $table_name = 'classifier_'.$id;
        $all_categories = $this->Classifier_Model->get_category(FALSE, $table_name);
        $specific_expert_id = 0;

        $publication = $this->Classifier_Model->get_publications($publication_id);
        $publication['experts'] = $this->Classifier_Model->get_expert_alias_publication($publication['publication_id'], FALSE);

        if (!empty($publication['experts'])) { 
            foreach ($publication['experts'] as $experts) {
                $expert_alias_publication_ids[] = $experts['expert_id'];
            }

            $expert_alias_publication_ids = array_values(array_unique($expert_alias_publication_ids));

            foreach ($expert_alias_publication_ids as $expert_alias_publication_id) {
                $expert_publications[] = $this->Classifier_Model->get_expert_alias_publication(FALSE, $expert_alias_publication_id);
            }

            foreach ($expert_publications as &$i) { 
                $num_publications = 0;
                $sum_categories = array();
                $num_highest_publications = array();
                foreach ($i as &$expert_publication) {
                    $expert_publication['publications'] = $this->Classifier_Model->get_publication_predict_multiple($id, $expert_publication['publication_id']);
                    $specific_expert_id = $expert_publication['expert_id'];

                    if ($expert_publication['publications'] != NULL) {
                        $categories = $this->Classifier_Model->get_publication_predict_cat($expert_publication['publications']['publication_predict_id']);

                        $distinct_categories = array();
                        foreach($categories as $y) { 
                            $distinct_categories[$y['publication_cat']] = $y['publication_cat_prob']; 

                            if(!isset($sum_categories[$y['publication_cat']])){
                                $sum_categories[$y['publication_cat']] = 0;
                            }
                            $sum_categories[$y['publication_cat']] += $y['publication_cat_prob'];
                        }

                        $expert_publication['publications']['categories'] = $distinct_categories;
                        $num_publications++;

                        $highest_publication = array_keys($distinct_categories, max($distinct_categories));
                        $num_highest_publications[] = $highest_publication[0];
                    }
                }
                $num_highest_publications = array_count_values($num_highest_publications);

                foreach ($all_categories as $all_category) {
                    if(!array_key_exists($all_category['cat_name'], $sum_categories)){
                        $sum_categories[$all_category['cat_name']] = 0;
                    }
                }
                
                if($num_publications != 0) {
                    foreach ($sum_categories as $category => $probabilty) {
                        $sum_categories[$category] = $probabilty/$num_publications;
                    }
                } else {
                    foreach ($sum_categories as $category => $probabilty) {
                        $sum_categories[$category] = 0.00;
                    }
                }

                $expert_predict = $this->Classifier_Model->get_expert_predict_multiple($id, $specific_expert_id);
                $this->Classifier_Model->delete('expert_predict_id', $expert_predict['expert_predict_id'], 'expert_predict');
                $this->Classifier_Model->delete('expert_predict_id', $expert_predict['expert_predict_id'], 'expert_predict_cat');

                $recent_expert_id = $this->Classifier_Model->add_expert_predict($id, $specific_expert_id, '0');

                foreach ($sum_categories as $category => $probabilty) {
                    if(array_key_exists($category, $num_highest_publications)){
                        $this->Classifier_Model->add_expert_predict_cat($recent_expert_id, $category, $probabilty, $num_highest_publications[$category]);
                    } else {
                        $this->Classifier_Model->add_expert_predict_cat($recent_expert_id, $category, $probabilty, '0');
                    }   
                }
            }
        }
        $this->session->set_flashdata('success', 'Data has been deleted Successfully.');
    }

    public function train_data_input_folder($input_file, $input_folder) {
        $count_read = 0;

        if(isset($input_file) && isset($input_folder)){
            if (count($_FILES[$input_file]['name']) > 1) {
                $current_category = json_decode($this->input->post($input_folder));
                
                foreach ($_FILES[$input_file]['name'] as $i => $name) {
                    if (strlen($_FILES[$input_file]['name'][$i]) > 1) {
                        $path = $_FILES[$input_file]['tmp_name'][$i];

                        if ($handle = @fopen($path, "r")) {
                            $x = 0; // Start the line counter

                            //Cycle each line until end or reach the lines to return limit
                            while (!feof($handle)) { //or $x < $linesToReturn
                                $line = mb_strtolower(trim($this->remove_utf8_bom(fgets($handle))), 'UTF-8'); // Read the line

                                if ($line != null && $line != "" && !empty($line)) {
                                    $category_texts[] = array("text" => $line, "category" => $current_category[$count_read]);

                                    $x++; // Increase the counter
                                }
                            }
                            $count_read++;
                        }
                        $handle = @fclose($path);
                    }
                }
                return $category_texts;
            } 
        } else {
            return false;
        }
    }

    public function train_data_input_excel($input_file) {
        if(isset($input_file)){
            if ($_FILES[$input_file]['size'] > 0) {
                $file = @fopen($_FILES[$input_file]['tmp_name'], "r");

                $flag = true;
                while (($datas = fgetcsv($file, 1000000, ",")) !== false) {
                    if ($flag) {
                        $flag = false; //skip header
                        continue;
                    }
                    $category_texts[] = array("text" => $datas[1], "category" => $datas[0]);
                }
                @fclose($path);

                $flag = true;
                foreach ($category_texts as $category_text) {
                    if ($flag) {
                        $categories_flag = $category_text['category'];
                        $categories_text[] = $category_text['text'];
                        $flag = false;
                        continue;
                    }

                    if ($categories_flag != $category_text['category']) {
                        $categories_flag = $category_text['category'];
                        $categories_text[] = $category_text['text'];
                    }
                }

                foreach ($categories_text as $key => $value) {
                    foreach ($category_texts as &$category_text) { //use reference in foreach to insert data
                        if ($category_text['category'] == $key) {
                            $category_text['category'] = $value;
                        }
                    }
                }
                
                foreach ($category_texts as $key => $values) {
                    if ($values['category'] == $values['text']) {
                        unset($category_texts[$key]);
                    }
                }
                $category_texts = array_values($category_texts);

                return $category_texts;
            }
        } else {
            return false;
        }
    }

    public function train_input_stopword($input_file) {
        if ($_FILES[$input_file]['size'] > 0) {
            
            $file = @fopen($_FILES[$input_file]['tmp_name'], "r");

            while(!feof($file)){
                $stopword[] = trim($this->remove_utf8_bom(fgets($file)));
            }
            @fclose($file);

            return $stopword;
        } else {
            return false;
        }
    }

    function remove_utf8_bom($text){
        $bom = pack('H*','EFBBBF');
        $text = preg_replace("/^$bom/", '', $text);
        return $text;
    }

    public function enable(){
        $data_id = $this->uri->segment(4);
        $data_table = $this->uri->segment(5);

        $this->Classifier_Model->update_visibility($data_id, 1, $data_table);
        $this->session->set_flashdata('success', 'Enabled Successfully.');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

    public function disable(){
        $data_id = $this->uri->segment(4);
        $data_table = $this->uri->segment(5);

        $this->Classifier_Model->update_visibility($data_id, 0, $data_table);
        $this->session->set_flashdata('success', 'Disabled Successfully.');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

    function format_period($seconds_input) {
        $hours = floor($seconds_input / 3600);
        $mins = floor($seconds_input / 60 % 60);
        $secs = floor($seconds_input % 60);
        return sprintf('%02d hours : %02d minutes : %02d seconds', $hours, $mins, $secs);
    }

    function number_removal($strings) {
        $WhitespaceTokenizer = new WhitespaceTokenizer();

        $tokens = $WhitespaceTokenizer->tokenize($strings);

        foreach ($tokens as $key => $token) {
            $tokens[$key] = preg_replace("/[0-9]+/", "", $token);
        }

        return implode(" ",$tokens);
    }

    function match_replace_word($strings, $ori_token, $new_token) {
        $WordTokenizer = new WordTokenizer();
        $boolean = false;

        $tokens = $WordTokenizer->tokenize($strings);

        foreach ($tokens as $key => $token) {
            if ($token == $ori_token) {
                $tokens[$key] = $new_token;
                $boolean = true;
            }
        }

        if ($boolean == true) {
            return implode(" ", $tokens);
        } else {
            return $strings;
        }
    }

    function decimal_places_step ($number) {
        $no_dp = strlen(substr(strrchr($number, "."), 1)) - 1;
        $dp_step = '0.';
        for ($x = 0; $x <= $no_dp; $x++) {
            if ($x == $no_dp) {
                $dp_step = $dp_step.'1';
                break;
            }
            $dp_step = $dp_step.'0';
        }

        return $dp_step;
    }
}
