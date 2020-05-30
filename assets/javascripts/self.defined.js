function checkAll() {
  		
    var main_check = document.getElementById('main_checkbox');

    var checkboxes = new Array(); 
      checkboxes = document.getElementsByTagName('input');

      for (var i=0; i<checkboxes.length; i++) {
        if (checkboxes[i].type == 'checkbox') {
              checkboxes[i].checked = main_check.checked;
        }
      }		
  }

function add_pub_confirm() {

    var add_pub_select = document.getElementById('add_unmatched');
    var sel_opt = add_pub_select.options[add_pub_select.selectedIndex].value;
    var opt_value_arr = sel_opt.split("|");
    if(opt_value_arr[0] == '' || opt_value_arr[2] == 'false'){
        document.getElementById('add_unmatched_form').submit();
    } else {
        if (confirm('This alias was already matched with other expert, are you sure you want to add it ?')) {
            document.getElementById('add_unmatched_form').submit();
        } else {
            return false;
        }
    }
}

function delete_expert(){
    if (confirm('Are you sure you want to delete this expert ?')) {
        document.getElementById('edit_exp_form').submit();
    } else {
        return false;
    }
}

function delete_pub(){
    if (confirm('Are you sure you want to delete this publication ?')) {
        document.getElementById('edit_pub_form').submit();
    } else {
        return false;
    }
}

function delete_school(){
    if (confirm('Are you sure you want to delete this school ?')) {
        document.getElementById('edit_school_form').submit();
    } else {
        return false;
    }
}

function auto_confirm_matching(){
    var auto_conf_none = document.getElementById('auto_conf_none');
    var auto_conf_high = document.getElementById('auto_conf_high');
    var auto_conf_medium = document.getElementById('auto_conf_medium');
    var auto_conf_low = document.getElementById('auto_conf_low');

    if(auto_conf_none.checked === true){
        auto_conf_high.checked  = false;
        auto_conf_medium.checked = false;
        auto_conf_low.checked = false;

        auto_conf_high.disabled  = true;
        auto_conf_medium.disabled  = true;
        auto_conf_low.disabled  = true;
    } else {
        auto_conf_high.disabled  = false;
        auto_conf_medium.disabled  = false;
        auto_conf_low.disabled  = false;
    }
 
    if(auto_conf_high.checked  === true || auto_conf_medium.checked  === true || auto_conf_low.checked  === true){
        auto_conf_none.checked  = false;
        auto_conf_none.disabled  = true;
    }

    if(auto_conf_high.checked  === false && auto_conf_medium.checked  === false && auto_conf_low.checked  === false){
        auto_conf_none.disabled  = false;
    }

}

function view_uni_dropdown(){
    var view_uni_dropdown_check = document.getElementById('check_select_uni');
    var uni_dropdown = document.getElementById('school_option_block');

    if(view_uni_dropdown_check.checked  === true){
        uni_dropdown.style.display = 'block';
    } else {
        uni_dropdown.style.display = 'none';
    }
}

function set_university() {
    var school = document.getElementById("expert_school");
    var opt = school.options[school.selectedIndex];
    document.getElementById("expert_university").value = opt.parentNode.label;
}

function preview_image(event) {
    var reader = new FileReader();
    reader.onload = function() {
        document.getElementById('preview_expert_photo').src = reader.result;
        document.getElementById('preview_photo').style.display = "block";
        document.getElementById('ori_photo').style.display = "none";
        document.getElementById('upload_photo_btn').style.display = "block";
    }
    reader.readAsDataURL(event.target.files[0]);
}

function remove_preview(){
    document.getElementById('preview_expert_photo').src = '';
    document.getElementById('preview_photo').style.display = "none";
    document.getElementById('ori_photo').style.display = "block";
    document.getElementById('upload_photo_btn').style.display = "none";
}

function preview_school_image(event) {
    var reader = new FileReader();
    reader.onload = function() {
        document.getElementById('preview_school_photo').src = reader.result;
        document.getElementById('preview_photo_frame').style.display = "block";
        document.getElementById('ori_photo').style.display = "none";
        document.getElementById('upload_photo_btn').style.display = "block";
    }
    reader.readAsDataURL(event.target.files[0]);
}

function remove_school_preview(){
    document.getElementById('preview_school_photo').src = '';
    document.getElementById('preview_photo_frame').style.display = "none";
    document.getElementById('ori_photo').style.display = "block";
    document.getElementById('upload_photo_btn').style.display = "none";
}


function add_multi_school(selectObject){
    var schools = [];
        $.each($("#school_name option:selected"), function(){            
            schools.push($(this).val());
        });
        document.getElementById("multiselected_school").value = schools.join("|");
}

function disp_export_option(){
    var export_filter_school = document.getElementById('export_filter_school');
    var export_filter_year = document.getElementById('export_filter_year');
    var export_filter_expert = document.getElementById('export_filter_expert');

    var school_opt_block = document.getElementById('school_opt_block');
    var year_opt_block = document.getElementById('year_opt_block');
    var expert_opt_block = document.getElementById('expert_opt_block');

    if(export_filter_school.checked === true){
        school_opt_block.style.display = 'block';
    } else {
        school_opt_block.style.display = 'none';
    }

    if(export_filter_year.checked === true){
        year_opt_block.style.display = 'block';
    } else {
        year_opt_block.style.display = 'none';
    }

    if(export_filter_expert.checked === true){
        expert_opt_block.style.display = 'block';
    } else {
        expert_opt_block.style.display = 'none';
    }

}
