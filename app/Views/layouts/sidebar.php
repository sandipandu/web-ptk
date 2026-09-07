<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div id="sidebar-wrapper" class="d-flex flex-column justify-content-between sidebar min-vh-100 text-white p-0 shadow" style="background-color: #1a202c; position: fixed; left: 0; top: 0; z-index: 1000; overflow: hidden; width: 330px; transition: width 0.3s ease;">
    
    <div class="w-100 d-flex flex-column" style="height: calc(100vh - 95px); overflow: hidden;">
        
        <div class="pt-4 pb-2 px-3 flex-shrink-0 d-flex justify-content-between align-items-start" style="background-color: #1a202c; box-shadow: 0 10px 20px rgba(26, 32, 44, 0.95); z-index: 11;">
            <div class="sidebar-brand-text px-2" style="transition: opacity 0.2s;">
                <h5 class="fw-extrabold mb-1" style="color: #22c55e; letter-spacing: 1.5px; font-weight: 800; font-size: 24px; white-space: nowrap;">PT LONSUM</h5>
                <small style="color: #94a3b8; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;">PTK System</small>
            </div>
            <button id="toggleSidebarBtn" class="btn text-white p-1 shadow-none" style="background: transparent; border: none;">
                <i class="bi bi-list fs-3"></i>
            </button>
        </div>
        <hr class="mt-2 mb-0 mx-3" style="border-color: rgba(255, 255, 255, 0.08); opacity: 1;">

        <div class="flex-grow-1" style="overflow-y: auto; overflow-x: hidden; padding-top: 10px; padding-bottom: 15px;" id="sidebarMenuScrollArea">
            <ul class="nav flex-column px-2" style="list-style: none; padding-left: 0;">
                
                <li class="nav-item mb-1 px-1">
                    <a class="nav-link text-white rounded py-2.5 px-3 d-flex align-items-center <?= url_is('dashboard') ? 'bg-success active shadow-sm' : '' ?>" href="<?= site_url('dashboard') ?>" style="font-size: 14.5px; font-weight: 600; transition: all 0.2s ease-in-out; letter-spacing: -0.1px; white-space: nowrap;">
                        <i class="bi bi-grid-1x2 me-3 fs-5" style="opacity: <?= url_is('dashboard') ? '1' : '0.75' ?>;"></i> 
                        <span class="sidebar-menu-text">Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item mb-1 px-1">
                    <?php $is_ptk_page = (url_is('ptk') || url_is('ptk/*')); ?>
                    
                    <a class="nav-link text-white rounded py-2.5 px-3 d-flex align-items-center justify-content-between <?= $is_ptk_page ? 'bg-success active shadow-sm' : '' ?>" 
                       data-bs-toggle="collapse" 
                       href="#collapseMenuKebun" 
                       role="button" 
                       aria-expanded="<?= $is_ptk_page ? 'true' : 'false' ?>" 
                       aria-controls="collapseMenuKebun"
                       style="font-size: 14.5px; font-weight: 600; transition: all 0.2s ease-in-out; letter-spacing: -0.1px; white-space: nowrap;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-file-earmark-text me-3 fs-5" style="opacity: <?= $is_ptk_page ? '1' : '0.75' ?>;"></i> 
                            <span class="sidebar-menu-text">PTK</span>
                        </div>
                        <i class="bi bi-chevron-down ptk-arrow sidebar-menu-text <?= $is_ptk_page ? 'rotate' : '' ?>" style="font-size: 12px; transition: transform 0.2s;"></i>
                    </a>

                    <div class="collapse <?= $is_ptk_page ? 'show' : '' ?>" id="collapseMenuKebun">
                        <?php
                            $ptkModel = new \App\Models\PtkModel();
                            // Menggunakan TRIM untuk menghapus spasi ekstra agar data benar-benar unik
                            $daftar_kebun = $ptkModel->select('TRIM(kebun) AS kebun')
                                                     ->where('kebun IS NOT NULL')
                                                     ->where("TRIM(kebun) != ''")
                                                     ->groupBy('TRIM(kebun)')
                                                     ->orderBy('kebun', 'ASC')
                                                     ->findAll();
                        ?>
                        
                        <ul class="nav flex-column ms-4 mt-2 mb-1 custom-submenu sidebar-menu-text" style="border-left: 2px solid rgba(255, 255, 255, 0.1); padding-left: 10px; list-style: none;">
                            <li class="nav-item my-0.5">
                                <a class="nav-link py-1.5 px-2 d-flex align-items-center rounded submenu-link <?= url_is('ptk') ? 'active-submenu' : '' ?>" 
                                   href="<?= site_url('ptk') ?>" 
                                   style="font-size: 13.5px; font-weight: 500;">
                                     <i class="bi bi-grid me-2" style="font-size: 11px;"></i> Lihat Semua Kebun
                                </a>
                            </li>
                        </ul>

                        <?php if (!empty($daftar_kebun)): ?>
                            <ul class="nav flex-column ms-4 mb-2 custom-submenu sidebar-menu-text" style="border-left: 2px solid rgba(255, 255, 255, 0.1); padding-left: 10px; list-style: none;">
                                <?php foreach ($daftar_kebun as $k): ?>
                                    <?php 
                                        $encoded_kebun = rawurlencode($k['kebun']);
                                        $is_kebun_active = url_is('*' . $encoded_kebun); 
                                    ?>
                                    <li class="nav-item my-0.5">
                                        <a class="nav-link py-1.5 px-2 d-flex align-items-start rounded submenu-link <?= $is_kebun_active ? 'active-submenu' : '' ?>" 
                                           href="<?= site_url('ptk/detail/' . $k['kebun']) ?>" 
                                           style="font-size: 13.5px; font-weight: 500; white-space: normal; word-break: break-word; line-height: 1.4;">
                                             <i class="bi bi-circle-fill me-2 dot-icon flex-shrink-0" style="font-size: 6px; opacity: <?= $is_kebun_active ? '1' : '0.4' ?>; margin-top: 6px;"></i> 
                                             <span>Kebun <?= esc($k['kebun']) ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <div class="w-100 pb-4 pt-2 flex-shrink-0" style="background-color: #1a202c; box-shadow: 0 -10px 20px rgba(26, 32, 44, 0.95); z-index: 10;">
        <hr class="mx-3 my-2" style="border-color: rgba(255, 255, 255, 0.08); opacity: 1;">
        
        <div class="px-4 logout-wrapper">
            <a href="<?= site_url('auth/logout') ?>" class="btn btn-danger btn-sm w-100 fw-bold shadow-sm py-2.5 d-flex align-items-center justify-content-center gap-2 logout-btn" style="border-radius: 8px; font-size: 13.5px; background-color: #dc2626; border-color: #dc2626; letter-spacing: 0.2px;">
                <i class="bi bi-power fs-6"></i> <span class="sidebar-menu-text">Keluar Sistem</span>
            </a>
        </div>
    </div>

