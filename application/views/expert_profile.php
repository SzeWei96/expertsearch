<div class="container-fluid " style="padding-top:30px"> 
    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-4 sticky-position">
            <h3 class="expert-name"><?php echo ucwords(strtolower($exp_name)). ", " . $expert_honorific;?></h3>
            <!-- <img src="assets/images/gans.jpg" class="avatar border border-light rounded-circle" id="expert-image" onerror="standby()"> -->
            <img src="<?php echo $exp_photo; ?>" class="avatar border border-light rounded-circle" id="expert-image" onerror="standby()">
            <p class="text-center"><?php echo $exp_faculty . ", " . $exp_uni; ?></p>
			<?php if(!empty($expert_predicts)): ?>
                <?php foreach($expert_predicts as $expert_predict) : ?>
                    <?php if($expert_predict['classifiers']['visibility'] == 1): ?>
                    <table style="width: inherit;">
                        <tr>
                            <th><?php echo $expert_predict['classifiers']['display_name']; ?></th>
                        </tr>
                        <?php $x = 0; ?>
                        <?php foreach ($expert_predict['categories'] as $categories => $probability): ?>
                            <?php if ($x > 2): ?>
                                <?php break; ?>
                            <?php endif; ?>
                            <?php if (round($probability*100,2) > 0.00): ?>
                                <?php if (round($probability*100,2) == 100): ?>
                                    <tr>
                                        <td class="cat-width"><?php echo ucwords($categories); ?></td>
                                    </tr>
                                <?php else : ?>
                                    <tr>
                                        <td class="cat-width"><?php echo ucwords($categories); ?></td>
                                        <!-- <td><?php printf(" : %.2f %%", round($probability*100,2)); ?></td> -->
                                    </tr>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php $x++; ?>
                        <?php endforeach; ?>
                    </table>
                    <br>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="col-lg-9 col-md-9 col-sm-8">
            <!-- Nav tabs -->

            <div class="container jumbotron text-center" style="background-color: #dc3545"><h1><?php echo("Keyword : ". $Keyword) ?></h1></div>
            <!-- Tab panes -->
            <div class="tab-content">                
                <!-- Summary Tab -->
                <!--<?php
                    //if($_GET['tab']=="summary"){
                ?> -->
               
                
                <div id="summary_tab" class="container tab-pane active"><br>
                    <!-- using row  -->
                    <div class="row" id="custom-padding">
                        <div class="col-xl-4 col-md-6 mb-3">
                            <div class="card">
                              <div class="card-header card-header-icon card-header-warning">
                                <div class="card-icon">
                                  <i class="material-icons">library_books</i>
                                </div>
                              </div>
                              <div class="card-body">
                                  <h4 class="card-title">Total Related Publication</h4>
                                  <h2 class="c-single-number"><?php echo $exp_tot_related_pub; ?></h2>
                              </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 mb-3">
                            <div class="card">
                              <div class="card-header card-header-icon card-header-info">
                                <div class="card-icon">
                                  <i class="material-icons">format_quote</i>
                                </div>
                              </div>
                              <div class="card-body">
                                  <h4 class="card-title">No. of Citation</h4>
                                  <h2 class="c-single-number"><?php echo $exp_tot_related_cite; ?></h2>
                              </div>
                            </div> 
                        </div>
                        <div class="col-xl-4 col-md-6 mb-3"> 
                            <div class="card">
                              <div class="card-header card-header-icon card-header-primary">
                                <div class="card-icon">
                                  <i class="material-icons">people_outline</i>
                                </div>
                              </div>
                              <div class="card-body">
                                  <h4 class="card-title">Total Related Co-Authors</h4>
                                  <h2 class="c-single-number"><?php echo $no_co_authors; ?></h2>
                              </div>
                            </div> 
                            
                        </div>
                    </div>
                    <div id="container-fluid">
                        <div class="row" id="custom-padding">
                            <div class="col-lg-6 col-md-12">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <img class="chart-icon" src="https://img.icons8.com/color/48/000000/queue.png">
                                        <b>Related Co-Authors</b>
                                    </div>
                                    <div class="card-body" id="custom-margin" style="height: 500px">
                                    <div class="list-type1">
                                      <ol>
                                        <?php if(count($co_authors) == 0):?>
                                            <?php echo ("No co-authors for this keyword.")?>
                                        <?php else :?>
                                             <?php for($i=0; $i<count($co_authors); $i++) : ?>
                                                <?php if(empty($pub_ids)) : ?>
                                                    <li><a href="<?php echo base_url().'expert_profile?expert_id='. $co_authors[$i]["expert_id"] .'&amp;keyword='.$co_authors[$i]["Keyword"].'&amp'.'&amp;tab=summary';?>"><?php echo("Dr. ".$co_authors[$i]["expert_name"]); ?></a></li>
                                                <?php else : ?>
                                                    <li><a href="<?php echo base_url().'expert_profile?expert_id='. $co_authors[$i]["expert_id"] .'&amp;keyword='.$co_authors[$i]["Keyword"].'&amp;pub_ids='.$pub_ids.'&amp;tab=summary';?>"><?php echo("Dr. ".$co_authors[$i]["expert_name"]); ?></a></li>
                                                <?php endif;?>
                                            <?php endfor;?>
                                        <?php endif ?>
                                      </ol>
                                    </div>
                                    </div>
                                </div>                         
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <img class="chart-icon rotate-img" src="https://img.icons8.com/color/48/000000/doughnut-chart--v1.png">
                                        <b>Publication Types</b>
                                    </div>
                                    <div class="card-body" id="custom-margin" style="height: 500px">
                                    <div id="chartdiv2"></div>
                                    </div>
                                </div>                         
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <!-- <i class="fab fa-wordpress-simple"></i> -->
                                        <img class="chart-icon" src="https://img.icons8.com/color/48/000000/abc.png">
                                        <b>Related Publication Keywords</b>
                                    </div>
                                    <div class="card-body" id="custom-margin">
                                        <div id="chartdiv">
                                        </div>
                                    </div>
                                </div>
                            </div>        
                        </div>                           
                    </div>
                           
                    <!-- Publication Summary -->
                    <div id="container-fluid">
                        <div class="card mb-3">
                            <div class="card-header">
                                <!-- <i class="fa fa-table"></i> -->
                                <img class="chart-icon" src="https://img.icons8.com/color/48/000000/news.png">
                                <b>Related Publications</b>
                            </div>
                             <div class="card-body">
                                <?php if(empty($pubs)): ?>
                                    <?php echo "No Record(s) Found"; ?>
                                <?php elseif(isset($pubs)): ?>
                                   <table id="example" class="table table-striped table-bordered" style="width:100%;">
                                         <thead>
                                            <tr style="border: 5px;">
                                                <th class="custom-th">No.</th>
                                                <th class="custom-th">Title</th>
                                                <th class="custom-th">Year</th>
                                                <th class="custom-th">Publication Type</th>
                                                <th class="custom-th">Cited By</th>
                                                <th class="custom-th">Abstract</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php for($i=0; $i<count($pubs); $i++) : ?>
                                            <tr>
                                                <td><?php echo $i+1 ?></td>
                                                <td><a href="<?php echo $pubs[$i]["link"];?>"><?php echo($pubs[$i]["title"]); ?></a></td>
                                                <td><?php echo($pubs[$i]["year"]); ?></td>
                                                <td><?php echo($pubs[$i]["document_type"]); ?></td>
                                                <td><?php echo($pubs[$i]["cited_by"]); ?></td>
                                                <td><?php echo($pubs[$i]["abstract"]); ?></td>
                                            </tr>
                                             <?php endfor ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                            <?php echo "<br/>" ?>
                            <?php echo "<br/>" ?>
                            <?php echo "<br/>" ?>
                            <?php echo "<br/>" ?>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </div>
