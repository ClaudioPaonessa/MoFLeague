<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en" ng-app="mfoApp">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="Claudio Paonessa" />
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">
        <title>MoF - Mansion of Fire</title>
        <link href="css/styles.css" rel="stylesheet" />

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

        <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
        <link href="https://cdn.datatables.net/searchpanes/1.2.1/css/searchPanes.dataTables.min.css" rel="stylesheet" crossorigin="anonymous" />
        <link href="https://cdn.datatables.net/select/1.3.1/css/select.dataTables.min.css" rel="stylesheet" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/2.5.6/ui-bootstrap-csp.min.css" integrity="sha512-3mC4Q7Z/awACW7Zf0QGvaU8dEXv862RQD6kmpNXTuiUV6X/sdl1QhiiN5z9x/iNpvMFsQ+NBD3TKGrFI3vP0QA==" crossorigin="anonymous" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat&display=swap" rel="stylesheet">
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand" href="#!tournaments">
                Mansion of Fire
            </a>
            <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0">
                
            </form>
            <!-- Navbar-->
            <ul class="navbar-nav ml-auto ml-md-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="userDropdown" data-target="#accountDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <div id="accountDropdown" class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        <a class="dropdown-item bg-danger text-white logout-button" href="/auth/logout">Logout</a>
                    </div>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Overview</div>
                            <a class="nav-link" href="#!tournaments">
                                <div class="sb-nav-link-icon"><i class="fas fa-list"></i></div>
                                Tournaments
                            </a>
                            <div class="sb-sidenav-menu-heading">Info</div>
                            <a class="nav-link" href="#!rules">
                                <div class="sb-nav-link-icon"><i class="fas fa-book"></i></div>
                                Rules
                            </a>
                            <?php if ($_SESSION["admin"]): ?>
                            <div class="sb-sidenav-menu-heading">Admin</div>
                            <a class="nav-link" href="#!admin_sets">
                                <div class="sb-nav-link-icon"><i class="fas fa-shield-alt"></i></div>
                                Import Sets
                            </a>
                            <a class="nav-link" href="#!admin_tournaments">
                                <div class="sb-nav-link-icon"><i class="fas fa-shield-alt"></i></div>
                                Manage Tournaments
                            </a>
                            <?php endif ?>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        <?php echo $_SESSION["displayName"] ?>
                        <?php if ($_SESSION["admin"]): ?>
                            <i class="fas fa-shield-alt"></i>
                        <?php endif ?>
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid" ng-view>
                        <!-- PAGE CONTENT -->
                    </div>
                </main>
                <footer class="py-4 bg-dark mt-auto">
                    <div class="container-fluid">
                        <div class="row small align-items-center footer-special">
                            <div class="col-xl-6">
                                <p>Copyright &copy; Mansion of Fire 2021</p>
                                <p class="text-muted small">Portions of Mansion of Fire are unofficial Fan Content permitted under the Wizards of the Coast Fan Content Policy. 
                                    The literal and graphical information presented on this site about Magic: The Gathering, including card images, 
                                    the mana symbols, and Oracle text, is copyright Wizards of the Coast, LLC, a subsidiary of Hasbro, 
                                    Mansion of Fire is not produced by, endorsed by, supported by, or affiliated with Wizards of the Coast.</p>
                            </div>
                            <div class="col-xl-6 text-right">
                                <a href="/privacy.html">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular-route.js"></script>
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/2.5.6/ui-bootstrap.min.js" integrity="sha512-DnqROrGrc9uBNiRGC7ZWLbctwtoVcD5005fL7pGUOkylaE7zXunb6xYUkD/nI0MYZn8XxReXnub2V/nspYUkUw==" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/2.5.6/ui-bootstrap-tpls.min.js" integrity="sha512-+qNXcNMBMY6Vx1oKJbdSsPsKS+bcB2yrprqb2jysz8bYC+vPJQsNBapHpq8zvf7oNGEhCgkFLKAsIeUXSkThHg==" crossorigin="anonymous"></script>

        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/angular-datatables/0.6.4/angular-datatables.js"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/searchpanes/1.2.1/js/dataTables.searchPanes.min.js"></script>
        <script src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/angular-filter/0.5.17/angular-filter.min.js" integrity="sha512-f2q5tYQJ0pnslHkuVw7tm7GP7E0BF1YLckJjgLU5z4p1vNz78Jv+nPIEKtZerevbt/HwEfYnRrAo9U3u4m0UHw==" crossorigin="anonymous"></script>

        <script src="app/routes.js"></script>
        <script src="app/helper/angularHelper.js"></script>

        <script src="app/controllers/homeController.js"></script>
        <script src="app/controllers/tournamentsController.js"></script>
        <script src="app/controllers/tournamentController.js"></script>
        <script src="app/controllers/rankingController.js"></script>
        <script src="app/controllers/rulesController.js"></script>
        <script src="app/controllers/poolController.js"></script>
        <script src="app/controllers/participantPoolController.js"></script>
        <script src="app/controllers/profileController.js"></script>
        <script src="app/controllers/adminSetsController.js"></script>
        <script src="app/controllers/adminTournamentsController.js"></script>
        <script src="app/controllers/adminTournamentController.js"></script>
        <script src="app/controllers/adminRankingController.js"></script>
    </body>
</html>
