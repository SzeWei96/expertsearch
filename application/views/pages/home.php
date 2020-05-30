<!-- <h3> This is <b><?= $title ?></b> Page.</h3>
<p>Welcome to CI blog Application</p> -->

	<div class="container-fluid school-wrapper">
	<div class="row d-flex justify-content-center custom padding" style="
    padding-bottom: 0px;
    height: 290px;
">
		<img src="assets/images/the_recommender.png" alt="USM Computer Science Expert Logo" width="250" height="250">	
	</div>
	<div class="row justify-content-center">
		<form class="form-width needs-validation" action="<?php echo base_url() . "search_result"; ?>", method="get" novalidate>
			<div class="input-group input-width col-md-6 col-sm-3" style="max-width:700px;">
				<input class="form-control py-2 border-right-0" style="border-width: 2px;" type="search" name="q" placeholder="Search a topic, keyword, short abstract, etc. (Maximum 80 words)" id="search-input" required>
				<span class="input-group-append">
					<button class="btn border border-left-0" type="submit" style="height: 36px;margin-top: 0px;">
						<i class="fa fa-search centre" style="padding-top: 8px;"></i>
					</button>
				</span>
			</div>
			<div class="col-md-6 col-sm-4" ><p style = " width: 500px;margin-bottom: 0px;"><i>Experts of School of Computer Science USM</i></p></div>
		</form>	
	</div>	
	
	</div>