</div>

<!-- </main> -->
<style>
#chartdiv {
  width: 100%;
  height: 500px;
}

#chartdiv2 {
  width: 100%;
  height: 400px;
}
</style>

<!--   Core JS Files   -->
<script>
    $(document).ready(function() {
        var table = $('#example').DataTable( {
        "scrollY":        "500px",
        "border": "10px", 
        "scrollCollapse": true,
        "paging":         false
    } );

    } );


    function standby() {
        document.getElementById('expert-image').src ="assets/images/expert.png";
    }

    var coll = document.getElementsByClassName("collapsible");
    var i;

    for (i = 0; i < coll.length; i++) {
      coll[i].addEventListener("click", function() {
        this.classList.toggle("active2");
        var content = document.getElementsByClassName("content");
        content.style.maxHeight = 0;
        console.log(content.style.maxHeight); 
        if (content.style.maxHeight){
          content.style.maxHeight = null;
        } else {
          content.style.maxHeight = content.scrollHeight + "px";
        } 
      });
    }

    
    am4core.ready(function() {

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        var chart = am4core.create("chartdiv", am4plugins_wordCloud.WordCloud);
        chart.fontFamily = "Segoe UI";
        var series = chart.series.push(new am4plugins_wordCloud.WordCloudSeries());
        series.randomness = 0.1;
        series.rotationThreshold = 0.1;
        series.data = [];
        

        var a = <?php echo json_encode(array_unique($merge_keywords_array)); ?>;
        var b = <?php echo json_encode($frequency_count); ?>;
        //console.log(a[i]);
        for(var i=0; i<<?php echo count(array_unique($merge_keywords_array)); ?>;i++){
            console.log(a[i]);
            series.data[i] = {"tag" :a[i],"count" :b[a[i]]};
        }

        if(a.length == undefined){
            series.dataFields.word = "tag";
            series.dataFields.value = "count";

            series.colors = new am4core.ColorSet();
            series.colors.passOptions = {};
            series.labels.template.propertyFields.fill = "color";
        }
        else if(a.length != undefined){
            series.dataFields.word = "tag";
            series.dataFields.value = "count";

            series.colors = new am4core.ColorSet();
            series.colors.passOptions = {};
            series.labels.template.propertyFields.fill = "color";
                
        }
        else if(document.getElementById("chartdiv")){
            
                document.getElementById("chartdiv").innerHTML = "No Words Available";
        }

        series.events.on("arrangestarted", function(ev) {
          ev.target.baseSprite.preloader.show(0);
        });

        series.events.on("arrangeprogress", function(ev) {
          ev.target.baseSprite.preloader.progress = ev.progress;
        });
        series.labels.template.url = "https://en.wikipedia.org/wiki/{word}";
        series.labels.template.urlTarget = "_blank";
        series.labels.template.tooltipText = "{word}: {value}";

        var hoverState = series.labels.template.states.create("hover");
        hoverState.properties.fill = am4core.color("#FF0000");

    }); // end am4core.ready()

    am4core.ready(function() {

        // Themes begin
        am4core.useTheme(am4themes_animated);

        // Create chart
        var chart = am4core.create("chartdiv2", am4plugins_forceDirected.ForceDirectedTree);

        // Create series
        var series = chart.series.push(new am4plugins_forceDirected.ForceDirectedSeries());

        series.data = [];
        
        var a = <?php echo json_encode(($pub_categories['cat'])); ?>;
        var b =  <?php echo json_encode(($pub_categories['no'])); ?>;
        //var b = <?php //echo json_encode($frequency_count); ?>;
        //console.log(a[i]);
        //for(var i=0; i<<?php //echo count(array_unique($merge_keywords_array)); ?>;i++){
            //word_cloud_summ_config.graphset[0].options.words[i] = {text: a[i],count:b[a[i]]};
        //}
        for(var i = 0; i<<?php echo count($pub_categories['cat']); ?>; i++){
            //console.log(a[i]);
          if(a[i] == "Article"){
            series.data[i] = {name: a[i], value:b[i], image:"https://img.icons8.com/dusk/64/000000/hot-article.png"};
          }
          else if(a[i] == "Article in Press"){
            series.data[i] = {name: a[i], value:b[i], image:"https://img.icons8.com/cute-clipart/64/000000/news.png"};
          }
          else if(a[i] == "Conference Paper"){
            series.data[i] = {name: a[i], value:b[i], image:"https://img.icons8.com/cute-clipart/64/000000/paper.png"};
          }
          else if(a[i] == "Book"){
            series.data[i] = {name: a[i], value:b[i], image:"https://img.icons8.com/cute-clipart/64/000000/book.png"};
          }
          else if(a[i] == "Book Chapter"){
            series.data[i] = {name: a[i], value:b[i], image:"https://img.icons8.com/flat_round/64/000000/bookmark-book.png"};
          }
          else if(a[i] == "Editorial"){
            series.data[i] = {name: a[i], value:b[i], image:"https://img.icons8.com/ultraviolet/40/000000/versions.png"};
          }
          else if(a[i] == "Review"){
            series.data[i] = {name: a[i], value:b[i], image:"https://img.icons8.com/ultraviolet/40/000000/document.png"};
          }
          
        }


        if(<?php echo count($pub_categories['cat']); ?> != 0){
            // Add labels
            series.nodes.template.label.text = "{name}";
            series.nodes.template.label.valign = "bottom";
            series.nodes.template.label.fill = am4core.color("#000");
            series.nodes.template.label.dy = 10;
            series.nodes.template.tooltipText = "{name}: [bold]{value}[/]";
            series.fontSize = 10;
            series.minRadius = 60;
            series.maxRadius = 60;

            // Configure circles
            series.nodes.template.circle.fillOpacity = 0.5;

            // Configure icons
            var icon = series.nodes.template.createChild(am4core.Image);
            icon.propertyFields.href = "image";
            icon.horizontalCenter = "middle";
            icon.verticalCenter = "middle";
            icon.width = 50;
            icon.height = 50;
        }else{
            if(document.getElementById("chartdiv2")){
                document.getElementById("chartdiv2").innerHTML = "No result found.";
            }
        }
        
    });

</script>