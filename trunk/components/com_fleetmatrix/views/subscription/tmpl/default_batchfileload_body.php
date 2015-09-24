<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="warning-message">
    <img class="warning-icon" src="images/Warning-icon.png" >
    Friendly reminder - any typo in input file will cause the operation to fail. Only accepts .txt and .csv files.
</div>
<br/>

<table class="batchFileLoad-table">
    <tr >
        <th>
            <div id="batchFileLoad-driverCreation">
                <h3>Driver Creation (required)</h3>  <br/>
            </div>
        </th>
    </tr>
    <tr class="batchFileLoad-tr">
        <td>
            <h4>Format: Company, Group, Driver, Driver license. Both company and driver name are required.</h4>
        </td>
    </tr>
    <tr class="batchFileLoad-tr">
        <td>
            <h4>Example: ABCCompany, BCDGroup, Sam Smith, D123465</h4>
        </td>
    </tr>
    <tr class="batchFileLoad-tr">
        <td>
            <input id='driver-create-input' type="file" class="batchFileLoad-selection-button" accept=".txt,.csv" required>
        </td>
    </tr>
    <tr class="batchFileLoad-tr batchFileLoad-tr-error batchFileLoad-tr-driver-error batchFileLoad-error" id="batchFileLoad-tr-driver-create-error-0" >
        <td id="batchFileLoad-td-driver-create-error-0" >
        </td>
    </tr>
</table>

<br/>

<table class="batchFileLoad-table">
    <tr>
        <th>
            <div id="batchFileLoad-subscriptionCreation">
                <h3>Subscription Creation (required)</h3> <br/>
            </div>
        </th>
    </tr>
    <tr class="batchFileLoad-tr">
        <td>
            <h4>Format: Reseller ID number, Company, Group, Driver, Weight, Friendly Name, VIN, Visible.</h4>
        </td>
    </tr>
    <tr class="batchFileLoad-tr">
        <td>
            <h4>Example: 102, ABCCompany, BCDGroup, Sam Smith, 5000, Sam's vehicle, 1GNDV23L96D239768, yes</h4>
        </td>
    </tr>
    <tr class="batchFileLoad-tr">
        <td>
            <input id='subscription-create-input' type="file" class="batchFileLoad-selection" accept=".txt,.csv" required>
        </td>
    </tr>
    <tr class="batchFileLoad-tr batchFileLoad-tr-error batchFileLoad-tr-sub-error batchFileLoad-error" id="batchFileLoad-tr-sub-create-error-0">
        <td id="batchFileLoad-td-sub-create-error-0">
        </td>
    </tr>
</table>
<br/><br/>

<div class ="batchFileLoad-error" id="fixErrorMessage">
    Invalid input. Please fix the above errors and try again.
</div>

<button id='batch-file-load-submit-button' type="submit" class="button"><?php echo JText::_('Submit'); ?></button>
