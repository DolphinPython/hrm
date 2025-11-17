<?php
    $link = $_SERVER['PHP_SELF'];
    $link_array = explode('/',$link);
    $page = end($link_array);
?>

<!-- Two Col Sidebar -->
<div class="two-col-bar" id="two-col-bar">
    <div class="sidebar sidebar-twocol" id="navbar-nav">
        <div class="sidebar-left slimscroll">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <a class="nav-link <?php echo ($page == 'admin-dashboard.php'|| $page =='employee-dashboard.php'|| $page =='deals-dashboard.php'|| $page =='leads-dashboard.php') ?'active':'';?>" id="v-pills-dashboard-tab" title="Dashboard" data-bs-toggle="pill" href="#v-pills-dashboard" role="tab" aria-controls="v-pills-dashboard" aria-selected="true">
                    <span class="material-icons-outlined">
                        home
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'chat.php' || $page == 'voice-call.php' || $page == 'video-call.php' || $page == 'incoming-call.php' || $page == 'outgoing-call.php' || $page == 'events.php' || $page == 'inbox.php' || $page == 'compose.php' || $page == 'file-manager.php' || $page == 'mail-view.php' || $page == 'contacts.php')?'active':'';?>" id="v-pills-apps-tab" title="Apps" data-bs-toggle="pill" href="#v-pills-apps" role="tab" aria-controls="v-pills-apps" aria-selected="false">
                    <span class="material-icons-outlined">
                        dashboard
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'employees-list.php'|| $page =='employees.php'|| $page =='holidays.php'|| $page =='leaves.php'|| $page =='leaves-employee.php'|| $page =='leave-settings.php'|| $page =='attendance.php'|| $page =='attendance-employee.php'|| $page =='departments.php'|| $page =='designations.php'|| $page =='timesheet.php'|| $page =='shift-scheduling.php'|| $page =='overtime.php'|| $page =='shift-list') ?'active':'';?>" id="v-pills-employees-tab" title="Employees" data-bs-toggle="pill" href="#v-pills-employees" role="tab" aria-controls="v-pills-employees" aria-selected="false">
                    <span class="material-icons-outlined">
                        people
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'clients.php'|| $page =='clients-list.php') ?'active':'';?>" id="v-pills-clients-tab" title="Clients" data-bs-toggle="pill" href="#v-pills-clients" role="tab" aria-controls="v-pills-clients" aria-selected="false">
                    <span class="material-icons-outlined">
                        person
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'projects.php'|| $page =='tasks.php'|| $page =='task-board.php'|| $page =='project-list.php') ?'active':'';?>" id="v-pills-projects-tab" title="Projects" data-bs-toggle="pill" href="#v-pills-projects" role="tab" aria-controls="v-pills-projects" aria-selected="false">
                    <span class="material-icons-outlined">
                        topic
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'contact-list.php'|| $page =='companies.php'|| $page =='deals.php'|| $page =='leads.php'|| $page =='leads-details.php'|| $page =='leads-kanban.php'|| $page =='pipeline.php'|| $page =='analytics.php'|| $page =='contact-grid.php'|| $page =='contact-details.php'|| $page =='companies-grid.php'|| $page =='company-details.php'|| $page =='deals-kanban.php'|| $page =='deals-details.php') ?'active':'';?>" id="v-pills-leads-tab" title="CRM" data-bs-toggle="pill" href="#v-pills-leads" role="tab" aria-controls="v-pills-leads" aria-selected="false">
                    <span class="material-icons-outlined">
                        leaderboard
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'tickets.php'|| $page =='ticket-details') ?'active':'';?>" id="v-pills-tickets-tab" title="Tickets" data-bs-toggle="pill" href="#v-pills-tickets" role="tab" aria-controls="v-pills-tickets" aria-selected="false">
                    <span class="material-icons-outlined">
                        confirmation_number
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'estimates.php'|| $page =='invoices.php'|| $page =='payments.php'|| $page =='expenses.php'|| $page =='provident-fund.php'|| $page =='taxes.php'|| $page =='edit-estimate.php'|| $page =='create-invoice.php'|| $page =='edit-invoice.php'|| $page =='create-estimate.php') ?'active':'';?>" id="v-pills-sales-tab" title="Sales" data-bs-toggle="pill" href="#v-pills-sales" role="tab" aria-controls="v-pills-sales" aria-selected="false">
                    <span class="material-icons-outlined">
                        shopping_bag
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'categories.php'|| $page =='budgets.php'|| $page =='budget-expenses.php'|| $page =='budget-revenues.php'|| $page =='sub-category.php') ?'active':'';?>" id="v-pills-accounting-tab" title="Accounting" data-bs-toggle="pill" href="#v-pills-accounting" role="tab" aria-controls="v-pills-accounting" aria-selected="false">
                    <span class="material-icons-outlined">
                        account_balance_wallet
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'salary.php'|| $page =='salary-view.php'|| $page =='payroll-items.php') ?'active':'';?>" id="v-pills-payroll-tab" title="Payroll" data-bs-toggle="pill" href="#v-pills-payroll" role="tab" aria-controls="v-pills-payroll" aria-selected="false">
                    <span class="material-icons-outlined">
                        request_quote
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'policies.php') ?'active':'';?>" id="v-pills-policies-tab" title="Policies" data-bs-toggle="pill" href="#v-pills-policies" role="tab" aria-controls="v-pills-policies" aria-selected="false">
                    <span class="material-icons-outlined">
                        verified_user
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'expense-reports.php'|| $page =='invoice-reports.php'|| $page =='payments-reports.php'|| $page =='task-reports.php'|| $page =='user-reports.php'|| $page =='employee-reports.php'|| $page =='payslip-reports.php'|| $page =='attendance-reports.php'|| $page =='leave-reports.php'|| $page =='daily-reports.php'|| $page =='project-reports.php') ?'active':'';?>" id="v-pills-reports-tab" title="Reports" data-bs-toggle="pill" href="#v-pills-reports" role="tab" aria-controls="v-pills-reports" aria-selected="false">
                    <span class="material-icons-outlined">
                        report_gmailerrorred
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'performance-indicator.php'|| $page =='performance.php'|| $page =='performance-appraisal.php') ?'active':'';?>" id="v-pills-performance-tab" title="Performance" data-bs-toggle="pill" href="#v-pills-performance" role="tab" aria-controls="v-pills-performance" aria-selected="false">
                    <span class="material-icons-outlined">
                        shutter_speed
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'goal-tracking.php'|| $page =='goal-type.php') ?'active':'';?>" id="v-pills-goals-tab" title="Goals" data-bs-toggle="pill" href="#v-pills-goals" role="tab" aria-controls="v-pills-goals" aria-selected="false">
                    <span class="material-icons-outlined">
                        track_changes
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'training.php'|| $page =='trainers.php'|| $page =='training-type.php') ?'active':'';?>" id="v-pills-training-tab" title="Training" data-bs-toggle="pill" href="#v-pills-training" role="tab" aria-controls="v-pills-training" aria-selected="false">
                    <span class="material-icons-outlined">
                        checklist_rtl
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'promotion.php') ?'active':'';?>" id="v-pills-promotion-tab" title="Promotions" data-bs-toggle="pill" href="#v-pills-promotion" role="tab" aria-controls="v-pills-promotion" aria-selected="false">
                    <span class="material-icons-outlined">
                        auto_graph
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'resignation.php') ?'active':'';?>" id="v-pills-resignation-tab" title="Resignation" data-bs-toggle="pill" href="#v-pills-resignation" role="tab" aria-controls="v-pills-resignation" aria-selected="false">
                    <span class="material-icons-outlined">
                        do_not_disturb_alt
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'termination.php') ?'active':'';?>" id="v-pills-termination-tab" title="Termination" data-bs-toggle="pill" href="#v-pills-termination" role="tab" aria-controls="v-pills-termination" aria-selected="false">
                    <span class="material-icons-outlined">
                        indeterminate_check_box
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'assets.php') ?'active':'';?>" id="v-pills-assets-tab" title="Assets" data-bs-toggle="pill" href="#v-pills-assets" role="tab" aria-controls="v-pills-assets" aria-selected="false">
                    <span class="material-icons-outlined">
                        web_asset
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'user-dashboard.php'|| $page =='jobs-dashboard.php'|| $page =='jobs.php'|| $page =='job-applicants.php'|| $page =='manage-resumes.php'|| $page =='shortlist-candidates.php'|| $page =='interview-questions.php'|| $page =='offer_approvals.php'|| $page =='experiance-level.php'|| $page =='candidates.php'|| $page =='schedule-timing.php'|| $page =='apptitude-result.php'|| $page =='user-all-jobs.php'|| $page =='saved-jobs.php'|| $page =='applied-jobs.php'|| $page =='interviewing.php'|| $page =='offered-jobs.php'|| $page =='visited-jobs.php'|| $page =='archived-jobs.php'|| $page =='job-aptitude.php'|| $page =='questions.php') ?'active':'';?>" id="v-pills-jobs-tab" title="Jobs" data-bs-toggle="pill" href="#v-pills-jobs" role="tab" aria-controls="v-pills-jobs" aria-selected="false">
                    <span class="material-icons-outlined">
                        work_outline
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'knowledgebase.php'|| $page =='knowledgebase-view.php') ? 'active' : '' ;?>" id="v-pills-knowledgebase-tab" title="Knowledgebase" data-bs-toggle="pill" href="#v-pills-knowledgebase" role="tab" aria-controls="v-pills-knowledgebase" aria-selected="false">
                    <span class="material-icons-outlined">
                        school
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'activities.php') ? 'active' : '' ;?>" id="v-pills-activities-tab" title="Activities" data-bs-toggle="pill" href="#v-pills-activities" role="tab" aria-controls="v-pills-activities" aria-selected="false">
                    <span class="material-icons-outlined">
                        toggle_off
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'users.php') ?'active':'';?>" id="v-pills-users-tab" title="Users" data-bs-toggle="pill" href="#v-pills-users" role="tab" aria-controls="v-pills-users" aria-selected="false">
                    <span class="material-icons-outlined">
                        group_add
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'settings.php'|| $page =='localization.php'|| $page =='theme-settings.php'|| $page =='roles-permissions.php'|| $page =='email-settings.php'|| $page =='performance-setting.php'|| $page =='approval-setting.php'|| $page =='invoice-settings.php'|| $page =='salary-settings.php'|| $page =='notifications-settings.php'|| $page =='change-password.php'|| $page =='leave-type.php'|| $page =='toxbox-setting.php'|| $page =='cron-setting.php') ?'active':'';?>" id="v-pills-settings-tab" title="Settings" data-bs-toggle="pill" href="#v-pills-settings" role="tab" aria-controls="v-pills-settings" aria-selected="false">
                    <span class="material-icons-outlined">
                        settings
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'profile.php'|| $page =='client-profile.php'|| $page =='user-asset-details.php') ?'active':'';?>" id="v-pills-profile-tab" title="Profile" data-bs-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">
                    <span class="material-icons-outlined">
                        manage_accounts
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'index.php'|| $page =='register.php'|| $page =='forgot-password.php'|| $page =='otp.php'|| $page =='lock-screen.php') ?'active':'';?>" id="v-pills-authentication-tab" title="Authentication" data-bs-toggle="pill" href="#v-pills-authentication" role="tab" aria-controls="v-pills-authentication" aria-selected="false">
                    <span class="material-icons-outlined">
                        perm_contact_calendar
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'error-404.php'|| $page =='error-500.php') ?'active':'';?>" id="v-pills-errorpages-tab" title="Error Pages" data-bs-toggle="pill" href="#v-pills-errorpages" role="tab" aria-controls="v-pills-errorpages" aria-selected="false">
                    <span class="material-icons-outlined">
                        announcement
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'subscriptions.php'|| $page =='subscriptions-company.php'|| $page =='subscribed-companies.php') ?'active':'';?>" id="v-pills-subscriptions-tab" title="Subscriptions" data-bs-toggle="pill" href="#v-pills-subscriptions" role="tab" aria-controls="v-pills-subscriptions" aria-selected="false">
                    <span class="material-icons-outlined">
                        loyalty
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'search.php'|| $page =='faq.php'|| $page =='terms.php'|| $page =='privacy-policy.php'|| $page =='blank-page.php'|| $page =='coming-soon.php'|| $page =='under-maintenance.php') ?'active':'';?>" id="v-pills-pages-tab" title="Pages" data-bs-toggle="pill" href="#v-pills-pages" role="tab" aria-controls="v-pills-pages" aria-selected="false">
                    <span class="material-icons-outlined">
                        layers
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'ui-alerts.php'|| $page =='ui-accordion.php'|| $page =='ui-avatar.php'|| $page =='ui-badges.php'|| $page =='ui-borders.php'|| $page =='ui-buttons.php'|| $page =='ui-buttons-group.php'|| $page =='ui-breadcrumb.php'|| $page =='ui-cards.php'|| $page =='ui-carousel.php'|| $page ==
                'ui-colors.php'|| $page == 'ui-dropdowns.php'|| $page =='ui-grid.php'|| $page =='ui-images.php'|| $page =='ui-lightbox.php'|| $page =='ui-media.php'|| $page =='ui-modals.php'|| $page =='ui-notification.php'|| $page =='ui-offcanvas.php'|| $page =='ui-pagination.php'|| $page =='ui-popovers.php'|| $page =='ui-progress.php'|| $page ==
                'ui-placeholders.php' || $page =='ui-rangeslider.php'|| $page =='ui-spinner.php'|| $page =='ui-sweetalerts.php'|| $page =='ui-nav-tabs.php'|| $page =='ui-toasts.php'|| $page =='ui-tooltips.php'|| $page =='ui-typography.php'|| $page =='ui-video.php') ?'active':'';?>" id="v-pills-baseui-tab" title="Base UI" data-bs-toggle="pill" href="#v-pills-baseui" role="tab" aria-controls="v-pills-baseui" aria-selected="false">
                    <span class="material-icons-outlined">
                        foundation
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'ui-ribbon.php'|| $page =='ui-clipboard.php'|| $page =='ui-drag-drop.php'|| $page =='ui-rangeslider.php'|| $page =='ui-rating.php'|| $page =='ui-text-editor.php'|| $page =='ui-counter.php'|| $page =='ui-scrollbar.php'|| $page =='ui-stickynote.php'|| $page =='ui-timeline.php') ?'active':'';?>" id="v-pills-elements-tab" title="Advanced UI" data-bs-toggle="pill" href="#v-pills-elements" role="tab" aria-controls="v-pills-elements" aria-selected="false">
                    <span class="material-icons-outlined">
                        bento
                    </span>
                </a>
                <a class="nav-link  <?php echo ($page == 'chart-apex.php'|| $page =='chart-js.php'|| $page =='chart-morris.php'|| $page =='chart-flot.php'|| $page =='chart-peity.php'|| $page =='chart-c3.php') ?'active':'';?>" id="v-pills-charts-tab" title="Charts" data-bs-toggle="pill" href="#v-pills-charts" role="tab" aria-controls="v-pills-charts" aria-selected="false">
                    <span class="material-icons-outlined">
                        bar_chart
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'icon-fontawesome.php'|| $page =='icon-feather.php'|| $page =='icon-ionic.php'|| $page =='icon-material.php'|| $page =='icon-pe7.php'|| $page =='icon-simpleline.php'|| $page =='icon-themify.php'|| $page =='icon-weather.php'|| $page =='icon-typicon.php'|| $page =='icon-flag.php') ?'active':'';?>" id="v-pills-icons-tab" title="Icons" data-bs-toggle="pill" href="#v-pills-icons" role="tab" aria-controls="v-pills-icons" aria-selected="false">
                    <span class="material-icons-outlined">
                        grading
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'form-basic-inputs.php'|| $page =='form-input-groups.php'|| $page =='form-horizontal.php'|| $page =='form-vertical.php'|| $page =='form-mask.php'|| $page =='form-validation.php'|| $page =='form-select2.php'|| $page =='form-fileupload.php'|| $page =='horizontal-timeline.php'|| $page =='form-wizard.php') ?'active':'';?>" id="v-pills-forms-tab" title="Forms" data-bs-toggle="pill" href="#v-pills-forms" role="tab" aria-controls="v-pills-forms" aria-selected="false">
                    <span class="material-icons-outlined">
                        view_day
                    </span>
                </a>
                <a class="nav-link <?php echo ($page == 'tables-basic.php'|| $page =='data-tables.php') ?'active':'';?>" id="v-pills-tables-tab" title="Tables" data-bs-toggle="pill" href="#v-pills-tables" role="tab" aria-controls="v-pills-tables" aria-selected="false">
                    <span class="material-icons-outlined">
                        table_rows
                    </span>
                </a>
                <a class="nav-link " id="v-pills-documentation-tab" title="Documentation" data-bs-toggle="pill" href="#v-pills-documentation" role="tab" aria-controls="v-pills-documentation" aria-selected="false">
                    <span class="material-icons-outlined">
                        description
                    </span>
                </a>
                <a class="nav-link " id="v-pills-changelog-tab" title="Changelog" data-bs-toggle="pill" href="#v-pills-changelog" role="tab" aria-controls="v-pills-changelog" aria-selected="false">
                    <span class="material-icons-outlined">
                        sync_alt
                    </span>
                </a>
                <a class="nav-link" id="v-pills-multilevel-tab" title="Multilevel" data-bs-toggle="pill" href="#v-pills-multilevel" role="tab" aria-controls="v-pills-multilevel" aria-selected="false">
                    <span class="material-icons-outlined">
                        library_add_check
                    </span>
                </a>
            </div>
        </div>
        
        <div class="sidebar-right">
            <div class="tab-content" id="v-pills-tabContent">
                <div class="tab-pane fade <?php echo ($page == 'admin-dashboard.php' || $page =='employee-dashboard.php'|| $page =='deals-dashboard.php'|| $page =='leads-dashboard.php') ?'show active':'';?>" id="v-pills-dashboard" role="tabpanel" aria-labelledby="v-pills-dashboard-tab">
                    <p>Dashboard</p>
                    <ul>
                        <li>
                            <a  class="<?php echo ($page == 'admin-dashboard.php') ?'active':'';?>" href="admin-dashboard.php" >Admin Dashboard</a>
                        </li>
                        <li>
                            <a  class="<?php echo ($page == 'employee-dashboard.php') ?'active':'';?>" href="employee-dashboard.php">Employee Dashboard</a>
                        </li>
                        <li><a   class="<?php echo ($page == 'deals-dashboard.php') ?'active':'';?>"href="deals-dashboard.php">Deals Dashboard</a></li>
                        <li><a   class="<?php echo ($page == 'leads-dashboard.php') ?'active':'';?>"href="leads-dashboard.php">Leads Dashboard</a></li>
                    </ul>
                </div>
                <div class="tab-pane fade <?php echo ($page == 'chat.php' || $page == 'voice-call.php' || $page == 'video-call.php' || $page == 'incoming-call.php' || $page == 'outgoing-call.php' || $page == 'events.php' || $page == 'inbox.php' || $page == 'compose.php' || $page == 'file-manager.php' || $page == 'mail-view.php' || $page == 'contacts.php')?'show active':'';?>" id="v-pills-apps" role="tabpanel" aria-labelledby="v-pills-apps-tab">
                    <p>App</p>
                    <ul>
                        <li>
                            <a class="<?php echo ($page == 'chat.php') ?'active':'';?>" href="chat.php">Chat</a>
                        </li>
                        <li class="sub-menu">
                            <a href="#">Calls <span class="menu-arrow"></span></a>
                            <ul>
                                <li><a    class="<?php echo ($page == 'voice-call.php') ?'active':'';?>"href="voice-call.php">Voice Call</a></li>
                                <li><a    class="<?php echo ($page == 'video-call.php') ?'active':'';?>"href="video-call.php">Video Call</a></li>
                                <li><a    class="<?php echo ($page == 'outgoing-call.php') ?'active':'';?>"href="outgoing-call.php">Outgoing Call</a></li>
                                <li><a    class="<?php echo ($page == 'incoming-call.php') ?'active':'';?>"href="incoming-call.php">Incoming Call</a></li>
                            </ul>
                        </li>
                        <li>
                            <a class="<?php echo ($page == 'events.php') ?'active':'';?>" href="events.php">Calendar</a>
                        </li>
                        <li>
                            <a class="<?php echo ($page == 'contacts.php') ?'active':'';?>" href="contacts.php">Contacts</a>
                        </li>
                        <li>
                            <a class="<?php echo ($page == 'inbox.php') ?'active':'';?>" href="inbox.php">Email</a>
                        </li>
                        <li>
                            <a class="<?php echo ($page == 'file-manager.php') ?'active':'';?>" href="file-manager.php">File Manager</a>
                        </li>
                    </ul>						
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'employees-list.php'|| $page =='employees.php'|| $page =='holidays.php'|| $page =='leaves.php'|| $page =='leaves-employee.php'|| $page =='leave-settings.php'|| $page =='attendance.php'|| $page =='attendance-employee.php'|| $page =='departments.php'|| $page =='designations.php'|| $page =='timesheet.php'|| $page =='shift-scheduling.php'|| $page =='overtime.php'|| $page =='shift-list.php') ?'show active':'';?>" id="v-pills-employees" role="tabpanel" aria-labelledby="v-pills-employees-tab">
                    <p>Employees</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'employees.php'|| $page =='employees-list.php') ?'active':'';?>" href="employees.php">All Employees</a></li>
                        <li><a    class="<?php echo ($page == 'holidays.php') ?'active':'';?>" href="holidays.php">Holidays</a></li>
                        <li><a    class="<?php echo ($page == 'leaves.php') ?'active':'';?>" href="leaves.php">Leaves (Admin) <span class="badge rounded-pill bg-primary float-end">1</span></a></li>
                        <li><a    class="<?php echo ($page == 'leaves-employee.php') ?'active':'';?>" href="leaves-employee.php">Leaves (Employee)</a></li>
                        <li><a    class="<?php echo ($page == 'leave-settings.php') ?'active':'';?>" href="leave-settings.php">Leave Settings</a></li>
                        <li><a    class="<?php echo ($page == 'attendance.php') ?'active':'';?>" href="attendance.php">Attendance (Admin)</a></li>
                        <li><a    class="<?php echo ($page == 'attendance-employee.php') ?'active':'';?>" href="attendance-employee.php">Attendance (Employee)</a></li>
                        <li><a    class="<?php echo ($page == 'departments.php') ?'active':'';?>" href="departments.php">Departments</a></li>
                        <li><a    class="<?php echo ($page == 'designations.php') ?'active':'';?>" href="designations.php">Designations</a></li>
                        <li><a    class="<?php echo ($page == 'timesheet.php') ?'active':'';?>" href="timesheet.php">Timesheet</a></li>
                        <li><a    class="<?php echo ($page == 'shift-scheduling.php'|| $page =='shift-list.php') ?'active':'';?>" href="shift-scheduling.php">Shift & Schedule</a></li>
                        <li><a    class="<?php echo ($page == 'overtime.php') ?'active':'';?>"  href="overtime.php">Overtime</a></li>
                    </ul>  
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'clients.php'|| $page =='clients-list.php') ?'show active':'';?>" id="v-pills-clients" role="tabpanel" aria-labelledby="v-pills-clients-tab">
                    <p>Clients</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'clients.php'|| $page =='clients-list.php') ?'active':'';?>" href="clients.php">Clients</a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'projects.php'|| $page =='tasks.php'|| $page =='task-board.php'|| $page =='project-list.php') ?'show active':'';?>"  id="v-pills-projects" role="tabpanel" aria-labelledby="v-pills-projects-tab">
                    <p>Projects</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'projects.php'|| $page =='project-list.php') ?'active':'';?>" href="projects.php">Projects</a></li>
                        <li><a    class="<?php echo ($page == 'tasks.php') ?'active':'';?>" href="tasks.php">Tasks</a></li>
                        <li><a    class="<?php echo ($page == 'task-board.php') ?'active':'';?>" href="task-board.php">Task Board</a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'contact-list.php'|| $page =='companies.php'|| $page =='deals.php'|| $page =='leads.php'|| $page =='leads-details.php'|| $page =='leads-kanban.php'|| $page =='pipeline.php'|| $page =='analytics.php'|| $page =='contact-grid.php'|| $page =='contact-details.php'|| $page =='companies-grid.php'|| $page =='company-details.php'|| $page =='deals-kanban.php'|| $page =='deals-details.php') ?'show active':'';?>" id="v-pills-leads" role="tabpanel" aria-labelledby="v-pills-leads-tab">
                    <p>CRM</p>
                    <ul>
                        <li>
                            <a class="<?php echo ($page == 'contact-list.php'|| $page =='contact-grid.php'|| $page =='contact-details.php') ?'active':'';?>"  href="contact-list.php"> Contacts</a>
                        </li>
                        <li>
                            <a class="<?php echo ($page == 'companies.php'|| $page =='companies-grid.php'|| $page =='company-details.php') ?'active':'';?>" href="companies.php">Companies</a>
                        </li>
                        <li>
                            <a class="<?php echo ($page == 'deals.php'|| $page =='deals-kanban.php'|| $page =='deals-details.php') ?'active':'';?>"  href="deals.php">  Deals</a>
                        </li>
                        <li>
                            <a class="<?php echo ($page == 'leads.php'|| $page =='leads-details.php'|| $page =='leads-kanban.php') ?'active':'';?>" href="leads.php"> Leads </a>
                        </li>
                        <li>
                            <a class="<?php echo ($page == 'pipeline.php') ?'active':'';?>" href="pipeline.php">Pipeline </a>
                        </li>
                        <li>
                            <a class="<?php echo ($page == 'analytics.php') ?'active':'';?>" href="analytics.php">Analytics</a>
                        </li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'tickets.php'|| $page =='ticket-details.php') ?'show active':'';?>" id="v-pills-tickets" role="tabpanel" aria-labelledby="v-pills-tickets-tab">
                    <p>Tickets</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'tickets.php') ?'active':'';?>" href="tickets.php">Tickets</a></li>
                        <li><a    class="<?php echo ($page == 'ticket-details.php') ?'active':'';?>"href="ticket-details.php">Ticket Details</a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'estimates.php'|| $page =='invoices.php'|| $page =='payments.php'|| $page =='expenses.php'|| $page =='provident-fund.php'|| $page =='taxes.php'|| $page =='edit-estimate.php'|| $page =='create-invoice.php'|| $page =='edit-invoice.php'|| $page =='create-estimate.php') ?'show active':'';?>" id="v-pills-sales" role="tabpanel" aria-labelledby="v-pills-sales-tab">
                    <p>Sales</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'estimates.php'|| $page =='edit-estimate.php'|| $page =='create-estimate.php') ?'active':'';?>"href="estimates.php">Estimates</a></li>
                        <li><a    class="<?php echo ($page == 'invoices.php'|| $page =='create-invoice.php'|| $page =='edit-invoice.php') ?'active':'';?>"href="invoices.php">Invoices</a></li>
                        <li><a    class="<?php echo ($page == 'payments.php') ?'active':'';?>"href="payments.php">Payments</a></li>
                        <li><a    class="<?php echo ($page == 'expenses.php') ?'active':'';?>"href="expenses.php">Expenses</a></li>
                        <li><a    class="<?php echo ($page == 'provident-fund.php') ?'active':'';?>"href="provident-fund.php">Provident Fund</a></li>
                        <li><a    class="<?php echo ($page == 'taxes.php') ?'active':'';?>"href="taxes.php">Taxes</a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'categories.php'|| $page =='budgets.php'|| $page =='budget-expenses.php'|| $page =='budget-revenues.php'|| $page =='sub-category.php') ?'show active':'';?>" id="v-pills-accounting" role="tabpanel" aria-labelledby="v-pills-accounting-tab">
                    <p>Accounting</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'categories.php'|| $page =='sub-category.php') ?'active':'';?>"href="categories.php">Categories</a></li>
                        <li><a    class="<?php echo ($page == 'budgets.php') ?'active':'';?>"href="budgets.php">Budgets</a></li>
                        <li><a    class="<?php echo ($page == 'budget-expenses.php') ?'active':'';?>"href="budget-expenses.php">Budget Expenses</a></li>
                        <li><a    class="<?php echo ($page == 'budget-revenues.php') ?'active':'';?>"href="budget-revenues.php">Budget Revenues</a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'salary.php'|| $page =='salary-view.php'|| $page =='payroll-items.php') ?'show active':'';?>" id="v-pills-payroll" role="tabpanel" aria-labelledby="v-pills-payroll-tab">
                    <p>Payroll</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'salary.php') ?'active':'';?>"href="salary.php"> Employee Salary </a></li>
                        <li><a    class="<?php echo ($page == 'salary-view.php') ?'active':'';?>"href="salary-view.php"> Payslip </a></li>
                        <li><a    class="<?php echo ($page == 'payroll-items.php') ?'active':'';?>"href="payroll-items.php"> Payroll Items </a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'policies.php') ?'show active':'';?>" id="v-pills-policies" role="tabpanel" aria-labelledby="v-pills-policies-tab">
                    <p>Policies</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'policies.php') ?'active':'';?>"href="policies.php"> Policies </a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'expense-reports.php'|| $page =='invoice-reports.php'|| $page =='payments-reports.php'|| $page =='task-reports.php'|| $page =='user-reports.php'|| $page =='employee-reports.php'|| $page =='payslip-reports.php'|| $page =='attendance-reports.php'|| $page =='leave-reports.php'|| $page =='daily-reports.php'|| $page =='project-reports.php') ?'show active':'';?>" id="v-pills-reports" role="tabpanel" aria-labelledby="v-pills-reports-tab">
                    <p>Reports</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'expense-reports.php') ?'active':'';?>"href="expense-reports.php"> Expense Report </a></li>
                        <li><a    class="<?php echo ($page == 'invoice-reports.php') ?'active':'';?>"href="invoice-reports.php"> Invoice Report </a></li>
                        <li><a    class="<?php echo ($page == 'payments-reports.php') ?'active':'';?>"href="payments-reports.php"> Payments Report </a></li>
                        <li><a    class="<?php echo ($page == 'project-reports.php') ?'active':'';?>"href="project-reports.php"> Project Report </a></li>
                        <li><a    class="<?php echo ($page == 'task-reports.php') ?'active':'';?>"href="task-reports.php"> Task Report </a></li>
                        <li><a    class="<?php echo ($page == 'user-reports.php') ?'active':'';?>"href="user-reports.php"> User Report </a></li>
                        <li><a    class="<?php echo ($page == 'employee-reports.php') ?'active':'';?>"href="employee-reports.php"> Employee Report </a></li>
                        <li><a    class="<?php echo ($page == 'payslip-reports.php') ?'active':'';?>"href="payslip-reports.php"> Payslip Report </a></li>
                        <li><a    class="<?php echo ($page == 'attendance-reports.php') ?'active':'';?>"href="attendance-reports.php"> Attendance Report </a></li>
                        <li><a    class="<?php echo ($page == 'leave-reports.php') ?'active':'';?>"href="leave-reports.php"> Leave Report </a></li>
                        <li><a    class="<?php echo ($page == 'daily-reports.php') ?'active':'';?>"href="daily-reports.php"> Daily Report </a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'performance-indicator.php'|| $page =='performance.php'|| $page =='performance-appraisal.php') ?'show active':'';?>" id="v-pills-performance" role="tabpanel" aria-labelledby="v-pills-performance-tab">
                    <p>Performance</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'performance-indicator.php') ?'active':'';?>"href="performance-indicator.php"> Performance Indicator </a></li>
                        <li><a    class="<?php echo ($page == 'performance.php') ?'active':'';?>"href="performance.php"> Performance Review </a></li>
                        <li><a    class="<?php echo ($page == 'performance-appraisal.php') ?'active':'';?>"href="performance-appraisal.php"> Performance Appraisal </a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'goal-tracking.php'|| $page =='goal-type.php') ?'show active':'';?>" id="v-pills-goals" role="tabpanel" aria-labelledby="v-pills-goals-tab">
                    <p>Goals</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'goal-tracking.php') ?'active':'';?>"href="goal-tracking.php"> Goal List </a></li>
                        <li><a    class="<?php echo ($page == 'goal-type.php') ?'active':'';?>"href="goal-type.php"> Goal Type </a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'training.php'|| $page =='trainers.php'|| $page =='training-type.php') ? 'show active' : '' ;?>" id="v-pills-training" role="tabpanel" aria-labelledby="v-pills-training-tab">
                    <p>Training</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'training.php') ?'active':'';?>"href="training.php"> Training List </a></li>
                        <li><a    class="<?php echo ($page == 'trainers.php') ?'active':'';?>"href="trainers.php"> Trainers</a></li>
                        <li><a    class="<?php echo ($page == 'training-type.php') ?'active':'';?>"href="training-type.php"> Training Type </a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'promotion.php') ?'show active':'';?>" id="v-pills-promotion" role="tabpanel" aria-labelledby="v-pills-promotion-tab">
                    <p>Promotion</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'promotion.php') ?'active':'';?>"href="promotion.php"> Promotion </a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'resignation.php') ?'show active':'';?>" id="v-pills-resignation" role="tabpanel" aria-labelledby="v-pills-resignation-tab">
                    <p>Resignation</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'resignation.php') ?'active':'';?>"href="resignation.php"> Resignation </a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'termination.php') ?'show active':'';?>" id="v-pills-termination" role="tabpanel" aria-labelledby="v-pills-termination-tab">
                    <p>Termination</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'termination.php') ?'active':'';?>"href="termination.php"> Termination </a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'assets.php') ? 'show active' : '' ;?>" id="v-pills-assets" role="tabpanel" aria-labelledby="v-pills-assets-tab">
                    <p>Assets</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'assets.php') ?'active':'';?>"href="assets.php"> Assets </a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'user-dashboard.php'|| $page =='jobs-dashboard.php'|| $page =='jobs.php'|| $page =='job-applicants.php'|| $page =='manage-resumes.php'|| $page =='shortlist-candidates.php'|| $page =='interview-questions.php'|| $page =='offer_approvals.php'|| $page =='experiance-level.php'|| $page =='candidates.php'|| $page =='schedule-timing.php'|| $page =='apptitude-result.php'|| $page =='user-all-jobs.php'|| $page =='saved-jobs.php'|| $page =='applied-jobs.php'|| $page =='interviewing.php'|| $page =='offered-jobs.php'|| $page =='visited-jobs.php'|| $page =='archived-jobs.php'|| $page =='job-aptitude.php'|| $page =='questions.php') ? 'show active' : '' ;?>" id="v-pills-jobs" role="tabpanel" aria-labelledby="v-pills-jobs-tab">
                    <p>Jobs</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'user-dashboard.php'|| $page =='user-all-jobs.php'|| $page =='saved-jobs.php'|| $page =='applied-jobs.php'|| $page =='interviewing.php'|| $page =='offered-jobs.php'|| $page =='visited-jobs.php'|| $page =='archived-jobs.php'|| $page =='job-aptitude.php'|| $page =='questions.php') ?'active':'';?>"href="user-dashboard" class="active"> User Dasboard </a></li>
                        <li><a    class="<?php echo ($page == 'jobs-dashboard.php') ?'active':'';?>"href="jobs-dashboard.php"> Jobs Dasboard </a></li>
                        <li><a    class="<?php echo ($page == 'jobs.php') ?'active':'';?>"href="jobs.php"> Manage Jobs </a></li>
                        <li><a    class="<?php echo ($page == 'job-applicants.php') ?'active':'';?>"href="job-applicants.php"> Applied Jobs </a></li>
                        <li><a    class="<?php echo ($page == 'manage-resumes.php') ?'active':'';?>"href="manage-resumes.php"> Manage Resumes </a></li>
                        <li><a    class="<?php echo ($page == 'shortlist-candidates.php') ?'active':'';?>"href="shortlist-candidates.php"> Shortlist Candidates </a></li>
                        <li><a    class="<?php echo ($page == 'interview-questions.php') ?'active':'';?>"href="interview-questions.php"> Interview Questions </a></li>
                        <li><a    class="<?php echo ($page == 'offer_approvals.php') ?'active':'';?>"href="offer_approvals.php"> Offer Approvals </a></li>
                        <li><a    class="<?php echo ($page == 'experiance-level.php') ?'active':'';?>"href="experiance-level.php"> Experience Level </a></li>
                        <li><a    class="<?php echo ($page == 'candidates.php') ?'active':'';?>"href="candidates.php"> Candidates List </a></li>
                        <li><a    class="<?php echo ($page == 'schedule-timing.php') ?'active':'';?>"href="schedule-timing.php"> Schedule timing </a></li>
                        <li><a    class="<?php echo ($page == 'apptitude-result.php') ?'active':'';?>"href="apptitude-result.php"> Aptitude Results </a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'knowledgebase.php'|| $page =='knowledgebase-view.php') ?'show active':'';?>" id="v-pills-knowledgebase" role="tabpanel" aria-labelledby="v-pills-knowledgebase-tab">
                    <p>Knowledgebase</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'knowledgebase.php'|| $page =='knowledgebase-view.php') ?'active':'';?>"href="knowledgebase.php"> Knowledgebase </a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'activities.php') ?'show active':'';?>" id="v-pills-activities" role="tabpanel" aria-labelledby="v-pills-activities-tab">
                    <p>Activities</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'activities.php') ?'active':'';?>"href="activities.php"> Activities </a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'users.php') ?'show active':'';?>" id="v-pills-users" role="tabpanel" aria-labelledby="v-pills-activities-tab">
                    <p>Users</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'users.php') ?'active':'';?>"href="users.php"> Users </a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'settings.php'|| $page =='localization.php'|| $page =='theme-settings.php'|| $page =='roles-permissions.php'|| $page =='email-settings.php'|| $page =='performance-setting.php'|| $page =='approval-setting.php'|| $page =='invoice-settings.php'|| $page =='salary-settings.php'|| $page =='notifications-settings.php'|| $page =='change-password.php'|| $page =='leave-type.php'|| $page =='toxbox-setting.php'|| $page =='cron-setting.php') ?'show active':'';?>" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab">
                    <p>Settings</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'settings.php'|| $page =='localization.php'|| $page =='theme-settings.php'|| $page =='roles-permissions.php'|| $page =='email-settings.php'|| $page =='performance-setting.php'|| $page =='approval-setting.php'|| $page =='invoice-settings.php'|| $page =='salary-settings.php'|| $page =='notifications-settings.php'|| $page =='change-password.php'|| $page =='leave-type.php'|| $page =='toxbox-setting.php'|| $page =='cron-setting.php') ?'active':'';?>"href="settings.php"> Settings </a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'profile.php'|| $page =='client-profile.php'|| $page =='user-asset-details.php') ?'show active':'';?>" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                    <p>Profile</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'profile.php'|| $page =='user-asset-details.php') ?'active':'';?>"href="profile.php"> Employee Profile </a></li>
                        <li><a    class="<?php echo ($page == 'client-profile.php') ?'active':'';?>"href="client-profile.php"> Client Profile </a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'index.php'|| $page =='register.php'|| $page =='forgot-password.php'|| $page =='otp.php'|| $page =='lock-screen.php') ? 'show active' : '' ;?>" id="v-pills-authentication" role="tabpanel" aria-labelledby="v-pills-authentication-tab">
                    <p>Authentication</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'index.php') ?'active':'';?>"href="index.php"> Login </a></li>
                        <li><a    class="<?php echo ($page == 'register.php') ?'active':'';?>"href="register.php"> Register </a></li>
                        <li><a    class="<?php echo ($page == 'forgot-password.php') ?'active':'';?>"href="forgot-password.php"> Forgot Password </a></li>
                        <li><a    class="<?php echo ($page == 'otp.php') ?'active':'';?>"href="otp.php"> OTP </a></li>
                        <li><a    class="<?php echo ($page == 'lock-screen.php') ?'active':'';?>"href="lock-screen.php"> Lock Screen </a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'error-404.php'|| $page =='error-500.php') ?'showactive':'';?>" id="v-pills-errorpages" role="tabpanel" aria-labelledby="v-pills-errorpages-tab">
                    <p>Error Pages</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'error-404.php') ?'active':'';?>"href="error-404.php">404 Error </a></li>
                        <li><a    class="<?php echo ($page == 'error-500.php') ?'active':'';?>"href="error-500.php">500 Error </a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'subscriptions.php'|| $page =='subscriptions-company.php'|| $page =='subscribed-companies.php') ?'show active':'';?>" id="v-pills-subscriptions" role="tabpanel" aria-labelledby="v-pills-subscriptions-tab">
                    <p>Subscriptions</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'subscriptions.php') ?'active':'';?>"href="subscriptions.php"> Subscriptions (Admin) </a></li>
                        <li><a    class="<?php echo ($page == 'subscriptions-company.php') ?'active':'';?>"href="subscriptions-company.php"> Subscriptions (Company) </a></li>
                        <li><a    class="<?php echo ($page == 'subscribed-companies.php') ?'active':'';?>"href="subscribed-companies.php"> Subscribed Companies</a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'search.php'|| $page =='faq.php'|| $page =='terms.php'|| $page =='privacy-policy.php'|| $page =='blank-page.php'|| $page =='coming-soon.php'|| $page =='under-maintenance.php') ?'show active':'';?>" id="v-pills-pages" role="tabpanel" aria-labelledby="v-pills-pages-tab">
                    <p>Pages</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'search.php') ?'active':'';?>"href="search.php"> Search </a></li>
                        <li><a    class="<?php echo ($page == 'faq.php') ?'active':'';?>"href="faq.php"> FAQ </a></li>
                        <li><a    class="<?php echo ($page == 'terms.php') ?'active':'';?>"href="terms.php"> Terms </a></li>
                        <li><a    class="<?php echo ($page == 'privacy-policy.php') ?'active':'';?>"href="privacy-policy.php"> Privacy Policy </a></li>
                        <li><a    class="<?php echo ($page == 'blank-page.php') ?'active':'';?>"href="blank-page.php"> Blank Page </a></li>
                        <li><a    class="<?php echo ($page == 'coming-soon.php') ?'active':'';?>"href="coming-soon.php"> Coming Soon </a></li>
                        <li><a    class="<?php echo ($page == 'under-maintenance.php') ?'active':'';?>"href="under-maintenance.php"> Under Maintanance </a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'ui-alerts.php'|| $page =='ui-accordion.php'|| $page =='ui-avatar.php'|| $page =='ui-badges.php'|| $page =='ui-borders.php'|| $page =='ui-buttons.php'|| $page =='ui-buttons-group.php'|| $page =='ui-breadcrumb.php'|| $page =='ui-cards.php'|| $page =='ui-carousel.php'|| $page ==
              'ui-colors.php'|| $page == 'ui-dropdowns.php'|| $page =='ui-grid.php'|| $page =='ui-images.php'|| $page =='ui-lightbox.php'|| $page =='ui-media.php'|| $page =='ui-modals.php'|| $page =='ui-notification.php'|| $page =='ui-offcanvas.php'|| $page =='ui-pagination.php'|| $page =='ui-popovers.php'|| $page =='ui-progress.php'|| $page ==
              'ui-placeholders' || $page =='ui-rangeslider.php'|| $page =='ui-spinner.php'|| $page =='ui-sweetalerts.php'|| $page =='ui-nav-tabs.php'|| $page =='ui-toasts.php'|| $page =='ui-tooltips.php'|| $page =='ui-typography.php'|| $page =='ui-video.php') ? 'show active' : '' ;?>" id="v-pills-baseui" role="tabpanel" aria-labelledby="v-pills-baseui-tab">
                    <p>Base UI</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'ui-alerts.php') ?'active':'';?>"href="ui-alerts.php">Alerts</a></li>
                        <li><a    class="<?php echo ($page == 'ui-accordion.php') ?'active':'';?>"href="ui-accordion.php">Accordion</a></li>
                        <li><a    class="<?php echo ($page == 'ui-avatar.php') ?'active':'';?>"href="ui-avatar.php">Avatar</a></li>
                        <li><a    class="<?php echo ($page == 'ui-badges.php') ?'active':'';?>"href="ui-badges.php">Badges</a></li>
                        <li><a    class="<?php echo ($page == 'ui-borders.php') ?'active':'';?>"href="ui-borders.php">Border</a></li>
                        <li><a    class="<?php echo ($page == 'ui-buttons.php') ?'active':'';?>"href="ui-buttons.php">Buttons</a></li>
                        <li><a    class="<?php echo ($page == 'ui-buttons-group.php') ?'active':'';?>"href="ui-buttons-group.php">Button Group</a></li>
                        <li><a    class="<?php echo ($page == 'ui-breadcrumb.php') ?'active':'';?>"href="ui-breadcrumb.php">Breadcrumb</a></li>
                        <li><a    class="<?php echo ($page == 'ui-cards.php') ?'active':'';?>"href="ui-cards.php">Card</a></li>
                        <li><a    class="<?php echo ($page == 'ui-carousel.php') ?'active':'';?>"href="ui-carousel.php">Carousel</a></li>
                        <li><a    class="<?php echo ($page == 'ui-colors.php') ?'active':'';?>"href="ui-colors.php">Colors</a></li>
                        <li><a    class="<?php echo ($page == 'ui-dropdowns.php') ?'active':'';?>"href="ui-dropdowns.php">Dropdowns</a></li>
                        <li><a    class="<?php echo ($page == 'ui-grid.php') ?'active':'';?>"href="ui-grid.php">Grid</a></li>
                        <li><a    class="<?php echo ($page == 'ui-images.php') ?'active':'';?>"href="ui-images.php">Images</a></li>
                        <li><a    class="<?php echo ($page == 'ui-lightbox.php') ?'active':'';?>"href="ui-lightbox.php">Lightbox</a></li>
                        <li><a    class="<?php echo ($page == 'ui-media.php') ?'active':'';?>"href="ui-media.php">Media</a></li>
                        <li><a    class="<?php echo ($page == 'ui-modals.php') ?'active':'';?>"href="ui-modals.php">Modals</a></li>
                        <li><a    class="<?php echo ($page == 'ui-notification.php') ?'active':'';?>"href="ui-notification.php">Notification</a></li>
                        <li><a    class="<?php echo ($page == 'ui-offcanvas.php') ?'active':'';?>"href="ui-offcanvas.php">Offcanvas</a></li>
                        <li><a    class="<?php echo ($page == 'ui-pagination.php') ?'active':'';?>"href="ui-pagination.php">Pagination</a></li>
                        <li><a    class="<?php echo ($page == 'ui-popovers.php') ?'active':'';?>"href="ui-popovers.php">Popovers</a></li>
                        <li><a    class="<?php echo ($page == 'ui-progress.php') ?'active':'';?>"href="ui-progress.php">Progress</a></li>
                        <li><a    class="<?php echo ($page == 'ui-placeholders.php') ?'active':'';?>"href="ui-placeholders.php">Placeholders</a></li>
                        <li><a    class="<?php echo ($page == 'ui-rangeslider.php') ?'active':'';?>"href="ui-rangeslider.php">Range Slider</a></li>
                        <li><a    class="<?php echo ($page == 'ui-spinner.php') ?'active':'';?>"href="ui-spinner.php">Spinner</a></li>
                        <li><a    class="<?php echo ($page == 'ui-sweetalerts.php') ?'active':'';?>"href="ui-sweetalerts.php">Sweet Alerts</a></li>
                        <li><a    class="<?php echo ($page == 'ui-nav-tabs.php') ?'active':'';?>"href="ui-nav-tabs.php">Tabs</a></li>
                        <li><a    class="<?php echo ($page == 'ui-toasts.php') ?'active':'';?>"href="ui-toasts.php">Toasts</a></li>
                        <li><a    class="<?php echo ($page == 'ui-tooltips.php') ?'active':'';?>"href="ui-tooltips.php">Tooltips</a></li>
                        <li><a    class="<?php echo ($page == 'ui-typography.php') ?'active':'';?>"href="ui-typography.php">Typography</a></li>
                        <li><a    class="<?php echo ($page == 'ui-video.php') ?'active':'';?>"href="ui-video.php">Video</a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'ui-ribbon.php'|| $page =='ui-clipboard.php'|| $page =='ui-drag-drop.php'|| $page =='ui-rangeslider.php'|| $page =='ui-rating.php'|| $page =='ui-text-editor.php'|| $page =='ui-counter.php'|| $page =='ui-scrollbar.php'|| $page =='ui-stickynote.php'|| $page =='ui-timeline.php') ?'show active':'';?>" id="v-pills-elements" role="tabpanel" aria-labelledby="v-pills-elements-tab">
                    <p>Advanced UI</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'ui-ribbon.php') ?'active':'';?>"href="ui-ribbon.php">Ribbon</a></li>
                        <li><a    class="<?php echo ($page == 'ui-clipboard.php') ?'active':'';?>"href="ui-clipboard.php">Clipboard</a></li>
                        <li><a    class="<?php echo ($page == 'ui-drag-drop.php') ?'active':'';?>"href="ui-drag-drop.php">Drag & Drop</a></li>
                        <li><a    class="<?php echo ($page == 'ui-rangeslider.php') ?'active':'';?>"href="ui-rangeslider.php">Range Slider</a></li>
                        <li><a    class="<?php echo ($page == 'ui-rating.php') ?'active':'';?>"href="ui-rating.php">Rating</a></li>
                        <li><a    class="<?php echo ($page == 'ui-text-editor.php') ?'active':'';?>"href="ui-text-editor.php">Text Editor</a></li>
                        <li><a    class="<?php echo ($page == 'ui-counter.php') ?'active':'';?>"href="ui-counter.php">Counter</a></li>
                        <li><a    class="<?php echo ($page == 'ui-scrollbar.php') ?'active':'';?>"href="ui-scrollbar.php">Scrollbar</a></li>
                        <li><a    class="<?php echo ($page == 'ui-stickynote.php') ?'active':'';?>"href="ui-stickynote.php">Sticky Note</a></li>
                        <li><a    class="<?php echo ($page == 'ui-timeline.php') ?'active':'';?>"href="ui-timeline.php">Timeline</a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'chart-apex.php'|| $page =='chart-js.php'|| $page =='chart-morris.php'|| $page =='chart-flot.php'|| $page =='chart-peity.php'|| $page =='chart-c3.php') ?'show active':'';?>" id="v-pills-charts" role="tabpanel" aria-labelledby="v-pills-charts-tab">
                    <p>Charts</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'chart-apex.php') ?'active':'';?>"href="chart-apex.php">Apex Charts</a></li>
                        <li><a    class="<?php echo ($page == 'chart-js.php') ?'active':'';?>"href="chart-js.php">Chart Js</a></li>
                        <li><a    class="<?php echo ($page == 'chart-morris.php') ?'active':'';?>"href="chart-morris.php">Morris Charts</a></li>
                        <li><a    class="<?php echo ($page == 'chart-flot.php') ?'active':'';?>"href="chart-flot.php">Flot Charts</a></li>
                        <li><a    class="<?php echo ($page == 'chart-peity.php') ?'active':'';?>"href="chart-peity.php">Peity Charts</a></li>
                        <li><a    class="<?php echo ($page == 'chart-c3.php') ?'active':'';?>"href="chart-c3.php">C3 Charts</a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'icon-fontawesome.php'|| $page =='icon-feather.php'|| $page =='icon-ionic.php'|| $page =='icon-material.php'|| $page =='icon-pe7.php'|| $page =='icon-simpleline.php'|| $page =='icon-themify.php'|| $page =='icon-weather.php'|| $page =='icon-typicon.php'|| $page =='icon-flag.php') ?'show active':'';?>" id="v-pills-icons" role="tabpanel" aria-labelledby="v-pills-icons-tab">
                    <p>Icons</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'icon-fontawesome.php') ?'active':'';?>"href="icon-fontawesome.php">Fontawesome Icons</a></li>
                        <li><a    class="<?php echo ($page == 'icon-feather.php') ?'active':'';?>"href="icon-feather.php">Feather Icons</a></li>
                        <li><a    class="<?php echo ($page == 'icon-ionic.php') ?'active':'';?>"href="icon-ionic.php">Ionic Icons</a></li>
                        <li><a    class="<?php echo ($page == 'icon-material.php') ?'active':'';?>"href="icon-material.php">Material Icons</a></li>
                        <li><a    class="<?php echo ($page == 'icon-pe7.php') ?'active':'';?>"href="icon-pe7.php">Pe7 Icons</a></li>
                        <li><a    class="<?php echo ($page == 'icon-simpleline.php') ?'active':'';?>"href="icon-simpleline.php">Simpleline Icons</a></li>
                        <li><a    class="<?php echo ($page == 'icon-themify.php') ?'active':'';?>"href="icon-themify.php">Themify Icons</a></li>
                        <li><a    class="<?php echo ($page == 'icon-weather.php') ?'active':'';?>"href="icon-weather.php">Weather Icons</a></li>
                        <li><a    class="<?php echo ($page == 'icon-typicon.php') ?'active':'';?>"href="icon-typicon.php">Typicon Icons</a></li>
                        <li><a    class="<?php echo ($page == 'icon-flag.php') ?'active':'';?>"href="icon-flag.php">Flag Icons</a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'form-basic-inputs.php'|| $page =='form-input-groups.php'|| $page =='form-horizontal.php'|| $page =='form-vertical.php'|| $page =='form-mask.php'|| $page =='form-validation.php'|| $page =='form-select2.php'|| $page =='form-fileupload.php'|| $page =='horizontal-timeline.php'|| $page =='form-wizard.php') ?'show active':'';?>" id="v-pills-forms" role="tabpanel" aria-labelledby="v-pills-forms-tab">
                    <p>Forms</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'form-basic-inputs.php') ?'active':'';?>"href="form-basic-inputs.php">Basic Inputs </a></li>
                        <li><a    class="<?php echo ($page == 'form-input-groups.php') ?'active':'';?>"href="form-input-groups.php">Input Groups </a></li>
                        <li><a    class="<?php echo ($page == 'form-horizontal.php') ?'active':'';?>"href="form-horizontal.php">Horizontal Form </a></li>
                        <li><a    class="<?php echo ($page == 'form-vertical.php') ?'active':'';?>"href="form-vertical.php"> Vertical Form </a></li>
                        <li><a    class="<?php echo ($page == 'form-mask.php') ?'active':'';?>"href="form-mask.php"> Form Mask </a></li>
                        <li><a    class="<?php echo ($page == 'form-validation.php') ?'active':'';?>"href="form-validation.php"> Form Validation </a></li>
                        <li><a    class="<?php echo ($page == 'form-select2.php') ?'active':'';?>"href="form-select2.php">Form Select2 </a></li>
                        <li><a    class="<?php echo ($page == 'form-fileupload.php') ?'active':'';?>"href="form-fileupload.php">File Upload </a></li>
                        <li><a    class="<?php echo ($page == 'horizontal-timeline.php') ?'active':'';?>"href="horizontal-timeline.php">Horizontal Timeline</a></li>
                        <li><a    class="<?php echo ($page == 'form-wizard.php') ?'active':'';?>"href="form-wizard.php">Form Wizard</a></li>
                    </ul>
                </div>
                <div class="tab-pane fade  <?php echo ($page == 'tables-basic.php'|| $page =='data-tables.php') ?'show active':'';?>" id="v-pills-tables" role="tabpanel" aria-labelledby="v-pills-tables-tab">
                    <p>Tables</p>
                    <ul>
                        <li><a    class="<?php echo ($page == 'tables-basic.php') ?'active':'';?>"href="tables-basic.php">Basic Tables </a></li>
                        <li><a    class="<?php echo ($page == 'data-tables.php') ?'active':'';?>"href="data-tables.php">Data Table </a></li>
                    </ul>
                </div>
                <div class="tab-pane fade" id="v-pills-documentation" role="tabpanel" aria-labelledby="v-pills-documentation-tab">
                    <p>Documentation</p>
                    <ul>
                        <li><a   href="#">Documentation </a></li>
                    </ul>
                </div>
                <div class="tab-pane fade" id="v-pills-changelog" role="tabpanel" aria-labelledby="v-pills-changelog-tab">
                    <p>Change Log</p>
                    <ul>
                        <li><a    href="#"><span>Change Log</span> <span class="badge badge-primary ms-auto">v4.0</span> </a></li>
                    </ul>
                </div>
                <div class="tab-pane fade" id="v-pills-multilevel" role="tabpanel" aria-labelledby="v-pills-multilevel-tab">
                    <p>Multi Level</p>
                    <ul>
                        <li class="sub-menu">
                            <a href="javascript:void(0);">Level 1 <span class="menu-arrow"></span></a>
                            <ul class="ms-3">
                                <li class="sub-menu">
                                    <a href="javascript:void(0);">Level 1 <span class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a   href="javascript:void(0);">Level 2</a></li>
                                        <li><a   href="javascript:void(0);">Level 3</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>								
                        <li><a    href="javascript:void(0);">Level 2</a></li>
                        <li><a   href="javascript:void(0);">Level 3</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Two Col Sidebar -->
