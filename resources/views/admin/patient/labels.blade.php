<!-- filepath: resources/views/admin/patient/labels.blade.php -->
@extends('layouts.admin')

@section('content')
<style>
    @media print {
        .no-print { display: none !important; }
        @page {
            size: A4 portrait;
            margin: 3mm; /* Slightly larger margin for safety */
        }
        html, body {
            width: 210mm !important;
            height: 297mm !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
            box-sizing: border-box !important;
            overflow: hidden !important;
        }
        .a4-sheet {
            width: 204mm !important; /* Reduced from 210mm */
            height: 280mm !important; /* Significantly reduced from 285mm */
            margin: 0 auto !important;
            padding: 0 !important;
            box-shadow: none !important;
            background: #fff !important;
            position: relative;
            box-sizing: border-box !important;
            overflow: hidden !important;
            page-break-after: auto !important;
            page-break-before: avoid !important;
            page-break-inside: avoid !important;
        }
        .labels-grid {
            width: 204mm !important; /* Match sheet width */
            height: 280mm !important; /* Match sheet height */
            max-height: 280mm !important;
            display: grid !important;
            grid-template-columns: repeat(4, 51mm) !important; /* Fixed column width */
            grid-template-rows: repeat(10, 28mm) !important; /* Fixed row height */
            gap: 0 !important;
            page-break-inside: avoid !important;
            page-break-after: avoid !important;
            page-break-before: avoid !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: hidden !important;
        }
        .label-box {
            height: 28mm !important; /* Reduced from 28.5mm */
            max-height: 28mm !important;
            width: 51mm !important; /* Reduced from 52.5mm */
            max-width: 51mm !important;
            padding: 0 !important;
            margin: 0 !important;
            box-sizing: border-box !important;
            overflow: hidden !important;
            border: 1px solid #000 !important;
            page-break-inside: avoid !important;
            page-break-after: avoid !important;
            page-break-before: avoid !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            position: relative !important;
        }
        .editable-label { 
            display: none !important; 
            visibility: hidden !important;
        }
        .label-content { 
            display: flex !important; 
            overflow: hidden !important;
        }
        
        /* Text wrapping and sizing for print - further reduced */
        .label-name {
            font-size: 12px !important; /* Reduced from 13px */
            font-weight: bold !important;
            margin: 0 0 1px 0 !important;
            line-height: 1.0 !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
            max-width: 100% !important;
            white-space: normal !important;
            text-align: center !important;
            overflow: hidden !important;
        }
        
        .label-medical-id {
            font-size: 12px !important; /* Reduced from 13px */
            font-weight: bold !important;
            margin: 1px 0 0 0 !important;
            line-height: 1.0 !important;
            letter-spacing: 0.2px !important; /* Reduced from 0.3px */
            word-wrap: break-word !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
            max-width: 100% !important;
            white-space: normal !important;
            text-align: center !important;
            overflow: hidden !important;
        }
        
        .label-age, .label-dept {
            font-size: 9px !important; /* Reduced from 10px */
            margin: 0.5px 0 !important;
            line-height: 1.0 !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
            max-width: 100% !important;
            white-space: normal !important;
            text-align: center !important;
            overflow: hidden !important;
        }
        
        .label-content {
            padding: 1px !important; /* Reduced padding */
            line-height: 1.0 !important;
            height: 100% !important;
            max-height: 100% !important;
            width: 100% !important;
            max-width: 100% !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            align-items: center !important;
            text-align: center !important;
            box-sizing: border-box !important;
            overflow: hidden !important;
            word-wrap: break-word !important;
        }
        
        /* Date labels */
        .label-content > div[style*="font-weight:bold"] {
            font-size: 12px !important; /* Reduced from 13px */
            margin: 1px 0 0 0 !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
            max-width: 100% !important;
            white-space: normal !important;
            text-align: center !important;
            overflow: hidden !important;
        }
        
        /* All content divs should wrap text properly */
        .label-content div {
            margin: 0 !important; /* Removed margins completely */
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            text-align: center !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
            white-space: normal !important;
            overflow: hidden !important;
        }
        
        /* Prevent page breaks completely */
        body, body * {
            page-break-inside: avoid !important;
            page-break-after: avoid !important;
            page-break-before: avoid !important;
        }
        
        /* Force single page with safety margins */
        html {
            max-height: 297mm !important;
            overflow: hidden !important;
        }
        
        body {
            max-height: 297mm !important;
            overflow: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        /* Completely hide non-print elements */
        .no-print,
        .no-print *,
        script,
        style:not([media*="print"]),
        .edit-controls,
        .edit-controls *,
        form,
        form *,
        nav,
        nav *,
        .navbar,
        .navbar *,
        .sidebar,
        .sidebar *,
        .footer,
        .footer * {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
            width: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            position: absolute !important;
            left: -9999px !important;
            top: -9999px !important;
            opacity: 0 !important;
        }
        
        /* Remove any content after grid */
        .labels-grid ~ *,
        .a4-sheet > :not(.labels-grid) {
            display: none !important;
            visibility: hidden !important;
        }
        
        /* Force page end */
        .a4-sheet::after {
            content: "";
            display: block;
            height: 0;
            clear: both;
            page-break-after: auto;
        }
        
        /* Ensure grid fits exactly with safety margins */
        .labels-grid {
            max-height: 280mm !important; /* 17mm safety margin from 297mm */
        }
        
        /* Additional constraints to prevent overflow */
        .a4-sheet {
            max-height: 280mm !important;
            max-width: 204mm !important;
        }
    }
    
    /* Screen styles - keep existing but adjust label box dimensions */
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
        page-break-after: avoid;
        overflow: hidden;
    }
    
    .labels-grid {
        width: 100%;
        height: 100%;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        grid-template-rows: repeat(10, 1fr);
        gap: 0;
        margin: 0;
        padding: 0;
        direction: rtl;
    }
    
    .label-box {
        border: 1px solid #f90;
        width: 100%;
        height: 100%;
        min-height: 28mm; /* Reduced from 28.5mm */
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        box-sizing: border-box;
        overflow: hidden;
        position: relative;
        word-wrap: break-word;
        word-break: break-word;
    }
    
    .label-content {
        width: 100%;
        height: 100%;
        padding: 3px 4px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        line-height: 1.1;
        box-sizing: border-box;
        text-align: center;
        overflow: hidden;
        word-wrap: break-word;
    }
    
    .label-name {
        font-weight: bold;
        font-size: 15px;
        margin: 0 0 3px 0;
        line-height: 1.1;
        text-align: center;
        width: 100%;
        word-wrap: break-word;
        word-break: break-word;
        overflow-wrap: break-word;
        white-space: normal;
        max-width: 100%;
        hyphens: auto;
        -webkit-hyphens: auto;
        -ms-hyphens: auto;
    }
    
    .label-medical-id {
        font-weight: bold;
        font-size: 15px;
        margin: 3px 0 0 0;
        line-height: 1.1;
        letter-spacing: 0.5px;
        text-align: center;
        width: 100%;
        word-wrap: break-word;
        word-break: break-word;
        overflow-wrap: break-word;
        white-space: normal;
        max-width: 100%;
    }
    
    .label-age, .label-dept {
        font-size: 12px;
        margin: 2px 0;
        line-height: 1.1;
        text-align: center;
        width: 100%;
        word-wrap: break-word;
        word-break: break-word;
        overflow-wrap: break-word;
        white-space: normal;
        max-width: 100%;
    }

    /* Keep all existing editable and other styles unchanged */
    .editable-label {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.95);
        display: none;
        padding: 3px;
        box-sizing: border-box;
        flex-direction: column;
        gap: 2px;
    }
    
    .editable-label input, .editable-label select {
        width: 100%;
        padding: 1px 2px;
        font-size: 11px;
        border: 1px solid #ddd;
        border-radius: 2px;
        text-align: center;
        direction: rtl;
        height: 18px;
        margin-bottom: 1px;
        word-wrap: break-word;
    }
    
    .editable-label input:focus {
        outline: 2px solid #007bff;
        border-color: #007bff;
    }
    
    .edit-mode .label-content {
        display: none;
    }
    
    .edit-mode .editable-label {
        display: flex;
    }
    
    .edit-controls {
        text-align: center;
        margin: 15px 0;
    }
    
    .edit-controls button {
        margin: 0 5px;
    }
    
    .template-selector {
        display: inline-block;
        margin: 0 10px;
    }
    
    .template-selector select {
        padding: 5px 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        direction: rtl;
    }

    /* Screen-specific styles */
    @media screen {
        .label-content {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            overflow: hidden;
        }
        .label-name {
            font-size: 14px;
            margin: 0 0 2px 0;
            line-height: 1.1;
        }
        .label-medical-id {
            font-size: 14px;
            margin: 2px 0 0 0;
            line-height: 1.1;
        }
        .label-age, .label-dept {
            font-size: 11px;
            margin: 1px 0;
            line-height: 1.1;
        }
    }
    
    /* Force text to wrap within constraints */
    .label-name,
    .label-medical-id,
    .label-age,
    .label-dept,
    .label-content div {
        hyphens: auto !important;
        -webkit-hyphens: auto !important;
        -ms-hyphens: auto !important;
        overflow-wrap: break-word !important;
        word-wrap: break-word !important;
        word-break: break-word !important;
        white-space: normal !important;
        max-width: 100% !important;
        min-width: 0 !important;
        overflow: hidden !important;
    }
