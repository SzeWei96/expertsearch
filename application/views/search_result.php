<style type="text/css">
.selectMultiple {
  width: 240px;
  position: relative;
}
.selectMultiple select {
  display: none;
}
.selectMultiple > div {
  position: relative;
  z-index: 2;
  padding: 8px 12px 2px 12px;
  border-radius: 8px;
  background: #fff;
  font-size: 14px;
  min-height: 44px;
  box-shadow: 0 4px 16px 0 rgba(22, 42, 90, 0.12);
  -webkit-transition: box-shadow .3s ease;
  transition: box-shadow .3s ease;
}
.selectMultiple > div:hover {
  box-shadow: 0 4px 24px -1px rgba(22, 42, 90, 0.16);
}
.selectMultiple > div .arrow {
  right: 1px;
  top: 0;
  bottom: 0;
  cursor: pointer;
  width: 28px;
  position: absolute;
}
.selectMultiple > div .arrow:before, .selectMultiple > div .arrow:after {
  content: '';
  position: absolute;
  display: block;
  width: 2px;
  height: 8px;
  border-bottom: 8px solid #99A3BA;
  top: 43%;
  -webkit-transition: all .3s ease;
  transition: all .3s ease;
}
.selectMultiple > div .arrow:before {
  right: 12px;
  -webkit-transform: rotate(-130deg);
          transform: rotate(-130deg);
}
.selectMultiple > div .arrow:after {
  left: 9px;
  -webkit-transform: rotate(130deg);
          transform: rotate(130deg);
}
.selectMultiple > div span {
  color: #99A3BA;
  display: block;
  position: absolute;
  left: 12px;
  cursor: pointer;
  top: 8px;
  line-height: 28px;
  -webkit-transition: all .3s ease;
  transition: all .3s ease;
}
.selectMultiple > div span.hide {
  opacity: 0;
  visibility: hidden;
  -webkit-transform: translate(-4px, 0);
          transform: translate(-4px, 0);
}
.selectMultiple > div a {
  position: relative;
  padding: 0 24px 6px 8px;
  line-height: 28px;
  color: #1E2330;
  display: inline-block;
  vertical-align: top;
  margin: 0 6px 0 0;
}
.selectMultiple > div a em {
  font-style: normal;
  display: block;
  white-space: nowrap;
}
.selectMultiple > div a:before {
  content: '';
  left: 0;
  top: 0;
  bottom: 6px;
  width: 100%;
  position: absolute;
  display: block;
  background: rgba(228, 236, 250, 0.7);
  z-index: -1;
  border-radius: 4px;
}
.selectMultiple > div a i {
  cursor: pointer;
  position: absolute;
  top: 0;
  right: 0;
  width: 24px;
  height: 28px;
  display: block;
}
.selectMultiple > div a i:before, .selectMultiple > div a i:after {
  content: '';
  display: block;
  width: 2px;
  height: 10px;
  position: absolute;
  left: 50%;
  top: 50%;
  background: #4D18FF;
  border-radius: 1px;
}
.selectMultiple > div a i:before {
  -webkit-transform: translate(-50%, -50%) rotate(45deg);
          transform: translate(-50%, -50%) rotate(45deg);
}
.selectMultiple > div a i:after {
  -webkit-transform: translate(-50%, -50%) rotate(-45deg);
          transform: translate(-50%, -50%) rotate(-45deg);
}
.selectMultiple > div a.notShown {
  opacity: 0;
  -webkit-transition: opacity .3s ease;
  transition: opacity .3s ease;
}
.selectMultiple > div a.notShown:before {
  width: 28px;
  -webkit-transition: width 0.45s cubic-bezier(0.87, -0.41, 0.19, 1.44) 0.2s;
  transition: width 0.45s cubic-bezier(0.87, -0.41, 0.19, 1.44) 0.2s;
}
.selectMultiple > div a.notShown i {
  opacity: 0;
  -webkit-transition: all .3s ease .3s;
  transition: all .3s ease .3s;
}
.selectMultiple > div a.notShown em {
  opacity: 0;
  -webkit-transform: translate(-6px, 0);
          transform: translate(-6px, 0);
  -webkit-transition: all .4s ease .3s;
  transition: all .4s ease .3s;
}
.selectMultiple > div a.notShown.shown {
  opacity: 1;
}
.selectMultiple > div a.notShown.shown:before {
  width: 100%;
}
.selectMultiple > div a.notShown.shown i {
  opacity: 1;
}
.selectMultiple > div a.notShown.shown em {
  opacity: 1;
  -webkit-transform: translate(0, 0);
          transform: translate(0, 0);
}
.selectMultiple > div a.remove:before {
  width: 28px;
  -webkit-transition: width 0.4s cubic-bezier(0.87, -0.41, 0.19, 1.44) 0s;
  transition: width 0.4s cubic-bezier(0.87, -0.41, 0.19, 1.44) 0s;
}
.selectMultiple > div a.remove i {
  opacity: 0;
  -webkit-transition: all .3s ease 0s;
  transition: all .3s ease 0s;
}
.selectMultiple > div a.remove em {
  opacity: 0;
  -webkit-transform: translate(-12px, 0);
          transform: translate(-12px, 0);
  -webkit-transition: all .4s ease 0s;
  transition: all .4s ease 0s;
}
.selectMultiple > div a.remove.disappear {
  opacity: 0;
  -webkit-transition: opacity .5s ease 0s;
  transition: opacity .5s ease 0s;
}
.selectMultiple > ul {
  margin: 0;
  padding: 0;
  list-style: none;
  font-size: 16px;
  z-index: 1;
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  visibility: hidden;
  opacity: 0;
  border-radius: 8px;
  -webkit-transform: translate(0, 20px) scale(0.8);
          transform: translate(0, 20px) scale(0.8);
  -webkit-transform-origin: 0 0;
          transform-origin: 0 0;
  -webkit-filter: drop-shadow(0 12px 20px rgba(22, 42, 90, 0.08));
          filter: drop-shadow(0 12px 20px rgba(22, 42, 90, 0.08));
  -webkit-transition: all 0.4s ease, -webkit-transform 0.4s cubic-bezier(0.87, -0.41, 0.19, 1.44), -webkit-filter 0.3s ease 0.2s;
  transition: all 0.4s ease, -webkit-transform 0.4s cubic-bezier(0.87, -0.41, 0.19, 1.44), -webkit-filter 0.3s ease 0.2s;
  transition: all 0.4s ease, transform 0.4s cubic-bezier(0.87, -0.41, 0.19, 1.44), filter 0.3s ease 0.2s;
  transition: all 0.4s ease, transform 0.4s cubic-bezier(0.87, -0.41, 0.19, 1.44), filter 0.3s ease 0.2s, -webkit-transform 0.4s cubic-bezier(0.87, -0.41, 0.19, 1.44), -webkit-filter 0.3s ease 0.2s;
}
.selectMultiple > ul li {
  color: #1E2330;
  background: #fff;
  padding: 12px 16px;
  cursor: pointer;
  overflow: hidden;
  position: relative;
  -webkit-transition: background .3s ease, color .3s ease, opacity .5s ease .3s, border-radius .3s ease .3s, -webkit-transform .3s ease .3s;
  transition: background .3s ease, color .3s ease, opacity .5s ease .3s, border-radius .3s ease .3s, -webkit-transform .3s ease .3s;
  transition: background .3s ease, color .3s ease, transform .3s ease .3s, opacity .5s ease .3s, border-radius .3s ease .3s;
  transition: background .3s ease, color .3s ease, transform .3s ease .3s, opacity .5s ease .3s, border-radius .3s ease .3s, -webkit-transform .3s ease .3s;
}
.selectMultiple > ul li:first-child {
  border-radius: 8px 8px 0 0;
}
.selectMultiple > ul li:first-child:last-child {
  border-radius: 8px;
}
.selectMultiple > ul li:last-child {
  border-radius: 0 0 8px 8px;
}
.selectMultiple > ul li:last-child:first-child {
  border-radius: 8px;
}
.selectMultiple > ul li:hover {
  background: #4D18FF;
  color: #fff;
}
.selectMultiple > ul li:after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 6px;
  height: 6px;
  background: rgba(0, 0, 0, 0.4);
  opacity: 0;
  border-radius: 100%;
  -webkit-transform: scale(1, 1) translate(-50%, -50%);
          transform: scale(1, 1) translate(-50%, -50%);
  -webkit-transform-origin: 50% 50%;
          transform-origin: 50% 50%;
}
.selectMultiple > ul li.beforeRemove {
  border-radius: 0 0 8px 8px;
}
.selectMultiple > ul li.beforeRemove:first-child {
  border-radius: 8px;
}
.selectMultiple > ul li.afterRemove {
  border-radius: 8px 8px 0 0;
}
.selectMultiple > ul li.afterRemove:last-child {
  border-radius: 8px;
}
.selectMultiple > ul li.remove {
  -webkit-transform: scale(0);
          transform: scale(0);
  opacity: 0;
}
.selectMultiple > ul li.remove:after {
  -webkit-animation: ripple .4s ease-out;
          animation: ripple .4s ease-out;
}
.selectMultiple > ul li.notShown {
  display: none;
  -webkit-transform: scale(0);
          transform: scale(0);
  opacity: 0;
  -webkit-transition: opacity .4s ease, -webkit-transform .35s ease;
  transition: opacity .4s ease, -webkit-transform .35s ease;
  transition: transform .35s ease, opacity .4s ease;
  transition: transform .35s ease, opacity .4s ease, -webkit-transform .35s ease;
}
.selectMultiple > ul li.notShown.show {
  -webkit-transform: scale(1);
          transform: scale(1);
  opacity: 1;
}
.selectMultiple.open > div {
  box-shadow: 0 4px 20px -1px rgba(22, 42, 90, 0.12);
}
.selectMultiple.open > div .arrow:before {
  -webkit-transform: rotate(-50deg);
          transform: rotate(-50deg);
}
.selectMultiple.open > div .arrow:after {
  -webkit-transform: rotate(50deg);
          transform: rotate(50deg);
}
.selectMultiple.open > ul {
  -webkit-transform: translate(0, 12px) scale(1);
          transform: translate(0, 12px) scale(1);
  opacity: 1;
  visibility: visible;
  -webkit-filter: drop-shadow(0 16px 24px rgba(22, 42, 90, 0.16));
          filter: drop-shadow(0 16px 24px rgba(22, 42, 90, 0.16));
}

