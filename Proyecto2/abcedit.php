<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "abcinfo.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$abc_edit = NULL; // Initialize page object first

class cabc_edit extends cabc {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{370C7FDA-3444-41D7-BEB3-CE961691456E}";

	// Table name
	var $TableName = 'abc';

	// Page object name
	var $PageObjName = 'abc_edit';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Methods to clear message
	function ClearMessage() {
		$_SESSION[EW_SESSION_MESSAGE] = "";
	}

	function ClearFailureMessage() {
		$_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
	}

	function ClearSuccessMessage() {
		$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
	}

	function ClearWarningMessage() {
		$_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
	}

	function ClearMessages() {
		$_SESSION[EW_SESSION_MESSAGE] = "";
		$_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
		$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
		$_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-info ewInfo\">" . $sMessage . "</div>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-danger ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p>" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Footer exists, display
			echo "<p>" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}
	var $Token = "";
	var $TokenTimeout = 0;
	var $CheckToken = EW_CHECK_TOKEN;
	var $CheckTokenFn = "ew_CheckToken";
	var $CreateTokenFn = "ew_CreateToken";

	// Valid Post
	function ValidPost() {
		if (!$this->CheckToken || !ew_IsHttpPost())
			return TRUE;
		if (!isset($_POST[EW_TOKEN_NAME]))
			return FALSE;
		$fn = $this->CheckTokenFn;
		if (is_callable($fn))
			return $fn($_POST[EW_TOKEN_NAME], $this->TokenTimeout);
		return FALSE;
	}

