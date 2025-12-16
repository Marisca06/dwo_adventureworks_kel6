<!-- ================= BEAUTIFUL ENHANCED SIDEBAR ================= -->
<style>
    /* Custom Sidebar Styling */
    #accordionSidebar {
        background: linear-gradient(180deg, #1e40af 0%, #1e3a8a 50%, #1e3a8a 100%) !important;
        box-shadow: 4px 0 20px rgba(30, 64, 175, 0.4);
        position: relative;
        width: 280px !important;
        min-width: 280px !important;
    }

    /* Expanded sidebar */
    .sidebar {
        width: 280px !important;
    }

    /* Collapsed sidebar */
    .sidebar.toggled {
        width: 90px !important;
    }

    /* Decorative Overlay */
    #accordionSidebar::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at top right, rgba(59, 130, 246, 0.15), transparent 50%),
                    radial-gradient(circle at bottom left, rgba(37, 99, 235, 0.15), transparent 50%);
        pointer-events: none;
    }

    /* Brand Styling */
    .sidebar-brand {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.25), rgba(37, 99, 235, 0.15));
        border-bottom: 2px solid rgba(96, 165, 250, 0.3);
        padding: 1.2rem 1rem !important;
        transition: all 0.3s ease;
        position: relative;
        backdrop-filter: blur(10px);
    }

    .sidebar-brand:hover {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.35), rgba(37, 99, 235, 0.25));
        border-bottom-color: rgba(96, 165, 250, 0.5);
    }

    .sidebar-brand-icon {
        font-size: 1.5rem;
        margin-right: 0.6rem;
        color: #60a5fa;
        filter: drop-shadow(0 0 8px rgba(96, 165, 250, 0.6));
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(-15deg); }
        50% { transform: translateY(-5px) rotate(-15deg); }
    }

    .sidebar-brand-text {
        font-size: 1.2rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        color: #ffffff;
        text-shadow: 0 2px 10px rgba(96, 165, 250, 0.5);
        background: linear-gradient(135deg, #ffffff, #bfdbfe);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Divider Enhancement */
    .sidebar-divider {
        border-top: 1px solid rgba(96, 165, 250, 0.25) !important;
        margin: 1rem 0;
        box-shadow: 0 1px 0 rgba(147, 197, 253, 0.1);
    }

    /* Heading Styling */
    .sidebar-heading {
        color: #bfdbfe !important;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 2.5px;
        text-transform: uppercase;
        padding: 0.75rem 1.5rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
    }

    .sidebar-heading i {
        margin-right: 0.5rem;
        font-size: 0.9rem;
        color: #93c5fd;
    }

    /* Nav Item Base Styling */
    .nav-item {
        margin: 0.4rem 1rem;
        border-radius: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    /* Nav Link Styling */
    .nav-item .nav-link {
        color: #e5e7eb !important;
        padding: 1.1rem 1.5rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        font-size: 1rem;
        font-weight: 500;
        background: transparent;
        border: 1px solid transparent;
    }

    /* Hover Effect */
    .nav-item .nav-link:hover {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.35), rgba(37, 99, 235, 0.25));
        color: #ffffff !important;
        transform: translateX(8px);
        border: 1px solid rgba(96, 165, 250, 0.3);
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3),
                    inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }

    /* Active State */
    .nav-item.active .nav-link {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #ffffff !important;
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.5),
                    inset 0 1px 0 rgba(255, 255, 255, 0.2);
        font-weight: 600;
        border: 1px solid rgba(147, 197, 253, 0.4);
        transform: translateX(8px);
    }

    .nav-item.active .nav-link::before {
        content: '';
        position: absolute;
        left: -1rem;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 60%;
        background: linear-gradient(180deg, #60a5fa, #3b82f6);
        border-radius: 0 4px 4px 0;
        box-shadow: 0 0 10px rgba(96, 165, 250, 0.6);
    }

    /* Icon Styling */
    .nav-link i {
        width: 1.75rem;
        font-size: 1.25rem;
        margin-right: 1.1rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        color: #93c5fd;
    }

    .nav-link:hover i {
        transform: scale(1.15);
        color: #bfdbfe;
        filter: drop-shadow(0 0 6px rgba(191, 219, 254, 0.6));
    }

    .nav-item.active .nav-link i {
        color: #ffffff;
        transform: scale(1.1);
    }

    /* Logout Special Styling */
    .nav-item .nav-link[data-target="#logoutModal"] {
        border: 1px solid rgba(239, 68, 68, 0.2);
    }

    .nav-item .nav-link[data-target="#logoutModal"]:hover {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.25), rgba(220, 38, 38, 0.2));
        color: #fecaca !important;
        border: 1px solid rgba(248, 113, 113, 0.4);
        box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
    }

    .nav-item .nav-link[data-target="#logoutModal"]:hover i {
        color: #fca5a5;
        filter: drop-shadow(0 0 6px rgba(252, 165, 165, 0.6));
    }

    /* Sidebar Toggle Button */
    #sidebarToggle {
        background: linear-gradient(135deg, #3b82f6, #1e3a8a) !important;
        width: 3rem;
        height: 3rem;
        border: 2px solid rgba(147, 197, 253, 0.3) !important;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        position: relative;
        overflow: hidden;
    }

    #sidebarToggle::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transition: all 0.5s ease;
    }

    #sidebarToggle:hover::before {
        width: 100%;
        height: 100%;
    }

    #sidebarToggle:hover {
        background: linear-gradient(135deg, #2563eb, #1e3a8a) !important;
        transform: rotate(180deg) scale(1.05);
        border-color: rgba(191, 219, 254, 0.5) !important;
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.6);
    }

    /* Scrollbar Styling */
    #accordionSidebar::-webkit-scrollbar {
        width: 8px;
    }

    #accordionSidebar::-webkit-scrollbar-track {
        background: rgba(30, 64, 175, 0.3);
        border-radius: 10px;
    }

    #accordionSidebar::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #3b82f6, #2563eb);
        border-radius: 10px;
        border: 2px solid rgba(30, 64, 175, 0.3);
    }

    #accordionSidebar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, #60a5fa, #3b82f6);
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .sidebar-brand-text {
            font-size: 1.2rem;
        }
        
        .nav-link {
            font-size: 0.9rem;
            padding: 0.85rem 1rem;
        }

        .nav-item {
            margin: 0.3rem 0.75rem;
        }
    }

    /* Smooth entrance animation */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .nav-item {
        animation: slideIn 0.5s ease forwards;
    }

    .nav-item:nth-child(1) { animation-delay: 0.1s; }
    .nav-item:nth-child(2) { animation-delay: 0.15s; }
    .nav-item:nth-child(3) { animation-delay: 0.2s; }
    .nav-item:nth-child(4) { animation-delay: 0.25s; }
    .nav-item:nth-child(5) { animation-delay: 0.3s; }
    .nav-item:nth-child(6) { animation-delay: 0.35s; }
    .nav-item:nth-child(7) { animation-delay: 0.4s; }
    .nav-item:nth-child(8) { animation-delay: 0.45s; }
</style>

<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- BRAND -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-mountain"></i>
        </div>
        <div class="sidebar-brand-text">ADVENTUREWORKS</div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- DASHBOARD -->
    <li class="nav-item">
        <a class="nav-link" href="dashboard.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Home</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        <i class="fas fa-chart-bar"></i>
        MENU
    </div>

    <li class="nav-item">
        <a class="nav-link" href="salesperson_analysis.php">
            <i class="fas fa-user-tie"></i>
            <span>Sales Person Analysis</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="customer_analysis.php">
            <i class="fas fa-user-friends"></i>
            <span>Customer Analysis</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="territory_analysis.php">
            <i class="fas fa-map-marked-alt"></i>
            <span>Territory Analysis</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="product_analysis.php">
            <i class="fas fa-box-open"></i>
            <span>Product Analysis</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="dashboard_olap.php">
            <i class="fas fa-cube"></i>
            <span>Mondrian OLAP</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- LOGOUT -->
    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="modal" data-target="#logoutModal">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

</ul>
<!-- ================= END BEAUTIFUL ENHANCED SIDEBAR ================= -->