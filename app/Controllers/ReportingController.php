<?php

namespace App\Controllers;

use App\Services\ReportingService;

class ReportingController extends BaseController
{
    public function index(?string $type = null)
    {
        if ($redirect = $this->authorize($this->reportingRoles())) {
            return $redirect;
        }

        $service = new ReportingService();
        $resolvedType = $service->normalizeType($type ?? $this->request->getGet('type'));
        $filters = $service->collectFilters($this->request->getGet());
        $report = $service->buildReport($resolvedType, $filters);

        return view('reporting/index', [
            'reportDefinitions' => $service->getReportDefinitions(),
            'report' => $report,
        ]);
    }

    public function export(string $type, string $format)
    {
        if ($redirect = $this->authorize($this->reportingRoles())) {
            return $redirect;
        }

        $service = new ReportingService();
        $resolvedType = $service->normalizeType($type);
        $filters = $service->collectFilters($this->request->getGet());
        $report = $service->buildReport($resolvedType, $filters);
        $safeTitle = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $report['title']) ?: 'report');

        return match (strtolower($format)) {
            'csv' => $this->exportCsv($report, $safeTitle),
            'excel', 'xls' => $this->exportExcel($report, $safeTitle),
            'pdf' => $this->exportPdfView($report),
            default => redirect()->back()->with('error', 'Unsupported export format.'),
        };
    }

    private function exportCsv(array $report, string $safeTitle)
    {
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, array_map(static fn(array $column): string => $column['label'], $report['columns']));

        foreach ($report['rows'] as $row) {
            fputcsv($handle, array_map(static fn(array $column): string => (string) ($row[$column['key']] ?? ''), $report['columns']));
        }

        rewind($handle);
        $content = stream_get_contents($handle) ?: '';
        fclose($handle);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $safeTitle . '.csv"')
            ->setBody("\xEF\xBB\xBF" . $content);
    }

    private function exportExcel(array $report, string $safeTitle)
    {
        $html = view('reporting/export_table', ['report' => $report]);

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $safeTitle . '.xls"')
            ->setBody($html);
    }

    private function exportPdfView(array $report)
    {
        return view('reporting/export_pdf', ['report' => $report]);
    }
}
