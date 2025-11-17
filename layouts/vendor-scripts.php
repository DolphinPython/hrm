<?php
    $link = $_SERVER['PHP_SELF'];
    $link_array = explode('/',$link);
    $page = end($link_array);
?>
	<!-- jQuery -->
	<script src="assets/js/jquery-3.7.1.min.js"></script>

	<!-- Bootstrap Core JS -->
	<script src="assets/js/bootstrap.bundle.min.js"></script>
	
		<!-- Feather Icon JS -->
		<script src="assets/js/feather.min.js"></script>
		<!-- Slimscroll JS -->
	<script src="assets/js/jquery.slimscroll.min.js"></script>
	
	<!-- Theme Settings JS -->
	<?php if ($page !== 'index.php'&& $page !== 'register.php'&& $page !== 'otp.php' && $page !== 'forgot-password.php' && $page !== 'lock-screen.php'  && $page !== 'error-404.php'  && $page !== 'error-500.php' && $page !== 'coming-soon.php' && $page !== 'under-maintenance.php' && $page !== 'change-password.php'&& $page !== 'job-list.php'&& $page !== 'job-view.php') {?>
	<script src="assets/js/layout.js"></script>
	<script src="assets/js/theme-settings.js"></script>
	<script src="assets/js/greedynav.js"></script>
	<?php } ?>

	<?php  if ($page == 'ui-clipboard.php') {   ?>
		<!-- Clipboard JS -->
		<script src="assets/plugins/clipboard/clipboard.min.js"></script>
		<?php } ?>
		<?php  if ($page == 'ui-drag-drop.php') {   ?>
		<!-- Dragula JS -->
		<script src="assets/plugins/dragula/js/dragula.min.js"></script>
		<script src="assets/plugins/dragula/js/drag-drop.min.js"></script>
		<script src="assets/plugins/dragula/js/draggable-cards.js"></script>
		<?php } ?>
		<?php  if ($page == 'ui-rating.php') {   ?>
		<!-- Raty JS -->
		<script src="assets/plugins/raty/jquery.raty.js"></script>
		<script src="assets/plugins/raty/custom.raty.js"></script>
		<?php } ?>
		<?php  if ($page == 'ui-lightbox.php') {   ?>
		<!-- lightbox JS -->
		<script src="assets/plugins/lightbox/glightbox.min.js"></script>
		<script src="assets/plugins/lightbox/lightbox.js"></script>
		<?php } ?>
		<?php  if ($page == 'ui-text-editor.php'||$page=='leads-details.php'||$page=='deals-details.php'|| $page == 'company-details.php' || $page == 'contact-details.php') {   ?>
			<!-- Summernote JS -->
			<script src="assets/plugins/summernote/summernote-bs4.min.js"></script>
			<?php } ?>
			<?php  if ($page == 'ui-counter.php') {   ?>
			<!-- Stickynote JS -->
		<script src="assets/plugins/countup/jquery.counterup.min.js"></script>
		<script src="assets/plugins/countup/jquery.waypoints.min.js"></script>
		<script src="assets/plugins/countup/jquery.missofis-countdown.js"></script>
		<?php } ?>
		<?php  if ($page == 'ui-scrollbar.php') {   ?>
			<!-- Plyr JS -->
			<script src="assets/plugins/scrollbar/scrollbar.min.js"></script>
		<script src="assets/plugins/scrollbar/custom-scroll.js"></script>
		<?php } ?>
		<?php  if ($page == 'ui-notification.php') {   ?>
		<!-- Alertify JS -->
		<script src="assets/plugins/alertify/alertify.min.js"></script>
		<script src="assets/plugins/alertify/custom-alertify.min.js"></script>
		<?php } ?>
		<?php  if ($page == 'ui-stickynote.php') {   ?>
			<!-- Stickynote JS -->
			<script src="assets/js/jquery-ui.min.js"></script>
		<script src="assets/plugins/stickynote/sticky.js"></script>
		<?php } ?>
		<?php  if ($page == 'ticket-details.php') {   ?>
		<script src="assets/plugins/theia-sticky-sidebar/ResizeSensor.js"></script>
		<script src="assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js"></script>
		<?php } ?>
		<?php  if ($page == 'ui-rangeslider.php') {   ?>
	<!-- Rangeslider JS -->
	<script src="assets/plugins/ion-rangeslider/js/ion.rangeSlider.min.js"></script>
		<script src="assets/plugins/ion-rangeslider/js/custom-rangeslider.js"></script>
		<?php } ?>
		<?php  if ($page == 'ui-sweetalerts.php') {   ?>
		<!-- Sweetalert 2 -->
		<script src="assets/plugins/sweetalert/sweetalert2.all.min.js"></script>
		<script src="assets/plugins/sweetalert/sweetalerts.min.js"></script>
		<?php } ?>
		<?php  if ($page == 'ui-toasts.php') {   ?>
		<!-- Mask JS -->
		<script src="assets/plugins/toastr/toastr.min.js"></script>
		<script src="assets/plugins/toastr/toastr.js"></script>
		<?php } ?>
	<?php  if ($page == 'form-fileupload.php') {   ?>
		<!-- Fileupload JS -->
		<script src="assets/plugins/fileupload/fileupload.min.js"></script>
		<?php } ?>

		<?php  if ($page == 'icon-feather.php'||$page == 'icon-flag.php'||$page == 'clipboard.php'||$page == 'icon-typicon.php'||$page == 'icon-weather.php'||$page == 'icon-themify.php'||$page == 'icon-simpleline.php'||$page == 'icon-ionic.php'||$page == 'icon-material'||$page == 'icon-pe7') {   ?>
			<!-- Feather Icon JS -->
			<script src="assets/js/feather.min.js"></script>
		 <?php } ?>
			
	<?php  if ($page == 'chart-apex.php'||$page=='leads-dashboard.php'||$page=='deals-dashboard.php'||$page=='analytics.php'||$page=='employee-dashboard.php') {   ?>
		<!-- Apexchart JS -->
		<script src="assets/plugins/apexchart/apexcharts.min.js"></script>
		<script src="assets/plugins/apexchart/chart-data.js"></script>
		<?php } ?>
		<?php  if ($page == 'chart-js.php') {   ?>
			<!-- Chart JS -->
			<script src="assets/plugins/chartjs/chart.min.js"></script>
		<script src="assets/plugins/chartjs/chart-data.js"></script>
		<?php } ?>
		<?php  if ($page == 'chart-morris.php') {   ?>
		<!-- Chart JS -->
		<script src="assets/plugins/morris/raphael-min.js"></script>
		<script src="assets/plugins/morris/morris.min.js"></script>
		<script src="assets/plugins/morris/chart-data.js"></script>
		<?php } ?>
		<?php  if ($page == 'chart-flot.php') {   ?>
		<!-- Chart JS -->
		<script src="assets/plugins/flot/jquery.flot.js"></script>
		<script src="assets/plugins/flot/jquery.flot.fillbetween.js"></script>
		<script src="assets/plugins/flot/jquery.flot.pie.js"></script>
		<script src="assets/plugins/flot/chart-data.js"></script>
		<?php } ?>
		<?php  if ($page == 'chart-c3.php') {   ?>
		<!-- Chart JS -->
		<script src="assets/plugins/c3-chart/d3.v5.min.js"></script>
		<script src="assets/plugins/c3-chart/c3.min.js"></script>
		<script src="assets/plugins/c3-chart/chart-data.js"></script>
		<?php } ?>
		<?php  if ($page == 'icon-fontawesome.php'||$page == 'employee-dashboard.php') {   ?>
		<!-- Feather Icon JS -->
		<script src="assets/js/feather.min.js"></script>
		<?php } ?>
		<?php  if ($page == 'chart-peity.php') {   ?>
			<!-- Chart JS -->
			<script src="assets/plugins/peity/jquery.peity.min.js"></script>
		<script src="assets/plugins/peity/chart-data.js"></script>
		<?php } ?>
	<?php  if ($page == 'horizontal-timeline.php') {   ?>
		<!-- Timeline JS -->
		<script src="assets/plugins/timeline/horizontal-timeline.js"></script>
		<?php } ?>
	<?php  if ($page == 'ui-scrollbar.php') {   ?>

	<link rel="stylesheet" href="assets/plugins/scrollbar/scroll.min.css">
	<?php } ?>
	<?php  if ($page == 'form-select2.php') {   ?>
		<!-- Select 2 -->
		<script src="assets/plugins/select2/js/select2.min.js"></script>
		<script src="assets/plugins/select2/js/custom-select.js"></script>
	<?php } ?>

	<?php  if ($page == 'task-board.php'||$page=='leads-kanban.php'||$page=='deals-kanban.php') {   ?>
		<script src="assets/js/jquery-ui.min.js"></script>
		<script src="assets/js/jquery.ui.touch-punch.min.js"></script>
		<?php } ?>
	<?php  if ($page == 'admin-dashboard.php') {   ?>
	<!-- Chart JS -->
	<script src="assets/plugins/morris/morris.min.js"></script>
	<script src="assets/plugins/raphael/raphael.min.js"></script>
	<script src="assets/js/chart_pankaj.js"></script>
	<script src="assets/js/greedynav.js"></script>

	

	<?php } if ($page == 'events.php' || $page == 'employees.php' || $page == 'employees-list.php' || $page == 'leaves.php' || $page == 'leaves-employee.php' || $page == 'leave-settings.php' || $page == 'attendance.php' || $page == 'attendance-employee.php' || $page == 'designations.php' || $page == 'timesheet.php' || $page == 'shift-scheduling.php' || $page == 'shift-list.php' 
	|| $page == 'overtime.php' || $page == 'clients.php' || $page == 'clients-list.php' || $page == 'client-profile.php' || $page == 'projects.php' || $page == 'project-list.php' || $page == 'project-view.php' || $page == 'tasks.php' || $page == 'task-board.php' || $page == 'tickets.php' || $page == 'ticket-view.php' || $page == 'estimates.php' || $page == 'create-estimate.php' || $page == 'edit-estimate.php' || $page == 'invoices.php' || $page == 'create-invoice.php' || $page == 'edit-invoice.php' || $page == 'expenses.php' || $page == 'provident-fund.php' || $page == 'taxes.php' || $page == 'salary.php' || $page == 'payroll-items.php' || $page == 'policies.php' || $page == 'expense-reports.php' || $page == 'invoice-reports.php' || $page == 'payments-reports.php' || $page == 'project-reports.php' || $page == 'task-reports.php' || $page == 'user-reports.php' || $page == 'employee-reports.php' || $page == 'payslip-reports.php'  || $page == 'leave-reports.php' || $page == 'daily-reports.php' || $page == 'performance-indicator.php' || $page == 'performance.php' || $page == 'performance-appraisal.php' || $page == 'goal-tracking.php' || $page == 'goal-type.php' || $page == 'training.php' || $page == 'trainers.php' || $page == 'training-type.php' || $page == 'promotion.php' || $page == 'resignation.php' || $page == 'termination.php' || $page == 'assets.php' || $page == 'user-all-jobs.php' || $page == 'saved-jobs.php' || $page == 'applied-jobs.php' || $page == 'job-details.php' || $page == 'job-apptitude.php' || $page == 'questions.php' || $page == 'offered-jobs.php' || $page == 'visited-jobs.php' || $page == 'archived-jobs.php' || $page == 'jobs.php' || $page == 'job-applicants.php' || $page == 'manage-resumes.php' || $page == 'shortlist-candidates.php' || $page == 'interview-questions.php' || $page == 'offer_approvals.php' || $page == 'experiance-level.php' || $page == 'candidates.php' || $page == 'schedule-timing.php' || $page == 'aptitude-result.php' || $page == 'users.php' || $page == 'settings.php' || $page == 'localization.php' || $page == 'email-settings.php' || $page == 'performance-setting.php' || $page == 'approval-setting.php' || $page == 'toxbox-setting.php' || $page == 'cron-setting.php' || $page == 'profile.php' || $page == 'subscriptions.php' || $page == 'subscribed-companies.php' || $page == 'components.php' || $page == 'form-horizontal.php' || $page == 'form-vertical.php' ||$page=='user-asset-details.php'
	 ||$page =='ticket-details.php'|| $page=='leads-details.php'||$page=='leads-kanban.php'||$page=='leads-dashboard.php'||$page=='deals.php'||$page=='deals-details.php'||$page=='deals-kanban.php'||$page=='deals-dashboard.php'|| $page == 'companies-grid.php' || $page == 'companies.php' || $page == 'company-details.php' || $page == 'contact-details.php' || $page == 'contact-grid.php' || $page == 'contact-list.php'|| $page == 'analytics.php'|| $page == 'assets-category.php'|| $page == 'assets-details.php'|| $page == 'assets-new.php'|| $page == 'assets-reports.php'|| $page == 'leads.php'|| $page == 'activities.php'|| $page == 'pipeline.php'|| $page == 'sub-category.php'|| $page == 'attendance-reports.php') { ?>
<!-- Select2 JS -->
	<script src="assets/js/select2.min.js"></script>

	<?php } if ($page == 'events.php' || $page == 'employees.php' || $page == 'employees-list.php' || $page == 'holidays.php' || $page == 'leaves.php' || $page == 'leaves-employee.php' || $page == 'leave-settings.php' || $page == 'attendance.php' || $page == 'attendance-employee.php' || $page == 'timesheet.php' || $page == 'shift-scheduling.php' || $page == 'shift-list.php' || $page == 'overtime.php' || $page == 'projects.php' || $page == 'project-list.php' || $page == 'project-view.php' || $page == 'tasks.php' || $page == 'task-board.php' || $page == 'tickets.php' || $page == 'estimates.php' || $page == 'create-estimate.php' || $page == 'edit-estimate.php' || $page == 'invoices.php' || $page == 'create-invoice.php' || $page == 'edit-invoice.php' || $page == 'expenses.php' || $page == 'categories.php' || $page == 'sub-category.php' || $page == 'budgets.php' || $page == 'budget-expenses.php' || $page == 'budget-revenues.php' || $page == 'salary.php' || $page == 'payroll-items.php' || $page == 'expense-reports.php' || $page == 'expense-reports.php' || $page == 'payments-reports.php' || $page == 'employee-reports.php' || $page == 'payslip-reports.php' || $page == 'leave-reports.php' || $page == 'daily-reports.php' || $page == 'performance-indicator.php' || $page == 'performance-appraisal.php' || $page == 'goal-tracking.php' 
	|| $page == 'training.php' || $page == 'promotion.php' || $page == 'resignation.php' || $page == 'termination.php' || $page == 'assets.php' || $page == 'job-details.php' || $page == 'jobs.php' || $page == 'job-applicants.php' || $page == 'manage-resumes.php' || $page == 'shortlist-candidates.php' || $page == 'interview-questions.php' || $page == 'offer_approvals.php' || $page == 'experiance-level.php' || $page == 'candidates.php' || $page == 'schedule-timing.php' || $page == 'aptitude-result.php' || $page == 'users.php' || $page == 'profile.php' || $page == 'components.php'|| $page == 'invoice-reports.php' ||$page == 'user-asset-details.php'||$page == 'leads-details.php'||$page=='leads-kanban.php'||$page=='leads-dashboard.php'||$page=='deals.php'||$page=='deals-details.php'||$page=='deals-kanban.php'||$page=='deals-dashboard.php'|| $page == 'companies-grid.php' || $page == 'companies.php' || $page == 'company-details.php' || $page == 'contact-details.php' || $page == 'contact-grid.php' || $page == 'contact-list.php'|| $page == 'analytics.php'|| $page == 'assets-category.php'|| $page == 'assets-new.php'|| $page == 'assets-reports.php'|| $page == 'leads.php'|| $page == 'activities.php'|| $page == 'pipeline.php'|| $page == 'compose.php'|| $page == 'sub-category.php') { ?>
<!-- Datetimepicker JS -->
	<script src="assets/js/moment.min.js"></script>
	<script src="assets/js/bootstrap-datetimepicker.min.js"></script>

	<?php } if ($page == 'events.php' || $page == 'employees.php' || $page == 'employees-list.php') { ?>
<!-- Calendar JS -->
	<script src="assets/js/jquery-ui.min.js"></script>
	<script src="assets/js/fullcalendar.min.js"></script>
	<script src="assets/js/jquery.fullcalendar.js"></script>

	<?php } if ($page == 'employee-dashboard.php') { ?>
	<!-- Owl Carousel JS -->
	<script src="assets/js/owl.carousel.min.js"></script>


	<?php } if ($page == 'employees-list.php' || $page == 'leaves.php' || $page == 'leaves-employee.php' || $page == 'departments.php' || $page == 'designations.php' || $page == 'timesheet.php' || $page == 'shift-scheduling.php' || $page == 'shift-list.php' || $page == 'overtime.php' || $page == 'clients-list.php' || $page == 'project-list.php' || $page == 'leads.php' || $page == 'tickets.php' || $page == 'payments.php' || $page == 'expenses.php' || $page == 'provident-fund.php' || $page == 'salary.php' || $page == 'payroll-items.php' || $page == 'policies.php' || $page == 'expense-reports.php' || $page == 'expense-reports.php' || $page == 'payments-reports.php' || $page == 'project-reports.php' || $page == 'task-reports.php' || $page == 'user-reports.php' || $page == 'employee-reports.php' || $page == 'payslip-reports.php'  || $page == 'leave-reports.php' || $page == 'daily-reports.php' || $page == 'performance-indicator.php' || $page == 'performance-appraisal.php' || $page == 'goal-tracking.php' || $page == 'goal-type.php' || $page == 'training.php' || $page == 'trainers.php' || $page == 'training-type.php' || $page == 'promotion.php' || $page == 'resignation.php' || $page == 'termination.php' || $page == 'assets.php' || $page == 'user-all-jobs.php' || $page == 'saved-jobs.php' || $page == 'applied-jobs.php' || $page == 'job-details.php' || $page == 'job-apptitude.php' || $page == 'questions.php' || $page == 'offered-jobs.php' || $page == 'visited-jobs.php' || $page == 'archived-jobs.php' || $page == 'jobs.php' || $page == 'job-applicants.php' || $page == 'manage-resumes.php' || $page == 'shortlist-candidates.php' || $page == 'interview-questions.php' || $page == 'offer_approvals.php' || $page == 'experiance-level.php' || $page == 'candidates.php' || $page == 'schedule-timing.php'  || $page == 'users.php' || $page == 'leave-type.php' || $page == 'subscribed-companies.php' || $page == 'data-tables.php'|| $page == 'invoice-reports.php'|| $page == 'leads-kanban.php'|| $page == 'deals.php'|| $page == 'deals-details.php'|| $page == 'deals-kanban.php'|| $page == 'companies-grid.php' || $page == 'companies.php' || $page == 'contact-list.php'|| $page == 'assets-category.php'|| $page == 'assets-details.php'|| $page == 'assets-new.php'|| $page == 'assets-reports.php'|| $page == 'leads.php'|| $page == 'activities.php'|| $page == 'pipeline.php'|| $page == 'apptitude-result.php' || $page == 'profile.php'|| $page == 'attendance-reports.php') { ?>
<!-- Datatable JS -->
	<script src="assets/js/jquery.dataTables.min.js"></script>
	<script src="assets/js/dataTables.bootstrap4.min.js"></script>
	<?php } if ($page == 'leads-details.php'||$page=='leads-kanban.php'||$page=='leads-dashboard.php'||$page=='deals.php'||$page=='deals-details.php'||$page=='deals-kanban.php'||$page=='deals-dashboard.php'||$page=='analytics.php'||$page=='assets-category.php'||$page=='assets-new.php'||$page=='leads.php'||$page=='activities.php') { ?>
	<!-- Daterangepikcer JS -->
	<script src="assets/js/moment.min.js"></script>
	<script src="assets/plugins/daterangepicker/daterangepicker.js"></script>
		

	<?php } if ($page == 'leave-settings.php') { ?>
<!-- Multiselect JS -->
	<script src="assets/js/multiselect.min.js"></script>
	

	<?php } if ($page == 'client-profile.php' || $page == 'project-view.php' || $page == 'tasks.php') { ?>
<!-- Task JS -->
	<script src="assets/js/task.js"></script>

	<?php } if ($page == 'projects.php' || $page == 'project-list.php' || $page == 'tasks.php'|| $page == 'compose.php') { ?>
<!-- Ck Editor JS -->
	<script src="assets/js/ckeditor.js"></script>

	<?php } if ($page == 'user-dashboard.php' || $page == 'jobs-dashboard.php') { ?>
<!-- Chart JS -->
	<script src="assets/js/Chart.min.js"></script>
	<script src="assets/js/line-chart.js"></script>

	<?php } if ($page == 'profile.php' || $page == 'components.php'||$page=='leads-kanban'||$page=='deals-kanban') { ?>
<!-- Tagsinput JS -->
	<script src="assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>

	<?php } if ($page == 'components.php') {  ?>
<!-- Sticky-kit -->
	<script src="assets/plugins/sticky-kit-master/dist/sticky-kit.min.js"></script>

	<?php } if ($page == 'form-mask.php') { ?>
<!-- Mask JS -->
	<script src="assets/js/jquery.maskedinput.min.js"></script>
    <script src="assets/js/mask.js"></script>

    <?php } if ($page == 'form-validation.php') { ?>
<!-- Form Validation JS -->
    <script src="assets/js/form-validation.js"></script>
    
	<?php } if ($page == 'companies-grid.php' || $page == 'companies.php' || $page == 'company-details.php' || $page == 'contact-details.php' || $page == 'contact-grid.php' || $page == 'contact-list.php'|| $page == 'analytics'|| $page == 'assets-category'|| $page == 'assets-new'|| $page == 'leads.php'|| $page == 'activities.php'|| $page == 'pipeline.php') { ?>
				<!-- Daterangepikcer JS -->
			<script src="assets/js/moment.min.js"></script>
		<script src="assets/plugins/daterangepicker/daterangepicker.js"></script>
		
				<!-- Bootstrap Tagsinput JS -->
				<script src="assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
		<?php } ?>
		<!-- Custom JS -->
	<script src="assets/js/app.js"></script>