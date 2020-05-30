<div class="container-fluid" style="padding-top:20px">
        <div class="row d-flex justify-content-center">
            <div class="col-md-2 text-center">
                <img src="assets/images/the_recommender.png" alt="USM Computer Science Expert Logo" width="180" height="180" style="margin-top:-30px;"> 
            </div>
           <div class="col-md-10 p-2">
            <form action="<?php echo base_url() . "search_result"; ?>", method="get">
                <div class="input-group input-width col-md-6 col-sm-3" style="max-width:700px; margin-top: 55px;">
                    <input class="form-control py-2 border-right-0" style="border-width: 2px;" type="search" name="q" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search a topic, keyword, short abstract, etc. (Maximum 80 words)"id="search-input" required>
                    <span class="input-group-append">
                        <button class="btn border border-left-0" type="submit" style="height: 36px;margin-top: 0px;">
                        <i class="fa fa-search centre" style="padding-top: 8px;"></i>
                      </button>
                    </span>

                </div>
            </form> 
          </div>
           <div class="col-lg-7.5 col-md-8 col-sm-7 custom-padding" style="margin-left: -100;">
                <!-- Nav tabs -->
                <div class="tab" style="width:850px;">
                        <button class="tablinks active" style="width: 849px" onclick="change(event,'firstword')">
                                    <?php echo("<b>"."Experts intersect between recommended list of keywords : "."</b>"."<br>") ?>
                                    <?php echo("<b>"."<i>".implode(" , ",$keywords)."</i>"."</b>");?>
                            
                        </button>
                </div>
                
                <!-- Tab panes -->
                <?php if(count($keywords) == 2) :?>
                <div id="firstword" class="tabcontent" style="display: block;width:850px;padding-top: 20px;">
                  <?php if(count($rank_list) == 0):?>
                    <?php echo ("No expert intersect between recommended list related to keywords you selected.")?>
                  <?php else:?>
                    <?php for($i = 0; $i < count($rank_list[0]); $i++):?>
                        <div class="profile" style="
                                padding-bottom: 0px;
                                padding-top: 10px;
                                margin-top: 50px;
                                margin-left: 50px;
                                height: 160px;
                                width: 665px;
                            ">
                            <div class="profile__picture" style="margin-left: 0px;border-left-width: 8px;"><img src="<?php echo($rank_list[0][$i]["photo"]); ?>" id="expert-image" alt="https://img.icons8.com/dusk/64/000000/gender-neutral-user--v1.png" style="
                                    height: 90px;
                                    width: 90px;
                                    border-left-width: 20px;
                                    margin-top: 5px;
                                    margin-left: 10px;
                                "></div>
                            <div class="profile__header">
                              <div class="profile__account">
                              <h4 class="profile__username" style="height: 40px;padding-top: 10px;width: 500px;">
                                <?php echo("Dr. ".$rank_list[0][$i]["Expert_name"]); ?></h4>
                              </div>
                            </div>
                            <div class="profile__stats">
                                <div class="profile__stat">
                                  <div class="profile__value" style="height: 40px;"><?php echo("Score : ".$rank_list[0][$i]["score1"]); ?>
                                    <div class="profile__key"><b><i><?php echo($keywords[0]); ?></i></b></div>
                                    <a href="<?php echo base_url().'expert_profile?expert_id=' .$rank_list[0][$i]["expert_id"].'&amp;keyword='.$keywords[0].'&amp;tab=summary';?>" class="profile__button" style="
                                        padding-top: 2px;
                                        padding-bottom: 5px;
                                        padding-left: 0px;
                                        padding-right: 0px;
                                        width: 122px;
                                        height: 25px;
                                        margin-left: 0px;
                                    "><b>Go to profile</b></a>
                                  </div>
                                </div>
                                <div class="profile__stat">
                                  <div class="profile__value" style="height: 40px;"><?php echo("Score : ".$rank_list[0][$i]["score2"]); ?>
                                    <div class="profile__key"><b><i><?php echo($keywords[1]); ?></i></b></div>
                                    <a href="<?php echo base_url().'expert_profile?expert_id=' .$rank_list[0][$i]["expert_id"].'&amp;keyword='.$keywords[1].'&amp;tab=summary';?>" class="profile__button" style="
                                        padding-top: 2px;
                                        padding-bottom: 5px;
                                        padding-left: 0px;
                                        padding-right: 0px;
                                        width: 122px;
                                        height: 25px;
                                        margin-left: 0px;
                                    "><b>Go to profile</b></a>
                                  </div>
                                </div>
                                <div class="profile__stat" style="
                                    padding-left: 4px;
                                    padding-top: 10px;
                                    padding-right: 0px;
                                ">
                                  <div class="profile__icon"><img style="width:24px;height:22px;" src="https://img.icons8.com/flat_round/64/000000/star--v1.png"/></div>
                                  <div class="profile__value" style="height: 40px;"><?php 
                                                echo($rank_list[0][$i]["sum_score"]); ?>
                                    <div class="profile__key"><b>Average Score (0-1)</b></div>
                                  </div>
                                </div>
                            </div>
                        </div>  
                      <?php endfor;?>
                    <?php endif;?>                       
                        <!--<div class="wrapper">
                                <div class="list">
                                    <div class="list__body">
                                      <table class="list__table">
                                        <?php if(count($rank_list) == 0):?>
                                            <?php echo ("No expert intersect between recommended list related to keywords you selected.")?>
                                        <?php else:?>
                                        <?php for($i = 0; $i < count($rank_list[0]); $i++):?>
                                        <tr class="list__row" data-image="https://www.formula1.com/content/fom-website/en/drivers/lewis-hamilton/_jcr_content/image.img.1920.medium.jpg/1533294345447.jpg" data-nationality="British" data-dob="1985-01-07" data-country="gb">
                                          <td class="list__cell"><span class="list__value"><?php echo $i+1 ?></span></td>
                                          <td class="list__cell"><span class="list__value"><?php 
                                              echo("<b>"."<i>"."Dr. ".$rank_list[0][$i]["Expert_name"]."</i>"."</b>"."<br/>")."<br/>"."<br/>"; ?></a></span></td>
                                          <td class="list__cell"><span class="list__value">
                                             <?php echo("<b>" ."<i>"."Score for ".$keywords[0]." : ".$rank_list[0][$i]["score1"]."</i>"."</b>"."<br/>")."<br/>"."<br/>"; ?></span><small class="list__label"><a href="<?php echo base_url().'expert_profile?expert_id=' .$rank_list[0][$i]["expert_id"].'&amp;keyword='.$keywords[0].'&amp;tab=summary';?>"><b>Go to profile</b></a></small></td>
                                          <td class="list__cell"><span class="list__value"><?php 
                                                  echo("<b>"."<i>"."Score for ".$keywords[1]." : ".$rank_list[0][$i]["score2"]."</i>"."</b>"."<br/>")."<br/>"."<br/>"; ?></span><small class="list__label"><a href="<?php echo base_url().'expert_profile?expert_id=' .$rank_list[0][$i]["expert_id"].'&amp;keyword='.$keywords[1].'&amp;tab=summary';?>"><b>Go to profile</b></a></small></td>
                                          <td class="list__cell"><span class="list__value"><?php 
                                                  echo("<b>"."<i>".$rank_list[0][$i]["sum_score"]."</i>"."</b>"."<br/>")."<br/>"."<br/>"; ?></span><small class="list__label"><b>Average Score</b></small></td>
                                        </tr>
                                         <?php endfor;?>
                                       <?php endif;?>
                                      </table>
                                    </div>
                                </div>
                            </div>-->
                </div>
                <?php elseif(count($keywords) == 3) :?>
                <div id="firstword" class="tabcontent" style="display: block;width:850px;padding-top: 20px;">
                  <?php if(count($rank_list) == 0):?>
                    <?php echo ("No expert intersect between recommended list related to keywords you selected.")?>
                  <?php else:?>
                    <?php for($i = 0; $i < count($rank_list[0]); $i++):?>
                        <div class="profile" style="
                                padding-bottom: 0px;
                                padding-top: 10px;
                                margin-top: 50px;
                                margin-left: 50px;
                                height: 160px;
                                width: 665px;
                            ">
                            <div class="profile__picture" style="margin-left: 0px;border-left-width: 8px;"><img src="<?php echo($rank_list[0][$i]["photo"]); ?>" id="expert-image" alt="https://img.icons8.com/dusk/64/000000/gender-neutral-user--v1.png" style="
                                    height: 90px;
                                    width: 90px;
                                    border-left-width: 20px;
                                    margin-top: 5px;
                                    margin-left: 10px;
                                "></div>
                            <div class="profile__header">
                            <div class="profile__account">
                            <h4 class="profile__username" style="height: 40px;padding-top: 10px;width: 500px;">
                              <?php echo("Dr. ".$rank_list[0][$i]["Expert_name"]); ?></h4>
                            </div>
                            </div>
                            <div class="profile__stats">
                               <div class="profile__stat">
                                  <div class="profile__value" style="height: 40px;"><?php echo("Score : ".$rank_list[0][$i]["score1"]); ?>
                                    <div class="profile__key"><b><i><?php echo($keywords[0]); ?></i></b></div>
                                    <a href="<?php echo base_url().'expert_profile?expert_id=' .$rank_list[0][$i]["expert_id"].'&amp;keyword='.$keywords[0].'&amp;tab=summary';?>" class="profile__button" style="
                                        padding-top: 2px;
                                        padding-bottom: 5px;
                                        padding-left: 0px;
                                        padding-right: 0px;
                                        width: 122px;
                                        height: 25px;
                                        margin-left: 0px;
                                    "><b>Go to profile</b></a>
                                  </div>
                                </div>
                                <div class="profile__stat">
                                  <div class="profile__value" style="height: 40px;"><?php echo("Score : ".$rank_list[0][$i]["score2"]); ?>
                                    <div class="profile__key"><b><i><?php echo($keywords[1]); ?></i></b></div>
                                    <a href="<?php echo base_url().'expert_profile?expert_id=' .$rank_list[0][$i]["expert_id"].'&amp;keyword='.$keywords[1].'&amp;tab=summary';?>" class="profile__button" style="
                                        padding-top: 2px;
                                        padding-bottom: 5px;
                                        padding-left: 0px;
                                        padding-right: 0px;
                                        width: 122px;
                                        height: 25px;
                                        margin-left: 0px;
                                    "><b>Go to profile</b></a>
                                  </div>
                                </div>
                                <div class="profile__stat">
                                  <div class="profile__value" style="height: 40px;"><?php echo("Score : ".$rank_list[0][$i]["score3"]); ?>
                                    <div class="profile__key"><b><i><?php echo($keywords[2]); ?></i></b></div>
                                    <a href="<?php echo base_url().'expert_profile?expert_id=' .$rank_list[0][$i]["expert_id"].'&amp;keyword='.$keywords[2].'&amp;tab=summary';?>" class="profile__button" style="
                                        padding-top: 2px;
                                        padding-bottom: 5px;
                                        padding-left: 0px;
                                        padding-right: 0px;
                                        width: 122px;
                                        height: 25px;
                                        margin-left: 0px;
                                    "><b>Go to profile</b></a>
                                  </div>
                                </div>
                              <div class="profile__stat" style="
                                  padding-left: 4px;
                                  padding-top: 0px;
                                  padding-right: 0px;
                              ">
                                <div class="profile__icon"><img style="width:24px;height:22px;" src="https://img.icons8.com/flat_round/64/000000/star--v1.png"/></div>
                                <div class="profile__value" style="height: 40px;"><?php 
                                              echo($rank_list[0][$i]["sum_score"]); ?>
                                  <div class="profile__key"><b>Average Score (0-1)</b></div>
                                </div>
                              </div>
                            </div>
                        </div>  
                      <?php endfor;?>
                    <?php endif;?>     
                </div>
                <?php elseif(count($keywords) == 4) :?>
                <div id="firstword" class="tabcontent" style="display: block;width:850px">
                  <?php if(count($rank_list) == 0):?>
                    <?php echo ("No expert intersect between recommended list related to keywords you selected.")?>
                  <?php else:?>
                    <?php for($i = 0; $i < count($rank_list[0]); $i++):?>
                        <div class="profile" style="
                                padding-bottom: 0px;
                                padding-top: 10px;
                                margin-top: 50px;
                                margin-left: 0px;
                                height: 160px;
                                width: 700px;
                            ">
                            <div class="profile__picture" style="margin-left: 0px;border-left-width: 8px;"><img src="<?php echo($rank_list[0][$i]["photo"]); ?>" id="expert-image" alt="https://img.icons8.com/dusk/64/000000/gender-neutral-user--v1.png" style="
                                    height: 90px;
                                    width: 90px;
                                    border-left-width: 20px;
                                    margin-top: 5px;
                                    margin-left: 10px;
                                "></div>
                            <div class="profile__header">
                            <div class="profile__account">
                            <h4 class="profile__username" style="height: 40px;padding-top: 10px;width: 500px;">
                              <?php echo("Dr. ".$rank_list[0][$i]["Expert_name"]); ?></h4>
                            </div>
                            </div>
                            <div class="profile__stats">
                              <div class="profile__stat">
                                  <div class="profile__value" style="height: 40px;"><?php echo("Score : ".$rank_list[0][$i]["score1"]); ?>
                                    <div class="profile__key"><b><i><?php echo($keywords[0]); ?></i></b></div>
                                    <a href="<?php echo base_url().'expert_profile?expert_id=' .$rank_list[0][$i]["expert_id"].'&amp;keyword='.$keywords[0].'&amp;tab=summary';?>" class="profile__button" style="
                                        padding-top: 2px;
                                        padding-bottom: 5px;
                                        padding-left: 0px;
                                        padding-right: 0px;
                                        width: 122px;
                                        height: 25px;
                                        margin-left: 0px;
                                    "><b>Go to profile</b></a>
                                  </div>
                                </div>
                                <div class="profile__stat">
                                  <div class="profile__value" style="height: 40px;"><?php echo("Score : ".$rank_list[0][$i]["score2"]); ?>
                                    <div class="profile__key"><b><i><?php echo($keywords[1]); ?></i></b></div>
                                    <a href="<?php echo base_url().'expert_profile?expert_id=' .$rank_list[0][$i]["expert_id"].'&amp;keyword='.$keywords[1].'&amp;tab=summary';?>" class="profile__button" style="
                                        padding-top: 2px;
                                        padding-bottom: 5px;
                                        padding-left: 0px;
                                        padding-right: 0px;
                                        width: 122px;
                                        height: 25px;
                                        margin-left: 0px;
                                    "><b>Go to profile</b></a>
                                  </div>
                                </div>
                                <div class="profile__stat">
                                  <div class="profile__value" style="height: 40px;"><?php echo("Score : ".$rank_list[0][$i]["score3"]); ?>
                                    <div class="profile__key"><b><i><?php echo($keywords[2]); ?></i></b></div>
                                    <a href="<?php echo base_url().'expert_profile?expert_id=' .$rank_list[0][$i]["expert_id"].'&amp;keyword='.$keywords[2].'&amp;tab=summary';?>" class="profile__button" style="
                                        padding-top: 2px;
                                        padding-bottom: 5px;
                                        padding-left: 0px;
                                        padding-right: 0px;
                                        width: 122px;
                                        height: 25px;
                                        margin-left: 0px;
                                    "><b>Go to profile</b></a>
                                  </div>
                                </div>
                                <div class="profile__stat">
                                  <div class="profile__value" style="height: 40px;"><?php echo("Score : ".$rank_list[0][$i]["score4"]); ?>
                                    <div class="profile__key"><b><i><?php echo($keywords[3]); ?></i></b></div>
                                    <a href="<?php echo base_url().'expert_profile?expert_id=' .$rank_list[0][$i]["expert_id"].'&amp;keyword='.$keywords[3].'&amp;tab=summary';?>" class="profile__button" style="
                                        padding-top: 2px;
                                        padding-bottom: 5px;
                                        padding-left: 0px;
                                        padding-right: 0px;
                                        width: 122px;
                                        height: 25px;
                                        margin-left: 0px;
                                    "><b>Go to profile</b></a>
                                  </div>
                                </div>
                              <div class="profile__stat" style="
                                  padding-left: 4px;
                                  padding-top: 0px;
                                  padding-right: 0px;
                              ">
                                <div class="profile__icon"><img style="width:24px;height:22px;" src="https://img.icons8.com/flat_round/64/000000/star--v1.png"/></div>
                                <div class="profile__value" style="height: 40px;"><?php 
                                              echo($rank_list[0][$i]["sum_score"]); ?>
                                  <div class="profile__key"><b>Average Score (0-1)</b></div>
                                </div>
                              </div>
                            </div>
                        </div>  
                      <?php endfor;?>
                    <?php endif;?> 
                </div>
                <?php elseif(count($keywords) == 5) :?>
                <div id="firstword" class="tabcontent" style="display: block;width:850px">
                  <?php if(count($rank_list) == 0):?>
                    <?php echo ("No expert intersect between recommended list related to keywords you selected.")?>
                  <?php else:?>
                    <?php for($i = 0; $i < count($rank_list[0]); $i++):?>
                        <div class="profile" style="
                                padding-bottom: 0px;
                                padding-top: 10px;
                                margin-top: 50px;
                                margin-left: 0px;
                                height: 160px;
                                width: 750px;
                            ">
                            <div class="profile__picture" style="margin-left: 0px;border-left-width: 8px;"><img src="<?php echo($rank_list[0][$i]["photo"]); ?>" id="expert-image" alt="https://img.icons8.com/dusk/64/000000/gender-neutral-user--v1.png" style="
                                    height: 90px;
                                    width: 90px;
                                    border-left-width: 20px;
                                    margin-top: 5px;
                                    margin-left: 10px;
                                "></div>
                            <div class="profile__header">
                            <div class="profile__account">
                            <h4 class="profile__username" style="height: 40px;width: 600px;padding-top: 10px;width: 500px;">
                              <?php echo("Dr. ".$rank_list[0][$i]["Expert_name"]); ?></h4>
                            </div>
                            </div>
                            <div class="profile__stats">
                              <div class="profile__stat">
                                  <div class="profile__value" style="height: 40px;"><?php echo("Score : ".$rank_list[0][$i]["score1"]); ?>
                                    <div class="profile__key"><b><i><?php echo($keywords[0]); ?></i></b></div>
                                    <a href="<?php echo base_url().'expert_profile?expert_id=' .$rank_list[0][$i]["expert_id"].'&amp;keyword='.$keywords[0].'&amp;tab=summary';?>" class="profile__button" style="
                                        padding-top: 2px;
                                        padding-bottom: 5px;
                                        padding-left: 0px;
                                        padding-right: 0px;
                                        width: 122px;
                                        height: 25px;
                                        margin-left: 0px;
                                    "><b>Go to profile</b></a>
                                  </div>
                                </div>
                                <div class="profile__stat">
                                  <div class="profile__value" style="height: 40px;"><?php echo("Score : ".$rank_list[0][$i]["score2"]); ?>
                                    <div class="profile__key"><b><i><?php echo($keywords[1]); ?></i></b></div>
                                    <a href="<?php echo base_url().'expert_profile?expert_id=' .$rank_list[0][$i]["expert_id"].'&amp;keyword='.$keywords[1].'&amp;tab=summary';?>" class="profile__button" style="
                                        padding-top: 2px;
                                        padding-bottom: 5px;
                                        padding-left: 0px;
                                        padding-right: 0px;
                                        width: 122px;
                                        height: 25px;
                                        margin-left: 0px;
                                    "><b>Go to profile</b></a>
                                  </div>
                                </div>
                                <div class="profile__stat">
                                  <div class="profile__value" style="height: 40px;"><?php echo("Score : ".$rank_list[0][$i]["score3"]); ?>
                                    <div class="profile__key"><b><i><?php echo($keywords[2]); ?></i></b></div>
                                    <a href="<?php echo base_url().'expert_profile?expert_id=' .$rank_list[0][$i]["expert_id"].'&amp;keyword='.$keywords[2].'&amp;tab=summary';?>" class="profile__button" style="
                                        padding-top: 2px;
                                        padding-bottom: 5px;
                                        padding-left: 0px;
                                        padding-right: 0px;
                                        width: 122px;
                                        height: 25px;
                                        margin-left: 0px;
                                    "><b>Go to profile</b></a>
                                  </div>
                                </div>
                                <div class="profile__stat">
                                  <div class="profile__value" style="height: 40px;"><?php echo("Score : ".$rank_list[0][$i]["score4"]); ?>
                                    <div class="profile__key"><b><i><?php echo($keywords[3]); ?></i></b></div>
                                    <a href="<?php echo base_url().'expert_profile?expert_id=' .$rank_list[0][$i]["expert_id"].'&amp;keyword='.$keywords[3].'&amp;tab=summary';?>" class="profile__button" style="
                                        padding-top: 2px;
                                        padding-bottom: 5px;
                                        padding-left: 0px;
                                        padding-right: 0px;
                                        width: 122px;
                                        height: 25px;
                                        margin-left: 0px;
                                    "><b>Go to profile</b></a>
                                  </div>
                                </div>
                                <div class="profile__stat">
                                  <div class="profile__value" style="height: 40px;"><?php echo("Score : ".$rank_list[0][$i]["score5"]); ?>
                                    <div class="profile__key"><b><i><?php echo($keywords[4]); ?></i></b></div>
                                    <a href="<?php echo base_url().'expert_profile?expert_id=' .$rank_list[0][$i]["expert_id"].'&amp;keyword='.$keywords[4].'&amp;tab=summary';?>" class="profile__button" style="
                                        padding-top: 2px;
                                        padding-bottom: 5px;
                                        padding-left: 0px;
                                        padding-right: 0px;
                                        width: 122px;
                                        height: 25px;
                                        margin-left: 0px;
                                    "><b>Go to profile</b></a>
                                  </div>
                                </div>
                              <div class="profile__stat" style="
                                  padding-left: 4px;
                                  padding-top: 0px;
                                  padding-right: 0px;
                              ">
                                <div class="profile__icon"><img style="width:24px;height:22px;" src="https://img.icons8.com/flat_round/64/000000/star--v1.png"/></div>
                                <div class="profile__value" style="height: 40px;"><?php 
                                              echo($rank_list[0][$i]["sum_score"]); ?>
                                  <div class="profile__key"><b>Average Score (0-1)</b></div>
                                </div>
                              </div>
                            </div>
                        </div>  
                      <?php endfor;?>
                    <?php endif;?>
                </div>
              <?php endif ?>
         </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type='text/javascript'>
