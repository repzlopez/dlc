<!-- 
- #HEADER
-->

<header class="header" data-header>
    <div class="container">

        <div class="overlay" data-overlay></div>

        <a href="<?php echo SHOP_URL; ?>" class="logo">
            <img src="/src/dlc_lifestyleshop _logo.png" height="50" alt="<?php echo SHOP_FULL; ?>">
        </a>

        <button class="nav-open-btn" data-nav-open-btn aria-label="Open Menu">
            <ion-icon name="menu-outline"></ion-icon>
        </button>

        <nav class="navbar" data-navbar>

            <button class="nav-close-btn" data-nav-close-btn aria-label="Close Menu">
                <ion-icon name="close-outline"></ion-icon>
            </button>

            <a href="<?php echo SHOP_URL; ?>" class="logo">
                <img src="/src/dlc_lifestyleshop _logo.png" height="50" alt="<?php echo SHOP_FULL; ?>">
            </a>

            <ul class="navbar-list">

                <li class="navbar-item">
                    <a href="<?php echo SHOP_URL; ?>" class="navbar-link">Home</a>
                </li>

                <!-- <li class="navbar-item">
                    <a href="#" class="navbar-link">About</a>
                </li> -->

                <li class="navbar-item">
                    <a href="products.php" class="navbar-link">Products</a>
                </li>

                <!-- <li class="navbar-item">
                    <a href="#" class="navbar-link">Shop</a>
                </li> -->

                <!-- <li class="navbar-item">
                    <a href="#" class="navbar-link">Blog</a>
                </li> -->

                <li class="navbar-item">
                    <a href="contact.php" class="navbar-link">Contact</a>
                </li>

            </ul>

            <ul class="nav-action-list">

                <li>
                    <button class="nav-action-btn">
                        <ion-icon name="search-outline" aria-hidden="true"></ion-icon>

                        <span class="nav-action-text">Search</span>
                    </button>
                </li>

                <!-- <li>
                    <a href="#" class="nav-action-btn">
                        <ion-icon name="person-outline" aria-hidden="true"></ion-icon>

                        <span class="nav-action-text">Login / Register</span>
                    </a>
                </li> -->

                <!-- <li>
                    <button class="nav-action-btn">
                        <ion-icon name="heart-outline" aria-hidden="true"></ion-icon>

                        <span class="nav-action-text">Wishlist</span>

                        <data class="nav-action-badge" value="5" aria-hidden="true">5</data>
                    </button>
                </li> -->

                <li>
                    <button class="nav-action-btn">
                        <ion-icon name="bag-outline" aria-hidden="true"></ion-icon>

                        <data class="nav-action-text" value="318.00">Basket: <strong>$318.00</strong></data>

                        <data class="nav-action-badge" value="4" aria-hidden="true">4</data>
                    </button>
                </li>

            </ul>

        </nav>

    </div>
</header>