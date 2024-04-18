<?php
/*
 * (guest.php)
 * Designed by Raymond E. Calore
 * Version 2.9
 * Last update 7/14/2017
 * (c) 2017 Blue Box Firewall
 * (c) 2017 BCI Computers
*/

require_once('settings.php');
session_start();
if(isset($_SESSION['back_option'])){
		unset($_SESSION['back_option']);
	}
require_once('head.inc');
require_once("shaper.inc");

// print_r($config['dnshaper']);
// exit;
	//Set Username = "Guest" For Guest User

	echo LOOPBACK_IP.APP_ENDPOINTS.'edit_user_policy.jsp';
	$user_name = GUEST_NAME;
	//Get Guest User Details 
	$ch = curl_init(); 
	curl_setopt($ch,CURLOPT_URL,LOOPBACK_IP.APP_ENDPOINTS.'userdata_by_uname.jsp');
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_HEADER, false); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, "username=" . $user_name);
	$user_data =curl_exec($ch);
	$user_data=json_decode($user_data);
	
	$policyId = $user_data->policy_id;
	$policyname= $user_data->policy_name;
	$policyarray= array('Full','High','Medium','Low',$user_name);
	if(in_array($policyname, $policyarray)){
		$policyname = $policyname;
	} else {
		$policyname = "Full";
	}

	//Check Policy is custom or not:If Not Then Create Guest Name Custom Policy
	function checkpolicy_list($user_name){
		$ch = curl_init(); 
		curl_setopt($ch,CURLOPT_URL,LOOPBACK_IP.APP_ENDPOINTS.'check_policylist.jsp');
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_HEADER, false); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, "custompolicyname=" . $user_name);
		$output=curl_exec($ch);
		curl_close($ch);

		if($output <= 0){
			$a = create_policy($user_name);
			if($a){
				$output = checkpolicy_list($user_name);
			}
		}
		
		
		return $output;
	}
	//Create Guest Policy and Assign Id to User
	function create_policy($user_name){
		$ch = curl_init(); 
		curl_setopt($ch,CURLOPT_URL,LOOPBACK_IP.APP_ENDPOINTS.'create_policy.jsp');
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_HEADER, false); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, "custompolicyname=" . $user_name);
		$output=curl_exec($ch);
		curl_close($ch);
		return $output;
	}
	
	//Edit screen A (List of custom Guest Policy)
	$output12=checkpolicy_list($user_name);

	//Edit screen A (List of custom)
	$ch = curl_init(); 
	curl_setopt($ch,CURLOPT_URL,LOOPBACK_IP.APP_ENDPOINTS.'policy_list.jsp');
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_HEADER, false); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, "policyId=" . $output12);
	$output=curl_exec($ch);
	curl_close($ch);
	$custom_policy_data=json_decode($output);
	$custom_policy_list = $custom_policy_data->data;
	
	//Get categories of Low policy
	$ch = curl_init(); 
	curl_setopt($ch,CURLOPT_URL,LOOPBACK_IP.APP_ENDPOINTS.'policy_list.jsp');
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_HEADER, false); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, "policyId=" . LOW_ID);
	$output=curl_exec($ch);
	curl_close($ch);
	$Low_policy_list=json_decode($output);
	$Low_policy_list = $Low_policy_list->data;

	//Get categories of Medium policy
	$ch = curl_init(); 
	curl_setopt($ch,CURLOPT_URL,LOOPBACK_IP.APP_ENDPOINTS.'policy_list.jsp');
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_HEADER, false); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, "policyId=" . MEDIUM_ID);
	$output=curl_exec($ch);
	curl_close($ch);
	$Medium_policy_list=json_decode($output);
	$Medium_policy_list = $Medium_policy_list->data;

	//Get categories of High policy
	$ch = curl_init(); 
	curl_setopt($ch,CURLOPT_URL,LOOPBACK_IP.APP_ENDPOINTS.'policy_list.jsp');
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_HEADER, false); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, "policyId=" . HIGH_ID);
	$output=curl_exec($ch);
	curl_close($ch);
	$High_policy_list=json_decode($output);
	$High_policy_list = $High_policy_list->data;

	//Get categories of Full policy
	$ch = curl_init(); 
	curl_setopt($ch,CURLOPT_URL,LOOPBACK_IP.APP_ENDPOINTS.'policy_list.jsp');
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_HEADER, false); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, "policyId=" . FULL_ID);
	$output=curl_exec($ch);
	curl_close($ch);
	$Full_policy_list=json_decode($output);
	$Full_policy_list = $Full_policy_list->data;

	//Get Guest details by UserID
	$userId = GUEST_USER_ID;
	$ch = curl_init(); 
	curl_setopt($ch,CURLOPT_URL,LOOPBACK_IP.APP_ENDPOINTS.'user_details.jsp');
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_HEADER, false); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, "userId=" . $userId);
	$output=curl_exec($ch);
	curl_close($ch);

	$result =str_replace('}{', '},{', $output);
	$user_data=json_decode("[" . $result . "]")[0]; 


