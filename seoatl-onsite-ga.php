<?php
/* 
Plugin Name: Seoatl On Site Google Analytics
Plugin URI: http://www.seoatl.com/tools/wordpress/on-site-google-analytics-plugin
Version: v0.1
Author: <a href="http://twitter.com/seoatl">James Charlesworth</a>
Description: A Google Analytics plugin for viewing GA on your website.
 
Copyright 2010  James Charlesworth  (email : james DOT charlesworth [a t ] g m ail DOT com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributded in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/



if (!class_exists("SeoatlOnSiteGa")) {
	class SeoatlOnSiteGa {
                var $adminOptionsName = "SeoatlOnSiteGaAdminOptions";
               
              


		function SeoatlOnSiteGa() { //constructor
			
		}

                function init() {
                    $this->getAdminOptions();
                   
                }



                function getAdminOptions() {
                    $seoatlAdminOptions = array('ga_username' => '',
                        'ga_password' => '',
                        'ga_profile_id' => '',
                        'ga_date_range'=>'month',
                        'author_global'=> 0,
                        'users'=>array());

                    $seoatlOptions = get_option($this->adminOptionsName);
                    if (!empty($seoatlOptions)) {
                        foreach ($seoatlOptions as $key => $option)
                            $seoatlAdminOptions[$key] = $option;
                    }
                    update_option($this->adminOptionsName, $seoatlAdminOptions);



                    return $seoatlAdminOptions;


                }

                public function getUserLevel($user_level)
                {
                    switch($user_level) {
                        case 10:
                            return 'admin';
                            break;
                        case 7:
                            return 'publisher';
                            break;
                        case 3:
                            return 'author';
                            break;
                        case 2:
                            return 'editor';
                            break;
                        

                    }
                }

                function checkUserPriviledges()
                {
                     global $user_ID;
                     global $post;
                     $seoatlOptions = $this->getAdminOptions();
                     $user_info = get_userdata($user_ID);
                    
                    if (is_page()) {
                        $page = get_page($post->ID);
                        
                        $author_id = $page['post_author'];
                    } else {
                        $author_id = get_post($post->ID)->post_author;
                   
                    }
                
                  

                     if ($user_info->user_level==10) {
                        
                         return true;

                    } elseif ($seoatlOptions['author_global']==1 && $user_info->user_level>1) {
                        return true;
                    } elseif (in_array($user_ID,$seoatlOptions['users'])) {
                         //check to make sure teh author id and the user id match

                         if ($author_id==$user_ID) {
                      
                             return true;

                            
                         } else {
                             return false;
                         }

                     }  else {
                         return false;
                     }
                }


                function printAdminPage() {
                    global $wpdb;
                    
                    $seoatlOptions = $this->getAdminOptions();

                    if (isset($_POST['update_seoatlOnSiteGaSettings'])) {

                        if (isset($_POST['seoatlGaUsername'])) {
                            $seoatlOptions['ga_username'] = $_POST['seoatlGaUsername'];
                        }

                        if (isset($_POST['seoatlGaPassword'])) {
                            $seoatlOptions['ga_password'] = $_POST['seoatlGaPassword'];
                        }
                        if (isset($_POST['seoatlGaProfileId'])) {
                            $seoatlOptions['ga_profile_id'] = $_POST['seoatlGaProfileId'];
                          
                        }

                        if (isset($_POST['seoatlGaDateRange'])) {
                            $seoatlOptions['ga_date_range'] = $_POST['seoatlGaDateRange'];

                        }
                    
                        if (isset($_POST['seoatlGAUsers'])) {
                            $seoatlOptions['users'] = $_POST['seoatlGAUsers'];

                        }

                       
                        if (isset($_POST['seoatlGaAuthorGlobal'])) {
                            $seoatlOptions['author_global'] = $_POST['seoatlGaAuthorGlobal'];

                        }
                    

                        update_option($this->adminOptionsName, $seoatlOptions);

                       ?>
                        <div class="updated"><p><strong><?php _e("Settings Updated.", "SeoatlOnSiteGa");?></strong></p></div>

                     <?php
                    }

                    ?>
                        <div class=wrap>
                        <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
                        <h2>Seoatl On Site Google Analytics</h2>
                        
                        
                        <label for="seoatlGaUsername">Google Analytics Username:</label><br />
                        <input type="text" id="seoatlGaUsername" value="<?php echo  _e(apply_filters('format_to_edit',$seoatlOptions['ga_username']), 'SeoatlOnSiteGa') ?>" name="seoatlGaUsername" />
                         <br /><br />
                         <label for="seoatlGaPassword">Google Analytics Password:</label><br />
                        <input type="password" id="seoatlGaPassword" value="<?php echo  _e(apply_filters('format_to_edit',$seoatlOptions['ga_password']), 'SeoatlOnSiteGa') ?>"  name="seoatlGaPassword" />
                        <br/><br />

                        <label for="seoatlGaProfileId">Google Analytics Profile</label><br />
                        <select id="seoatlGaProfileId" name="seoatlGaProfileId">
                            <option value=""></option>
                        </select>
                        <img id="profile_loader" style="display:none;" src="<?php bloginfo('wpurl') ?>/wp-content/plugins/on-site-google-analytics/images/admin-loader.gif" />

                        <br /><br />
                       <label for="seoatlGaProfileId">Date Range:</label>
                        <select  id="seoatlGaDateRange" name="seoatlGaDateRange">
                            <option <?php if ($seoatlOptions['ga_date_range']=='today') echo "selected" ;?> value="today">Today</option>
                            <option <?php if ($seoatlOptions['ga_date_range']=='yesterday') echo "selected" ;?> value="yesterday">Yesterday</option>
                            <option <?php if ($seoatlOptions['ga_date_range']=='month') echo "selected" ;?> value="month">Past Month</option>
                        </select>
                        <br /><br />

                        <label for="seoatlGAUsers">Select Users Who Can See Analytics</label><br />
                        <select multiple size="5" style="width:400px;height:200px;" name="seoatlGAUsers[]">
                            
                   
                       
<?php
for ( $i=2;$i<=10;$i++) {
		$userlevel = $i;
		$authors = $wpdb->get_results("SELECT * from $wpdb->usermeta WHERE meta_key = 'wp_user_level' AND meta_value = '$userlevel'");
		foreach ( (array) $authors as $author ) {
			$author    = get_userdata( $author->user_id );
			$userlevel = $author->wp2_user_level;
			$name      = $author->nickname;
			if ( $show_fullname && ($author->first_name != '' && $author->last_name != '') ) {
				$name = "$author->first_name $author->last_name";
			}
                        if (in_array($author->ID,$seoatlOptions['users'])) {
                            $selected = 'true';
                        } else {
                            $selected =null;
                        }

			$link = '<option value="'.$author->ID.'" selected="'.$selected.'">' . $name . ' - '.self::getUserLevel($author->user_level).'</option>';
			echo $link;
		}
		
               
	}
?>
                        </select><br /><br />
                        <label for="seoatlGaAuthorGlobal">Allow authors to view analytics of other authors posts?</label><br />
                         <input type="radio" name="seoatlGaAuthorGlobal" value="1" <?php echo (!isset( $seoatlOptions['author_global']) or ($seoatlOptions['author_global']==1)) ? 'checked' : '' ?> />Yes
                         <input type="radio" name="seoatlGaAuthorGlobal" value="0" <?php echo (isset( $seoatlOptions['author_global']) && ($seoatlOptions['author_global']==0)) ? 'checked' : '' ?> /> No <br />



                        
                        <div class="submit">
                        <input type="submit" name="update_seoatlOnSiteGaSettings" value="<?php _e('Update Settings', 'SeoatlOnSiteGa') ?>" /></div>
                        </form>
                        
                         <script type="text/javascript">
                          
                            jQuery("#seoatlGaPassword").bind("blur",function(){
                                jQuery("#profile_loader").css("display","inline");
                               jQuery("#seoatlGaProfileId").load("<?php bloginfo('wpurl') ?>/wp-content/plugins/on-site-google-analytics/php/ga-profile-ajax.php",{'username': jQuery("#seoatlGaUsername").val(),'password': jQuery("#seoatlGaPassword").val()}, function(){
                                  jQuery("#profile_loader").css("display","none");
                               });
                            })

                            jQuery(document).ready(function(){
                                 jQuery("#profile_loader").css("display","inline");
                                jQuery("#seoatlGaProfileId").load("<?php bloginfo('wpurl') ?>/wp-content/plugins/on-site-google-analytics/php/ga-profile-ajax.php",{'username': jQuery("#seoatlGaUsername").val(),'password': jQuery("#seoatlGaPassword").val()},function(){
                                    jQuery("#profile_loader").css("display","none");
                                });
                            })

                         

                         </script>
                         </div><?php
 
                   

                }


                function loadProfileOptions($username,$password) {
 global $user_ID;

                     
                     if (!self::checkUserPriviledges()) {
                         return;
                     }

                    $seoatlOptions = $this->getAdminOptions();
                     require_once('php/classes/gapi.class.php');
                     $ga = new gapi($username,$password);
                   
                     foreach ($ga->requestAccountData() as $account) {
                   //  print_r($account);
                        echo '<option ';
                        if ($seoatlOptions['ga_profile_id']==$account->getProfileId()) echo 'selected';
                        echo ' value="'.$account->getProfileId().'">'.$account->getAccountName().' ('.$account->getTitle().')</option>';

                     }
                   // echo '<option value="">test</option>';

                }
 

                
                function addHeaderCode() {
                           global $user_ID;

                     //$user_info = get_userdata($user_ID);
                     if (!self::checkUserPriviledges()) {
                         return;
                     }
                     echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/on-site-google-analytics/css/seoatl-on-site-ga.css" />' . "\n";
                     echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/on-site-google-analytics/css/tipsy.css" />' . "\n";
                    if (function_exists('wp_enqueue_script')) {
                        wp_enqueue_script('seoatl_on_site_ga_c', get_bloginfo('wpurl') . '/wp-content/plugins/on-site-google-analytics/js/jquery-ui-1.8.2.custom.min.js', array('jquery'), '0.1');
                        wp_enqueue_script('seoatl_on_site_ga_b', get_bloginfo('wpurl') . '/wp-content/plugins/on-site-google-analytics/js/jquery.tipsy.js', array('jquery'), '0.1');
                        wp_enqueue_script('seoatl_on_site_ga_a', get_bloginfo('wpurl') . '/wp-content/plugins/on-site-google-analytics/js/seoatl-on-site-ga.js.php', array('jquery'), '0.1');
                        
                    }

                    $seoatlOptions = $this->getAdminOptions();

                    if ($seoatlOptions['show_header'] == "false") { return; }

   
                     
                }

                function addFooterCode() {
                  
                    global $user_ID;
                    
                     
                     if (!self::checkUserPriviledges()) {
                         return;
                     }
	             $seoatlOptions = $this->getAdminOptions();

                    
                  
               ?>
                 
                        <div id="onsite-ga-plugin" style="position:fixed;"><div id="onsite-ga-plugin-inner">
                     <img src="<?php bloginfo('wpurl') ?>/wp-content/plugins/on-site-google-analytics/images/ajax-loader.gif" />
                     <input type="hidden" id="seoatl_onsite_ga_request_uri" name="seoatl_onsite_ga_request_uri" value="<?php echo $_SERVER['REQUEST_URI'];?>" />
                </div></div><br /><br />
                
                <?php 
                 



                }



                function loadData($request_uri) {
                     global $user_ID;

                     if (!self::checkUserPriviledges()) {
                         return;
                     }
              
                    //magic
                    $seoatlOptions = $this->getAdminOptions();

                    switch ($seoatlOptions['ga_date_range']) {
                        case 'today':
                            $start_date = date('Y-m-d',strtotime("now"));
                            $end_date   = date('Y-m-d',strtotime("+1 day"));
                            $tip_date = 'today';
                            break;
                        case 'yesterday':
                            $start_date = date('Y-m-d',strtotime("-1 day"));
                            $end_date   = date('Y-m-d',strtotime("-1 day"));
                            $tip_date = 'yesterday';
                            break;
                        case 'month':
                            $start_date = date('Y-m-d',strtotime("-1 month"));
                            $end_date   = date('Y-m-d',strtotime("now"));
                            $tip_date = 'over the last 30 days';
                            break;
                        default:
                            $start_date = date('Y-m-d',strtotime("-1 month"));
                            $end_date   = date('Y-m-d',strtotime("now"));
                            $tip_date = 'over the last 30 days';
                            break;

                    }
                  


                     require_once('php/classes/gapi.class.php');
                     $ga = new gapi($seoatlOptions['ga_username'],$seoatlOptions['ga_password']);
             

		    		
                     //http://code.google.com/apis/analytics/docs/gdata/gdataReferenceCommonCalculations.html
                     //bounce rate ga:bounces/ga:entrances
                     //avg time on page ga:timeOnPage/(ga:pageviews - ga:exits)
                     //exit rate ga:exits/ga:pageviews, ga:pagePath

                     $filter = 'pagePath == '.$request_uri; // -----live
                     
                      //$filter = 'pagePath ==/';
                     
                     $ga->requestReportData($seoatlOptions['ga_profile_id'],array('pagePath'),array('timeOnPage','pageviews','visits','entrances','bounces','exits'),null,$filter,$start_date,$end_date);
                    
  
                     foreach($ga->getResults() as $result) {
                         if (($result->getPageviews() - $result->getExits()) > 0) {

                             $seconds = ($result->getTimeOnPage() / ($result->getPageviews() - $result->getExits()));

                         } else {
                             $seconds  = 0;
                         }
                        
                         $avg_time = floor($seconds/60) . ":" . $seconds % 60;
                         $views= $result->getPageviews();
                         $bounce_rate = number_format(($ga->getBounces()/$ga->getEntrances())*100,2);
                         $exit_rate = number_format(($ga->getExits()/$ga->getPageviews())*100,2);
                     }

                     unset($ga);
                     $ga = new gapi($seoatlOptions['ga_username'],$seoatlOptions['ga_password']);
                     $referral_filter = "medium==referral && pagePath==".$request_uri;
                     //get the referring sites
                     $ga->requestReportData($seoatlOptions['ga_profile_id'],array('source','referralPath','date'),array('visits'),'-date',$referral_filter,$start_date,$end_date);
                     $referring_visits=array();

                     foreach($ga->getResults() as $result)
                     {

                         $referring_visits[] = array('source'=>$result->getSource(),'path'=>$result->getReferralPath(),'visits'=>$result->getVisits() ) ;


                     }

                     $r_total_visits = $ga->getVisits();

                     unset($ga);
                     $ga = new gapi($seoatlOptions['ga_username'],$seoatlOptions['ga_password']);
                     $keyword_filter = "ga:keyword!=(not set) && pagePath==".$request_uri;
                     $ga->requestReportData($seoatlOptions['ga_profile_id'],array('keyword','source','medium'),array('visits'),'-visits',$keyword_filter,$start_date,$end_date);
                     $keywords = array();
                     foreach($ga->getResults() as $result)
                     {

                         $keywords[] = array('keyword'=>$result->getKeyword(),'source'=>$result->getSource(),'medium'=>$result->getMedium(),'visits'=>$result->getVisits());

                     }
                     $source=array();
                     $volume=array();
                     foreach($keywords as $key => $row) {
                         $source[$key]= $row['source'];
                         $volume[$key]= $row['visits'];



                     }

                     array_multisort($source, SORT_ASC, $volume, SORT_DESC, $keywords);

                     $total_visits = $ga->getVisits();
                     ?>
                     
                     <div class="onsite-ga-stat"><span class="label">Views: <?php echo $views; ?></span><a href="#" onclick="return false" title="The number of times this page has been viewed by website visitors <?php echo $tip_date; ?>." class="tipTip"><img width="12" height="12" src="<?php bloginfo('wpurl') ?>/wp-content/plugins/on-site-google-analytics/images/question.png" /></a></div>
                     <div class="onsite-ga-stat"><span class="label">Avg Time on Page: <?php echo $avg_time;?></span><a href="#" onclick="return false" title="The average amount of time visitors spent on this page" class="tipTip"><img width="12" height="12" src="<?php bloginfo('wpurl') ?>/wp-content/plugins/on-site-google-analytics/images/question.png" /></a></div>

                     <div class="onsite-ga-stat"><span class="label">Bounce Rate:  <?php echo $bounce_rate; ?>%</span><a href="#" onclick="return false" title="A bounce is determined by a single page visit to your site. The bounce rate is the number of bounces divided by the number of entrances." class="tipTip"><img width="12" height="12" src="<?php bloginfo('wpurl') ?>/wp-content/plugins/on-site-google-analytics/images/question.png" /></a></div>
                     <div class="onsite-ga-stat"><span class="label">Exit Rate: <?php echo $exit_rate; ?>%</span><a href="#" onclick="return false" title="The number of visitors who exited from your site on this page. The exit rate is calcuated by the number of exits divided by the number of page views." class="tipTip"><img width="12" height="12" src="<?php bloginfo('wpurl') ?>/wp-content/plugins/on-site-google-analytics/images/question.png" /></a></div>
                     <div class="onsite-ga-stat onsite-ga-hover" id="onsite-ga-referring-sites">
                         <?php if (count($referring_visits)>0) :?>
                         <div class="onsite-ga-referring-sites">
                             <ul>
                                 <?php foreach ($referring_visits as $site) :?>
                                 <li><a href="http://<?php echo $site['source']; ?><?php echo $site['path']; ?>" title="<?php echo $site['source']; ?><?php echo $site['path']; ?>" target="_blank"><?php echo $site['source']; ?></a>: <?php echo $site['visits'];?> Visit<?php if ( $site['visits']>1) echo 's'; ?></li>
                                 <?php endforeach; ?>
                             </ul>

                         </div>
                         <?php endif; ?>
                        <span class="label"><?php echo count($referring_visits); ?> Referring Sites Sent <?php echo $r_total_visits; ?> Visits</span><a href="#" onclick="return false" title="The 30 most recent websites that are currently driving traffic to your website via links <?php echo $tip_date;?>." class="tipTip"><img width="12" height="12" src="<?php bloginfo('wpurl') ?>/wp-content/plugins/on-site-google-analytics/images/question.png" /></a></div>
                     <div class="onsite-ga-stat onsite-ga-hover onsite-ga-stat-last" id="onsite-ga-keywords">
                         <?php if (count($keywords)>0) :?>
                         <div class="onsite-ga-keywords">

                               
                             <div id="onsite-ga-accordion">
                                 <?php foreach ($keywords as $key => $keyword) :?>

                                 <?php
                              
                                 
                                if ($keywords[$key-1]['source']!=$keyword['source']) {
              
                                 echo'<h3><a href="#">'.ucwords($keyword['source']).'</a></h3><div><ul>';
                                }
                                 ?>

                                 <li><b><?php echo $keyword['keyword'];?></b> (<?php echo $keyword['medium']; ?>): <?php echo $keyword['visits']; ?> </li>
                                 <?php

                                if ($keywords[$key+1]['source']!=$keyword['source']) {
                                 echo '</ul></div>';
                                }
                                
                                 ?>
                                 <?php endforeach; ?>
 
                             </div>
                         </div>
                         <?php endif; ?>
                                <script type="text/javascript">
	jQuery(function() {

 


                jQuery("div.onsite-ga-hover").bind("mouseenter",function(){
                    var id_class = jQuery(this).attr("id");
                  
                    jQuery("."+id_class).css("display","inline");

                    if (id_class=="onsite-ga-keywords") {

                               var icons = {
                                    header: "ui-icon-triangle-1-e",
                                    headerSelected: "ui-icon-triangle-1-s"
                              };
                              jQuery("#onsite-ga-accordion").accordion({
                                    icons: icons
                               });

                    }

                });

                jQuery(".onsite-ga-hover").bind("mouseleave", function(){
                     var id_class = jQuery(this).attr("id");

                    jQuery("."+id_class).css("display","none");
                })






	});
	</script>

                        <span class="label"><?php echo count($keywords); ?> Keywords Sent <?php echo $total_visits; ?> Visits</span> <a href="#" onclick="return false" title="The 30 most recent terms search engine visitors are using to find this page <?php echo $tip_date; ?>." class="tipTip"><img width="12" height="12" src="<?php bloginfo('wpurl') ?>/wp-content/plugins/on-site-google-analytics/images/question.png" /></a></div>
               <script type="text/javascript">
                     jQuery(function() {
                       jQuery('.tipTip').tipsy({gravity: 's'});
                     });
               </script>
                 <?php
                     

                }

	}

} //End Class SeoatlOnSiteGa

//Initialize the admin panel
if (!function_exists("SeoatlOnSiteGa_ap")) {
    function SeoatlOnSiteGa_ap() {
        global $seoatl_onsite_ga_plugin;
        if (!isset($seoatl_onsite_ga_plugin)) {
            return;
        }
        if (function_exists('add_options_page')) {
            add_options_page('Seoatl Onsite Google Analytics', 'Onsite Google Analytics', 9, basename(__FILE__), array(&$seoatl_onsite_ga_plugin, 'printAdminPage'));
        }

    }
}



if (class_exists("SeoatlOnSiteGa")) {
	$seoatl_onsite_ga_plugin = new SeoatlOnSiteGa();
}



//Actions and Filters	
if (isset($seoatl_onsite_ga_plugin)) {
	//Actions


        
    	add_action('wp_head', array(&$seoatl_onsite_ga_plugin, 'addHeaderCode'), 1);
	add_action('wp_footer', array(&$seoatl_onsite_ga_plugin, 'addFooterCode'), 1);
        add_action('admin_menu', 'SeoatlOnSiteGa_ap');
        add_action('activate_seoatl-onsite-ga/seoatl-onsite-ga.php', array(&$seoatl_onsite_ga_plugin, 'init'));
         

        ////Filters
}


