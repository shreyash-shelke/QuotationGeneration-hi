<?php 
 namespace App\Http\Controllers;

 use App\Models\ExcelData;
 use App\Models\Quotation;  // Add this line
 use Illuminate\Http\Request;
 use Maatwebsite\Excel\Facades\Excel;
 use App\Imports\ExcelImport;
 use Illuminate\Support\Facades\DB;
class ExcelController extends Controller
{
    public function showUploadForm()
    {
        return view('upload');
    }

    public function uploadFile(Request $request)
{
    // Validate the file
    $request->validate([
        'file' => 'required|mimes:xlsx,csv|max:2048',
    ]);

    // Store the uploaded file
    $filePath = $request->file('file')->store('uploads');

    // Parse the file using Maatwebsite Excel
    $data = Excel::toArray(new ExcelImport, $filePath);

    // Loop through the data (skip the first row if it's a header)
    foreach ($data[0] as $index => $row) {
        if ($index == 0 || strtolower($row[0]) == 'description' || strtolower($row[1]) == 'qty' || strtolower($row[2]) == 'amount') {
            continue;
        }

        // Insert the data into the database
        if (isset($row[0]) && is_numeric($row[1]) && is_numeric($row[2])) {
            ExcelData::create([
                'description' => $row[0],
                'qty' => (int) $row[1],
                'amount' => (float) $row[2],
                'phone' => $row[3] ?? null, // Assuming phone number is in the 4th column
                'quotation_number' => $this->generateQuotationNumber(), // Use the generateQuotationNumber method for each row
            ]);
        }
    }

    // Redirect to the page that displays the data
    return view('display', ['data' => $data[0]]);
}

public function processData(Request $request)
{
    // Retrieve the selected rows
    $selectedRows = $request->input('selected_rows');
    $selectedData = [];

    if ($selectedRows) {
        foreach ($selectedRows as $row) {
            $selectedData[] = json_decode($row, true); // Decode JSON data back to an array
        }
    }

    // Check if GST is selected
    $includeGST = $request->has('include_gst'); // This will be true if the checkbox is checked

    // Generate a new Quotation Number
    $quotationNumber = $this->generateQuotationNumber();

    // Pass the selected data, GST flag, and Quotation Number to the quotation view
    return view('quotation', [
        'selectedData' => $selectedData,
        'includeGST' => $includeGST,
        'quotationNumber' => $quotationNumber,
    ]);
}

public function viewQuotation($id)
{
    // Retrieve the quotation by ID
    $quotation = ExcelData::findOrFail($id);  // This will fetch the record or throw a 404 error if not found

    // Return a view to show the quotation details
    return view('dashboard.quotation_details', compact('quotation'));
}

private function generateQuotationNumber()
{
    // Fetch the last generated quotation number from the 'settings' table
    $lastQuotation = DB::table('settings')->where('key', 'last_quotation_number')->first();

    // Get the current year and month in YYMM format (e.g., 2501 for January 2025)
    $currentYearMonth = date('ym'); 

    // Extract the last sequential number and check if it matches the current month-year format
    $lastNumber = $lastQuotation ? $lastQuotation->value : 'AQI-' . $currentYearMonth . '-000';

    // Check if the last quotation number's year-month matches the current month-year
    $lastYearMonth = substr($lastNumber, 4, 4);  // Extracting the year-month part from the last number
    $lastSeqNum = (int)substr($lastNumber, 9);  // Extracting the sequential number

    // If the year-month doesn't match, reset the sequential number to 001
    if ($lastYearMonth !== $currentYearMonth) {
        $newSeqNum = 1;
    } else {
        // Increment the sequential number
        $newSeqNum = $lastSeqNum + 1;
    }

    // Format the sequential number to always have 3 digits (e.g., 001, 002, etc.)
    $newSeqNumFormatted = str_pad($newSeqNum, 3, '0', STR_PAD_LEFT);

    // Generate the new quotation number
    $newNumber = 'AQN-' . $currentYearMonth . '-' . $newSeqNumFormatted;

    // Update the last quotation number in the database
    DB::table('settings')->updateOrInsert(
        ['key' => 'last_quotation_number'],
        ['value' => $newNumber]
    );

    return $newNumber;
}

public function listQuotations(Request $request)
{
    $query = ExcelData::query();

    // Search functionality
    if ($request->has('search')) {
        $search = $request->input('search');
        $query->where('phone', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%");
    }

    // Paginate the results
    $quotations = $query->orderBy('created_at', 'desc')->paginate(10);

    return view('dashboard.quotations_list', compact('quotations'));
}

public function showQuotationDetails($id)
{
    $quotation = Quotation::find($id);  // Fetch the quotation
    if (!$quotation) {
        return redirect()->route('quotations.list')->with('error', 'Quotation not found!');
    }

    return view('dashboard.quotation_details', compact('quotation'));
}
public function store(Request $request)
{
    // Create a new quotation
    $quotation = Quotation::create([
        'quotation_number' => 'QN-2025-001',
        'customer_name' => $request->customer_name,
        'total_amount' => $request->total_amount,
        'gst_amount' => $request->gst_amount,
        'total_with_gst' => $request->total_with_gst,
        'due_date' => $request->due_date,
    ]);

    // Create quotation items
    foreach ($request->items as $itemData) {
        $quotation->items()->create($itemData);  // Create associated items
    }

    return redirect()->route('quotations.list');
}


}
