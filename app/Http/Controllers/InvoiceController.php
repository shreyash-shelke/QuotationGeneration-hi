<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;  // Assuming you have a settings model for the invoice number
use PDF;  // We will use this for generating PDF invoices (requires the `dompdf` library)

class InvoiceController extends Controller
{
    public function generateInvoice(Request $request)
    {
        // Retrieve data from the request
        $selectedData = json_decode($request->input('selectedData'), true);
        $gstIncluded = filter_var($request->input('gstIncluded'), FILTER_VALIDATE_BOOLEAN); // Check if GST is included

        // Calculate totals
        $totalAmount = array_reduce($selectedData, function ($carry, $item) {
            return $carry + $item[2]; // Sum the 'Amount' column
        }, 0);

        $gstAmount = $gstIncluded ? $totalAmount * 0.18 : 0; // Calculate GST at 18% if applicable
        $totalWithGST = $totalAmount + $gstAmount;

        // Get the current year and month in "YYMM" format
        $currentYearMonth = date('ym');

        // Retrieve the last invoice number from the 'settings' table or generate a new one
        $lastInvoice = Setting::where('key', 'last_invoice_number')->first();
        $lastInvoiceValue = $lastInvoice ? $lastInvoice->value : 0;

        // Extract the last invoice's year and month part to check if it matches the current year-month
        $lastYearMonth = substr($lastInvoiceValue, 0, 4);
        $lastCounter = $lastInvoiceValue ? substr($lastInvoiceValue, 4) : 0;

        // Reset the counter if the year-month has changed
        $newCounter = ($lastYearMonth === $currentYearMonth) ? $lastCounter + 1 : 1;

        // Format the invoice number as AIN-YYYYMM-XXX
        $formattedCounter = str_pad($newCounter, 3, '0', STR_PAD_LEFT);
        $invoiceNumberFormatted = "AIN-{$currentYearMonth}-{$formattedCounter}";

        // Save the new invoice number as "YYMMXXX" in the settings
        $newInvoiceValue = $currentYearMonth . $formattedCounter;
        if ($lastInvoice) {
            $lastInvoice->update(['value' => $newInvoiceValue]);
        } else {
            Setting::create(['key' => 'last_invoice_number', 'value' => $newInvoiceValue]);
        }

        // Return the invoice view with the necessary data
        return view('invoice', compact('selectedData', 'totalAmount', 'gstAmount', 'totalWithGST', 'invoiceNumberFormatted', 'gstIncluded'));
    }
}
