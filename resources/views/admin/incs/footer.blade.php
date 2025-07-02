<footer class="footer py-4 ">
  <div class="container-fluid">
    <div class="row align-items-center justify-content-center">
      <div class="col-lg-8 mb-lg-0 mb-4">
        <div class="copyright text-center text-sm no-print">
          © <script>
            document.write(new Date().getFullYear())
            </script>,
            تم التطوير بواسطة
            <a href="https://aman.it.com" class="font-weight-bold company-link" target="_blank" style="display: inline-flex; align-items: center;">
            &nbsp;
            <img src="{{ asset('assets/img/amanlogo.png') }}" alt="Logo" style="height:100px; vertical-align:middle; margin-right:5px; margin-left:5px;">
            </a>
            إهداءً لمستشفيات اسوان الجامعية.
        </div>
      </div>
    </div>
  </div>
</footer>
</div>
</main>
<div class="fixed-plugin no-print">
  <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
    <i class="material-symbols-rounded py-2">settings</i>
  </a>
  <div class="card shadow-lg">
    <div class="card-header pb-0 pt-3">
      <div class="float-end">
        <h5 class="mt-3 mb-0">اعدادات الواجهة</h5>
        <p> خيارات لوحة التحكم.</p>
      </div>
      <div class="float-start mt-4">
        <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
          <i class="material-symbols-rounded">clear</i>
        </button>
      </div>
    </div>
    <hr class="horizontal dark my-1">
    <div class="card-body pt-sm-3 pt-0">
      <!-- Sidebar Backgrounds -->
      <div>
        <h6 class="mb-0">ألوان الشريط الجانبي</h6>
      </div>
      <a href="javascript:void(0)" class="switch-trigger background-color">
        <div class="badge-colors my-2 text-end">
          <span class="badge filter bg-gradient-primary" data-color="primary" onclick="sidebarColor(this)"></span>
          <span class="badge filter bg-gradient-dark active" data-color="dark" onclick="sidebarColor(this)"></span>
          <span class="badge filter bg-gradient-info" data-color="info" onclick="sidebarColor(this)"></span>
          <span class="badge filter bg-gradient-success" data-color="success" onclick="sidebarColor(this)"></span>
          <span class="badge filter bg-gradient-warning" data-color="warning" onclick="sidebarColor(this)"></span>
          <span class="badge filter bg-gradient-danger" data-color="danger" onclick="sidebarColor(this)"></span>
        </div>
      </a>
      <!-- Sidenav Type -->
      <div class="mt-3">
        <h6 class="mb-0">نوع الشريط الجانبي</h6>
        <p class="text-sm">اختر بين أنواع الشريط الجانبي المختلفة.</p>
      </div>
      <div class="d-flex">
        <button class="btn bg-gradient-dark px-3 mb-2" data-class="bg-gradient-dark" onclick="sidebarType(this)">داكن</button>
        <button class="btn bg-gradient-dark px-3 mb-2 ms-2" data-class="bg-transparent" onclick="sidebarType(this)">شفاف</button>
        <button class="btn bg-gradient-dark px-3 mb-2 active me-2" data-class="bg-white" onclick="sidebarType(this)">أبيض</button>
      </div>
      <p class="text-sm d-xl-none d-block mt-2">يمكنك تغيير نوع الشريط الجانبي فقط في عرض سطح المكتب.</p>
      <!-- Navbar Fixed -->
      <div class="mt-3 d-flex">
        <h6 class="mb-0">تثبيت شريط التنقل</h6>
        <div class="form-check form-switch me-auto my-auto">
          <input class="form-check-input mt-1 float-end me-auto" type="checkbox" id="navbarFixed" onclick="navbarFixed(this)">
        </div>
      </div>
      <hr class="horizontal dark my-3">
      <div class="mt-2 d-flex">
        <h6 class="mb-0">فاتح / داكن</h6>
        <div class="form-check form-switch me-auto my-auto">
          <input class="form-check-input mt-1 float-end me-auto" type="checkbox" id="dark-version" onclick="darkMode(this)">
        </div>
      </div>
      <hr class="horizontal dark my-sm-4">
    </div>
  </div>
</div>
<!-- Core JS Files -->
<script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>

<script src="{{ asset('assets/js/fileinput.min.js') }}"></script>
<script src="{{ asset('assets/js/locales/ar.js') }}"></script>
<!-- jQuery Toast Plugin -->
<link rel="stylesheet" href="{{ asset('assets/css/jquery.toast.min.css') }}">
<script src="{{ asset('assets/js/jquery.toast.min.js') }}"></script>


{{-- Before closing body --}}
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => console.log('ServiceWorker registered'))
            .catch(err => console.log('ServiceWorker registration failed: ', err));
    });
}
</script>
<script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
            damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }

$(document).ready(function () {
 
// initialize plugin with defaults only if not already initialized
if (!$("#attachments").hasClass('file-input')) {
    $("#attachments").fileinput({
        rtl: true, // تمكين الاتجاه من اليمين إلى اليسار
        language: "ar", // تعيين اللغة إلى العربية
        dropZoneEnabled: true,
        allowedFileExtensions: ["jpg", "png", "jpeg", "pdf", "doc", "docx"],
        showUpload: false, // إخفاء زر الرفع
        previewFileType: 'any',
    });
 
    // with plugin options
    $("#attachments").fileinput({'showUpload':false, 'previewFileType':'any'});
}
    });
