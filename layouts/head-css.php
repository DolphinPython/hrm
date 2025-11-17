<?php
    $link = $_SERVER['PHP_SELF'];
    $link_array = explode('/',$link);
    $page = end($link_array);
?>
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">

	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

	<!-- Lineawesome CSS -->
	<link rel="stylesheet" href="assets/css/line-awesome.min.css">
	<link rel="stylesheet" href="assets/css/material.css">

	<?php  if ($page !== 'activities.php') {   ?>
	<!-- Feather CSS -->
	<link rel="stylesheet" href="assets/css/feather.css">
	<?php } ?>

	<?php  if ($page == 'ui-clipboard.php'|| $page == 'ui-drag-drop.php') {   ?>
		<!-- Dragula CSS -->
		<link rel="stylesheet" href="assets/plugins/dragula/css/dragula.min.css">
	 <?php } ?>

	 <?php  if ($page == 'ui-notification.php') {   ?>
	 <link rel="stylesheet" href="assets/plugins/alertify/alertify.min.css">
	 <?php } ?>

	 <?php  if ($page == 'ui-stickynote.php') {   ?>
	 	<!-- Sticky CSS -->
		 <link rel="stylesheet" href="assets/plugins/stickynote/sticky.css">
		 <?php } ?>

	 <?php  if ($page == 'ui-text-editor.php' || $page == 'company-details.php' || $page == 'contact-details.php'||$page=='laeds-details') {   ?>
	<!-- Summernote CSS -->
	<link rel="stylesheet" href="assets/plugins/summernote/summernote-bs4.min.css">
	<?php } ?>
	<?php  if ($page == 'icon-weather.php') {   ?>
	<!-- Pe7 CSS -->
	<link rel="stylesheet" href="assets/plugins/icons/weather/weathericons.css">
	<?php } ?>

	<?php  if ($page == 'ui-toasts.php') {   ?>
		<!-- Toatr CSS -->		
        <link rel="stylesheet" href="assets/plugins//toastr/toatr.css">
		<?php } ?>
	<?php  if ($page == 'icon-typicon.php') {   ?>
		<!-- Pe7 CSS -->
		<link rel="stylesheet" href="assets/plugins/icons/typicons/typicons.css">
		<?php } ?>
		<?php  if ($page == 'icon-flag.php') {   ?>
		<!-- Pe7 CSS -->
		<link rel="stylesheet" href="assets/plugins/icons/flags/flags.css">
		<?php } ?>
	<?php  if ($page == 'chart-c3.php') {   ?>
	<!-- Main CSS -->
	<link rel="stylesheet" href="assets/plugins/c3-chart/c3.min.css">
        <link rel="stylesheet" href="assets/css/style.css">
		<?php } ?>

		<?php  if ($page == 'ui-rangeslider.php') {   ?>
		<!-- Rangeslider CSS -->
        <link rel="stylesheet" href="assets/plugins/ion-rangeslider/css/ion.rangeSlider.min.css">
		<?php } ?>
		<?php  if ($page == 'ui-lightbox.php') {   ?>
		<!-- Lightbox CSS -->
        <link rel="stylesheet" href="assets/plugins/lightbox/glightbox.min.css">
		<?php } ?>
		<?php  if ($page == 'form-select2.php'||$page == 'ui-clipboard.php') {   ?>
		<!-- Select2 CSS -->
		<link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
		<?php } ?>

		<?php  if ($page == 'icon-feather.php'||$page=='employee-dashboard.php') {   ?>
		<!-- Feather CSS -->
		<link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">
		<?php } ?>
		<?php  if ($page == 'icon-ionic.php') {   ?>
		<!-- Ionic CSS -->
		<link rel="stylesheet" href="assets/plugins/icons/ionic/ionicons.css">
		<?php } ?>
		<?php  if ($page == 'icon-simpleline.php') {   ?>
		 <!-- Simpleline CSS -->
		 <link rel="stylesheet" href="assets/plugins/simpleline/simple-line-icons.css">
		 <?php } ?>
		<?php  if ($page == 'icon-material.php') {   ?>
			<!-- Material CSS -->
			<link rel="stylesheet" href="assets/plugins/material/materialdesignicons.css">
			<?php } ?>
			<?php  if ($page == 'icon-themify.php') {   ?>
			<!-- Themify CSS -->
          <link rel="stylesheet" href="assets/plugins/icons/themify/themify.css" >
          <?php } ?>
			<?php  if ($page == 'icon-pe7.php') {   ?>
			<!-- Pe7 CSS -->
		<link rel="stylesheet" href="assets/plugins/icons/pe7/pe-icon-7.css">
		<?php } ?>
	<?php  if ($page == 'admin-dashboard.php'||$page=='leads-dashboard.php'||$page=='deals-dashboard.php'||$page=='assets-category.php'||$page=='assets-new.php') {   ?>
	<!-- Chart CSS -->
	<link rel="stylesheet" href="assets/plugins/morris/morris.css">
	<?php  if ($page == 'ui-scrollbar.php') {   ?>
	<link rel="stylesheet" href="assets/plugins/scrollbar/scroll.min.css">
	<?php }?>

	<?php  if ($page=='leads-dashboard.php'||$page=='deals.php'||$page=='deals-details.php'||$page=='deals-kanban.php'||$page=='deals-dashboard.php'||$page=='assets-category.php'||$page=='assets-details.php'||$page=='assets-new.php'||$page=='leads.php'||$page=='activities.php'||$page=='pipeline.php') {   ?>
	<!-- Daterangepikcer CSS -->
	<link rel="stylesheet" href="assets/plugins/daterangepicker/daterangepicker.css">

	<?php }?>
	<?php } if ($page == 'ui-ribbon.php' || $page == 'events.php' || $page == 'employees.php' || $page == 'employees-list.php' || $page == 'leaves.php' || $page == 'leaves-employee.php' || $page == 'leave-settings.php' || $page == 'attendance.php' || $page == 'attendance-employee.php' || $page == 'designations.php' || $page == 'timesheet.php' || $page == 'shift-scheduling.php' || $page == 'shift-list.php' || $page == 'overtime.php'|| $page == 'clients.php' || $page == 'clients-list.php' || $page == 'client-profile.php' || $page == 'projects.php' || $page == 'project-list.php' || $page == 'project-view.php' || $page == 'tasks.php' || $page == 'task-board.php' || $page == 'tickets.php' || $page == 'ticket-view.php' || $page == 'estimates.php' || $page == 'create-estimate.php' || $page == 'edit-estimate.php' || $page == 'invoices.php' || $page == 'create-invoice.php' || $page == 'edit-invoice.php' || $page == 'expenses.php'|| $page == 'provident-fund.php' || $page == 'taxes.php' || $page == 'salary.php' || $page == 'payroll-items.php' || $page == 'policies.php' || $page == 'expense-reports.php' || $page == 'invoice-reports.php' || $page == 'payments-reports.php' || $page == 'project-reports.php' || $page == 'task-reports.php' || $page == 'user-reports.php' || $page == 'employee-reports.php' || $page == 'payslip-reports.php'  || $page == 'leave-reports.php' || $page == 'daily-reports.php' || $page == 'performance-indicator.php'|| $page == 'performance-indicator.php' || $page == 'performance-appraisal.php' || $page == 'goal-tracking.php' || $page == 'goal-type.php' || $page == 'training.php' || $page == 'trainers.php' || $page == 'training-type.php' || $page == 'promotion.php' || $page == 'resignation.php' || $page == 'termination.php' || $page == 'assets.php' || $page == 'user-all-jobs.php' || $page == 'saved-jobs.php' || $page == 'applied-jobs.php' || $page == 'job-details.php' || $page == 'job-apptitude.php' || $page == 'questions.php' || $page == 'offered-jobs.php' || $page == 'visited-jobs.php' || $page == 'archived-jobs.php' || $page == 'jobs.php' || $page == 'job-applicants.php' || $page == 'manage-resumes.php' || $page == 'shortlist-candidates.php' || $page == 'interview-questions.php' || $page == 'offer_approvals.php' || $page == 'experiance-level.php' || $page == 'candidates.php' || $page == 'schedule-timing.php' || $page == 'aptitude-result.php' || $page == 'users.php' || $page == 'settings.php' || $page == 'localization.php' || $page == 'email-settings.php' || $page == 'performance-setting.php' || $page == 'approval-setting.php' || $page == 'toxbox-setting.php' || $page == 'cron-setting.php' || $page == 'profile.php' || $page == 'subscriptions.php' || $page == 'subscribed-companies.php' || $page == 'components.php' || $page == 'form-horizontal.php' || $page == 'form-vertical.php'
	 || $page == 'companies-grid.php' || $page == 'companies.php' || $page == 'company-details.php' || $page == 'contact-details.php' || $page == 'contact-grid.php' || $page == 'contact-list.php'|| $page=='user-asset-details.php' ||$page=='ticket-details.php'||$page=='leads-details.php'||$page=='leads-kanban.php'||$page=='leads-dashboard.php'||$page=='deals.php'||$page=='deals-details.php'||$page=='deals-kanban.php'||$page=='deals-dashboard.php'||$page=='analytics.php'||$page=='assets-category.php'||$page=='assets-details.php'||$page=='assets-new.php'||$page=='assets-reports.php'||$page=='leads.php'||$page=='activities.php'||$page=='pipeline.php' || $page =='attendance-reports.php') { ?>
	<!-- Select2 CSS -->
	<link rel="stylesheet" href="assets/css/select2.min.css">

	<?php } if ($page == 'events.php' || $page == 'employees.php' || $page == 'employees-list.php' || $page == 'holidays.php' || $page == 'leaves.php' || $page == 'leaves-employee.php' || $page == 'leave-settings.php' || $page == 'attendance.php' || $page == 'attendance-employee.php' || $page == 'timesheet.php' || $page == 'shift-scheduling.php' || $page == 'shift-list.php' || $page == 'overtime.php'|| $page == 'clients.php' || $page == 'projects.php' || $page == 'project-list.php' || $page == 'project-view.php' || $page == 'tasks.php' || $page == 'task-board.php' || $page == 'tickets.php' || $page == 'estimates.php' || $page == 'create-estimate.php' || $page == 'edit-estimate.php' || $page == 'invoices.php' || $page == 'create-invoice.php' || $page == 'edit-invoice.php' || $page == 'expenses.php' || $page == 'categories.php' || $page == 'sub-category.php' || $page == 'budgets.php' || $page == 'budget-expenses.php' || $page == 'budget-revenues.php' || $page == 'salary.php' || $page == 'payroll-items.php' || $page == 'expense-reports.php' || $page == 'invoice-reports.php' || $page == 'payments-reports.php' || $page == 'employee-reports.php' || $page == 'payslip-reports.php' || $page == 'leave-reports.php' || $page == 'daily-reports.php' || $page == 'performance-indicator.php' || $page == 'performance-appraisal.php' || $page == 'goal-tracking.php' || $page == 'training.php' || $page == 'promotion.php' || $page == 'resignation.php' || $page == 'termination.php' || $page == 'assets.php' || $page == 'job-details.php' || $page == 'jobs.php' || $page == 'job-applicants.php' || $page == 'manage-resumes.php' || $page == 'shortlist-candidates.php' || $page == 'interview-questions.php' || $page == 'offer_approvals.php' || $page == 'experiance-level.php' || $page == 'candidates.php' || $page == 'schedule-timing.php' || $page == 'aptitude-result.php' || $page == 'users.php' || $page == 'profile.php'
	 || $page == 'components.php' || $page == 'user-asset-details.php'|| $page == 'leads-dashboard.php'|| $page == 'deals.php'|| $page == 'deals-details.php'|| $page == 'deals-kanban.php'|| $page == 'deals-dashboard.php'|| $page == 'analytics.php'|| $page == 'assets-category.php'|| $page == 'assets-new.php'|| $page == 'assets-reports.php'|| $page == 'leads.php'|| $page == 'activities.php'|| $page == 'pipeline.php'|| $page == 'apptitude-result.php') { ?>
	<!-- Datetimepicker CSS -->
	<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">

	<?php } if ($page == 'events.php') { ?>
	<!-- Calendar CSS -->
	<link rel="stylesheet" href="assets/css/fullcalendar.min.css">
	<?php } if ($page == 'employee-dashboard.php') { ?>
	 <!-- Owl carousel CSS -->
	 <link rel="stylesheet" href="assets/css/owl.carousel.min.css">

	<?php } if ($page == 'employees-list.php' || $page == 'leaves.php' || $page == 'leaves-employee.php' || $page == 'departments.php' || $page == 'designations.php' || $page == 'timesheet.php' || $page == 'shift-scheduling.php' || $page == 'shift-list.php' || $page == 'overtime.php'|| $page == 'clients.php' || $page == 'clients-list.php' || $page == 'project-list.php' || $page == 'leads.php' || $page == 'tickets.php' || $page == 'payments.php' || $page == 'expenses.php'|| $page == 'provident-fund.php' || $page == 'salary.php' || $page == 'payroll-items.php' || $page == 'policies.php' || $page == 'expense-reports.php' || $page == 'invoice-reports.php' || $page == 'payments-reports.php' || $page == 'project-reports.php' || $page == 'task-reports.php' || $page == 'user-reports.php' || $page == 'employee-reports.php' || $page == 'payslip-reports.php' || $page == 'leave-reports.php' || $page == 'daily-reports.php' || $page == 'performance-indicator.php' || $page == 'performance-appraisal.php' || $page == 'goal-tracking.php' || $page == 'goal-type.php' || $page == 'training.php' || $page == 'trainers.php' || $page == 'training-type.php' || $page == 'promotion.php' || $page == 'resignation.php' || $page == 'termination.php' || $page == 'assets.php' || $page == 'user-all-jobs.php' || $page == 'saved-jobs.php' || $page == 'applied-jobs.php' || $page == 'job-details.php' || $page == 'job-details.php' || $page == 'job-apptitude.php' || $page == 'questions.php' || $page == 'offered-jobs.php' || $page == 'visited-jobs.php' || $page == 'archived-jobs.php' || $page == 'jobs.php' || $page == 'job-applicants.php' || $page == 'manage-resumes.php' || $page == 'shortlist-candidates.php' || $page == 'interview-questions.php' || $page == 'offer_approvals.php' || $page == 'experiance-level.php' || $page == 'candidates.php' || $page == 'schedule-timing.php' || $page == 'aptitude-result.php' || $page == 'users.php' || $page == 'leave-type.php' || $page == 'subscribed-companies.php' || $page == 'data-tables.php'|| $page == 'leads-kanban.php'|| $page == 'deals.php'|| $page == 'deals-details.php'|| $page == 'deals-kanban.php' || $page == 'companies-grid.php' || $page == 'companies.php' || $page == 'company-details.php' || $page == 'contact-details.php' || $page == 'contact-grid.php' || $page == 'contact-list.php'|| $page == 'assets-category.php'|| $page == 'assets-details.php'|| $page == 'assets-new.php'|| $page == 'assets-reports.php'|| $page == 'leads.php'|| $page == 'activities.php'|| $page == 'pipeline.php'|| $page == 'apptitude-result.php'|| $page == 'attendance-reports.php') { ?>
	<!-- Datatable CSS -->
	<link rel="stylesheet" href="assets/css/dataTables.bootstrap4.min.css">

	

	<?php } if ($page == 'projects.php' || $page == 'project-list.php' || $page == 'tasks.php'|| $page == 'compose.php') { ?>
<!-- Ck Editor -->
	<link rel="stylesheet" href="assets/css/ckeditor.css">

	<?php } if ($page == 'profile.php' || $page == 'components.php'||$page == 'leave-settings.php'||$page=='leads-kanban'||$page=='deals'||$page=='deals-details'||$page=='deals-kanban'|| $page == 'company-details.php' || $page == 'companies-grid.php' || $page == 'companies.php' || $page == 'contact-details.php' || $page == 'contact-grid.php' || $page == 'contact-list.php' || $page == 'analytics.php'|| $page == 'leads.php'|| $page == 'activities.php'|| $page == 'pipeline.php') {?>
<!-- Tagsinput CSS -->
	<link rel="stylesheet" href="assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css">
	<?php } ?>
	
	<?php if ($page !== 'settings.php' ) { ?>
		<!-- Daterangepikcer CSS -->
		<link rel="stylesheet" href="assets/plugins/daterangepicker/daterangepicker.css">

		<!-- Datetimepicker CSS -->
		<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">
		<?php } ?>

		<!-- Main CSS -->
	<link rel="stylesheet" href="assets/css/style.css">