//Update Guest User Details

if(isset($_POST['done'])){
			$policyArrayList = array(
				LOW_ID => "blockCategoryArr_" . LOW_ID,
				MEDIUM_ID => "blockCategoryArr_" . MEDIUM_ID,
				HIGH_ID => "blockCategoryArr_" . HIGH_ID,
				FULL_ID => "blockCategoryArr_" . FULL_ID
			);
			$user_id = $_POST['user_id'];
			$start_hour = $_POST['start_hour'];
			$start_minute = $_POST['start_minute'];
			$end_hour = $_POST['end_hour'];
			$password = $_POST['password'];
			$safe_mode = $_POST['safeMode'];
			
			$end_minute = $_POST['end_minute'];
			$blockarray = json_encode($_POST[$policyArrayList[$policyId]]);
			$policyId = $_POST['webFilter'];
			if(!is_numeric($policyId)){
				$policyId = checkpolicy_list($user_name);
				$blockarray = json_encode($_POST['blockCategoryArr']);
			}

			
			$ftPolicyId = $_POST['ftPolicyId'];
			$token = $_POST['token'];

			


			//Edit User Policy 
			$ch = curl_init(); 
			curl_setopt($ch,CURLOPT_URL,LOOPBACK_IP.APP_ENDPOINTS.'edit_user_policy.jsp');
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($ch,CURLOPT_HEADER, false);
			$data='user_id='.$user_id.'&policyId='.$policyId.'&ftPolicyId='.$ftPolicyId.'&token='.$token.'&blockCategoryArr='.$blockarray.'&start_hour='.$start_hour.'&start_minute='.$start_minute.'&end_hour='.$end_hour.'&end_minute='.$end_minute.'&safeMode='.$safe_mode;

			// if(count($_POST['blockCategoryArr'])>0 && !is_numeric($_POST['webFilter'])){
			// 	$data='user_id='.$user_id.'&policyId='.$policyId.'&ftPolicyId='.$ftPolicyId.'&token='.$token.'&blockCategoryArr='.$blockarray.'&start_hour='.$start_hour.'&start_minute='.$start_minute.'&end_hour='.$end_hour.'&end_minute='.$end_minute;
			// }else{
			// 	$blockarray="";
			// 	$data='user_id='.$user_id.'&policyId='.$policyId.'&ftPolicyId='.$ftPolicyId.'&token='.$token.'&blockCategoryArr='.$blockarray.'&start_hour='.$start_hour.'&start_minute='.$start_minute.'&end_hour='.$end_hour.'&end_minute='.$end_minute;
			// }
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			$output=curl_exec($ch);
			curl_close($ch);
			$result =str_replace('}{', '},{', $output);
			$user_group_data=json_decode("[" . $result . "]");

			$ch = curl_init(); 
			curl_setopt($ch,CURLOPT_URL,LOOPBACK_IP.APP_ENDPOINTS.'edit_guest_user_policy.jsp');
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($ch,CURLOPT_HEADER, false);
			$data='userid=3&guest_userid='.$user_id.'&password='.$password;
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			$outpu_res =curl_exec($ch);
			curl_close($ch);
			$output_data = json_decode($outpu_res);
			
			if($user_group_data[0]->update_policy){
				$_SESSION['five_min_pop'] = "FiveMin";
				echo "<script>redirect_handler('".APP_GUEST_URL."');</script>";

			} else {
				echo "<script>bootbox.alert('Some Error!!!', function(result) {
									redirect_handler('".APP_GUEST_URL."');
								}); </script>";

			}
	
}
?>

			<!-- Start Content -->
			<div class="content">
				<div id="main_page_custom" class="main-page">
				  <div class="inner-page">
				  	<form method="POST">  
						<div class="user-info guest-info">
							<!-- <p><strong>Guest Network:&nbsp;&nbsp;<a href="javascript:void(0);"><span class="blue-text blue-blur-text"><?= $config['system']['domain']?></span></a></strong> </p> -->
							<p>
								<input type="hidden" type="text" value="<?= $user_data->user_id?>" name="user_id">
								<input type="hidden" type="text" value="<?= $user_data->policyId?>" name="policyId">
								<input type="hidden" type="text" value="<?= $user_data->ftPolicyId?>" name="ftPolicyId">
								<input type="hidden" type="text" value="<?= $user_data->token?>" name="token">
								<label>Guest WIFI Password: </label> <input type="text"  name="password" id="password" class="guest-password" value="<?= $user_data->user_desc ?>" placeholder="Click here..." />
							</p>
							<div>
								<label>Limit Guest Bandwidth to </label><br/>
								<label>Download:</label>&nbsp;&nbsp;<span class="blue-text blue-blur-text"><?= $config['dnshaper']['queue'][2]['bandwidth']['item'][0]['bw']?>&nbsp;&nbsp;<?= $config['dnshaper']['queue'][2]['bandwidth']['item'][0]['bwscale'].gettext("its")?></span>
							<label>Upload:</label>&nbsp;&nbsp;<span class="blue-text blue-blur-text"><?= $config['dnshaper']['queue'][0]['bandwidth']['item'][0]['bw']?>&nbsp;&nbsp;<?= $config['dnshaper']['queue'][0]['bandwidth']['item'][0]['bwscale'].gettext("its")?></span>
							</div>
							<!-- <p><strong>Download Bandwidth:&nbsp;<em class="blue-text" id="bandwidthDown"></em></strong> </p>
							<p><strong>Upload Bandwidth:&nbsp;<em class="blue-text" id="bandwidthUP"></em></strong> </p> -->
							<input type="submit" class="btn" value="Save" name="done" id="done"/>
							<a  href="javascript:void(0);" class="btn cancel">Cancel</a>
						</div>			
					
					<?php if($user_name == $policyname){ ?>
						<div class="text-center webfilerheading">
							<u>Select Web filter Level</u>
						</div>
						<?php } else { ?>
						<div class="text-center webfilerheading">
							<u>Web filter Level</u>
						</div>
						<?php }?>
						<div id="snackbar">Changes have been made successfully!!</div>
						<div class="web-filter guest">
								<div class="text-center">
									<div class="custom-radio">							
								        <input type="radio" name="webFilter" id="low" value="<?php echo LOW_ID; ?>" <?php echo ($policyname == "Low")?"checked":""?>>
								        <label for="low">Low</label>
							        </div>

							        <div class="custom-radio">					        
								        <input type="radio" name="webFilter" id="medium" value="<?php echo MEDIUM_ID; ?>" <?php echo ($policyname == "Medium")?"checked":""?> >
								        <label for="medium">Medium</label>
							        </div>
							        <div class="custom-radio">					        
								        <input type="radio" name="webFilter" id="high" value="<?php echo HIGH_ID; ?>" <?php echo ($policyname == "High")?"checked":""?>>
								        <label for="high">High</label>
							        </div>
							        <div class="custom-radio">					        
								        <input type="radio" name="webFilter" id="full" value="<?php echo FULL_ID; ?>" <?php echo ($policyname == "Full")?"checked":""?> >
								        <label for="full">Full</label>
							        </div>
							        <div class="custom-radio">					        
								        <input type="radio" name="webFilter" id="custom" value="<?=$user_name?>" <?php echo ($user_name == $policyname)?"checked":""?> >
								        <label for="custom">Custom</label>
							        </div>
							    </div>
							<div><span class="blue-text">Blocked Category's =</span>&nbsp;<img src="images/red-on-btn.png" style="width:15px" title="<?php echo $_SESSION['app_Username']; ?>" /></div>
							<br>

					         <div id="low_filter" class="filter-list">
						        	<ul class="cf">
					        		<?php foreach($Low_policy_list as $key=>$value){ ?>
					        		<li>
				        				<?php if($value->chk == "checked") { ?>
				        					<img src="images/red-on-btn.png" title="<?php echo $_SESSION['app_Username']; ?>" />
				        					<input type="hidden" name="blockCategoryArr_<?php echo LOW_ID; ?>[]" value="<?=$value->policy_name_id; ?>"/>

			        					<? } else {?>
			        						<img src="images/green-on-btn.png" title="<?php echo $_SESSION['app_Username']; ?>" />
		        						<?php } ?>
		        						<?=$value->policy_list?>
		        					</li>
					        		<?php } ?>

					        		</ul>
						        </div>
						        <div id="medium_filter" class="filter-list">
						        	<ul class="cf">
					        		<?php foreach($Medium_policy_list as $key=>$value){ ?>
					        		<li>
				        				<?php if($value->chk == "checked"){?>
				        					<img src="images/red-on-btn.png" title="<?php echo $_SESSION['app_Username']; ?>" />
				        					<input type="hidden" name="blockCategoryArr_<?php echo MEDIUM_ID; ?>[]" value="<?=$value->policy_name_id; ?>"/>

			        					<? } else { ?>
			        						<img src="images/green-on-btn.png" title="<?php echo $_SESSION['app_Username']; ?>" />
		        						<?php } ?>
		        						<?=$value->policy_list?>
		        					</li>
					        		<?php } ?>
					        		</ul>
						        </div>
						      
						        <div id="high_filter" class="filter-list">
						        	<ul class="cf">
					        		<?php foreach($High_policy_list as $key=>$value){ ?>
					        		<li>
				        				<?php if($value->chk == "checked"){?>
				        					<img src="images/red-on-btn.png" title="<?php echo $_SESSION['app_Username']; ?>" />
				        					<input type="hidden" name="blockCategoryArr_<?php echo HIGH_ID; ?>[]" value="<?=$value->policy_name_id; ?>"/>

			        					<? } else { ?>
			        						<img src="images/green-on-btn.png" title="<?php echo $_SESSION['app_Username']; ?>" />
		        						<?php } ?>
		        						<?=$value->policy_list?>
		        					</li>
					        		<?php } ?>
					        		</ul>
						        </div>
						        <div id="full_filter" class="filter-list">
						        	<ul class="cf">
						        		<?php foreach($Full_policy_list as $key=>$value) { ?>
						        			<li>
						        				<?php if($value->chk == "checked") { ?>
						        					<img src="images/red-on-btn.png" title="<?php echo $_SESSION['app_Username']; ?>" />
						        					<input type="hidden" name="blockCategoryArr_<?php echo FULL_ID; ?>[]" value="<?=$value->policy_name_id; ?>"/>

					        					<?}else{?>
					        						<img src="images/green-on-btn.png" title="<?php echo $_SESSION['app_Username']; ?>" />
				        						<?php } ?>
				        						<?=$value->policy_list?>
		        							</li>
					        			<?php } ?>

					        		</ul>
						        </div>
						    <div id="custom_filter" class="filter-list custom_list">
						        	<ul class="cf">

						        		<?php foreach($custom_policy_list as $key=>$value) { ?>
						        			<li>
						        				<?php if($value->chk == "checked") { ?>
						        					<span class="img-button"><img src="images/red-on-btn.png" title="<?php echo $_SESSION['app_Username']; ?>" class="blr" /></span>
					        					<? } else { ?>
					        						<span class="img-button"><img src="images/green-on-btn.png" title="<?php echo $_SESSION['app_Username']; ?>" class="blr" /></span>
				        						<?php } ?>
				        						<input style="display:none" type="checkbox" name="blockCategoryArr[]" value="<?=$value->policy_name_id?>" <?=$value->chk?> />
				        						<?=$value->policy_list?>
				        					</li>
						        		<?php } ?>
						        	</ul>
						        	 	 
					        	
					        </div>
					    	</br>
					        <div class="text-center" id="blocktime" style="display:<?php echo ($user_name == $policyname)?"block":"none";?>;">
					        	<table class = "safemode-table">
					        		<tbody>
					        		<tr>
					        			<td>
					        				Safe Search
					        			</td>
					        			<td>

								<input name="safeMode" id="safeMode" value="0" <?php echo ($custom_policy_data->safeMode == 0)? "checked": ""; ?> type="radio"> Off
								<input name="safeMode" id="safeMode" value="1" <?php echo ($custom_policy_data->safeMode == 1)? "checked": ""; ?> type="radio"> Moderate
								<input name="safeMode" id="safeMode" value="2" <?php echo ($custom_policy_data->safeMode == 2)? "checked": ""; ?> type="radio"> Strict
					        			</td>
					        		</tr>
					        			<tr>
					        			<td>
					        				Pause Internet Daily 
					        			</td>
					        			<td>
					        					<span class="blue-text">From:</span>
								<select name="start_hour">
										<?php 
										for($i=0;$i<24;$i++) { 
											$starthr = ($i<=9)?'0'.$i:$i;
											$checked="";

											 if(substr($custom_policy_data->btStime, 0, 2 ) == '0'.$i){
											 	$checked = "selected";
											 } 
											?>
										<option <?php echo $checked; ?> value="<?php echo $starthr;?>"><?php echo $starthr;?></option>
										<?php } ?>
								</select><span class="blue-text">:</span>
								<select name="start_minute">
										<?php for($i=0;$i<60;$i++) {
											$starthr = ($i<=9)?'0'.$i:$i;
											$checked="";

											 if(substr($custom_policy_data->btStime, 2, 3 ) == '0'.$i)  {
											 	$checked = "selected";
											 } 
										?>

										<option <?php echo $checked; ?> value="<?php echo $starthr;?>"><?php echo $starthr;?></option>
										<?php } ?>
								</select>&nbsp;
								<span class="blue-text">To:</span>
								<select name="end_hour">
										<?php for($i=0;$i<24;$i++) {
											$starthr = ($i<=9)?'0'.$i:$i;
											$checked="";

											 if(substr($custom_policy_data->btEtime, 0, 2 ) == '0'.$i) { 
											 	$checked = "selected";
											 } 
										?>

										<option <?php echo $checked; ?> value="<?php echo $starthr;?>"><?php echo $starthr;?></option>
										<?php } ?>
								</select><span class="blue-text">:</span>
								<select name="end_minute">
										<?php for($i=0;$i<60;$i++) {
											$starthr = ($i<=9)?'0'.$i:$i;
											$checked="";

											 if(substr($custom_policy_data->btEtime, 2, 3 ) == '0'.$i) {
											 	$checked = "selected";
											 } 
										?>

										<option <?php echo $checked; ?> value="<?php echo $starthr;?>"><?php echo $starthr;?></option>
										<?php } ?>
								</select>
					        			</td>
					        		</tr>
					        	</tbody>
					        	</table>
								
							</div>
						    
					    </div>

				</form>	<!-- end form -->
					<!-- Custom Dots section Start-->
					<div class="bottom_button text-center">
						<input id="button2" type="button" value="Guest Insights" class="btn">
					</div>
					<!-- <ul class="page-pagination" style="display: block;">
					  	<li id="button1" <?php //echo ($_SERVER['DOCUMENT_URI'] == APP_GUEST_URL)?'class="active"':'';?>>
					  		<button type="button">1</button></li>
				  		<li id="button2" <?php //echo ($_SERVER['DOCUMENT_URI'] == GUEST_INSIGHTS)?'class="active"':'';?>>
				  			<button type="button">2</button></li>
		  		  	</ul> -->
		  		  	<!-- Custom Dots section End-->	
				  </div> <!-- inner-page -->
				  
				</div>	<!-- main-page -->		
				
			</div> <!-- End Content -->

			<!-- New Password Modal Start-->
			<div class="modal fade" id="newPasswordModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<!-- Modal Header -->
			            <div class="modal-header">
			                <h4 class="modal-title" id="myModalLabel">
			                    Enter New Password
			                </h4>
			            </div>

						<!-- Modal Body -->
			            <div class="modal-body modal-body-guest">						                
			                <form role="form">
			                  <div class="form-group">
			                    <label for="password">New Password</label>
			                      <input type="password" class="form-control"
			                      id="newPass" placeholder="Enter Password"/>
			                  </div>
			                  <div class="form-group">
			                    <label for="userPassword">Confirm Password</label>
			                      <input type="password" class="form-control"
			                          id="newCPass" placeholder="Re-Enter Password"/>
			                  </div>
			                  <div class="form-group">
			                    <span class="error"></span>
			                      
			                  </div>
			                  	<div class="form-group">
			                  		<input type="hidden" type="text" value="<?= $user_data->user_id ?>" id="userId">
			                  		<input type="hidden" type="text" value="" id="error_userId">
			                  	</div>
			                </form>						                
			            </div>            
						<!-- Modal Footer -->
			            <div class="modal-footer">
			                <button type="button" class="btn btn-blue cancle" style="float:left;"
			                        data-dismiss="modal">
			                            Cancel
			                </button>
			                <button type="button" class="btn btn-blue" id="updatePassword">
			                    Save
			                </button>
			            </div>
					</div>
				</div>
			</div>
			<!-- New Password Modal End-->

