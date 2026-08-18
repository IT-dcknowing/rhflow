<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;  
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class AttendanceExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $employees;
    protected $days;
    protected $periodType;
    protected $request;

    public function __construct($employees, $days, $periodType, $request)
    {
        $this->employees = $employees;
        $this->days = $days;
        $this->periodType = $periodType;
        $this->request = $request;
    }

    public function collection()
    {
        $data = collect();

        foreach ($this->employees as $employee) {
            $row = collect([$employee->name]);
            $totalHours = 0;
            $totalMinutes = 0;
            $daysPresent = 0;
            $daysAbsent = 0;

            foreach ($this->days as $day) {
                $pointages = $employee->pointeuses
                    ->where('auth_date', $day->format('Y-m-d'))
                    ->sortBy('auth_time');

                if ($pointages->isNotEmpty()) {
                    $entree = $pointages->firstWhere('type', 'entree');
                    $sortie = $pointages->firstWhere('type', 'sortie');

                    $entreeTime = $entree ? $entree->auth_time : '--:--';
                    $sortieTime = $sortie ? $sortie->auth_time : '--:--';

                    // Calcul du temps de travail
                    if ($entree && $sortie) {
                        $start = Carbon::parse("{$entree->auth_date} {$entree->auth_time}");
                        $end = Carbon::parse("{$sortie->auth_date} {$sortie->auth_time}");
                        $diff = $start->diff($end);

                        $totalHours += $diff->h;
                        $totalMinutes += $diff->i;
                        $daysPresent++;

                        $workingTime = sprintf('%02d:%02d', $diff->h, $diff->i);
                    } else {
                        $workingTime = '--:--';
                    }

                    $row->push("Arrivée: {$entreeTime} | Départ: {$sortieTime} | Temps: {$workingTime}");
                } else {
                    $row->push('Absent');
                    $daysAbsent++;
                }
            }

            // Conversion des minutes en heures
            $totalHours += intval($totalMinutes / 60);
            $totalMinutes = $totalMinutes % 60;

            // Ajouter les statistiques
            $row->push(sprintf('%02d:%02d', $totalHours, $totalMinutes)); // Temps total
            $row->push($daysPresent); // Jours présents
            $row->push($daysAbsent); // Jours absents
            $row->push(number_format($daysPresent > 0 ? ($daysPresent / ($daysPresent + $daysAbsent)) * 100 : 0, 1) . '%'); // Taux de présence

            $data->push($row->toArray());
        }

        return $data;
    }

    public function headings(): array
    {
        $headings = ['Employé'];

        // Ajouter les en-têtes des jours
        foreach ($this->days as $day) {
            $headings[] = $day->locale('fr')->isoFormat('ddd D MMM');
        }

        // Ajouter les en-têtes des statistiques
        $headings[] = 'Temps Total';
        $headings[] = 'Jours Présents';
        $headings[] = 'Jours Absents';
        $headings[] = 'Taux de Présence';

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();

        // Style pour l'en-tête
        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '007BFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Bordures pour tout le tableau
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Style pour la colonne des employés
        $sheet->getStyle('A2:A' . $highestRow)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'F8F9FA'],
            ],
        ]);

        // Style pour les colonnes de statistiques (les 4 dernières colonnes)
        $statsStartColumn = $this->getColumnLetter(count($this->days) + 2);
        $sheet->getStyle($statsStartColumn . '1:' . $highestColumn . $highestRow)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '1e3a8a'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Colorer les weekends
        foreach ($this->days as $index => $day) {
            if ($day->isWeekend()) {
                $columnLetter = $this->getColumnLetter($index + 2);
                $sheet->getStyle($columnLetter . '1:' . $columnLetter . $highestRow)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'ff0202'],
                    ],
                ]);
            }
        }

        return $sheet;
    }

    public function columnWidths(): array
    {
        $widths = ['A' => 25]; // Colonne employé

        // Largeur pour les colonnes de jours
        foreach ($this->days as $index => $day) {
            $columnLetter = $this->getColumnLetter($index + 2);
            $widths[$columnLetter] = 35;
        }

        // Largeur pour les colonnes de statistiques
        $statsColumns = ['Temps Total', 'Jours Présents', 'Jours Absents', 'Taux de Présence'];
        $startIndex = count($this->days) + 2;

        foreach ($statsColumns as $index => $column) {
            $columnLetter = $this->getColumnLetter($startIndex + $index);
            $widths[$columnLetter] = 15;
        }

        return $widths;
    }

    public function title(): string
    {
        $title = 'Pointages';

        switch ($this->periodType) {
            case 'week':
                $week = $this->request->get('week', date('W'));
                $year = $this->request->get('year', date('Y'));
                $title .= " - Semaine {$week} ({$year})";
                break;

            case 'month':
                $month = $this->request->get('month', date('n'));
                $year = $this->request->get('year', date('Y'));
                $monthName = Carbon::create($year, $month, 1)->locale('fr')->isoFormat('MMMM');
                $title .= " - {$monthName} {$year}";
                break;

            case 'day':
                $date = Carbon::parse($this->request->get('specific_date', date('Y-m-d')));
                $title .= " - " . $date->locale('fr')->isoFormat('D MMMM YYYY');
                break;

            case 'custom':
                $startDate = Carbon::parse($this->request->get('start_date', date('Y-m-d')));
                $endDate = Carbon::parse($this->request->get('end_date', date('Y-m-d')));
                $title .= " - Du " . $startDate->locale('fr')->isoFormat('D MMM YYYY') .
                         " au " . $endDate->locale('fr')->isoFormat('D MMM YYYY');
                break;
        }

        // Ajouter le filtre employé si applicable
        if ($this->request->get('employee_id')) {
            $employee = $this->employees->first();
            if ($employee) {
                $title .= " - " . $employee->name;
            }
        }

        return $title;
    }

    private function getColumnLetter($columnIndex)
    {
        $letter = '';
        while ($columnIndex > 0) {
            $columnIndex--;
            $letter = chr($columnIndex % 26 + 65) . $letter;
            $columnIndex = intval($columnIndex / 26);
        }
        return $letter;
    }
}
