<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\Lead;
use App\Models\LeadReport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\LeadsExport;
use Illuminate\Support\Facades\DB;

class LeadsReportController extends Controller
{
    // Get list of available fields from leads table
    public function getFields()
    {
        $excludedFields = ['id', 'created_at', 'updated_at'];

        $columns = DB::select('SHOW COLUMNS FROM leads');

        $fields = collect($columns)
            ->filter(fn($col) => !in_array($col->Field, $excludedFields))
            ->map(function ($col) {
                return [
                    'name' => $col->Field,
                    'type' => str_contains($col->Type, 'date') ? 'date' : 'text'
                ];
            })
            ->values(); // reset index

        return response()->json($fields);
    }

    public function getCriterias($field)
    {
        $excludedFields = ['id', 'created_at', 'updated_at'];

        // Validasi jika field yang diminta tidak diperbolehkan
        if (in_array($field, $excludedFields)) {
            return response()->json([], 400);
        }

        $values = DB::table('leads')
            ->select($field)
            ->distinct()
            ->whereNotNull($field)
            ->pluck($field)
            ->values();

        return response()->json($values);
    }


    // Store new report
    public function storeReport(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'criteria' => 'required|array',
            'fields' => 'required|array|min:1',
        ]);

        $report = new LeadReport();
        $report->name = $request->name;
        $report->filter_criteria = json_encode($request->criteria);
        $report->selected_fields = json_encode($request->fields);
        $report->save();

        return response()->json(['message' => 'Report saved successfully']);
    }

    // Show data based on selected report
    public function showReport($id)
    {
        $report = LeadReport::findOrFail($id);
        $criteria = json_decode($report->filter_criteria, true);
        $fields = json_decode($report->selected_fields, true);

        $query = Lead::select($fields);
        foreach ($criteria as $field => $value) {
            if (is_array($value) && count($value) === 2 && strtotime($value[0]) && strtotime($value[1])) {
                $query->whereBetween($field, $value);
            } elseif (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        $data = $query->get();
        return response()->json([
            'report_name' => $report->name,
            'fields' => $fields,
            'data' => $data
        ]);
    }

    // Export to Excel
    public function exportToExcel($id)
    {
        $report = LeadReport::findOrFail($id);
        $criteria = json_decode($report->filter_criteria, true);
        $fields = json_decode($report->selected_fields, true);

        $query = Lead::select($fields);
        foreach ($criteria as $field => $value) {
            if (is_array($value) && count($value) === 2 && strtotime($value[0]) && strtotime($value[1])) {
                $query->whereBetween($field, $value);
            } elseif (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        $data = $query->get();

        return Excel::download(new LeadsExport($data, $fields), $report->name . '.xlsx');
    }

    // Export to PDF
    public function exportToPdf($id)
    {
        $report = LeadReport::findOrFail($id);
        $criteria = json_decode($report->filter_criteria, true);
        $fields = json_decode($report->selected_fields, true);

        $query = Lead::select($fields);
        foreach ($criteria as $field => $value) {
            if (is_array($value) && count($value) === 2 && strtotime($value[0]) && strtotime($value[1])) {
                $query->whereBetween($field, $value);
            } elseif (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        $data = $query->get();

        $pdf = Pdf::loadView('pdf.leads', [
            'data' => $data,
            'fields' => $fields,
            'report_name' => $report->name
        ]);

        return $pdf->download($report->name . '.pdf');
    }

    // Delete report
    public function deleteReport($id)
    {
        LeadReport::destroy($id);
        return response()->json(['message' => 'Report deleted']);
    }
}
