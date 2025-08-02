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
use Carbon\Carbon;

class PatientsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithProperties, ShouldAutoSize, WithEvents
{
    protected $dateFilter;
    protected $startDate;
    protected $endDate;
    protected $departmentFilter; // Add department filter

    public function __construct($dateFilter = null, $startDate = null, $endDate = null, $departmentFilter = null)
    {
        $this->dateFilter = $dateFilter;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->departmentFilter = $departmentFilter;
    }

    public function collection()
    {
        // Base query - get all patients with their visits and departments
        $query = Patient::with(['department', 'creator', 'visits' => function($visitQuery) {
                $visitQuery->with('department')->orderBy('visit_at', 'desc');
            }])
            ->orderBy('created_at', 'desc');

        // Apply department filter if specified
        if ($this->departmentFilter && $this->departmentFilter !== 'all') {
            $query->where(function($q) {
                $q->where('department_id', $this->departmentFilter)
                  ->orWhereHas('visits', function($visitQuery) {
                      $visitQuery->where('department_id', $this->departmentFilter);
                  });
            });
        }

        // Apply date filters based on BOTH registration AND visits consistently
        if ($this->dateFilter) {
            switch ($this->dateFilter) {
                case 'today':
                    $query->where(function($q) {
                        $q->whereDate('created_at', Carbon::today())
                          ->orWhereHas('visits', function($visitQuery) {
                              $visitQuery->whereDate('visit_at', Carbon::today());
                          });
                    });
                    break;
                    
                case 'yesterday':
                    $query->where(function($q) {
                        $q->whereDate('created_at', Carbon::yesterday())
                          ->orWhereHas('visits', function($visitQuery) {
                              $visitQuery->whereDate('visit_at', Carbon::yesterday());
                          });
                    });
                    break;
                    
                case 'this_week':
                    $query->where(function($q) {
                        $q->whereBetween('created_at', [
                            Carbon::now()->startOfWeek(),
                            Carbon::now()->endOfWeek()
                        ])->orWhereHas('visits', function($visitQuery) {
                            $visitQuery->whereBetween('visit_at', [
                                Carbon::now()->startOfWeek(),
                                Carbon::now()->endOfWeek()
                            ]);
                        });
                    });
                    break;
                    
                case 'last_week':
                    $query->where(function($q) {
                        $q->whereBetween('created_at', [
                            Carbon::now()->subWeek()->startOfWeek(),
                            Carbon::now()->subWeek()->endOfWeek()
                        ])->orWhereHas('visits', function($visitQuery) {
                            $visitQuery->whereBetween('visit_at', [
                                Carbon::now()->subWeek()->startOfWeek(),
                                Carbon::now()->subWeek()->endOfWeek()
                            ]);
                        });
                    });
                    break;
                    
                case 'this_month':
                    $query->where(function($q) {
                        $q->whereMonth('created_at', Carbon::now()->month)
                          ->whereYear('created_at', Carbon::now()->year)
                          ->orWhereHas('visits', function($visitQuery) {
                              $visitQuery->whereMonth('visit_at', Carbon::now()->month)
                                         ->whereYear('visit_at', Carbon::now()->year);
                          });
                    });
                    break;
                    
                case 'last_month':
                    $lastMonth = Carbon::now()->subMonth();
                    $query->where(function($q) use ($lastMonth) {
                        $q->whereMonth('created_at', $lastMonth->month)
                          ->whereYear('created_at', $lastMonth->year)
                          ->orWhereHas('visits', function($visitQuery) use ($lastMonth) {
                              $visitQuery->whereMonth('visit_at', $lastMonth->month)
                                         ->whereYear('visit_at', $lastMonth->year);
                          });
                    });
                    break;
                    
                case 'this_year':
                    $query->where(function($q) {
                        $q->whereYear('created_at', Carbon::now()->year)
                          ->orWhereHas('visits', function($visitQuery) {
                              $visitQuery->whereYear('visit_at', Carbon::now()->year);
                          });
                    });
                    break;
                    
                case 'last_year':
                    $lastYear = Carbon::now()->subYear()->year;
                    $query->where(function($q) use ($lastYear) {
                        $q->whereYear('created_at', $lastYear)
                          ->orWhereHas('visits', function($visitQuery) use ($lastYear) {
                              $visitQuery->whereYear('visit_at', $lastYear);
                          });
                    });
                    break;
                    
                case 'custom':
                    if ($this->startDate && $this->endDate) {
                        $startDate = Carbon::parse($this->startDate)->startOfDay();
                        $endDate = Carbon::parse($this->endDate)->endOfDay();
                        
                        $query->where(function($q) use ($startDate, $endDate) {
                            $q->whereBetween('created_at', [$startDate, $endDate])
                              ->orWhereHas('visits', function($visitQuery) use ($startDate, $endDate) {
                                  $visitQuery->whereBetween('visit_at', [$startDate, $endDate]);
                              });
                        });
                    }
                    break;
                    
                case 'all':
                    // No filter - include all patients
                    break;
            }
        }

        return $query->get();
    }

