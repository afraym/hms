<?php

namespace App\Exports;

use App\Models\Patient;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PatientsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithProperties, ShouldAutoSize, WithEvents
{
    public function collection()
    {
        return Patient::with(['department', 'creator', 'visits'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'الرقم الطبي',
            'الاسم',
            'الرقم القومي',
            'رقم التأمين الصحي',
            'تاريخ الميلاد',
            'السن',
            'النوع',
            'رقم الهاتف',
            'العنوان',
            'المحافظة',
            'القسم',
            'الحالة',
            'اسم المرافق',
            'هاتف المرافق',
            'صلة القرابة',
            'تاريخ التسجيل',
            'عدد الزيارات',
            'آخر زيارة',
            'بواسطة'
        ];
    }

    public function map($patient): array
    {
        $lastVisit = $patient->visits->sortByDesc('visit_at')->first();
        
        return [
            floatval($patient->medical_id), // Convert to number
            $patient->full_name,
            floatval($patient->national_id), // Convert to number
            floatval(str_replace('UHI', '', $patient->uhi_number)), // Convert to number without UHI prefix
            $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->format('Y-m-d') : '',
            $this->calculateAge($patient->date_of_birth),
            $patient->gender == 'male' ? 'ذكر' : 'أنثى',
            $patient->phone,
            $patient->address,
            $patient->governorate,
            optional($patient->department)->name,
            $this->translateStatus($patient->status),
            $patient->companion_name,
            $patient->companion_phone,
            $patient->companion_relation,
            // Ensure created_at is a Carbon instance
            \Carbon\Carbon::parse($patient->created_at)->format('Y-m-d H:i'),
            $patient->visits->count(),
            // Handle last visit date safely
            $lastVisit ? \Carbon\Carbon::parse($lastVisit->visit_at)->format('Y-m-d H:i') : '-',
            optional($patient->creator)->name
        ];
    }

    private function calculateAge($birthDate)
    {
        if (!$birthDate) return '-';
        
        $birth = \Carbon\Carbon::parse($birthDate);
        $now = \Carbon\Carbon::now();
        
        $years = $birth->diffInYears($now);
        if ($years >= 1) {
            return round($years) . ' سنة';
        } else {
            return round($birth->diffInDays($now) / 30.44) . ' شهر';
        }
    }

    private function translateStatus($status)
    {
        $translations = [
            'waiting' => 'في الانتظار',
            'admitted' => 'تم الدخول',
            'discharged' => 'تم الخروج'
        ];

        return $translations[$status] ?? $status;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A1:S1' => ['fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'CCCCCC']]],
            // Format ID columns as numbers with 0 decimals
            'A2:A1000' => ['numberFormat' => ['formatCode' => '0']], // Medical ID
            'C2:C1000' => ['numberFormat' => ['formatCode' => '0']], // National ID
            'D2:D1000' => ['numberFormat' => ['formatCode' => '0']], // UHI number
        ];
    }

    public function properties(): array
    {
        return [
            'creator'        => auth()->user()->name,
            'title'          => 'تقرير المرضى',
            'description'    => 'قائمة بيانات المرضى',
            'subject'        => 'المرضى',
            'keywords'       => 'مرضى,تقرير,إحصائيات',
            'category'       => 'تقارير المرضى',
            'company'        => config('app.name'),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
            },
        ];
    }
}