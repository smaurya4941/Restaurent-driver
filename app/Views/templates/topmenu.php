<body class="hold-transition sidebar-mini layout-fixed modern-ops">
<div class="wrapper">



  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light app-navbar">

<ul class="navbar-nav">
<li class="nav-item">
<a class="nav-link nav-icon-button" data-widget="pushmenu" href="#" role="button" aria-label="Toggle sidebar"><i class="fas fa-bars"></i></a>
</li>
<li class="nav-item d-none d-md-flex align-items-center">
<span class="navbar-page-kicker">Driver operations</span>
</li>

</ul>

<!-- <ul class="navbar-nav ml-auto">
<li class="nav-item">
<a href="<?= base_url('logout') ?>" class="btn btn-logout" title="Logout" aria-label="Logout">
<i class="fa-solid fa-right-from-bracket"></i>
<span class="d-none d-sm-inline ml-1">Logout</span>
</a>
</li>
<li>
<select id="languageSwitcher" class="form-control" style="width:150px;">
    <option value="en">English</option>
    <option value="hi">हिन्दी</option>
</select>

<div id="google_translate_element" style="display:none;"></div>
</li>
</ul> -->

<ul class="navbar-nav ml-auto d-flex flex-row align-items-center">

    <?php include 'app/Views/partials/branch_switcher.php'; ?>

    <!-- Language Switcher -->
    <li class="nav-item mr-2">
        <div class="d-flex align-items-center bg-white border rounded px-2"
             style="height:40px;">

            <i class="fa-solid fa-language text-primary mr-2"></i>

            <select id="languageSwitcher"
                    class="border-0 bg-transparent"
                    style="outline:none; box-shadow:none; font-size:14px; cursor:pointer;">

                <option value="en">English</option>
                <option value="hi">हिन्दी</option>
            </select>
        </div>

        <div id="google_translate_element" style="display:none;"></div>
    </li>

    <!-- Logout Button -->
    <li class="nav-item">
        <a href="<?= base_url('logout') ?>"
           class="btn btn-danger d-flex align-items-center px-3"
           style="height:40px; border-radius:8px; gap:6px;">

            <i class="fa-solid fa-right-from-bracket"></i>

            <span class="d-none d-sm-inline">
                Logout
            </span>
        </a>
    </li>

</ul>
</nav>
  <!-- /.navbar -->



