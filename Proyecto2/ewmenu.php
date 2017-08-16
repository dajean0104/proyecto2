<!-- Begin Main Menu -->
<?php $RootMenu = new cMenu(EW_MENUBAR_ID) ?>
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(1, "mi_abc", $Language->MenuPhrase("1", "MenuText"), "abclist.php", -1, "", IsLoggedIn() || AllowListMenu('{370C7FDA-3444-41D7-BEB3-CE961691456E}abc'), FALSE, FALSE);
$RootMenu->AddMenuItem(2, "mi_proveedores", $Language->MenuPhrase("2", "MenuText"), "proveedoreslist.php", -1, "", IsLoggedIn() || AllowListMenu('{370C7FDA-3444-41D7-BEB3-CE961691456E}proveedores'), FALSE, FALSE);
$RootMenu->AddMenuItem(-1, "mi_logout", $Language->Phrase("Logout"), "logout.php", -1, "", IsLoggedIn());
$RootMenu->AddMenuItem(-1, "mi_login", $Language->Phrase("Login"), "login.php", -1, "", !IsLoggedIn() && substr(@$_SERVER["URL"], -1 * strlen("login.php")) <> "login.php");
$RootMenu->Render();
?>
<!-- End Main Menu -->