    public function map($patient): array
    {
        $lastVisit = $patient->visits->sortByDesc('visit_at')->first();
        
        // Calculate visits for the current filter period
        $periodVisits = $this->countVisitsInPeriod($patient);
        
        // Get current/primary department
        $currentDepartment = optional($patient->department)->name;
        
        return [
            floatval($patient->medical_id),
            $patient->full_name,
            floatval($patient->national_id),
            floatval(str_replace('UHI', '', $patient->uhi_number)),
            $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->format('Y-m-d') : '',
            $this->calculateAge($patient->date_of_birth),
            $patient->gender == 'male' ? 'ذكر' : 'أنثى',
            $patient->phone,
            $patient->address,
            $patient->governorate,
            $currentDepartment ?: 'غير محدد', // Current department
            $this->translateStatus($patient->status),
            $patient->companion_name,
            $patient->companion_phone,
            $patient->companion_relation,
            \Carbon\Carbon::parse($patient->created_at)->format('Y-m-d H:i'),
            $patient->visits->count(),
            $lastVisit ? \Carbon\Carbon::parse($lastVisit->visit_at)->format('Y-m-d H:i') : '-',
            $periodVisits, // Visits in the filtered period
            optional($patient->creator)->name
        ];
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
            'القسم الحالي', // Current department
            'الحالة',
            'اسم المرافق',
            'هاتف المرافق',
            'صلة القرابة',
            'تاريخ التسجيل',
            'إجمالي الزيارات',
            'آخر زيارة',
            'زيارات الفترة', // Visits in the selected period
            'بواسطة'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ],
            
            // Format ID columns as numbers with 0 decimals
            'A2:A1000' => [
                'numberFormat' => ['formatCode' => '0'] // Medical ID
            ],
            'C2:C1000' => [
                'numberFormat' => ['formatCode' => '0'] // National ID
            ],
            'D2:D1000' => [
                'numberFormat' => ['formatCode' => '0'] // UHI number
            ],
            
            // Highlight department column (K)
            'K1' => [
                'fill' => [
                    'fillType' => 'solid',
                    'color' => ['rgb' => 'E8F5E8'] // Light green for department header
                ]
            ],
            
            // Highlight patients with visits in the selected period (column S)
            'S2:S1000' => [
                'conditional' => [
                    [
                        'type' => 'greaterThan',
                        'value' => 0,
                        'fill' => [
                            'fillType' => 'solid',
                            'color' => ['rgb' => 'E8F5E8'] // Light green
                        ]
                    ]
                ]
            ],
            