$(document).ready(function() {

function change(evt, word) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(word).style.display = "block";
  evt.currentTarget.className += " active";
}

console.clear();
var tableRow = document.querySelectorAll(".list__row");
var overlay = document.querySelector(".overlay");
var sidebar = document.querySelector(".sidebar");
var closeOverlayBtn = document.querySelector(".button--close");
var sidebarClose = function () {
  sidebar.classList.remove("is-open");
  overlay.style.opacity = 0;
  setTimeout(function () {
    overlay.classList.remove("is-open");
    overlay.style.opacity = 1;
  }, 300);
};
tableRow.forEach(function (tableRow) {
  tableRow.addEventListener("click", function () {
    overlay.style.opacity = 0;
    overlay.classList.add("is-open");
    sidebar.classList.add("is-open");
    setTimeout(function () {
      overlay.style.opacity = 1;
    }, 100);
    var driverInfo = document.createElement('div');
    driverInfo.innerHTML = "\n\t\t<table class=\"driver__table\">\n\t\t\t<tbody>\n\t\t\t\t<tr>\n\t\t\t\t\t<td><small>Team</small></td>\n\t\t\t\t\t<td>" + driverTeam + "</td>\n\t\t\t\t</tr>\n\t\t\t\t<tr>\n\t\t\t\t\t<td><small>Nationality</small></td>\n\t\t\t\t\t<td><img src=\"https://www.countryflags.io/" + driverCountry + "/shiny/24.png\">" + driverNationality + "</td>\n\t\t\t\t</tr>\n\t\t\t\t<tr>\n\t\t\t\t\t<td><small>Date of birth:</small></td>\n\t\t\t\t\t<td>" + driverDOB + "</td>\n\t\t\t\t</tr>\n\t\t\t\t<tr>\n\t\t\t\t\t<td><small>Place</small></td>\n\t\t\t\t\t<td>" + driverPlace + "</td>\n\t\t\t\t</tr>\n\t\t\t\t<tr>\n\t\t\t\t\t<td><small>Points</small></td>\n\t\t\t\t\t<td>" + driverPoints + "</td>\n\t\t\t\t</tr>\n\t\t\t</tbody>\n\t\t</table>";
    driverContent.appendChild(driverInfo);
    newDriver.appendChild(driverContent);
    sidebarBody.appendChild(newDriver);
  });
});
});

</script>