	// Create Token
	function CreateToken() {
		global $gsToken;
		if ($this->CheckToken) {
			$fn = $this->CreateTokenFn;
			if ($this->Token == "" && is_callable($fn)) // Create token
				$this->Token = $fn();
			$gsToken = $this->Token; // Save to global variable
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language;
		global $UserTable, $UserTableConn;
		$GLOBALS["Page"] = &$this;
		$this->TokenTimeout = ew_SessionTimeoutTime();

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (abc)
		if (!isset($GLOBALS["abc"]) || get_class($GLOBALS["abc"]) == "cabc") {
			$GLOBALS["abc"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["abc"];
		}

		// Table object (usuarios)
		if (!isset($GLOBALS['usuarios'])) $GLOBALS['usuarios'] = new cusuarios();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'abc', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect($this->DBID);

		// User table object (usuarios)
		if (!isset($UserTable)) {
			$UserTable = new cusuarios();
			$UserTableConn = Conn($UserTable->DBID);
		}
	}

	//
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsCustomExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// Security
		$Security = new cAdvancedSecurity();
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		if ($Security->IsLoggedIn()) $Security->TablePermission_Loading();
		$Security->LoadCurrentUserLevel($this->ProjectID . $this->TableName);
		if ($Security->IsLoggedIn()) $Security->TablePermission_Loaded();
		if (!$Security->CanEdit()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage(ew_DeniedMsg()); // Set no permission
			if ($Security->CanList())
				$this->Page_Terminate(ew_GetUrl("abclist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->ID->SetVisibility();
		$this->ID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();
		$this->NombreProducto->SetVisibility();
		$this->FechaIngreso->SetVisibility();
		$this->Existencia->SetVisibility();
		$this->Proveedor->SetVisibility();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Check token
		if (!$this->ValidPost()) {
			echo $Language->Phrase("InvalidPostRequest");
			$this->Page_Terminate();
			exit();
		}

		// Process auto fill
		if (@$_POST["ajax"] == "autofill") {
			$results = $this->GetAutoFill(@$_POST["name"], @$_POST["q"]);
			if ($results) {

				// Clean output buffer
				if (!EW_DEBUG_ENABLED && ob_get_length())
					ob_end_clean();
				echo $results;
				$this->Page_Terminate();
				exit();
			}
		}

		// Create Token
		$this->CreateToken();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $gsExportFile, $gTmpImages;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();

		// Export
		global $EW_EXPORT, $abc;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($abc);
				$doc->Text = $sContent;
				if ($this->Export == "email")
					echo $this->ExportEmail($doc->Text);
				else
					$doc->Export();
				ew_DeleteTmpImages(); // Delete temp images
				exit();
			}
		}
		$this->Page_Redirecting($url);

		 // Close connection
		ew_CloseConn();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();

			// Handle modal response
			if ($this->IsModal) {
				$row = array();
				$row["url"] = $url;
				echo ew_ArrayToJson(array($row));
			} else {
				header("Location: " . $url);
			}
		}
		exit();
	}
	var $FormClassName = "form-horizontal ewForm ewEditForm";
	var $IsModal = FALSE;
	var $DbMasterFilter;
	var $DbDetailFilter;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;
		global $gbSkipHeaderFooter;

		// Check modal
		$this->IsModal = (@$_GET["modal"] == "1" || @$_POST["modal"] == "1");
		if ($this->IsModal)
			$gbSkipHeaderFooter = TRUE;

		// Load key from QueryString
		if (@$_GET["ID"] <> "") {
			$this->ID->setQueryStringValue($_GET["ID"]);
		}

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Check if valid key
		if ($this->ID->CurrentValue == "") {
			$this->Page_Terminate("abclist.php"); // Invalid key, return to list
		}

		// Validate form if post back
		if (@$_POST["a_edit"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = ""; // Form error, reset action
				$this->setFailureMessage($gsFormError);
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues();
			}
		}
		switch ($this->CurrentAction) {
			case "I": // Get a record to display
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("abclist.php"); // No matching record, return to list
				}
				break;
			Case "U": // Update
				$sReturnUrl = $this->getReturnUrl();
				if (ew_GetPageName($sReturnUrl) == "abclist.php")
					$sReturnUrl = $this->AddMasterUrl($sReturnUrl); // List page, return to list page with correct master key if necessary
				$this->SendEmail = TRUE; // Send email on update success
				if ($this->EditRow()) { // Update record based on key
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Update success
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} elseif ($this->getFailureMessage() == $Language->Phrase("NoRecord")) {
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Restore form values if update failed
				}
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Render the record
		$this->RowType = EW_ROWTYPE_EDIT; // Render as Edit
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Set up starting record parameters
	function SetUpStartRec() {
		if ($this->DisplayRecs == 0)
			return;
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET[EW_TABLE_START_REC] <> "") { // Check for "start" parameter
				$this->StartRec = $_GET[EW_TABLE_START_REC];
				$this->setStartRecordNumber($this->StartRec);
			} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
				$PageNo = $_GET[EW_TABLE_PAGE_NO];
				if (is_numeric($PageNo)) {
					$this->StartRec = ($PageNo-1)*$this->DisplayRecs+1;
					if ($this->StartRec <= 0) {
						$this->StartRec = 1;
					} elseif ($this->StartRec >= intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1) {
						$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1;
					}
					$this->setStartRecordNumber($this->StartRec);
				}
			}
		}
		$this->StartRec = $this->getStartRecordNumber();

		// Check if correct start record counter
		if (!is_numeric($this->StartRec) || $this->StartRec == "") { // Avoid invalid start record counter
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} elseif (intval($this->StartRec) > intval($this->TotalRecs)) { // Avoid starting record > total records
			$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to last page first record
			$this->setStartRecordNumber($this->StartRec);
		} elseif (($this->StartRec-1) % $this->DisplayRecs <> 0) {
			$this->StartRec = intval(($this->StartRec-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to page boundary
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm, $Language;

		// Get upload data
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->ID->FldIsDetailKey)
			$this->ID->setFormValue($objForm->GetValue("x_ID"));
		if (!$this->NombreProducto->FldIsDetailKey) {
			$this->NombreProducto->setFormValue($objForm->GetValue("x_NombreProducto"));
		}
		if (!$this->FechaIngreso->FldIsDetailKey) {
			$this->FechaIngreso->setFormValue($objForm->GetValue("x_FechaIngreso"));
			$this->FechaIngreso->CurrentValue = ew_UnFormatDateTime($this->FechaIngreso->CurrentValue, 0);
		}
		if (!$this->Existencia->FldIsDetailKey) {
			$this->Existencia->setFormValue($objForm->GetValue("x_Existencia"));
		}
		if (!$this->Proveedor->FldIsDetailKey) {
			$this->Proveedor->setFormValue($objForm->GetValue("x_Proveedor"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->ID->CurrentValue = $this->ID->FormValue;
		$this->NombreProducto->CurrentValue = $this->NombreProducto->FormValue;
		$this->FechaIngreso->CurrentValue = $this->FechaIngreso->FormValue;
		$this->FechaIngreso->CurrentValue = ew_UnFormatDateTime($this->FechaIngreso->CurrentValue, 0);
		$this->Existencia->CurrentValue = $this->Existencia->FormValue;
		$this->Proveedor->CurrentValue = $this->Proveedor->FormValue;
	}

	// Load row based on key values
	function LoadRow() {
		global $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn = &$this->Connection();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql, $conn);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		$this->ID->setDbValue($rs->fields('ID'));
		$this->NombreProducto->setDbValue($rs->fields('NombreProducto'));
		$this->FechaIngreso->setDbValue($rs->fields('FechaIngreso'));
		$this->Existencia->setDbValue($rs->fields('Existencia'));
		$this->Proveedor->setDbValue($rs->fields('Proveedor'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->ID->DbValue = $row['ID'];
		$this->NombreProducto->DbValue = $row['NombreProducto'];
		$this->FechaIngreso->DbValue = $row['FechaIngreso'];
		$this->Existencia->DbValue = $row['Existencia'];
		$this->Proveedor->DbValue = $row['Proveedor'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// ID
		// NombreProducto
		// FechaIngreso
		// Existencia
		// Proveedor

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// ID
		$this->ID->ViewValue = $this->ID->CurrentValue;
		$this->ID->ViewCustomAttributes = "";

		// NombreProducto
		$this->NombreProducto->ViewValue = $this->NombreProducto->CurrentValue;
		$this->NombreProducto->ViewCustomAttributes = "";

		// FechaIngreso
		$this->FechaIngreso->ViewValue = $this->FechaIngreso->CurrentValue;
		$this->FechaIngreso->ViewValue = ew_FormatDateTime($this->FechaIngreso->ViewValue, 0);
		$this->FechaIngreso->ViewCustomAttributes = "";

		// Existencia
		$this->Existencia->ViewValue = $this->Existencia->CurrentValue;
		$this->Existencia->ViewCustomAttributes = "";

		// Proveedor
		$this->Proveedor->ViewValue = $this->Proveedor->CurrentValue;
		if (strval($this->Proveedor->CurrentValue) <> "") {
			$sFilterWrk = "`ID`" . ew_SearchString("=", $this->Proveedor->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `ID`, `NombreProveedor` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `proveedores`";
		$sWhereWrk = "";
		$this->Proveedor->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Proveedor, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Proveedor->ViewValue = $this->Proveedor->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Proveedor->ViewValue = $this->Proveedor->CurrentValue;
			}
		} else {
			$this->Proveedor->ViewValue = NULL;
		}
		$this->Proveedor->ViewCustomAttributes = "";

			// ID
			$this->ID->LinkCustomAttributes = "";
			$this->ID->HrefValue = "";
			$this->ID->TooltipValue = "";

			// NombreProducto
			$this->NombreProducto->LinkCustomAttributes = "";
			$this->NombreProducto->HrefValue = "";
			$this->NombreProducto->TooltipValue = "";

			// FechaIngreso
			$this->FechaIngreso->LinkCustomAttributes = "";
			$this->FechaIngreso->HrefValue = "";
			$this->FechaIngreso->TooltipValue = "";

			// Existencia
			$this->Existencia->LinkCustomAttributes = "";
			$this->Existencia->HrefValue = "";
			$this->Existencia->TooltipValue = "";

			// Proveedor
			$this->Proveedor->LinkCustomAttributes = "";
			$this->Proveedor->HrefValue = "";
			$this->Proveedor->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// ID
			$this->ID->EditAttrs["class"] = "form-control";
			$this->ID->EditCustomAttributes = "";
			$this->ID->EditValue = $this->ID->CurrentValue;
			$this->ID->ViewCustomAttributes = "";

			// NombreProducto
			$this->NombreProducto->EditAttrs["class"] = "form-control";
			$this->NombreProducto->EditCustomAttributes = "";
			$this->NombreProducto->EditValue = ew_HtmlEncode($this->NombreProducto->CurrentValue);
			$this->NombreProducto->PlaceHolder = ew_RemoveHtml($this->NombreProducto->FldCaption());

			// FechaIngreso
			$this->FechaIngreso->EditAttrs["class"] = "form-control";
			$this->FechaIngreso->EditCustomAttributes = "";
			$this->FechaIngreso->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->FechaIngreso->CurrentValue, 8));
			$this->FechaIngreso->PlaceHolder = ew_RemoveHtml($this->FechaIngreso->FldCaption());

			// Existencia
			$this->Existencia->EditAttrs["class"] = "form-control";
			$this->Existencia->EditCustomAttributes = "";
			$this->Existencia->EditValue = ew_HtmlEncode($this->Existencia->CurrentValue);
			$this->Existencia->PlaceHolder = ew_RemoveHtml($this->Existencia->FldCaption());

			// Proveedor
			$this->Proveedor->EditAttrs["class"] = "form-control";
			$this->Proveedor->EditCustomAttributes = "";
			$this->Proveedor->EditValue = ew_HtmlEncode($this->Proveedor->CurrentValue);
			if (strval($this->Proveedor->CurrentValue) <> "") {
				$sFilterWrk = "`ID`" . ew_SearchString("=", $this->Proveedor->CurrentValue, EW_DATATYPE_NUMBER, "");
			$sSqlWrk = "SELECT `ID`, `NombreProveedor` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `proveedores`";
			$sWhereWrk = "";
			$this->Proveedor->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Proveedor, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = Conn()->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$arwrk = array();
					$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
					$this->Proveedor->EditValue = $this->Proveedor->DisplayValue($arwrk);
					$rswrk->Close();
				} else {
					$this->Proveedor->EditValue = ew_HtmlEncode($this->Proveedor->CurrentValue);
				}
			} else {
				$this->Proveedor->EditValue = NULL;
			}
			$this->Proveedor->PlaceHolder = ew_RemoveHtml($this->Proveedor->FldCaption());

			// Edit refer script
			// ID

			$this->ID->LinkCustomAttributes = "";
			$this->ID->HrefValue = "";

			// NombreProducto
			$this->NombreProducto->LinkCustomAttributes = "";
			$this->NombreProducto->HrefValue = "";

			// FechaIngreso
			$this->FechaIngreso->LinkCustomAttributes = "";
			$this->FechaIngreso->HrefValue = "";

			// Existencia
			$this->Existencia->LinkCustomAttributes = "";
			$this->Existencia->HrefValue = "";

			// Proveedor
			$this->Proveedor->LinkCustomAttributes = "";
			$this->Proveedor->HrefValue = "";
		}
		if ($this->RowType == EW_ROWTYPE_ADD ||
			$this->RowType == EW_ROWTYPE_EDIT ||
			$this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
			$this->SetupFieldTitles();
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!$this->NombreProducto->FldIsDetailKey && !is_null($this->NombreProducto->FormValue) && $this->NombreProducto->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->NombreProducto->FldCaption(), $this->NombreProducto->ReqErrMsg));
		}
		if (!$this->FechaIngreso->FldIsDetailKey && !is_null($this->FechaIngreso->FormValue) && $this->FechaIngreso->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->FechaIngreso->FldCaption(), $this->FechaIngreso->ReqErrMsg));
		}
		if (!ew_CheckDateDef($this->FechaIngreso->FormValue)) {
			ew_AddMessage($gsFormError, $this->FechaIngreso->FldErrMsg());
		}
		if (!$this->Existencia->FldIsDetailKey && !is_null($this->Existencia->FormValue) && $this->Existencia->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Existencia->FldCaption(), $this->Existencia->ReqErrMsg));
		}
		if (!ew_CheckInteger($this->Existencia->FormValue)) {
			ew_AddMessage($gsFormError, $this->Existencia->FldErrMsg());
		}
		if (!$this->Proveedor->FldIsDetailKey && !is_null($this->Proveedor->FormValue) && $this->Proveedor->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Proveedor->FldCaption(), $this->Proveedor->ReqErrMsg));
		}
		if (!ew_CheckInteger($this->Proveedor->FormValue)) {
			ew_AddMessage($gsFormError, $this->Proveedor->FldErrMsg());
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
	}

	// Update record based on key values
	function EditRow() {
		global $Security, $Language;
		$sFilter = $this->KeyFilter();
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$conn = &$this->Connection();
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE)
			return FALSE;
		if ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
			$EditRow = FALSE; // Update Failed
		} else {

			// Save old values
			$rsold = &$rs->fields;
			$this->LoadDbValues($rsold);
			$rsnew = array();

			// NombreProducto
			$this->NombreProducto->SetDbValueDef($rsnew, $this->NombreProducto->CurrentValue, "", $this->NombreProducto->ReadOnly);

			// FechaIngreso
			$this->FechaIngreso->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->FechaIngreso->CurrentValue, 0), ew_CurrentDate(), $this->FechaIngreso->ReadOnly);

			// Existencia
			$this->Existencia->SetDbValueDef($rsnew, $this->Existencia->CurrentValue, 0, $this->Existencia->ReadOnly);

			// Proveedor
			$this->Proveedor->SetDbValueDef($rsnew, $this->Proveedor->CurrentValue, 0, $this->Proveedor->ReadOnly);

			// Call Row Updating event
			$bUpdateRow = $this->Row_Updating($rsold, $rsnew);
			if ($bUpdateRow) {
				$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
				if (count($rsnew) > 0)
					$EditRow = $this->Update($rsnew, "", $rsold);
				else
					$EditRow = TRUE; // No field to update
				$conn->raiseErrorFn = '';
				if ($EditRow) {
				}
			} else {
				if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

					// Use the message, do nothing
				} elseif ($this->CancelMessage <> "") {
					$this->setFailureMessage($this->CancelMessage);
					$this->CancelMessage = "";
				} else {
					$this->setFailureMessage($Language->Phrase("UpdateCancelled"));
				}
				$EditRow = FALSE;
			}
		}

		// Call Row_Updated event
		if ($EditRow)
			$this->Row_Updated($rsold, $rsnew);
		$rs->Close();
		return $EditRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("abclist.php"), "", $this->TableVar, TRUE);
		$PageId = "edit";
		$Breadcrumb->Add("edit", $PageId, $url);
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		case "x_Proveedor":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `ID` AS `LinkFld`, `NombreProveedor` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `proveedores`";
			$sWhereWrk = "{filter}";
			$this->Proveedor->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`ID` = {filter_value}', "t0" => "3", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->Proveedor, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		}
	}

	// Setup AutoSuggest filters of a field
	function SetupAutoSuggestFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		case "x_Proveedor":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `ID`, `NombreProveedor` AS `DispFld` FROM `proveedores`";
			$sWhereWrk = "`NombreProveedor` LIKE '{query_value}%'";
			$this->Proveedor->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->Proveedor, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " LIMIT " . EW_AUTO_SUGGEST_MAX_ENTRIES;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		}
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($abc_edit)) $abc_edit = new cabc_edit();

