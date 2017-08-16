<!-- Begin Main Menu -->
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(1, "mmi_abc", $Language->MenuPhrase("1", "MenuText"), "abclist.php", -1, "", IsLoggedIn() || AllowListMenu('{370C7FDA-3444-41D7-BEB3-CE961691456E}abc'), FALSE, FALSE);
$RootMenu->AddMenuItem(2, "mmi_proveedores", $Language->MenuPhrase("2", "MenuText"), "proveedoreslist.php", -1, "", IsLoggedIn() || AllowListMenu('{370C7FDA-3444-41D7-BEB3-CE961691456E}proveedores'), FALSE, FALSE);
$RootMenu->AddMenuItem(-1, "mmi_logout", $Language->Phrase("Logout"), "logout.php", -1, "", IsLoggedIn());
$RootMenu->AddMenuItem(-1, "mmi_login", $Language->Phrase("Login"), "login.php", -1, "", !IsLoggedIn() && substr(@$_SERVER["URL"], -1 * strlen("login.php")) <> "login.php");
$RootMenu->Render();
?>
<!-- End Main Menu -->
