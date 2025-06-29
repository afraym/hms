<!-- filepath: resources/views/admin/patient/labels.blade.php -->
@extends('layouts.admin')

@section('content')
<style>
    @media print {
        .no-print { display: none; }
        @page {
            size: A4 portrait;
            margin-right: 6px;
            margin-left: 6px;
        }
        html, body {
            width: 210mm;
            height: 297mm;
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
            box-sizing: border-box;
        }
        .a4-sheet {
            width: 210mm !important;
            height: 297mm !important;
            margin: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            background: #fff !important;
            position: relative;
            box-sizing: border-box;
        }
        .labels-table {
            width: 100% !important;
            height: 100% !important;
            table-layout: fixed;
            border-collapse: collapse;
        }
        .labels-table td {
            height: 29.7mm !important; /* 297mm / 10 rows = 29.7mm */
            width: 25% !important;
            padding: 0 !important;
            box-sizing: border-box;
            overflow: hidden;
        }
    }
    .a4-sheet {
        width: 210mm;
        height: 297mm;
        margin: 0 auto;
        background: #fff;
        padding: 0;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        justify-content: flex-start;
    }
    .labels-table {
        width: 100%;
        height: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        margin: 0;
        padding: 0;
        direction: rtl;
    }
    .labels-table td {
        border: 1px solid #f90;
        width: 25%;
        height: 74px;
        vertical-align: middle;
        text-align: center;
        padding: 0;
        box-sizing: border-box;
        overflow: hidden;
    }
    .label-content {
        width: 100%;
        height: 100%;
        padding: 2px 2px 0 2px;
        font-size: 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        line-height: 1.2;
        box-sizing: border-box;
    }
    .label-name {
        font-weight: bold;
        font-size: 17px;
    }
    .label-medical-id {
        font-weight: bold;
        font-size: 15px;
        margin-top: 2px;
        letter-spacing: 1px;
    }
    .label-age, .label-dept {
        font-size: 13px;
        margin-top: 1px;
    }
</style>

<div class="a4-sheet">
    <div class="alert alert-info no-print" style="text-align:center;">
        يرجى التأكد من ضبط إعدادات الطباعة على:
        <strong>Paper size: A4</strong> &nbsp; | &nbsp;
        <strong>Margins: None</strong> &nbsp; | &nbsp;
        <strong>Scale: 100%</strong>
    </div>
    <form class="no-print mb-3" style="text-align:center;" onsubmit="return false;">
        <label>عدد الملصقات الإضافية: </label>
        <input type="number" name="labels" min="0" max="36" 
               id="labelsInput"
               value="{{ request('labels', $repeat - 4) }}" 
               style="width:70px;">
        <small class="text-muted">(سيتم إضافتها بعد الـ 4 ملصقات الأساسية)</small>
        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
            طباعة <span id="totalLabels">{{ $repeat }}</span> ملصق
        </button>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const labelsInput = document.getElementById('labelsInput');
        
        // Load saved value from localStorage
        const savedLabels = localStorage.getItem('labelsCount');
        if (savedLabels !== null) {
            labelsInput.value = savedLabels;
            updateTotalLabels(savedLabels);
            
            // Update the table content with saved value
            updateTableContent(savedLabels);
        }

        // Add input event listener for Ajax update
        labelsInput.addEventListener('input', function(e) {
            const value = this.value;
            updateTotalLabels(value);
            updateTableContent(value);
        });
    });

    function updateTotalLabels(additionalLabels) {
        // Save to localStorage
        localStorage.setItem('labelsCount', additionalLabels);
        
        // Update display
        const total = parseInt(additionalLabels) + 4;
        document.getElementById('totalLabels').textContent = total;
    }

    function updateTableContent(labels) {
        fetch(`${window.location.pathname}?labels=${labels}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-Saved-Labels': labels
            }
        })
        .then(response => response.text())
        .then(html => {
            // Update only the table content
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTable = doc.querySelector('.labels-table');
            if (newTable) {
                document.querySelector('.labels-table').innerHTML = newTable.innerHTML;
            }
        })
        .catch(error => console.error('Error:', error));
    }
    </script>

    <table class="labels-table">
        <tbody>
        @foreach($patients as $patient)
            @php
                // أول 4 ملصقات متنوعة (ثابتة)
                $lastVisit = optional($patient->visits()->latest('visit_at')->first())->visit_at;
                $firstLabels = [
                    '<div class="label-content"><div class="label-medical-id">'.$patient->medical_id.'</div></div>',
                    '<div class="label-content"><div class="label-medical-id">'.$patient->medical_id.'</div><div style="font-weight:bold;font-size:13px;margin-top:4px;">'.($lastVisit ? \Carbon\Carbon::parse($lastVisit)->format('d-m-Y') : '-').'</div></div>',
                    '<div class="label-content"><div class="label-name">'.$patient->full_name.'</div></div>',
                    '<div class="label-content">'
                        .'<div class="label-name">'.$patient->full_name.'</div>'
                        .'<div class="label-age">السن: '.($patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->age : '-').' سنة</div>'
                        .'<div class="label-medical-id">'.$patient->medical_id.'</div>'
                    .'</div>',
                ];

                // الملصقات الإضافية
                $additionalLabels = [];
                $additionalCount = max(0, $repeat - 4); // عدد الملصقات الإضافية
                for($i=0; $i < $additionalCount; $i++) {
                    $additionalLabels[] = '<div class="label-content">'
                        .'<div class="label-name">'.$patient->full_name.'</div>'
                        .'<div class="label-age">السن: '.($patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->age : '-').' سنه - ' .($patient->department->name ?? '-').'</div>'
                        .'<div class="label-medical-id">'.$patient->medical_id.'</div>'
                        .'</div>';
                }

                // دمج الملصقات الأساسية مع الإضافية
                $allLabels = array_merge($firstLabels, $additionalLabels);
            @endphp

            {{-- Print exactly 40 cells (10 rows × 4 columns) --}}
            @for($row=0; $row < 10; $row++)
                <tr>
                    @for($col=0; $col<4; $col++)
                        @php 
                            $idx = $row*4 + $col;
                            $showLabel = $idx < count($allLabels); // Only show content if we have a label for this cell
                        @endphp
                        <td>{!! $showLabel ? $allLabels[$idx] : '' !!}</td>
                    @endfor
                </tr>
            @endfor
        @endforeach
        </tbody>
    </table>
</div>
@endsection