</style>

<div class="a4-sheet">
    <!-- Controls -->
    <div class="no-print">
        <form class="mb-3" style="text-align:center;" onsubmit="return false;">
            <div style="display: flex; align-items: center; gap: 16px; justify-content: center; flex-wrap: wrap;">
                <div class="input-group input-group-outline my-3 focused is-focused" style="width:110px; margin-bottom:0;">
                    <label class="form-label">عدد الملصقات:</label>
                    <input type="number" name="labels" min="4" max="40" 
                           id="labelsInput"
                           value="{{ request('labels', 4) }}" 
                           style="width:70px;" class="form-control">
                </div>
                
                <div class="template-selector" style="display:none;">
                    <label>نمط الملصق:</label>
                    <select id="templateSelect" onchange="applyTemplate()">
                        <option value="default">نمط افتراضي</option>
                        <option value="name_only">الاسم فقط</option>
                        <option value="id_only">الرقم الطبي فقط</option>
                        <option value="department_only">القسم فقط</option>
                        <option value="full_info">معلومات كاملة</option>
                        <option value="custom">مخصص</option>
                    </select>
                </div>
                
                <small class="text-muted" style="margin-bottom:0;">(الحد الأدنى 4 ملصقات)</small>
            </div>
        </form>

        <div class="edit-controls">
            <button type="button" class="btn btn-success btn-sm" id="editBtn" onclick="toggleEditMode()">
                <i class="material-icons">edit</i> تحرير الملصقات
            </button>
            <button type="button" class="btn btn-primary btn-sm" id="saveBtn" onclick="saveChanges()" style="display: none;">
                <i class="material-icons">save</i> حفظ التغييرات
            </button>
            <button type="button" class="btn btn-secondary btn-sm" id="cancelBtn" onclick="cancelEdit()" style="display: none;">
                <i class="material-icons">cancel</i> إلغاء
            </button>
            <button type="button" class="btn btn-info btn-sm" onclick="resetLabels()">
                <i class="material-icons">refresh</i> استعادة الأصلي
            </button>
            <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="material-icons">print</i> طباعة <span id="totalLabels">{{ min(4, $repeat) }}</span> ملصق
            </button>
        </div>
    </div>

    <div class="labels-grid" id="labelsGrid">
        @foreach($patients as $patient)
            @php
                function formatAge($birthDate) {
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

                $lastVisitRecord = $patient->visits()->with('department')->orderBy('visit_at', 'desc')->first();
                $lastVisit = optional($lastVisitRecord)->visit_at;
                $lastVisitDepartment = optional($lastVisitRecord->department ?? null)->name ?? '-';
        
                // Store patient data for JavaScript
                $patientData = [
                    'id' => $patient->id,
                    'medical_id' => $patient->medical_id,
                    'full_name' => $patient->full_name,
                    'age' => formatAge($patient->date_of_birth),
                    'last_visit' => $lastVisit ? \Carbon\Carbon::parse($lastVisit)->format('d-m-Y') : '-',
                    'department' => $lastVisitDepartment
                ];

                // Define default label templates
                $allLabels = [
                    // Template 1: Medical ID only
                    [
                        'medical_id' => $patient->medical_id,
                        'name' => '',
                        'age' => '',
                        'date' => '',
                        'department' => ''
                    ],
                    // Template 2: Medical ID + Date
                    [
                        'medical_id' => $patient->medical_id,
                        'name' => '',
                        'age' => '',
                        'date' => ($lastVisit ? \Carbon\Carbon::parse($lastVisit)->format('d-m-Y') : ''),
                        'department' => ''
                    ],
                    // Template 3: Name only
                    [
                        'medical_id' => '',
                        'name' => $patient->full_name,
                        'age' => '',
                        'date' => '',
                        'department' => ''
                    ],
                    // Template 4: Full info with Department
                    [
                        'medical_id' => $patient->medical_id,
                        'name' => $patient->full_name,
                        'age' => formatAge($patient->date_of_birth),
                        'date' => '',
                        'department' => $lastVisitDepartment
                    ]
                ];

                // Add additional labels (repeat template 4)
                $additionalCount = max(0, $repeat - 4);
                for($i = 0; $i < $additionalCount; $i++) {
                    $allLabels[] = [
                        'medical_id' => $patient->medical_id,
                        'name' => $patient->full_name,
                        'age' => formatAge($patient->date_of_birth),
                        'date' => '',
                        'department' => $lastVisitDepartment
                    ];
                }
            @endphp

            <script type="application/json" id="patientData">@json($patientData)</script>

            {{-- Generate exactly 40 label boxes (10 rows × 4 columns) --}}
            @for($idx = 0; $idx < 40; $idx++)
                @php 
                    $showLabel = $idx < count($allLabels);
                    $labelData = $showLabel ? $allLabels[$idx] : null;
                @endphp
                <div class="label-box" data-index="{{ $idx }}">
                    @if($showLabel)
                        <!-- Display content -->
                        <div class="label-content">
                            @if($labelData['name'])
                                <div class="label-name">{{ $labelData['name'] }}</div>
                            @endif
                            @if($labelData['age'] || ($labelData['department'] && $labelData['department'] !== '-'))
                                <div class="label-age">
                                    @if($labelData['age'])
                                        العمر: {{ $labelData['age'] }}
                                    @endif
                                    @if($labelData['age'] && ($labelData['department'] && $labelData['department'] !== '-'))
                                        - 
                                    @endif
                                    @if($labelData['department'] && $labelData['department'] !== '-')
                                        {{ $labelData['department'] }}
                                    @endif
                                </div>
                            @endif
                            @if($labelData['medical_id'])
                                <div class="label-medical-id">{{ $labelData['medical_id'] }}</div>
                            @endif
                            @if($labelData['date'])
                                <div style="font-weight:bold;font-size:13px;margin-top:4px;">{{ $labelData['date'] }}</div>
                            @endif
                        </div>

                        <!-- Edit form -->
                        <div class="editable-label">
                            <input type="text" name="name" placeholder="الاسم" value="{{ $labelData['name'] }}">
                            <input type="text" name="medical_id" placeholder="الرقم الطبي" value="{{ $labelData['medical_id'] }}">
                            <input type="text" name="age" placeholder="العمر" value="{{ $labelData['age'] }}">
                            <input type="text" name="department" placeholder="القسم" value="{{ $labelData['department'] }}" 
                                   title="اكتب اسم القسم هنا">
                        </div>
                    @endif
                </div>
            @endfor
        @endforeach
    </div>
</div>

<script>
// Global variables
let editMode = false;
let originalData = {};
let patientData = {};

document.addEventListener('DOMContentLoaded', function() {
    // Add department template option to select
    const templateSelect = document.getElementById('templateSelect');
    const departmentOption = document.createElement('option');
    departmentOption.value = 'department_only';
    departmentOption.textContent = 'القسم فقط';
    templateSelect.appendChild(departmentOption);
    
    // Load patient data
    const patientDataScript = document.getElementById('patientData');
    if (patientDataScript) {
        patientData = JSON.parse(patientDataScript.textContent);
    }
    
    // Store original data
    storeOriginalData();
    
    // Set up labels input
    const labelsInput = document.getElementById('labelsInput');
    const savedLabels = localStorage.getItem('labelsCount');
    if (savedLabels !== null) {
        labelsInput.value = savedLabels;
        updateTotalLabels(savedLabels);
        updateGridContent(savedLabels);
    }

    labelsInput.addEventListener('input', function(e) {
        let value = parseInt(this.value);
        if (value > 40) value = 40;
        this.value = value;
        updateTotalLabels(value);
        updateGridContent(value);
    });
    
    // Add double-click edit functionality for individual boxes
    const boxes = document.querySelectorAll('.label-box[data-index]');
    boxes.forEach(box => {
        box.addEventListener('dblclick', function() {
            if (!editMode) {
                // Enter edit mode for individual box
                const departmentInput = this.querySelector('input[name="department"]');
                if (departmentInput) {
                    this.querySelector('.label-content').style.display = 'none';
                    this.querySelector('.editable-label').style.display = 'flex';
                    departmentInput.focus();
                    departmentInput.select();
                    
                    // Save on Enter or blur
                    const saveBox = () => {
                        this.querySelector('.label-content').style.display = 'block';
                        this.querySelector('.editable-label').style.display = 'none';
                        updateSingleBox(this);
                    };
                    
                    departmentInput.addEventListener('blur', saveBox, { once: true });
                    departmentInput.addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') {
                            saveBox();
                        }
                    }, { once: true });
                }
            }
        });
    });
});

