
     <x-guest-layout>

      <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search...">
                </div>
                <div class="user-actions">
                    <div class="notification">
                        <i class="fas fa-bell text-[#66CC00] text-3xl"></i>
                       <!-- <span class="notification-badge absolute -top-2 -right-2 bg-[#FF3366] text-xs px-1 rounded-full"></span>-->
                    </div>
                    <div class="user-profile">
                        <div class="user-avatar">HR</div>
                        <span class="text-bold ">Admin User</span>
                        <i class="fas fa-chevron-down" style="margin-left: 10px;"></i>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div>
                            <div class="stat-value"></div>
                            <div class="stat-label">Total Users</div>
                        </div>
                        <div class="stat-icon users">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div>
                            <div class="stat-value"></div>
                            <div class="stat-label"></div>
                        </div>
                        <div class="stat-icon sessions">
                            <i class="fas fa-signal"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div>
                            <div class="stat-value"></div>
                            <div class="stat-label">Number of Units</div>
                        </div>
                        <div class="stat-icon health">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div>
                            <div class="stat-value"></div>
                            <div class="stat-label">Recent Alerts</div>
                        </div>
                        <div class="stat-icon alerts">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="content-grid">
                <div class="left-column">
                    <!-- Activity Chart -->
                    <div class="chart-container">
                        <div class="section-header">
                            <h3 class="section-title text-shadow-black">Activity Overview of Services and Payroll Computation</h3>
                            <select style="padding: 5px; border-radius: 5px; border: 1px solid #ffffff; color:whitesmoke;">
                                <option class="option">Total salary</option>
                                <option class="option">Number of Units</option>
                                <option class="option">Types of Services</option>
                            </select>
                        </div>
                        <div class="chart-placeholder">
                            [Activity Chart Will Appear Here]
                        </div>
                    </div>

                    <!-- Recent Actions -->
                    <div class="recent-actions">
                        <div class="section-header">
                            <h3 class="section-title">Recent Actions</h3>
                            <button class="button">
                                View All
                            </button>
                        </div>
                        <div class="actions-list">
                            <div class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <div class="action-details">
                                    <div class="action-title">New user registered</div>
                                    <div class="action-time"></div>
                                </div>
                            </div>
                            <div class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-file-upload"></i>
                                </div>
                                <div class="action-details">
                                    <div class="action-title">Document uploaded</div>
                                    <div class="action-time"></div>
                                </div>
                            </div>
                            <div class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-cog"></i>
                                </div>
                                <div class="action-details">
                                    <div class="action-title">System settings updated</div>
                                    <div class="action-time"></div>
                                </div>
                            </div>
                            <div class="">
                                <div class="">
                                    <i class=""></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   
       

     </x-guest-layout>