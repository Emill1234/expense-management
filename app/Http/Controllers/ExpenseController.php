<?php 

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::all();
        $categories = Category::all();

        return view('expenses.index', compact('expenses', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
            'amount' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
        ]);

        Expense::create($request->all());

        return redirect()->route('expenses.index')->with('success', 'Expense created successfully');
    }

    public function filter(Request $request)
    {
        $categoryId = $request->input('filterCategory');
        $filterAmountFrom = $request->input('filterAmountFrom');
        $filterAmountTo = $request->input('filterAmountTo');

        $expenses = $this->getFilteredExpenses($categoryId, $filterAmountFrom, $filterAmountTo);

        $categories = Category::all();

        return view('expenses.index', compact('expenses', 'categories'));
    }

    public function exportTable(Request $request)
    {
        $format = $request->input('exportFormat');

        $categoryId = $request->input('filterCategory');
        $filterAmountFrom = $request->input('filterAmountFrom');
        $filterAmountTo = $request->input('filterAmountTo');

        $filteredExpenses = $this->getFilteredExpenses($categoryId, $filterAmountFrom, $filterAmountTo);

        if ($format === 'csv') {
            return $this->exportToCsv($filteredExpenses);
        } elseif ($format === 'xml') {
            return $this->exportToXml($filteredExpenses);
        } elseif ($format === 'docx') {
            return $this->exportToDocx($filteredExpenses);
        } elseif ($format == 'xlsx') {
            return $this->exportToXlsx($filteredExpenses);
        } else {
            return redirect()->route('expenses.index')->with('error', 'Unsupported export format.');
        }
    }

    protected function getFilteredExpenses($categoryId, $filterAmountFrom, $filterAmountTo)
    {        
        $query = Expense::query();

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($filterAmountFrom !== null) {
            $query->where('amount', '>=', $filterAmountFrom);
        }

        if ($filterAmountTo !== null) {
            $query->where('amount', '<=', $filterAmountTo);
        }

        $filteredExpenses = $query->get();

        return $filteredExpenses;
    }

    protected function exportToCsv($expenses)
    {
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=expenses.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $handle = fopen('php://output', 'w');

        fputcsv($handle, array('Description', 'Category', 'Amount'));

        foreach ($expenses as $expense) {
            fputcsv($handle, array($expense->description, $expense->category->name, $expense->amount));
        }

        return Response::stream(
            function () use ($handle) {
                fclose($handle);
            },
            200,
            $headers
        );
    }

    protected function exportToXml($expenses)
    {
        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->startDocument('1.0', 'UTF-8');

        $xml->startElement('expenses');

        foreach ($expenses as $expense) {
            $xml->startElement('expense');
            $xml->writeElement('description', $expense->description);
            $xml->writeElement('category', $expense->category->name);
            $xml->writeElement('amount', $expense->amount);
            $xml->endElement();
        }

        $xml->endElement();
        $xml->endDocument();

        $headers = array(
            "Content-type" => "application/xml",
            "Content-Disposition" => "attachment; filename=expenses.xml",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        return Response::stream(
            function () use ($xml) {
                echo $xml->outputMemory();
            },
            200,
            $headers
        );
    }

    protected function exportToDocx($expenses)
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        $section = $phpWord->addSection();

        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80]);

        $headerRow = $table->addRow();
        $headerRow->addCell(3000)->addText('Description');
        $headerRow->addCell(3000)->addText('Category');
        $headerRow->addCell(3000)->addText('Amount');

        foreach ($expenses as $expense) {
            $dataRow = $table->addRow();
            $dataRow->addCell(3000)->addText($expense->description);
            $dataRow->addCell(3000)->addText($expense->category->name);
            $dataRow->addCell(3000)->addText('$' . number_format($expense->amount, 2));
        }

        $filename = 'expenses.docx';
        $path = storage_path($filename);
        $phpWord->save($path);

        $headers = [
            'Content-Type' => 'application/msword',
            'Content-Disposition' => 'attachment;filename=' . $filename,
            'Cache-Control' => 'max-age=0',
        ];

        return Response::download($path, $filename, $headers);
    }

    public function exportToXlsx($expenses)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Description', 'Category', 'Amount'];
        $sheet->fromArray([$headers], null, 'A1');

        $row = 2;
        foreach ($expenses as $expense) {
            $sheet->setCellValue('A' . $row, $expense->description);
            $sheet->setCellValue('B' . $row, $expense->category->name);
            $sheet->setCellValue('C' . $row, $expense->amount);
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);

        $tempFile = tempnam(sys_get_temp_dir(), 'export_');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        $response = Response::download($tempFile, 'expenses.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);

        register_shutdown_function(function () use ($tempFile) {
            @unlink($tempFile);
        });

        return $response;
    }
}