function storeOriginalData() {
    const boxes = document.querySelectorAll('.label-box[data-index]');
    boxes.forEach(box => {
        const index = box.dataset.index;
        const inputs = box.querySelectorAll('.editable-label input');
        originalData[index] = {};
        inputs.forEach(input => {
            originalData[index][input.name] = input.value;
        });
    });
}

function toggleEditMode() {
    editMode = !editMode;
    const grid = document.getElementById('labelsGrid');
    const editBtn = document.getElementById('editBtn');
    const saveBtn = document.getElementById('saveBtn');
    const cancelBtn = document.getElementById('cancelBtn');

    if (editMode) {
        grid.classList.add('edit-mode');
        editBtn.style.display = 'none';
        saveBtn.style.display = 'inline-block';
        cancelBtn.style.display = 'inline-block';
    } else {
        grid.classList.remove('edit-mode');
        editBtn.style.display = 'inline-block';
        saveBtn.style.display = 'none';
        cancelBtn.style.display = 'none';
    }
}

function saveChanges() {
    const boxes = document.querySelectorAll('.label-box[data-index]');
    
    boxes.forEach(box => {
        const inputs = box.querySelectorAll('.editable-label input');
        const labelContent = box.querySelector('.label-content');
        
        if (labelContent && inputs.length > 0) {
            const values = {};
            inputs.forEach(input => {
                values[input.name] = input.value.trim();
            });
            
            labelContent.innerHTML = '';
            
            if (values.name) {
                labelContent.innerHTML += `<div class="label-name">${values.name}</div>`;
            }
            
            // Handle age and department - both are fully editable
            if (values.age || values.department) {
                let ageDepText = '';
                if (values.age) {
                    ageDepText += `العمر: ${values.age}`;
                }
                if (values.age && values.department) {
                    ageDepText += ' - ';
                }
                if (values.department) {
                    ageDepText += values.department;
                }
                if (ageDepText) {
                    labelContent.innerHTML += `<div class="label-age">${ageDepText}</div>`;
                }
            }
            
            if (values.medical_id) {
                labelContent.innerHTML += `<div class="label-medical-id">${values.medical_id}</div>`;
            }
            if (values.date) {
                labelContent.innerHTML += `<div style="font-weight:bold;font-size:13px;margin-top:4px;">${values.date}</div>`;
            }
        }
    });
    
    storeOriginalData();
    toggleEditMode();
    showToast('تم حفظ التغييرات بنجاح - القسم قابل للتعديل', 'success');
}

