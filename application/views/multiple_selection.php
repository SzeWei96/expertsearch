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
           <div class="col-lg-7.5 col-md-8 col-sm-7 custom-padding" style="margin-left: -100;width: 930px;">
                <!-- Nav tabs -->
                <div class="tab" style="width:850px;">
                        <button class="tablinks active" style="width: 849px" onclick="change(event,'firstword')">
                          
                                    <?php echo("<b>"."<i>"."Expert(s) who has at least one publication related to : "."<br/>".implode(" & ",$keywords)."</i>"."</b>");?>
                            
                        </button>
                  
                  <!--<?php if(empty($rank_list)):?>
                                    <?php echo ("keyword not found.")?>
                            <?php else :?>
                          <?php for($i = 1; $i < count($rank_list[0]); $i++):?>
                                <button style="width: 300px"><?php foreach($keywords as $words):?>
                                            <?php echo("<b>"."<i>".$words."</i>"."&"."</b>");?>
                                        <?php endforeach ?>
                                      ?></button>
                          <?php endfor;?>
                   <?php endif ?>-->
                </div>
                
                <!-- Tab panes -->
                    <div id="firstword" class="tabcontent" style="display: block;width:850px;padding-top: 20px;">
                        <?php if(empty($rank_list)):?>
                            <?php echo ("No result.")?>
                        <?php else :?>
                        <?php for($i = 0; $i < count($rank_list[0]); $i++):?>
                            <div class="profile" style="
                                    padding-bottom: 0px;
                                    padding-top: 10px;
                                    margin-top: 50px;
                                    margin-left: 80px;
                                    width: 565;
                                    height: 140px;
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
                                <h4 class="profile__username" style="height: 40px;width: 200px;">
                                  <?php echo("Dr. ".$rank_list[0][$i]["Expert_name"]); ?></h4>
                                </div>
                                <div class="profile__edit"><a href="<?php echo base_url().'expert_profile?expert_id='. $rank_list[0][$i]["expert_id"] .'&amp;keyword='.$rank_list[0][$i]["Keyword"].'&amp;pub_ids='.$pub_ids.'&amp;tab=summary';?>" class="profile__button" style="
                                    padding-top: 5px;
                                    padding-bottom: 5px;
                                    padding-left: 0px;
                                    padding-right: 0px;
                                    width: 122px;
                                    margin-left: 30px;
                                "><b>Go to profile</b></a></div>
                                </div>
                                <div class="profile__stats">
                                  <div class="profile__stat">
                                    <div class="profile__icon profile__icon--gold"><i class="material-icons">library_books</i></div>
                                    <div class="profile__value" style="height: 40px;"><?php 
                                                  echo($rank_list[0][$i]["No_publication"]); ?>
                                      <div class="profile__key"><b>No. Publication</b></div>
                                    </div>
                                  </div>
                                  <div class="profile__stat">
                                    <div class="profile__icon profile__icon--maroon"><i class="material-icons">format_quote</i></div>
                                    <div class="profile__value" style="height: 40px;"><?php 
                                                  echo($rank_list[0][$i]["No_citation"]); ?>
                                      <div class="profile__key"><b>No. Citation</b></div>
                                    </div>
                                  </div>
                                  <div class="profile__stat">
                                    <div class="profile__icon"><img style="width:24px;height:22px;" src="https://img.icons8.com/flat_round/64/000000/star--v1.png"/></div>
                                    <div class="profile__value" style="height: 40px;"><?php 
                                                  echo($rank_list[0][$i]["Sum_of_score"]); ?>
                                      <div class="profile__key"><b>Score (0-1)</b></div>
                                    </div>
                                  </div>
                                </div>
                            </div>  
                        <?php endfor;?>
                        <?php endif ?> 
                    </div>            
                        <!--<div class="list-type5">
                            <ol>
                                <?php if(count($rank_list) == 0):?>
                                        <?php echo ("no result.")?>
                                <?php else :?>
                                    <?php for($i = 0; $i < count($rank_list[0]); $i++):?>
                                    <li><a class="remove-uline" href="<?php echo base_url().'expert_profile?expert_id='. $rank_list[0][$i]["expert_id"] .'&amp;keyword='.$rank_list[0][$i]["Keyword"].'&amp'.'&amp;tab=summary';?>"><center><?php 
                                          echo("<b>"."<i>"."Dr. ".$rank_list[0][$i]["Expert_name"]."</i>"."</b>"."<br/>"."Score : ".$rank_list[0][$i]["Sum_of_score"]."<br/>"."Number Of Publication : ".$rank_list[0][$i]["No_publication"]."<br/>"."Number Of Citation : ".$rank_list[0][$i]["No_citation"])."<br/>"."<br/>"; ?></center></a></li>
                                    <?php endfor;?>
                                <?php endif ?>
                            </ol>
                        </div>-->

                        <!--<div class="wrapper">
                                <div class="list">
                                    <div class="list__body">
                                      <table class="list__table">
                                        <?php if(empty($rank_list)):?>
                                            <?php echo ("Expert who published content related to whole keywords combination above not found.")?>
                                        <?php else :?>
                                        <?php for($i = 0; $i < count($rank_list[0]); $i++):?>
                                        <tr class="list__row" data-image="https://www.formula1.com/content/fom-website/en/drivers/lewis-hamilton/_jcr_content/image.img.1920.medium.jpg/1533294345447.jpg" data-nationality="British" data-dob="1985-01-07" data-country="gb">
                                          <td class="list__cell"><span class="list__value"><?php echo $i+1 ?></span></td>
                                          <td class="list__cell"><span class="list__value"><a href="<?php echo base_url().'expert_profile_intersect?expert_id='. $rank_list[0][$i]["expert_id"] .'&amp;pubId='.$pubId.'&amp;keywordsString='.$keywordsString;?>"><?php 
                                              echo("<b>"."<i>"."Dr. ".$rank_list[0][$i]["Expert_name"]."</i>"."</b>"."<br/>")."<br/>"."<br/>"; ?></a></span></td>
                                          <td class="list__cell"><span class="list__value"><?php 
                                              echo("<b>"."<i>".$rank_list[0][$i]["No_publication"]."</i>"."</b>"."<br/>")."<br/>"."<br/>"; ?></span><small class="list__label"><b>No. Publication</b></small></td>
                                          <td class="list__cell"><span class="list__value"><?php 
                                              echo("<b>"."<i>".$rank_list[0][$i]["No_citation"]."</i>"."</b>"."<br/>")."<br/>"."<br/>"; ?></span><small class="list__label"><b>No. Citation</b></small></td>
                                          <td class="list__cell"><span class="list__value"><?php 
                                              echo("<b>"."<i>".$rank_list[0][$i]["Sum_of_score"]."</i>"."</b>"."<br/>")."<br/>"."<br/>"; ?></span><small class="list__label"><b>Score</b></small></td>
                                        </tr>
                                         <?php endfor;?>
                                        <?php endif ?>
                                      </table>
                                    </div>
                                </div>
                            </div>-->
                    
            
                            <!--<div class="list-type5">
                                <ol>
                                    <?php for($j = 0; $j < count($rank_list[$i]); $j++):?>
                                        <?php if(count($rank_list) == 0):?>
                                        <?php echo ("no result.")?>
                                    <?php endif ?>
                                     <li><a class="remove-uline" href="<?php echo base_url().'expert_profile?expert_id='. $rank_list[$i][$j]["expert_id"] .'&amp;keyword='.$rank_list[$i][$j]["Keyword"].'&amp;tab=summary';?>"><center><?php 
                                      echo("<b>"."<i>".$rank_list[$i][$j]["Expert_name"]."</i>"."</b>"."<br/>"."Score : ".$rank_list[$i][$j]["Sum_of_score"]."<br/>"."Number Of Publication : ".$rank_list[$i][$j]["No_publication"]."<br/>"."Number Of Citation : ".$rank_list[$i][$j]["No_citation"])."<br/>"."<br/>"; ?></center></a></li>
                                    <?php endfor;?>
                                </ol>
                            </div>-->
                            <!--<div class="wrapper">
                                <div class="list">
                                    <div class="list__body">
                                      <table class="list__table">
                                        <?php if(empty($rank_list)):?>
                                                <?php echo ("no result.")?>
                                        <?php else:?>
                                        <?php for($j = 0; $j < count($rank_list[$i]); $j++):?>
                                        <tr class="list__row" data-image="https://www.formula1.com/content/fom-website/en/drivers/lewis-hamilton/_jcr_content/image.img.1920.medium.jpg/1533294345447.jpg" data-nationality="British" data-dob="1985-01-07" data-country="gb">
                                          <td class="list__cell"><span class="list__value"><?php echo $j+1 ?></span></td>
                                          <td class="list__cell"><span class="list__value"><a href="<?php echo base_url().'expert_profile_intersect?expert_id='. $rank_list[$i][$j]["expert_id"] .'&amp;keyword='.$rank_list[$i][$j]["Keyword"].'&amp;tab=summary';?>"><?php 
                                              echo("<b>"."<i>"."Dr. ".$rank_list[$i][$j]["Expert_name"]."</i>"."</b>"."<br/>")."<br/>"."<br/>"; ?></a></span></td>
                                          <td class="list__cell"><span class="list__value"><?php 
                                              echo("<b>"."<i>".$rank_list[$i][$j]["No_publication"]."</i>"."</b>"."<br/>")."<br/>"."<br/>"; ?></span><small class="list__label">No. Publication</small></td>
                                          <td class="list__cell"><span class="list__value"><?php 
                                              echo("<b>"."<i>".$rank_list[$i][$j]["No_citation"]."</i>"."</b>"."<br/>")."<br/>"."<br/>"; ?></span><small class="list__label">No. Citation</small></td>
                                          <td class="list__cell"><span class="list__value"><?php 
                                              echo("<b>"."<i>".$rank_list[$i][$j]["Sum_of_score"]."</i>"."</b>"."<br/>")."<br/>"."<br/>"; ?></span><small class="list__label">Score</small></td>
                                        </tr>
                                        <?php endfor;?>
                                    <?php endif;?>
                                      </table>
                                    </div>
                                </div>-->
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