// Page init
$abc_edit->Page_Init();

// Page main
$abc_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$abc_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "edit";
var CurrentForm = fabcedit = new ew_Form("fabcedit", "edit");

// Validate form
fabcedit.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	if ($fobj.find("#a_confirm").val() == "F")
		return true;
	var elm, felm, uelm, addcnt = 0;
	var $k = $fobj.find("#" + this.FormKeyCountName); // Get key_count
	var rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
	var gridinsert = $fobj.find("#a_list").val() == "gridinsert";
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = ($k[0]) ? String(i) : "";
		$fobj.data("rowindex", infix);
			elm = this.GetElements("x" + infix + "_NombreProducto");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $abc->NombreProducto->FldCaption(), $abc->NombreProducto->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_FechaIngreso");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $abc->FechaIngreso->FldCaption(), $abc->FechaIngreso->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_FechaIngreso");
			if (elm && !ew_CheckDateDef(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($abc->FechaIngreso->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Existencia");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $abc->Existencia->FldCaption(), $abc->Existencia->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Existencia");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($abc->Existencia->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Proveedor");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $abc->Proveedor->FldCaption(), $abc->Proveedor->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Proveedor");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($abc->Proveedor->FldErrMsg()) ?>");

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}

	// Process detail forms
	var dfs = $fobj.find("input[name='detailpage']").get();
	for (var i = 0; i < dfs.length; i++) {
		var df = dfs[i], val = df.value;
		if (val && ewForms[val])
			if (!ewForms[val].Validate())
				return false;
	}
	return true;
}