<?php 
if(isset($_SESSION['five_sec_pop']) && $_SESSION['five_sec_pop'] == 'sec'){	
	echo '<script>$("#snackbar").addClass("show"); setTimeout(function(){ $( "#snackbar" ).removeClass("show"); }, 5000);</script>';
	unset($_SESSION["five_sec_pop"]);
}
if(isset($_SESSION['five_min_pop']) && $_SESSION['five_min_pop'] == 'FiveMin'){	
	$_SESSION['five_sec_pop'] = 'sec';
	unset($_SESSION["five_min_pop"]);
}
include('foot.inc'); ?>

<script type="text/javascript">

function set_custom_data(){
	var html="";
	<?php foreach($custom_policy_list as $key=>$value) { ?>
		html+='<li>';
			<?php if($value->chk == "checked") { ?>
				html+='<img src="images/red-on-btn.png" title="blr" class="blr" />';
			<? } else { ?>
				html+='<img src="images/green-on-btn.png" title="blr" class="blr" />';
			<?php } ?>
			html+='<input style="display:none" type="checkbox" name="blockCategoryArr[]" value="<?php echo $value->policy_name_id?>" <?php echo $value->chk?> />';
			html+='<?php echo $value->policy_list?>';
		html+='</li>';
	<?php } ?>
	$('#custom_filter .cf').html(html);
	policy_action();
}