body .selectMultiple {
  margin-top: -12%;
}
body .dribbble {
  position: fixed;
  display: block;
  right: 20px;
  bottom: 20px;
  opacity: .5;
  -webkit-transition: all .4s ease;
  transition: all .4s ease;
}
body .dribbble:hover {
  opacity: 1;
}
body .dribbble img {
  display: block;
  height: 36px;
}

.btna {
  box-sizing: border-box;
  appearance: none;
  background-color: transparent;
  border: 2px solid $red;
  border-radius: 0.6em;
  color: $red;
  cursor: pointer;
  display: flex;
  align-self: center;
  font-size: 1rem;
  font-weight: 400;
  line-height: 1;
  margin: 20px;
  padding: 1.2em 2.8em;
  text-decoration: none;
  text-align: center;
  text-transform: uppercase;
  font-family: 'Montserrat', sans-serif;
  font-weight: 700;

  &:hover,
  &:focus {
    color: #fff;
    outline: 0;
  }
}

.third {
  border-color: $blue;
  color: #fff;
  box-shadow: 0 0 40px 40px $blue inset, 0 0 0 0 $blue;
  transition: all 150ms ease-in-out;
  
  &:hover {
    box-shadow: 0 0 10px 0 $blue inset, 0 0 10px 4px $blue;
  }
}