function cancelEdit() {
    // Restore original values
    const boxes = document.querySelectorAll('.label-box[data-index]');
    boxes.forEach(box => {
        const index = box.dataset.index;
        const inputs = box.querySelectorAll('.editable-label input');
        
        if (originalData[index]) {
            inputs.forEach(input => {
                input.value = originalData[index][input.name] || '';
            });
        }
    });
    
    toggleEditMode();
}

function resetLabels() {
    if (confirm('هل تريد استعادة الملصقات للحالة الأصلية؟')) {
        location.reload();
    }
}

function applyTemplate() {
    const template = document.getElementById('templateSelect').value;
    const boxes = document.querySelectorAll('.label-box[data-index]');
    
    boxes.forEach(box => {
        const inputs = box.querySelectorAll('.editable-label input');
        if (inputs.length === 0) return;
        
        const nameInput = box.querySelector('input[name="name"]');
        const medicalIdInput = box.querySelector('input[name="medical_id"]');
        const ageInput = box.querySelector('input[name="age"]');
        const dateInput = box.querySelector('input[name="date"]');
        const departmentInput = box.querySelector('input[name="department"]');
        
        // Clear all first
        inputs.forEach(input => input.value = '');
        
        switch (template) {
            case 'name_only':
                if (nameInput) nameInput.value = patientData.full_name || '';
                break;
            case 'id_only':
                if (medicalIdInput) medicalIdInput.value = patientData.medical_id || '';
                break;
            case 'full_info':
                if (nameInput) nameInput.value = patientData.full_name || '';
                if (medicalIdInput) medicalIdInput.value = patientData.medical_id || '';
                if (ageInput) ageInput.value = patientData.age || '';
                if (departmentInput) departmentInput.value = patientData.department || '';
                break;
            case 'department_only':
                if (departmentInput) departmentInput.value = patientData.department || '';
                break;
            case 'default':
                // Apply default template based on index
                const index = parseInt(box.dataset.index);
                if (index === 0) {
                    if (medicalIdInput) medicalIdInput.value = patientData.medical_id || '';
                } else if (index === 1) {
                    if (medicalIdInput) medicalIdInput.value = patientData.medical_id || '';
                    if (dateInput) dateInput.value = patientData.last_visit || '';
                } else if (index === 2) {
                    if (nameInput) nameInput.value = patientData.full_name || '';
                } else {
                    if (nameInput) nameInput.value = patientData.full_name || '';
                    if (medicalIdInput) medicalIdInput.value = patientData.medical_id || '';
                    if (ageInput) ageInput.value = patientData.age || '';
                    if (departmentInput) departmentInput.value = patientData.department || '';
                }
                break;
            case 'custom':
                // Don't fill anything - let user type whatever they want
                break;
        }
    });
    
    if (editMode) {
        showToast('تم تطبيق النمط - يمكنك تعديل القسم يدوياً', 'info');
    }
}