function policy_action(){
	$('img.blr').unbind('click');
	$('img.blr').click(function(){
			        var $this = $(this);
			        var checkbox = $this.closest('li').find('input:checkbox[name="blockCategoryArr[]"]');
			        if(checkbox.is(':checked')){
			            //$this.addClass('checked');
			            $this.attr('src','images/green-on-btn.png');
			            $this.closest('li').find('input:checkbox[name="blockCategoryArr[]"]').prop('checked',false);
			        } else {
			            //img$this.removeClass('checked');
			            $this.attr('src','images/red-on-btn.png');
			            $this.closest('li').find('input:checkbox[name="blockCategoryArr[]"]').prop('checked', true);
			        }
    		});
}

$(document).ready(function(){
	policy_action();
	var userID = $('#userId').val();

	$('#button2').click(function(){
			$( "#snackbar" ).removeClass("show");
			redirect_handler('<?php echo GUEST_INSIGHTS; ?>');
	});

	$('.cancel').click(function(){
			$( "#snackbar" ).removeClass("show");
		redirect_handler('<?php echo APP_GUEST_URL;?>');
	});
	$('#password').click(function(){
			$( "#snackbar" ).removeClass("show");
        $('#newPasswordModal').modal('show');
    });

     $('#updatePassword').click(function(){
			$( "#snackbar" ).removeClass("show");
				var userID = $('#userId').val();
				var password = $('#newPass').val();
				var cpassword = $('#newCPass').val();
				if(password != cpassword){
					$('.error').html('Passwords Don’t Match Try Again');
					$("#newPasswordModal input").val("");
					$('#userId').val(userID);
					return false;
				} 
				// else if(password == ''){
				// 	$('.error').html('Please Enter password');
				// 	return false;
				// }
				 else {
 					$.ajax({
						url: "change_password.php",
						type:"POST",
						data:{'userid':userID,'password':password,'name':'Guest'},
						cache: false,
						beforeSend: function() {
							$('.loading').show();
						},
						success: function(data){
							var ftPolicyId = $('input[name=ftPolicyId]').val();
							var token = $('input[name=token]').val();
							if(data == 1){
								$.ajax({
									url: "update_guest_policy.php",
									type:"POST",
									data:{'userid':userID,'password':password},
									cache: false,
									beforeSend: function() {
										$('.loading').show();
									},
									success: function(data){
										var data = JSON.parse(data);
										if(data.update_policy == 1){
											$("#newPasswordModal input").val("");
											$("#newPasswordModal").modal('hide');
											bootbox.alert('<b>Guest Network updated successfully!!</b>', function(result) {
												location.reload();
											});
										  } else {
											bootbox.alert('Some Error!!!');
											$("#newPasswordModal input").val("");
										}
										$('.loading').hide();
									}
								});
								
							} else {
								bootbox.alert('Someee Error!!!');
								$("#newPasswordModal input").val("");
							}
							$('.loading').hide();
						}
					});
				}
			});

	$('#newPasswordModal .cancle').click(function(){
			$( "#snackbar" ).removeClass("show");
		$('.error').text('');
	});

	$('.custom-radio').click(function(){
			$( "#snackbar" ).removeClass("show");
		var value = $(this).find('input[name="webFilter"]:checked').val();
		if(value =='Guest'){
			$('#blocktime').show();
		}else{
			$('#blocktime').hide();
		}

	});
			
	$('#password').dblclick(function(){
			$( "#snackbar" ).removeClass("show");
		$(this).attr("readonly",false).focus();
	});

	$('#password').focusout(function(){
			$( "#snackbar" ).removeClass("show");
		$(this).attr("readonly",true);
	});

	$('#password').click(function(){
			$( "#snackbar" ).removeClass("show");
		$(this).focusout();
	});

	$('#password').on("keyup", action);

	action();

   function action() {
	    if( $.trim($('#password').val()) != '' ) {
	    } else {
	    }   
	}

	$('#custom').click(function(){
			$( "#snackbar" ).removeClass("show");
		$('.webfilerheading').html("<u>Select Web filter Level</u>");
	});
	$('#low,#medium,#high,#full').click(function(){
			$( "#snackbar" ).removeClass("show");
		$('.webfilerheading').html("<u>Web filter Level</u>");
	});
							
});


</script>