.btn2{
 box-sizing: border-box;
  appearance: none;
  background-color: transparent;
  border: 2px solid $red;
  border-radius: 0.6em;
  color: $red;
  cursor: pointer;
  display: flex;
  align-self: center;
  font-size: 1rem;
  font-weight: 400;
  line-height: 1;
  margin: 20px;
  padding: 1.2em 2.8em;
  text-decoration: none;
  text-align: center;
  text-transform: uppercase;
  font-family: 'Montserrat', sans-serif;
  font-weight: 700;

  &:hover,
  &:focus {
    color: #fff;
    outline: 0;
  }
}

}

</style>
<main class="main">
<div class="container-fluid" style="padding-top:20px">
        <div class="row d-flex justify-content-center">
            <div class="col-md-2 text-center">
                <img src="assets/images/the_recommender.png" alt="USM Computer Science Expert Logo" width="180" height="180" style="margin-top:-30px;">	
            </div>
			     <div class="col-md-10 p-2">
            <form action="<?php echo base_url() . "search_result"; ?>", method="get">
                <div class="input-group input-width col-md-6 col-sm-3" style="max-width:700px; margin-top: 55px;">
                    <input class="form-control py-2 border-right-0" style="border-width: 2px;" type="search" name="q" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search a topic, keyword, short abstract, etc. (Maximum 50 words)" id="search-input" required>
                    <span class="input-group-append">
                        <button class="btn border border-left-0" type="submit" style="height: 36px;margin-top: 0px;">
                        <i class="fa fa-search centre" style="padding-top: 8px;"></i>
                      </button>
                    </span>

                </div>
            </form>	
          </div>

          <div class="col-lg-7.5 col-md-8 col-sm-7 custom-padding" style="margin-left: 50px;margin-right: -150px;">
                <!-- Nav tabs -->
                <div class="tab" style="width:850px;position:relative;">
                        <button class="tablinks active" style="position:relative; /*  100/(5+1)  */font-family: Montserrat, sans-serif;height: 49.8px;" onclick="change(event,'firstword')">
                        	<?php if((count($rank_list) == 0)) :?>
            								  <?php echo ("Keyword(s) not found.")?>
                            <?php elseif($num_input>=80) :?>
                              <?php echo ("Search input exceeds maximum number of words.")?>
              							<?php else :?>
                          		<?php echo("<b>"."<i>".$rank_list[0][0]['Keyword']."</i>");?>
                        	<?php endif ?>
                        </button>
                    
                      <?php for($i = 1; $i < count($rank_list); $i++):?>
                            <button class="tablinks" style="position:relative; /*  100/(5+1)  */; font-family: Montserrat, sans-serif;height: 49.8px;" onclick="change(event,'<?php echo($rank_list[$i][0]['Keyword']
                            ) ?>')" style="width: 180px"><?php 
                                    echo("<b>"."<i>".$rank_list[$i][0]['Keyword']."</i>");
                                  ?></button>
                      <?php endfor;?>
                </div>
                
                <!-- Tab panes -->
                	<div id="firstword" class="tabcontent" style="display: block;width:850px;padding-top: 20px;padding-left: 40px;padding-right: 40px;">
                    <?php if(count($rank_list) == 0):?>
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
                            <div class="profile__picture" style="margin-left: 0px;border-left-width: 8px;"><img src="<?php echo($rank_list[0][$i]["photo"]); ?>" id="expert-image" alt="assets/images/expert.png" style="
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
                            <div class="profile__edit"><a href="<?php echo base_url().'expert_profile?expert_id='. $rank_list[0][$i]["expert_id"] .'&amp;keyword='.$rank_list[0][$i]["Keyword"].'&amp'.'&amp;tab=summary';?>" class="profile__button" style="
                                padding-top: 5px;
                                padding-bottom: 5px;
                                padding-left: 0px;
                                padding-right: 0px;
                                width: 122px;
                                margin-left: 30px;
                            ">Go to profile</a></div>
                            </div>
                            <div class="profile__stats">
                              <div class="profile__stat">
                                <div class="profile__icon profile__icon--gold"><i class="material-icons">library_books</i></i></div>
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
        								      	<?php if(count($rank_list) == 0):?>
        											   <?php echo ("no result.")?>
        										    <?php else :?>
        								      	<?php for($i = 0; $i < count($rank_list[0]); $i++):?>
        								        <tr class="list__row" data-image="https://www.formula1.com/content/fom-website/en/drivers/lewis-hamilton/_jcr_content/image.img.1920.medium.jpg/1533294345447.jpg" data-nationality="British" data-dob="1985-01-07" data-country="gb">
        								          <td class="list__cell"><span class="list__value"><?php echo $i+1 ?></span></td>
        								          <td class="list__cell"><span class="list__value"><a href="<?php echo base_url().'expert_profile?expert_id='. $rank_list[0][$i]["expert_id"] .'&amp;keyword='.$rank_list[0][$i]["Keyword"].'&amp'.'&amp;tab=summary';?>"><?php 
        				                              echo("<b>"."<i>"."Dr. ".$rank_list[0][$i]["Expert_name"]."</i>"."</b>"."<br/>")."<br/>"."<br/>"; ?></a></span></td>
        				                          <td class="list__cell"><span class="list__value"><?php 
        				                              echo("<b>"."<i>".$rank_list[0][$i]["No_publication"]."</i>"."</b>"."<br/>")."<br/>"."<br/>"; ?></span><small class="list__label"><b>No. Publication</b></small></td>
        				                          <td class="list__cell"><span class="list__value"><?php 
        				                              echo("<b>"."<i>".$rank_list[0][$i]["No_citation"]."</i>"."</b>"."<br/>")."<br/>"."<br/>"; ?></span><small class="list__label"><b>No. Citation</b></small></td>
        								          <td class="list__cell" style="width: 80px; text-align: center;"><span class="list__value"><?php 
        				                              echo("<b>"."<i>".$rank_list[0][$i]["Sum_of_score"]."</i>"."</b>"."<br/>")."<br/>"."<br/>"; ?></span><small class="list__label"><b>Score</b></small></td>
        								        </tr>
        								         <?php endfor;?>
        		                    			<?php endif ?>
        								      </table>
        							    	</div>
        							    </div>
        							</div>-->
                    
                    <?php for($i = 1; $i < count($rank_list); $i++):?>
                		<div id="<?php echo($rank_list[$i][0]['Keyword'])?>" class="tabcontent" style=style="display: block;width:850px;padding-top: 20px;padding-left: 40px;padding-right: 40px;">
                      <?php for($j = 0; $j < count($rank_list[$i]); $j++):?>
                          <?php if(count($rank_list) == 0):?>
                            <?php echo ("No result.")?>
                          <?php endif ?>
                        <div class="profile" style="
                                padding-bottom: 0px;
                                padding-top: 10px;
                                margin-top: 50px;
                                margin-left: 80px;
                                width: 565;
                                height: 140px;
                            ">
                            <div class="profile__picture" style="margin-left: 0px;border-left-width: 8px;"><img src="<?php echo($rank_list[$i][$j]["photo"]); ?>" id="expert-image" alt="assets/images/expert.png" style="
                                    height: 90px;
                                    width: 90px;
                                    border-left-width: 20px;
                                    margin-top: 5px;
                                    margin-left: 10px;
                                "></div>
                            <div class="profile__header">
                            <div class="profile__account">
                            <h4 class="profile__username" style="height: 40px;width: 200px;">
                              <?php echo("Dr. ".$rank_list[$i][$j]["Expert_name"]); ?></h4>
                            </div>
                            <div class="profile__edit"><a href="<?php echo base_url().'expert_profile?expert_id='. $rank_list[$i][$j]["expert_id"] .'&amp;keyword='.$rank_list[$i][$j]["Keyword"].'&amp'.'&amp;tab=summary';?>" class="profile__button" style="
                                padding-top: 5px;
                                padding-bottom: 5px;
                                padding-left: 0px;
                                padding-right: 0px;
                                width: 122px;
                                margin-left: 30px;
                            ">Go to profile</a></div>
                            </div>
                            <div class="profile__stats">
                              <div class="profile__stat">
                                <div class="profile__icon profile__icon--gold"><i class="material-icons">library_books</i></div>
                                <div class="profile__value" style="height: 40px;"><?php 
                                              echo($rank_list[$i][$j]["No_publication"]); ?>
                                  <div class="profile__key"><b>No. Publication</b></div>
                                </div>
                              </div>
                              <div class="profile__stat">
                                <div class="profile__icon profile__icon--maroon"><i class="material-icons">format_quote</i></div>
                                <div class="profile__value" style="height: 40px;"><?php 
                                              echo($rank_list[$i][$j]["No_citation"]); ?>
                                  <div class="profile__key"><b>No. Citation</b></div>
                                </div>
                              </div>
                              <div class="profile__stat">
                                <div class="profile__icon"><img style="width:24px;height:22px;" src="https://img.icons8.com/flat_round/64/000000/star--v1.png"/></div>
                                <div class="profile__value" style="height: 40px;"><?php 
                                              echo($rank_list[$i][$j]["Sum_of_score"]); ?>
                                  <div class="profile__key"><b>Score (0-1)</b></div>
                                </div>
                              </div>
                            </div>
                        </div>  
                        <?php endfor;?>           
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
        								      	<?php for($j = 0; $j < count($rank_list[$i]); $j++):?>
        			                        		<?php if(count($rank_list) == 0):?>
        												<?php echo ("no result.")?>
        											<?php endif ?>
        								        <tr class="list__row" data-image="https://www.formula1.com/content/fom-website/en/drivers/lewis-hamilton/_jcr_content/image.img.1920.medium.jpg/1533294345447.jpg" data-nationality="British" data-dob="1985-01-07" data-country="gb">
        								          <td class="list__cell"><span class="list__value"><?php echo $j+1 ?></span></td>
        								          <td class="list__cell"><span class="list__value"><a href="<?php echo base_url().'expert_profile?expert_id='. $rank_list[$i][$j]["expert_id"] .'&amp;keyword='.$rank_list[$i][$j]["Keyword"].'&amp;tab=summary';?>"><?php 
        				                              echo("<b>"."<i>"."Dr. ".$rank_list[$i][$j]["Expert_name"]."</i>"."</b>"."<br/>")."<br/>"."<br/>"; ?></a></span></td>
        				                          <td class="list__cell"><span class="list__value"><?php 
        				                              echo("<b>"."<i>".$rank_list[$i][$j]["No_publication"]."</i>"."</b>"."<br/>")."<br/>"."<br/>"; ?></span><small class="list__label"><b>No. Publication</b></small></td>
        				                          <td class="list__cell"><span class="list__value"><?php 
        				                              echo("<b>"."<i>".$rank_list[$i][$j]["No_citation"]."</i>"."</b>"."<br/>")."<br/>"."<br/>"; ?></span><small class="list__label"><b>No. Citation</b></small></td>
        								          <td class="list__cell"><span class="list__value"><?php 
        				                              echo("<b>"."<i>".$rank_list[$i][$j]["Sum_of_score"]."</i>"."</b>"."<br/>")."<br/>"."<br/>"; ?></span><small class="list__label"><b>Score</b></small></td>
        								        </tr>
        								        <?php endfor;?>
        								      </table>
    							         </div>
    							       </div>
							      </div>-->
                    </div>
                  <?php endfor;?>
         </div>
         <aside class="col-md-3">
          <div class="card" style="width: 380px; margin-top: 15px; margin-left: 50px;">
            <article class="filter-group">
              <header class="card-header" style="background-color:#e9ecef8a;">
                <h6 class="title" style="color:black; font-family: Montserrat, sans-serif;"><b>Advanced Search</b></h6>
              </header>
                <div class="card-body col-lg-3 col-md-3 col-sm-4 sticky-position" style="width: 300px; margin-top: 0px;">
                  <div class="filter-content">
                    <div style="width: 250px;">
                    <h6 class="title" style="width: 300px;">
                      <a class="dropdown-toggle collapsed" data-toggle="collapse" data-target="#collapse_1" aria-expanded="false" style="color:black;font-size:15px;font-family: Montserrat, sans-serif;"><b>SEARCH EXPERT(S) BY</b><br/><b>MULTIPLE KEYWORDS</b></a>
                    </h6>
                  </div>
                    <div class="filter-content collapse" style="width: 300px;" id="collapse_1">
                        <a>Select keywords to search expert(s) who have at least one publication related to multiple keywords.</a>
                        <div style="margin-top: 50px;">
                        <select input multiple data-placeholder="Select keywords">
                          <?php foreach($ext_keyword as $value): ?>
                            <option><?php echo $value ?></option>
                          <?php endforeach?>
                        </select>
                      </div>
                        <button2 type="button" class="btn2 btna third" title="Search experts who published content related to whole keywords combination you selected." style="width: 132px;height: 44px;margin-top: 25px;margin-left: 0px;padding-top: 8px;padding-left: 15px;background-color: darkslategray  ;font: bold 15px/30px Avantgarde, TeX Gyre Adventor, URW Gothic L, sans-serif;width: 152px;padding-right: 5px;margin-bottom: 10px;">View expert(s)</button2>
                        <a class="dribbble" href="https://dribbble.com/shots/5112850-Multiple-select-animation-field" target="_blank"><img src="" alt=" "></a>
                    </div>
                </div> <!-- card-body.// -->
                <div class="filter-content" style="margin-top: 20px;">
                    <h6 class="title" style="width: 300px;">
                      <a style="width: 300px; color:black; font-size:15px; font-family: Montserrat, sans-serif;" class="dropdown-toggle collapsed" data-toggle="collapse" data-target="#collapse_2" aria-expanded="false"><b>SEARCH EXPERT(S) INTERSECT WITH</b><br/> <b>MULTIPLE RECOMMENDATION LISTS</b></a>
                    </h6>
                    <div class="filter-content collapse" style="width: 300px;" id="collapse_2">
                        <a>Select keywords to check existance of expert(s) in multiple expert recommendation lists.</a>
                        <div style="margin-top: 50px;">
                          <select input multiple2 data-placeholder="Select keywords">
                            <?php foreach($ext_keyword as $value): ?>
                              <option><?php echo $value ?></option>
                            <?php endforeach?>
                          </select>
                        </div>
                        <button3 type="button" class="button btna third" title="Search experts who are intersect between the expert recommended list related to keywords you selected." style="width: 132px;height: 44px;margin-top: 25px;margin-left: 0px;padding-top: 8px;padding-left: 15px;background-color: darkslategray  ;font: bold 15px/30px Avantgarde, TeX Gyre Adventor, URW Gothic L, sans-serif;width: 152px;padding-right: 5px;margin-bottom: 20px;">View expert(s)</button3>
                    </div>
                </div>
              </div>
            </article>
          </div>  <!-- card .// -->
        </aside>
         
    </div>