// Form_CustomValidate event
fabcedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fabcedit.ValidateRequired = true;
<?php } else { ?>
fabcedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fabcedit.Lists["x_Proveedor"] = {"LinkField":"x_ID","Ajax":true,"AutoFill":false,"DisplayFields":["x_NombreProveedor","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"proveedores"};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$abc_edit->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $abc_edit->ShowPageHeader(); ?>
<?php
$abc_edit->ShowMessage();
?>
<form name="fabcedit" id="fabcedit" class="<?php echo $abc_edit->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($abc_edit->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $abc_edit->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="abc">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<?php if ($abc_edit->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<div>
<?php if ($abc->ID->Visible) { // ID ?>
	<div id="r_ID" class="form-group">
		<label id="elh_abc_ID" class="col-sm-2 control-label ewLabel"><?php echo $abc->ID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $abc->ID->CellAttributes() ?>>
<span id="el_abc_ID">
<span<?php echo $abc->ID->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $abc->ID->EditValue ?></p></span>
</span>
<input type="hidden" data-table="abc" data-field="x_ID" name="x_ID" id="x_ID" value="<?php echo ew_HtmlEncode($abc->ID->CurrentValue) ?>">
<?php echo $abc->ID->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($abc->NombreProducto->Visible) { // NombreProducto ?>
	<div id="r_NombreProducto" class="form-group">
		<label id="elh_abc_NombreProducto" for="x_NombreProducto" class="col-sm-2 control-label ewLabel"><?php echo $abc->NombreProducto->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $abc->NombreProducto->CellAttributes() ?>>
<span id="el_abc_NombreProducto">
<input type="text" data-table="abc" data-field="x_NombreProducto" name="x_NombreProducto" id="x_NombreProducto" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($abc->NombreProducto->getPlaceHolder()) ?>" value="<?php echo $abc->NombreProducto->EditValue ?>"<?php echo $abc->NombreProducto->EditAttributes() ?>>
</span>
<?php echo $abc->NombreProducto->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($abc->FechaIngreso->Visible) { // FechaIngreso ?>
	<div id="r_FechaIngreso" class="form-group">
		<label id="elh_abc_FechaIngreso" for="x_FechaIngreso" class="col-sm-2 control-label ewLabel"><?php echo $abc->FechaIngreso->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $abc->FechaIngreso->CellAttributes() ?>>
<span id="el_abc_FechaIngreso">
<input type="text" data-table="abc" data-field="x_FechaIngreso" name="x_FechaIngreso" id="x_FechaIngreso" placeholder="<?php echo ew_HtmlEncode($abc->FechaIngreso->getPlaceHolder()) ?>" value="<?php echo $abc->FechaIngreso->EditValue ?>"<?php echo $abc->FechaIngreso->EditAttributes() ?>>
</span>
<?php echo $abc->FechaIngreso->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($abc->Existencia->Visible) { // Existencia ?>
	<div id="r_Existencia" class="form-group">
		<label id="elh_abc_Existencia" for="x_Existencia" class="col-sm-2 control-label ewLabel"><?php echo $abc->Existencia->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $abc->Existencia->CellAttributes() ?>>
<span id="el_abc_Existencia">
<input type="text" data-table="abc" data-field="x_Existencia" name="x_Existencia" id="x_Existencia" size="30" placeholder="<?php echo ew_HtmlEncode($abc->Existencia->getPlaceHolder()) ?>" value="<?php echo $abc->Existencia->EditValue ?>"<?php echo $abc->Existencia->EditAttributes() ?>>
</span>
<?php echo $abc->Existencia->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($abc->Proveedor->Visible) { // Proveedor ?>
	<div id="r_Proveedor" class="form-group">
		<label id="elh_abc_Proveedor" class="col-sm-2 control-label ewLabel"><?php echo $abc->Proveedor->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $abc->Proveedor->CellAttributes() ?>>
<span id="el_abc_Proveedor">
<?php
$wrkonchange = trim(" " . @$abc->Proveedor->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$abc->Proveedor->EditAttrs["onchange"] = "";
?>
<span id="as_x_Proveedor" style="white-space: nowrap; z-index: 8950">
	<input type="text" name="sv_x_Proveedor" id="sv_x_Proveedor" value="<?php echo $abc->Proveedor->EditValue ?>" size="30" placeholder="<?php echo ew_HtmlEncode($abc->Proveedor->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($abc->Proveedor->getPlaceHolder()) ?>"<?php echo $abc->Proveedor->EditAttributes() ?>>
</span>
<input type="hidden" data-table="abc" data-field="x_Proveedor" data-value-separator="<?php echo $abc->Proveedor->DisplayValueSeparatorAttribute() ?>" name="x_Proveedor" id="x_Proveedor" value="<?php echo ew_HtmlEncode($abc->Proveedor->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x_Proveedor" id="q_x_Proveedor" value="<?php echo $abc->Proveedor->LookupFilterQuery(true) ?>">
<script type="text/javascript">
fabcedit.CreateAutoSuggest({"id":"x_Proveedor","forceSelect":false});
</script>
</span>
<?php echo $abc->Proveedor->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
<?php if (!$abc_edit->IsModal) { ?>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("SaveBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $abc_edit->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
	</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fabcedit.Init();
</script>
<?php
$abc_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$abc_edit->Page_Terminate();
?>
