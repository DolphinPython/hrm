<?php
    $link = $_SERVER['PHP_SELF'];
    $link_array = explode('/',$link);
    $page = end($link_array);
?>
<!-- Sidebar -->
<div class="sidebar" id="sidebar">
                <div class="sidebar-inner slimscroll">
					<div id="sidebar-menu" class="sidebar-menu">
							
						<ul>
							<li> 
								<a href="admin-dashboard.php"><i class="la la-home"></i> <span>Back to Home</span></a>
							</li>
							<li class="menu-title">Settings</li>
							<li class="<?php echo ($page == 'settings.php')?'active':'';?>"> 
								<a href="settings.php"><i class="la la-building"></i> <span>Company Settings</span></a>
							</li>
							<li class="<?php echo ($page == 'localization.php')?'active':'';?>"> 
								<a href="localization.php"><i class="la la-clock-o"></i> <span>Localization</span></a>
							</li>
							<li class="<?php echo ($page == 'theme-settings.php')?'active':'';?>"> 
								<a href="theme-settings.php"><i class="la la-photo"></i> <span>Theme Settings</span></a>
							</li>
							<li class="<?php echo ($page == 'roles-permissions.php')?'active':'';?>"> 
								<a href="roles-permissions.php"><i class="la la-key"></i> <span>Roles & Permissions</span></a>
							</li>
							<li class="<?php echo ($page == 'email-settings.php')?'active':'';?>"> 
								<a href="email-settings.php"><i class="la la-at"></i> <span>Email Settings</span></a>
							</li>
							<li class="<?php echo ($page == 'performance-setting.php')?'active':'';?>"> 
								<a href="performance-setting.php"><i class="la la-chart-bar"></i> <span>Performance Settings</span></a>
							</li>
							<li class="<?php echo ($page == 'approval-setting.php')?'active':'';?>"> 
								<a href="approval-setting.php"><i class="la la-thumbs-up"></i> <span>Approval Settings</span></a>
							</li>
							<li class="<?php echo ($page == 'invoice-settings.php')?'active':'';?>"> 
								<a href="invoice-settings.php"><i class="la la-pencil-square"></i> <span>Invoice Settings</span></a>
							</li>
							<li class="<?php echo ($page == 'salary-settings.php')?'active':'';?>"> 
								<a href="salary-settings.php"><i class="la la-money"></i> <span>Salary Settings</span></a>
							</li>
							<li class="<?php echo ($page == 'notifications-settings.php')?'active':'';?>"> 
								<a href="notifications-settings.php"><i class="la la-globe"></i> <span>Notifications</span></a>
							</li>
							<li class="<?php echo ($page == 'change-password.php')?'active':'';?>"> 
								<a href="change-password.php"><i class="la la-lock"></i> <span>Change Password</span></a>
							</li>
							<li class="<?php echo ($page == 'leave-type.php')?'active':'';?>"> 
								<a href="leave-type.php"><i class="la la-cogs"></i> <span>Leave Type</span></a>
							</li>
							<li class="<?php echo ($page == 'toxbox-setting.php')?'active':'';?>"> 
								<a href="toxbox-setting.php"><i class="la la-comment"></i> <span>ToxBox Settings</span></a>
							</li>
							<li class="<?php echo ($page == 'cron-settings.php')?'active':'';?>"> 
								<a href="cron-setting.php"><i class="la la-rocket"></i> <span>Cron Settings</span></a>
							</li>
						</ul>
					</div>
                </div>
</div>
<!-- Sidebar -->