</div>


</main>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type='text/javascript'>
  $(document).ready(function(){
    if (window.performance && window.performance.navigation.type == window.performance.navigation.TYPE_BACK_FORWARD) {
      location.reload(true);
    }
  });
// JS for thumbnail presentation only
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

$(document).ready(function() {

    var select = $('select[multiple]');
    var options = select.find('option');

    var div = $('<div />').addClass('selectMultiple');
    var active = $('<div />');
    var list = $('<ul />');
    var placeholder = select.data('placeholder');
    var span = $('<span />').text(placeholder).appendTo(active);
    
    options.each(function() {
        var text = $(this).text();
        //active.removeClass().addClass('remove');
      //list.append($('<li />').html(text));
        if($(this).is(':selected')) {
            active.append($('<a />').html('<em>' + text + '</em><i></i>'));
            span.addClass('hide');
        }else{
          list.append($('<li />').html(text));
        }
    });

    active.append($('<div />').addClass('arrow'));
    div.append(active).append(list);

    select.wrap(div);
    //var array = localStorage.getItem("keywordsArray");
    //console.log(array);
    var array = [];
    $(document).on('click', '.selectMultiple ul li', function(e) {
        var select = $(this).parent().parent();
        var li = $(this);
        
        if(!select.hasClass('clicked')) {
            select.addClass('clicked');
            li.prev().addClass('beforeRemove');
            li.next().addClass('afterRemove');
            li.addClass('remove');
            var a = $('<a />').addClass('notShown').html('<em>' + li.text() + '</em><i></i>').hide().appendTo(select.children('div'));
            a.slideDown(400, function() {
                setTimeout(function() {
                    a.addClass('shown');
                    select.children('div').children('span').addClass('hide');
                    select.find('option:contains(' + li.text() + ')').prop('selected', true);
                    //console.log(li.text().prop());
                }, 500);
            });

            var x = select.find('option:contains(' + li.text() + ')').prop('selected', true);
             array.push(x.prop('selected',true).val());
             //localStorage.setItem("keywordsArray", array);
             console.log(array);
            setTimeout(function() {
                if(li.prev().is(':last-child')) {
                    li.prev().removeClass('beforeRemove');
                }
                if(li.next().is(':first-child')) {
                    li.next().removeClass('afterRemove');
                }
                setTimeout(function() {
                    li.prev().removeClass('beforeRemove');
                    li.next().removeClass('afterRemove');
                }, 200);

                li.slideUp(400, function() {
                    li.remove();
                    select.removeClass('clicked');
                });
            }, 600);
        }
     
    });
    
    $(document).on('click', '.selectMultiple > div a', function(e) {
        var select = $(this).parent().parent();
        var self = $(this);
        self.removeClass().addClass('remove');
        select.addClass('open');
        setTimeout(function() {
            self.addClass('disappear');
            setTimeout(function() {
                self.animate({
                    width: 0,
                    height: 0,
                    padding: 0,
                    margin: 0
                }, 300, function() {
                    var li = $('<li />').text(self.children('em').text()).addClass('notShown').appendTo(select.find('ul'));
                    li.slideDown(400, function() {
                        li.addClass('show');
                        setTimeout(function() {
                            var y = select.find('option:contains(' + self.children('em').text() + ')').prop('selected', false);
                            array.pop(y.val());
                            console.log(array);
                            if(!select.find('option:selected').length) {
                                select.children('div').children('span').removeClass('hide');
                            }
                            li.removeClass();
                            
                        }, 400);

                    });
                    self.remove();
                })
            }, 300);
        }, 400);
    });

    $(document).on('click','button2', function(e){
      //alert("button was clicked");
      var keywordsArray = array;
      var stringKeywords = keywordsArray.join();
      console.log(keywordsArray);
      console.log(stringKeywords);
      //var keywords = JSON.stringify(keywordsArray);
      //console.log(keywords);
      if((keywordsArray.length)<=1){
        alert('At least 2 keywords must be selected');
      }
      else if((keywordsArray.length)>5){
        alert('At most 5 keywords can be selected');
      }
      else{
        $.ajax({
                    url:"<?php echo base_url(); ?>Multiple_Selection/getKeywords",
                    type: 'POST',
                    data: {mydata: stringKeywords},
                    success: function(data)
              {
                console.log(data);
                  alert('success');
                  window.location.href = "<?php echo base_url(); ?>multiple_selection?word="+data;
                  
              },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert("jqXHR:" + jqXHR.status + " errorThrown: " + errorThrown + textStatus);
                },
                
                });
      }
    });

    $(document).on('click', '.selectMultiple > div .arrow, .selectMultiple > div span', function(e) {
        $(this).parent().parent().toggleClass('open');
    });

});