function editVisit(visitId) {
    console.log('Edit visit clicked for ID:', visitId);
    
    // Fetch visit data and populate the edit modal
    fetch(`{{ url('admin/patient-visits') }}/${visitId}`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Visit data received:', data);
        
        // Populate the edit form fields
        const editForm = document.getElementById('editVisitForm');
        if (!editForm) {
            console.error('Edit form not found');
            return;
        }
        
        document.getElementById('edit_visit_type').value = data.type || '';
        document.getElementById('edit_visit_at').value = data.visit_at ? data.visit_at.substring(0, 16) : '';
        document.getElementById('edit_visit_department_id').value = data.department_id || '';
        document.getElementById('edit_visit_bed_id').value = data.bed_id || '';
        document.getElementById('edit_visit_companion_name').value = data.companion_name || '';
        document.getElementById('edit_visit_companion_relation').value = data.companion_relation || '';
        document.getElementById('edit_visit_companion_phone').value = data.companion_phone || '';
        document.getElementById('edit_visit_companion_national_id').value = data.companion_national_id || '';
        document.getElementById('edit_visit_notes').value = data.notes || '';
        
        // Set the form action URL only if on patient edit or show route
        @if (Route::is('admin.patients.edit') || Route::is('admin.patients.show'))
            editForm.action = `{{ url('admin/patients') }}/{{ $patient->id }}/visits/${visitId}`;
        @endif
        
        // Show the modal - try multiple methods for compatibility
        const modalElement = document.getElementById('editVisitModal');
        if (modalElement) {
            // Try Bootstrap 5 first
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
            // Fallback to jQuery if available
            else if (typeof $ !== 'undefined' && $.fn.modal) {
                $('#editVisitModal').modal('show');
            }
            // Fallback to direct manipulation
            else {
                modalElement.style.display = 'block';
                modalElement.classList.add('show');
                document.body.classList.add('modal-open');
            }
        } else {
            console.error('Modal element not found');
        }
    })
    .catch(error => {
        console.error('Error fetching visit data:', error);
        $.toast({
            heading: 'خطأ في تحميل البيانات',
            text: 'حدث خطأ في تحميل بيانات الزيارة: ' + error.message,
            icon: 'error',
            position: 'top-right',
            showHideTransition: 'slide'
        });
    });
}

// Handle edit visit form submission
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up event listeners');
    
    const editForm = document.getElementById('editVisitForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submitted');
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            
            submitButton.disabled = true;
            submitButton.textContent = 'جارٍ التحديث...';
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                console.log('Update response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Update response data:', data);
                if (data.success) {
                    // Hide modal
                    const modalElement = document.getElementById('editVisitModal');
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) modal.hide();
                    } else if (typeof $ !== 'undefined' && $.fn.modal) {
                        $('#editVisitModal').modal('hide');
                    } else {
                        modalElement.style.display = 'none';
                        modalElement.classList.remove('show');
                        document.body.classList.remove('modal-open');
                    }
                    
                    // Show success toast
                    $.toast({
                        heading: 'نجح التحديث',
                        text: data.message || 'تم تحديث الزيارة بنجاح',
                        icon: 'success',
                        position: 'top-right',
                        showHideTransition: 'slide'
                    });
                    
                    setTimeout(() => location.reload(), 1500); // Reload after showing toast
                } else {
                    $.toast({
                        heading: 'خطأ في التحديث',
                        text: data.message || 'حدث خطأ أثناء تحديث الزيارة',
                        icon: 'error',
                        position: 'top-right',
                        showHideTransition: 'slide'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                $.toast({
                     heading: 'نجح التحديث',
                text: data.message || 'تم تحديث الزيارة بنجاح',
                icon: 'success',
                position: 'top-right',
                showHideTransition: 'slide'
                });
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            });
        });
    } else {
        console.error('Edit form not found on page load');
    }

    // Handle visit type change
    const editVisitType = document.getElementById('edit_visit_type');
    if (editVisitType) {
        editVisitType.addEventListener('change', function() {
            const type = this.value;
            const bedSelect = document.getElementById('edit_visit_bed_id');
            
            if (type === 'out') {
                bedSelect.disabled = true;
                bedSelect.value = '';
            } else {
                bedSelect.disabled = false;
            }
        });
    }

    // Clear form when modal is hidden
    const editModal = document.getElementById('editVisitModal');
    if (editModal) {
        const clearForm = function() {
            const form = document.getElementById('editVisitForm');
            if (form) {
                form.reset();
                document.getElementById('edit_visit_bed_id').disabled = false;
            }
        };
        
        // Handle both Bootstrap and jQuery modal events
        editModal.addEventListener('hidden.bs.modal', clearForm);
        if (typeof $ !== 'undefined') {
            $('#editVisitModal').on('hidden.bs.modal', clearForm);
        }
    }
});
</script>
{{-- @if(app()->environment('production')) --}}

{{-- @endif --}}
@stack('scripts')
</body>
</html>