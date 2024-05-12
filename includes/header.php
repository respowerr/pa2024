<header>
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
          <a class="navbar-item" href="<?= '/index.php' ?>">
            <img src="<?= '/assets/img/atd_logo.png' ?>" alt="atd_header_logo">
          </a>
      
          <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbar">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
          </a>
        </div>
      
        <div id="navbar" class="navbar-menu">
          <div class="navbar-start">
            <a class="navbar-item" href="<?= '/index.php' ?>">
              Home
            </a>
            <a class="navbar-item" href="<?= '/public/about.php' ?>">
              About us
            </a>
            <a class="navbar-item" href="<?= '/public/missions.php' ?>">
              Our missions
            </a>
            <a class="navbar-item" href="<?= '/public/donation.php' ?>">
              Donation
            </a>
            <a class="navbar-item" href="<?= '/public/contact.php' ?>">
              Contact us
            </a>
          </div>
      
          <div class="navbar-end">
            <div class="navbar-item">
              <?php
                if(isset($_SESSION['username'])) {
                    echo '<p class="navbar-item" style="margin-right: 15px;">Welcome, ' . $_SESSION['username'] . '.</p>';
                    echo '<a class="navbar-item" href="/public/myprofil.php">My profil</a>';
                    echo '<a class="navbar-item" href="/public/events.php">Events</a>';
                    echo '<a class="navbar-item" href="/public/tickets.php">Tickets</a>';
                    echo '<a class="navbar-item" href="/public/warehouses.php">Warehouses</a>';
                    if(in_array('ROLE_ADMIN', $_SESSION['role'])){
                        echo '<a class="navbar-item" href="/admin/index.php">Admin panel</a>';
                    }
                    echo '<a class="button is-info" href="/public/logout.php">Logout</a>';
                } else {
              ?>
              <div class="buttons">
                <a class="button is-info" href="<?= '/public/register.php' ?>">
                  <strong>Join us</strong>
                </a>
                <a class="button is-light" href="<?= '/public/login.php' ?>">
                  Log in
                </a>
              </div>
              <?php
                }
              ?>
            </div>
          </div>
        </div>
    </nav>
</header>