            // Highlight patients with no total visits (column Q)
            'Q2:Q1000' => [
                'conditional' => [
                    [
                        'type' => 'equal',
                        'value' => 0,
                        'fill' => [
                            'fillType' => 'solid',
                            'color' => ['rgb' => 'FFE6E6'] // Light red
                        ]
                    ]
                ]
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Set right-to-left for Arabic text
                $event->sheet->getDelegate()->setRightToLeft(true);
                
                // Auto-fit columns for better readability
                foreach(range('A','T') as $col) {
                    $event->sheet->getDelegate()->getColumnDimension($col)->setAutoSize(true);
                }
                
                // Make department column wider
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(20); // Current department
                
                // Add borders to all cells with data
                $lastRow = $event->sheet->getDelegate()->getHighestRow();
                $event->sheet->getDelegate()->getStyle('A1:T' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);
                
                // Add explanatory notes at the bottom
                $noteRow = $lastRow + 2;
                $event->sheet->getDelegate()->setCellValue('A' . $noteRow, 
                    'ملاحظات:');
                $event->sheet->getDelegate()->setCellValue('A' . ($noteRow + 1), 
                    '• القسم الحالي: القسم المسجل عليه المريض حالياً');
                $event->sheet->getDelegate()->setCellValue('A' . ($noteRow + 2), 
                    '• المرضى الذين لديهم 0 زيارات إجمالية مميزون بخلفية حمراء فاتحة');
                $event->sheet->getDelegate()->setCellValue('A' . ($noteRow + 3), 
                    '• المرضى الذين لديهم زيارات في الفترة المحددة مميزون بخلفية خضراء فاتحة');
                
                // Style the notes
                $event->sheet->getDelegate()->getStyle('A' . $noteRow . ':A' . ($noteRow + 3))->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 10,
                        'color' => ['rgb' => '666666']
                    ]
                ]);
            },
        ];
    }

    /**
     * Count visits for the current filter period
     */
    private function countVisitsInPeriod($patient)
    {
        if (!$this->dateFilter) {
            return $patient->visits->count();
        }

        $visits = $patient->visits;
        
        switch ($this->dateFilter) {
            case 'today':
                return $visits->filter(function($visit) {
                    return Carbon::parse($visit->visit_at)->isToday();
                })->count();
                
            case 'yesterday':
                return $visits->filter(function($visit) {
                    return Carbon::parse($visit->visit_at)->isYesterday();
                })->count();
                
            case 'this_week':
                return $visits->filter(function($visit) {
                    return Carbon::parse($visit->visit_at)->between(
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    );
                })->count();
                
            case 'last_week':
                return $visits->filter(function($visit) {
                    return Carbon::parse($visit->visit_at)->between(
                        Carbon::now()->subWeek()->startOfWeek(),
                        Carbon::now()->subWeek()->endOfWeek()
                    );
                })->count();
                
            case 'this_month':
                return $visits->filter(function($visit) {
                    $visitDate = Carbon::parse($visit->visit_at);
                    return $visitDate->month === Carbon::now()->month && 
                           $visitDate->year === Carbon::now()->year;
                })->count();
                
            case 'last_month':
                $lastMonth = Carbon::now()->subMonth();
                return $visits->filter(function($visit) use ($lastMonth) {
                    $visitDate = Carbon::parse($visit->visit_at);
                    return $visitDate->month === $lastMonth->month && 
                           $visitDate->year === $lastMonth->year;
                })->count();
                
            case 'this_year':
                return $visits->filter(function($visit) {
                    return Carbon::parse($visit->visit_at)->year === Carbon::now()->year;
                })->count();
                
            case 'last_year':
                $lastYear = Carbon::now()->subYear()->year;
                return $visits->filter(function($visit) use ($lastYear) {
                    return Carbon::parse($visit->visit_at)->year === $lastYear;
                })->count();
                
            case 'custom':
                if ($this->startDate && $this->endDate) {
                    $startDate = Carbon::parse($this->startDate)->startOfDay();
                    $endDate = Carbon::parse($this->endDate)->endOfDay();
                    
                    return $visits->filter(function($visit) use ($startDate, $endDate) {
                        return Carbon::parse($visit->visit_at)->between($startDate, $endDate);
                    })->count();
                }
                return 0;
                
            default:
                return $patient->visits->count();
        }
    }

    public function properties(): array
    {
        $title = 'تقرير المرضى الشامل';
        
        // Add department filter to title
        if ($this->departmentFilter && $this->departmentFilter !== 'all') {
            $department = \App\Models\Department::find($this->departmentFilter);
            if ($department) {
                $title .= ' - قسم ' . $department->name;
            }
        }
        
        if ($this->dateFilter) {
            $filterNames = [
                'today' => 'اليوم (تسجيل أو زيارة)',
                'yesterday' => 'أمس (تسجيل أو زيارة)',
                'this_week' => 'هذا الأسبوع (تسجيل أو زيارة)',
                'last_week' => 'الأسبوع الماضي (تسجيل أو زيارة)',
                'this_month'
                
                => 'هذا الشهر (تسجيل أو زيارة)',
                'last_month' => 'الشهر الماضي (تسجيل أو زيارة)',
                'this_year' => 'هذا العام (تسجيل أو زيارة)',
                'last_year' => 'العام الماضي (تسجيل أو زيارة)',
                'custom' => 'فترة مخصصة (تسجيل أو زيارة)',
                'all' => 'جميع المرضى'
            ];
            
            $title .= ' - ' . ($filterNames[$this->dateFilter] ?? 'مخصص');
            
            if ($this->dateFilter === 'custom' && $this->startDate && $this->endDate) {
                $title .= ' (' . Carbon::parse($this->startDate)->format('Y-m-d') . ' إلى ' . Carbon::parse($this->endDate)->format('Y-m-d') . ')';
            }
        }

        return [
            'creator'        => auth()->user()->name ?? 'النظام',
            'title'          => $title,
            'description'    => 'قائمة شاملة بالمرضى مع القسم الحالي',
            'subject'        => 'المرضى والأقسام',
            'keywords'       => 'مرضى,تقرير,إحصائيات,تاريخ,زيارات,قسم,شامل',
            'category'       => 'تقارير المرضى الشاملة',
            'company'        => config('app.name'),
        ];
    }
}