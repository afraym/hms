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
            width: 208mm;
            height: 297mm;
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
            box-sizing: border-box;
        }
        .a4-sheet {
            width: 208mm !important;
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
    <button class="btn btn-primary no-print mb-3" onclick="window.print()">طباعة كل الملصقات</button>
    <table class="labels-table">
        <tbody>
        @foreach($patients as $patient)
            @php
                // Prepare the first 4 labels
                $lastVisit = optional($patient->visits()->latest('visit_at')->first())->visit_at;
                $firstLabels = [
                    // 1: medical id only
                    '<div class="label-content"><div class="label-medical-id">'.$patient->medical_id.'</div></div>',
                    // 2: medical id + last visit date
                    '<div class="label-content"><div class="label-medical-id">'.$patient->medical_id.'</div><div style="font-weight:bold;font-size:13px;margin-top:4px;">'.($lastVisit ? \Carbon\Carbon::parse($lastVisit)->format('d-m-Y') : '-').'</div></div>',
                    // 3: full name only
                    '<div class="label-content"><div class="label-name">'.$patient->first_name.' '.$patient->second_name.' '.$patient->third_name.' '.$patient->fourth_name.'</div></div>',
                    // 4: full label
                    '<div class="label-content">'
                        .'<div class="label-name">'.$patient->first_name.' '.$patient->second_name.' '.$patient->third_name.' '.$patient->fourth_name.'</div>'
                        .'<div class="label-dept">القسم: '.($patient->department->name ?? '-').'</div>'
                        .'<div class="label-age">السن: '.($patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->age : '-').' سنة</div>'
                        .'<div class="label-medical-id">'.$patient->medical_id.'</div>'
                    .'</div>',
                ];
                // Prepare the rest of the labels (36 labels)
                $restLabels = [];
                for($i=0; $i<36; $i++) {
                    $restLabels[] = '<div class="label-content">'
                        .'<div class="label-name">'.$patient->first_name.' '.$patient->second_name.' '.$patient->third_name.' '.$patient->fourth_name.'</div>'
                        .'<div class="label-age">السن: '.($patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->age : '-').' -سنة' .($patient->department->name ?? '-').'</div>'
                        .'<div class="label-medical-id">'.$patient->medical_id.'</div>'
                        .'</div>';
                }
                $allLabels = array_merge($firstLabels, $restLabels); // total 40
            @endphp

            {{-- Print the labels in rows of 4 --}}
            @for($row=0; $row<10; $row++)
                <tr>
                    @for($col=0; $col<4; $col++)
                        @php $idx = $row*4 + $col; @endphp
                        <td>{!! $allLabels[$idx] ?? '' !!}</td>
                    @endfor
                </tr>
            @endfor
        @endforeach
        </tbody>
    </table>
</div>
@endsection