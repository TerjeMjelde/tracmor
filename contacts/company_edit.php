<?php
/*
 * Copyright (c)  2006, Universal Diagnostic Solutions, Inc. 
 *
 * This file is part of Tracmor.  
 *
 * Tracmor is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version. 
 *	
 * Tracmor is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tracmor; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
?>

<?php
	require_once('../includes/prepend.inc.php');
	QApplication::Authenticate(4);
	require_once(__FORMBASE_CLASSES__ . '/CompanyEditFormBase.class.php');

	/**
	 * This is a quick-and-dirty draft form object to do Create, Edit, and Delete functionality
	 * of the Company class.  It extends from the code-generated
	 * abstract CompanyEditFormBase class.
	 *
	 * Any display custimizations and presentation-tier logic can be implemented
	 * here by overriding existing or implementing new methods, properties and variables.
	 *
	 * Additional qform control objects can also be defined and used here, as well.
	 * 
	 * @package Application
	 * @subpackage FormDraftObjects
	 * 
	 */
	class CompanyEditForm extends CompanyEditFormBase {
		
		// Header Tabs
		protected $ctlHeaderMenu;
		// Shortcut Menu
		protected $ctlShortcutMenu;
		
		// Address Object
		protected $objAddress;
		
		// Labels
		protected $lblShortDescription;
		protected $lblHeaderCompanyName;
		protected $lblWebsite;
		protected $lblEmail;
		protected $lblAddress;
		protected $lblTelephone;
		protected $lblFax;
		protected $lblCreationDate;
		protected $lblModifiedDate;
		protected $pnlLongDescription;
		
		// Primary Address inputs for new company
		protected $txtAddressShortDescription;
		protected $lstCountry;
		protected $txtAddress1;
		protected $txtAddress2;
		protected $txtCity;
		protected $lstStateProvince;
		protected $txtPostalCode;		
		
		// Buttons
		protected $btnEdit;
		protected $btnCreateContact;
		protected $btnCreateAddress;
		
		// Datagrids
		protected $dtgContact;
		protected $dtgAddress;
		
		protected function Form_Create() {
			// Call SetupCompany to either Load/Edit Existing or Create New
			$this->SetupCompany();
			
			// Create the Header Menu
			$this->ctlHeaderMenu_Create();
			// Create the Shortcut Menu
			$this->ctlShortcutMenu_Create();			
			
			// Create labels for Company information
			$this->lblShortDescription_Create();
			$this->lblHeaderCompanyName_Create();
			$this->lblWebsite_Create();
			$this->lblEmail_Create();
			$this->lblAddress_Create();
			$this->pnlLongDescription_Create();
			$this->lblTelephone_Create();
			$this->lblFax_Create();
			$this->lblCreationDate_Create();
			$this->lblModifiedDate_Create();
			$this->UpdateCompanyLabels();

			// Create/Setup Controls for Company's Data Fields
			$this->txtShortDescription_Create();
			$this->txtWebsite_Create();
			$this->txtEmail_Create();
			$this->txtLongDescription_Create();
			$this->lstAddress_Create();
			$this->txtTelephone_Create();
			$this->txtFax_Create();
			if (!$this->blnEditMode) {
				$this->txtAddressShortDescription_Create();
				$this->lstCountry_Create();
				$this->txtAddress1_Create();
				$this->txtAddress2_Create();
				$this->txtCity_Create();
				$this->lstStateProvince_Create();
				$this->txtPostalCode_Create();
			}
			$this->UpdateCompanyControls();

			// Create/Setup Button Action controls
			$this->btnEdit_Create();
			$this->btnSave_Create();
			$this->btnCancel_Create();
			$this->btnDelete_Create();
			$this->btnCreateAddress_Create();
			$this->btnCreateContact_Create();
			
			// Create/Setup Datagrids
			$this->dtgContact_Create();
			$this->dtgAddress_Create();
			
			// Display labels for the existing asset
			if ($this->blnEditMode) {
				$this->displayLabels();
			}
			// Display empty inputs to create a new asset
			else {
				$this->displayInputs();
			}
		}
		
		protected function SetupCompany() {
			parent::SetupCompany();
			QApplication::AuthorizeEntity($this->objCompany, $this->blnEditMode);
		}				
		
		// Assign the values for the Address and Contact datagrids
		protected function Form_PreRender() {
			$this->dtgContact->TotalItemCount = Contact::CountByCompanyId($this->objCompany->CompanyId);
			if ($this->dtgContact->TotalItemCount == 0) {
				$this->dtgContact->ShowHeader = false;
			}
			else {
				$objClauses = array();
				if ($objClause = $this->dtgContact->OrderByClause)
					array_push($objClauses, $objClause);
				if ($objClause = $this->dtgContact->LimitClause)
					array_push($objClauses, $objClause);
				$this->dtgContact->DataSource = Contact::LoadArrayByCompanyId($this->objCompany->CompanyId, $objClauses);
				$this->dtgContact->ShowHeader = true;
			}
			
			$this->dtgAddress->TotalItemCount = Address::CountByCompanyId($this->objCompany->CompanyId);
			if ($this->dtgAddress->TotalItemCount == 0) {
				$this->dtgAddress->ShowHeader = false;
			}
			else {
				$objClauses = array();
				if ($objClause = $this->dtgAddress->OrderByClause)
					array_push($objClauses, $objClause);
				if ($objClause = $this->dtgAddress->LimitClause)
					array_push($objClauses, $objClause);
				$this->dtgAddress->DataSource = Address::LoadArrayByCompanyId($this->objCompany->CompanyId, $objClauses);
				$this->dtgAddress->ShowHeader = true;
			}
			// Do not show the datagrids or 'Create New' buttons if creating a new company
			if (!$this->blnEditMode) {
				$this->btnCreateContact->Visible = false;
				$this->dtgContact->Visible = false;
				$this->btnCreateAddress->Visible = false;
				$this->dtgAddress->Visible = false;
			}
		}
		
  	// Create and Setup the Header Composite Control
  	protected function ctlHeaderMenu_Create() {
  		$this->ctlHeaderMenu = new QHeaderMenu($this);
  	}

  	// Create and Setp the Shortcut Menu Composite Control
  	protected function ctlShortcutMenu_Create() {
  		$this->ctlShortcutMenu = new QShortcutMenu($this);
  	}		
		
		// Setup the Short Description (Company Name) Label
		protected function lblShortDescription_Create() {
			$this->lblShortDescription = new QLabel($this);
			$this->lblShortDescription->Name = 'Company Name';
		}
		
		// Setup the header (Company Name) Label
		protected function lblHeaderCompanyName_Create() {
			$this->lblHeaderCompanyName = new QLabel($this);
		}
		
		// Setup the Website Label
		protected function lblWebsite_Create() {
			$this->lblWebsite = new QLabel($this);
			$this->lblWebsite->Name = 'Website';
		}
		
		// Setup the Email Label
		protected function lblEmail_Create() {
			$this->lblEmail = new QLabel($this);
			$this->lblEmail->Name = 'Email';
		}
		
		// Setup the Long Description Panel
		protected function pnlLongDescription_Create() {
			$this->pnlLongDescription = new QPanel($this);
			$this->pnlLongDescription->CssClass='scrollBox';
			$this->pnlLongDescription->Name = 'Description';
		}
		
		// Setup the Address Label (Primary Address)
		protected function lblAddress_Create() {
			$this->lblAddress = new QLabel($this);
			$this->lblAddress->Name = 'Primary Address';
		}
		
		// Setup the Telephone Label
		protected function lblTelephone_Create() {
			$this->lblTelephone = new QLabel($this);
			$this->lblTelephone->Name = 'Telephone';
		}
		
		// Setup the Fax Label
		protected function lblFax_Create() {
			$this->lblFax = new QLabel($this);
			$this->lblFax->Name = 'Fax';
		}

		// Create the Creation Date Label
		protected function lblCreationDate_Create() {
			$this->lblCreationDate = new QLabel($this);
			$this->lblCreationDate->Name = 'Date Created';
			if (!$this->blnEditMode) {
				$this->lblCreationDate->Visible = false;
			}
		}
		
		// Create the Modified Date Label
		protected function lblModifiedDate_Create() {
			$this->lblModifiedDate = new QLabel($this);
			$this->lblModifiedDate->Name = 'Last Modified';
			if (!$this->blnEditMode) {
				$this->lblModifiedDate->Visible = false;
			}
		}
		
		// Create the Company Name Text Field
		protected function txtShortDescription_Create() {
			parent::txtShortDescription_Create();
			$this->txtShortDescription->CausesValidation = true;
			$this->txtShortDescription->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnSave_Click'));
			$this->txtShortDescription->AddAction(new QEnterKeyEvent(), new QTerminateAction());
			$this->txtShortDescription->TabIndex=1;
			QApplication::ExecuteJavaScript(sprintf("document.getElementById('%s').focus()", $this->txtShortDescription->ControlId));
		}
		
		// Create the Website Text Field
		protected function txtWebsite_Create() {
			parent::txtWebsite_Create();
			$this->txtWebsite->CausesValidation = true;
			$this->txtWebsite->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnSave_Click'));
			$this->txtWebsite->AddAction(new QEnterKeyEvent(), new QTerminateAction());
			$this->txtWebsite->TabIndex=3;
		}
		
		// Create the LongDescription Text Field
		protected function txtLongDescription_Create() {
			parent::txtLongDescription_Create();
			$this->txtLongDescription->TabIndex=2;
			
		}
		
		// Create the Telephone Text Field
		protected function txtTelephone_Create() {
			parent::txtTelephone_Create();
			$this->txtTelephone->CausesValidation = true;
			$this->txtTelephone->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnSave_Click'));
			$this->txtTelephone->AddAction(new QEnterKeyEvent(), new QTerminateAction());
			if ($this->blnEditMode) {
				if ($this->objCompany->CompanyId == QApplication::$TracmorSettings->CompanyId) {
					$this->txtTelephone->Required = true;
				}
			}
			$this->txtTelephone->TabIndex=5;
		}		
		
		// Create the Email Text Field
		protected function txtEmail_Create() {
			parent::txtEmail_Create();
			$this->txtEmail->CausesValidation = true;
			$this->txtEmail->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnSave_Click'));
			$this->txtEmail->AddAction(new QEnterKeyEvent(), new QTerminateAction());
			$this->txtEmail->TabIndex=4;		
		}
		
		// Create the Telephone Fax Field
		protected function txtFax_Create() {
			parent::txtFax_Create();
			$this->txtFax->CausesValidation = true;
			$this->txtFax->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnSave_Click'));
			$this->txtFax->AddAction(new QEnterKeyEvent(), new QTerminateAction());
			$this->txtFax->TabIndex=6;	
		}
		
		// Create and Setup lstAddress
		protected function lstAddress_Create() {
			$this->lstAddress = new QListBox($this);
			$this->lstAddress->Name = QApplication::Translate('Address');
			$this->lstAddress->AddItem('- Select One -', null);
			$objAddressArray = $this->objCompany->GetAddressArray();
			if ($objAddressArray) foreach ($objAddressArray as $objAddress) {
				$objListItem = new QListItem($objAddress->__toString(), $objAddress->AddressId);
				$this->lstAddress->AddItem($objListItem);
			}
			$this->lstAddress->TabIndex=7;
		}
		
		// Create and Setup txtShortDescription
		protected function txtAddressShortDescription_Create() {
			$this->txtAddressShortDescription = new QTextBox($this);
			$this->txtAddressShortDescription->Name = QApplication::Translate('Address Name');
			$this->txtAddressShortDescription->CausesValidation = true;
			$this->txtAddressShortDescription->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnSave_Click'));
			$this->txtAddressShortDescription->AddAction(new QEnterKeyEvent(), new QTerminateAction());
			$this->txtAddressShortDescription->TabIndex = 8;
		}

		// Create the Country Input
		protected function lstCountry_Create() {
			$this->lstCountry = new QListBox($this);
			$this->lstCountry->Name = QApplication::Translate('Country');
			if (!$this->blnEditMode) {
				$this->lstCountry->AddItem('- Select One -', null);
				$this->lstCountry->AddItem('United States', 228);
			}	
			$objCountryArray = Country::LoadAll();
			if ($objCountryArray) foreach ($objCountryArray as $objCountry) {
				$objListItem = new QListItem($objCountry->__toString(), $objCountry->CountryId);
				$this->lstCountry->AddItem($objListItem);
			}
			$this->lstCountry->AddAction(new QChangeEvent(), new QAjaxAction('lstCountry_Select'));
			$this->lstCountry->TabIndex = 14;
		}

		// Create and Setup txtAddress1
		protected function txtAddress1_Create() {
			$this->txtAddress1 = new QTextBox($this);
			$this->txtAddress1->Name = QApplication::Translate('Address 1');
			$this->txtAddress1->CausesValidation = true;
			$this->txtAddress1->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnSave_Click'));
			$this->txtAddress1->AddAction(new QEnterKeyEvent(), new QTerminateAction());			
			$this->txtAddress1->TabIndex = 9;
		}

		// Create and Setup txtAddress2
		protected function txtAddress2_Create() {
			$this->txtAddress2 = new QTextBox($this);
			$this->txtAddress2->Name = QApplication::Translate('Address 2');
			$this->txtAddress2->CausesValidation = true;
			$this->txtAddress2->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnSave_Click'));
			$this->txtAddress2->AddAction(new QEnterKeyEvent(), new QTerminateAction());			
			$this->txtAddress2->TabIndex = 10;
		}

		// Create and Setup txtCity
		protected function txtCity_Create() {
			$this->txtCity = new QTextBox($this);
			$this->txtCity->Name = QApplication::Translate('City');
			$this->txtCity->CausesValidation = true;
			$this->txtCity->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnSave_Click'));
			$this->txtCity->AddAction(new QEnterKeyEvent(), new QTerminateAction());			
			$this->txtCity->TabIndex = 11;
		}

		// Create and Setup lstStateProvince
		protected function lstStateProvince_Create() {
			$this->lstStateProvince = new QListBox($this);
			$this->lstStateProvince->Name = QApplication::Translate('State Province');
			$this->lstStateProvince->AddItem('- Select One -', null);
			$objStateProvinceArray = StateProvince::LoadAll();
			if ($objStateProvinceArray) foreach ($objStateProvinceArray as $objStateProvince) {
				$objListItem = new QListItem($objStateProvince->__toString(), $objStateProvince->StateProvinceId);
				$this->lstStateProvince->AddItem($objListItem);
			}
			$this->lstStateProvince->TabIndex = 12;
		}

		// Create and Setup txtPostalCode
		protected function txtPostalCode_Create() {
			$this->txtPostalCode = new QTextBox($this);
			$this->txtPostalCode->Name = QApplication::Translate('Postal Code');
			$this->txtPostalCode->CausesValidation = true;
			$this->txtPostalCode->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnSave_Click'));
			$this->txtPostalCode->AddAction(new QEnterKeyEvent(), new QTerminateAction());		
			$this->txtPostalCode->TabIndex = 13;
		}		
		
		// Setup Edit Button
		protected function btnEdit_Create() {
		  $this->btnEdit = new QButton($this);
	    $this->btnEdit->Text = 'Edit';
	    $this->btnEdit->AddAction(new QClickEvent(), new QAjaxAction('btnEdit_Click'));
	    $this->btnEdit->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnEdit_Click'));
	    $this->btnEdit->AddAction(new QEnterKeyEvent(), new QTerminateAction());
	    $this->btnEdit->CausesValidation = false;
	    QApplication::AuthorizeControl($this->objCompany, $this->btnEdit, 2);	    
		}
		
		// Setup Save Button
		protected function btnSave_Create() {
			$this->btnSave = new QButton($this);
			$this->btnSave->Text = 'Save';
			$this->btnSave->AddAction(new QClickEvent(), new QAjaxAction('btnSave_Click'));
			$this->btnSave->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnSave_Click'));
			$this->btnSave->AddAction(new QEnterKeyEvent(), new QTerminateAction());
			$this->btnSave->TabIndex=15;
		}
		
		// Setup Cancel Button
		protected function btnCancel_Create() {
			$this->btnCancel = new QButton($this);
			$this->btnCancel->Text = 'Cancel';
			$this->btnCancel->AddAction(new QClickEvent(), new QAjaxAction('btnCancel_Click'));
			$this->btnCancel->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnCancel_Click'));
			$this->btnCancel->AddAction(new QEnterKeyEvent(), new QTerminateAction());
			$this->btnCancel->CausesValidation = false;
		}
		
		// Setup Delete Button
		protected function btnDelete_Create() {
			$this->btnDelete = new QButton($this);
			$this->btnDelete->Text = 'Delete';
			$this->btnDelete->AddAction(new QClickEvent(), new QConfirmAction(sprintf(QApplication::Translate('Are you SURE you want to DELETE this %s?'), 'Company')));
			$this->btnDelete->AddAction(new QClickEvent(), new QAjaxAction('btnDelete_Click'));
			$this->btnDelete->AddAction(new QEnterKeyEvent(), new QConfirmAction(sprintf(QApplication::Translate('Are you SURE you want to DELETE this %s?'), 'Company')));
			$this->btnDelete->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnDelete_Click'));
			$this->btnDelete->AddAction(new QEnterKeyEvent(), new QTerminateAction());
			$this->btnDelete->CausesValidation = false;
			QApplication::AuthorizeControl($this->objCompany, $this->btnDelete, 3);
		}
		
		// Setup Create Address Button
		protected function btnCreateAddress_Create() {
			$this->btnCreateAddress = new QButton($this);
			$this->btnCreateAddress->Text = 'Create New Address';
			$this->btnCreateAddress->AddAction(new QClickEvent(), new QServerAction('btnCreateAddress_Click'));
			$this->btnCreateAddress->AddAction(new QEnterKeyEvent(), new QServerAction('btnCreateAddress_Click'));
			$this->btnCreateAddress->AddAction(new QEnterKeyEvent(), new QTerminateAction());
			$this->btnCreateAddress->CausesValidation = false;
	    QApplication::AuthorizeControl($this->objCompany, $this->btnCreateAddress, 2);				
		}
		
		// Setup Create Contact Button
		protected function btnCreateContact_Create() {
			$this->btnCreateContact = new QButton($this);
			$this->btnCreateContact->Text = 'Create New Contact';
			$this->btnCreateContact->AddAction(new QClickEvent(), new QServerAction('btnCreateContact_Click'));
			$this->btnCreateContact->AddAction(new QEnterKeyEvent(), new QServerAction('btnCreateContact_Click'));
			$this->btnCreateContact->AddAction(new QEnterKeyEvent(), new QTerminateAction());
			$this->btnCreateContact->CausesValidation = false;
			QApplication::AuthorizeControl($this->objCompany, $this->btnCreateContact, 2);	
		}		

		// Setup Contacts Datagrid
		protected function dtgContact_Create() {
			$this->dtgContact = new QDataGrid($this);
  		$this->dtgContact->CellPadding = 5;
  		$this->dtgContact->CellSpacing = 0;
  		$this->dtgContact->CssClass = "datagrid";
      		
      // Enable AJAX - this won't work while using the DB profiler
      $this->dtgContact->UseAjax = true;

      // Enable Pagination, and set to 20 items per page
      $objPaginator = new QPaginator($this->dtgContact);
      $this->dtgContact->Paginator = $objPaginator;
      $this->dtgContact->ItemsPerPage = 10;
      
      $this->dtgContact->AddColumn(new QDataGridColumn('Name', '<?= $_ITEM->__toStringWithLink("bluelink") ?>', array('OrderByClause' => QQ::OrderBy(QQN::Contact()->LastName), 'ReverseOrderByClause' => QQ::OrderBy(QQN::Contact()->LastName, false), 'CssClass' => "dtg_column", 'HtmlEntities' => false)));
      $this->dtgContact->AddColumn(new QDataGridColumn('Title', '<?= $_ITEM->Title ?>', array('OrderByClause' => QQ::OrderBy(QQN::Contact()->Title), 'ReverseOrderByClause' => QQ::OrderBy(QQN::Contact()->Title, false), 'Width' => "200", 'CssClass' => "dtg_column")));
      $this->dtgContact->AddColumn(new QDataGridColumn('Email', '<?= $_ITEM->Email ?>', array('OrderByClause' => QQ::OrderBy(QQN::Contact()->Email), 'ReverseOrderByClause' => QQ::OrderBy(QQN::Contact()->Email, false), 'CssClass' => "dtg_column")));
      
      $this->dtgContact->SortColumnIndex = 0;
    	$this->dtgContact->SortDirection = 0;
      
      $objStyle = $this->dtgContact->RowStyle;
      $objStyle->ForeColor = '#000000';
      $objStyle->BackColor = '#FFFFFF';
      $objStyle->FontSize = 12;

      $objStyle = $this->dtgContact->AlternateRowStyle;
      $objStyle->BackColor = '#EFEFEF';

      $objStyle = $this->dtgContact->HeaderRowStyle;
      $objStyle->ForeColor = '#000000';
      $objStyle->BackColor = '#EFEFEF';
      $objStyle->CssClass = 'dtg_header';
		}
		
		// Setup Contacts Datagrid
		protected function dtgAddress_Create() {
			$this->dtgAddress = new QDataGrid($this);
  		$this->dtgAddress->CellPadding = 5;
  		$this->dtgAddress->CellSpacing = 0;
  		$this->dtgAddress->CssClass = "datagrid";
      		
      // Enable AJAX - this won't work while using the DB profiler
      $this->dtgAddress->UseAjax = true;

      // Enable Pagination, and set to 20 items per page
      $objPaginator = new QPaginator($this->dtgAddress);
      $this->dtgAddress->Paginator = $objPaginator;
      $this->dtgAddress->ItemsPerPage = 10;
          
      $this->dtgAddress->AddColumn(new QDataGridColumn('Name', '<?= $_ITEM->__toStringWithLink("bluelink") ?>', array('OrderByClause' => QQ::OrderBy(QQN::Address()->ShortDescription), 'ReverseOrderByClause' => QQ::OrderBy(QQN::Address()->ShortDescription, false), 'HtmlEntities' => false, 'CssClass' => "dtg_column")));
      $this->dtgAddress->AddColumn(new QDataGridColumn('Address', '<?= $_ITEM->__toStringFullAddress() ?>', array('Width' => "200", 'CssClass' => "dtg_column", 'HtmlEntities' => false)));
      
      $this->dtgAddress->SortColumnIndex = 0;
    	$this->dtgAddress->SortDirection = 0;
      
      $objStyle = $this->dtgAddress->RowStyle;
      $objStyle->ForeColor = '#000000';
      $objStyle->BackColor = '#FFFFFF';
      $objStyle->FontSize = 12;

      $objStyle = $this->dtgAddress->AlternateRowStyle;
      $objStyle->BackColor = '#EFEFEF';

      $objStyle = $this->dtgAddress->HeaderRowStyle;
      $objStyle->ForeColor = '#000000';
      $objStyle->BackColor = '#EFEFEF';
      $objStyle->CssClass = 'dtg_header';
		}
		
		// Update state/province list when country is selected for Primary address if creating a new company
		protected function lstCountry_Select() {
			
			// Save the currently selected StateProvince
			$intStateProvinceId = $this->lstStateProvince->SelectedValue;
			// Clear out the items from lstAddress
			$this->lstStateProvince->RemoveAllItems();
			if ($this->lstCountry->SelectedValue) {
				// Load the selected country
				$objCountry = Country::Load($this->lstCountry->SelectedValue);
				// Get all available state/provinces for that company
				$objStateProvinceArray = $objCountry->GetStateProvinceArray();
			}
			else {
				// Or load all addresses for all companies
				$objStateProvinceArray = StateProvince::LoadAll();
			}
			$this->lstStateProvince->AddItem('- Select One -', null);
			if ($objStateProvinceArray) foreach ($objStateProvinceArray as $objStateProvince) {
				// Create a new ListItem Object
				$objListItem = new QListItem($objStateProvince->__toString(), $objStateProvince->StateProvinceId);
				// If this State/Province is the one previously selected, make it selected again
				if ($intStateProvinceId == $objStateProvince->StateProvinceId)
					$objListItem->Selected = true;
				// Add the ListItem object
				$this->lstStateProvince->AddItem($objListItem);
				// Enable the input
				$this->lstStateProvince->Enabled = true;
			}
			// If there are no State/Provinces for a country, disable the input
			else {
				$this->lstStateProvince->Enabled = false;
			}
		}		
		
		// Edit Button Click
		protected function btnEdit_Click($strFormId, $strControlId, $strParameter) {

			// Hide labels and display inputs where appropriate
			$this->displayInputs();
		}
		
		// Control ServerActions
		protected function btnSave_Click($strFormId, $strControlId, $strParameter) {
			
			try {
				
				$blnError = false;
				
				if (!$this->blnEditMode) {
					
					if ($this->txtAddressShortDescription->Text) {
						if (!$this->txtAddress1->Text) {
							$this->txtAddress1->Warning = 'Address is a required field.';
							$blnError = true;
						}
						if (!$this->txtCity->Text) {
							$this->txtCity->Warning = 'City is a required field.';
							$blnError = true;
						}
						if (!$this->txtPostalCode->Text) {
							$this->txtPostalCode->Warning = 'Postal Code is a required field.';
							$blnError = true;
						}
						if (!$this->lstCountry->SelectedValue) {
							$this->lstCountry->Warning = 'Country is a required field.';
							$blnError = true;
						}
						if ($blnError) {
							return;
						}
					}
					elseif ($this->txtAddress1->Text || $this->txtAddress2->Text || $this->txtCity->Text || $this->lstStateProvince->SelectedValue || $this->txtPostalCode->Text || $this->lstCountry->SelectedValue) {
						$this->txtAddressShortDescription->Warning = 'Address Name is a required field.';
						return;
					}
				}
				
				
				$this->UpdateCompanyFields();
				$this->objCompany->Save();
				$this->SaveNewAddress();
	
				if ($this->blnEditMode) {
					$this->SetupCompany();
					$this->UpdateCompanyLabels();
					$this->DisplayLabels();
				}
				elseif (!$this->blnEditMode) {
					QApplication::Redirect('company_edit.php?intCompanyId='.$this->objCompany->CompanyId);
				}
			}
			catch (QExtendedOptimisticLockingException $objExc) {
				$this->btnCancel->Warning = sprintf('This company has been updated by another user. You must <a href="company_edit.php?intCompanyId=%s">Refresh</a> to edit this company.', $this->objCompany->CompanyId);
			}			
		}
		
		// Cancel Button Click Actions
		protected function btnCancel_Click($strFormId, $strControlId, $strParameter) {
			if ($this->blnEditMode) {
				$this->DisplayLabels();
				$this->UpdateCompanyControls();
			}
			else {
				QApplication::Redirect('company_list.php');
			}
		}
		
		protected function btnDelete_Click($strFormId, $strControlId, $strParameter) {

			// $this->objCompany->AddressId = null;
			// $this->objCompany->Save();
			parent::btnDelete_Click($strFormId, $strControlId, $strParameter);
		}		
		
		protected function btnCreateAddress_Click($strFormId, $strControlId, $strParameter) {
			QApplication::Redirect('address_edit.php?intCompanyId='.$this->objCompany->CompanyId);
		}
		
		protected function btnCreateContact_Click($strFormId, $strControlId, $strParameter) {
			QApplication::Redirect('contact_edit.php?intCompanyId='.$this->objCompany->CompanyId);
		}
		
		// Protected Update Methods
		protected function UpdateCompanyFields() {
			$this->objCompany->AddressId = $this->lstAddress->SelectedValue;
			$this->objCompany->ShortDescription = $this->txtShortDescription->Text;
			$this->objCompany->Website = $this->txtWebsite->Text;
			$this->objCompany->Telephone = $this->txtTelephone->Text;
			$this->objCompany->Fax = $this->txtFax->Text;
			$this->objCompany->Email = $this->txtEmail->Text;
			$this->objCompany->LongDescription = $this->txtLongDescription->Text;
		}
		
		// Save Primary Address for new Companies {
		protected function SaveNewAddress() {
			
			if (!$this->blnEditMode && $this->txtAddressShortDescription->Text) {
				$this->objAddress = new Address();
				$this->objAddress->CompanyId = $this->objCompany->CompanyId;
				$this->objAddress->ShortDescription = $this->txtAddressShortDescription->Text;
				$this->objAddress->Address1 = $this->txtAddress1->Text;
				$this->objAddress->Address2 = $this->txtAddress2->Text;
				$this->objAddress->City = $this->txtCity->Text;
				$this->objAddress->StateProvinceId = $this->lstStateProvince->SelectedValue;
				$this->objAddress->PostalCode = $this->txtPostalCode->Text;
				$this->objAddress->CountryId = $this->lstCountry->SelectedValue;
				$this->objAddress->Save();
				$this->objCompany->AddressId = $this->objAddress->AddressId;
				$this->objCompany->Save();
			}
		}
		
		// Update Company Controls
		protected function UpdateCompanyControls() {
			$this->lstAddress->SelectedValue = $this->objCompany->AddressId;
			$this->txtShortDescription->Text = $this->objCompany->ShortDescription;
			$this->txtWebsite->Text = $this->objCompany->Website;
			$this->txtTelephone->Text = $this->objCompany->Telephone;
			$this->txtFax->Text = $this->objCompany->Fax;
			$this->txtEmail->Text = $this->objCompany->Email;
			$this->txtLongDescription->Text = $this->objCompany->LongDescription;
		}
		
		// Update the Company Labels
		protected function UpdateCompanyLabels() {
			
			if ($this->blnEditMode) {
				$this->lblHeaderCompanyName->Text = $this->objCompany->__toString();
			} else {
				$this->lblHeaderCompanyName->Text = 'New Company';
			}
			$this->lblShortDescription->Text = $this->objCompany->ShortDescription;
			$this->lblWebsite->Text = $this->objCompany->Website;
			$this->lblEmail->Text = $this->objCompany->Email;
			$this->pnlLongDescription->Text = nl2br($this->objCompany->LongDescription);
			if ($this->objCompany->Address) {
				$this->lblAddress->Text = $this->objCompany->Address->__toString();
			}
			else {
				$this->lblAddress->Text = "";
			}
			$this->lblTelephone->Text = $this->objCompany->Telephone;
			$this->lblFax->Text = $this->objCompany->Fax;
			if ($this->objCompany->CreationDate) {
				$this->lblCreationDate->Text = $this->objCompany->CreationDate->PHPDate('Y-m-d H:i:s') . ' by ' . $this->objCompany->CreatedByObject->__toStringFullName();
			}
			if ($this->objCompany->ModifiedDate) {
				$this->lblModifiedDate->Text = $this->objCompany->ModifiedDate . ' by ' . $this->objCompany->ModifiedByObject->__toStringFullName();
			}

		}
		
		// Display the labels and buttons for Company Viewing mode
		protected function DisplayLabels() {
	
			// Do not display inputs
			$this->txtShortDescription->Display = false;
			$this->txtWebsite->Display = false;
			$this->txtEmail->Display = false;
			$this->txtLongDescription->Display = false;
			$this->lstAddress->Display = false;
			$this->txtTelephone->Display = false;
			$this->txtFax->Display = false;
			
			// Do not display Cancel and Save buttons
			$this->btnCancel->Display = false;
			$this->btnSave->Display = false;		
			
			// Display Labels for Viewing mode
			$this->lblShortDescription->Display = true;
			$this->lblWebsite->Display = true;
			$this->lblEmail->Display = true;
			$this->pnlLongDescription->Display = true;
			$this->lblAddress->Display = true;
			$this->lblTelephone->Display = true;
			$this->lblFax->Display = true;
	
			// Display Edit and Delete buttons
			$this->btnEdit->Display = true;
			$this->btnDelete->Display = true;
		}
		
		// Display the inputs for Company Edit mode
		protected function DisplayInputs() {
			
			// Do not display labels
			$this->lblShortDescription->Display = false;
			$this->lblWebsite->Display = false;
			$this->lblEmail->Display = false;
			$this->pnlLongDescription->Display = false;
			$this->lblAddress->Display = false;
			$this->lblTelephone->Display = false;
			$this->lblFax->Display = false;
			
			// Do not display the Edit or Delete button
			$this->btnEdit->Display = false;
			$this->btnDelete->Display = false;
			
			// Display the inputs for edit mode
			$this->txtShortDescription->Display = true;
			$this->txtWebsite->Display = true;
			$this->txtEmail->Display = true;
			$this->txtLongDescription->Display = true;
			$this->lstAddress->Display = true;
			$this->txtTelephone->Display = true;
			$this->txtFax->Display = true;
			
			// Display the Save and Cancel buttons
			$this->btnSave->Display = true;
			$this->btnCancel->Display = true;
		}
		
	}
	CompanyEditForm::Run('CompanyEditForm', __DOCROOT__ . __SUBDIRECTORY__ . '/contacts/company_edit.tpl.php');
?>