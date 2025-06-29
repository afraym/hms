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
    {{-- <div class="alert alert-info no-print" style="text-align:center;">
        يرجى التأكد من ضبط إعدادات الطباعة على:
        <strong>Paper size: A4</strong> &nbsp; | &nbsp;
        <strong>Margins: None</strong> &nbsp; | &nbsp;
        <strong>Scale: 100%</strong>
    </div> --}}
    <form class="no-print mb-3" style="text-align:center;" onsubmit="return false;">
        <div style="display: flex; align-items: center; gap: 16px; justify-content: center;">
            <div class="input-group input-group-outline my-3 focused is-focused" style="width:110px; margin-bottom:0;">
                <label class="form-label">عدد الملصقات:</label>
                <input type="number" name="labels" min="4" max="40" 
                       id="labelsInput"
                       value="{{ request('labels', 4) }}" 
                       style="width:70px;" class="form-control ">
            </div>
            <small class="text-muted" style="margin-bottom:0;">(الحد الأدنى 4 ملصقات)</small>
            <button type="button" class="btn btn-primary btn-sm" onclick="window.print()" style="margin-bottom:0;">
                طباعة <span id="totalLabels">{{ $repeat }}</span> ملصق
            </button>
        </div>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const labelsInput = document.getElementById('labelsInput');
        
        // Load saved value from localStorage
        const savedLabels = localStorage.getItem('labelsCount');
        if (savedLabels !== null) {
            labelsInput.value = savedLabels;
            updateTotalLabels(savedLabels);
            updateTableContent(savedLabels);
        }

        // Add input event listener
        labelsInput.addEventListener('input', function(e) {
            let value = parseInt(this.value);
            // Ensure minimum of 4 labels
            if (value < 4) value = 4;
            // Ensure maximum of 40 labels
            if (value > 40) value = 40;
            this.value = value;
            updateTotalLabels(value);
            updateTableContent(value);
        });
    });

    function updateTotalLabels(total) {
        // Save to localStorage
        localStorage.setItem('labelsCount', total);
        
        // Update display
        document.getElementById('totalLabels').textContent = total;
    }

    function updateTableContent(total) {
        fetch(`${window.location.pathname}?labels=${total}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-Total-Labels': total
            }
        })
        .then(response => response.text())
        .then(html => {
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
                function formatAge($birthDate) {
                    if (!$birthDate) return '-';
                    
                    $birth = \Carbon\Carbon::parse($birthDate);
                    $now = \Carbon\Carbon::now();
                    
                    $years = $birth->diffInYears($now);
                    $months = $birth->copy()->addYears($years)->diffInDays($now) / 30.44;
                    
                    if ($years >= 1) {
                        return round($years) . ' سنة';  // Round to whole years
                    } else {
                        return round($birth->diffInDays($now) / 30.44) . ' شهر';  // Round to whole months
                    }
                }

                $lastVisit = optional($patient->visits()->latest('visit_at')->first())->visit_at;
        
                // Define all labels array by combining first and additional labels
                $allLabels = [
                    // First 4 labels
                    '<div class="label-content"><div class="label-medical-id">'.$patient->medical_id.'</div></div>',
                    '<div class="label-content"><div class="label-medical-id">'.$patient->medical_id.'</div><div style="font-weight:bold;font-size:13px;margin-top:4px;">'.($lastVisit ? \Carbon\Carbon::parse($lastVisit)->format('d-m-Y') : '-').'</div></div>',
                    '<div class="label-content"><div class="label-name">'.$patient->full_name.'</div></div>',
                    '<div class="label-content">'
                        .'<div class="label-name">'.$patient->full_name.'</div>'
                        .'<div class="label-age">العمر: '.formatAge($patient->date_of_birth).'</div>'
                        .'<div class="label-medical-id">'.$patient->medical_id.'</div>'
                    .'</div>'
                ];

                // Add additional labels
                $additionalCount = max(0, $repeat - 4);
                for($i = 0; $i < $additionalCount; $i++) {
                    $allLabels[] = '<div class="label-content">'
                        .'<div class="label-name">'.$patient->full_name.'</div>'
                        .'<div class="label-age">العمر: '.formatAge($patient->date_of_birth).' - ' .($patient->department->name ?? '-').'</div>'
                        .'<div class="label-medical-id">'.$patient->medical_id.'</div>'
                        .'</div>';
                }
            @endphp

            {{-- Print exactly 40 cells (10 rows × 4 columns) --}}
            @for($row = 0; $row < 10; $row++)
                <tr>
                    @for($col = 0; $col < 4; $col++)
                        @php 
                            $idx = $row * 4 + $col;
                            $showLabel = $idx < count($allLabels);
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