function updateTotalLabels(total) {
    localStorage.setItem('labelsCount', total);
    document.getElementById('totalLabels').textContent = total;
}

function updateGridContent(total) {
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
        const newGrid = doc.querySelector('.labels-grid');
        if (newGrid) {
            document.querySelector('.labels-grid').innerHTML = newGrid.innerHTML;
            storeOriginalData();
        }
    })
    .catch(error => console.error('Error:', error));
}

function updateSingleBox(box) {
    const inputs = box.querySelectorAll('.editable-label input');
    const labelContent = box.querySelector('.label-content');
    
    if (labelContent && inputs.length > 0) {
        const values = {};
        inputs.forEach(input => {
            values[input.name] = input.value.trim();
        });
        
        labelContent.innerHTML = '';
        
        if (values.name) {
            labelContent.innerHTML += `<div class="label-name">${values.name}</div>`;
        }
        
        if (values.age || values.department) {
            let ageDepText = '';
            if (values.age) {
                ageDepText += `العمر: ${values.age}`;
            }
            if (values.age && values.department) {
                ageDepText += ' - ';
            }
            if (values.department) {
                ageDepText += values.department;
            }
            if (ageDepText) {
                labelContent.innerHTML += `<div class="label-age">${ageDepText}</div>`;
            }
        }
        
        if (values.medical_id) {
            labelContent.innerHTML += `<div class="label-medical-id">${values.medical_id}</div>`;
        }
        if (values.date) {
            labelContent.innerHTML += `<div style="font-weight:bold;font-size:13px;margin-top:4px;">${values.date}</div>`;
        }
    }
}

function showToast(message, type = 'info') {
    // Create toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed`;
    toast.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        animation: slideIn 0.3s ease;
    `;
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="material-icons me-2">${type === 'success' ? 'check_circle' : type === 'error' ? 'error' : 'info'}</i>
            ${message}
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
</script>

@endsection