$(document).ready(function() {

    var select = $('select[multiple2]');
    var options = select.find('option');

    var div = $('<div />').addClass('selectMultiple2');
    var active = $('<div />');
    var list = $('<ul />');
    var placeholder = select.data('placeholder');
    var span = $('<span />').text(placeholder).appendTo(active);
    
    options.each(function() {
        var text = $(this).text();
        //active.removeClass().addClass('remove');
      list.append($('<li />').html(text));
    });

    active.append($('<div />').addClass('arrow'));
    div.append(active).append(list);

    select.wrap(div);
    //var array = localStorage.getItem("keywordsArray");
    //console.log(array);
    var array = [];
    $(document).on('click', '.selectMultiple2 ul li', function(e) {
        var select = $(this).parent().parent();
        var li = $(this);
        
        if(!select.hasClass('clicked')) {
            select.addClass('clicked');
            li.prev().addClass('beforeRemove');
            li.next().addClass('afterRemove');
            li.addClass('remove');
            var a = $('<a />').addClass('notShown').html('<em>' + li.text() + '</em><i></i>').hide().appendTo(select.children('div'));
            a.slideDown(400, function() {
                setTimeout(function() {
                    a.addClass('shown');
                    select.children('div').children('span').addClass('hide');
                    select.find('option:contains(' + li.text() + ')').prop('selected', true);
                    //console.log(li.text().prop());
                }, 500);
            });

            var x = select.find('option:contains(' + li.text() + ')').prop('selected', true);
             array.push(x.prop('selected',true).val());
             //localStorage.setItem("keywordsArray", array);
             console.log(array);
            setTimeout(function() {
                if(li.prev().is(':last-child')) {
                    li.prev().removeClass('beforeRemove');
                }
                if(li.next().is(':first-child')) {
                    li.next().removeClass('afterRemove');
                }
                setTimeout(function() {
                    li.prev().removeClass('beforeRemove');
                    li.next().removeClass('afterRemove');
                }, 200);

                li.slideUp(400, function() {
                    li.remove();
                    select.removeClass('clicked');
                });
            }, 600);
        }
     
    });
    
    $(document).on('click', '.selectMultiple2 > div a', function(e) {
        var select = $(this).parent().parent();
        var self = $(this);
        self.removeClass().addClass('remove');
        select.addClass('open');
        setTimeout(function() {
            self.addClass('disappear');
            setTimeout(function() {
                self.animate({
                    width: 0,
                    height: 0,
                    padding: 0,
                    margin: 0
                }, 300, function() {
                    var li = $('<li />').text(self.children('em').text()).addClass('notShown').appendTo(select.find('ul'));
                    li.slideDown(400, function() {
                        li.addClass('show');
                        setTimeout(function() {
                            var y = select.find('option:contains(' + self.children('em').text() + ')').prop('selected', false);
                            array.pop(y.val());
                            console.log(array);
                            if(!select.find('option:selected').length) {
                                select.children('div').children('span').removeClass('hide');
                            }
                            li.removeClass();
                            
                        }, 400);

                    });
                    self.remove();
                })
            }, 300);
        }, 400);
    });

  $(document).on('click','button3', function(e){
      //alert("button was clicked");
      var keywordsArray = array;
      var stringKeywords = keywordsArray.join();
      console.log(keywordsArray);
      console.log(stringKeywords);
      //var keywords = JSON.stringify(keywordsArray);
      //console.log(keywords);
      if((keywordsArray.length)<=1){
        alert('At least 2 keywords must be selected');
      }
      else if((keywordsArray.length)>5){
        alert('At most 5 keywords can be selected');
      }
      else{
        $.ajax({
                    url:"<?php echo base_url(); ?>Keywords_Intersect/getKeywords",
                    type: 'POST',
                    data: {mydata: stringKeywords},
                    success: function(data)
              {
                console.log(data);
                  alert('success');
                  window.location.href = "<?php echo base_url(); ?>keywords_intersect?word="+data;
                  
              },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert("jqXHR:" + jqXHR.status + " errorThrown: " + errorThrown + textStatus);
                },
                
                });
      }
  });

  $(document).on('click', '.selectMultiple2 > div .arrow, .selectMultiple2 > div span', function(e) {
        $(this).parent().parent().toggleClass('open');
    });
});

</script>