</div>

<style>
    /* Transisi Utama pada area Main agar smooth membesar/mengecil */
    main {
        transition: margin-left 0.3s ease, width 0.3s ease !important;
    }
    
    /* CLASS SAAT SIDEBAR TERBUKA (NORMAL) */
    .sidebar-expanded {
        width: 330px !important;
    }
    .main-expanded {
        margin-left: 330px !important;
        width: calc(100% - 330px) !important;
    }

    /* CLASS SAAT SIDEBAR TERTUTUP (MINIMIZED) */
    .sidebar-collapsed {
        width: 75px !important; /* Hanya sisakan ruang untuk Icon */
    }
    .main-collapsed {
        margin-left: 75px !important;
        width: calc(100% - 75px) !important;
    }

    /* Sembunyikan semua teks di dalam sidebar saat tertutup */
    .sidebar-collapsed .sidebar-menu-text, 
    .sidebar-collapsed .sidebar-brand-text {
        opacity: 0;
        visibility: hidden;
        width: 0;
        height: 0;
        display: none !important;
    }
    
    /* Posisikan tombol menu hamburger agar rapi saat tertutup */
    .sidebar-collapsed #toggleSidebarBtn {
        margin: 0 auto;
        width: 100%;
    }

    /* Perbaikan khusus tombol logout saat sidebar tertutup */
    .sidebar-collapsed .logout-wrapper {
        padding-left: 15px !important;
        padding-right: 15px !important;
    }
    .sidebar-collapsed .logout-btn {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    .sidebar-collapsed .logout-btn i {
        margin: 0 !important;
        font-size: 1.2rem !important;
    }

    .ptk-arrow.rotate {
        transform: rotate(180deg);
    }

    /* DESAIN SCROLLBAR INTERNAL MENU UTAMA SIDEBAR */
    #sidebarMenuScrollArea::-webkit-scrollbar {
        width: 4px;
    }
    #sidebarMenuScrollArea::-webkit-scrollbar-track {
        background: transparent;
    }
    #sidebarMenuScrollArea::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 4px;
    }
    #sidebarMenuScrollArea::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    /* Style Utama Hover Sidebar */
    .sidebar .nav-link.active {
        background-color: #16a34a !important; 
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2) !important;
    }
    .sidebar .nav-link:not(.active) {
        color: #cbd5e1 !important;
    }
    .sidebar .nav-link:hover:not(.active) {
        background-color: rgba(255, 255, 255, 0.06) !important;
        color: #ffffff !important;
        padding-left: 1rem !important; 
    }
    /* Matikan efek hover padding kiri saat sidebar sedang ditutup */
    .sidebar-collapsed .nav-link:hover:not(.active) {
        padding-left: 0.5rem !important; 
        justify-content: center !important;
    }
    .sidebar-collapsed .nav-link i {
        margin-right: 0 !important;
    }
    
    .sidebar .nav-link {
        transition: all 0.2s ease-in-out !important;
    }
    
    /* STYLE SUBMENU KEBUN */
    .custom-submenu {
        list-style-type: none !important;
        padding-left: 15px !important;
    }
    .custom-submenu .submenu-link {
        color: #94a3b8 !important;
        text-decoration: none !important;
        transition: all 0.15s ease-in-out;
    }
    .custom-submenu .submenu-link:hover {
        color: #ffffff !important;
        background-color: rgba(255, 255, 255, 0.05) !important;
    }

    /* KONDISI SUBMENU KEBUN AKTIF */
    .custom-submenu .submenu-link.active-submenu {
        color: #22c55e !important; 
        font-weight: 700 !important;
        background-color: rgba(34, 197, 94, 0.12) !important; 
    }
    .custom-submenu .submenu-link.active-submenu .dot-icon {
        color: #22c55e !important;
        opacity: 1 !important;
    }

    /* Utilitas Padding Custom */
    .py-2\.5 {
        padding-top: 0.65rem !important;
        padding-bottom: 0.65rem !important;
    }
    .my-0.5 {
        margin-top: 0.15rem !important;
        margin-bottom: 0.15rem !important;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- LOGIKA BUKA TUTUP SIDEBAR & RESIZE MAIN CONTENT ---
        const sidebar = document.getElementById('sidebar-wrapper');
        const toggleBtn = document.getElementById('toggleSidebarBtn');
        const mainContent = document.querySelector('main'); 
        
        // Cek LocalStorage untuk mengingat posisi terakhir
        const sidebarState = localStorage.getItem('sidebarState');
        
        // Terapkan state awal
        if (sidebarState === 'collapsed') {
            sidebar.classList.add('sidebar-collapsed');
            sidebar.classList.remove('sidebar-expanded');
            if (mainContent) {
                mainContent.classList.add('main-collapsed');
                mainContent.classList.remove('main-expanded', 'ms-sm-auto');
            }
        } else {
            sidebar.classList.add('sidebar-expanded');
            sidebar.classList.remove('sidebar-collapsed');
            if (mainContent) {
                mainContent.classList.add('main-expanded');
                mainContent.classList.remove('main-collapsed');
            }
        }

        // Event saat tombol hamburger ditekan
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                if (sidebar.classList.contains('sidebar-expanded')) {
                    sidebar.classList.replace('sidebar-expanded', 'sidebar-collapsed');
                    if (mainContent) {
                        mainContent.classList.replace('main-expanded', 'main-collapsed');
                        mainContent.classList.remove('ms-sm-auto');
                    }
                    localStorage.setItem('sidebarState', 'collapsed');
                    
                    let collapseMenu = bootstrap.Collapse.getInstance(document.getElementById('collapseMenuKebun'));
                    if (collapseMenu) collapseMenu.hide();

                } else {
                    sidebar.classList.replace('sidebar-collapsed', 'sidebar-expanded');
                    if (mainContent) {
                        mainContent.classList.replace('main-collapsed', 'main-expanded');
                    }
                    localStorage.setItem('sidebarState', 'expanded');
                }
            });
        }

        // --- LOGIKA LAINNYA BAWAAN SIDEBAR ---
        var myCollapse = document.getElementById('collapseMenuKebun');
        var arrow = document.querySelector('.ptk-arrow');
        
        if(myCollapse && arrow) {
            myCollapse.addEventListener('show.bs.collapse', function (e) {
                if(sidebar.classList.contains('sidebar-collapsed')) {
                    e.preventDefault();
                    toggleBtn.click(); 
                    return false;
                }
                arrow.classList.add('rotate');
            });
            myCollapse.addEventListener('hide.bs.collapse', function () {
                arrow.classList.remove('rotate');
            });
        }

        const menuScrollArea = document.getElementById('sidebarMenuScrollArea');
        if (menuScrollArea) {
            const savedScroll = localStorage.getItem("sidebarInternalScrollPos");
            if (savedScroll !== null) {
                menuScrollArea.scrollTop = savedScroll;
            }
            
            menuScrollArea.addEventListener("scroll", function() {
                localStorage.setItem("sidebarInternalScrollPos", menuScrollArea.scrollTop);
            });
            
            menuScrollArea.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function() {
                    localStorage.setItem("sidebarInternalScrollPos", menuScrollArea.scrollTop);
                });
            });
        }
    });
</script>